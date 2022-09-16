<?php
/**
 * Created by PhpStorm.
 * User: rherrerag
 * Date: 1/9/2018
 * Time: 11:24 a.m.
 */

namespace Geocuba\PortadoresBundle\Controller;

use Doctrine\Common\Util\Debug;
use Geocuba\PortadoresBundle\Entity\Nactividad;
use Doctrine\Common\CommonException;
use Geocuba\PortadoresBundle\Entity\Tarjeta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Geocuba\AdminBundle\Util\Util;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\DocumentosEnum;

class Anexo8Controller extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $datos = array();

        $em = $this->getDoctrine()->getManager();
        $idtarjeta = $request->get('id');
        $action = $request->get('action');

        $anno = $request->get('anno');
        $mes = $request->get('mes');

        $anno_anterior = $anno;

        if($mes != 1){
            $mes_anterior = $mes - 1;
        }else{
            $mes_anterior = 12;
            $anno_anterior = $anno - 1;
        }

        $sql = "select t.nro_tarjeta,
                     extract(day from ht.fecha) as d,
                     extract(month from ht.fecha) as m,
                     extract(year from ht.fecha) as y,
                     extract(hour from ht.fecha) as h,
                     extract(minute from ht.fecha) as minutos,
                     extract(second from ht.fecha) as s,
                     ht.existencia_importe as existencia_imp,
                     ht.existencia_cantidad as existencia_cant,
                     ht.entrada_importe as entrada_imp,
                     ht.entrada_cantidad as entrada_cant,
                     ht.salida_importe as abastecido_importe,
                     v.matricula,
                     ht.nro_vale,
                     ht.salida_cantidad as abastecido_cantidad,
                     n.nombre
                    
                 from datos.historial_tarjeta as ht 
                 join nomencladores.tarjeta as t on t.id = ht.tarjetaid
                 left join datos.liquidacion as l on l.id = ht.liquidacionid
                 left join nomencladores.vehiculo as v on v.id = l.nvehiculoid
                 left join nomencladores.persona as n on n.id = l.npersonaid
                 where ht.tarjetaid = '$idtarjeta'
                 and extract(month from ht.fecha) = '$mes'
                 and extract(year from ht.fecha) = '$anno'
                 order by d,m,y,h,minutos";

        $sql_anterior = "select   t.nro_tarjeta,
                     extract(day from ht.fecha) as d,
                     extract(month from ht.fecha) as m,
                     extract(year from ht.fecha) as y,
                     extract(hour from ht.fecha) as h,
                     extract(minute from ht.fecha) as minutos,
                     extract(second from ht.fecha) as s,
                     ht.existencia_importe as existencia_imp,
                     ht.existencia_cantidad as existencia_cant,
                     ht.entrada_importe as entrada_imp,
                     ht.entrada_cantidad as entrada_cant,
                     ht.salida_importe as abastecido_importe,
                     v.matricula,
                     ht.nro_vale,
                     ht.salida_cantidad as abastecido_cantidad,
                     n.nombre
                    
                 from datos.historial_tarjeta as ht 
                 join nomencladores.tarjeta as t on t.id = ht.tarjetaid
                 left join datos.liquidacion as l on l.id = ht.liquidacionid
                 left join nomencladores.vehiculo as v on v.id = l.nvehiculoid
                 left join nomencladores.persona as n on n.id = l.npersonaid
                 where ht.tarjetaid = '$idtarjeta'
                 and extract(month from ht.fecha) = '$mes_anterior'
                 and extract(year from ht.fecha) = '$anno_anterior'
                 order by d desc,m desc ,y desc ,h desc ,minutos desc";

        $datos = $this->getDoctrine()->getConnection()->fetchAll($sql);
        $datos_anterior = $this->getDoctrine()->getConnection()->fetchAll($sql_anterior);


        for ($i = 0; $i < sizeof($datos); $i++) {
            if ($i == 0) {
                if (sizeof($datos_anterior) != 0) {
                    $datos[0]['existencia_inicial_imp'] = $datos_anterior[0]['existencia_imp'];
                    $datos[0]['existencia_inicial_cant'] = $datos_anterior[0]['existencia_cant'];
                }elseif(sizeof($datos_anterior) == 0 && $datos[$i]['abastecido_importe'] != 0 && $datos[$i]['abastecido_cantidad'] != 0){
                    $datos[0]['existencia_inicial_imp'] = $datos[$i]['existencia_imp'] + $datos[$i]['abastecido_importe'];
                    $datos[0]['existencia_inicial_cant'] = $datos[$i]['existencia_cant'] + $datos[$i]['abastecido_cantidad'];
                }elseif (sizeof($datos_anterior) == 0 && $datos[$i]['entrada_imp'] != 0 && $datos[$i]['entrada_cant'] != 0){
                    $datos[0]['existencia_inicial_imp'] = $datos[$i]['existencia_imp'] - $datos[$i]['entrada_imp'];
                    $datos[0]['existencia_inicial_cant'] = $datos[$i]['existencia_cant'] + $datos[$i]['entrada_cant'];
                }
            }else{
                $datos[$i]['existencia_inicial_imp'] = $datos[$i-1]['existencia_imp'];
                $datos[$i]['existencia_inicial_cant'] = $datos[$i-1]['existencia_cant'];
            }
        }

        if(count($datos) === 0){
            /**@var Tarjeta $tarjeta*/
            $tarjeta = $em->getRepository('PortadoresBundle:Tarjeta')->find($idtarjeta);

            $datos[] = array(
                'd' =>$tarjeta->getFechaRegistro()->format('j'),
                'm' =>$tarjeta->getFechaRegistro()->format('n'),
                'y' =>$tarjeta->getFechaRegistro()->format('Y'),
                'existencia_imp' => $tarjeta->getImporte(),
                'existencia_cant' => round($tarjeta->getImporte() / $tarjeta->getTipoCombustibleid()->getPrecio(),2),
                'entrada_imp' => '',
                'entrada_cant' => '',
                'salida_importe' => '',
                'abastecido_importe' => '',
                'abastecido_cantidad' => '',
                'matricula' => '',
                'existencia_inicial_cant' => '',
                'existencia_inicial_imp' => '',
                'nro_vale' => '',
                'nombre' => '',

            );
        }

        if($action){
            return $datos;
        }
        return new JsonResponse(array('rows' => $datos, 'total' => count($datos)));
    }

    public function printAction(Request $request){

        $datos = $this->loadAction($request);
        $tarjeta = $request->get('id');

        $sql = "select t.nro_tarjeta as tarjeta, 
                       tc.nombre as combustible,
                       u.nombre as unidad,
                       u.id as unidad_id
                       
                       from nomencladores.tarjeta as t 
                       join nomencladores.tipo_combustible as tc on tc.id = t.ntipo_combustibleid
                       join nomencladores.unidad as u on u.id = t.nunidadid
                       where t.id = '$tarjeta'";

        $cabecera = $this->getDoctrine()->getConnection()->fetchAll($sql);

        $tarjeta = $cabecera[0]['tarjeta'];
        $combustible = $cabecera[0]['combustible'];
        $unidad = $cabecera[0]['unidad'];
        $unidad_id = $cabecera[0]['unidad_id'];

        $html = "<!doctype html>
        <html>
        <head>
        <meta charset='utf-8'>
        <title>Existencia de Combustible</title>
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
                /*.bordes_derecho_arriba
                {
                    border-right:1px solid #999;
                    border-top:1px solid #999;
                }*/
                .bordes_derecho
                {
                    border-right:1px solid #999;
                }
                .bordes_abajo
                {
                    border-bottom:1px solid #999;
                }
                /*.bordes_arriba_derecho_abajo
                {
                    border-top:1px solid #999;
                    border-right:1px solid #999;
                    border-bottom:1px solid #999;
                }
                .bordes_izquierda_derecho_abajo
                {
                    border-left:1px solid #999;
                    border-right:1px solid #999;
                    border-bottom:1px solid #999;
                }*/
        </style>
        </head>
        
        <body>
        <header>
            <img  src='../../assets/img/PNG/logo.png' height='60px' width='160px'>
        </header>
        <table width='100%' cellspacing='0' cellpadding='0'>
          <tr>
            <td colspan='2'>&nbsp;</td>
            <td width='13%'>&nbsp;</td>
          </tr>
          <tr>
            <td colspan='2' style='font-size:12px'>Anexo 8</td>
            <td colspan='2' align='center' style='font-size:12px'>MODELO No. 2</td>
          </tr>
          <tr>
            <td height='55' colspan='4' align='center' style='font-size:14px'><strong>REGISTRO Y CONTROL DE LAS EXISTENCIAS DE COMBUSTIBLE <br>EN LAS TARJETAS MAGNETICAS DE COMBUSTIBLE</strong></td>
          </tr>
          <tr>
            <td height='24' style='font-size:12px'>Tipo de Combustible: $combustible</td>
            <td colspan='2' style='font-size:12px'>Tarjeta No.:$tarjeta</td>
            <td style='font-size:12px'>Entidad: $unidad</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan='2'>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table>
        
        <table width='100%' cellpadding='0' cellspacing='0' class='bordes_tablas'>
          <tr>
            <td colspan='3' align='center' class='bordes_derecho_abajo' style='font-size:12px'>FECHA</td>
            <td width='9%' rowspan='2' align='center' valign='bottom' class='bordes_derecho_abajo' style='font-size:12px'>MATRICULA</td>
            <td width='10%' rowspan='2' align='center' valign='bottom' class='bordes_derecho_abajo' style='font-size:12px'>No. DE <br>COMPROBANTE</td>
            <td colspan='2' align='center' class='bordes_derecho_abajo' style='font-size:12px'>EXISTENCIA INICIAL</td>
            <td colspan='2' align='center' class='bordes_derecho_abajo' style='font-size:12px'>ENTRADA</td>
            <td colspan='4' align='center' class='bordes_derecho_abajo' style='font-size:12px'>ABASTECIDO</td>
            <td colspan='2' align='center' class='bordes_abajo' style='font-size:12px'>SALDO FINAL</td>
          </tr>
          <tr>
            <td width='3%' align='center' class='bordes_derecho_abajo' style='font-size:12px'>D</td>
            <td width='3%' align='center' class='bordes_derecho_abajo' style='font-size:12px'>M</td>
            <td width='4%' align='center' class='bordes_derecho_abajo' style='font-size:12px'>A</td>
            <td width='6%' align='center' class='bordes_derecho_abajo' style='font-size:12px'>CANT.</td>
            <td width='7%' align='center' class='bordes_derecho_abajo' style='font-size:12px'>MP.</td>
            <td width='6%' align='center' class='bordes_derecho_abajo' style='font-size:12px'>CANT.</td>
            <td width='5%' align='center' class='bordes_derecho_abajo' style='font-size:12px'>MP.</td>
            <td width='5%' align='center' class='bordes_derecho_abajo' style='font-size:12px'>CANT.</td>
            <td width='4%' align='center' class='bordes_derecho_abajo' style='font-size:12px'>MP.</td>
            <td width='15%' align='center' class='bordes_derecho_abajo' style='font-size:12px'>NOMBRE Y APELLIDOS</td>
            <td width='10%' align='center' class='bordes_derecho_abajo' style='font-size:12px'>FIRMA</td>
            <td width='6%' align='center' class='bordes_derecho_abajo' style='font-size:12px'>CANT.</td>
            <td width='5%' align='center' class='bordes_abajo' style='font-size:12px'>MP.</td>
          </tr>";

        for($i = 0; $i < sizeof($datos); $i++) {
            $html .= "
          <tr>
            <td align='center' class='bordes_derecho_abajo'>" . $datos[$i]['d']. "</td>
            <td align='center' class='bordes_derecho_abajo'>" . $datos[$i]['m']. "</td>
            <td align='center' class='bordes_derecho_abajo'>" . $datos[$i]['y']. "</td>
            <td align='center' class='bordes_derecho_abajo'>" . $datos[$i]['matricula']. "</td>
            <td align='center' class='bordes_derecho_abajo'>" . $datos[$i]['nro_vale']. "</td>          
            <td align='center' class='bordes_derecho_abajo'>". $datos[$i]['existencia_inicial_cant']."</td>
            <td align='center' class='bordes_derecho_abajo'>". $datos[$i]['existencia_inicial_imp']."</td>
            <td align='center' class='bordes_derecho_abajo'>". $datos[$i]['entrada_cant']."</td>
            <td align='center' class='bordes_derecho_abajo'>". $datos[$i]['entrada_imp']."</td>
            <td align='center' class='bordes_derecho_abajo'>". $datos[$i]['abastecido_cantidad']."</td>
            <td align='center' class='bordes_derecho_abajo'>". $datos[$i]['abastecido_importe']."</td>
            <td align='center' class='bordes_derecho_abajo'>". $datos[$i]['nombre']."</td>
            <td align='center' class='bordes_derecho_abajo'></td>
            <td align='center' class='bordes_derecho_abajo'>". $datos[$i]['existencia_cant']."</td>
            <td align='center' class='bordes_derecho_abajo'>". $datos[$i]['existencia_imp']."</td>
          </tr>                  
           ";
        }

        $html .= "</table>";

        $pieFirma = $this->get('portadores.piefirma')->getPieFirmaDistribucion(DocumentosEnum::anexo8, $unidad_id);
        $html .= "
        <br>
        <br>
        
        $pieFirma";

        $html .= "
        </body>
        </html>";

        return new Response(json_encode(array('success' => true, 'html' => $html)));
    }
}