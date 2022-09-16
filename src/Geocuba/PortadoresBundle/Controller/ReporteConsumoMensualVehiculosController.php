<?php
/**
 * Created by PhpStorm.
 * User: rherrerag
 * Date: 03/02/2017
 * Time: 10:12
 */


namespace AIDAG\PortadoresBundle\Controller;

use AIDAG\MySecurityBundle\Util\FechaUtil;
use AIDAG\MySecurityBundle\Util\Util;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ReporteConsumoMensualVehiculosController extends Controller
{
    public function indexAction()
    {
        $env = $this->getParameter('kernel.environment');
        if ($env == "dev")
            $_user = "Dev";
        else {
            $_user = $this->get('security.context')->getToken()->getUser();
        }
        $_arrjs = Util::GetJS($_user, 'Consumo Mensual por Vehiculos', $this->getDoctrine()->getManager());
        $_css = Util::GetCss($_user, 'Consumo Mensual por Vehiculos', $this->getDoctrine()->getManager());
        return $this->render('PortadoresBundle:Default:index.html.twig', array('arrjs' => $_arrjs, 'arrcss' => $_css));
    }

    public function loadTotalesMNAction(Request $request){
        $datos = $this->loadAction($request);
        $data = array();

        $exp = $request->get('exp');


        $sql = "select tc.nombre, p.nombre as portador from nomencladores.ntipo_combustible as tc 
                join nomencladores.nportador as p on p.id = tc.portadorid
                where tc.visible = true order by p.nombre desc";

        $conn = $this->getDoctrine()->getConnection();
        $tipos_combustible = $entities = $conn->fetchAll($sql);;

        $_data = array();
        for ($i = 0; $i < sizeof($tipos_combustible); $i++) {
            $kms = 0;
            $comb = 0;
            $abast = 0;
            $tipo_comb = $tipos_combustible[$i]['nombre'];
            $portador = $tipos_combustible[$i]['portador'];
            for ($j = 0; $j < sizeof($datos); $j++) {
                if (strcasecmp($datos[$j]['tipo_combustible'], $tipo_comb) == 0) {
                    $kms += $datos[$j]['kms_recorridos_mn'];
                    $comb += $datos[$j]['cmb_consumido_mn'];
                    $abast += $datos[$j]['cmb_abastecido_mn'];
                }
            }
            if ($kms != 0 && $comb != 0 && $abast != 0) {
                $_data[] = array(
                    'kms' => $kms,
                    'comb' => $comb,
                    'abast' => $abast,
                    'tipo' => $tipo_comb,
                    'portador' => $portador,
                    'orden' => $i,
                );
            }
        }
        if ($exp) {
            return $_data;
        }
        return new JsonResponse(array('rows' => $_data, 'total' => count($_data)));
    }

    public function loadTotalesCUCAction(Request $request)
    {

        $datos = $this->loadAction($request);
        $exp = $request->get('exp');

        $sql = "select tc.nombre, p.nombre as portador from nomencladores.ntipo_combustible as tc 
                join nomencladores.nportador as p on p.id = tc.portadorid
                where tc.visible = true order by p.nombre desc";

        $conn = $this->getDoctrine()->getConnection();
        $tipos_combustible = $entities = $conn->fetchAll($sql);;

        $_data = array();
        for ($i = 0; $i < sizeof($tipos_combustible); $i++) {
            $kms = 0;
            $comb = 0;
            $abast = 0;
            $tipo_comb = $tipos_combustible[$i]['nombre'];
            $portador = $tipos_combustible[$i]['portador'];
            for ($j = 0; $j < sizeof($datos); $j++) {
                if (strcasecmp($datos[$j]['tipo_combustible'], $tipo_comb) == 0) {
                    $kms += $datos[$j]['kms_recorridos_cuc'];
                    $comb += $datos[$j]['cmb_consumido_cuc'];
                    $abast += $datos[$j]['cmb_abastecido_cuc'];
                }
            }
            if ($kms != 0 && $comb != 0 && $abast != 0) {
                $_data[] = array(
                    'kms' => $kms,
                    'comb' => $comb,
                    'abast' => $abast,
                    'tipo' => $tipo_comb,
                    'orden' => $i,
                    'portador' => $portador,
                );
            }
        }

        if ($exp) {
            return $_data;
        }
        return new JsonResponse(array('rows' => $_data, 'total' => count($_data)));
    }

    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        //$mes = $session->get('SELECTED_MONTH');
        $mes = $request->get('mes');

        $anno = $session->get('SELECTED_YEAR');
        $accion = $request->get('accion');

        $start = $request->get('start');
        $limit = $request->get('limit');

        $fecha = $anno . '-' . $mes . '-01';

        $fechaFin = FechaUtil::getUltimoDiaMes($mes, $anno);
        $fechaFinal = $fechaFin . ' 24:00:00';


        if ($request->get('acumulado') === 'true') {
            $fecha = $anno . '-01-01';
            $sql = "select
                  v.id,
                  max(v.norma_far) as norma,
                  max(tc.nombre)as tipo_combustible,
                  max(v.matricula) as matricula,
                  sum(rca.km) as km_recorridos_total,
                  sum(rca.combustible)as comb_consumido_total,
                  (select (Case when sum(l.cant_litros)<> 0 then sum(l.cant_litros) else 0 end)  from datos.liquidacion as l 
                  join nomencladores.ntarjeta as t on t.id = l.ntarjetaid
                  where l.fecha_vale between '$fecha' and '$fechaFinal' and l.nvehiculoid = v.id and t.nmonedaid = 'apr_portadores_34823')as cmb_abastecido_cuc,
                  (select (Case when sum(l.cant_litros)<> 0 then sum(l.cant_litros) else 0 end)  from datos.liquidacion as l 
                  join nomencladores.ntarjeta as t on t.id = l.ntarjetaid
                  where l.fecha_vale between '$fecha' and '$fechaFinal' and l.nvehiculoid = v.id and t.nmonedaid = 'apr_portadores_34822')as cmb_abastecido_mn
                  from  datos.registro_combustible as rc
                  join nomencladores.nvehiculo as v on v.id = rc.vehiculoid
                  join nomencladores.ntipo_combustible as tc on tc.id = v.ntipo_combustibleid
                  join datos.registro_combustible_analisis as rca on rca.registro_combustible_id = rc.id
            
                  where rca.conceptoid = '3'and extract(year from (rc.fecha)) = '$anno' group by v.id ";

            $reporte = $this->getDoctrine()->getConnection()->fetchAll($sql);
        } else {
            $sql = "select
                  v.id,
                  max(v.norma_far) as norma,
                  max(tc.nombre)as tipo_combustible,
                  max(v.matricula) as matricula,
                  sum(rca.km) as km_recorridos_total,
                  sum(rca.combustible)as comb_consumido_total,
                  (select (Case when sum(l.cant_litros)<> 0 then sum(l.cant_litros) else 0 end)  from datos.liquidacion as l 
                  join nomencladores.ntarjeta as t on t.id = l.ntarjetaid
                  where l.fecha_vale between '$fecha' and '$fechaFinal' and l.nvehiculoid = v.id and t.nmonedaid = 'apr_portadores_34823')as cmb_abastecido_cuc,
                  (select (Case when sum(l.cant_litros)<> 0 then sum(l.cant_litros) else 0 end)  from datos.liquidacion as l 
                  join nomencladores.ntarjeta as t on t.id = l.ntarjetaid
                  where l.fecha_vale between '$fecha' and '$fechaFinal' and l.nvehiculoid = v.id and t.nmonedaid = 'apr_portadores_34822')as cmb_abastecido_mn
                  from  datos.registro_combustible as rc
                  join nomencladores.nvehiculo as v on v.id = rc.vehiculoid
                  join nomencladores.ntipo_combustible as tc on tc.id = v.ntipo_combustibleid
                  join datos.registro_combustible_analisis as rca on rca.registro_combustible_id = rc.id
            
                  where rca.conceptoid = '3' and extract(month from (rc.fecha)) = '$mes' and extract(year from (rc.fecha)) = '$anno' group by v.id";
            $reporte = $this->getDoctrine()->getConnection()->fetchAll($sql);
        }


        for ($i = 0; $i < sizeof($reporte); $i++) {
            $km_recorridos_mn = 0;
            $km_recorridos_cuc = 0;
            $cmb_abastecido_mn = 0;
            $cmb_abastecido_cuc = 0;
            $cmb_consumido_mn = 0;
            $cmb_consumido_cuc = 0;

            if ($reporte[$i]['cmb_abastecido_mn'] != 0 && $reporte[$i]['cmb_abastecido_cuc'] != 0) {
                //-----Kilometros-----\\
                $km_recorridos_mn = isset($reporte[$i]['cmb_abastecido_mn']) ? ceil($reporte[$i]['cmb_abastecido_mn'] * 100 / $reporte[$i]['norma']) : '0';
                $km_recorridos_cuc = isset($km_recorridos_mn) ? $reporte[$i]['km_recorridos_total'] - $km_recorridos_mn : '0';

                //-----Combustible----\\
                $cmb_abastecido_mn = $reporte[$i]['cmb_abastecido_mn'];
                $cmb_abastecido_cuc = $reporte[$i]['cmb_abastecido_cuc'];

                $cmb_consumido_mn = $reporte[$i]['cmb_abastecido_mn'];
                $cmb_consumido_cuc = $reporte[$i]['comb_consumido_total'] - $reporte[$i]['cmb_abastecido_mn'];

            } elseif ($reporte[$i]['cmb_abastecido_mn'] == 0 && $reporte[$i]['cmb_abastecido_cuc'] != 0) {
                $km_recorridos_cuc = $reporte[$i]['km_recorridos_total'];
                $cmb_consumido_cuc = $reporte[$i]['comb_consumido_total'];
                $cmb_abastecido_cuc = $reporte[$i]['cmb_abastecido_cuc'];
            } elseif ($reporte[$i]['cmb_abastecido_mn'] != 0 && $reporte[$i]['cmb_abastecido_cuc'] == 0) {
                $km_recorridos_mn = $reporte[$i]['km_recorridos_total'];
                $cmb_consumido_mn = $reporte[$i]['comb_consumido_total'];
                $cmb_abastecido_mn = $reporte[$i]['cmb_abastecido_mn'];
            }

            $reporte[$i]['kms_recorridos_mn'] = ceil($km_recorridos_mn);
            $reporte[$i]['kms_recorridos_cuc'] = round($km_recorridos_cuc, 2);
            $reporte[$i]['cmb_abastecido_cuc'] = $cmb_abastecido_cuc;
            $reporte[$i]['cmb_abastecido_mn'] = $cmb_abastecido_mn;
            $reporte[$i]['cmb_consumido_cuc'] = $cmb_consumido_cuc;
            $reporte[$i]['cmb_consumido_mn'] = $cmb_consumido_mn;

            $reporte[$i]['comb_consumido_norma_mn'] = round(($reporte[$i]['kms_recorridos_mn'] / 100) * $reporte[$i]['norma'], 2);
            $reporte[$i]['comb_consumido_norma_cuc'] = round(($reporte[$i]['kms_recorridos_cuc'] / 100) * $reporte[$i]['norma'], 2);
        }
        if ($accion) {
            return $reporte;
        }

        return new JsonResponse(array('rows' => $reporte, 'total' => count($reporte)));
    }

    public function printAction(Request $request)
    {

        $data = $this->loadAction($request);
        $totales_mn = $this->loadTotalesMNAction($request);
        $totales_cuc = $this->loadTotalesCUCAction($request);

        $session = $request->getSession();
        $anno = $session->get('SELECTED_YEAR');
        $mes = FechaUtil::getNombreMes($session->get('SELECTED_MONTH'));


        $_html = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
        <html xmlns='http://www.w3.org/1999/xhtml'>
         <head>
          <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
           <title>Consumo Mensual por Veh&iacute;culos</title>
            <style>
            table {
                border:0 solid;
                margin-top: 15px;
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
           <table cellspacing='0' cellpadding='5' border='1' width='100%'>
             <tr>
              <td colspan='10' style='text-align: left;border: none; padding: 0;'><img src='../../image/logoreporte.png' width='166' height='90'></td>
             </tr>
             <tr>
              <td colspan='10' style='text-align: center; border: none; font-size: 14px;'><strong>Consumo Mensual por Veh&iacute;culos</strong></td>
             </tr>
             <tr>
              <td colspan='2' style='text-align: right; border: none;font-size: 12px;'><strong>Año:</strong> $anno</td>
              <td colspan='6' style='text-align: center; border: none'></td>
              <td colspan='2' style='text-align: left; border: none;font-size: 12px;'><strong>Mes:</strong> $mes</td>
             </tr>
            <tr>
              <td rowspan='2' style='text-align: center'><strong>Matr&iacute;cula</strong></td>
              <td rowspan='2' style='text-align: center'><strong>Combustible</strong></td>
              <td colspan='4' style='text-align: center' ><strong>CONSUMO MN</strong></td>
              <td colspan='4' style='text-align: center' ><strong>CONSUMO CUC</strong></td>
            </tr>
            <tr>
              <td style='text-align: center'><strong>Kms Recorridos</strong></td>
              <td style='text-align: center'><strong>Combustible Consumido</strong></td>
              <td style='text-align: center'><strong>Combustible <br>Consumido x<br>norma</strong></td>
              <td style='text-align: center'><strong>Abastecido</strong></td>
              <td style='text-align: center'><strong>Kms Recorridos</strong></td>
              <td style='text-align: center'><strong>Combustible Consumido</strong></td>
              <td style='text-align: center'><strong>Combustible <br>Consumido x<br>norma</strong></td>
              <td style='text-align: center'><strong>Abastecido</strong></td>
            </tr>";

        for ($i = 0; $i < count($data); $i++) {
            $_html .= "<tr>
              <td style='text-align: center' width='10%'>" . $data[$i]['matricula'] . "</td>
              <td style='text-align: center' width='5%'>" . $data[$i]['tipo_combustible'] . "</td>

              <td style='text-align: center' width='5%'>" . $data[$i]['kms_recorridos_mn'] . "</td>
              <td style='text-align: center' width='5%'>" . $data[$i]['cmb_consumido_mn'] . "</td>
              <td style='text-align: center' width='5%'>" . $data[$i]['comb_consumido_norma_mn'] . "</td>
              <td style='text-align: center' width='5%'>" . $data[$i]['cmb_abastecido_mn'] . "</td>
              <td style='text-align: center' width='5%'>" . $data[$i]['kms_recorridos_cuc'] . "</td>
              <td style='text-align: center' width='5%'>" . $data[$i]['cmb_consumido_cuc'] . "</td>
              <td style='text-align: center' width='5%'>" . $data[$i]['comb_consumido_norma_cuc'] . "</td>
              <td style='text-align: center' width='5%'>" . $data[$i]['cmb_abastecido_cuc'] . "</td>
            </tr>";
        }
        $_html .= "
             <table cellspacing='0' cellpadding='5' border='1' width='50%' align='center' >
             <tr height=\"21\">
             <td colspan=\"4\" class=\"Style_fecha\" height=\"21\" bgcolor=\"#CCCCCC\"><div align=\"center\"><strong>MN</strong></div></td>
             <tr height=\"21\">
             <td class=\"Estilo2\" height=\"21\" bgcolor=\"#CCCCCC\"><div class=\"Estilo3\" align=\"center\"><div align=\"left\">TIPO</div></div></td>
             <td class=\"Estilo2\" bgcolor=\"#CCCCCC\"><div class=\"Estilo3\" align=\"center\">KMS</div></td>
             <td class=\"Estilo2\" bgcolor=\"#CCCCCC\"><div class=\"Estilo3\" align=\"center\"><div align=\"center\">COMB</div></div></td>
             <td class=\"Estilo2\" bgcolor=\"#CCCCCC\"><div class=\"Estilo3\" align=\"center\"><div align=\"center\">ABAST</div></div></td>          
             ";
        $suma_km = 0;
        $suma_comb = 0;
        $suma_abast = 0;
        $portador = $totales_mn[0]['portador'];
        for ($mn = 0; $mn <= sizeof($totales_mn); $mn++) {
            if ($mn == sizeof($totales_mn)) {
                $_html .= "  <td height=\"21\" bgcolor=\"#CCCCCC\"><strong>TOTAL</strong></td>
             <td bgcolor=\"#CCCCCC\"><div align=\"center\"><strong>" . $suma_km . "</strong></div></td>
             <td bgcolor=\"#CCCCCC\"><div align=\"center\"><strong>" . $suma_comb . "</strong></div></td>
             <td bgcolor=\"#CCCCCC\"><div align=\"center\"><strong>" . $suma_abast . "</strong></div></td>
             </tr>";
            } else {
                if ($portador != $totales_mn[$mn]['portador']) {
                    $portador = $totales_mn[$mn]['portador'];
                    $_html .= "  <td height=\"21\" bgcolor=\"#CCCCCC\"><strong>TOTAL</strong></td>
             <td bgcolor=\"#CCCCCC\"><div align=\"center\"><strong>" . $suma_km . "</strong></div></td>
             <td bgcolor=\"#CCCCCC\"><div align=\"center\"><strong>" . $suma_comb . "</strong></div></td>
             <td bgcolor=\"#CCCCCC\"><div align=\"center\"><strong>" . $suma_abast . "</strong></div></td>
             </tr>";
                    $suma_km = $totales_mn[$mn]['kms'];
                    $suma_comb = $totales_mn[$mn]['comb'];
                    $suma_abast = $totales_mn[$mn]['abast'];
                } else {
                    $suma_km += $totales_mn[$mn]['kms'];
                    $suma_comb += $totales_mn[$mn]['comb'];
                    $suma_abast += $totales_mn[$mn]['abast'];

                }
                $_html .= " 
             <tr height='21'>
             <td height='21' width='82'>" . $totales_mn[$mn]['tipo'] . "</td>
             <td width='109'><div align='center'><span class='Estilo3'>" . $totales_mn[$mn]['kms'] . "</span></div></td>
             <td width='115'><div align='center'><span class='Estilo3'>" . $totales_mn[$mn]['comb'] . "</span></div></td>
             <td width='115'><div align='center'><span class='Estilo3'>" . $totales_mn[$mn]['abast'] . "</span></div></td>
             </tr>
              ";
            }
        }
        $_html .= "  
             <table cellspacing='0' cellpadding='5' border='1' width='50%' align='center' >         
             <td colspan=\"4\" class=\"Style_fecha\" bgcolor=\"#CCCCCC\"><div align=\"center\"><strong>CUC</strong></div></td></tr>
             <tr height=\"21\">
             <td class=\"Estilo2\" height=\"21\" bgcolor=\"#CCCCCC\"><div class=\"Estilo3\" align=\"center\"><div align=\"left\">TIPO</div></div></td>
             <td class=\"Estilo2\" bgcolor=\"#CCCCCC\"><div class=\"Estilo3\" align=\"center\">KMS</div></td>
             <td class=\"Estilo2\" bgcolor=\"#CCCCCC\"><div class=\"Estilo3\" align=\"center\"><div align=\"center\">COMB</div></div></td>
             <td class=\"Estilo2\" bgcolor=\"#CCCCCC\"><div class=\"Estilo3\" align=\"center\"><div align=\"center\">ABAST</div></div></td>
             </tr>";
        $suma_km_cuc = 0;
        $suma_comb_cuc = 0;
        $suma_abast_cuc = 0;
        $portador = $totales_cuc[0]['portador'];
        for ($cuc = 0; $cuc <= sizeof($totales_cuc); $cuc++) {
            if ($cuc == sizeof($totales_cuc)) {
                $_html .= "  <td height=\"21\" bgcolor=\"#CCCCCC\"><strong>TOTAL</strong></td>
             <td bgcolor=\"#CCCCCC\"><div align=\"center\"><strong>" . $suma_km_cuc . "</strong></div></td>
             <td bgcolor=\"#CCCCCC\"><div align=\"center\"><strong>" . $suma_comb_cuc . "</strong></div></td>
             <td bgcolor=\"#CCCCCC\"><div align=\"center\"><strong>" . $suma_abast_cuc . "</strong></div></td>
             </tr>";
            } else {
                if ($portador != $totales_cuc[$cuc]['portador']) {
                    $portador = $totales_cuc[$cuc]['portador'];
                    $_html .= "  <td height=\"21\" bgcolor=\"#CCCCCC\"><strong>TOTAL</strong></td>
             <td bgcolor=\"#CCCCCC\"><div align=\"center\"><strong>" . $suma_km_cuc . "</strong></div></td>
             <td bgcolor=\"#CCCCCC\"><div align=\"center\"><strong>" . $suma_comb_cuc . "</strong></div></td>
             <td bgcolor=\"#CCCCCC\"><div align=\"center\"><strong>" . $suma_abast_cuc . "</strong></div></td>
             </tr>";
                    $suma_km_cuc = $totales_cuc[$cuc]['kms'];
                    $suma_comb_cuc = $totales_cuc[$cuc]['comb'];
                    $suma_abast_cuc = $totales_cuc[$cuc]['abast'];
                } else {
                    $suma_km_cuc += $totales_cuc[$cuc]['kms'];
                    $suma_comb_cuc += $totales_cuc[$cuc]['comb'];
                    $suma_abast_cuc += $totales_cuc[$cuc]['abast'];

                }
                $_html .= " 
             <tr height='21'>
             <td height='21' width='82'>" . $totales_cuc[$cuc]['tipo'] . "</td>
             <td width='109'><div align='center'><span class='Estilo3'>" . $totales_cuc[$cuc]['kms'] . "</span></div></td>
             <td width='115'><div align='center'><span class='Estilo3'>" . $totales_cuc[$cuc]['comb'] . "</span></div></td>
             <td width='115'><div align='center'><span class='Estilo3'>" . $totales_cuc[$cuc]['abast'] . "</span></div></td>
             </tr>
              ";
            }
        }
        $_html .= "
             </table>
               </body>
             </html>";

        return new Response(json_encode(array('success' => true, 'html' => $_html)));
    }
}