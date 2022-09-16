<?php
/**
 * Created by PhpStorm.
 * User: rherrerag
 * Date: 1/9/2018
 * Time: 11:24 a.m.
 */

namespace Geocuba\PortadoresBundle\Controller;

use Doctrine\Common\CommonException;
use Doctrine\Common\Util\Debug;
use Geocuba\PortadoresBundle\Entity\Tarjeta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Geocuba\AdminBundle\Util\Util;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\DocumentosEnum;

class AnalisisTarjetaController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $datos = array();
        $datos_anterior = array();
        $idtarjeta = $request->get('id');
        $action = $request->get('action');

        $anno = $request->get('anno');
        $mes = $request->get('mes');
        $em = $this->getDoctrine()->getManager();
        $anno_anterior = $anno;

        if ($mes != 1) {
            $mes_anterior = $mes - 1;
        } else {
            $mes_anterior = 12;
            $anno_anterior = $anno - 1;
        }

        $fechaActual = $anno . '-' . $mes . '-01';
        $fechaObj = date_create_from_format('Y-m-d', $fechaActual);

        $conn = $this->getDoctrine()->getConnection();

        $sql = "select t.nro_tarjeta,                     
                     ht.fecha,
                     s.nombre as servicentro,                                         
                     (ht.entrada_importe+ht.salida_importe) as importe,
                     (ht.entrada_cantidad+ht.salida_cantidad) as cantidad,
                     ht.existencia_importe as saldo_final,
                     ht.existencia_cantidad as cantidad_final,                     
                     v.matricula,
                     v.norma,
                     v.id as vehiculo,
                     ht.nro_vale,
                     n.nombre as responsable,
                     tc.nombre as servicio
                 from datos.historial_tarjeta as ht 
                 join nomencladores.tarjeta as t on t.id = ht.tarjetaid
                 left join datos.liquidacion as l on l.id = ht.liquidacionid
                 left join nomencladores.servicentro s on s.id = l.nservicentroid
                 left join nomencladores.vehiculo as v on v.id = l.nvehiculoid
                 left join nomencladores.persona as n on n.id = l.npersonaid
                 left join nomencladores.tipo_combustible as tc on tc.id = t.ntipo_combustibleid
                 where ht.tarjetaid = '$idtarjeta'
                 and extract(month from ht.fecha) = '$mes'
                 and extract(year from ht.fecha) = '$anno'
                 order by fecha";

        $datos = $conn->fetchAll($sql);

        $data = array();
        $cont = 1;
        for ($i = 0; $i < sizeof($datos); $i++) {
            $reg_norma = $em->getRepository('PortadoresBundle:RegistroCombustible')->findOneBy(array('vehiculoid' => $datos[$i]['vehiculo'], 'fecha' => $fechaObj));
            $fecha = date_create_from_format('Y-m-d H:i:s', $datos[$i]['fecha']);
            $data[$i]['fecha'] = $fecha->format('d/m/Y');
            $data[$i]['hora'] = $fecha->format('g:i A');
            $data[$i]['servicentro'] = $datos[$i]['servicentro'];
            $data[$i]['servicio'] = $datos[$i]['servicio'];
            $data[$i]['importe'] = $datos[$i]['importe'];
            $data[$i]['cantidad'] = $datos[$i]['cantidad'];
            $data[$i]['saldo_final'] = $datos[$i]['saldo_final'];
            $data[$i]['cantidad_final'] = $datos[$i]['cantidad_final'];
            $data[$i]['matricula'] = $datos[$i]['matricula'];
            $data[$i]['norma'] = ($reg_norma) ? $reg_norma->getNormaPlan() : $datos[$i]['norma'];
            $data[$i]['responsable'] = $datos[$i]['responsable'];

            if ($i > 0) {
                if ($data[$i]['fecha'] == $data[$i - 1]['fecha'] && $data[$i]['matricula'] && $data[$i - 1]['matricula'])
                    $cont++;
                else
                    $cont = 1;
            }


            $anexos = array();
            if ($data[$i]['matricula']) {
                $fecha_string = $fecha->format('Y-m-d');
                $sql = "select ck.combustible_estimado_tanque as combustible, ck.kilometraje as kilometraje from datos.combustible_kilometros ck where ck.fecha = '$fecha_string' and ck.ntarjetaid = '$idtarjeta' order by ck.kilometraje limit '$cont'";
                $anexos = $conn->fetchAll($sql);
            }

            $data[$i]['kilometraje'] = isset($anexos[$cont - 1]['kilometraje']) ? $anexos[$cont - 1]['kilometraje'] : '';
            $data[$i]['combustible'] = isset($anexos[$cont - 1]['combustible']) ? $anexos[$cont - 1]['combustible'] : '';

        }

        if (count($data) === 0) {
            /**@var Tarjeta $tarjeta */
            $tarjeta = $em->getRepository('PortadoresBundle:Tarjeta')->find($idtarjeta);
            $data[] = array(
                'fecha' => $tarjeta->getFechaRegistro()->format('d/m/Y'),
                'hora' => '',
                'servicentro' => '',
                'servicio' => '',
                'importe' => $tarjeta->getImporte(),
                'cantidad' => round($tarjeta->getImporte() / $tarjeta->getTipoCombustibleid()->getPrecio(), 2),
                'saldo_final' => $tarjeta->getImporte(),
                'cantidad_final' => round($tarjeta->getImporte() / $tarjeta->getTipoCombustibleid()->getPrecio(), 2),
                'matricula' => round($tarjeta->getImporte() / $tarjeta->getTipoCombustibleid()->getPrecio(), 2),
                'norma' => '',
                'responsable' => '',
                'kilometraje' => '',
                'combustible' => ''
            );
        }

        if ($action) {
            return $data;
        }
        return new JsonResponse(array('rows' => $data, 'total' => count($data)));
    }

    public function printAction(Request $request)
    {

        $datos = $this->loadAction($request);
        $tarjeta = $request->get('id');

        $em = $this->getDoctrine()->getManager();

        $tarjeta = $em->getRepository('PortadoresBundle:Tarjeta')->find($tarjeta);
        $unidad = strtoupper($tarjeta->getUnidadid()->getSiglas());

        $html = "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\"
xmlns:x=\"urn:schemas-microsoft-com:office:excel\"
xmlns=\"http://www.w3.org/TR/REC-html40\">

<head>
<meta http-equiv=Content-Type content=\"text/html; charset=utf-8\">
<meta name=ProgId content=Excel.Sheet>
<meta name=Generator content=\"Microsoft Excel 15\">
<link rel=File-List
href=\"TARJETAS%20MAGNETICAS%20DE%20COMBUSTIBLE_files/filelist.xml\">
<style id=\"TARJETAS MAGNETICAS DE COMBUSTIBLE_31493_Styles\">
<!--table
	{mso-displayed-decimal-separator:\"\.\";
	mso-displayed-thousand-separator:\"\,\";}
.font531493
	{color:black;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;}
.font631493
	{color:black;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;}
.xl6331493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl6431493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl6531493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl6631493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl6731493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:middle;
	border:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl6831493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:justify;
	vertical-align:justify;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl6931493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:justify;
	border:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl7031493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:justify;
	vertical-align:justify;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl7131493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:justify;
	vertical-align:justify;
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl7231493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:none;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl7331493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl7431493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:justify;
	vertical-align:justify;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl7531493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:justify;
	vertical-align:justify;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl7631493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl7731493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl7831493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl7931493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:none;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl8031493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:justify;
	vertical-align:justify;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl8131493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:justify;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl8231493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:none;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl8331493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:justify;
	vertical-align:justify;
	border-top:none;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl8431493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:justify;
	vertical-align:justify;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl8531493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl8631493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"\[$-409\]h\:mm\\ AM\/PM\;\@\";
	text-align:center;
	vertical-align:middle;
	border:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl8731493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"Short Date\";
	text-align:center;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl8831493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:Fixed;
	text-align:center;
	vertical-align:middle;
	border:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl8931493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl9031493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl9131493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:justify;
	vertical-align:justify;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl9231493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:justify;
	vertical-align:justify;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl9331493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:justify;
	vertical-align:justify;
	border-top:.5pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl9431493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:justify;
	vertical-align:justify;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl9531493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:middle;
	border-top:none;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl9631493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:middle;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl9731493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:middle;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl9831493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:18.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl9931493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:18.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl10031493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:justify;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl10131493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:bottom;
	border-top:none;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl10231493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:bottom;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl10331493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:0;
	text-align:center;
	vertical-align:bottom;
	border-top:none;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl10431493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl10531493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl10631493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl10731493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:justify;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl10831493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:middle;
	border-top:none;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl10931493
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:middle;
	border-top:none;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
-->
</style>
</head>

<body>
<!--[if !excel]>&nbsp;&nbsp;<![endif]-->
<!--The following information was generated by Microsoft Excel's Publish as Web
Page wizard.-->
<!--If the same item is republished from Excel, all information between the DIV
tags will be replaced.-->
<!----------------------------->
<!--START OF OUTPUT FROM EXCEL PUBLISH AS WEB PAGE WIZARD -->
<!----------------------------->

<div id=\"TARJETAS MAGNETICAS DE COMBUSTIBLE_31493\" align=center
x:publishsource=\"Excel\">

<table border=0 cellpadding=0 cellspacing=0 width=1592 class=xl6331493
 style='border-collapse:collapse;table-layout:fixed;width:1198pt'>
 <col class=xl6331493 width=105 style='mso-width-source:userset;mso-width-alt:
 3840;width:79pt'>
 <col class=xl6331493 width=93 style='mso-width-source:userset;mso-width-alt:
 3401;width:70pt'>
 <col class=xl6331493 width=138 style='mso-width-source:userset;mso-width-alt:
 5046;width:104pt'>
 <col class=xl6331493 width=113 style='mso-width-source:userset;mso-width-alt:
 4132;width:85pt'>
 <col class=xl6831493 width=81 span=2 style='mso-width-source:userset;
 mso-width-alt:2962;width:61pt'>
 <col class=xl6831493 width=78 style='mso-width-source:userset;mso-width-alt:
 2852;width:59pt'>
 <col class=xl6831493 width=90 style='mso-width-source:userset;mso-width-alt:
 3291;width:68pt'>
 <col class=xl6831493 width=95 style='mso-width-source:userset;mso-width-alt:
 3474;width:71pt'>
 <col class=xl6831493 width=86 style='mso-width-source:userset;mso-width-alt:
 3145;width:65pt'>
 <col class=xl6831493 width=185 style='mso-width-source:userset;mso-width-alt:
 6765;width:139pt'>
 <col class=xl6831493 width=80 style='width:60pt'>
 <col class=xl6831493 width=145 style='mso-width-source:userset;mso-width-alt:
 5302;width:109pt'>
 <col class=xl6831493 width=116 style='mso-width-source:userset;mso-width-alt:
 4242;width:87pt'>
 <col class=xl6831493 width=106 style='mso-width-source:userset;mso-width-alt:
 3876;width:80pt'>
 <tr height=43 style='mso-height-source:userset;height:32.25pt'>
  <td colspan=8 height=43 class=xl9831493 width=779 style='height:32.25pt;
  width:587pt'><a name=\"RANGE!A1:O32\">Análisis<span style='mso-spacerun:yes'> 
  </span>del Comportamiento de las Tarjetas Magnéticas<span
  style='mso-spacerun:yes'> </span></a></td>
  <td class=xl7031493 width=95 style='width:71pt'>&nbsp;</td>
  <td class=xl7031493 width=86 style='width:65pt'>&nbsp;</td>
  <td class=xl7031493 width=185 style='width:139pt'>&nbsp;</td>
  <td class=xl7031493 width=80 style='width:60pt'>&nbsp;</td>
  <td class=xl7031493 width=145 style='width:109pt'>&nbsp;</td>
  <td class=xl7031493 width=116 style='width:87pt'>&nbsp;</td>
  <td class=xl7131493 width=106 style='width:80pt'>&nbsp;</td>
 </tr>
 <tr height=24 style='height:18.0pt'>
  <td height=24 class=xl7231493 style='height:18.0pt'>Fecha:</td>
  <td class=xl7331493></td>
  <td class=xl7331493></td>
  <td class=xl7331493></td>
  <td class=xl7431493></td>
  <td class=xl7431493></td>
  <td class=xl7431493></td>
  <td class=xl7431493></td>
  <td class=xl7431493></td>
  <td class=xl7431493></td>
  <td class=xl7431493></td>
  <td class=xl7431493></td>
  <td class=xl7431493></td>
  <td class=xl7431493></td>
  <td class=xl7531493>&nbsp;</td>
 </tr>
 <tr height=24 style='height:18.0pt'>
  <td height=24 class=xl7231493 style='height:18.0pt'>OACE:</td>
  <td class=xl8531493>INRH</td>
  <td class=xl7331493></td>
  <td colspan=2 class=xl8531493>Empresa:</td>
  <td colspan=3 class=xl10031493>" . strtoupper($tarjeta->getUnidadid()->getSiglas()) . "</td>
  <td class=xl7431493></td>
  <td class=xl7431493>UEB:</td>
  <td class=xl7431493></td>
  <td class=xl7431493></td>
  <td class=xl7431493></td>
  <td class=xl7431493></td>
  <td class=xl7531493>" . ($datos[0]['importe'] / $datos[0]['cantidad']) . "</td>
 </tr>
 <tr height=24 style='height:18.0pt'>
  <td height=24 class=xl7231493 style='height:18.0pt'>&nbsp;</td>
  <td class=xl7331493></td>
  <td class=xl7331493></td>
  <td class=xl7331493></td>
  <td class=xl7431493></td>
  <td class=xl7431493></td>
  <td class=xl7431493></td>
  <td class=xl7431493></td>
  <td class=xl7431493></td>
  <td class=xl7431493></td>
  <td class=xl7431493></td>
  <td class=xl7431493></td>
  <td class=xl7431493></td>
  <td class=xl7431493></td>
  <td class=xl7531493>&nbsp;</td>
 </tr>
 <tr height=25 style='height:18.75pt'>
  <td colspan=2 height=25 class=xl10131493 style='height:18.75pt'><font
  class=\"font631493\">&#8470;</font><font class=\"font531493\">. De tarjeta:<span
  style='mso-spacerun:yes'> </span></font></td>
  <td colspan=5 class=xl10331493>" . $tarjeta->getNroTarjeta() . "</td>
  <td class=xl7431493></td>
  <td class=xl7431493></td>
  <td class=xl7431493></td>
  <td class=xl7431493></td>
  <td class=xl7431493></td>
  <td class=xl7431493></td>
  <td class=xl7431493></td>
  <td class=xl7531493>&nbsp;</td>
 </tr>
 <tr class=xl6531493 height=21 style='height:15.75pt'>
  <td height=21 class=xl7631493 style='height:15.75pt'>1</td>
  <td class=xl6631493 style='border-left:none'>2</td>
  <td class=xl6631493 style='border-top:none;border-left:none'>3</td>
  <td class=xl6631493 style='border-top:none;border-left:none'>4</td>
  <td class=xl6931493 style='border-top:none;border-left:none'>5</td>
  <td class=xl6931493 style='border-top:none;border-left:none'>6</td>
  <td class=xl6931493 style='border-top:none;border-left:none'>7</td>
  <td class=xl6931493 style='border-left:none'>8</td>
  <td class=xl6931493 style='border-left:none'>9</td>
  <td class=xl6931493 style='border-left:none'>10</td>
  <td class=xl6931493 style='border-left:none'>11</td>
  <td class=xl6931493 style='border-left:none'>12</td>
  <td class=xl6931493 style='border-left:none'>13</td>
  <td class=xl6931493 style='border-left:none'>14</td>
  <td class=xl7731493 style='border-left:none'>15</td>
 </tr>
 <tr class=xl6531493 height=21 style='height:15.75pt'>
  <td height=21 class=xl7631493 style='height:15.75pt;border-top:none'>Fecha</td>
  <td class=xl6631493 style='border-top:none;border-left:none'>Hora</td>
  <td class=xl6631493 style='border-top:none;border-left:none'>Comercio</td>
  <td class=xl6631493 style='border-top:none;border-left:none'>Servicio</td>
  <td colspan=3 class=xl10431493 style='border-right:.5pt solid black;
  border-left:none'>Operaciones en Importe</td>
  <td colspan=3 class=xl10431493 style='border-right:.5pt solid black;
  border-left:none'>Operaciones en Litros</td>
  <td rowspan=2 class=xl8931493 style='border-bottom:.5pt solid black;
  border-top:none'>Asignado al Equipo</td>
  <td rowspan=2 class=xl9131493 style='border-bottom:.5pt solid black;
  border-top:none'>Indice Plan</td>
  <td rowspan=2 class=xl8931493 style='border-bottom:.5pt solid black;
  border-top:none'>Responsable</td>
  <td rowspan=2 class=xl9131493 style='border-bottom:.5pt solid black;
  border-top:none'>Capacidad del Tanque</td>
  <td rowspan=2 class=xl9331493 style='border-bottom:.5pt solid black;
  border-top:none'>Nivel de Actividad</td>
 </tr>
 <tr class=xl6531493 height=42 style='height:31.5pt'>
  <td height=42 class=xl7631493 style='height:31.5pt;border-top:none'>&nbsp;</td>
  <td class=xl6631493 style='border-top:none;border-left:none'>&nbsp;</td>
  <td class=xl6631493 style='border-top:none;border-left:none'>&nbsp;</td>
  <td class=xl6731493 style='border-top:none;border-left:none'>&nbsp;</td>
  <td class=xl6931493 style='border-top:none;border-left:none'>Saldo Inicial</td>
  <td class=xl6931493 style='border-top:none;border-left:none'>Importe</td>
  <td class=xl6931493 style='border-top:none;border-left:none'>Saldo Final</td>
  <td class=xl6931493 style='border-top:none;border-left:none'>Litros
  Iniciales<span style='mso-spacerun:yes'> </span></td>
  <td class=xl6931493 style='border-top:none;border-left:none'>Consumo o Carga</td>
  <td class=xl6931493 style='border-top:none;border-left:none'>Litros
  Finales<span style='mso-spacerun:yes'> </span></td>
 </tr>";

        $rows = 20 - sizeof($datos);

        foreach ($datos as $dato) {

            $html .= "<tr class=xl6531493 height=40 style='mso-height-source:userset;height:30.0pt'>
  <td height=40 class=xl8731493 style='height:30.0pt;border-top:none'>" . $dato['fecha'] . "</td>
  <td class=xl8631493 style='border-top:none;border-left:none'>" . $dato['hora'] . "</td>
  <td class=xl6931493 style='border-top:none;border-left:none'>" . $dato['servicentro'] . "</td>
  <td class=xl6931493 style='border-top:none;border-left:none'>" . $dato['servicio'] . "</td>
  <td class=xl8831493 style='border-top:none;border-left:none'>" . (number_format((float)$dato['saldo_final'] - (float)$dato['importe'], 2)) . "</td>
  <td class=xl6631493 style='border-top:none;border-left:none'>" . $dato['importe'] . "</td>
  <td class=xl8831493 style='border-top:none;border-left:none'>" . $dato['saldo_final'] . "</td>
  <td class=xl6631493 style='border-top:none;border-left:none'>" . ((float)$dato['cantidad_final'] - (float)$dato['cantidad']) . "</td>
  <td class=xl6631493 style='border-top:none;border-left:none'>" . $dato['cantidad'] . "</td>
  <td class=xl6631493 style='border-top:none;border-left:none'>" . $dato['cantidad_final'] . "</td>
  <td class=xl6631493 style='border-top:none;border-left:none'>" . $dato['matricula'] . "</td>
  <td class=xl6631493 style='border-top:none;border-left:none'>" . $dato['norma'] . "</td>
  <td class=xl6631493 style='border-top:none;border-left:none'>" . $dato['responsable'] . "</td>
  <td class=xl6631493 style='border-top:none;border-left:none'>" . $dato['combustible'] . "</td>
  <td class=xl7731493 style='border-top:none;border-left:none'>" . $dato['kilometraje'] . "</td>
 </tr>";
        }

        for ($i = 0; $i < $rows; $i++) {
            $html .= "<tr class=xl6531493 height=39 style='mso-height-source:userset;height:29.25pt'>
  <td height=39 class=xl8731493 style='height:29.25pt;border-top:none'>&nbsp;</td>
  <td class=xl8631493 style='border-top:none;border-left:none'>&nbsp;</td>
  <td class=xl6931493 style='border-top:none;border-left:none'>&nbsp;</td>
  <td class=xl6931493 style='border-top:none;border-left:none'>&nbsp;</td>
  <td class=xl8831493 style='border-top:none;border-left:none'>&nbsp;</td>
  <td class=xl6631493 style='border-top:none;border-left:none'>&nbsp;</td>
  <td class=xl8831493 style='border-top:none;border-left:none'>&nbsp;</td>
  <td class=xl6631493 style='border-top:none;border-left:none'>&nbsp;</td>
  <td class=xl6631493 style='border-top:none;border-left:none'>&nbsp;</td>
  <td class=xl6631493 style='border-top:none;border-left:none'>&nbsp;</td>
  <td class=xl6931493 style='border-top:none;border-left:none'>&nbsp;</td>
  <td class=xl6931493 style='border-top:none;border-left:none'>&nbsp;</td>
  <td class=xl6931493 style='border-top:none;border-left:none'>&nbsp;</td>
  <td class=xl6931493 style='border-top:none;border-left:none'>&nbsp;</td>
  <td class=xl7731493 style='border-top:none;border-left:none'>&nbsp;</td>
 </tr>";

        }

        $html .= "
 <tr height=21 style='height:15.75pt'>
  <td height=21 class=xl7931493 style='height:15.75pt'>&nbsp;</td>
  <td class=xl6331493></td>
  <td class=xl6331493></td>
  <td class=xl6331493></td>
  <td class=xl6831493></td>
  <td class=xl6831493></td>
  <td class=xl6831493></td>
  <td class=xl6831493></td>
  <td class=xl6831493></td>
  <td class=xl6831493></td>
  <td class=xl6831493></td>
  <td class=xl6831493></td>
  <td class=xl6831493></td>
  <td class=xl6831493></td>
  <td class=xl8031493>&nbsp;</td>
 </tr>
 <tr height=28 style='mso-height-source:userset;height:21.0pt'>
  <td colspan=2 height=28 class=xl9531493 style='height:21.0pt'>Elaborado
  por:<span style='mso-spacerun:yes'> </span></td>
  <td class=xl6331493></td>
  <td class=xl6331493></td>
  <td class=xl6831493></td>
  <td class=xl6831493></td>
  <td colspan=2 class=xl9731493></td>
  <td class=xl6831493></td>
  <td class=xl6831493></td>
  <td colspan=2 class=xl9731493>Aprobado<span style='mso-spacerun:yes'> 
  </span>por:</td>
  <td class=xl6831493></td>
  <td class=xl6831493></td>
  <td class=xl8031493>&nbsp;</td>
 </tr>
 <tr height=21 style='height:15.75pt'>
  <td height=21 class=xl7931493 style='height:15.75pt'>&nbsp;</td>
  <td class=xl6331493></td>
  <td class=xl6331493></td>
  <td class=xl6331493></td>
  <td class=xl6831493></td>
  <td class=xl6831493></td>
  <td colspan=2 class=xl10731493></td>
  <td class=xl6831493></td>
  <td class=xl6831493></td>
  <td class=xl8131493>Director :</td>
  <td class=xl8131493></td>
  <td class=xl6831493></td>
  <td class=xl6831493></td>
  <td class=xl8031493>&nbsp;</td>
 </tr>
 <tr height=30 style='mso-height-source:userset;height:22.5pt'>
  <td colspan=3 height=30 class=xl10831493 style='height:22.5pt'>Responsable de
  la tarjeta Magnetica:<span style='mso-spacerun:yes'> </span></td>
  <td class=xl8231493>&nbsp;</td>
  <td class=xl8331493>&nbsp;</td>
  <td class=xl8331493>&nbsp;</td>
  <td class=xl8331493>&nbsp;</td>
  <td class=xl8331493>&nbsp;</td>
  <td class=xl8331493>&nbsp;</td>
  <td class=xl8331493>&nbsp;</td>
  <td class=xl8331493>Firma:</td>
  <td class=xl8331493>&nbsp;</td>
  <td class=xl8331493>&nbsp;</td>
  <td class=xl8331493>&nbsp;</td>
  <td class=xl8431493>&nbsp;</td>
 </tr>
 <![if supportMisalignedColumns]>
 <tr height=0 style='display:none'>
  <td width=105 style='width:79pt'></td>
  <td width=93 style='width:70pt'></td>
  <td width=138 style='width:104pt'></td>
  <td width=113 style='width:85pt'></td>
  <td width=81 style='width:61pt'></td>
  <td width=81 style='width:61pt'></td>
  <td width=78 style='width:59pt'></td>
  <td width=90 style='width:68pt'></td>
  <td width=95 style='width:71pt'></td>
  <td width=86 style='width:65pt'></td>
  <td width=185 style='width:139pt'></td>
  <td width=80 style='width:60pt'></td>
  <td width=145 style='width:109pt'></td>
  <td width=116 style='width:87pt'></td>
  <td width=106 style='width:80pt'></td>
 </tr>
 <![endif]>
</table>

</div>


<!----------------------------->
<!--END OF OUTPUT FROM EXCEL PUBLISH AS WEB PAGE WIZARD-->
<!----------------------------->
</body>

</html>
";

        return new Response(json_encode(array('success' => true, 'html' => $html)));
    }
}