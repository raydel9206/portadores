<?php
/**
 * Created by PhpStorm.
 * User: pfcadenas
 * Date: 25/10/2016
 * Time: 8:34
 */

namespace Geocuba\PortadoresBundle\Controller;


use Doctrine\Common\Util\Debug;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;
use Geocuba\PortadoresBundle\Util\Utiles;


class ReporteReembolsoCombustibleController extends Controller
{
    use ViewActionTrait;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $unidadid = trim($request->get('unidadid'));
        $monedaid = trim($request->get('monedaid'));
        $fechaDesde = trim($request->get('fechaDesde'));
        $fechaHasta = trim($request->get('fechaHasta'));
        $export = $request->get('export');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($unidadid), $_unidades);

        $unidades_string = $this->unidadesToString($_unidades);

        if ((is_null($monedaid) || $monedaid == '') || (is_null($fechaDesde) || $fechaDesde == '') || (is_null($fechaHasta) || $fechaHasta == '')) {
            return new JsonResponse(array('rows' => array(), 'total' => 0));
        }

        $strFechaDesde = date_create_from_format('d/m/Y', trim($request->get('fechaDesde')));
        $strFechaHasta = date_create_from_format('d/m/Y', trim($request->get('fechaHasta')));
        $fechaDesde = $strFechaDesde->format('Y/m/d');
        $fechaHasta = $strFechaHasta->format('Y/m/d') . ' 23:59:59';


        $sql = "select
                    extract( day from lq.fecha_vale) as dia,
                    a.no_vale as no_vale,
                    lq.importe as importe

                    from datos.liquidacion as lq
                    inner join datos.anticipo as a on lq.anticipo = a.id
                    inner join nomencladores.tarjeta as t on a.tarjeta = t.id

                    where t.nunidadid in ($unidades_string) and t.nmonedaid = '$monedaid' and a.visible=true and a.fecha_cierre between '$fechaDesde' and '$fechaHasta'
                    order by lq.fecha_vale";

        $entities = $this->getDoctrine()->getConnection()->fetchAll($sql);

        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'dia' => $entity['dia'],
                'vale' => $entity['no_vale'],
                'importe' => $entity['importe']
            );
        }

        if ($export) {
            return $_data;
        }

        return new JsonResponse(array('rows' => $_data, 'total' => count($_data)));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function printAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $this->loadAction($request);


        $monedaid = trim($request->get('monedaid'));
        $fechaDesde = trim($request->get('fechaDesde'));
        $fechaHasta = trim($request->get('fechaHasta'));

        if ((is_null($monedaid) || $monedaid == '') || (is_null($fechaDesde) || $fechaDesde == '') || (is_null($fechaHasta) || $fechaHasta == '')) {
            return new JsonResponse(array('rows' => array(), 'total' => 0));
        }

        $strFechaDesde = date_create_from_format('d/m/Y', trim($request->get('fechaDesde')));
        $strFechaHasta = date_create_from_format('d/m/Y', trim($request->get('fechaHasta')));
        $fechaDesde = $strFechaDesde->format('Y/m/d');
        $fechaHasta = $strFechaHasta->format('Y/m/d') . ' 23:59:59';

        $arrFechaDesde = explode('/', $fechaDesde);
        $arrFechaHasta = explode('/', $strFechaHasta->format('Y/m/d'));

        $moneda = $em->getRepository('PortadoresBundle:Moneda')->find($monedaid);

        $entities = $this->loadAction($request);

        $_html = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
        <html xmlns='http://www.w3.org/1999/xhtml'>
        <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <title>REEMBOLSO DE COMBUSTIBLE</title>
        <style>

            table.main {
                border:0px solid;
                border-radius:5px 5px 5px 5px;
                font-family: 'Times New Roman', Times, serif;
                font-size: 10px;
            }


            table {
                border:0 solid;
                border-radius:0;
                font-family: 'Arial', serif;
                font-size: 10px;
                border-collapse: collapse;
            }

            td, tr{
                width: 40px;
            }

        </style>
        </head>

        <body>
        <table cellspacing='0' cellpadding='3' border='1' width='100%'>

         <tr>
            <td colspan='6' style='text-align: center;'>Entidad:<br>" . $request->get('unidad_nombre') . "</td>
            <td  colspan='12' style='text-align: center;'><b>REEMBOLSO DE COMBUSTIBLE<br>" . $moneda->getNombre() . "</b></td>
            <td  colspan='6' style='text-align: center;'>

            <table cellspacing='0' cellpadding='2' border='1' width='100%'>
            <body>
            <tr>
            <td colspan='2'>DÍA</td>
            <td rowspan='2'>MES</td>
            <td rowspan='2'>AÑO</td>
            </tr>
            <tr>
            <td>DEL</td>
            <td>AL</td>
            </tr>
            <tr>
            <td>$arrFechaDesde[2]</td>
            <td>$arrFechaHasta[2]</td>
            <td>$arrFechaDesde[1]</td>
            <td>$arrFechaDesde[0]</td>
            </tr>
            </body>
            </table>


            </td>
         </tr>

         <tr>
            <td colspan='1' style='text-align: center;'><strong>Día</strong></td>
            <td colspan='2' style='text-align: center;'><strong>Núm</strong></td>
            <td colspan='3' style='text-align: center;'><strong>IMPORTE</strong></td>

            <td colspan='1' style='text-align: center;'><strong>Día</strong></td>
            <td colspan='2' style='text-align: center;'><strong>Núm</strong></td>
            <td colspan='3' style='text-align: center;'><strong>IMPORTE</strong></td>

            <td colspan='1' style='text-align: center;'><strong>Día</strong></td>
            <td colspan='2' style='text-align: center;'><strong>Núm</strong></td>
            <td colspan='3' style='text-align: center;'><strong>IMPORTE</strong></td>

            <td colspan='1' style='text-align: center;'><strong>Día</strong></td>
            <td colspan='2' style='text-align: center;'><strong>Núm</strong></td>
            <td colspan='3' style='text-align: center;'><strong>IMPORTE</strong></td>
         </tr>
         ";

        $a1 = 0;
        $a2 = 29;
        $a3 = 58;
        $a4 = 87;

        $total = 0;

        while ($a1 < 29) {
            $_html .= "<tr>";

            if (count($entities) > $a1) {
                $_html .= "<td colspan='1' style='text-align: center;'>" . $entities[$a1]['dia'] . "</td>
                <td colspan='2' style='text-align: center;'>" . $entities[$a1]['vale'] . "</td>
                <td colspan='2' style='text-align: center;'>" . number_format($entities[$a1]['importe'], 2) . "</td>
                <td colspan='1' style='text-align: center;'>";

                $total += $entities[$a1]['importe'];
            } else {
                $_html .= "<td colspan='1' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'>";
            }

            if (count($entities) > $a2) {
                $_html .= "<td colspan='1' style='text-align: center;'>" . $entities[$a2]['dia'] . "</td>
                <td colspan='2' style='text-align: center;'>" . $entities[$a2]['vale'] . "</td>
                <td colspan='2' style='text-align: center;'>" . number_format($entities[$a2]['importe'], 2) . "</td>
                <td colspan='1' style='text-align: center;'>";

                $total += $entities[$a2]['importe'];
            } else {
                $_html .= "<td colspan='1' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'>";
            }

            if (count($entities) > $a3) {
                $_html .= "<td colspan='1' style='text-align: center;'>" . $entities[$a3]['dia'] . "</td>
                <td colspan='2' style='text-align: center;'>" . $entities[$a3]['vale'] . "</td>
                <td colspan='2' style='text-align: center;'>" . number_format($entities[$a3]['importe'], 2) . "</td>
                <td colspan='1' style='text-align: center;'>";

                $total += $entities[$a3]['importe'];
            } else {
                $_html .= "<td colspan='1' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'>";
            }

            if (count($entities) > $a4) {
                $_html .= "<td colspan='1' style='text-align: center;'>" . $entities[$a4]['dia'] . "</td>
                <td colspan='2' style='text-align: center;'>" . $entities[$a4]['vale'] . "</td>
                <td colspan='2' style='text-align: center;'>" . number_format($entities[$a4]['importe'], 2) . "</td>
                <td colspan='1' style='text-align: center;'>";

                $total += $entities[$a4]['importe'];
            } else {
                $_html .= "<td colspan='1' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>";
            }

            $_html .= "</tr>";

            $a1++;
            $a2++;
            $a3++;
            $a4++;
        }

        $_html .= "<tr>
                <td colspan='19' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'>TOTAL</td>
                <td colspan='2' style='text-align: center;'>$" . number_format($total, 2) . "</td>
                <td colspan='1' style='text-align: center;'></td>
            </tr>";

        $_html .= "<tr>
                <td colspan='24' style='text-align: center;'>ASIENTOS DE CONTABILIDAD (DÉBITOS)</td>
            </tr>

            <tr>
                <td colspan='4' style='text-align: center;'>CÓDIGO</td>
                <td colspan='4' style='text-align: center;'>IMPORTE</td>

                <td colspan='4' style='text-align: center;'>CÓDIGO</td>
                <td colspan='4' style='text-align: center;'>IMPORTE</td>

                <td colspan='4' style='text-align: center;'>CÓDIGO</td>
                <td colspan='4' style='text-align: center;'>IMPORTE</td>
            </tr>

            <tr>
                <td colspan='2' style='text-align: center;'>Cuenta</td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'>Parcial</td>
                <td colspan='2' style='text-align: center;'>Cuenta<br>Control</td>

                <td colspan='2' style='text-align: center;'>Cuenta</td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'>Parcial</td>
                <td colspan='2' style='text-align: center;'>Cuenta<br>Control</td>

                <td colspan='2' style='text-align: center;'>Cuenta</td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'>Parcial</td>
                <td colspan='2' style='text-align: center;'>Cuenta<br>Control</td>
            </tr>
            ";

        $i = 6;
        while ($i-- > 0) {
            $_html .= "<tr>
                   <td colspan='2' style='text-align: center;'></td>
                    <td colspan='1' style='text-align: center;'></td>
                    <td colspan='1' style='text-align: center;'></td>
                    <td colspan='2' style='text-align: center;'></td>
                    <td colspan='2' style='text-align: center;'></td>

                    <td colspan='2' style='text-align: center;'></td>
                    <td colspan='1' style='text-align: center;'></td>
                    <td colspan='1' style='text-align: center;'></td>
                    <td colspan='2' style='text-align: center;'></td>
                    <td colspan='2' style='text-align: center;'></td>

                    <td colspan='2' style='text-align: center;'></td>
                    <td colspan='1' style='text-align: center;'></td>
                    <td colspan='1' style='text-align: center;'></td>
                    <td colspan='2' style='text-align: center;'></td>
                    <td colspan='2' style='text-align: center;'></td>
                </tr>";
        }

        $_html .= "
            <tr>
               <td colspan='2' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>

                <td colspan='2' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>

                <td colspan='8' style='text-align: center;'>CRÉDITOS</td>
            </tr>

             <tr>
               <td colspan='2' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>

                <td colspan='2' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>

                <td colspan='3' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'>CÓDIGO</td>
                <td colspan='3' style='text-align: center;'>IMPORTE</td>
            </tr>

            <tr>
               <td colspan='2' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>

                <td colspan='2' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>

                <td colspan='3' style='text-align: left;'>Cuenta</td>
                <td colspan='2' style='text-align: center;'></td>
                <td colspan='3' style='text-align: center;'></td>
            </tr>

             <tr>
               <td colspan='2' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>

                <td colspan='2' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>

                <td colspan='3' style='text-align: left;'>Sub-Cuenta</td>
                <td colspan='2' style='text-align: center;'></td>
                <td colspan='3' style='text-align: center;'></td>
            </tr>

            <tr>
               <td colspan='2' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>

                <td colspan='2' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>

                <td colspan='8' style='text-align: center;'>CHEQUE NOMINATIVO</td>
            </tr>

             <tr>
               <td colspan='2' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>

                <td colspan='2' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>

                <td colspan='5' style='text-align: center;'>NÚMERO</td>
                <td colspan='3' style='text-align: center;'>FECHA</td>
            </tr>

            <tr>
               <td colspan='2' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>

                <td colspan='2' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>

                <td rowspan='2' colspan='5' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'>DÍA</td>
                <td colspan='1' style='text-align: center;'>MES</td>
                <td colspan='1' style='text-align: center;'>AÑO</td>
            </tr>

            <tr>
               <td colspan='2' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>

                <td colspan='2' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>

                <td colspan='1' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
            </tr>

            <tr>
               <td colspan='2' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>

                <td colspan='2' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='1' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>
                <td colspan='2' style='text-align: center;'></td>

                <td colspan='4' style='text-align: left;'>ANOTADO:</td>
                <td colspan='4' style='text-align: left;'>ANOTADO:</td>
            </tr>

            <tr>
                <td colspan='4' style='text-align: left;'>CUSTODIO:<br> &nbsp;</td>
                <td colspan='4' style='text-align: left;'>RECIBIDO:<br>&nbsp; </td>

                <td colspan='4' style='text-align: left;'>REVISADO:<br>&nbsp; </td>
                <td colspan='4' style='text-align: left;'>AUTORIZADO:<br>&nbsp; </td>

                <td colspan='4' style='text-align: left;'>CODIFICADO:<br> &nbsp;</td>
                <td colspan='4' style='text-align: left;'>NO.<br> &nbsp;</td>
            </tr>";

        $_html .= "</table>
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