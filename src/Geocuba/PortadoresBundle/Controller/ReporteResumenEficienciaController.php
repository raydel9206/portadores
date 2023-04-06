<?php
/**
 * Created by PhpStorm.
 * User: orlando
 * Date: 03/02/2017
 * Time: 10:12
 */


namespace Geocuba\PortadoresBundle\Controller;

use Doctrine\Common\Util\Debug;
use Geocuba\AdminBundle\Util\FechaUtil;
use Geocuba\AdminBundle\Util\Util;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Geocuba\PortadoresBundle\Util\Utiles;
use Geocuba\Utils\ViewActionTrait;


class ReporteResumenEficienciaController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $nunidadid = $request->get('unidadid');
        $tipoCombustible = $request->get('tipoCombustible');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $qb = $em->createQueryBuilder();


        $qb->select('vehiculo.id, marca.nombre marca_vehiculo, vehiculo.matricula, vehiculo.normaFar, tipoComb.id tipoCombustible, vehiculo.nroOrden, modelo.nombre modelo_vehiculo')
            ->from('PortadoresBundle:Vehiculo', 'vehiculo')
            ->innerJoin('vehiculo.nmodeloid', 'modelo')
            ->innerJoin('vehiculo.ntipoCombustibleid', 'tipoComb')
            ->innerJoin('modelo.marcaVehiculoid', 'marca')
            ->where('vehiculo.visible = true')
            ->andWhere($qb->expr()->in('vehiculo.nunidadid', $_unidades));

        if ($tipoCombustible !== "null" && $tipoCombustible !== "") {
            $qb->andWhere('vehiculo.ntipoCombustibleid = :tipoCombustible')
                ->setParameter('tipoCombustible', "$tipoCombustible");
        }

        $vehiculos = $qb->getQuery()->getResult();

        $data = array();
        $anno = $request->get('anno');
        $mes = $request->get('mes');

        $arr_mes = array(1 => 'ene', 2 => 'feb', 3 => 'mar', 4 => 'abr', 5 => 'may', 6 => 'jun', 7 => 'jul', 8 => 'ago', 9 => 'sep', 10 => 'oct', 11 => 'nov', 12 => 'dic');
        foreach ($vehiculos as $vehiculo) {
            $conn = $this->get('database_connection');

            $plan_mn = 0;
            $plan_cuc = 0;
            $plan = 0;

            if ($request->get('acumulado') == 'false') {

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


                $plan_mn = $conn->fetchAll("select nivel_act_kms_" . $arr_mes[$mes] . " from datos.planificacion_combustible where anno = $anno and vehiculoid = '" . $vehiculo['id'] . "'");
                $plan_cuc = $conn->fetchAll("select nivel_act_kms_" . $arr_mes[$mes] . " from datos.planificacion_combustible_cuc where anno = $anno and vehiculoid = '" . $vehiculo['id'] . "'");
                $plan_mn = empty($plan_mn[0]["nivel_act_kms_" . $arr_mes[$mes]]) ? 0 : $plan_mn[0]["nivel_act_kms_" . $arr_mes[$mes]];
                $plan_cuc = empty($plan_cuc[0]["nivel_act_kms_" . $arr_mes[$mes]]) ? 0 : $plan_cuc[0]["nivel_act_kms_" . $arr_mes[$mes]];
                $plan = $plan_mn + $plan_cuc;

                $kms_salir = empty($result_salida[0]['km']) ? 0 : $result_salida[0]['km'];
                $comb_salir = empty($result_salida[0]['combustible']) ? 0 : $result_salida[0]['combustible'];
                $kms_llegar = empty($result_llegada[0]['km']) ? 0 : $result_llegada[0]['km'];
                $comb_llegar = empty($result_llegada[0]['combustible']) ? 0 : $result_llegada[0]['combustible'];
                $comb_abastecido = empty($combustible_abastecido[0]['abastecido']) ? 0 : $combustible_abastecido[0]['abastecido'];
                $kms_trabajado = is_null($result_kmt_trab[0]['sum']) ? '0' : $result_kmt_trab[0]['sum'];

            } else {

                for ($i = $mes; $i >= 1; $i--) {

                    $result_llegada = $conn->fetchAll("select registro_combustible_analisis.* from datos.registro_combustible
            inner join nomencladores.vehiculo nvehiculo on nvehiculo.id = registro_combustible.vehiculoid and nvehiculo.id = '" . $vehiculo['id'] . "'
            inner join datos.registro_combustible_analisis on registro_combustible_analisis.registro_combustible_id = registro_combustible.id
            where registro_combustible_analisis.conceptoid = '4' and date_part('YEAR', registro_combustible.fecha) = $anno and date_part('MONTH', registro_combustible.fecha) = $i
            order by numerosemana DESC limit 1");
                    if ($result_llegada) {
                        if ($result_llegada[0]['combustible'] != 0) {
                            break;
                        }
                    }
                }

                $result_salida = $conn->fetchAll("select registro_combustible_analisis.* from datos.registro_combustible
            inner join nomencladores.vehiculo nvehiculo on nvehiculo.id = registro_combustible.vehiculoid and nvehiculo.id = '" . $vehiculo['id'] . "'
            inner join datos.registro_combustible_analisis on registro_combustible_analisis.registro_combustible_id = registro_combustible.id
            where registro_combustible_analisis.conceptoid = '1' and date_part('YEAR', registro_combustible.fecha) = $anno
            order by registro_combustible.fecha, numerosemana limit 1");

                $result_kmt_trab = $conn->fetchAll("select      sum(registro_combustible_analisis.km) from datos.registro_combustible
            inner join nomencladores.vehiculo nvehiculo on nvehiculo.id = registro_combustible.vehiculoid and nvehiculo.id = '" . $vehiculo['id'] . "'
            inner join datos.registro_combustible_analisis on registro_combustible_analisis.registro_combustible_id = registro_combustible.id
            where registro_combustible_analisis.conceptoid = '3' and date_part('YEAR', registro_combustible.fecha) = $anno and  registro_combustible.visible = true
            limit 1");

                $combustible_abastecido = $conn->fetchAll("select sum(rca.combustible) abastecido
                                                                   from datos.registro_combustible as rc
                                                                   join datos.registro_combustible_analisis as rca on rc.id = rca.registro_combustible_id

                                                             where rc.vehiculoid = '" . $vehiculo['id'] . "'
                                                                   and rca.conceptoid = '2'
                                                                   and extract(YEAR from rc.fecha) = $anno
                                                                   and extract(MONTH from rc.fecha) <= $mes");

                for ($i = 1; $i <= $mes; $i++) {
                    $plan_mn_a = $conn->fetchAll("select nivel_act_kms_" . $arr_mes[$i] . " from datos.planificacion_combustible where anno = $anno and vehiculoid = '" . $vehiculo['id'] . "'");
                    $plan_cuc_a = $conn->fetchAll("select nivel_act_kms_" . $arr_mes[$i] . " from datos.planificacion_combustible_cuc where anno = $anno and vehiculoid = '" . $vehiculo['id'] . "'");
                    $plan_mn += empty($plan_mn_a[0]["nivel_act_kms_" . $arr_mes[$i]]) ? 0 : $plan_mn_a[0]["nivel_act_kms_" . $arr_mes[$i]];
                    $plan_cuc += empty($plan_cuc_a[0]["nivel_act_kms_" . $arr_mes[$i]]) ? 0 : $plan_cuc_a[0]["nivel_act_kms_" . $arr_mes[$i]];
                    $plan = $plan_mn + $plan_cuc;

                }

                $kms_salir = empty($result_salida[0]['km']) ? 0 : $result_salida[0]['km'];
                $comb_salir = empty($result_salida[0]['combustible']) ? 0 : $result_salida[0]['combustible'];
                $kms_llegar = empty($result_llegada[0]['km']) ? 0 : $result_llegada[0]['km'];
                $comb_llegar = empty($result_llegada[0]['combustible']) ? 0 : $result_llegada[0]['combustible'];
                $comb_abastecido = empty($combustible_abastecido[0]['abastecido']) ? 0 : $combustible_abastecido[0]['abastecido'];
                $kms_trabajado = is_null($result_kmt_trab[0]['sum']) ? '0' : $result_kmt_trab[0]['sum'];


            }
            $data[] = array(
                'marca' => $vehiculo['marca_vehiculo'] . '-' . $vehiculo['modelo_vehiculo'],
                'matricula' => $vehiculo['matricula'],
                'nro_orden' => $vehiculo['nroOrden'],
                'norma_plan' => round($vehiculo['normaFar'], 2),
                'kms_salir' => $kms_salir,
                'comb_salir' => $comb_salir,
                'kms_llegar' => $kms_llegar,
                'comb_llegar' => $comb_llegar,
                'comb_abastecido' => $comb_abastecido,
                'plan_motorrecursos' => $plan,
                'kms_trabajado' => $kms_trabajado,
                'comb_consumido' => $comb_salir + $comb_abastecido - $comb_llegar,
                'norma_real' => ($kms_trabajado == 0) ? 0 : round(($comb_salir + $comb_abastecido - $comb_llegar) * 100 / ($kms_trabajado), 2),
                'existencia' => round($plan - $kms_trabajado,2)
            );
        }


        return new JsonResponse(array('rows' => $data, 'total' => count($data)));
    }

    public function printAction(Request $request)
    {
        $data = json_decode($request->get('store'));
        $anno = $request->get('anno');
        $mes = FechaUtil::getNombreMes($request->get('mes'));
        $acumulado = $request->get('acumulado');

        $_html = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
        <html xmlns='http://www.w3.org/1999/xhtml'>
         <head>
          <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
           <title>Resumen de la Eficiencia del Transporte</title>
            <style>
            table {
                border:0 solid;
                border-radius:0;
                font-family: 'Arial', serif;
                font-size: 11px;
                border-collapse: collapse;
            }
            td{
                height: 10px;
                padding: 2px;
            }
        </style>
         </head>
          <body>
          <header>
            <img  src='../../assets/img/PNG/logo.png' height='60px' width='160px'>
            </header>
           <table cellspacing='0' cellpadding='5' border='1' width='100%'>
           
             <tr>
              <td colspan='16' style='text-align: center; border: none; font-size: 14px;'><strong>Resumen de la Eficiencia del Transporte</strong></td>
             </tr>
             <tr>
              <td colspan='5' style='text-align: center; border: none'></td>";
        if ($acumulado == 'true')
            $_html .= "<td colspan='6' style='text-align: center; border: none;font-size: 12px;'><strong>Hasta el Mes de:</strong> $mes del $anno</td>";
        else
            $_html .= "<td colspan='6' style='text-align: center; border: none;font-size: 12px;'><strong>Mes:</strong> $mes del $anno</td>";

        $_html .= "
              <td colspan='5' style='text-align: center; border: none'></td>
             </tr>
            <tr>
              <td rowspan='2' style='text-align: center;'><strong>No.</strong></td>
              <td rowspan='2' style='text-align: left;'><strong>Marca</strong></td>
              <td rowspan='2' style='text-align: left;'><strong>Matr&iacute;cula</strong></td>
              <td colspan='2' style='text-align: center;'><strong>De la Salida</strong></td>
              <td colspan='1' style='text-align: center;'><strong>Abastecidos</strong></td>
              <td colspan='2' style='text-align: center;'><strong>De la Salida</strong></td>
              <td colspan='4' style='text-align: center;'><strong>Eficiencia Real</strong></td>
              <td colspan='3' style='text-align: center;'><strong>Motorrecursos/MotoHoras</strong></td>
               </tr>
            <tr>
              <td style='text-align: center'><strong>Kms/Mth al Salir</strong></td>
              <td style='text-align: center'><strong>Comb. al Salir</strong></td>
              <td style='text-align: center'><strong>Comb. Abastecido</strong></td>              

              <td style='text-align: center'><strong>Kms/Mth al Llegar</strong></td>
              <td style='text-align: center'><strong>Comb. al Llegar</strong></td>
              <td style='text-align: center'><strong>Kms/Mth Trab.</strong></td>
              <td style='text-align: center'><strong>Comb. Consumido</strong></td>
              <td style='text-align: center'><strong>Norma Plan</strong></td>
              <td style='text-align: center'><strong>Norma Real</strong></td>
              <td style='text-align: center'><strong>Plan Mes</strong></td>
              <td style='text-align: center'><strong>Adicionales</strong></td>
              <td style='text-align: center'><strong>Existencia</strong></td>
            </tr>";

        for ($i = 0; $i < count($data); $i++) {

            $_html .= "<tr>
              <td style='text-align: center'>" . ($i + 1) . "</td>
              <td style='text-align: left'>" . $data[$i]->marca . "</td>
              <td style='text-align: left'>" . $data[$i]->matricula . "</td>
              <td style='text-align: center'> " . $data[$i]->kms_salir . "</td>
              <td style='text-align: center'> " . $data[$i]->comb_salir . "</td>
              <td style='text-align: center'> " . $data[$i]->comb_abastecido . "</td>              
              <td style='text-align: center'> " . $data[$i]->kms_llegar . "</td>
              <td style='text-align: center'> " . $data[$i]->comb_llegar . "</td>
              <td style='text-align: center'> " . $data[$i]->kms_trabajado . "</td>
              <td style='text-align: center'> " . $data[$i]->comb_consumido . "</td>
              <td style='text-align: center'> " . $data[$i]->norma_plan . "</td>
              <td style='text-align: center'> " . number_format($data[$i]->norma_real, 2) . "</td>
              <td style='text-align: center'> " . $data[$i]->plan_motorrecursos . "</td>
              <td style='text-align: center'>0</td>
              <td style='text-align: center'> " . $data[$i]->existencia . "</td>
            </tr>
            ";
        }

        //todo agragar totales

        $_html .= "
               </table>
               </body>
             </html>";
        return new Response(json_encode(array('success' => true, 'html' => $_html)));
    }

    private
    function unidadesToString($_unidades)
    {
        $_string_unidades = "'" . $_unidades[0] . "'";
        for ($i = 1; $i < count($_unidades); $i++) {
            $_string_unidades .= ",'" . $_unidades[$i] . "'";
        }
        return $_string_unidades;
    }
}