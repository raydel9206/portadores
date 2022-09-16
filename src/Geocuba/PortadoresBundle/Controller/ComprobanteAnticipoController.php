<?php
/**
 * Created by PhpStorm.
 * User: Mire
 * Date: 16/04/2020
 * Time: 10:34
 */

namespace Geocuba\PortadoresBundle\Controller;

use Doctrine\Common\Util\Debug;
use Geocuba\AdminBundle\Util\Util;
use Geocuba\PortadoresBundle\Entity\DemandaCombustible;
use Geocuba\PortadoresBundle\Entity\Persona;
use Doctrine\Common\CommonException;
use Symfony\Component\HttpFoundation\Response;
use Geocuba\PortadoresBundle\Util\Datos;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;


class ComprobanteAnticipoController extends Controller
{

    use ViewActionTrait;

    public function loadAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $data = array();

        $unidadid = $request->get('unidadid');
        $moneda = $request->get('moneda');
        $anno = $request->get('anno');
        $mes = $request->get('mes');

        $calificadorCuentaDebito = $em->getRepository('PortadoresBundle:Clasificador')->findOneBy(array('codigo' => 'ATMC'));
        $calificadorCuentaCredito = $em->getRepository('PortadoresBundle:Clasificador')->findOneBy(array('codigo' => 'TMCC'));
        if(!$calificadorCuentaDebito || !$calificadorCuentaCredito){
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Aún no han sido agregados los clasificadores de cuenta'));
        }
        $cuentaDebito = $em->getRepository('PortadoresBundle:Cuenta')->findOneBy(array('clasificador' => $calificadorCuentaDebito));
        $cuentaCredito = $em->getRepository('PortadoresBundle:Cuenta')->findOneBy(array('clasificador' => $calificadorCuentaCredito));

        if(!$cuentaDebito || !$cuentaCredito){
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Algunas cuentas no han sido agregadas'));
        }

        $subcuentaDebito = $em->getRepository('PortadoresBundle:SubCuenta')->findOneBy(array('cuenta' =>  $cuentaDebito->getId(), 'moneda' => $moneda));
        $subcuentaCredito = $em->getRepository('PortadoresBundle:SubCuenta')->findOneBy(array('cuenta' =>  $cuentaCredito->getId(), 'moneda' => $moneda));
        $nro_subcuentaDebito = isset($subcuentaDebito) ? $subcuentaDebito->getNroSubcuenta() : null;
        $nro_subcuentaCredito = isset($subcuentaCredito) ? $subcuentaCredito->getNroSubcuenta() : null;


        $sql = "select a.no_vale, a.importe as importe, a.fecha 
                from datos.anticipo as a
                inner join nomencladores.tarjeta as t on a.tarjeta = t.id
                inner join nomencladores.unidad as u on u.id = t.nunidadid
                inner join nomencladores.moneda as m on t.nmonedaid = m.id
                where m.id = '$moneda' and u.id = '$unidadid'
                and extract( year from a.fecha) = $anno and extract(month from a.fecha) = $mes";

        $total = $this->getDoctrine()->getConnection()->fetchAll($sql);

        $sum = 0;
        foreach ($total as $item) {
            $sum += $item['importe'];
            $data[] = array(
                'nro_vale' => $item['no_vale'],
                'cuenta' => $cuentaDebito->getNroCuenta() . ' / ' . $nro_subcuentaDebito,
                'debito' => round($item['importe'], 2),
                'credito' =>'',
            );
            $data[] = array(
                'nro_vale' => $item['no_vale'],
                'cuenta' => $cuentaCredito->getNroCuenta() . ' / ' . $nro_subcuentaCredito,
                'debito' => '',
                'credito' => round($item['importe'], 2),
            );
        }

        return new JsonResponse(array('rows' => $data));
    }
    public function exportAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $_html = "MIME-Version: 1.0
