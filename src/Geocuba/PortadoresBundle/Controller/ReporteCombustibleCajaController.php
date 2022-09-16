<?php
/**
 * Created by PhpStorm.
 * User: orlando
 * Date: 09/01/2017
 * Time: 10:46
 */


namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\AdminBundle\Util\FechaUtil;
use Doctrine\Common\Util\Debug;
use Geocuba\AdminBundle\Util\Util;
use Geocuba\PortadoresBundle\Entity\Tarjeta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\DocumentosEnum;
use Geocuba\PortadoresBundle\Util\Utiles;

class ReporteCombustibleCajaController extends Controller
{

    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $numero = trim($request->get('nro_tarjeta'));
        if (is_null($numero) || $numero == '')
            return new JsonResponse(array('rows' => array(), 'total' => 0));

        $start = $request->get('start');
        $limit = $request->get('limit');

        $anno = $request->get('anno');
        $mes = $request->get('mes');

        $em = $this->getDoctrine()->getManager();

        $nunidadid = $request->get('unidadid');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $unidades_string = $this->unidadesToString($_unidades);

        $fechaAnterior = $anno . '-' . $mes . '-01 0:0:0';


        $sqlAnterior = "select
                    ht.nro_vale as vale,
                    to_char(ht.fecha,'DD/MM/YYYY HH:MI AM') as fecha,
                    ht.nro_factura,
                    ht.entrada_importe as entrada_importe,
                    ht.entrada_cantidad as entrada_litros,
                    ht.salida_importe as salida_importe,
                    ht.salida_cantidad as salida_litros,
                    ht.existencia_importe as existencia_importe,
                    ht.existencia_cantidad as existencia_litros,
                    v.matricula as chapa,
                    tc.nombre as combustible,
                    cc.nombre as centro_costo,
                    an.no_vale as anticipo

                    from datos.historial_tarjeta as ht
                    left join datos.liquidacion as lq on ht.liquidacionid = lq.id
                    inner join nomencladores.tarjeta as t on ht.tarjetaid = t.id
                    inner join nomencladores.tipo_combustible as tc on t.ntipo_combustibleid = tc.id
                    left join nomencladores.centro_costo as cc on lq.ncentrocostoid = cc.id
                    left join nomencladores.vehiculo as v on lq.nvehiculoid = v.id
                    left join datos.anticipo as an on an.id = lq.anticipo

                    where
                    t.nunidadid in ($unidades_string) and ht.fecha <= '$fechaAnterior' and t.nro_tarjeta = '$numero'
                    order by ht.fecha DESC";

        $entityAnterior = $this->getDoctrine()->getConnection()->fetchAll($sqlAnterior);

        $_data = array();

        if (sizeof($entityAnterior) > 0)
            if ($entityAnterior[0]['entrada_importe'] != 0) {
                $_data[] = array(
//                    'fecha_entrada' => $entityAnterior[0]['fecha'],
                    'fecha_entrada' => '',
//                    'nro_factura' => $entityAnterior[0]['nro_factura'],
                    'nro_factura' => '',
//                    'entrada_importe' => $entityAnterior[0]['entrada_importe'],
                    'entrada_importe' => '',
                    'fecha_salida' => '',
                    'no_anticipo' => '',
                    'nro_vale' => '',
                    'centro_costo' => '',
                    'chapa' => '',
                    'cantidad' => '',
                    'importe_inicial' => '',
                    'salida_importe' => '',
                    'importe_final' => $entityAnterior[0]['existencia_importe']
                );
            }
            else {
                $_data[] = array(
//                    'fecha_entrada' => $entityAnterior[0]['fecha'],
                    'fecha_entrada' => '',
//                    'nro_factura' => $entityAnterior[0]['nro_factura'],
                    'nro_factura' => '',
//                    'entrada_importe' => $entityAnterior[0]['salida_importe'],
                    'entrada_importe' => '',
                    'fecha_salida' => '',
                    'no_anticipo' => '',
                    'nro_vale' => '',
                    'centro_costo' => '',
                    'chapa' => '',
                    'cantidad' => '',
                    'importe_inicial' => '',
                    'salida_importe' => '',
                    'importe_final' => $entityAnterior[0]['existencia_importe']
                );
            }

