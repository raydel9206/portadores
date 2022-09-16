<?php
/**
 * Created by PhpStorm.
 * User: orlando
 * Date: 07/10/2015
 * Time: 9:58
 */

namespace Geocuba\PortadoresBundle\Controller;

use Doctrine\Common\Util\Debug;
use Geocuba\PortadoresBundle\Util\FechaUtil;
use Geocuba\AdminBundle\Util\Util;
use Geocuba\PortadoresBundle\Entity\Paralizacion;
use Doctrine\Common\CommonException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ParalizacionController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $_matricula = trim($request->get('matricula'));
        $start = $request->get('start');
        $limit = $request->get('limit');
        $nunidadid = $request->get('nunidadid');

        $_data = array();
        $em = $this->getDoctrine()->getManager();

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $entities = $em->getRepository('PortadoresBundle:Paralizacion')->buscarParalizacion($_matricula, $_unidades, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:Paralizacion')->buscarParalizacion($_matricula, $_unidades, $start, $limit, true);

        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'motivo' => $entity->getMotivo(),
                'en_sasa' => $entity->getEnSasa(),
                'nro_pedido' => $entity->getNroPedido(),
                'vehiculoid' => $entity->getVehiculoid()->getId(),
                'matricula' => $entity->getVehiculoid()->getMatricula(),
                'tipo_combustible' => $entity->getVehiculoid()->getNtipoCombustibleid()->getCodigo(),
                'modelo' => $entity->getVehiculoid()->getNmodeloid()->getNombre(),
                'modeloid' => $entity->getVehiculoid()->getNmodeloid()->getId(),
                'marca' => $entity->getVehiculoid()->getNmodeloid()->getMarcaVehiculoid()->getNombre() . ' ' . $entity->getVehiculoid()->getNmodeloid()->getNombre(),
                'nunidadid' => $entity->getVehiculoid()->getNunidadid()->getId(),
                'nunidadnombre' => $entity->getVehiculoid()->getNunidadid()->getNombre(),
                'denominacion' => $entity->getVehiculoid()->getNdenominacionVehiculoid()->getNombre(),
                'fecha' => ($entity->getFecha() != null) ? $entity->getFecha()->format('d/m/Y') : null,
                'fecha_marcha' => ($entity->getFechaMarcha() != null) ? $entity->getFechaMarcha()->format('d/m/Y') : null,
                'observaciones' => $entity->getObservaciones(),
                'taller' => $entity->getTaller(),
            );
        }
        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $motivo = $request->get('motivo');
        $en_sasa = $request->get('en_sasa');
        $nro_pedido = $request->get('nro_pedido');
        $vehiculoid = $request->get('vehiculoid');
        $fecha = $request->get('fecha');
        $fechaMarcha = $request->get('fecha_marcha');
        $observaciones = $request->get('observaciones');
        $taller = $request->get('taller');
        $var = date_create_from_format('d/m/Y', $fecha);

        $entities_lic = $em->getRepository('PortadoresBundle:Paralizacion')->findBy(array(
            'vehiculoid' => $vehiculoid,
            'visible' => true
        ));
        if ($entities_lic)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Este vehículo ya está paralizado.'));

        $entity = new Paralizacion();
        $entity->setMotivo($motivo);
        $entity->setEnSasa($en_sasa);
        $entity->setNroPedido((isset($nro_pedido)) ? $nro_pedido : '');
        $entity->setVehiculoid($em->getRepository('PortadoresBundle:Vehiculo')->find($vehiculoid));
        $entity->setVisible(true);
        $entity->setFecha($var);
        $entity->setObservaciones($observaciones);
        $entity->setTaller($taller);
        $entity_vehiculo = $em->getRepository('PortadoresBundle:Vehiculo')->find($vehiculoid);
        $entity_vehiculo->setParalizado(true);

        try {
            $em->persist($entity);
            $em->merge($entity_vehiculo);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Vehículo paralizado con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function modAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $motivo = $request->get('motivo');
        $en_sasa = $request->get('en_sasa');
        $nro_pedido = $request->get('nro_pedido');
        $vehiculoid = trim($request->get('vehiculoid'));
        $fecha = $request->get('fecha');
        $fechaMarcha = $request->get('fecha_marcha');
        $observaciones = $request->get('observaciones');
        $taller = $request->get('taller');
        $var = date_create_from_format('d/m/Y', $fecha);


        $entity = $em->getRepository('PortadoresBundle:Paralizacion')->find($id);
        $entity->setMotivo($motivo);
        $entity->setEnSasa($en_sasa);
        $entity->setNroPedido((isset($nro_pedido)) ? $nro_pedido : '');
        $entity->setVehiculoid($em->getRepository('PortadoresBundle:Vehiculo')->find($vehiculoid));
        $entity->setFecha($var);
        $entity->setObservaciones($observaciones);
        $entity->setTaller($taller);
        try {
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Paralización modificada con éxito.'));
            return $response;
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function delAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');

        $entity = $em->getRepository('PortadoresBundle:Paralizacion')->find($id);
        $entity_vehiculo = $em->getRepository('PortadoresBundle:Vehiculo')->find($entity->getVehiculoid()->getId());
        $entity->setVisible(false);
        $entity_vehiculo->setParalizado(false);

        try {
            $em->persist($entity);
            $em->merge($entity_vehiculo);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Paralización eliminada con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function ponerMarchaAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');

        $fechaMarcha = $request->get('fecha_marcha');
        $fecha = date_create_from_format('d/m/Y', $fechaMarcha);

        $entity = $em->getRepository('PortadoresBundle:Paralizacion')->find($id);
        $entity->setFechaMarcha($fecha);

        $entity_vehiculo = $em->getRepository('PortadoresBundle:Vehiculo')->find($entity->getVehiculoid());
        $entity_vehiculo->setParalizado(false);


        try {
            $em->persist($entity);
            $em->merge($entity_vehiculo);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Vehículo puesto en marcha con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function printAction(Request $request)
    {
        $store = json_decode($request->get('store'));
        $unidad = $request->get('unidad');

        $_html = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
        <html xmlns='http://www.w3.org/1999/xhtml'>
        <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <title>CDT VEHÍCULOS PARALIZADOS</title>
        <style>

            table {
                border:0 solid;
                border-radius:0;
                font-family: 'Arial', serif;
                font-size: 14px;
                border-collapse: collapse;
            }

            td{
                width: 40px;
                height: 20px;
            }

        </style>
        </head>

        <body>
        <header>
            <img  src='../../assets/img/PNG/logo.png' height='60px' width='160px'>
        </header>
        <table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">
              <tr>                
              
                <td width=\"100%\" align=\"center\"  style=\"font-size:14px\"><strong>Disposición Técnica</strong></td>
                
                  
              </tr>
            </table>
         <br>
        <table cellspacing='0' cellpadding='5' border='1' width='100%'>
               
         <tr>
            <td style='text-align: center;width: 10%'><strong>L&iacutenea</strong></td>
            <td style='text-align: center;width: 10%'><strong>Total</strong></td>
            <td style='text-align: center;width: 10%;'><strong>Paralizados</strong></td>
            <td style='text-align: center;width: 15%;'><strong>CDT</strong></td>

         </tr>";
        $sumaTotal = 0;
        $sumaParalizados = 0;
        $cdt = 0;
        for ($i = 0; $i < sizeof($store); $i++) {
            $sumaTotal += $store[$i]->total;
            $sumaParalizados += $store[$i]->paralizados;
            $cdt = ($sumaTotal > 0) ? round(($sumaParalizados * 100) / $sumaTotal, 2) : 100;
            $_html .= "
         <tr>
            <td style='text-align: center;width: 10%'>" . $store[$i]->denominacion_nombre . "</td>
            <td style='text-align: center;width: 10%'>" . $store[$i]->total . "</td>
            <td style='text-align: center;width: 10%;'>" . $store[$i]->paralizados . "</td>
            <td style='text-align: center;width: 15%;'>" . $store[$i]->cdt . "</td>
         </tr>
            ";
        }
        $_html .= "<tr>
            <td style='text-align: center;width: 10%'><strong>Total</strong></td>
            <td style='text-align: center;width: 10%'><strong>" . $sumaTotal . "</strong></td>
            <td style='text-align: center;width: 10%'><strong>" . $sumaParalizados . "</strong></td>
            <td style='text-align: center;width: 10%'><strong>" . $cdt . "</strong></td>

         </tr>";

        $_html.="</table>
                </body>
            </html>";
        return new JsonResponse(array('success' => true, 'html' => $_html));
    }

    public function printParqueAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $unidadid = $request->get('unidadid');

        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();

        $mes = $session->get('selected_month');
        $anno = $session->get('selected_year');

        $name_mes = FechaUtil::getNombreMes($mes);

        $fechaDesde =  $anno . '-' . $mes . '-' . '1';
        $fechaHasta =  FechaUtil::getUltimoDiaMes($mes, $anno);

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($unidadid), $_unidades);

        $entities = $em->getRepository('PortadoresBundle:Vehiculo')->buscarVehiculo(null, null, $_unidades, 0, 10000);

        $_html = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
        <html xmlns='http://www.w3.org/1999/xhtml'>
        <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <title>CDT VEHÍCULOS PARALIZADOS</title>
        <style>

            table {
                border:0 solid;
                border-radius:0;
                font-family: 'Arial', serif;
                font-size: 14px;
                border-collapse: collapse;
            }

            td{
                width: 40px;
                height: 20px;
            }

        </style>
        </head>

        <body>
        <header>
            <img  src='../../assets/img/PNG/logo.png' height='60px' width='160px'>
        </header>
        <table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">
              <tr>                
              
                <td width=\"2%\" align=\"center\">&nbsp;</td>
                <td width=\"30%\" align=\"center\"  style=\"font-size:14px\"><strong>Disposición Técnica</strong></td>
                <td width=\"31%\" align=\"center\"  style=\"font-size:14px\"><strong>MES: $name_mes</strong>&nbsp;
                  <strong>AÑO: $anno</strong></td>
                  
              </tr>
            </table>
         <br>
        <table cellspacing='0' cellpadding='5' border='1' width='100%'>
               
         <tr>
            <td style='text-align: center;width: 10%'><strong>Matrícula</strong></td>
            <td style='text-align: center;width: 20%'><strong>Marca/Modelo</strong></td>
            <td style='text-align: center;width: 10%;'><strong>Estado</strong></td>
            <td style='text-align: center;width: 15%;'><strong>Motivo</strong></td>

         </tr>";
        foreach ($entities as $entity) {

            $qb = $em->createQueryBuilder();
            $qb->select('p.motivo')
                ->from('PortadoresBundle:Paralizacion', 'p')
                ->Where('p.fecha between :fechaDesde and :fechaHasta')
                ->orWhere('p.fechaMarcha between :fechaDesde and :fechaHasta')
                ->andWhere('p.vehiculoid = :vehiculo')
                ->setParameter('fechaDesde', $fechaDesde)
                ->setParameter('fechaHasta', $fechaHasta)
                ->setParameter('vehiculo', $entity->getId())
                ->andWhere('p.visible = true')
                ->setMaxResults(1);
            $result = $qb->getQuery()->getResult();


             $motivo = count($result)==0?'':$result[0]['motivo'];
             $estado = count($result)==0?'Activo':'Paralizado';

            $_html .= "
         <tr>
            <td style='text-align: center;width: 10%'>" . $entity->getMatricula() . "</td>
            <td style='text-align: center;width: 10%'>" . $entity->getNmodeloid()->getMarcaVehiculoid()->getNombre() . ' ' . $entity->getNmodeloid()->getNombre() . "</td>
            <td style='text-align: center;width: 10%;'>" . $estado . "</td>
            <td style='text-align: center;width: 15%;'>" . $motivo. "</td>

         </tr>
            ";
        }

        $_html .= "</table>
                </body>
            </html>";
        //var_dump($store[0]->motivo);die;
        return new JsonResponse(array('success' => true, 'html' => $_html));


        var_dump(count($entities));


    }

    public function printParalizacionesAction(Request $request)
    {
        $store = json_decode($request->get('store'));
        $unidad = $request->get('unidad');

        $_html = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
        <html xmlns='http://www.w3.org/1999/xhtml'>
        <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <title>CDT VEHÍCULOS PARALIZADOS</title>
        <style>

            table {
                border:0 solid;
                border-radius:0;
                font-family: 'Arial', serif;
                font-size: 14px;
                border-collapse: collapse;
            }

            td{
                width: 40px;
                height: 20px;
            }

        </style>
        </head>

        <body>
        <header>
            <img  src='../../assets/img/PNG/logo.png' height='60px' width='160px'>
        </header>
        <table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">
              <tr>                
              
                <td width=\"2%\" align=\"center\">&nbsp;</td>
                <td width=\"30\" align=\"center\"  style=\"font-size:14px\"><strong>Disposición Técnica</strong></td>
                <td width=\"31%\" align=\"center\"  style=\"font-size:14px\"><strong>Fecha: 18/02/2018</strong>&nbsp;
                  
              </tr>
            </table>
         <br>
        <table cellspacing='0' cellpadding='5' border='1' width='100%'>
               
         <tr>
            <td style='text-align: center;width: 10%'><strong>L&iacutenea</strong></td>
            <td style='text-align: center;width: 10%'><strong>Total</strong></td>
            <td style='text-align: center;width: 10%;'><strong>Paralizados</strong></td>
            <td style='text-align: center;width: 15%;'><strong>CDT</strong></td>

         </tr>";
        for ($i = 0; $i < sizeof($store); $i++) {
//            Debug::dump($store[$i]->denominacion_nombre);

            $_html .= "
         <tr>
            <td style='text-align: center;width: 10%'>" . $store[$i]->denominacion_nombre . "</td>
            <td style='text-align: center;width: 10%'>" . $store[$i]->total . "</td>
            <td style='text-align: center;width: 10%;'>" . $store[$i]->paralizados . "</td>
            <td style='text-align: center;width: 15%;'>" . $store[$i]->cdt . "</td>

         </tr>
            ";
        }
        $_html .= "</table>
                </body>
            </html>";
        //var_dump($store[0]->motivo);die;
        return new JsonResponse(array('success' => true, 'html' => $_html));
    }

    public function exportAction(Request $request)
    {
        $nunidadid = $request->get('nunidadid');

        $_data = array();
        $em = $this->getDoctrine()->getManager();

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $entities = $em->getRepository('PortadoresBundle:Paralizacion')->buscarParalizacion(null, $_unidades);

        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'motivo' => $entity->getMotivo(),
                'en_sasa' => $entity->getEnSasa(),
                'nro_pedido' => $entity->getNroPedido(),
                'vehiculoid' => $entity->getVehiculoid()->getId(),
                'matricula' => $entity->getVehiculoid()->getMatricula(),
                'denominacion' => $entity->getVehiculoid()->getNdenominacionVehiculoid()->getNombre(),
                'tipo_combustible' => $entity->getVehiculoid()->getNtipoCombustibleid()->getNombre(),
                'modelo' => $entity->getVehiculoid()->getNmodeloid()->getNombre(),
                'modeloid' => $entity->getVehiculoid()->getNmodeloid()->getId(),
                'marca' => $entity->getVehiculoid()->getNmodeloid()->getMarcaVehiculoid()->getNombre(),
                'nunidadid' => $entity->getVehiculoid()->getNunidadid()->getId(),
                'nunidadnombre' => $entity->getVehiculoid()->getNunidadid()->getNombre(),
                'fecha' => ($entity->getFecha() != null) ? $entity->getFecha()->format('d/m/Y') : null,
                'fecha_marcha' => ($entity->getFechaMarcha() != null) ? $entity->getFechaMarcha()->format('d/m/Y') : null,
                'observaciones' => $entity->getObservaciones(),
                'taller' => $entity->getTaller(),
            );
        }
        $session = $request->getSession();
        //TODO Tema session fecha
        $anno = $request->get('current_year');
        $mes = FechaUtil::getNombreMes($session->get('current_month'));

        $spreadsheet = new Spreadsheet();

        $active_sheet = $spreadsheet->getActiveSheet();

        $active_sheet->setTitle('Paralizaciones');
        //Header
        $active_sheet->mergeCells('A1:K1');
        $active_sheet->mergeCells('A2:K2');
        $active_sheet->mergeCells('A3:K3');
        $active_sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
        $active_sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
        $active_sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
        $active_sheet->setCellValue('A1', 'ESTADO DE LA PARALIZACIÓN DE LOS VEHÍCULOS DE ' . strtoupper($em->getRepository('PortadoresBundle:Unidad')->find($_unidades[0])->getNombre()));
        $active_sheet->setCellValue('A2', 'MES: ' . $mes . '    AÑo/' . $anno);

        $active_sheet->setCellValue('A4', 'Nro.');
        $active_sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $active_sheet->setCellValue('B4', 'Matrícula ');
        $active_sheet->getStyle('B4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $active_sheet->setCellValue('C4', 'Marca');
        $active_sheet->getStyle('C4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $active_sheet->setCellValue('D4', 'Modelo');
        $active_sheet->getStyle('D4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $active_sheet->setCellValue('E4', 'Tipo');
        $active_sheet->getStyle('E4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $active_sheet->setCellValue('F4', 'Fecha de paralizacion');
        $active_sheet->getStyle('F4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $active_sheet->setCellValue('G4', 'Causas de la paralizacion');
        $active_sheet->getStyle('G4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $active_sheet->setCellValue('H4', 'Equipos paralizados en taller');
        $active_sheet->getStyle('H4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $active_sheet->setCellValue('I4', 'Nro. pedido');
        $active_sheet->getStyle('I4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $active_sheet->setCellValue('J4', 'OBSERV');
        $active_sheet->getStyle('J4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $active_sheet->setCellValue('K4', 'Fecha de puesta en marcha');
        $active_sheet->getStyle('K4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $pos = 0;
        for ($row = 0; $row < count($_data); $row++) {
            $pos = 5 + $row;
            $active_sheet->setCellValue('A' . $pos, $row + 1);
            $active_sheet->setCellValue('B' . $pos, $_data[$row]['matricula']);
            $active_sheet->setCellValue('C' . $pos, $_data[$row]['marca']);
            $active_sheet->setCellValue('D' . $pos, $_data[$row]['modelo']);
            $active_sheet->setCellValue('E' . $pos, $_data[$row]['denominacion']);
            $active_sheet->setCellValue('F' . $pos, $_data[$row]['fecha']);
            $active_sheet->setCellValue('G' . $pos, $_data[$row]['motivo']);
            $active_sheet->setCellValue('H' . $pos, $_data[$row]['taller']);
            $active_sheet->setCellValue('I' . $pos, $_data[$row]['nro_pedido']);
            $active_sheet->setCellValue('J' . $pos, $_data[$row]['observaciones']);
            $active_sheet->setCellValue('K' . $pos, $_data[$row]['fecha_marcha']);
        }

        //TODO Cojer del documento de Pie de Firma
//        $_elaborado = $request->get('elaboradoid');
//        $_revisado = $request->get('revisadoid');
//        $_aprobado = $request->get('aprobadoid');

//        $pos += 5;
//        $active_sheet->mergeCells('A' . $pos . ':C' . $pos);
//        $active_sheet->getStyle('A' . $pos)->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
//        $active_sheet->setCellValue('A' . $pos, 'Elaborado: ' . $em->getRepository('PortadoresBundle:Persona')->find($_elaborado)->getNombre());
//        $active_sheet->setCellValue('E' . $pos, 'Revisado: ' . $em->getRepository('PortadoresBundle:Persona')->find($_revisado)->getNombre());
//        $active_sheet->setCellValue('G' . $pos, 'Aprobado: ' . $em->getRepository('PortadoresBundle:Persona')->find($_aprobado)->getNombre());
//
//        $pos += 1;
//        $active_sheet->mergeCells('A' . $pos . ':C' . $pos);
//        $active_sheet->getStyle('A' . $pos)->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
//        $active_sheet->setCellValue('A' . $pos, $em->getRepository('PortadoresBundle:Persona')->find($_elaborado)->getCargoid()->getNombre());
//        $active_sheet->setCellValue('E' . $pos, $em->getRepository('PortadoresBundle:Persona')->find($_revisado)->getCargoid()->getNombre());
//        $active_sheet->setCellValue('G' . $pos, $em->getRepository('PortadoresBundle:Persona')->find($_aprobado)->getCargoid()->getNombre());

        $pos += 2;
        $active_sheet->mergeCells('A' . $pos . ':C' . $pos);
        $active_sheet->setCellValue('A' . $pos, '_______________');
        $active_sheet->getStyle('A' . $pos)->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);

        $active_sheet->setCellValue('E' . $pos, '_______________');
        $active_sheet->getStyle('E' . $pos)->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);

        $active_sheet->setCellValue('G' . $pos, '_______________');
        $active_sheet->getStyle('G' . $pos)->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);


        $pos += 1;
        $active_sheet->mergeCells('A' . $pos . ':C' . $pos);
        $active_sheet->setCellValue('A' . $pos, 'FIRMA');
        $active_sheet->getStyle('A' . $pos)->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);

        $active_sheet->setCellValue('E' . $pos, 'FIRMA');
        $active_sheet->getStyle('E' . $pos)->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);

        $active_sheet->setCellValue('G' . $pos, 'FIRMA');
        $active_sheet->getStyle('G' . $pos)->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);


        $writer = new Xls($spreadsheet);

        $response = new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            }, \Symfony\Component\HttpFoundation\Response::HTTP_OK, ['Content-Type' => 'application/vnd.ms-excel', 'Pragma' => 'public', 'Cache-Control' => 'maxage=0']);


        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'Paralizaciones.xls'));
        return $response;
    }

    public function loadCdtAction(Request $request)
    {
        $nunidadid = $request->get('nunidadid');
        $em = $this->getDoctrine()->getManager();

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $entities_denominaciones = $em->getRepository('PortadoresBundle:DenominacionVehiculo')->findByVisible(true);
        $_data = array();
        foreach ($entities_denominaciones as $entity) {
            $total = $em->getRepository('PortadoresBundle:Vehiculo')->buscarVehiculoPorDenominacion($entity->getId(), $_unidades);
            if (count($total) > 0) {
                $causa = '';
                foreach ($total as $total_entity) {
                    $entities_paralizacion = $em->getRepository('PortadoresBundle:Paralizacion')->buscarParalizacion($total_entity->getMatricula(), $_unidades);
                    foreach ($entities_paralizacion as $entity_paralizacion)
                        $causa .= ' / ' . $total_entity->getNmodeloid()->getMarcaVehiculoid()->getNombre() . '-' . $total_entity->getNmodeloid()->getNombre() . '(' . $entity_paralizacion->getMotivo() . ')';
                }

                $paralizados = count($em->getRepository('PortadoresBundle:Vehiculo')->buscarVehiculoParalizadosPorDenominacion($entity->getId(), $_unidades));
                $_data[] = array(
                    'denominacionid' => $entity->getId(),
                    'denominacion_nombre' => $entity->getNombre(),
                    'total' => count($total),
                    'paralizados' => $paralizados,
                    'causas' => $causa,
                    'cdt' => ($paralizados > 0) ? round(((count($total) - $paralizados) * 100) / count($total), 2) : 100,
                );
            }
        }

        return new JsonResponse(array('rows' => $_data));
    }
