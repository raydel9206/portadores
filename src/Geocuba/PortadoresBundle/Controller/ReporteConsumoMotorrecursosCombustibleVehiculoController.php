<?php
/**
 * Created by PhpStorm.
 * User: javier
 * Date: 20/05/2016
 * Time: 14:12
 */


namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\AdminBundle\Util\FechaUtil;
use Geocuba\AdminBundle\Util\Util;
use Doctrine\Common\Util\Debug;
use Geocuba\PortadoresBundle\Entity\Vehiculo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;


class ReporteConsumoMotorrecursosCombustibleVehiculoController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $nunidadid = $request->get('unidadid');
        $tipo_combustibleid = $request->get('tipoCombustible');

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

        if ($tipo_combustibleid !== "null" && $tipo_combustibleid !== "") {
            $qb->andWhere('vehiculo.ntipoCombustibleid = :tipoCombustible')
                ->setParameter('tipoCombustible', "$tipo_combustibleid");
        }

        $vehiculos = $qb->getQuery()->getResult();

        foreach ($vehiculos as $vehiculo) {
            /**@var Vehiculo $veh */
            $veh = $em->getRepository('PortadoresBundle:Vehiculo')->findOneBy(array('id' => $vehiculo['id']));
            $id_veh = $veh->getId();

            $del_mes = $conn->fetchAll("select   sum(registro_combustible_analisis.km) as km,
                                                 sum(registro_combustible_analisis.combustible) as cmb
                                        from datos.registro_combustible
                                                    inner join nomencladores.vehiculo nvehiculo on nvehiculo.id = registro_combustible.vehiculoid and nvehiculo.id = '" . $vehiculo['id'] . "'
                                                    inner join datos.registro_combustible_analisis on registro_combustible_analisis.registro_combustible_id = registro_combustible.id
                                                    where registro_combustible_analisis.conceptoid = '3' and date_part('YEAR', registro_combustible.fecha) = $anno and date_part('MONTH', registro_combustible.fecha)= $mes and  registro_combustible.visible = true
                                                    limit 1");

            $acumulado = $conn->fetchAll("select      sum(registro_combustible_analisis.km) as km,
                                                       sum(registro_combustible_analisis.combustible) as cmb
                                                 from datos.registro_combustible
                                                            inner join nomencladores.vehiculo nvehiculo on nvehiculo.id = registro_combustible.vehiculoid and nvehiculo.id = '" . $id_veh . "'
                                                            inner join datos.registro_combustible_analisis on registro_combustible_analisis.registro_combustible_id = registro_combustible.id
                                                            where registro_combustible_analisis.conceptoid = '3' and date_part('YEAR', registro_combustible.fecha) = $anno and  registro_combustible.visible = true
            limit 1");

            $kms_trabajado = is_null($acumulado[0]['km']) ? '0' : $acumulado[0]['km'];
            $cmb_consumido = is_null($acumulado[0]['cmb']) ? '0' : $acumulado[0]['cmb'];
            $kms_mes = is_null($del_mes[0]['km']) ? '0' : $del_mes[0]['km'];
            $cmb_mes = is_null($del_mes[0]['cmb']) ? '0' : $del_mes[0]['cmb'];

            $plan_mn = $conn->fetchAll("select nivel_act_kms_" . $arr_mes[$mes] . ", combustible_litros_" . $arr_mes[$mes] . " from datos.planificacion_combustible where anno = $anno and vehiculoid = '" . $id_veh . "'");
            $plan_cuc = $conn->fetchAll("select nivel_act_kms_" . $arr_mes[$mes] . ", combustible_litros_" . $arr_mes[$mes] . " from datos.planificacion_combustible_cuc where anno = $anno and vehiculoid = '" . $id_veh . "'");
            $plan_mn_km = empty($plan_mn[0]["nivel_act_kms_" . $arr_mes[$mes]]) ? 0 : $plan_mn[0]["nivel_act_kms_" . $arr_mes[$mes]];
            $plan_mn_comb = empty($plan_mn[0]["combustible_litros_" . $arr_mes[$mes]]) ? 0 : $plan_mn[0]["combustible_litros_" . $arr_mes[$mes]];
            $plan_cuc_km = empty($plan_cuc[0]["nivel_act_kms_" . $arr_mes[$mes]]) ? 0 : $plan_cuc[0]["nivel_act_kms_" . $arr_mes[$mes]];
            $plan_cuc_comb = empty($plan_cuc[0]["combustible_litros_" . $arr_mes[$mes]]) ? 0 : $plan_cuc[0]["combustible_litros_" . $arr_mes[$mes]];
            $plan_km = $plan_mn_km + $plan_cuc_km;
            $plan_comb = $plan_mn_comb + $plan_cuc_comb;

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
                $plan_comb_acumulado = $plan_mn_comb_acumulado + $plan_cuc_comb_acumulado;
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
                $plan_comb_anno = $plan_mn_comb_anno + $plan_cuc_comb_anno;
            }

            $_data[] = array(
                'nro_orden' => $veh->getNroOrden(),
                'matricula' => $veh->getMatricula(),
                'plan_motorrecurso_mes' => round($plan_km,2),
                'real_motorrecurso_mes' => round($kms_mes,2),
                'porciento_motorrecurso_mes' => ($plan_km != 0) ? number_format(($kms_mes / $plan_km) * 100, 2) : 0,
                'plan_combustible_mes' => $plan_comb,
                'real_combustible_mes' => $cmb_mes,
                'porciento_combustible_mes' => ($plan_comb != 0) ? number_format(($cmb_mes / $plan_comb) * 100, 2) : 0,
                'plan_motorrecurso_acumulado' => round($plan_km_acumulado,2),
                'real_motorrecurso_acumulado' => round($kms_trabajado,2),
                'porciento_motorrecurso_acumulado' => $plan_km_acumulado != 0 ? number_format($kms_trabajado / $plan_km_acumulado * 100, 2) : 0,
                'plan_combustible_acumulado' => $plan_comb_acumulado,
                'real_combustible_acumulado' => $cmb_consumido,
                'porciento_combustible_acumulado' => $plan_comb_acumulado != 0 ? number_format($cmb_consumido / $plan_comb_acumulado * 100, 2) : 0,
                'plan_motorrecurso_anno' => round($plan_km_anno,2),
                'real_motorrecurso_anno' => round($kms_trabajado,2),
                'porciento_motorrecurso_anno' => $plan_km_anno != 0 ? number_format($kms_trabajado / $plan_km_anno * 100, 2) : 0,
                'plan_combustible_anno' => $plan_comb_anno,
                'real_combustible_anno' => $cmb_consumido,
                'porciento_combustible_anno' => $plan_comb_anno != 0 ? number_format($cmb_consumido / $plan_comb_anno * 100, 2) : 0,
                'faltan_motorrecurso_anno' => round($plan_km_anno - $kms_trabajado,2)

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