        $fechaActual = FechaUtil::getUltimoDiaMes($mes, $anno);
        $fechaActual.= ' 23:59:59';
        $sql = "select
                    ht.nro_vale as vale,
                    to_char(ht.fecha,'DD/MM/YYYY HH:MI AM') as fecha,
                    ht.nro_factura,
                    ht.entrada_importe as entrada_importe,
                    ht.entrada_cantidad as entrada_litros,
                    ht.salida_importe as salida_importe,
                    ht.salida_cantidad as salida_litros,
                    ht.existencia_importe as existencia_importe,
                    ht.existencia_cantidad as existencia_litros,
                    v.matricula as chapa,
                    tc.nombre as combustible,
                    cc.nombre as centro_costo,
                    an.no_vale as anticipo

                    from datos.historial_tarjeta as ht
                    left join datos.liquidacion as lq on ht.liquidacionid = lq.id
                    inner join nomencladores.tarjeta as t on ht.tarjetaid = t.id
                    inner join nomencladores.tipo_combustible as tc on t.ntipo_combustibleid = tc.id
                    left join nomencladores.centro_costo as cc on lq.ncentrocostoid = cc.id
                    left join nomencladores.vehiculo as v on lq.nvehiculoid = v.id
                    left join datos.anticipo as an on an.id = lq.anticipo

                    where
                    t.nunidadid in ($unidades_string) and ht.fecha between '$fechaAnterior' and '$fechaActual' and t.nro_tarjeta = '$numero'
                    order by ht.fecha
                    offset $start limit $limit";

        $entities = $this->getDoctrine()->getConnection()->fetchAll($sql);

        foreach ($entities as $entity) {
            if ($entity['entrada_importe'] != 0) {
                $_data[] = array(
                    'fecha_entrada' => $entity['fecha'],
                    'nro_factura' => $entity['nro_factura'],
                    'entrada_importe' => $entity['entrada_importe'],
                    'fecha_salida' => '',
                    'no_anticipo' => '',
                    'nro_vale' => '',
                    'centro_costo' => '',
                    'chapa' => '',
                    'cantidad' => '',
                    'importe_inicial' => '',
                    'salida_importe' => '',
                    'importe_final' => $entity['existencia_importe']
                );
            }
            else {
                $_data[] = array(
                    'fecha_entrada' => '',
                    'nro_factura' => '',
                    'entrada_importe' => '',
                    'fecha_salida' => $entity['fecha'],
                    'no_anticipo' => $entity['anticipo'],
                    'nro_vale' => $entity['vale'],
                    'centro_costo' => $entity['centro_costo'],
                    'chapa' => $entity['chapa'],
                    'cantidad' => $entity['salida_litros'],
                    'importe_inicial' => $entity['salida_importe'] + $entity['existencia_importe'] - $entity['entrada_importe'],
                    'salida_importe' => $entity['salida_importe'],
                    'importe_final' => $entity['existencia_importe']
                );
            }
        }