X-Document-Type: Worksheet
Content-Location: file:///C:/6EC3326F/anticipo.htm
Content-Transfer-Encoding: quoted-printable
Content-Type: text/html; charset=\"windows-1252\"

<html xmlns:o=3D\"urn:schemas-microsoft-com:office:office\"
xmlns:x=3D\"urn:schemas-microsoft-com:office:excel\"
xmlns=3D\"http://www.w3.org/TR/REC-html40\">

<head>
<meta http-equiv=3DContent-Type content=3D\"text/html; charset=3Dwindows-125=
2\">
<meta name=3DProgId content=3DExcel.Sheet>
<meta name=3DGenerator content=3D\"Microsoft Excel 14\">
<link rel=3DFile-List href=3D\"anticipo_archivos/filelist.xml\">
<style id=3D\"anticipo_18599_Styles\">
<!--table
	{mso-displayed-decimal-separator:\"\.\";
	mso-displayed-thousand-separator:\"\,\";}
.xl1518599
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"MS Sans Serif\";
	mso-generic-font-family:auto;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl6318599
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Times New Roman\";
	mso-generic-font-family:auto;
	mso-font-charset:186;
	mso-number-format:General;
	text-align:left;
	vertical-align:middle;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl6418599
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:9.85pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Times New Roman\";
	mso-generic-font-family:auto;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:middle;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl6518599
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:9.85pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Times New Roman\";
	mso-generic-font-family:auto;
	mso-font-charset:0;
	mso-number-format:\"dd\\\/mm\\\/yyyy\";
	text-align:left;
	vertical-align:middle;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl6618599
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:9.85pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Times New Roman\";
	mso-generic-font-family:auto;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:middle;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl6718599
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:9.85pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Times New Roman\";
	mso-generic-font-family:auto;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:middle;
	border-top:none;
	border-right:none;
	border-bottom:.5pt solid black;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl6818599
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:9.85pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Times New Roman\";
	mso-generic-font-family:auto;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:right;
	vertical-align:middle;
	border-top:none;
	border-right:none;
	border-bottom:.5pt solid black;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl6918599
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:9.85pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Times New Roman\";
	mso-generic-font-family:auto;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:none;
	border-bottom:.5pt solid black;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl7018599
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:9.85pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Times New Roman\";
	mso-generic-font-family:auto;
	mso-font-charset:0;
	mso-number-format:\"\#\,\#\#0\.00_\)\;\\-\#\,\#\#0\.00\";
	text-align:right;
	vertical-align:middle;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl7118599
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:9.85pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Times New Roman\";
	mso-generic-font-family:auto;
	mso-font-charset:0;
	mso-number-format:\"dd\\\/mm\\\/yyyy\";
	text-align:general;
	vertical-align:middle;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl7218599
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:8.05pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Times New Roman\";
	mso-generic-font-family:auto;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:middle;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl7318599
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:9.85pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Times New Roman\";
	mso-generic-font-family:auto;
	mso-font-charset:0;
	mso-number-format:Standard;
	text-align:right;
	vertical-align:middle;
	border-top:.5pt solid black;
	border-right:none;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl7418599
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:9.85pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Times New Roman\";
	mso-generic-font-family:auto;
	mso-font-charset:0;
	mso-number-format:\"\#\,\#\#0\.00_\)\;\\-\#\,\#\#0\.00\";
	text-align:right;
	vertical-align:middle;
	border-top:.5pt solid black;
	border-right:none;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl7518599
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:9.85pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Times New Roman\";
	mso-generic-font-family:auto;
	mso-font-charset:0;
	mso-number-format:Standard;
	text-align:right;
	vertical-align:middle;
	border-top:none;
	border-right:none;
	border-bottom:2.0pt double black;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl7618599
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:9.85pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Times New Roman\";
	mso-generic-font-family:auto;
	mso-font-charset:0;
	mso-number-format:\"\#\,\#\#0\.00_\)\;\\-\#\,\#\#0\.00\";
	text-align:right;
	vertical-align:middle;
	border-top:none;
	border-right:none;
	border-bottom:2.0pt double black;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl7718599
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:9.85pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Times New Roman\";
	mso-generic-font-family:auto;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:middle;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl7818599
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Times New Roman\";
	mso-generic-font-family:auto;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:middle;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl7918599
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Times New Roman\";
	mso-generic-font-family:auto;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:middle;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl8018599
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:9.85pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Times New Roman\";
	mso-generic-font-family:auto;
	mso-font-charset:0;
	mso-number-format:\"\#\,\#\#0\";
	text-align:right;
	vertical-align:middle;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
-->
</style>
</head>

<body>
<!--[if !excel]>&nbsp;&nbsp;<![endif]-->
<!--La siguiente información se generó mediante el Asistente para publicar =
como
página web de Microsoft Excel.-->
<!--Si se vuelve a publicar el mismo elemento desde Excel, se reemplazará t=
oda
la información comprendida entre las etiquetas DIV.-->
<!----------------------------->
<!--INICIO DE LOS RESULTADOS DEL ASISTENTE PARA PUBLICAR COMO PÁGINA WEB DE
EXCEL -->
<!----------------------------->

<div id=3D\"anticipo_18599\" align=3Dcenter x:publishsource=3D\"Excel\">

<table border=3D0 cellpadding=3D0 cellspacing=3D0 width=3D874 style=3D'bord=
er-collapse:
 collapse;table-layout:fixed;width:654pt'>
 <col width=3D179 style=3D'mso-width-source:userset;mso-width-alt:6546;widt=
h:134pt'>
 <col width=3D79 style=3D'mso-width-source:userset;mso-width-alt:2889;width=
:59pt'>
 <col width=3D87 style=3D'mso-width-source:userset;mso-width-alt:3181;width=
:65pt'>
 <col width=3D40 style=3D'mso-width-source:userset;mso-width-alt:1462;width=
:30pt'>
 <col width=3D91 style=3D'mso-width-source:userset;mso-width-alt:3328;width=
:68pt'>
 <col width=3D75 style=3D'mso-width-source:userset;mso-width-alt:2742;width=
:56pt'>
 <col width=3D79 style=3D'mso-width-source:userset;mso-width-alt:2889;width=
:59pt'>
 <col width=3D60 style=3D'mso-width-source:userset;mso-width-alt:2194;width=
:45pt'>
 <col width=3D24 style=3D'mso-width-source:userset;mso-width-alt:877;width:=
18pt'>
 <col width=3D80 span=3D2 style=3D'width:60pt'>
 <tr height=3D21 style=3D'height:15.75pt'>
  <td height=3D21 class=3Dxl1518599 width=3D179 style=3D'height:15.75pt;wid=
th:134pt'></td>
  <td class=3Dxl1518599 width=3D79 style=3D'width:59pt'></td>
  <td class=3Dxl1518599 width=3D87 style=3D'width:65pt'></td>
  <td class=3Dxl6318599 colspan=3D6 width=3D369 style=3D'width:276pt'>SC-5-=
05 -
  COMPROBANTE DE OPERACIONES<span style=3D'mso-spacerun:yes'> </span></td>
  <td class=3Dxl1518599 width=3D80 style=3D'width:60pt'></td>
  <td class=3Dxl1518599 width=3D80 style=3D'width:60pt'></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6418599 colspan=3D4>271.0.00001 -- DIRECCION DE EMPRESA</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6418599 colspan=3D2>Período: Marzo 2022</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6518599 align=3Dcenter>#####</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6418599 colspan=3D2>Comprobantes</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Comprobante:</=
td>
  <td class=3Dxl6418599>00-00070</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220154</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>05</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>971.14 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220154</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0102534782</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>971.14 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>971.14</td>
  <td class=3Dxl7418599>971.14 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220154-A</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>02</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>713.49 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220154-A</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9007059910</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>713.49 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>713.49</td>
  <td class=3Dxl7418599>713.49 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220155</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>03</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,246.10 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220155</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9006371480</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,246.10 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>1,246.10</td>
  <td class=3Dxl7418599>1,246.10 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220155-1</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>03</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>8,673.85 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220155-1</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9007059894</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>8,673.85 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>8,673.85</td>
  <td class=3Dxl7418599>8,673.85 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220156</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>02</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,389.60 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220156</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9700222</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,389.60 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>1,389.60</td>
  <td class=3Dxl7418599>1,389.60 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220157</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>162 / 10002</t=
d>
  <td class=3Dxl6418599>9008194294</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>868.50 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9008194294</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>868.50 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>868.50</td>
  <td class=3Dxl7418599>868.50 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220158</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>02</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,466.03 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220158</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9006775953</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,466.03 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>1,466.03</td>
  <td class=3Dxl7418599>1,466.03 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220159</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>02</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,466.00 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220159</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9006775979</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,466.00 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>1,466.00</td>
  <td class=3Dxl7418599>1,466.00 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220160</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>02</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>2,052.40 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220160</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9006775987</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>2,052.40 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>2,052.40</td>
  <td class=3Dxl7418599>2,052.40 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220161</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>02</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,466.02 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220161</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9006775961</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,466.02 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>1,466.02</td>
  <td class=3Dxl7418599>1,466.02 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220162</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>162 / 10002</t=
d>
  <td class=3Dxl6418599>0102534774</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>2,962.81 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0102534774</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>2,962.81 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>2,962.81</td>
  <td class=3Dxl7418599>2,962.81 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220163</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>162 / 10002</t=
d>
  <td class=3Dxl6418599>0001614437</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>3,292.00 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0001614437</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>3,292.00 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>3,292.00</td>
  <td class=3Dxl7418599>3,292.00 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220164</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>02</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>2,199.00 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220164</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9006776001</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>2,199.00 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>2,199.00</td>
  <td class=3Dxl7418599>2,199.00 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220164-1</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>02</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,905.80 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220164-1</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9006776001</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,905.80 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>1,905.80</td>
  <td class=3Dxl7418599>1,905.80 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220164-2</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>02</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,319.40 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220164-2</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9006776001</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,319.40 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>1,319.40</td>
  <td class=3Dxl7418599>1,319.40 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220165</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>02</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>839.40 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220165</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0005925864</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>839.40 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>839.40</td>
  <td class=3Dxl7418599>839.40 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220166</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>02</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>699.50 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220166</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9008971634</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>699.50 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>699.50</td>
  <td class=3Dxl7418599>699.50 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220167</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>03</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,810.60 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220167</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9006371456</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,810.60 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>1,810.60</td>
  <td class=3Dxl7418599>1,810.60 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220168</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>90</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>699.50 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220168</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0005925906</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>699.50 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>699.50</td>
  <td class=3Dxl7418599>699.50 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220169</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>90</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,759.20 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220169</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9006371472</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,759.20 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>1,759.20</td>
  <td class=3Dxl7418599>1,759.20 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220170</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>90</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>699.50 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220170</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0005925880</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>699.50 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>699.50</td>
  <td class=3Dxl7418599>699.50 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220171</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>03</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>2,199.00 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220171</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9006371464</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>2,199.00 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>2,199.00</td>
  <td class=3Dxl7418599>2,199.00 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220172</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>03</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>726.92 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220172</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9007059928</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>726.92 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>726.92</td>
  <td class=3Dxl7418599>726.92 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220173</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>06</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>699.50 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220173</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0006639894</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>699.50 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>699.50</td>
  <td class=3Dxl7418599>699.50 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220174</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>162 / 10002</t=
d>
  <td class=3Dxl6418599>0006639902</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>699.50 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0006639902</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>699.50 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>699.50</td>
  <td class=3Dxl7418599>699.50 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220175</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>06</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>411.50 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220175</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0102584621</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>411.50 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>411.50</td>
  <td class=3Dxl7418599>411.50 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220176</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>06</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>2,962.80 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220177</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0102534808</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>2,962.80 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>2,962.80</td>
  <td class=3Dxl7418599>2,962.80 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220177</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>05</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,399.00 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220177</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0102584670</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,399.00 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>1,399.00</td>
  <td class=3Dxl7418599>1,399.00 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220178</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>03</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>6,645.25 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220178</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0005925906</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>6,645.25 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>6,645.25</td>
  <td class=3Dxl7418599>6,645.25 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220179</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>162 / 10002</t=
d>
  <td class=3Dxl6418599>0102584597</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,910.70 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0102584597</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,910.70 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>1,910.70</td>
  <td class=3Dxl7418599>1,910.70 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220180</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>162 / 10002</t=
d>
  <td class=3Dxl6418599>0103171469</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>411.50 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0103171469</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>411.50 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>411.50</td>
  <td class=3Dxl7418599>411.50 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220181</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>06</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>139.90 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220181</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0005925864</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>139.90 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>139.90</td>
  <td class=3Dxl7418599>139.90 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220182</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>05</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>86.85 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220182</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0102964112</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>86.85 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>86.85</td>
  <td class=3Dxl7418599>86.85 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220183</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>05</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>86.85 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220183</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0102964120</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>86.85 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>86.85</td>
  <td class=3Dxl7418599>86.85 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220184</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>162 / 10002</t=
d>
  <td class=3Dxl6418599>0001600014</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>2,962.80 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0001600014</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>2,962.80 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>2,962.80</td>
  <td class=3Dxl7418599>2,962.80 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220185</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>04</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>411.50 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220185</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0102584613</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>411.50 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>411.50</td>
  <td class=3Dxl7418599>411.50 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220186</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>03</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>3,497.50 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220186</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9006371423</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>3,497.50 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>3,497.50</td>
  <td class=3Dxl7418599>3,497.50 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220187</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>05</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,958.60 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220187</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0102713386</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,958.60 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>1,958.60</td>
  <td class=3Dxl7418599>1,958.60 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220188</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>03</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>733.07 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220188</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0000545094</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>733.07 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>733.07</td>
  <td class=3Dxl7418599>733.07 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220189</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>162 / 10002</t=
d>
  <td class=3Dxl6418599>9006371423</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>3,077.80 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9006371423</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>3,077.80 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>3,077.80</td>
  <td class=3Dxl7418599>3,077.80 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220190</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>02</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>521.10 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220190</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0102964138</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>521.10 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>521.10</td>
  <td class=3Dxl7418599>521.10 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220190-A</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>02</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,399.00 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220190-A</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9007059910</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,399.00 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>1,399.00</td>
  <td class=3Dxl7418599>1,399.00 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220191</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>02</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>559.60 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220191</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9008971634</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>559.60 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>559.60</td>
  <td class=3Dxl7418599>559.60 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220192</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>03</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>2,518.20 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220192</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9006371423</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>2,518.20 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>2,518.20</td>
  <td class=3Dxl7418599>2,518.20 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220193</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>162 / 10002</t=
d>
  <td class=3Dxl6418599>9102908648</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,466.07 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9102908648</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,466.07 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>1,466.07</td>
  <td class=3Dxl7418599>1,466.07 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220194</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>05</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,991.66 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220194</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0102534782</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,991.66 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>1,991.66</td>
  <td class=3Dxl7418599>1,991.66 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220194-1</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>05</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,415.56 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220194-1</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0102534782</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,415.56 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>1,415.56</td>
  <td class=3Dxl7418599>1,415.56 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220194-2</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>05</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>921.76 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220194-2</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0102534782</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>921.76 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>921.76</td>
  <td class=3Dxl7418599>921.76 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220196</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>02</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>559.60 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220196</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9008971634</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>559.60 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>559.60</td>
  <td class=3Dxl7418599>559.60 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220197</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>04</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,646.00 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220197</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9006775748</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,646.00 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>1,646.00</td>
  <td class=3Dxl7418599>1,646.00 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220198</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>90</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,119.20 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220198</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0005925880</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,119.20 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>1,119.20</td>
  <td class=3Dxl7418599>1,119.20 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220198-1</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>90</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>559.60 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220198-1</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0005925880</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>559.60 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>559.60</td>
  <td class=3Dxl7418599>559.60 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220199</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>90</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,466.00 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220199</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0102713394</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,466.00 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>1,466.00</td>
  <td class=3Dxl7418599>1,466.00 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220200</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>162 / 10002</t=
d>
  <td class=3Dxl6418599>0102964104</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>521.10 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0102964104</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>521.10 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>521.10</td>
  <td class=3Dxl7418599>521.10 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220201</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>03</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,466.00 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220201</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0102713402</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,466.00 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>1,466.00</td>
  <td class=3Dxl7418599>1,466.00 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220202</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>02</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,538.95 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220202</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9006371431</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,538.95 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>1,538.95</td>
  <td class=3Dxl7418599>1,538.95 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220203</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>162 / 10002</t=
d>
  <td class=3Dxl6418599>9008196901</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>764.28 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9008196901</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>764.28 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>764.28</td>
  <td class=3Dxl7418599>764.28 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220204</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>06</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>615.42 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220204</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0006639902</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>615.42 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>615.42</td>
  <td class=3Dxl7418599>615.42 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220205</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>06</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,119.20 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220205</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0006639894</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,119.20 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>1,119.20</td>
  <td class=3Dxl7418599>1,119.20 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220206</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>162 / 10002</t=
d>
  <td class=3Dxl6418599>0102584605</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>2,962.87 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0102584605</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>2,962.87 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>2,962.87</td>
  <td class=3Dxl7418599>2,962.87 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220207</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>02</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,678.80 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220207</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9008971634</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,678.80 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>1,678.80</td>
  <td class=3Dxl7418599>1,678.80 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220208</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>02</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>839.40 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220208</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9008971634</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>839.40 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>839.40</td>
  <td class=3Dxl7418599>839.40 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220209</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>03</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>22,384.01 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220209</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9007059902</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>22,384.01 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>22,384.01</td>
  <td class=3Dxl7418599>22,384.01 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220210</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>05</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>3,077.80 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220210</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0102713386</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>3,077.80 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>3,077.80</td>
  <td class=3Dxl7418599>3,077.80 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220211</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>05</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,399.00 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220211</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0005925906</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,399.00 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>1,399.00</td>
  <td class=3Dxl7418599>1,399.00 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220212</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>03</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>2,518.20 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220212</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9006371423</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>2,518.20 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>2,518.20</td>
  <td class=3Dxl7418599>2,518.20 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220213</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>03</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>3,736.42 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220213</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9006775730</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>3,736.42 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>3,736.42</td>
  <td class=3Dxl7418599>3,736.42 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220214</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>162 / 10002</t=
d>
  <td class=3Dxl6418599>0102964104</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,042.20 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0102964104</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,042.20 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>1,042.20</td>
  <td class=3Dxl7418599>1,042.20 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220215</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>162 / 10002</t=
d>
  <td class=3Dxl6418599>0102584605</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>2,304.47 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0102584605</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>2,304.47 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>2,304.47</td>
  <td class=3Dxl7418599>2,304.47 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220216</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>02</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>2,199.00 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220216</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9006775995</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>2,199.00 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>2,199.00</td>
  <td class=3Dxl7418599>2,199.00 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220217</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>05</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>277.92 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220217</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0102964112</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>277.92 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>277.92</td>
  <td class=3Dxl7418599>277.92 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220218</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>05</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>277.92 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220218</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0102964120</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>277.92 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>277.92</td>
  <td class=3Dxl7418599>277.92 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220219</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>162 / 10002</t=
d>
  <td class=3Dxl6418599>0102584597</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>4,342.50 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0102584597</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>4,342.50 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>4,342.50</td>
  <td class=3Dxl7418599>4,342.50 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220220</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>04</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>839.45 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220220</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9006371431</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>839.45 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>839.45</td>
  <td class=3Dxl7418599>839.45 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220221</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>03</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>2,518.20 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220221</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0002382331</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>2,518.20 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>2,518.20</td>
  <td class=3Dxl7418599>2,518.20 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220222</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>90</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>419.70 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220222</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0102584662</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>419.70 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>419.70</td>
  <td class=3Dxl7418599>419.70 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220223</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>05</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,748.75 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220223</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9006371423</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,748.75 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>1,748.75</td>
  <td class=3Dxl7418599>1,748.75 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220224</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>02</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>293.20 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220224</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9006776001</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>293.20 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>293.20</td>
  <td class=3Dxl7418599>293.20 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220226</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>06</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>419.75 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220226</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0102584688</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>419.75 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>419.75</td>
  <td class=3Dxl7418599>419.75 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220227</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>05</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>3,287.65 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220227</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0102584696</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>3,287.65 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>3,287.65</td>
  <td class=3Dxl7418599>3,287.65 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220228</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>02</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>839.40 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220228</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9008971634</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>839.40 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>839.40</td>
  <td class=3Dxl7418599>839.40 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220229</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>02</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>419.70 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220229</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9008971634</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>419.70 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>419.70</td>
  <td class=3Dxl7418599>419.70 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220230</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>05</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,958.60 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220230</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0102713386</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,958.60 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>1,958.60</td>
  <td class=3Dxl7418599>1,958.60 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220231</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>162 / 10002</t=
d>
  <td class=3Dxl6418599>3472297</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,354.86 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>3472297</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,354.86 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>1,354.86</td>
  <td class=3Dxl7418599>1,354.86 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220232</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>03</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>7,134.53 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220232</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9007059894</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>7,134.53 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>7,134.53</td>
  <td class=3Dxl7418599>7,134.53 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220233</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>90</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>3,241.47 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220233</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9006775730</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>3,241.47 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>3,241.47</td>
  <td class=3Dxl7418599>3,241.47 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220234</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>06</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>2,658.10 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220234</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0002382331</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>2,658.10 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>2,658.10</td>
  <td class=3Dxl7418599>2,658.10 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220235</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>06</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,399.00 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220235</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0005925864</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,399.00 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>1,399.00</td>
  <td class=3Dxl7418599>1,399.00 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220236</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>06</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>559.60 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220236</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0006639902</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>559.60 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>559.60</td>
  <td class=3Dxl7418599>559.60 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220237</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>90</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>699.50 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220237</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0102534840</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>699.50 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>699.50</td>
  <td class=3Dxl7418599>699.50 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220238</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>02</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>909.35 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220238</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0103258894</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>909.35 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>909.35</td>
  <td class=3Dxl7418599>909.35 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220239</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>03</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>2,583.07 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220239</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9006775730</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>2,583.07 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>2,583.07</td>
  <td class=3Dxl7418599>2,583.07 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220240</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>03</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>2,089.27 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220240</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9006775730</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>2,089.27 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>2,089.27</td>
  <td class=3Dxl7418599>2,089.27 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220241</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>04</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>658.47 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220241</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0102584605</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>658.47 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>658.47</td>
  <td class=3Dxl7418599>658.47 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220242</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>90</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,595.47 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220242</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9006775730</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,595.47 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>1,595.47</td>
  <td class=3Dxl7418599>1,595.47 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220243</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>90</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>329.27 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220243</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0102584605</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>329.27 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>329.27</td>
  <td class=3Dxl7418599>329.27 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220244</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>162 / 10002</t=
d>
  <td class=3Dxl6418599>0102534774</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>329.21 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0102534774</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>329.21 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>329.21</td>
  <td class=3Dxl7418599>329.21 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220245</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>162 / 10002</t=
d>
  <td class=3Dxl6418599>0002382331</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>2,014.28 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0002382331</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>2,014.28 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>2,014.28</td>
  <td class=3Dxl7418599>2,014.28 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220246</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>02</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,172.80 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220246</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9006775995</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>1,172.80 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>1,172.80</td>
  <td class=3Dxl7418599>1,172.80 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220247</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>02</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>820.96 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220247</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>9006775995</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>820.96 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>820.96</td>
  <td class=3Dxl7418599>820.96 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220248</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>06</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>419.70 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220248</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0006639894</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>419.70 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>419.70</td>
  <td class=3Dxl7418599>419.70 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl6418599>220249</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6718599 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6718599>Criterio 1</td>
  <td class=3Dxl6718599>Criterio 2</td>
  <td class=3Dxl6718599>Criterio<span style=3D'display:none'> 3</span></td>
  <td class=3Dxl6818599>Débito</td>
  <td class=3Dxl6818599>Crédito</td>
  <td class=3Dxl6918599>Obligación</td>
  <td class=3Dxl6718599>Fecha</td>
  <td class=3Dxl6718599>Det<span style=3D'display:none'>alle</span></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>696 / 10001</t=
d>
  <td class=3Dxl6418599>06</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>559.60 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>220249</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7118599 align=3Dcenter>##</td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6418599 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6418599>0006639894</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7018599>559.60 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7218599 colspan=3D2>CONTABILIDAD</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7318599>559.60</td>
  <td class=3Dxl7418599>559.60 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7518599>184,002.10</td>
  <td class=3Dxl7618599>184,002.10 </td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6618599 style=3D'height:12.75pt'>Explicación:</=
td>
  <td class=3Dxl6418599 colspan=3D6>CONTABILIZANDO ANTICIPOS DE COMBUSTIBLE=
 DEL MES
  DE MARZO</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl7718599 style=3D'height:12.75pt'>Confeccionado =
por:</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7718599 colspan=3D2>Aprobado por:</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7718599>Revisado por:</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D21 style=3D'height:15.75pt'>
  <td height=3D21 class=3Dxl7818599 style=3D'height:15.75pt'>Rodas XXI - Co=
ntabilidad</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl7918599>00-00070</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1518599 style=3D'height:12.75pt'></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>Fecha:</td>
  <td class=3Dxl7118599 align=3Dright>14/03/2022</td>
  <td class=3Dxl6618599 colspan=3D2>Comprobante:</td>
  <td class=3Dxl1518599></td>
  <td class=3Dxl6618599>Pág.</td>
  <td class=3Dxl8018599>1</td>
  <td class=3Dxl1518599></td>
 </tr>
 <![if supportMisalignedColumns]>
 <tr height=3D0 style=3D'display:none'>
  <td width=3D179 style=3D'width:134pt'></td>
  <td width=3D79 style=3D'width:59pt'></td>
  <td width=3D87 style=3D'width:65pt'></td>
  <td width=3D40 style=3D'width:30pt'></td>
  <td width=3D91 style=3D'width:68pt'></td>
  <td width=3D75 style=3D'width:56pt'></td>
  <td width=3D79 style=3D'width:59pt'></td>
  <td width=3D60 style=3D'width:45pt'></td>
  <td width=3D24 style=3D'width:18pt'></td>
  <td width=3D80 style=3D'width:60pt'></td>
  <td width=3D80 style=3D'width:60pt'></td>
 </tr>
 <![endif]>
</table>

</div>


<!----------------------------->
<!--FINAL DE LOS RESULTADOS DEL ASISTENTE PARA PUBLICAR COMO PÁGINA WEB DE
EXCEL-->
<!----------------------------->
</body>

</html>

";
        return new Response(json_encode(array('success' => true, 'html' => $_html)));
    }

}