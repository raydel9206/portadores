<?php
/**
 * Created by PhpStorm.
 * User: yosley
 * Date: 10/04/2017
 * Time: 08:57 AM
 */

namespace Geocuba\PortadoresBundle\Controller;


use ClassesWithParents\D;
use function DeepCopy\deep_copy;
use Geocuba\AdminBundle\Util\FechaUtil;
use Geocuba\PortadoresBundle\Entity\Autolecturaprepago;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Util\Debug;
use Geocuba\Utils\ViewActionTrait;

class AutolecturaPrepagoController extends Controller
{

    use ViewActionTrait;

    public function getautolecturasbyserviciosAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $idservicios = $request->get('idservicios');

        $mes = $session->get('selected_month');
        $anno = $session->get('selected_year');


        if ($mes !== 12) {
            $mes_siguiente = $mes + 1;
            $fechaFin = $anno . '-' . $mes_siguiente . '-05';
        } else {
            $mes_siguiente = '01';
            $fechaFin = $anno + 1 . '-' . $mes_siguiente . '-05';
        }
        $fecha = $anno . '-' . $mes . '-01';

        $entities = $em->getRepository('PortadoresBundle:Autolecturaprepago')->getAutolecturbyMesAbierto($idservicios, $fecha, $fechaFin);

        $_data = array();

        for ($i = 0, $iMax = \count($entities); $i < $iMax; $i++) {
            $servicio = $entities[$i]->getServiciosid()->getId();
            $entit_desglose_servicio = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:DesgloseServicios')->getDesgServFecha($mes, $anno, $servicio);
            $perdidaT_dia = 0;
            if ($entit_desglose_servicio) {
                $id_desglose_servicio = $entit_desglose_servicio[0]->getId();
                $fecha_lectura = $entities[$i]->getFechaLectura();
                $entity_desglose_electricidad = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:DesgloseElectricidad')->getDesgFecha($mes, $anno, $fecha_lectura, $id_desglose_servicio);

                if ($entity_desglose_electricidad) {
                    if ($entity_desglose_electricidad[0]->getFechaDesglose()->format('Y-m-d') === $entities[$i]->getFechaLectura()->format('Y-m-d')) {
                        $perdidaT_dia = floatval($entity_desglose_electricidad[0]->getPerdidast());
                    }
                }
            }


            $_data[] = array(
                'id' => $entities[$i]->getId(),
                'fecha_autolectura' => $entities[$i]->getFechaLectura()->format('D,j M, Y '),
                'fecha' => $entities[$i]->getFechaLectura()->format('Y-m-d'),
                'serviciosid' => $entities[$i]->getServiciosid()->getId(),
                'nombreserviciosid' => $entities[$i]->getServiciosid()->getNombreServicio(),
                'perdidaT_dia' => round($perdidaT_dia, 2),
                'lectura_dia' => $entities[$i]->getLecturaDia(),
                'consumo_total_dia' => $entities[$i]->getConsumoTotalDia(),
                'consumo_total_real' => $entities[$i]->getConsumoTotalReal(),
                'consumo_total_porciento' => $entities[$i]->getConsumoTotalPorciento(),
                'plan_diario' => $entities[$i]->getPlanDiario()
            );

        }