//    public function recursivoAction($id)
//    {
//
////
//        $_data = array();
//        $em = $this->getDoctrine()->getManager();
//        $hoja = true;
//        $id_U = $id;
//        $tree = array();
//
//        $entitiess = $em->getRepository('PortadoresBundle:NestructuraUnidades')->findByPadreid($id);
////        $tree[]=$id;
//        array_push($tree,$id);
//
//        if ($entitiess) {
//
//            foreach ($entitiess as $entity) {
////                        print_r($entity->getNunidadid()->getNombre());die;
////                $tree[]=$this->recursivoAction($entity->getNunidadid()->getId());
//                array_push($tree,$entity->getNunidadid()->getId());
////                print_r($tree);die;
//                $this->recursivoAction($entity->getNunidadid()->getId());
////                        $tree[] = array(
////                            'id' => $entity->getNunidadid()->getId(),
////                            'hijos'=>)
////                        );
//            }
//
//        }
//
////        for($i=0;$i<count($tree);$i++)
////        {
////            array_push($tree,$this->recursivoAction($tree[$i]));
////        }
//
//
//
////        print_r($tree);die;
//
//
//        return $tree;
//    }
    public function exportCdtToExcelAction(Request $request)
    {
        $data = json_decode($request->get('store'));
        $em = $this->getDoctrine()->getManager();

        $session = $request->getSession();
        $anno = $session->get('current_year');
        $mes = FechaUtil::getNombreMes($session->get('current_month'));

        $spreadsheet = new Spreadsheet();

        $active_sheet = $spreadsheet->getActiveSheet();

        $active_sheet->setTitle('Paralizaciones');
        //Header
        $active_sheet->mergeCells('A1:K1');
        $active_sheet->mergeCells('A2:K2');
        $active_sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
        $active_sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
        $active_sheet->setCellValue('A1', 'COMPORTAMIENTO DEL CDT EN EL MES DE ' . strtoupper($mes) . ' ' . $anno);

        $active_sheet->setCellValue('A4', 'LINEA');
        $active_sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $active_sheet->setCellValue('B4', 'TOTAL EQ.');
        $active_sheet->getStyle('B4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $active_sheet->setCellValue('C4', 'PARALIZADOS');
        $active_sheet->getStyle('C4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $active_sheet->setCellValue('D4', 'CAUSAS');
        $active_sheet->getStyle('D4')->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
        $active_sheet->setCellValue('E4', 'CDT');
        $active_sheet->getStyle('E4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $pos = 5;
        $total = 0;
        $paralizados = 0;
        for ($row = 0; $row < count($data); $row++) {
            $pos += $row;
            $active_sheet->setCellValue('A' . $pos, $data[$row]->denominacion_nombre);
            $active_sheet->setCellValue('B' . $pos, $data[$row]->total);
            $active_sheet->setCellValue('C' . $pos, $data[$row]->paralizados);
            $active_sheet->setCellValue('D' . $pos, $data[$row]->causas);
            $active_sheet->setCellValue('E' . $pos, $data[$row]->cdt);
            $total += $data[$row]->total;
            $paralizados += $data[$row]->paralizados;
        }
        $pos += 1;
        $active_sheet->setCellValue('A' . $pos, 'TOTAL');
        $active_sheet->setCellValue('B' . $pos, $total);
        $active_sheet->setCellValue('C' . $pos, $paralizados);
        $active_sheet->setCellValue('E' . $pos, ($paralizados > 0) ? (100 - ($paralizados * 100 / $total)) / 100 : 1.00);

        $_elaborado = $request->get('elaboradoid');
        $_revisado = $request->get('revisadoid');
        $_aprobado = $request->get('aprobadoid');

        $pos += 5;
        $active_sheet->mergeCells('A' . $pos . ':B' . $pos);
        $active_sheet->mergeCells('C' . $pos . ':D' . $pos);
        $active_sheet->getStyle('A' . $pos)->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
        $active_sheet->getStyle('C' . $pos)->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
        $active_sheet->getStyle('E' . $pos)->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
        $active_sheet->setCellValue('A' . $pos, 'Elaborado: ' . $em->getRepository('PortadoresBundle:Persona')->find($_elaborado)->getNombre());
        $active_sheet->setCellValue('C' . $pos, 'Revisado: ' . $em->getRepository('PortadoresBundle:Persona')->find($_revisado)->getNombre());
        $active_sheet->setCellValue('E' . $pos, 'Aprobado: ' . $em->getRepository('PortadoresBundle:Persona')->find($_aprobado)->getNombre());

        $pos += 1;
        $active_sheet->mergeCells('A' . $pos . ':B' . $pos);
        $active_sheet->mergeCells('C' . $pos . ':D' . $pos);
        $active_sheet->getStyle('A' . $pos)->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
        $active_sheet->getStyle('C' . $pos)->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
        $active_sheet->getStyle('E' . $pos)->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
        $active_sheet->setCellValue('A' . $pos, $em->getRepository('PortadoresBundle:Persona')->find($_elaborado)->getCargoid()->getNombre());
        $active_sheet->setCellValue('C' . $pos, $em->getRepository('PortadoresBundle:Persona')->find($_revisado)->getCargoid()->getNombre());
        $active_sheet->setCellValue('E' . $pos, $em->getRepository('PortadoresBundle:Persona')->find($_aprobado)->getCargoid()->getNombre());

        $pos += 2;
        $active_sheet->mergeCells('A' . $pos . ':B' . $pos);
        $active_sheet->setCellValue('A' . $pos, '_______________');
        $active_sheet->getStyle('A' . $pos)->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);

        $active_sheet->mergeCells('C' . $pos . ':D' . $pos);
        $active_sheet->setCellValue('C' . $pos, '_______________');
        $active_sheet->getStyle('C' . $pos)->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);

        $active_sheet->setCellValue('E' . $pos, '_______________');
        $active_sheet->getStyle('E' . $pos)->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);


        $pos += 1;
        $active_sheet->mergeCells('A' . $pos . ':B' . $pos);
        $active_sheet->setCellValue('A' . $pos, 'FIRMA');
        $active_sheet->getStyle('A' . $pos)->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);

        $active_sheet->mergeCells('C' . $pos . ':D' . $pos);
        $active_sheet->setCellValue('C' . $pos, 'FIRMA');
        $active_sheet->getStyle('C' . $pos)->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);

        $active_sheet->setCellValue('E' . $pos, 'FIRMA');
        $active_sheet->getStyle('E' . $pos)->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);


        $writer = new Xls($spreadsheet);

        $response = new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            }, \Symfony\Component\HttpFoundation\Response::HTTP_OK, ['Content-Type' => 'application/vnd.ms-excel', 'Pragma' => 'public', 'Cache-Control' => 'maxage=0']);


        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'Paralizaciones.xls'));
        return $response;
    }
}