        if(count($_data) === 0){
            /**@var Tarjeta $tarjeta*/
            $tarjeta = $em->getRepository('PortadoresBundle:Tarjeta')->findOneBy(array('nroTarjeta' => $numero));
            $_data[] = array(
                'fecha_entrada' =>$tarjeta->getFechaRegistro()->format('Y-m-d'),
                'nro_factura' => '',
                'entrada_importe' => '',
                'fecha_salida' => '',
                'no_anticipo' => '',
                'nro_vale' => '',
                'centro_costo' => $tarjeta->getCentrocosto()->getNombre(),
                'chapa' => '',
                'cantidad' => '',
                'importe_inicial' => $tarjeta->getImporte(),
                'salida_importe' => '',
                'importe_final' => $tarjeta->getImporte()
            );
        }
        return new JsonResponse(array('rows' => $_data, 'total' => sizeof($_data)));
    }

    public function printAction(Request $request)
    {		
		$numero = trim($request->get('nro_tarjeta'));
        if (is_null($numero) || $numero == '')
            return new JsonResponse(array('rows' => array(), 'total' => 0));

        $start = $request->get('start');
        $limit = $request->get('limit');

        $mesNombre = FechaUtil::getNombreMes($request->get('mes'));
        $anno = $request->get('anno');
        $mes = $request->get('mes');

        $em = $this->getDoctrine()->getManager();

        $nunidadid = $request->get('unidadid');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $unidades_string = $this->unidadesToString($_unidades);

        $fechaAnterior = $anno . '-' . $mes . '-01 0:0:0';
        $sqlAnterior = "select
                    ht.nro_vale as vale,
                    to_char(ht.fecha,'DD/MM/YYYY HH:MI AM') as fecha,
                    ht.nro_factura,
                    ht.entrada_importe as entrada_importe,
                    ht.entrada_cantidad as entrada_litros,
                    ht.salida_importe as salida_importe,
                    ht.salida_cantidad as salida_litros,
                    ht.existencia_importe as existencia_importe,
                    ht.existencia_cantidad as existencia_litros,
                    v.matricula as chapa,
                    tc.nombre as combustible,
                    cc.nombre as centro_costo,
                    an.no_vale as anticipo

                    from datos.historial_tarjeta as ht
                    left join datos.liquidacion as lq on ht.liquidacionid = lq.id
                    inner join nomencladores.tarjeta as t on ht.tarjetaid = t.id
                    inner join nomencladores.tipo_combustible as tc on t.ntipo_combustibleid = tc.id
                    left join nomencladores.centro_costo as cc on lq.ncentrocostoid = cc.id
                    left join nomencladores.vehiculo as v on lq.nvehiculoid = v.id
                    left join datos.anticipo as an on an.id = lq.anticipo

                    where
                    t.nunidadid in ($unidades_string) and ht.fecha <= '$fechaAnterior' and t.nro_tarjeta = '$numero'
                    order by ht.fecha DESC";

        $entityAnterior = $this->getDoctrine()->getConnection()->fetchAll($sqlAnterior);

        $_data = array();

        if (sizeof($entityAnterior) > 0)
            if ($entityAnterior[0]['entrada_importe'] != 0) {
                $_data[] = array(
                    'fecha_entrada' => $entityAnterior[0]['fecha'],
                    'nro_factura' => $entityAnterior[0]['nro_factura'],
                    'entrada_importe' => $entityAnterior[0]['entrada_importe'],
                    'fecha_salida' => '',
                    'no_anticipo' => '',
                    'nro_vale' => '',
                    'centro_costo' => '',
                    'chapa' => '',
                    'cantidad' => '',
                    'importe_inicial' => '',
                    'salida_importe' => '',
                    'importe_final' => $entityAnterior[0]['existencia_importe']
                );
            }
            else {
                $_data[] = array(
                    'fecha_entrada' => $entityAnterior[0]['fecha'],
                    'nro_factura' => $entityAnterior[0]['nro_factura'],
                    'entrada_importe' => $entityAnterior[0]['salida_importe'],
                    'fecha_salida' => '',
                    'no_anticipo' => '',
                    'nro_vale' => '',
                    'centro_costo' => '',
                    'chapa' => '',
                    'cantidad' => '',
                    'importe_inicial' => '',
                    'salida_importe' => '',
                    'importe_final' => $entityAnterior[0]['existencia_importe']
                );
            }

        $fechaActual = FechaUtil::getUltimoDiaMes($mes, $anno);
        $fechaActual.= ' 24:00:00';
        $sql = "select
                    ht.nro_vale as vale,
                    to_char(ht.fecha,'DD/MM/YYYY HH:MI AM') as fecha,
                    ht.nro_factura,
                    ht.entrada_importe as entrada_importe,
                    ht.entrada_cantidad as entrada_litros,
                    ht.salida_importe as salida_importe,
                    ht.salida_cantidad as salida_litros,
                    ht.existencia_importe as existencia_importe,
                    ht.existencia_cantidad as existencia_litros,
                    v.matricula as chapa,
                    tc.nombre as combustible,
                    cc.nombre as centro_costo,
                    an.no_vale as anticipo

                    from datos.historial_tarjeta as ht
                    left join datos.liquidacion as lq on ht.liquidacionid = lq.id
                    inner join nomencladores.tarjeta as t on ht.tarjetaid = t.id
                    inner join nomencladores.tipo_combustible as tc on t.ntipo_combustibleid = tc.id
                    left join nomencladores.centro_costo as cc on lq.ncentrocostoid = cc.id
                    left join nomencladores.vehiculo as v on lq.nvehiculoid = v.id
                    left join datos.anticipo as an on an.id = lq.anticipo

                    where
                    t.nunidadid in ($unidades_string) and ht.fecha between '$fechaAnterior' and '$fechaActual' and t.nro_tarjeta = '$numero'
                    order by ht.fecha
                    offset $start limit $limit";

        $entities = $this->getDoctrine()->getConnection()->fetchAll($sql);

        foreach ($entities as $entity) {
            if ($entity['entrada_importe'] != 0) {
                $_data[] = array(
                    'fecha_entrada' => $entity['fecha'],
                    'nro_factura' => $entity['nro_factura'],
                    'entrada_importe' => $entity['entrada_importe'],
                    'fecha_salida' => '',
                    'no_anticipo' => '',
                    'nro_vale' => '',
                    'centro_costo' => '',
                    'chapa' => '',
                    'cantidad' => '',
                    'importe_inicial' => '',
                    'salida_importe' => '',
                    'importe_final' => $entity['existencia_importe']
                );
            } else {
                $_data[] = array(
                    'fecha_entrada' => '',
                    'nro_factura' => '',
                    'entrada_importe' => '',
                    'fecha_salida' => $entity['fecha'],
                    'no_anticipo' => $entity['anticipo'],
                    'nro_vale' => $entity['vale'],
                    'centro_costo' => $entity['centro_costo'],
                    'chapa' => $entity['chapa'],
                    'cantidad' => $entity['salida_litros'],
                    'importe_inicial' => $entity['salida_importe'] + $entity['existencia_importe'] - $entity['entrada_importe'],
                    'salida_importe' => $entity['salida_importe'],
                    'importe_final' => $entity['existencia_importe']
                );
            }
        }

        if(count($_data) === 0){
            /**@var Tarjeta $tarjeta*/
            $tarjeta = $em->getRepository('PortadoresBundle:Tarjeta')->findOneBy(array('nroTarjeta' => $numero));
            $_data[] = array(
                'fecha_entrada' =>$tarjeta->getFechaRegistro()->format('Y-m-d'),
                'nro_factura' => '',
                'entrada_importe' => '',
                'fecha_salida' => '',
                'no_anticipo' => '',
                'nro_vale' => '',
                'centro_costo' => $tarjeta->getCentrocosto()->getNombre(),
                'chapa' => '',
                'cantidad' => '',
                'importe_inicial' => $tarjeta->getImporte(),
                'salida_importe' => '',
                'importe_final' => $tarjeta->getImporte()
            );
        }


        $_html = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
        <html xmlns='http://www.w3.org/1999/xhtml'>
        <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <title>Libro Combustible en Caja</title>
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
            <td colspan='13' style='text-align: center;border: none; font-size: 14px;'><strong>Libro Combustible en Caja</strong></td>
         </tr>

         <tr>

            <td colspan='4' style='text-align: center;border: none;font-size: 12px;'><strong>Año: $anno</strong></td>
            <td colspan='4' style='text-align: center;border: none;font-size: 12px;'><strong>Mes: $mesNombre</strong></td>
            <td colspan='5' style='text-align: center;border: none;font-size: 12px;'><strong>Tarjeta: $numero</strong></td>
         </tr>

         <tr>
            <td style='text-align: center;'><strong>Fecha</strong></td>
            <td style='text-align: center;'><strong>No. Fact</strong></td>
            <td style='text-align: center;'><strong>Importe</strong></td>
            <td style='text-align: center;'><strong>Firma</strong></td>
            <td style='text-align: center;'><strong>Fecha</strong></td>
            <td style='text-align: center;'><strong>No. Anticipo</strong></td>
            <td style='text-align: center;;'><strong>No. Vale Servi.</strong></td>
            <td style='text-align: center;'><strong>Centro Costo</strong></td>
            <td style='text-align: center;'><strong>Chapa</strong></td>
            <td style='text-align: center;'><strong>Cantidad</strong></td>
            <td style='text-align: center;'><strong>Importe Inic.</strong></td>
            <td style='text-align: center;'><strong>Importe Abast.</strong></td>
            <td style='text-align: center;'><strong>Importe Final</strong></td>
         </tr>";

        for ($i = 0; $i < sizeof($_data); $i++) {
            $_html .= "<tr>
                    <td style='text-align: center;'>" . $_data[$i]['fecha_entrada'] . "</td>
                    <td style='text-align: center;'>" . $_data[$i]['nro_factura'] . " </td>
                    <td style='text-align: center;'>" . $_data[$i]['entrada_importe'] . "</td>
                    <td style='text-align: center;'></td>
                    <td style='text-align: center;'>" . $_data[$i]['fecha_salida'] . "</td>
                    <td style='text-align: center;'>" . $_data[$i]['no_anticipo'] . "</td>
                    <td style='text-align: center;'>" . $_data[$i]['nro_vale'] . "</td>
                    <td style='text-align: center;'>" . $_data[$i]['centro_costo'] . "</td>
                    <td style='text-align: center;'>" . $_data[$i]['chapa'] . "</td>
                    <td style='text-align: center;'>" . $_data[$i]['cantidad'] . "</td>
                    <td style='text-align: center;'>" . $_data[$i]['importe_inicial'] . "</td>
                    <td style='text-align: center;'>" . $_data[$i]['salida_importe'] . "</td>
                    <td style='text-align: center;'>" . $_data[$i]['importe_final'] . "</td>
                    </tr>";
        }

        $_html .= "</table>
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