        return new JsonResponse(array('success' => true, 'rows' => $_data, 'total' => \count($_data)));
    }

    public function existedesgloseAutolecturaPrepagoAction(Request $request)
    {
        $idservicio = $request->get('servicio');
        $mes = $request->get('mes');
        $existe = false;
        $datos_plan = array();
        $entities_desglose = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:DesgloseServicios')->findBy(array(
            'idservicio' => $idservicio,
            'mes' => $mes
        ));

        if (!$entities_desglose) {
            return new JsonResponse(array('success' => false, 'valor' => $existe));
        }

        foreach ($entities_desglose as $entity) {
            $datos_plan = array(
                'id' => $entity->getId(),
            );
        }

        $entities_desglose1 = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:DesgloseElectricidad')->findBy(array(
            'iddesgloseServicios' => $datos_plan['id'],
        ));

        if ($entities_desglose1) {
            $existe = true;
        }

        return new JsonResponse(array('success' => true, 'valor' => $existe));
    }

    public function addAutolecturaPrepagoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $serviciosid = trim($request->get('serviciosid'));
        $fecha_lectura_ = trim($request->get('fecha_autolectura'));

        $lectura_dia = trim($request->get('lectura_dia'));
        $fecha_lectura = date_create_from_format('d/m/Y', $fecha_lectura_);

        $mess = (int)$fecha_lectura->format('n');
        $anno = (int)$fecha_lectura->format('Y');

        $entitie_desglose = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:DesgloseServicios')->findOneBy(array(
            'idservicio' => $serviciosid,
            'mes' => $mess,
            'anno' => $anno
        ));

        $entities_desgloseDiario = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:DesgloseElectricidad')->findOneBy(array(
            'iddesgloseServicios' => $entitie_desglose->getId(),
            'fechaDesglose' => $fecha_lectura
        ));

        if (!$entities_desgloseDiario) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Debe realizar el desglose diario del plan de energía'));
        }

        $fecha_lectura_ = date_create_from_format('d/m/Y', trim($request->get('fecha_autolectura')));
        $entities = $em->getRepository('PortadoresBundle:Autolecturaprepago')->findOneBy(
            array(
                'fechaLectura' => $fecha_lectura_,
                'serviciosid' => $serviciosid
            )
        );

        $entities_fecha = $em->getRepository('PortadoresBundle:Autolecturaprepago')->ValidarFecha($fecha_lectura_, $serviciosid);
        if ($entities_fecha) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'No puede entrar autolecturas de dias anteriores a la ultima autolectura tomada para este servicio.'));
        } elseif ($entities) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'La Autolectura correspondiente al dia de hoy  para el servicio seleccionado fue realizada.'));
        } else {
            $entity = new Autolecturaprepago();
            $entity->setFechaLectura($fecha_lectura_);
            $entity->setServiciosid($em->getRepository('PortadoresBundle:Servicio')->find($serviciosid));
            $entity->setLecturaDia($lectura_dia);
            $entity->setPlanDiario($entities_desgloseDiario->getPlanDiario());

            try {
                $ultima_fecha = $em->getRepository('PortadoresBundle:Autolecturaprepago')->getLastFecha($serviciosid);
                $em->persist($entity);
                $datos = array(
                    'id' => $entity->getId(),
                    'serviciosid' => $serviciosid,
                    'lectura_dia' => $lectura_dia,
                    'fecha_lectura' => $fecha_lectura_,
                    'plan_real' => $entities_desgloseDiario->getPlanDiario(),
                    'perdidasT' => $entities_desgloseDiario->getPerdidast(),
                    'ultima_fecha' => $ultima_fecha,
                );
                $this->consumoAction($datos, 'add');
                $em->flush();
                return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Autolectura Realizada con éxito.'));
            } catch (\Exception $ex) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
            }

        }
    }

    public function validarlecturasAction($datos_lecturas)
    {

        $valid = false;
        $entities_lastfecha = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:Autolecturaprepago')->getLastFecha($datos_lecturas['servicioid']);
        $fecha_lectura = new \DateTime($entities_lastfecha[0][1]);

        $entities = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:Autolecturaprepago')->findOneBy(
            array(
                'fechaLectura' => $fecha_lectura,
                'serviciosid' => $datos_lecturas['servicioid']
            )
        );
        if ($entities) {
            if ($entities->getLecturaDia() >= $datos_lecturas['lectura_dia'] && $entities->getServiciosid()->getMetroRegresivo() === true) {
                $valid = true;
            }

            if ($entities->getLecturaDia() <= $datos_lecturas['lectura_dia'] && $entities->getServiciosid()->getMetroRegresivo() === false) {
                $valid = true;
            }

        } else {
            $valid = true;
        }
        return $valid;

    }

    public function consumoAction($datos_lecturas, $action)
    {
        $em = $this->getDoctrine()->getManager();
        $fecha_lectura = ($action === 'add') ? new \DateTime($datos_lecturas['ultima_fecha'][0][1]) : new \DateTime($datos_lecturas['ultima_fecha'][0]['max']);;
        $entities = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:Autolecturaprepago')->findBy(
            array(
                'fechaLectura' => $fecha_lectura,
                'serviciosid' => $datos_lecturas['serviciosid']
            )
        );
        if ($entities) {
            $datos = array();
            foreach ($entities as $entity) {
                $datos[] = array(
                    'id' => $entity->getId(),
                    'fecha_lectura' => $entity->getFechaLectura()->format('D,j M, Y '),
                    'serviciosid' => $entity->getServiciosid()->getId(),
                    'nombreserviciosid' => $entity->getServiciosid()->getNombreServicio(),
                    'lectura_dia' => $entity->getLecturaDia(),
                );
            }
            $consumo_total_dia = abs(floatval($datos_lecturas['lectura_dia']) - floatval($datos[0]['lectura_dia']) + floatval($datos_lecturas['perdidasT']));
            $consumo_total_real = round(floatval($consumo_total_dia), 3);
            $consumo_total_porciento = (floatval($datos_lecturas['plan_real']) != 0) ? (floatval($consumo_total_real) / floatval($datos_lecturas['plan_real'])) * 100 : 0;

            $idayer = $datos[0]['id'];

            $entity = $em->getRepository('PortadoresBundle:Autolecturaprepago')->find($idayer);
            $entity->setConsumoTotalDia($consumo_total_dia);
            $entity->setConsumoTotalReal($consumo_total_real);
            $entity->setConsumoTotalPorciento(round($consumo_total_porciento, 3));
            $em->persist($entity);
            $em->flush();
        }

    }

    public function modAutolecturaPrepagoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');

        $fecha_lectura_ = trim($request->get('fecha_autolectura'));
        $lectura_dia = trim($request->get('lectura_dia'));
        $serviciosid = trim($request->get('serviciosid'));
        $enttiess = $em->getRepository('PortadoresBundle:Autolecturaprepago')->findById($id);
        $_data = array();

        $mes = (int)date_create_from_format('d/m/Y', $fecha_lectura_)->format('n');

        $entitie_desglose = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:DesgloseServicios')->findOneBy(array(
            'idservicio' => $serviciosid,
            'mes' => $mes
        ));

        $entities_desgloseDiario = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:DesgloseElectricidad')->findOneBy(array(
            'iddesgloseServicios' => $entitie_desglose->getId(),
            'fechaDesglose' => date_create_from_format('d/m/Y', $fecha_lectura_)
        ));

        foreach ($enttiess as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'fecha_lectura' => $entity->getFechaLectura()->format('D,j M, Y '),
                'serviciosid' => $entity->getServiciosid()->getId(),
                'nombreserviciosid' => $entity->getServiciosid()->getNombreServicio(),
                'lectura_dia' => $entity->getLecturaDia(),
                'perdidasT' => $entities_desgloseDiario->getPerdidast(),
                'consumo_total_real' => $entity->getConsumoTotalReal(),
                'consumo_total_porciento' => $entity->getConsumoTotalPorciento()
            );
        }

        $fechaLectObj = new \DateTime($_data[0]['fecha_lectura']);
        $fecha_lectura = $fechaLectObj->format('Y-m-d');
        $entity = $em->getRepository('PortadoresBundle:Autolecturaprepago')->find($id);

        $entity->setLecturaDia($lectura_dia);
        try {
            $em->persist($entity);
            $em->flush();

            $sql = "select MAX(c1.fecha) from (SELECT max(a0_.fecha_lectura) AS fecha FROM datos.autolecturaprepago a0_
                              WHERE a0_.serviciosid = '$serviciosid' and fecha_lectura < '$fecha_lectura' group by a0_.fecha_lectura order by a0_.fecha_lectura DESC) as C1";

            $ultima_fecha = $this->getDoctrine()->getConnection()->fetchAll($sql);
            $datos_lecturas = array(
                'id' => $entity->getId(),
                'serviciosid' => $serviciosid,
                'nombreserviciosid' => $entity->getServiciosid()->getNombreServicio(),
                'lectura_dia' => $lectura_dia,
                'plan_real' => $entities_desgloseDiario->getPlanDiario(),
                'perdidasT' => $entities_desgloseDiario->getPerdidast(),
                'fecha_lectura' => $fecha_lectura,
                'ultima_fecha' => $ultima_fecha,
            );

            $this->consumoAction($datos_lecturas, 'mod');

            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Autolectura Modificada con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }


    }

    public function acumuladosAction(Request $request)
    {
        $session = $request->getSession();
        $mes_abierto = $session->get('selected_month');
        $anno_abierto = $session->get('selected_year');
        $serviciosid = $request->get('id');
        $valores = array();
        $acumulado_real = 0;
        $acumulado_plan = 0;

        $fecha_inicial = $anno_abierto . '-' . $mes_abierto . '-01';
        $fecha_final = FechaUtil::getUltimoDiaMes($mes_abierto, $anno_abierto);

        $entities_auto = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:Autolecturaprepago')->getAutolecturbyMesAbierto($serviciosid, $fecha_inicial, $fecha_final);

        foreach ($entities_auto as $index => $entity) {
            $valores[] = array(
                'id' => $entity->getId(),
                'mes_fecha' => $entity->getFechaLectura()->format('n'),
                'serviciosid' => $entity->getServiciosid()->getId(),
                'nombreserviciosid' => $entity->getServiciosid()->getNombreServicio(),
                'lectura_dia' => $entity->getLecturaDia(),
                'consumo_total_dia' => $entity->getConsumoTotalDia(),
                'consumo_total_real' => $entity->getConsumoTotalReal(),
                'consumo_total_porciento' => $entity->getConsumoTotalPorciento(),
                'plan_diario' => $entity->getPlanDiario()
            );
        }

        for ($i = 0, $iMax = \count($valores); $i < $iMax; $i++) {
            if ($i < $iMax) {
                $acumulado_real += $valores[$i]['consumo_total_real'];
            }

            if ($i < $iMax - 1) {
                $acumulado_plan += $valores[$i]['plan_diario'];
            }
        }

        $acumulados = array(
            'acumulado_real' => round($acumulado_real, 2),
            'acumulado_plan' => round($acumulado_plan, 2),
            'diferencia' => round($acumulado_plan - $acumulado_real, 2),
            '_plan_real' => isset($acumulado_plan) ? round(($acumulado_real / $acumulado_plan) * 100, 2) : '0',
        );

        return new JsonResponse(array('success' => true, 'array' => $acumulados));
    }

    public function cleanautolecturasAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $servicio_id = $request->get('servicio_id');
        $fecha_lectura = $request->get('fecha_lectura');
        $em->getRepository('PortadoresBundle:Autolecturaprepago')->cleanAllAutolecturas($servicio_id, $fecha_lectura);


        $sql = "SELECT a0_.id as id FROM datos.autolecturaprepago a0_
                              WHERE a0_.serviciosid = '$servicio_id' and fecha_lectura < '$fecha_lectura' group by a0_.id order by a0_.fecha_lectura DESC";
        $ultima_fecha = $this->getDoctrine()->getConnection()->fetchAll($sql);

        if ($ultima_fecha) {
            $entity = $em->getRepository('PortadoresBundle:Autolecturaprepago')->findOneBy(array('id' => $ultima_fecha[0]['id']));
            $entity->setConsumoTotalDia(null);
            $entity->setConsumoTotalReal(null);
            $entity->setConsumoTotalPorciento(null);
            $em->persist($entity);
            $em->flush();
        }

        return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Autolectura Eliminada con éxito.'));

    }

    public function bitacoraPrepagoAction(Request $request)
    {
        $session = $request->getSession();
        $mes = trim($request->get('mes'));
        $anno = trim($request->get('anno'));
        $servicio = trim($request->get('servicio'));

        if ($mes !== 12) {
            $mes_siguiente = $mes + 1;
            $fechaFin = $anno . '-' . $mes_siguiente . '-01';
        } else {
            $mes_siguiente = '01';
            $fechaFin = $anno + 1 . '-' . $mes_siguiente . '-01';
        }
        $fecha = $anno . '-' . $mes . '-01';

        $bitacora = array();

        $entities_auto = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:Autolecturaprepago')->getAutolecturbyMesAbierto($servicio, $fecha, $fechaFin);
        $valores_by_servi = array();

        for ($i = 0, $iMax = \count($entities_auto); $i < $iMax; $i++) {
            $servicio = $entities_auto[$i]->getServiciosid()->getId();
            $entit_desglose_servicio = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:DesgloseServicios')->getDesgServFecha($mes, $anno, $servicio);

            $perdidaT_dia = 0;
            $plan_mes = 0;
            if ($entit_desglose_servicio) {
                $id_desglose_servicio = $entit_desglose_servicio[0]->getId();
                $plan_mes = $entit_desglose_servicio[0]->getPlanTotal();
                $fecha_lectura = $entities_auto[$i]->getFechaLectura();
                $entity_desglose_electricidad = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:DesgloseElectricidad')->getDesgFecha($mes, $anno, $fecha_lectura, $id_desglose_servicio);

                if ($entity_desglose_electricidad) {
                    if ($entity_desglose_electricidad[0]->getFechaDesglose() == $entities_auto[$i]->getFechaLectura()) {
                        $perdidaT_dia = $entity_desglose_electricidad[0]->getPerdidast();
                    }
                }
            }
            $valores[] = array(
                'id' => $entities_auto[$i]->getId(),
                'mes_fecha' => $entities_auto[$i]->getFechaLectura()->format('n'),
                'dia_fecha' => $entities_auto[$i]->getFechaLectura()->format('j'),
                'anno_fecha' => $entities_auto[$i]->getFechaLectura()->format('Y'),
                'serviciosid' => $entities_auto[$i]->getServiciosid()->getId(),
                'nombreserviciosid' => $entities_auto[$i]->getServiciosid()->getNombreServicio(),
                'perdidaT_dia' => $perdidaT_dia,
                'plan_mes' => $plan_mes,
                'lectura_dia' => $entities_auto[$i]->getLecturaDia(),
                'consumo_total_dia' => $entities_auto[$i]->getConsumoTotalDia(),
                'consumo_total_real' => $entities_auto[$i]->getConsumoTotalReal(),
                'consumo_total_porciento' => $entities_auto[$i]->getConsumoTotalPorciento(),
                'plan_diario' => $entities_auto[$i]->getPlanDiario()
            );
        }

        for ($i = 0, $iMax = \count($valores); $i < $iMax; $i++) {
            if ($valores[$i]['mes_fecha'] === $mes and $valores[$i]['anno_fecha'] === $anno) {
                $valores_by_servi[] = $valores[$i];
            }

        }

        $consumo_acum_real_anterior = 0;
        $consumo_acum_plan_anterior = 0;
        for ($i = 0, $iMax = \count($valores_by_servi); $i < $iMax; $i++) {
            $j = $i + 1;
            $existe = array_key_exists($j, $valores_by_servi);
            if ($existe) {
                $consumo_acum_real_anterior += $valores_by_servi[$i]['consumo_total_real'];
                $consumo_acum_plan_anterior += $valores_by_servi[$i]['plan_diario'];
                if ($i == 0) {
                    $acum_real_plan = $valores_by_servi[$i]['consumo_total_real'] - $valores_by_servi[$i]['plan_diario'];
                } else {
                    $acum_real_plan = ($valores_by_servi[$i]['consumo_total_real'] + $valores_by_servi[$i - 1]['consumo_total_real']) - ($valores_by_servi[$i]['plan_diario'] + $valores_by_servi[$i - 1]['plan_diario']);
                }

                $bitacora[] = array(
                    'dia' => $valores_by_servi[$i]['dia_fecha'],
                    'lect_anterior' => $valores_by_servi[$i]['lectura_dia'],
                    'perdidaT_dia' => $valores_by_servi[$i]['perdidaT_dia'],
                    'lect_actual' => $valores_by_servi[$j]['lectura_dia'],
                    'plan_mes' => $valores_by_servi[$j]['plan_mes'],
                    'consumo_diario_real' => $valores_by_servi[$i]['consumo_total_real'] + $valores_by_servi[$i]['perdidaT_dia'],
                    'plan_diario' => $valores_by_servi[$i]['plan_diario'],
                    'real_plan' => $valores_by_servi[$i]['consumo_total_real'] - $valores_by_servi[$i]['plan_diario'],
                    'consumo_acum_real' => $consumo_acum_real_anterior,
                    'consumo_acum_plan' => $consumo_acum_plan_anterior,
                    'acum_real_plan' => $acum_real_plan
                );
            }
        }


        return new JsonResponse(array('rows' => $bitacora, 'total' => \count($bitacora)));

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

    public function printBitacoraPrepagoAction(Request $request)
    {
        $data = json_decode($request->request->get('store'));
        $mes = json_decode($request->request->get('mes'));
        $nombremes = $this->damemesAction($mes);
        $plan_mes = $data[0]->plan_mes;


        $_html = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
        <html xmlns='http://www.w3.org/1999/xhtml'>
         <head>
          <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
           <title>Cumplimiento del Plan Consumo de Electricidad total, mes: $nombremes </title>
            <style>
              table.main {
                border:0px solid;
              border-radius:10px 10px 10px 10px;
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
             border-radius:10px 10px 10px 10px;
             font-family: 'Times New Roman', Times, serif;
             font-size: 18px;
            }
            </style>
         </head>
          <table width='1000' cellspacing='0' cellpadding='0'>
          <tr>
          <td align='center'>Cumplimiento del plan de consumo de electricidad total</td></tr>
            <tr>
                <td>Plan del Mes: $plan_mes</td>
            </tr>
          </table>

            <table  width='1000' cellspacing='0' cellpadding='0' border='1'>
            <tr>
              <td rowspan='2' style='text-align: center'>Día</td>
              <td rowspan='1' colspan='2' style='text-align: center'>Lectura del Metro</td>
              <td rowspan='1' colspan='3' style='text-align: center'>Consumo Diario KWh</td>
              <td rowspan='1' colspan='3' style='text-align: center'>Consumo Acumulado KWh</td>
              <td rowspan='2' style='text-align: center'>Firma del Responsable</td>
            </tr>
            <tr>
                <td rowspan='1' colspan='1' style='text-align: center'>Anterior</td>
                <td rowspan='1' colspan='1' style='text-align: center'>Actual</td>
                <td rowspan='1' colspan='1' style='text-align: center'>Real</td>
                <td rowspan='1' colspan='1' style='text-align: center'>Plan</td>
                <td rowspan='1' colspan='1' style='text-align: center'>Real-Plan</td>
                <td rowspan='1' colspan='1' style='text-align: center'>Real</td>
                <td rowspan='1' colspan='1' style='text-align: center'>Plan</td>
                <td rowspan='1' colspan='1' style='text-align: center'>Real-Plan</td>
            </tr>";
        for ($i = 0, $iMax = \count($data); $i < $iMax; $i++) {
            $_html .= "<tr>
              <td width='200' style='text-align: center'>" . $data[$i]->dia . " </td>
              <td width='50' style='text-align: center'>" . $data[$i]->lect_anterior . " </td>
              <td colspan='1' style='text-align: center'> " . $data[$i]->lect_actual . "</td>
              <td colspan='1' style='text-align: center'> " . round($data[$i]->consumo_diario_real, 2) . "</td>
              <td colspan='1' style='text-align: center'>" . round($data[$i]->plan_diario, 2) . "</td>
              <td colspan='1' style='text-align: center'>" . round($data[$i]->real_plan, 2) . "</td>
              <td colspan='1' style='text-align: center'> " . round($data[$i]->consumo_acum_real, 2) . "</td>
              <td colspan='1' style='text-align: center'> " . round($data[$i]->consumo_acum_plan, 2) . "</td>
              <td colspan='1' style='text-align: center'> " . round($data[$i]->acum_real_plan, 2) . "</td>
              <td colspan='1' style='text-align: center'> " . ' ' . "</td>
            </tr>";
        }
        $_html .= "
            </table>
              </body>
             </html>";

        return new JsonResponse((array('success' => true, 'html' => $_html)));
    }

    public function getIsLastLectAction(Request $request)
    {
        $servicio = $request->get('servicio');

        $fecha_lectura = $request->get('fecha_autolectura');
        $anno = $request->get('anno');

        $sql = "select * from datos.autolecturaprepago as aut
                where (select extract(Year from aut.fecha_lectura) ) = $anno and aut.serviciosid ='$servicio'
                order by aut.fecha_lectura DESC";

        $datos = $this->getDoctrine()->getConnection()->fetchAll($sql);
        $succes = $lastLect = ($datos[0]['fecha_lectura'] > $fecha_lectura) ? false : true;

        return new JsonResponse(array('success' => $succes, 'valor' => $lastLect));

    }
}