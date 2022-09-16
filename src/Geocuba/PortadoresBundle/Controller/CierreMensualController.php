<?php
/**
 * Created by PhpStorm.
 * User: Yosley
 * Date: 11/07/2017
 * Time: 16:02
 */

namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\AdminBundle\Util\Util;
use Geocuba\PortadoresBundle\Util\FechaUtil;
use Geocuba\PortadoresBundle\Entity\AnexoUnico;
use Geocuba\PortadoresBundle\Entity\CombustibleKilometros;
use Geocuba\PortadoresBundle\Entity\Vehiculo;
use Doctrine\Common\CommonException;
use Doctrine\Common\Util\Debug;
use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\Utils\ViewActionTrait;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Geocuba\PortadoresBundle\Util\Utiles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Geocuba\PortadoresBundle\Util\DocumentosEnum;

class CierreMensualController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $mes = $request->get('mes');
        $anno = $request->get('anno');
        $tipo_combustibleid = $request->get('tipoCombustible');
        $export = $request->get('export');

        $nunidadid = $request->get('unidad');
        $conn = $this->get('database_connection');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $qb = $em->createQueryBuilder();

        $qb->select('vehiculo.id, u.id nunidadid, u.siglas unidad_siglas,a.id actividadid, tc.nombre tipoComb, a.nombre actividad_nombre,
                    marca.nombre marca_vehiculo, vehiculo.matricula, vehiculo.normaFar, vehiculo.nroOrden, modelo.nombre modelo_vehiculo')
            ->from('PortadoresBundle:Vehiculo', 'vehiculo')
            ->innerJoin('vehiculo.nunidadid', 'u')
            ->innerJoin('vehiculo.actividad', 'a')
            ->innerJoin('vehiculo.ntipoCombustibleid', 'tc')
            ->innerJoin('vehiculo.nmodeloid', 'modelo')
            ->innerJoin('modelo.marcaVehiculoid', 'marca')
            ->where('vehiculo.visible = true')
            ->andWhere($qb->expr()->in('vehiculo.nunidadid', $_unidades));

        if ($tipo_combustibleid !== "null" && $tipo_combustibleid !== "") {
            $qb->andWhere('vehiculo.ntipoCombustibleid = :tipoCombustible')
                ->setParameter('tipoCombustible', "$tipo_combustibleid");
        }

        $vehiculos = $qb->getQuery()->getResult();

        foreach ($vehiculos as $vehiculo){

            $result_llegada = $conn->fetchAll("select registro_combustible_analisis.* from datos.registro_combustible
            inner join nomencladores.vehiculo nvehiculo on nvehiculo.id = registro_combustible.vehiculoid and nvehiculo.id = '" . $vehiculo['id'] . "'
            inner join datos.registro_combustible_analisis on registro_combustible_analisis.registro_combustible_id = registro_combustible.id
            where registro_combustible_analisis.conceptoid = '4' and date_part('YEAR', registro_combustible.fecha) = $anno and date_part('MONTH', registro_combustible.fecha) = $mes
            order by numerosemana DESC limit 1");

            $sql = "select registro_combustible_analisis.* from datos.registro_combustible
            inner join nomencladores.vehiculo nvehiculo on nvehiculo.id = registro_combustible.vehiculoid and nvehiculo.id = '" . $vehiculo['id'] . "'
            inner join datos.registro_combustible_analisis on registro_combustible_analisis.registro_combustible_id = registro_combustible.id
            where registro_combustible_analisis.conceptoid = '1' and date_part('YEAR', registro_combustible.fecha) = $anno and date_part('MONTH', registro_combustible.fecha) = $mes
            order by numerosemana limit 1";
            $result_salida = $conn->fetchAll($sql);

            $combustible_abastecido = $conn->fetchAll("select sum(rca.combustible) abastecido
                                                                   from datos.registro_combustible as rc
                                                                   join datos.registro_combustible_analisis as rca on rc.id = rca.registro_combustible_id

                                                             where rc.vehiculoid = '" . $vehiculo['id'] . "'
                                                                   and rca.conceptoid = '2'
                                                                   and extract(YEAR from rc.fecha) = $anno
                                                                   and extract(MONTH from rc.fecha) = $mes");

            $result_kmt_trab = $conn->fetchAll("select   sum(registro_combustible_analisis.km) from datos.registro_combustible
            inner join nomencladores.vehiculo nvehiculo on nvehiculo.id = registro_combustible.vehiculoid and nvehiculo.id = '" . $vehiculo['id'] . "'
            inner join datos.registro_combustible_analisis on registro_combustible_analisis.registro_combustible_id = registro_combustible.id
            where registro_combustible_analisis.conceptoid = '3' and date_part('YEAR', registro_combustible.fecha) = $anno and date_part('MONTH', registro_combustible.fecha)= $mes and  registro_combustible.visible = true
            limit 1");

            $kms_salir = empty($result_salida[0]['km']) ? 0 : $result_salida[0]['km'];
            $comb_salir = empty($result_salida[0]['combustible']) ? 0 : $result_salida[0]['combustible'];
            $kms_llegar = empty($result_llegada[0]['km']) ? 0 : $result_llegada[0]['km'];
            $comb_llegar = empty($result_llegada[0]['combustible']) ? 0 : $result_llegada[0]['combustible'];
            $comb_abastecido = empty($combustible_abastecido[0]['abastecido']) ? 0 : $combustible_abastecido[0]['abastecido'];
            $kms_trabajado = is_null($result_kmt_trab[0]['sum']) ? 0 : $result_kmt_trab[0]['sum'];
            $comb_consumido = $comb_salir + $comb_abastecido - $comb_llegar;


            $_data_au[] = array(
                'vehiculo_id' => $vehiculo['id'],
                'matricula' => $vehiculo['matricula'],
                'tipo_combustible' => $vehiculo['tipoComb'],
                'unidad_id' => $vehiculo['nunidadid'],
                'unidad' => strtoupper($vehiculo['unidad_siglas']),
                'actividad_id' => $vehiculo['actividadid'],
                'actividad' => $vehiculo['actividad_nombre'],
                'modelo' => $vehiculo['modelo_vehiculo'],
                'km_inicial' => $kms_salir ,
                'comb_inicial' => $comb_salir,
                'km_final' => $kms_llegar,
                'comb_final' =>  $comb_llegar,
                'km_rec' => $kms_trabajado,
                'comb_abast' => $comb_abastecido,
                'comb_real_cons' => round($comb_consumido,2),
                'indice_cons_real' =>($kms_trabajado == 0) ? 0 : round(($comb_salir + $comb_abastecido - $comb_llegar) * 100 / ($kms_trabajado), 2),
                'indice_cons_norm' => $vehiculo['normaFar'],
                'porciento_desv' => ($comb_consumido !== 0 && $vehiculo['normaFar'] !== 0) ? (($vehiculo['normaFar'] - $kms_trabajado / $comb_consumido ) / $vehiculo['normaFar'] * 100) / 100: 0,
                'desv_abs' => ($comb_consumido !== 0 && $vehiculo['normaFar'] !== 0) ? abs((($vehiculo['normaFar'] - $kms_trabajado / $comb_consumido ) / $vehiculo['normaFar'] * 100) / 100): 0);

        }
        return new JsonResponse(array('rows' => $_data_au, 'total' => count($_data_au)));

    }

    public function printAction(Request $request)
    {
        $data = json_decode($request->get('store'));
        $unidad_padre = strtoupper($data[0]->unidad);

        $mes_ = $request->get('mes');
        $anno = $request->get('anno');
        $mes = strtoupper(FechaUtil::getNombreMes($mes_));

        $actv = array();
        foreach ($data as $d) {
            if (array_search($d->actividad, $actv) === false)
                $actv[] = $d->actividad;
        }

        $_html = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
        <html xmlns='http://www.w3.org/1999/xhtml'>
         <head>
          <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
           <title>Cierre Mensual</title>
            <style>
              table.main {
                border:0px ;
              border-radius:1px 1px 1px 1px;
              font-family: 'Times New Roman', Times, serif;
              font-size: 10px;
              }
            .sinborde{
                border-color:#FFF;
            }
            .bordearriba{
                border-top:#000;
            }
            table.main1 {	border:0px ;
             border-radius:1px 1px 1px 1px;
             font-family: 'Times New Roman', Times, serif;
             font-size: 10px;
            }
            </style>
         </head>
          <body>
          <header>
                <img  src='../../assets/img/PNG/logo.png' height='60px' width='160px'>
            </header>
           
          <h3 style='text-align: center'>$unidad_padre</h3>
          <h3 style='text-align: center'>CONCILIACIÓN COMBUSTIBLE CIERRE MES: $mes $anno</h3>
           <table cellspacing='0' cellpadding='5' border='1'  align='center'>
             <tr>
                 <td rowspan='2' align='center' style='vertical-align:middle;'>No</td>
                 <td rowspan='2' align='center' style='vertical-align:middle;'>Unidad Básica</td>
                 <td colspan='2' align='center' style='vertical-align:middle;'>EQUIPOS</td>
                 <td align='center' style='vertical-align:middle;'>Km Inicial</td>
                 <td align='center' style='vertical-align:middle;'>Km Final</td>
                 <td align='center' style='vertical-align:middle;'>Kms Rec.</td>
                 <td align='center'>Comb.<br>Inicial TQ</td>
                 <td align='center'>Comb.<br>Abast.</td>
                 <td align='center'>Comb. Final<br>TQ</td>
                 <td align='center'>Comb. Real<br>Cons.</td>
                 <td align='center'>Indice de<br>cons. real</td>
                 <td align='center'>Indice cons. <br>Norm.</td>
                 <td rowspan='2' align='center' style='vertical-align:middle;'>% de Desv.</td>
                 <td rowspan='2' align='center' style='vertical-align:middle;'>Desv. ABS</td>
             </tr>
             <tr>
                 <td align='center'>Modelo</td>
                 <td align='center'>Matricula</td>
                 <td align='center'>Km</td>
                 <td align='center'>Km</td>
                 <td align='center'>Km</td>
                 <td align='center'>L</td>
                 <td align='center'>L</td>
                 <td align='center'>L</td>
                 <td align='center'>L</td>
                 <td align='center'>Km/L</td>
                 <td align='center'>Km/L</td>
             </tr>
          ";

            $cont_final = 0;
            $km_inicial = 0;
            $km_final = 0;
            $km_rec = 0;
            $comb_inicial = 0;
            $comb_abast = 0;
            $comb_final = 0;
            $comb_real = 0;

        foreach ($data as $da) {
            $cont_final++;
                    $km_inicial += $da->km_inicial;
                    $km_final += $da->km_final;
                    $km_rec += $da->km_rec;
                    $comb_inicial += $da->comb_inicial;
                    $comb_abast += $da->comb_abast;
                    $comb_final += $da->comb_final;
                    $comb_real += $da->comb_real_cons;
            $indice_cons_real = $da->km_rec==0?0:number_format(floatval($da->indice_cons_real), 2);
            $porciento_desv = $da->km_rec==0?0:number_format(floatval($da->porciento_desv), 2);
            $_html .= "<tr>
                                   <td align='center'>$cont_final</td>
                                   <td align='center'>" . $da->unidad . "</td>
                                   <td align='center'>" . $da->modelo . "</td>
                                   <td align='center'>" . $da->matricula . "</td>
                                   <td align='center'>" . $da->km_inicial . "</td>
                                   <td align='center'>" . $da->km_final . "</td>
                                   <td align='center'>" . $da->km_rec . "</td>
                                   <td align='center'>" . $da->comb_inicial . "</td>
                                   <td align='center'>" . $da->comb_abast . "</td>
                                   <td align='center'>" . $da->comb_final . "</td>
                                   <td align='center'>" . $da->comb_real_cons . "</td>
                                   <td align='center'>" .$indice_cons_real  . "</td>
                                   <td align='center'>" . $da->indice_cons_norm . "</td>
                                   <td align='center'>" . $porciento_desv . "</td>
                                   <td align='center'>" . number_format(floatval($da->desv_abs), 2) . "</td>
                    </tr>";
//                    $cont_actv++;
//                }
        }
            $_html .= "<tr>
                           <td align='center'>$cont_final</td>
                           <td colspan='3' align='center'>SUB TOTAL</td>
                           <td align='center'>$km_inicial</td>
                           <td align='center'>$km_final</td>
                           <td align='center'>$km_rec</td>
                           <td align='center'>$comb_inicial</td>
                           <td align='center'>$comb_abast</td>
                           <td align='center'>$comb_final</td>
                           <td align='center'>$comb_real</td>
                           <td ></td>
                           <td></td>
                           <td></td>
                           <td></td>
            </tr>";
//        }
        $_html .= "
            </table>
             ";
        $pieFirma = $this->get('portadores.piefirma')->getPieFirmaDistribucion(DocumentosEnum::cierreMensual, $request->get('unidadid'));
        $_html .= "
        <br>
        <br>
        
        $pieFirma";

        $_html .= "
        </body>
        </html>";
        return new Response(json_encode(array('success' => true, 'html' => $_html)));
    }

    public function exportAction(Request $request)
    {
        $mes_ = $request->get('mes');
        $anno = $request->get('anno');
        $mes = FechaUtil::getNombreMes($mes_);
//        var_dump($request->get('mes'));
//        var_dump($request->get('anno'));
//        var_dump($request->get('unidad'));
//        var_dump($request->get('tipoCombustible'));

        $data = json_decode($this->loadAction($request));
//        Debug::dump($data);

        $unidad_padre = strtoupper($data[0]->unidad);

        $actv = array();
        foreach ($data as $d) {
            if (array_search($d->actividad, $actv) === false)
                $actv[] = $d->actividad;
        }
//        $phpexcel = $this->get('phpexcel');
//        $excel_object = $phpexcel->createPHPExcelObject();
//        $active_sheet = $excel_object->getActiveSheet();

        $spreadsheet = new Spreadsheet();
        $active_sheet = $spreadsheet->getActiveSheet();

        $active_sheet->setTitle('Cierre Mensual');


        $active_sheet->mergeCells('B4:Y4');
        $active_sheet->setCellValue('B4', $unidad_padre);
        $active_sheet->getStyle('B4')->getFont()->setSize(14);
        $active_sheet->getStyle('B4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $active_sheet->mergeCells('B6:Y6');
        $active_sheet->setCellValue('B6', 'CONCILIACIÓN COMBUSTIBLE CIERRE MES: ' . $mes . ' ' . $anno);
        $active_sheet->getStyle('B6')->getFont()->setSize(14);
        $active_sheet->getStyle('B6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $active_sheet->mergeCells('B9:B10');
        $active_sheet->setCellValue('B9', 'No');
        $active_sheet->getStyle('B9')->getFont()->setBold(true)->setSize(12);
        $active_sheet->getStyle('B9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $active_sheet->mergeCells('C9:E10');
        $active_sheet->setCellValue('C9', 'Unidad Básica');
        $active_sheet->getStyle('C9')->getFont()->setBold(true)->setSize(12);
        $active_sheet->getStyle('C9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('F9:H10');
//        $active_sheet->setCellValue('F9', 'Responsable');
//        $active_sheet->getStyle('F9')->getFont()->setBold(true)->setSize(12);
//        $active_sheet->getStyle('F9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('I9:K10');
//        $active_sheet->setCellValue('I9', 'Cargo');
//        $active_sheet->getStyle('I9')->getFont()->setBold(true)->setSize(12);
//        $active_sheet->getStyle('I9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $active_sheet->mergeCells('I9:K10');
        $active_sheet->setCellValue('I9', 'Cargo');
        $active_sheet->getStyle('I9')->getFont()->setBold(true)->setSize(12);
        $active_sheet->getStyle('I9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $active_sheet->mergeCells('L9:N9');
        $active_sheet->setCellValue('L9', 'EQUIPOS');
        $active_sheet->getStyle('L9')->getFont()->setBold(true)->setSize(12);
        $active_sheet->getStyle('L9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $active_sheet->setCellValue('O9', 'Km Inicial');
        $active_sheet->getStyle('O9')->getFont()->setBold(true)->setSize(12);
        $active_sheet->getStyle('O9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_JUSTIFY);
        $active_sheet->setCellValue('P9', 'Km Final');
        $active_sheet->getStyle('P9')->getFont()->setBold(true)->setSize(12);
        $active_sheet->getStyle('P9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_JUSTIFY);
        $active_sheet->setCellValue('Q9', 'Km Rec.');
        $active_sheet->getStyle('Q9')->getFont()->setBold(true)->setSize(12);
        $active_sheet->getStyle('Q9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_JUSTIFY);
        $active_sheet->setCellValue('R9', 'Comb.Inicial TQ');
        $active_sheet->getStyle('R9')->getFont()->setBold(true)->setSize(12);
        $active_sheet->getStyle('R9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_JUSTIFY);
        $active_sheet->setCellValue('S9', 'Comb.Abast');
        $active_sheet->getStyle('S9')->getFont()->setBold(true)->setSize(12);;
        $active_sheet->getStyle('S9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_JUSTIFY);
        $active_sheet->setCellValue('T9', 'Comb.Final TQ');
        $active_sheet->getStyle('T9')->getFont()->setBold(true)->setSize(12);
        $active_sheet->getStyle('T9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_JUSTIFY);
        $active_sheet->setCellValue('U9', 'Comb.Real Cons.');
        $active_sheet->getStyle('U9')->getFont()->setBold(true)->setSize(12);
        $active_sheet->getStyle('U9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_JUSTIFY);
        $active_sheet->setCellValue('V9', 'ĺndice de cons. real');
        $active_sheet->getStyle('V9')->getFont()->setBold(true)->setSize(12);
        $active_sheet->getStyle('V9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_JUSTIFY);
        $active_sheet->setCellValue('W9', 'ĺndice cons. Norm.');
        $active_sheet->getStyle('W9')->getFont()->setBold(true)->setSize(12);
        $active_sheet->getStyle('W9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_JUSTIFY);
        $active_sheet->mergeCells('X9:X10');
        $active_sheet->setCellValue('X9', '% de Desv.');
        $active_sheet->getStyle('X9')->getFont()->setBold(true)->setSize(12);
        $active_sheet->getStyle('X9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_JUSTIFY);
        $active_sheet->mergeCells('Y9:Y10');
        $active_sheet->setCellValue('Y9', 'Desv. ABS');
        $active_sheet->getStyle('Y9')->getFont()->setBold(true)->setSize(12);
        $active_sheet->getStyle('Y9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_JUSTIFY);

        $active_sheet->mergeCells('L10:M10');
        $active_sheet->setCellValue('L10', 'Modelo');
        $active_sheet->getStyle('L10')->getFont()->setBold(true)->setSize(12);
        $active_sheet->getStyle('L10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $active_sheet->setCellValue('N10', 'Matrícula');
        $active_sheet->getStyle('N10')->getFont()->setBold(true)->setSize(12);
        $active_sheet->getStyle('N10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $active_sheet->setCellValue('O10', 'Km');
        $active_sheet->getStyle('O10')->getFont()->setBold(true)->setSize(12);
        $active_sheet->getStyle('O10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $active_sheet->setCellValue('P10', 'Km');
        $active_sheet->getStyle('P10')->getFont()->setBold(true)->setSize(12);
        $active_sheet->getStyle('P10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $active_sheet->setCellValue('Q10', 'Km');
        $active_sheet->getStyle('Q10')->getFont()->setBold(true)->setSize(12);
        $active_sheet->getStyle('Q10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $active_sheet->setCellValue('R10', 'L');
        $active_sheet->getStyle('R10')->getFont()->setBold(true)->setSize(12);
        $active_sheet->getStyle('R10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $active_sheet->setCellValue('S10', 'L');
        $active_sheet->getStyle('S10')->getFont()->setBold(true)->setSize(12);
        $active_sheet->getStyle('S10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $active_sheet->setCellValue('T10', 'L');
        $active_sheet->getStyle('T10')->getFont()->setBold(true)->setSize(12);
        $active_sheet->getStyle('T10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $active_sheet->setCellValue('U10', 'L');
        $active_sheet->getStyle('U10')->getFont()->setBold(true)->setSize(12);
        $active_sheet->getStyle('U10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $active_sheet->setCellValue('W10', 'Km/L');
        $active_sheet->getStyle('W10')->getFont()->setBold(true)->setSize(12);
        $active_sheet->getStyle('W10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $pos = 11;
        $posi = 12;

        foreach ($actv as $act) {
            $cont_actv = 1;
            $cont_final = 0;
            $km_inicial = 0;
            $km_final = 0;
            $km_rec = 0;
            $comb_inicial = 0;
            $comb_abast = 0;
            $comb_final = 0;
            $comb_real = 0;

            $totaldiesel = 0;
            $totalgasolina = 0;
            $cantvehidiesel = 0;
            $cantvehigasolina = 0;
            $active_sheet->mergeCells('B' . $pos . ':Y' . $pos);
            $active_sheet->setCellValue('B' . $pos, $act);
            $active_sheet->getStyle('B' . $pos)->getFont()->setBold(true)->setSize(12);
            $active_sheet->getStyle('B' . $pos)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            foreach ($data as $da) {
//                if ($da->tipo_combustible == 'Diesel') {
//                    $totaldiesel += $da->comb_real_cons;
//                    $cantvehidiesel++;
//                } elseif ($da->tipo_combustible == 'Gasolina Especial') {
//                    $totalgasolina += $da->comb_real_cons;
//                    $cantvehigasolina++;
//                }
                if ($da->actividad == $act) {
                    $cont_final++;
                    $km_inicial += $da->km_inicial;
                    $km_final += $da->km_final;
                    $km_rec += $da->km_rec;
                    $comb_inicial += $da->comb_inicial;
                    $comb_abast += $da->comb_abast;
                    $comb_final += $da->comb_final;
                    $comb_real += $da->comb_real_cons;

                    $active_sheet->setCellValue('B' . $posi, $cont_actv);
                    $active_sheet->getStyle('B' . $posi)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $active_sheet->mergeCells('C' . $posi . ':E' . $posi);
                    $active_sheet->setCellValue('C' . $posi, $da->unidad);
                    $active_sheet->getStyle('C' . $posi)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
//                    $active_sheet->mergeCells('F' . $posi . ':H' . $posi);
//                    $active_sheet->setCellValue('F' . $posi, $da->responsable);
//                    $active_sheet->getStyle('F' . $posi)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
//                    $active_sheet->mergeCells('I' . $posi . ':K' . $posi);
//                    $active_sheet->setCellValue('I' . $posi, $da->cargo);
//                    $active_sheet->getStyle('I' . $posi)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $active_sheet->mergeCells('L' . $posi . ':M' . $posi);
                    $active_sheet->setCellValue('L' . $posi, $da->modelo);
                    $active_sheet->getStyle('L' . $posi)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $active_sheet->setCellValue('N' . $posi, $da->matricula);
                    $active_sheet->getStyle('N' . $posi)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $active_sheet->setCellValue('O' . $posi, $da->km_inicial);
                    $active_sheet->getStyle('O' . $posi)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $active_sheet->setCellValue('P' . $posi, $da->km_final);
                    $active_sheet->getStyle('P' . $posi)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $active_sheet->setCellValue('Q' . $posi, $da->km_rec);
                    $active_sheet->getStyle('Q' . $posi)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $active_sheet->setCellValue('R' . $posi, $da->comb_inicial);
                    $active_sheet->getStyle('R' . $posi)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $active_sheet->setCellValue('S' . $posi, $da->comb_abast);
                    $active_sheet->getStyle('S' . $posi)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $active_sheet->setCellValue('T' . $posi, $da->comb_final);
                    $active_sheet->getStyle('T' . $posi)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $active_sheet->setCellValue('U' . $posi, $da->comb_real_cons);
                    $active_sheet->getStyle('U' . $posi)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $active_sheet->setCellValue('V' . $posi, $da->indice_cons_real);
                    $active_sheet->getStyle('V' . $posi)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $active_sheet->setCellValue('W' . $posi, $da->indice_cons_norm);
                    $active_sheet->getStyle('W' . $posi)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $active_sheet->setCellValue('X' . $posi, $da->porciento_desv);
                    $active_sheet->getStyle('X' . $posi)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $active_sheet->setCellValue('Y' . $posi, $da->desv_abs);
                    $active_sheet->getStyle('Y' . $posi)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
//                    $active_sheet->getStyle('Y' . $posi)->setColor(\PHPExcel_Style_Color::getRed());
                    $cont_actv++;
                    $posi++;
                }
            }
            $pos = $posi;
            $active_sheet->setCellValue('B' . $pos, $cont_final);
            $active_sheet->getStyle('B' . $pos)->getFont()->setBold(true)->setSize(12);
            $active_sheet->getStyle('B' . $pos)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $active_sheet->mergeCells('C' . $pos . ':M' . $pos);
            $active_sheet->setCellValue('C' . $pos, 'SUB TOTAL');
            $active_sheet->getStyle('C' . $pos)->getFont()->setBold(true)->setSize(12);
            $active_sheet->getStyle('C' . $pos)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $active_sheet->setCellValue('O' . $pos, $km_inicial);
            $active_sheet->getStyle('O' . $pos)->getFont()->setBold(true)->setSize(12);
            $active_sheet->getStyle('O' . $pos)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $active_sheet->setCellValue('P' . $pos, $km_final);
            $active_sheet->getStyle('P' . $pos)->getFont()->setBold(true)->setSize(12);
            $active_sheet->getStyle('P' . $pos)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $active_sheet->setCellValue('Q' . $pos, $km_rec);
            $active_sheet->getStyle('Q' . $pos)->getFont()->setBold(true)->setSize(12);
            $active_sheet->getStyle('Q' . $pos)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $active_sheet->setCellValue('R' . $pos, $comb_inicial);
            $active_sheet->getStyle('R' . $pos)->getFont()->setBold(true)->setSize(12);
            $active_sheet->getStyle('R' . $pos)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $active_sheet->setCellValue('S' . $pos, $comb_abast);
            $active_sheet->getStyle('S' . $pos)->getFont()->setBold(true)->setSize(12);
            $active_sheet->getStyle('S' . $pos)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $active_sheet->setCellValue('T' . $pos, $comb_final);
            $active_sheet->getStyle('T' . $pos)->getFont()->setBold(true)->setSize(12);
            $active_sheet->getStyle('T' . $pos)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $active_sheet->setCellValue('U' . $pos, $comb_real);
            $active_sheet->getStyle('U' . $pos)->getFont()->setBold(true)->setSize(12);
            $active_sheet->getStyle('U' . $pos)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $pos++;
            $posi = $pos + 1;


        }

//        $active_sheet->mergeCells('C' . $posi . ':T' . $posi);
//            $active_sheet->setCellValue('C' . $posi, ' TOTAL');
//            $active_sheet->getStyle('C' . $posi)->getFont()->setBold(true)->setSize(12);
//            $active_sheet->getStyle('C' . $posi)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
//        $posis = $posi + 1;
//        $active_sheet->mergeCells('I' . $posis . ':J' . $posis);
//        $active_sheet->setCellValue('I' . $posis, $cantvehidiesel . ' Vehiculos');
//        $active_sheet->getStyle('I' . $posis)->getFont()->setBold(true)->setSize(12);
//        $active_sheet->getStyle('I' . $posis)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
//
//
//        $active_sheet->mergeCells('K' . $posis . ':L' . $posis);
//        $active_sheet->setCellValue('K' . $posis, ' TOTAL DIESEL');
//        $active_sheet->getStyle('K' . $posis)->getFont()->setBold(true)->setSize(12);
//        $active_sheet->getStyle('K' . $posis)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

//        $active_sheet->setCellValue('U' . $posis, $totaldiesel);
//        $active_sheet->getStyle('U' . $posis)->getFont()->setBold(true)->setSize(12);
//        $active_sheet->getStyle('U' . $posis)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
//        $posis = $posi + 2;
//        $active_sheet->mergeCells('I' . $posis . ':J' . $posis);
//        $active_sheet->setCellValue('I' . $posis, $cantvehigasolina . ' Vehiculos');
//        $active_sheet->getStyle('I' . $posis)->getFont()->setBold(true)->setSize(12);
//        $active_sheet->getStyle('I' . $posis)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
//
//        $active_sheet->mergeCells('K' . $posis . ':L' . $posis);
//        $active_sheet->setCellValue('K' . $posis, ' TOTAL GASOLINA');
//        $active_sheet->getStyle('K' . $posis)->getFont()->setBold(true)->setSize(12);
//        $active_sheet->getStyle('K' . $posis)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
//
//        $active_sheet->setCellValue('U' . $posis, $totalgasolina);
//        $active_sheet->getStyle('U' . $posis)->getFont()->setBold(true)->setSize(12);
//        $active_sheet->getStyle('U' . $posis)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $active_sheet->getSheetView()->setZoomScale(100);

        $writer = new Xls($spreadsheet);

        $response = new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            }, \Symfony\Component\HttpFoundation\Response::HTTP_OK, ['Content-Type' => 'application/vnd.ms-excel', 'Pragma' => 'public', 'Cache-Control' => 'maxage=0']);


        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'Cierre Mensual.xls'));
        return $response;
    }

    public function damemesAction($Nromes)
    {
        if ($Nromes == 1) {
            $nombremes = 'Enero';
        } elseif ($Nromes == 2) {
            $nombremes = 'Febrero';
        } elseif ($Nromes == 3) {
            $nombremes = 'Marzo';
        } elseif ($Nromes == 4) {
            $nombremes = 'Abril';
        } elseif ($Nromes == 5) {
            $nombremes = 'Mayo';
        } elseif ($Nromes == 6) {
            $nombremes = 'Junio';
        } elseif ($Nromes == 7) {
            $nombremes = 'Julio';
        } elseif ($Nromes == 8) {
            $nombremes = 'Agosto';
        } elseif ($Nromes == 9) {
            $nombremes = 'Septiembre';
        } elseif ($Nromes == 10) {
            $nombremes = 'Octubre';
        } elseif ($Nromes == 11) {
            $nombremes = 'Noviembre';
        } elseif ($Nromes == 12) {
            $nombremes = 'Diciembre';
        }
        return $nombremes;
    }

    private function unidadesToString($_unidades)
    {
        $_string_unidades = "'" . $_unidades[0] . "'";
        for ($i = 1; $i < count($_unidades); $i++) {
            $_string_unidades .= ",'" . $_unidades[$i] . "'";
        }
        return $_string_unidades;
    }

}