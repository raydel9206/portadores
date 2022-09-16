<?php
/**
 * Created by PhpStorm.
 * User: rherrerag
 * Date: 13/12/2017
 * Time: 8:25
 */

namespace Geocuba\PortadoresBundle\Controller;


use Doctrine\Common\Util\Debug;
use Geocuba\AdminBundle\Util\FechaUtil;
use Doctrine\Common\CommonException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Geocuba\AdminBundle\Util\Util;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\DocumentosEnum;
use Geocuba\PortadoresBundle\Util\Utiles;


class ReporteConciliacionSemanalController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $conn = $this->get('database_connection');
        $em = $this->getDoctrine()->getManager();

        $nunidadid = $request->get('unidadid');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $unidades_string = $this->unidadesToString($_unidades);

        $anno = $request->get('anno');
        $mes = $request->get('mes');

        $action = $request->get('accion');

        $fecha = $anno . '-' . $mes . '-01';
        $ultimoMes = FechaUtil::getUltimoDiaMes($mes, $anno);

        //Calculando las fechas de inicio y fin de cada semana del mes
        $data = array();

        $fecha_obj = date_create_from_format('Y-m-d', $fecha);
        $day = $fecha_obj->format('N');
        $i = -1;
        $di = 0;
        $df = 0;
        $lastDay = explode('-', $ultimoMes)[2];
        while ($df < $lastDay) {
            if ($i == -1) {
                $i = 0;
                $di = 1;
                $df = $di + 7 - $day;
            } else {
                $di = $df + 1;
                $df = $di + 6;

                if ($di + 6 > $lastDay) {
                    $df = $lastDay;
                }
                $i += 1;
            }

            $semana = $di . '-' . $df;
            $fecha_datetime_inicial = $anno . '-' . $mes . '-' . $di;
            $fecha_datetime_final = $anno . '-' . $mes . '-' . $df . ' 24:00:00';

            $sql_transporte = $conn->fetchAll("select max(v.matricula) as matricula,
                                               max(v.id) as id_vehiculo,
                                               max(v.nro_orden) as nro_orden,
                                               (select sum(cant_litros) as litros_pe from datos.liquidacion as l  where l.fecha_vale between '$fecha_datetime_inicial' and '$fecha_datetime_final'  and l.nvehiculoid = v.id   group by l.nvehiculoid) as litros_pe,
                                               (select sum(rca.combustible) as litros_transporte 
                    from datos.registro_combustible as rc 
					left join datos.registro_combustible_analisis as rca on rc.id = rca.registro_combustible_id
					where rc.visible = true and rca.conceptoid = '2' and extract(year from rc.fecha) = $anno and extract(month from rc.fecha) = $mes and rca.semana = '$semana' and rc.vehiculoid = v.id) as litros_transporte
                    from nomencladores.vehiculo as v                          
                    where v.visible = true and v.nunidadid in ($unidades_string)
                    group by v.id");

            //Construyendo los datos para mostrar en el store y para no repetir n veces los vehiculos
            for ($j = 0; $j < sizeof($sql_transporte); $j++) {
                if (!is_null($sql_transporte[$j]['litros_transporte']) || !is_null($sql_transporte[$j]['litros_pe'])) {
                    $exist = false;
                    $index = 0;
                    foreach ($data as $ind => $d) {
                        if ($d['id_vehiculo'] == $sql_transporte[$j]['id_vehiculo']) {
                            $exist = true;
                            $index = $ind;
                        }
                    }
                    if ($exist) {
                        $data[$index]['litros_transporte-semana' . ($i + 1)] = !is_null($sql_transporte[$j]['litros_transporte']) ? $sql_transporte[$j]['litros_transporte'] : 0;
                        $data[$index]['litros_pe-semana' . ($i + 1)] = !is_null($sql_transporte[$j]['litros_pe']) ? $sql_transporte[$j]['litros_pe'] : 0;
                        $data[$index]['diferencia_pe-semana' . ($i + 1)] = (($sql_transporte[$j]['litros_pe'] - $sql_transporte[$j]['litros_transporte'] > 0) ? round($sql_transporte[$j]['litros_pe'] - $sql_transporte[$j]['litros_transporte'],2) : round(($sql_transporte[$j]['litros_pe'] - $sql_transporte[$j]['litros_transporte']),2) * (-1));
                    } else {
                        $data[] = array(
                            'matricula' => $sql_transporte[$j]['matricula'],
                            'id_vehiculo' => $sql_transporte[$j]['id_vehiculo'],
                            'nroOrden' => $sql_transporte[$j]['nro_orden'],
                            'litros_transporte-semana' . ($i + 1) => !is_null($sql_transporte[$j]['litros_transporte']) ? $sql_transporte[$j]['litros_transporte'] : 0,
                            'litros_pe-semana' . ($i + 1) => !is_null($sql_transporte[$j]['litros_pe']) ? $sql_transporte[$j]['litros_pe'] : 0,
                            'semana' => $semana,
                            'diferencia_pe-semana' . ($i + 1) => (($sql_transporte[$j]['litros_pe'] - $sql_transporte[$j]['litros_transporte'] > 0) ? round($sql_transporte[$j]['litros_pe'] - $sql_transporte[$j]['litros_transporte'],2) : round($sql_transporte[$j]['litros_pe'] - $sql_transporte[$j]['litros_transporte'],2) * (-1)),
                        );
                    }
                }
            }
        }
        if ($action) {
            return $data;
        }
        return new JsonResponse(array('rows' => $data));

    }

    public function printAction(Request $request)
    {

        $data = $this->loadAction($request);

        $size = count($data) - 1;
        for ($i = 0; $i < $size; $i++) {
            for ($j = 0; $j < $size - $i; $j++) {
                $k = $j + 1;
                if ($data[$k]['nroOrden'] < $data[$j]['nroOrden']) {
                    list($data[$j], $data[$k]) = array($data[$k], $data[$j]);
                }
            }
        }
        $unidad_id = $request->get('unidadid');

        $anno = $request->get('anno');
        $mes = $request->get('mes');
        $name_mes = FechaUtil::getNombreMes($mes = $request->get('mes'));

        $_html = "<!doctype html>
            <html>
            <head>
            <meta charset=\"utf-8\">
            <title>Conciliación Semanal</title>
            <style>
                .bordes_tablas
                {
                    border-bottom:1px solid #999;
                    border-left:1px solid #999;
                    border-right:1px solid #999;
                    border-top:1px solid #999;
                }
                .bordes_derecho_abajo
                {
                    border-right:1px solid #999;
                    border-bottom:1px solid #999;
                }
                .bordes_arriba_derecho_abajo
                {
                    border-top:1px solid #999;
                    border-right:1px solid #999;
                    border-bottom:1px solid #999;
                }
                .bordes_derecho
                {
                    border-right:1px solid #999;
                }
                .bordes_abajo
                {
                    border-bottom:1px solid #999;
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
                <td width=\"30\" align=\"center\"  style=\"font-size:12px\"><strong>Conciliación Mensual de Portadores y Transporte</strong></td>
                <td width=\"31%\" align=\"center\"  style=\"font-size:12px\"><strong>MES: $name_mes</strong>&nbsp;
                  <strong>AÑO: $anno</strong></td>
              </tr>
            </table>
            <br>
            
            <table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" class=\"bordes_tablas\">
              <tr style=\"font-size:11px\">
                <td width=\"3%\" rowspan=\"2\" align=\"center\" class=\"bordes_derecho_abajo\"><strong>No.</strong></td>
                <td width=\"10%\" rowspan=\"2\" align=\"center\" class=\"bordes_derecho_abajo\"><strong>Vehículo</strong></td>
                <td colspan=\"2\" align=\"center\" class=\"bordes_derecho_abajo\"><strong>Sem 1</strong></td>
                <td colspan=\"2\" align=\"center\" class=\"bordes_derecho_abajo\"><strong>Sem 2</strong></td>
                <td colspan=\"2\" align=\"center\" class=\"bordes_derecho_abajo\"><strong>Sem 3</strong></td>
                <td colspan=\"2\" align=\"center\" class=\"bordes_derecho_abajo\"><strong>Sem 4</strong></td>
                <td colspan=\"2\" align=\"center\" class=\"bordes_derecho_abajo\"><strong>Sem 5</strong></td>
                <td colspan=\"2\" align=\"center\" class=\"bordes_derecho_abajo\"><strong>Sem 6</strong></td>
                <td colspan=\"2\" align=\"center\" class=\"bordes_derecho_abajo\"><strong>Total</strong></td>
                <td width=\"4%\" rowspan=\"2\" align=\"center\" class=\"bordes_derecho_abajo\"><strong>1 Sem</strong></td>
                <td width=\"4%\" rowspan=\"2\" align=\"center\" class=\"bordes_derecho_abajo\"><strong>2 Sem</strong></td>
                <td width=\"4%\" rowspan=\"2\" align=\"center\" class=\"bordes_derecho_abajo\"><strong>3 Sem</strong></td>
                <td width=\"4%\" rowspan=\"2\" align=\"center\" class=\"bordes_derecho_abajo\"><strong>4 Sem</strong></td>
                <td width=\"4%\" rowspan=\"2\" align=\"center\" class=\"bordes_abajo\"><strong>5 Sem</strong></td>
                <td width=\"4%\" rowspan=\"2\" align=\"center\" class=\"bordes_abajo\"><strong>6 Sem</strong></td>
              </tr>
              <tr style=\"font-size:11px\">
                <td width=\"2%\" align=\"center\" class=\"bordes_derecho_abajo\"><strong>Transp</strong></td>
                <td width=\"4%\" align=\"center\" class=\"bordes_derecho_abajo\"><strong>PE</strong></td>
                <td width=\"4%\" align=\"center\" class=\"bordes_derecho_abajo\"><strong>Transp</strong></td>
                <td width=\"4%\" align=\"center\" class=\"bordes_derecho_abajo\"><strong>PE</strong></td>
                <td width=\"4%\" align=\"center\" class=\"bordes_derecho_abajo\"><strong>Transp</strong></td>
                <td width=\"4%\" align=\"center\" class=\"bordes_derecho_abajo\"><strong>PE</strong></td>
                <td width=\"4%\" align=\"center\" class=\"bordes_derecho_abajo\"><strong>Transp</strong></td>
                <td width=\"4%\" align=\"center\" class=\"bordes_derecho_abajo\"><strong>PE</strong></td>
                <td width=\"4%\" align=\"center\" class=\"bordes_derecho_abajo\"><strong>Transp</strong></td>
                <td width=\"4%\" align=\"center\" class=\"bordes_derecho_abajo\"><strong>PE</strong></td>
                <td width=\"4%\" align=\"center\" class=\"bordes_derecho_abajo\"><strong>Transp</strong></td>
                <td width=\"4%\" align=\"center\" class=\"bordes_derecho_abajo\"><strong>PE</strong></td>
                <td width=\"4%\" align=\"center\" class=\"bordes_derecho_abajo\"><strong>Transp</strong></td>
                <td width=\"4%\" align=\"center\" class=\"bordes_derecho_abajo\"><strong>PE</strong></td>
              </tr>";
        for ($i = 0; $i < sizeof($data); $i++) {
            $nro = $i + 1;

            $data[$i]['litros_transporte-semana6'] = isset($data[$i]['litros_transporte-semana6']) ? $data[$i]['litros_transporte-semana6'] : 0;
            $data[$i]['litros_pe-semana6'] = isset($data[$i]['litros_pe-semana6']) ? $data[$i]['litros_pe-semana6'] : 0;
            $data[$i]['diferencia_pe-semana6'] = isset($data[$i]['diferencia_pe-semana6']) && $data[$i]['diferencia_pe-semana6'] != -0 ? $data[$i]['diferencia_pe-semana6'] : 0;

            $data[$i]['litros_transporte-semana5'] = isset($data[$i]['litros_transporte-semana5']) ? $data[$i]['litros_transporte-semana5'] : 0;
            $data[$i]['litros_pe-semana5'] = isset($data[$i]['litros_pe-semana5']) ? $data[$i]['litros_pe-semana5'] : 0;
            $data[$i]['diferencia_pe-semana5'] = isset($data[$i]['diferencia_pe-semana5']) && $data[$i]['diferencia_pe-semana5'] != -0 ? $data[$i]['diferencia_pe-semana5'] : 0;

            $data[$i]['litros_transporte-semana4'] = isset($data[$i]['litros_transporte-semana4']) ? $data[$i]['litros_transporte-semana4'] : 0;
            $data[$i]['litros_pe-semana4'] = isset($data[$i]['litros_pe-semana4']) ? $data[$i]['litros_pe-semana4'] : 0;
            $data[$i]['diferencia_pe-semana4'] = isset($data[$i]['diferencia_pe-semana4']) && $data[$i]['diferencia_pe-semana4'] != -0 ? $data[$i]['diferencia_pe-semana4'] : 0;

            $data[$i]['litros_transporte-semana3'] = isset($data[$i]['litros_transporte-semana3']) ? $data[$i]['litros_transporte-semana3'] : 0;
            $data[$i]['litros_pe-semana3'] = isset($data[$i]['litros_pe-semana3']) ? $data[$i]['litros_pe-semana3'] : 0;
            $data[$i]['diferencia_pe-semana3'] = isset($data[$i]['diferencia_pe-semana3']) && $data[$i]['diferencia_pe-semana3'] != -0 ? $data[$i]['diferencia_pe-semana3'] : 0;

            $data[$i]['litros_transporte-semana2'] = isset($data[$i]['litros_transporte-semana2']) ? $data[$i]['litros_transporte-semana2'] : 0;
            $data[$i]['litros_pe-semana2'] = isset($data[$i]['litros_pe-semana2']) ? $data[$i]['litros_pe-semana2'] : 0;
            $data[$i]['diferencia_pe-semana2'] = isset($data[$i]['diferencia_pe-semana2']) && $data[$i]['diferencia_pe-semana2'] != -0 ? $data[$i]['diferencia_pe-semana2'] : 0;

            $data[$i]['litros_transporte-semana1'] = isset($data[$i]['litros_transporte-semana1']) ? $data[$i]['litros_transporte-semana1'] : 0;
            $data[$i]['litros_pe-semana1'] = isset($data[$i]['litros_pe-semana1']) ? $data[$i]['litros_pe-semana1'] : 0;
            $data[$i]['diferencia_pe-semana1'] = isset($data[$i]['diferencia_pe-semana1']) && $data[$i]['diferencia_pe-semana1'] != -0 ? $data[$i]['diferencia_pe-semana1'] : 0;

            $total_litros_transporte = $data[$i]['litros_transporte-semana5'] + $data[$i]['litros_transporte-semana4'] + $data[$i]['litros_transporte-semana3'] + $data[$i]['litros_transporte-semana2'] + $data[$i]['litros_transporte-semana1'];
            $total_litros_pe = $data[$i]['litros_pe-semana5'] + $data[$i]['litros_pe-semana4'] + $data[$i]['litros_pe-semana3'] + $data[$i]['litros_pe-semana2'] + $data[$i]['litros_pe-semana1'];

            $_html .= "
              <tr style=\"font-size:10px\">
                <td align=\"center\" class=\"bordes_derecho_abajo\">" . $nro . "</td>
                <td align=\"center\" class=\"bordes_derecho_abajo\">" . $data[$i]['matricula'] . "</td>
                <td align=\"center\" class=\"bordes_derecho_abajo\">" . $data[$i]['litros_transporte-semana1'] . "</td>
                <td align=\"center\" class=\"bordes_derecho_abajo\">" . $data[$i]['litros_pe-semana1'] . "</td>
                <td align=\"center\" class=\"bordes_derecho_abajo\">" . $data[$i]['litros_transporte-semana2'] . "</td>
                <td align=\"center\" class=\"bordes_derecho_abajo\">" . $data[$i]['litros_pe-semana2'] . "</td>
                <td align=\"center\" class=\"bordes_derecho_abajo\">" . $data[$i]['litros_transporte-semana3'] . "</td>
                <td align=\"center\" class=\"bordes_derecho_abajo\">" . $data[$i]['litros_pe-semana3'] . "</td>
                <td align=\"center\" class=\"bordes_derecho_abajo\">" . $data[$i]['litros_transporte-semana4'] . "</td>
                <td align=\"center\" class=\"bordes_derecho_abajo\">" . $data[$i]['litros_pe-semana4'] . "</td>
                <td align=\"center\" class=\"bordes_derecho_abajo\">" . $data[$i]['litros_transporte-semana5'] . "</td>
                <td align=\"center\" class=\"bordes_derecho_abajo\">" . $data[$i]['litros_pe-semana5'] . "</td>
                <td align=\"center\" class=\"bordes_derecho_abajo\">" . $data[$i]['litros_transporte-semana6'] . "</td>
                <td align=\"center\" class=\"bordes_derecho_abajo\">" . $data[$i]['litros_pe-semana6'] . "</td>
                <td align=\"center\" class=\"bordes_derecho_abajo\">" . $total_litros_transporte . "</td>
                <td align=\"center\" class=\"bordes_derecho_abajo\">" . $total_litros_pe . "</td>
                <td align=\"center\" class=\"bordes_derecho_abajo\">" . $data[$i]['diferencia_pe-semana1'] . "</td>
                <td align=\"center\" class=\"bordes_derecho_abajo\">" . $data[$i]['diferencia_pe-semana2'] . "</td>
                <td align=\"center\" class=\"bordes_derecho_abajo\">" . $data[$i]['diferencia_pe-semana3'] . "</td>
                <td align=\"center\" class=\"bordes_derecho_abajo\">" . $data[$i]['diferencia_pe-semana4'] . "</td>
                <td align=\"center\" class=\"bordes_derecho_abajo\">" . $data[$i]['diferencia_pe-semana5'] . "</td>
                <td align=\"center\" class=\"bordes_derecho_abajo\">" . $data[$i]['diferencia_pe-semana6'] . "</td>
              </tr>";
        }

        $_html .= "</table>";

        $pieFirma = $this->get('portadores.piefirma')->getPieFirmaDistribucion(DocumentosEnum::conciliacionMensual, $unidad_id);
        $_html .= "
        <br>
        <br>
        
        $pieFirma";

        $_html .= "
        </body>
        </html>";

        return new Response(json_encode(array('success' => true, 'html' => $_html)));
    }

    private function unidadesToString($_unidades)
    {
        $_string_unidades = "'" . $_unidades[0] . "'";
        for ($i = 1, $iMax = count($_unidades); $i < $iMax; $i++) {
            $_string_unidades .= ",'" . $_unidades[$i] . "'";
        }
        return $_string_unidades;
    }

}