<?php
/**
 * Created by PhpStorm.
 * User: yosley
 * Date: 18/05/2017
 * Time: 08:53 AM
 */

namespace Geocuba\PortadoresBundle\Controller;

use Doctrine\Common\Util\Debug;
use Geocuba\PortadoresBundle\Entity\AutolecturaTresescalas;
use Geocuba\PortadoresBundle\Util\FechaUtil;
use Geocuba\AdminBundle\Util\Util;
use Geocuba\PortadoresBundle\Entity\Paralizacion;
use Doctrine\Common\CommonException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;
use Symfony\Component\HttpFoundation\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ParteDiarioElectricidadController extends Controller
{
    use ViewActionTrait;

    public function getParteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $fecha = date_create_from_format('Y-m-d', $request->get('fecha'));
        $provincia = $request->get('provincia');

        $_data = array();
        $nunidadid = $request->get('unidadid');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $qb = $em->createQueryBuilder();
        $qb->select('servicios')
            ->from('PortadoresBundle:Servicio', 'servicios')
            ->Where($qb->expr()->in('servicios.nunidadid', $_unidades))
            ->andWhere($qb->expr()->eq('servicios.visible', 'true'));

        if (isset($provincia) && $provincia != '') {
            $qb->andWhere('servicios.provinciaid = :provinciaid')
                ->setParameter('provinciaid', $provincia);
        }

        $entities = $qb->orderBy('servicios.nombreServicio', 'ASC')
            ->getQuery()
            ->getResult();

        foreach ($entities as $entity) {
            $turno = $em->getRepository('PortadoresBundle:Turnotrabajo')->findOneBy(array('id' => $entity->getTurnosTrabajo()));
            $acumulados = $this->acumulados($entity->getId(), $fecha);

            $_data[] = array(
                'idservicio' => $entity->getId(),
                'nombre_servicio' => $entity->getNombreServicio(),
                'codigo_cliente' => $entity->getCodigoCliente(),
                'factor_metrocontador' => $entity->getFactorMetrocontador(),
                'MaximaDemandaContratada' => $entity->getMaximaDemandaContratada(),
                'control' => $entity->getControl(),
                'ruta' => $entity->getRuta(),
                'folio' => $entity->getFolio(),
                'direccion' => $entity->getDireccion(),
                'factor_combustible' => $entity->getFactorCombustible(),
                'tipo_servicio' => $entity->getServicioElectrico(),
                'id_turno_trabajo' => $turno->getTurno(),
                'turno_trabajo' => $turno->getId(),
                'turno_trabajo_horas' => $turno->getHoras(),
                'nunidadid' => $entity->getNunidadid()->getId(),
                'nombreunidadid' => $entity->getNunidadid()->getNombre(),
                'provicianid' => $entity->getProvinciaid()->getId(),
                'nombreprovicianid' => $entity->getProvinciaid()->getNombre(),
                'municipioid' => $entity->getMunicipio()->getId(),
                'municipio' => $entity->getMunicipio()->getNombre(),
                'tarifaid' => $entity->getNtarifaid()->getId(),
                'nombretarifaid' => $entity->getNtarifaid()->getNombre(),
                'nactividadid' => $entity->getNactividadid()->getId(),
                'nombrenactividadid' => $entity->getNactividadid()->getNombre(),
                'num_nilvel_actividadid' => $entity->getNactividadid()->getUmActividad()->getId(),
                'nombreum_nilvel_actividadid' => $entity->getNactividadid()->getUmActividad()->getNivelActividad(),
                'acumulado_real' => $acumulados['acumulado_real'],
                'acumulado_plan' => $acumulados['acumulado_plan'],
                'acumulado_real_pico' => $acumulados['acumulado_real_pico'],
                'acumulado_plan_pico' => $acumulados['acumulado_plan_pico'],
            );
        }
        $Fecha_anterior = $fecha->sub(new \DateInterval('P1D'));
        $mes = $Fecha_anterior->format('n');
        $anno = $Fecha_anterior->format('Y');


        $datos_servic = array();
        $datos_prepago = array();
        $datos_mayor = array();

        for ($i = 0; $i < count($_data); $i++) {
            //PLAN DEL SERVI PARA EL MES
            $entities_desglose = $em->getRepository('PortadoresBundle:DesgloseServicios')->findBy(array(
                'idservicio' => $_data[$i]['idservicio'],
                'mes' => $mes
            ));

            $var_desglose = $this->getdesglosediario($_data[$i]['idservicio'], $mes, $anno, $Fecha_anterior);
            if ($entities_desglose) {
                foreach ($entities_desglose as $entity) {
                    $_desgloseservicios = array(
                        'id' => $entity->getId(),
                        'plan_total' => $entity->getPlanTotal(),
                        'plan_pico' => $entity->getPlanPico(),
                        'mes' => $entity->getMes(),
                        'nombre_mes' => $this->damemesAction($entity->getMes()),
                        'fecha' => $entity->getFecha(),
                        'servicio' => $entity->getIdservicio()->getNombreServicio(),
                        'idservicio' => $entity->getIdservicio()->getId(),
                    );
                }


                //DATOS DE LA AUTOLECTURA DEL SERVICIO
                $entities_prepago = $em->getRepository('PortadoresBundle:Autolecturaprepago')->findBy(
                    array(
                        'serviciosid' => $_data[$i]['idservicio'],
                        'fechaLectura' => $Fecha_anterior
                    ));

                $entities_mayor = $em->getRepository('PortadoresBundle:AutolecturaTresescalas')->findBy(
                    array(
                        'serviciosid' => $_data[$i]['idservicio'],
                        'fechaLectura' => $Fecha_anterior
                    ));


                if ($entities_prepago) {
                    foreach ($entities_prepago as $ent_menor) {
                        $hijo = $em->getRepository('PortadoresBundle:Unidad')->findOneBy(array('padreid' => $ent_menor->getServiciosid()->getNunidadid()->getId(), 'visible' => true));
                        $datos_prepago[] = array(
                            'id' => $ent_menor->getId(),
                            'fecha_lectura' => $ent_menor->getFechaLectura()->format('D,j M, Y '),
                            'serviciosid' => $ent_menor->getServiciosid()->getId(),
                            'nombreserviciosid' => $ent_menor->getServiciosid()->getNombreServicio(),
                            'codigo_cliente' => $ent_menor->getServiciosid()->getCodigoCliente(),
                            'control' => $ent_menor->getServiciosid()->getControl(),
                            'ruta' => $ent_menor->getServiciosid()->getRuta(),
                            'folio' => $ent_menor->getServiciosid()->getFolio(),
                            'nunidadid' => $ent_menor->getServiciosid()->getNunidadid()->getId(),
                            'nombreunidadid' => $hijo == null ? $ent_menor->getServiciosid()->getNunidadid()->getPadreid()->getNombre() : $ent_menor->getServiciosid()->getNunidadid()->getNombre(),
                            'provicianid' => $ent_menor->getServiciosid()->getProvinciaid()->getId(),
                            'nombreprovicianid' => $ent_menor->getServiciosid()->getProvinciaid()->getNombre(),
                            'municipioid' => $ent_menor->getServiciosid()->getMunicipio()->getId(),
                            'municipio' => $ent_menor->getServiciosid()->getMunicipio()->getNombre(),

                            'lectura_dia' => $ent_menor->getLecturaDia(),
                            'consumo_total_dia' => $ent_menor->getConsumoTotalDia(),
                            'consumo_total_real' => $ent_menor->getConsumoTotalReal(),
                            'consumo_total_porciento' => $ent_menor->getConsumoTotalPorciento(),
                            'plan_diario' => $ent_menor->getPlanDiario()
                        );
                    }
                    //DATOS DEL PARTE DIARIO
                    for ($p = 0; $p < count($datos_prepago); $p++) {
                        if ($datos_prepago[$p]['serviciosid'] == $_data[$i]['idservicio']) {
                            $datos_servic[] = array(
                                'servicioid' => $datos_prepago[$p]['serviciosid'],
                                'nombre_servicio' => $datos_prepago[$p]['nombreserviciosid'],
                                'codigo_cliente' => $datos_prepago[$p]['codigo_cliente'],
                                'control' => $datos_prepago[$p]['control'],

                                'osde' => 'GEOCUBA',
                                'oace' => 'MINFAR',
                                'ruta' => $datos_prepago[$p]['ruta'],
                                'folio' => $datos_prepago[$p]['folio'],
                                'nunidadid' => $datos_prepago[$p]['nunidadid'],
                                'nombreunidadid' => $datos_prepago[$p]['nombreunidadid'],
                                'provicianid' => $datos_prepago[$p]['provicianid'],
                                'nombreprovicianid' => $datos_prepago[$p]['nombreprovicianid'],
                                'municipioid' => $datos_prepago[$p]['municipioid'],
                                'municipio' => $datos_prepago[$p]['municipio'],

                                'plan_diario' => round($datos_prepago[$p]['plan_diario'] / 1000, 3),
                                'consumo_total_dia' => round($datos_prepago[$p]['consumo_total_dia'] / 1000, 3),
                                'real_plan' => round(($datos_prepago[$p]['consumo_total_dia'] - $datos_prepago[$p]['plan_diario']) / 1000, 3),
                                'porciento' => ($datos_prepago[$p]['plan_diario'] != 0) ? round((($datos_prepago[$p]['consumo_total_dia'] - $datos_prepago[$p]['plan_diario']) / $datos_prepago[$p]['plan_diario']), 2) : 0,

                                'acumulado_real' => round($_data[$i]['acumulado_real'] / 1000, 3),
                                'acumulado_plan' => round($_data[$i]['acumulado_plan'] / 1000, 3),
                                'real_plan_acum' => round(($_data[$i]['acumulado_real'] - $_data[$i]['acumulado_plan']) / 1000, 3),
                                'porcientoacumulado' => round(($_data[$i]['acumulado_plan'] != 0) ? round((($_data[$i]['acumulado_real'] - $_data[$i]['acumulado_plan']) / $_data[$i]['acumulado_plan']), 2) : 0, 2),
                                'acumulado_real_pico' => 0,
                                'acumulado_plan_pico' => 0,
                                'plan_mes' => round($_desgloseservicios['plan_total'] / 1000, 3)
                            );
                        }
                    }

                }
                elseif ($entities_mayor) {
                    foreach ($entities_mayor as $ent_mayor) {
                        $hijo = $em->getRepository('PortadoresBundle:Unidad')->findOneBy(array('padreid' => $ent_mayor->getServiciosid()->getNunidadid()->getId(), 'visible' => true));
                        $perdidas_transf = $this->Perdida_TAction($ent_mayor->getServiciosid(), $ent_mayor);

                        $datos_mayor[] = array(
                            'id' => $ent_mayor->getId(),
                            'fecha_lectura' => $ent_mayor->getFechaLectura()->format('D,j M, Y '),

                            'serviciosid' => $ent_mayor->getServiciosid()->getId(),
                            'nombreserviciosid' => $ent_mayor->getServiciosid()->getNombreServicio(),
                            'codigo_cliente' => $ent_mayor->getServiciosid()->getCodigoCliente(),
                            'control' => $ent_mayor->getServiciosid()->getControl(),
                            'ruta' => $ent_mayor->getServiciosid()->getRuta(),
                            'folio' => $ent_mayor->getServiciosid()->getFolio(),
                            'nunidadid' => $ent_mayor->getServiciosid()->getNunidadid()->getId(),
                            'nombreunidadid' => $hijo == null ? $ent_mayor->getServiciosid()->getNunidadid()->getPadreid()->getNombre() : $ent_mayor->getServiciosid()->getNunidadid()->getNombre(),
                            'provicianid' => $ent_mayor->getServiciosid()->getProvinciaid()->getId(),
                            'nombreprovicianid' => $ent_mayor->getServiciosid()->getProvinciaid()->getNombre(),
                            'municipioid' => $ent_mayor->getServiciosid()->getMunicipio()->getId(),
                            'municipio' => $ent_mayor->getServiciosid()->getMunicipio()->getNombre(),

                            'lectura_pico' => $ent_mayor->getLecturaPico(),
                            'lectura_mad' => $ent_mayor->getLecturaMad(),
                            'lectura_dia' => $ent_mayor->getLecturaDia(),

                            'consumo_total_mad' => ($perdidas_transf !== 0) ? $ent_mayor->getConsumoTotalMad() + ($perdidas_transf / 3):  $ent_mayor->getConsumoTotalMad(),
                            'consumo_total_dia' => ($perdidas_transf !== 0)  ? $ent_mayor->getConsumoTotalDia() + ($perdidas_transf / 2): $ent_mayor->getConsumoTotalDia() ,
                            'consumo_total_real' => ($perdidas_transf !== 0) ? $ent_mayor->getConsumoTotalReal() + $perdidas_transf : $ent_mayor->getConsumoTotalReal(),
                            'consumo_pico_real' => ($perdidas_transf !== 0) ? $ent_mayor->getConsumoPicoReal() + ($perdidas_transf / 6) : $ent_mayor->getConsumoPicoReal() ,

                            'consumo_total_porciento' => $ent_mayor->getConsumoTotalPorciento(),

                            'plan_pico' => $var_desglose[0]['plan_pico'],
                            'plan_diario' => $var_desglose[0]['plan_diario'],
                            'perdidas_transf' => $perdidas_transf
                        );
                    }
                    //DATOS DEL PARTE DIARIO
                    for ($m = 0; $m < count($datos_mayor); $m++) {
                        if ($datos_mayor[$m]['serviciosid'] == $_data[$i]['idservicio']) {
                            $datos_servic[] = array(
                                'servicioid' => $datos_mayor[$m]['serviciosid'],
                                'nombre_servicio' => $datos_mayor[$m]['nombreserviciosid'],
                                'codigo_cliente' => $datos_mayor[$m]['codigo_cliente'],
                                'control' => $datos_mayor[$m]['control'],

                                'osde' => 'GEOCUBA',
                                'oace' => 'MINFAR',
                                'ruta' => $datos_mayor[$m]['ruta'],
                                'folio' => $datos_mayor[$m]['folio'],
                                'nunidadid' => $datos_mayor[$m]['nunidadid'],
                                'nombreunidadid' => $datos_mayor[$m]['nombreunidadid'],
                                'provicianid' => $datos_mayor[$m]['provicianid'],
                                'nombreprovicianid' => $datos_mayor[$m]['nombreprovicianid'],
                                'municipioid' => $datos_mayor[$m]['municipioid'],
                                'municipio' => $datos_mayor[$m]['municipio'],

                                'plan_diario' => round($datos_mayor[$m]['plan_diario'] / 1000, 3),
                                'consumo_total_dia' => round(($datos_mayor[$m]['consumo_total_mad'] + $datos_mayor[$m]['consumo_total_dia'] + $datos_mayor[$m]['consumo_pico_real']) / 1000, 3),
                                'real_plan' => round(($datos_mayor[$m]['consumo_total_real'] - $datos_mayor[$m]['plan_diario']) / 1000, 3),
                                'porciento' => ($datos_mayor[$m]['plan_diario']) ? round((($datos_mayor[$m]['consumo_total_real'] - $datos_mayor[$m]['plan_diario']) / $datos_mayor[$m]['plan_diario']), 2) : 0,

                                'real_plan_acum' => round(($_data[$i]['acumulado_real'] - $_data[$i]['acumulado_plan']) / 1000, 3),
                                'porcientoacumulado' => round(($_data[$i]['acumulado_plan'] != 0) ? round((($_data[$i]['acumulado_real'] - $_data[$i]['acumulado_plan']) / $_data[$i]['acumulado_plan']), 2) : 0, 2),
                                'acumulado_real' => round($_data[$i]['acumulado_real'] / 1000, 3),
                                'acumulado_plan' => round($_data[$i]['acumulado_plan'] / 1000, 3),
                                'acumulado_real_pico' => round($_data[$i]['acumulado_real_pico'] / 1000, 3),
                                'acumulado_plan_pico' => round($_data[$i]['acumulado_plan_pico'] / 1000, 3),
                                'plan_mes' => round($_desgloseservicios['plan_total'] / 1000, 3)
                            );
                        }
                    }
                }
            }
        }
        return new JsonResponse(array('rows' => $datos_servic, 'total' => count($datos_servic)));
    }

    public function Perdida_TAction($servicio, $autolectura)
    {

        $em = $this->getDoctrine()->getManager();
        $servicioid = $servicio->getId();

        $sql1 = "select s.cap_transf1,
                        s.cap_transf2,
                        s.cap_transf3,
                        s.cap_transf4,
                        s.cap_transf5
                from nomencladores.servicio as s where s.id = '$servicioid'";

        $consulta1 = $this->getDoctrine()->getConnection()->fetchAll($sql1);

        $arrayPfe = array();
        $arrayPCu = array();
        for ($i = 1; $i <= 5; $i++) {
            $keyPfe = 'cap_transf' . $i;
            $trans = ($consulta1[0][$keyPfe] !== null) ? $em->getRepository('PortadoresBundle:BancoTransformadores')->find($consulta1[0][$keyPfe]) : null;
            if ($trans) {
                $arrayPfe[] = $trans->getPfe();
                $arrayPCu[] = $trans->getPcu();
            }
        }


        $perdidas_dia = 0;
        for ($i = 0, $iMax = \count($arrayPfe); $i < $iMax; $i++) {
            $kwh = $autolectura->getConsumoTotalMad() + $autolectura->getConsumoTotalDia() + $autolectura->getConsumoPicoReal();
            $kvarh = $autolectura->getConsumo();
            $factor_potencia = ($kwh !== null && $kwh !== 0) ? round(cos(atan($kvarh / $kwh)), 2) : '0';

            $servicio = $em->getRepository('PortadoresBundle:Servicio')->find($servicioid);
            $turno = $em->getRepository('PortadoresBundle:TurnoTrabajo')->find($servicio->getTurnosTrabajo());

            $horas_turno = $turno->getHoras();

            $T1 = $horas_turno;
            $t3 = 24;

            $kvar = round(($T1 !== null) ? $kwh / $T1 * $factor_potencia : '0', 2);

            $kvan = $servicio->getCapBancoMayor();

            $perdidas_transf = ($kvarh !== null) ? $arrayPfe[$i] * $t3 + (($kvar / $kvan) ** 2) * $arrayPCu[$i] * $T1 : '0';


            $perdidas_dia += $perdidas_transf;
        }
        return $perdidas_dia;

    }

    public function getdesglosediario($servicio, $mes, $anno, $fecha)
    {
        $em = $this->getDoctrine()->getManager();

        $desglose = array();
        $entities_desglose = $em->getRepository('PortadoresBundle:DesgloseServicios')->findBy(array(
            'idservicio' => $servicio,
            'mes' => $mes,
            'anno' => $anno,
        ));
        $_desgloseservicios = array();
        if ($entities_desglose) {
            foreach ($entities_desglose as $entity) {
                $_desgloseservicios[] = array(
                    'id' => $entity->getId(),
                    'plan_total' => $entity->getPlanTotal(),
                    'plan_pico' => $entity->getPlanPico(),
                    'mes' => $entity->getMes(),
                    'nombre_mes' => $this->damemesAction($entity->getMes()),
                    'fecha' => $entity->getFecha(),
                    'idservicio' => $entity->getIdservicio()->getId(),
                );
            }

            $entities_desglose_dia = $em->getRepository('PortadoresBundle:DesgloseElectricidad')->findBy(array(
                'iddesgloseServicios' => $_desgloseservicios[0]['id'],
                'fechaDesglose' => $fecha
            ));

            if ($entities_desglose_dia) {
                foreach ($entities_desglose_dia as $entity) {

                    $desglose[] = array(
                        'plan_pico' => $entity->getPlanPico(),
                        'plan_diario' => $entity->getPlanDiario(),
                    );
                }
            }
            foreach ($entities_desglose_dia as $entity) {
                $desglose[] = array(
                    'plan_pico' => $entity->getPlanPico(),
                    'plan_diario' => $entity->getPlanDiario(),
                );
            }
        }
        return $desglose;

    }

    public function acumulados($serviciosid, $fecha)
    {
        $em = $this->getDoctrine()->getManager();
        $fecha_inicial = $fecha;
        $mes = $fecha_inicial->format('m');
        $anno = $fecha_inicial->format('Y');
        $anno_anterior = $anno - 1;


        if ($fecha_inicial->format('d') == 01 && $mes == 01) {
            $fecha_anterior = $anno . '-12' . '-31';
            $fecha_inicio = $anno_anterior . '-12-01';
        } elseif ($fecha_inicial->format('d') == 01 && $mes != 01) {
            $mes_anterior = $mes - 1;
            $fecha_inicio = $anno . '-' . $mes_anterior . '-01';
            $fecha_anterior = FechaUtil::getUltimoDiaMes($mes_anterior, $anno);
        } else {
            $fecha_anteriorArr = explode('-', $fecha->format('Y-m-d'));
            $fecha_anteriorArr[2] -= 1;
            $fecha_anterior = implode('-', $fecha_anteriorArr);
            $fecha_inicio = $anno . '-' . $mes . '-01';
        }

        $acumulado_real = 0;
        $acumulado_plan = 0;
        $acumulado_real_pico = 0;
        $acumulado_plan_pico = 0;

        $entities_auto = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:Autolecturaprepago')->getAutolecturbyMesAbierto($serviciosid, $fecha_inicio, $fecha_anterior);
        $entities_mayor = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:AutolecturaTresescalas')->getAutolecturbyMesAbierto($serviciosid, $fecha_inicio, $fecha_anterior);

        if ($entities_auto) {
            foreach ($entities_auto as $entity) {
                $acumulado_real += $entity->getConsumoTotalReal();
                $acumulado_plan += $entity->getPlanDiario();
            }
        } elseif ($entities_mayor) {
            foreach ($entities_mayor as $entity) {
                $servicio = $em->getRepository('PortadoresBundle:Servicio')->find($serviciosid);
                $perdidas_transf = $this->Perdida_TAction($servicio, $entity);
                /**@var AutolecturaTresescalas $entity*/
                if ($entity->getConsumoPicoReal() != 0) {
                    $acumulado_real_pico += round(floatval($entity->getConsumoPicoReal()) + ($perdidas_transf / 6), 2);
                    $acumulado_plan_pico += round($entity->getConsumoPicoPlan(), 2);
                }
                $acumulado_real += round(floatval($entity->getConsumoTotalReal()) + $perdidas_transf, 2);
                $acumulado_plan += round($entity->getConsumoTotalPlan(), 2);
            }
        }

        $acumulados = array(
            'id' => $serviciosid,
            'acumulado_real' => $acumulado_real,
            'acumulado_plan' => $acumulado_plan,
            'acumulado_real_pico' => $acumulado_real_pico,
            'acumulado_plan_pico' => $acumulado_plan_pico,
        );

        return $acumulados;
    }

    public function printAction(Request $request)
    {

        $data = json_decode($request->get('store'));
        $fecha = $request->get('fecha');
        $fechaArray = explode('T', $fecha);
        $fechaStr = $fechaArray[0];

        $_html = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
        <html xmlns='http://www.w3.org/1999/xhtml'>
        <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <title> Modelo No.5 Reporte del desglose metro a metro y la autolectura diaria</title>
        <style>
            table.main {
                border:0px solid;
            border-radius:5px 5px 5px 5px;
            font-family: 'Times New Roman', Times, serif;
            font-size: 18px;
            }
        .sinborde{
                border-color:#FFF;
            }
            .bordearriba{
                border-top:#000;
            }
        table.main1 {	border:0px solid;
            border-radius:5px 5px 5px 5px;
            font-family: 'Times New Roman', Times, serif;
            font-size: 18px;
        }
        </style>
        </head>
        <body>
        <table  border='1' >
           <tr>
               <td colspan='19' style='text-align: center'><p>Reporte diario de todos los servicios eléctricos del OACE y OSDE por municipio y provincia
               <br>Fecha : $fechaStr</p></td>                     
           </tr> 
           <tr>
               <td style='width:2%' >No</td>
               <td style='text-align: center'>Provincia</td>
               <td style='text-align: center'>Municipio</td>
               <td style='text-align: center'>OACE</td>
               <td style='text-align: center'>OSDE</td>
               <td style='text-align: center'>CONTROL</td>
               <td style='text-align: center'>RUTA</td>
               <td style='text-align: center'>FOLIO</td>
               <td style='text-align: center'>Nombre del servicio</td>
               <td style='text-align: center'>Nombre de la empresa</td>
               <td style='text-align: center'><p>Plan Total del Mes</p><p>(MWh)</p></td>
               <td style='text-align: center'><p>Plan acumulado</p>(MWh)</td>
               <td style='text-align: center'>Real acumulado (MWh)</td>
               <td style='text-align: center'>Real-Plan</td>
               <td style='text-align: center'>%</td>
               <td style='text-align: center'>Plan día (MWh)</td>
               <td style='text-align: center'>Real día (MWh)</td>
               <td style='text-align: center'>Real-Plan</td>
               <td style='text-align: center'>%</td>
           </tr>
          ";
        $no = 1;

        for ($i = 0; $i < count($data); $i++) {
            $_html .= "<tr>
            <td width='49' style='text-align: center'>" . $no . "</td>
            <td width='127' style='text-align: center'>" . $data[$i]->nombreprovicianid . "</td>
            <td width='134' style='text-align: center'>" . $data[$i]->municipio . "</td>
            <td width='95' style='text-align: center'>" . 'MINFAR' . "</td>
            <td style='text-align: center'>" . 'Geocuba' . "</td>
            <td style='text-align: center'>" . $data[$i]->control . "</td>
            <td style='text-align: center'>" . $data[$i]->ruta . "</td>
            <td style='text-align: center'>" . $data[$i]->folio . "</td>
            <td style='text-align: center'>" . $data[$i]->nombre_servicio . "</td>
            <td style='text-align: center'>" . $data[$i]->nombreunidadid . "</td>
            <td style='text-align: center'>" . $data[$i]->plan_mes . "</td>
            <td style='text-align: center'>" . $data[$i]->acumulado_plan . "</td>
            <td style='text-align: center'>" . $data[$i]->acumulado_real . "</td>
            <td style='text-align: center'>" . $data[$i]->real_plan_acum . "</td>
            <td style='text-align: center'>" . $data[$i]->porcientoacumulado . "</td>
            <td style='text-align: center'>" . $data[$i]->plan_diario . "</td>
            <td style='text-align: center'>" . $data[$i]->consumo_total_dia . "</td>
            <td style='text-align: center'>" . $data[$i]->real_plan . "</td>
            <td style='text-align: center'>" . $data[$i]->porciento . "</td>
           </tr>";
            $no++;
        }
        return new Response(json_encode(array('success' => true, 'html' => $_html)));
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

}