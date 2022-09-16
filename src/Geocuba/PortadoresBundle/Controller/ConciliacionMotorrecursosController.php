<?php
/**
 * Created by PhpStorm.
 * User: asisoftware13
 * Date: 9/4/2019
 * Time: 2:26 p.m.
 */

namespace Geocuba\PortadoresBundle\Controller;


use Doctrine\Common\Util\Debug;
use Geocuba\AdminBundle\Util\Util;
use Geocuba\PortadoresBundle\Entity\DemandaCombustible;
use Geocuba\PortadoresBundle\Entity\Persona;
use Doctrine\Common\CommonException;
use Geocuba\PortadoresBundle\Entity\Vehiculo;
use Geocuba\PortadoresBundle\Util\Datos;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;

class ConciliacionMotorrecursosController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $nunidadid = $request->get('unidadid');


        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $anno = $request->get('anno');
        $mes = $request->get('mes');

        $arr_mes = array(1 => 'ene', 2 => 'feb', 3 => 'mar', 4 => 'abr', 5 => 'may', 6 => 'jun', 7 => 'jul', 8 => 'ago', 9 => 'sep', 10 => 'oct', 11 => 'nov', 12 => 'dic');

        $conn = $this->get('database_connection');
        $_data = array();

        $qb = $em->createQueryBuilder();
        $qb->select('vehiculo.id, marca.nombre marca_vehiculo, vehiculo.matricula, vehiculo.normaFar, tipoComb.id tipoCombustible, vehiculo.nroOrden, modelo.nombre modelo_vehiculo')
            ->from('PortadoresBundle:Vehiculo', 'vehiculo')
            ->innerJoin('vehiculo.nmodeloid', 'modelo')
            ->innerJoin('vehiculo.ntipoCombustibleid', 'tipoComb')
            ->innerJoin('modelo.marcaVehiculoid', 'marca')
            ->where('vehiculo.visible = true')
            ->andWhere($qb->expr()->in('vehiculo.nunidadid', $_unidades));


        $vehiculos = $qb->getQuery()->getResult();

        foreach ($vehiculos as $vehiculo) {
            /**@var Vehiculo $veh */
            $veh = $em->getRepository('PortadoresBundle:Vehiculo')->findOneBy(array('id' => $vehiculo['id']));
            $id_veh = $veh->getId();

            $sqlreg = $conn->fetchAll("select registro_combustible.norma_plan as norma_plan
                                              from datos.registro_combustible
                                              inner join nomencladores.vehiculo nvehiculo on nvehiculo.id = registro_combustible.vehiculoid 
                                              where  nvehiculo.id = '" . $id_veh . "' and date_part('YEAR', registro_combustible.fecha) = $anno and date_part('MONTH', registro_combustible.fecha) = $mes
                                              order by registro_combustible.norma_plan DESC limit 1");

            $del_mes = $conn->fetchAll("select   sum(registro_combustible_analisis.km) as km,
                                                 sum(registro_combustible_analisis.combustible) as cmb
                                        from datos.registro_combustible
                                                    inner join nomencladores.vehiculo nvehiculo on nvehiculo.id = registro_combustible.vehiculoid 
                                                    inner join datos.registro_combustible_analisis on registro_combustible_analisis.registro_combustible_id = registro_combustible.id
                                                    where nvehiculo.id = '" . $vehiculo['id'] . "' and registro_combustible_analisis.conceptoid = '3' and date_part('YEAR', registro_combustible.fecha) = $anno and date_part('MONTH', registro_combustible.fecha)= $mes and  registro_combustible.visible = true
                                                    limit 1");

            $acumulado = $conn->fetchAll("select      sum(registro_combustible_analisis.km) as km,
                                                       sum(registro_combustible_analisis.combustible) as cmb
                                                 from datos.registro_combustible
                                                            inner join nomencladores.vehiculo nvehiculo on nvehiculo.id = registro_combustible.vehiculoid 
                                                            inner join datos.registro_combustible_analisis on registro_combustible_analisis.registro_combustible_id = registro_combustible.id
                                                            where nvehiculo.id = '" . $id_veh . "' and  registro_combustible_analisis.conceptoid = '3' and date_part('YEAR', registro_combustible.fecha) = $anno and registro_combustible.visible = true
            limit 1");

            $kms_trabajado = is_null($acumulado[0]['km']) ? '0' : $acumulado[0]['km'];

            $kms_mes = is_null($del_mes[0]['km']) ? '0' : $del_mes[0]['km'];
            $cmb_mes = is_null($del_mes[0]['cmb']) ? '0' : $del_mes[0]['cmb'];


            $norma_plan = (\count($sqlreg) > 0) ? round(100 / $sqlreg[0]['norma_plan'], 2) : 0;
            $cmb_debio_consumir = ($norma_plan !== 0) ? $kms_trabajado / $norma_plan : 0;


            $plan_mn_acumulado = 0;
            $plan_cuc_acumulado = 0;
            $plan_mn_comb_acumulado = 0;
            $plan_cuc_comb_acumulado = 0;

            for ($i = 1; $i <= $mes; $i++) {
                $plan_mn_a = $conn->fetchAll("select nivel_act_kms_" . $arr_mes[$i] . ", combustible_litros_" . $arr_mes[$i] . " from datos.planificacion_combustible where anno = $anno and vehiculoid = '" . $id_veh . "'");
                $plan_cuc_a = $conn->fetchAll("select nivel_act_kms_" . $arr_mes[$i] . ", combustible_litros_" . $arr_mes[$i] . " from datos.planificacion_combustible_cuc where anno = $anno and vehiculoid = '" . $id_veh . "'");
                $plan_mn_acumulado += empty($plan_mn_a[0]["nivel_act_kms_" . $arr_mes[$i]]) ? 0 : $plan_mn_a[0]["nivel_act_kms_" . $arr_mes[$i]];
                $plan_mn_comb_acumulado += empty($plan_mn_a[0]["combustible_litros_" . $arr_mes[$i]]) ? 0 : $plan_mn_a[0]["combustible_litros_" . $arr_mes[$i]];
                $plan_cuc_acumulado += empty($plan_cuc_a[0]["nivel_act_kms_" . $arr_mes[$i]]) ? 0 : $plan_cuc_a[0]["nivel_act_kms_" . $arr_mes[$i]];
                $plan_cuc_comb_acumulado += empty($plan_cuc_a[0]["combustible_litros_" . $arr_mes[$i]]) ? 0 : $plan_cuc_a[0]["combustible_litros_" . $arr_mes[$i]];
                $plan_km_acumulado = $plan_mn_acumulado + $plan_cuc_acumulado;
            }

            $plan_mn_anno = 0;
            $plan_mn_comb_anno = 0;
            $plan_cuc_anno = 0;
            $plan_cuc_comb_anno = 0;

            for ($i = 1; $i <= 12; $i++) {
                $plan_mn_a = $conn->fetchAll("select nivel_act_kms_" . $arr_mes[$i] . ", combustible_litros_" . $arr_mes[$i] . " from datos.planificacion_combustible where anno = $anno and vehiculoid = '" . $id_veh . "'");
                $plan_cuc_a = $conn->fetchAll("select nivel_act_kms_" . $arr_mes[$i] . ", combustible_litros_" . $arr_mes[$i] . " from datos.planificacion_combustible_cuc where anno = $anno and vehiculoid = '" . $id_veh . "'");
                $plan_mn_anno += empty($plan_mn_a[0]["nivel_act_kms_" . $arr_mes[$i]]) ? 0 : $plan_mn_a[0]["nivel_act_kms_" . $arr_mes[$i]];
                $plan_mn_comb_anno += empty($plan_mn_a[0]["combustible_litros_" . $arr_mes[$i]]) ? 0 : $plan_mn_a[0]["combustible_litros_" . $arr_mes[$i]];
                $plan_cuc_anno += empty($plan_cuc_a[0]["nivel_act_kms_" . $arr_mes[$i]]) ? 0 : $plan_cuc_a[0]["nivel_act_kms_" . $arr_mes[$i]];
                $plan_cuc_comb_anno += empty($plan_cuc_a[0]["combustible_litros_" . $arr_mes[$i]]) ? 0 : $plan_cuc_a[0]["combustible_litros_" . $arr_mes[$i]];
                $plan_km_anno = $plan_mn_anno + $plan_cuc_anno;
            }


            $_data[] = array(
                'nro_orden' => $veh->getNroOrden(),
                'matricula' => $veh->getMatricula(),
                'actividad' => $veh->getActividad()->getNombre(),
                'denominacion' => $veh->getNdenominacionVehiculoid()->getNombre(),
                'tipo_combustible' => $veh->getNtipoCombustibleid()->getNombre(),
                'plan_motorrecurso_anno' => number_format($plan_km_anno , 0),
                'real_motorrecurso_mes' => $kms_mes,
                'plan_motorrecurso_acumulado' => $plan_km_acumulado,
                'real_motorrecurso_acumulado' =>$kms_trabajado,
                'porciento_motorrecurso_anno' => number_format(floatval($plan_km_anno) , 0) != 0 ? number_format(($acumulado[0]['km'] * 100 / $plan_km_anno) , 2) : 0,

                'consumo_norma_b83' => ($veh->getNtipoCombustibleid()->getNombre() === 'Gasolina Motor') ? round(floatval($cmb_debio_consumir),2) : 0.00,
                'consumo_real_b83' => ($veh->getNtipoCombustibleid()->getNombre() === 'Gasolina Motor') ? floatval($cmb_mes) : 0.00,
                'diferencia_b83' => round((($veh->getNtipoCombustibleid()->getNombre() === 'Gasolina Motor') ? floatval($cmb_debio_consumir) : 0.00) - (($veh->getNtipoCombustibleid()->getNombre() === 'Gasolina Motor') ? $cmb_mes : 0.00),2),

                'consumo_norma_b90' => ($veh->getNtipoCombustibleid()->getNombre() === 'Gasolina Regular') ? round(floatval($cmb_debio_consumir),2) : 0.00,
                'consumo_real_b90' => ($veh->getNtipoCombustibleid()->getNombre() === 'Gasolina Regular') ? floatval($cmb_mes) : 0.00,
                'diferencia_b90' => round((($veh->getNtipoCombustibleid()->getNombre() === 'Gasolina Regular') ? floatval($cmb_debio_consumir) : 0.00) - (($veh->getNtipoCombustibleid()->getNombre() === 'Gasolina Regular') ? $cmb_mes : 0.00),2),

                'consumo_norma_b94' => ($veh->getNtipoCombustibleid()->getNombre() === 'Gasolina Especial') ? round(floatval($cmb_debio_consumir),2) : 0.00,
                'consumo_real_b94' => ($veh->getNtipoCombustibleid()->getNombre() === 'Gasolina Especial') ? floatval($cmb_mes) : 0.00,
                'diferencia_b94' => round((($veh->getNtipoCombustibleid()->getNombre() === 'Gasolina Especial') ? floatval($cmb_debio_consumir) : 0.00) - (($veh->getNtipoCombustibleid()->getNombre() === 'Gasolina Especial') ? $cmb_mes : 0.00),2),

                'consumo_norma_diesel' => ($veh->getNtipoCombustibleid()->getNombre() === 'Diesel') ? round(floatval($cmb_debio_consumir),2) : 0.00,
                'consumo_real_diesel' => ($veh->getNtipoCombustibleid()->getNombre() === 'Diesel') ? floatval($cmb_mes)  : 0.00,
                'diferencia_diesel' => round((($veh->getNtipoCombustibleid()->getNombre() === 'Diesel') ? floatval($cmb_debio_consumir) : 0.00) - (($veh->getNtipoCombustibleid()->getNombre() === 'Diesel') ? $cmb_mes : 0.00),2),

                'consumo_norma_glp' =>  0.00,
                'consumo_real_glp' => 0.00,
                'diferencia_glp' => 0.00,
            );
        }

        return new JsonResponse(array('rows' => $_data));
    }

    public function printAction(Request $request)
    {
        $data = json_decode($request->get('store'));

        $anno = $request->get('anno');
        $mes = FechaUtil::getNombreMes($request->get('mes'));

        $_html = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
        <html xmlns='http://www.w3.org/1999/xhtml'>
         <head>
          <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
           <title>Consumo Motorrecursos y Combustible x Veh&iacute;culos</title>
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
              <td colspan='19' style='text-align: center; border: none; font-size: 14px;'><strong>Consumo Motorrecursos y Combustible x Veh&iacute;culos</strong></td>
             </tr>
             <tr>
              <td colspan='5' style='text-align: right; border: none;font-size: 12px;'><strong>Año:</strong> $anno</td>
              <td colspan='10' style='text-align: center; border: none'></td>
              <td colspan='4' style='text-align: left; border: none;font-size: 12px;'><strong>Mes:</strong> $mes</td>
             </tr>
            <tr>
              <td rowspan='3' style='text-align: center'><strong>Matr&iacute;cula</strong></td>
              <td colspan='6' style='text-align: center'><strong>MES</strong></td>
              <td colspan='6' style='text-align: center'><strong>ACUMULADO HASTA EL MES</strong></td>
              <td colspan='6' style='text-align: center' ><strong>AÑO</strong></td>
            </tr>
            <tr>
              <td colspan='3' style='text-align: center'><strong>Motorrecursos</strong></td>
              <td colspan='3' style='text-align: center'><strong>Combustible</strong></td>
              <td colspan='3' style='text-align: center' ><strong>Motorrecursos</strong></td>
              <td colspan='3' style='text-align: center' ><strong>Combustible</strong></td>
              <td colspan='4' style='text-align: center'><strong>Motorrecursos</strong></td>
              <td colspan='2' style='text-align: center'><strong>Combustible</strong></td>
            </tr>
            <tr>
              <td style='text-align: center'><strong>Plan</strong></td>
              <td style='text-align: center'><strong>Real</strong></td>
              <td style='text-align: center'><strong>%</strong></td>
              <td style='text-align: center'><strong>Plan</strong></td>
              <td style='text-align: center'><strong>Real</strong></td>
              <td style='text-align: center'><strong>%</strong></td>
              <td style='text-align: center'><strong>Plan</strong></td>
              <td style='text-align: center'><strong>Real</strong></td>
              <td style='text-align: center'><strong>%</strong></td>
              <td style='text-align: center'><strong>Plan</strong></td>
              <td style='text-align: center'><strong>Real</strong></td>
              <td style='text-align: center'><strong>%</strong></td>
              <td style='text-align: center'><strong>Plan</strong></td>
              <td style='text-align: center'><strong>Real Acumul.</strong></td>
              <td style='text-align: center'><strong>%</strong></td>
              <td style='text-align: center'><strong>Faltan Rec.</strong></td>
              <td style='text-align: center'><strong>Plan</strong></td>
              <td style='text-align: center'><strong>Real</strong></td>
            </tr>";

        $sumPMM = 0;
        $sumRMM = 0;
        $aveMM = 0;
        $sumPCM = 0;
        $sumRCM = 0;
        $aveCM = 0;
        $sumPMA = 0;
        $sumRMA = 0;
        $aveMA = 0;
        $sumPCA = 0;
        $sumRCA = 0;
        $aveCA = 0;
        $sumPMAn = 0;
        $aveMAn = 0;
        $faltanAn = 0;
        $sumPCAn = 0;

        for ($i = 0; $i < count($data); $i++) {
            $_html .= "<tr>
              <td style='text-align: center' width='10%'>" . $data[$i]->matricula . "</td>
              <td style='text-align: center' width='5%'>" . $data[$i]->plan_motorrecurso_mes . "</td>
              <td style='text-align: center' width='5%'>" . $data[$i]->real_motorrecurso_mes . "</td>
              <td style='text-align: center' width='5%'><strong>" . $data[$i]->porciento_motorrecurso_mes . "%</strong></td>

              <td style='text-align: center' width='5%'>" . $data[$i]->plan_combustible_mes . "</td>
              <td style='text-align: center' width='5%'>" . $data[$i]->real_combustible_mes . "</td>
              <td style='text-align: center' width='5%'><strong>" . $data[$i]->porciento_combustible_mes . "%</strong></td>

              <td style='text-align: center' width='5%'>" . $data[$i]->plan_motorrecurso_acumulado . "</td>
              <td style='text-align: center' width='5%'>" . $data[$i]->real_motorrecurso_acumulado . "</td>
              <td style='text-align: center' width='5%'><strong>" . $data[$i]->porciento_motorrecurso_acumulado . "%</strong></td>

              <td style='text-align: center' width='5%'>" . $data[$i]->plan_combustible_acumulado . "</td>
              <td style='text-align: center' width='5%'>" . $data[$i]->real_combustible_acumulado . "</td>
              <td style='text-align: center' width='5%'><strong>" . $data[$i]->porciento_combustible_acumulado . "%</strong></td>

              <td style='text-align: center' width='5%'>" . $data[$i]->plan_motorrecurso_anno . "</td>
              <td style='text-align: center' width='5%'>" . $data[$i]->real_motorrecurso_acumulado . "</td>
              <td style='text-align: center' width='5%'><strong>" . $data[$i]->porciento_motorrecurso_anno . "%</strong></td>
              <td style='text-align: center' width='5%'>" . $data[$i]->faltan_motorrecurso_anno . "</td>

              <td style='text-align: center' width='5%'>" . $data[$i]->plan_combustible_anno . "</td>
              <td style='text-align: center' width='5%'>" . $data[$i]->real_combustible_acumulado . "</td>
            </tr>
            ";

            $sumPMM += $data[$i]->plan_motorrecurso_mes;
            $sumRMM += $data[$i]->real_motorrecurso_mes;
            $aveMM += $data[$i]->porciento_motorrecurso_mes;

            $sumPCM += $data[$i]->plan_combustible_mes;
            $sumRCM += $data[$i]->real_combustible_mes;
            $aveCM += $data[$i]->porciento_combustible_mes;

            $sumPMA += $data[$i]->plan_motorrecurso_acumulado;
            $sumRMA += $data[$i]->real_motorrecurso_acumulado;
            $aveMA += $data[$i]->porciento_motorrecurso_acumulado;

            $sumPCA += $data[$i]->plan_combustible_acumulado;
            $sumRCA += $data[$i]->real_combustible_acumulado;
            $aveCA += $data[$i]->porciento_combustible_acumulado;

            $sumPMAn += $data[$i]->plan_motorrecurso_anno;
            $aveMAn += $data[$i]->porciento_motorrecurso_anno;
            $faltanAn += $data[$i]->faltan_motorrecurso_anno;

            $sumPCAn += $data[$i]->plan_combustible_anno;
        }

        $_html .= "<tr>
              <td style='text-align: center'><strong>TOTALES EMPRESA</strong></td>
              <td style='text-align: center'><strong>" . $sumPMM . "</strong></td>
              <td style='text-align: center'><strong>" . $sumRMM . "</strong></td>
              <td style='text-align: center'><strong>" . $aveMM . "%</strong></td>

              <td style='text-align: center'><strong>" . $sumPCM . "</strong></td>
              <td style='text-align: center'><strong>" . $sumRCM . "</strong></td>
              <td style='text-align: center'><strong>" . $aveCM . "%</strong></td>

              <td style='text-align: center'><strong>" . $sumPMA . "</strong></td>
              <td style='text-align: center'><strong>" . $sumRMA . "</strong></td>
              <td style='text-align: center'><strong>" . $aveMA . "%</strong></td>

              <td style='text-align: center'><strong>" . $sumPCA . "</strong></td>
              <td style='text-align: center'><strong>" . $sumRCA . "</strong></td>
              <td style='text-align: center'><strong>" . $aveCA . "%</strong></td>

              <td style='text-align: center'><strong>" . $sumPMAn . "</strong></td>
              <td style='text-align: center'><strong>" . $sumRMA . "</strong></td>
              <td style='text-align: center'><strong>" . $aveMAn . "%</strong></td>
              <td style='text-align: center'><strong>" . $faltanAn . "</strong></td>

              <td style='text-align: center'><strong>" . $sumPCAn . "</strong></td>
              <td style='text-align: center'><strong>" . $sumRCA . "</strong></td>
            </tr>
            ";

        $_html .= "
               </table>
               </body>
             </html>";

//        var_dump($data);
        return new Response(json_encode(array('success' => true, 'html' => $_html)));
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