<?php
/**
 * Created by PhpStorm.
 * User: Mire
 * Date: 16/04/2020
 * Time: 9:52
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

class ComprobanteRecargaController extends Controller
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

        $calificadorCuentaDebito = $em->getRepository('PortadoresBundle:Clasificador')->findOneBy(array('codigo' => 'TMCC'));
        $calificadorCuentaCredito = $em->getRepository('PortadoresBundle:Clasificador')->findOneBy(array('codigo' => 'PADO'));
        if (!$calificadorCuentaDebito || !$calificadorCuentaCredito) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Aún no han sido agregados los clasificadores de cuenta'));
        }

        $cuentaDebito = $em->getRepository('PortadoresBundle:Cuenta')->findOneBy(array('clasificador' => $calificadorCuentaDebito));
        $cuentaCredito = $em->getRepository('PortadoresBundle:Cuenta')->findOneBy(array('clasificador' => $calificadorCuentaCredito));

        if (!$cuentaDebito || !$cuentaCredito) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Algunas cuentas no han sido agregadas'));
        }

        $subcuentaDebito = $em->getRepository('PortadoresBundle:SubCuenta')->findOneBy(array('cuenta' => $cuentaDebito->getId(), 'moneda' => $moneda));
        $subcuentaCredito = $em->getRepository('PortadoresBundle:SubCuenta')->findOneBy(array('cuenta' => $cuentaCredito->getId(), 'moneda' => $moneda));

        $nro_subcuentaDebito = isset($subcuentaDebito) ? $subcuentaDebito->getNroSubcuenta() : null;
        $nro_subcuentaCredito = isset($subcuentaCredito) ? $subcuentaCredito->getNroSubcuenta() : null;


        $sql = "select t.nro_tarjeta as nro_tarjeta, m.nombre as moneda, hrc.monto_recarga as monto_recarga
                from datos.historial_contable_recarga as hrc
                inner join nomencladores.tarjeta as t on hrc.id_tarjeta = t.id
                inner join nomencladores.unidad as u on u.id = t.nunidadid
                inner join nomencladores.moneda as m on t.nmonedaid = m.id where m.id = '$moneda' and u.id ='$unidadid' 
                and extract( year from hrc.fecha) = $anno and extract(month from hrc.fecha) = $mes";

        $total = $this->getDoctrine()->getConnection()->fetchAll($sql);
        $sum = 0;
        foreach ($total as $item) {
            $sum += $item['monto_recarga'];
            $data[] = array(
                'cuenta' => $cuentaDebito->getNroCuenta() . ' / ' . $nro_subcuentaDebito,
                'tarjeta' => $item['nro_tarjeta'],
                'debito' => round($item['monto_recarga'], 2),
                'credito' => '',
            );

            $data[] = array(
                'cuenta' => $cuentaCredito->getNroCuenta() . ' / ' . $nro_subcuentaCredito,
                'tarjeta' => '',
                'debito' => '',
                'credito' => round($item['monto_recarga'], 2),
            );
        }

        return new JsonResponse(array('rows' => $data));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function exportAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $_html = "MIME-Version: 1.0
X-Document-Type: Worksheet
Content-Location: file:///C:/6069C672/Copiadecombustibledeenero2022.htm
Content-Transfer-Encoding: quoted-printable
Content-Type: text/html; charset=\"windows-1252\"

<html xmlns:o=3D\"urn:schemas-microsoft-com:office:office\"
xmlns:x=3D\"urn:schemas-microsoft-com:office:excel\"
xmlns=3D\"http://www.w3.org/TR/REC-html40\">

<head>
<meta http-equiv=3DContent-Type content=3D\"text/html; charset=3Dwindows-125=
2\">
<meta name=3DProgId content=3DExcel.Sheet>
<meta name=3DGenerator content=3D\"Microsoft Excel 15\">
<link rel=3DFile-List href=3D\"Copiadecombustibledeenero2022_archivos/fileli=
st.xml\">
<style id=3D\"Copia de combustible de enero 2022_26898_Styles\">
<!--table
	{mso-displayed-decimal-separator:\"\,\";
	mso-displayed-thousand-separator:\"\.\";}
.xl1526898
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
.xl6526898
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
.xl6626898
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
.xl6726898
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
.xl6826898
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
.xl6926898
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
.xl7026898
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
.xl7126898
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
.xl7226898
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
.xl7326898
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
.xl7426898
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
.xl7526898
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
.xl7626898
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
.xl7726898
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
.xl7826898
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
	background:yellow;
	mso-pattern:black none;
	white-space:nowrap;}
.xl7926898
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
	background:yellow;
	mso-pattern:black none;
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

<div id=3D\"Copia de combustible de enero 2022_26898\" align=3Dcenter
x:publishsource=3D\"Excel\">

<table border=3D0 cellpadding=3D0 cellspacing=3D0 width=3D880 style=3D'bord=
er-collapse:
 collapse;table-layout:fixed;width:660pt'>
 <col width=3D80 span=3D7 style=3D'width:60pt'>
 <col width=3D80 style=3D'width:60pt'>
 <col width=3D80 span=3D3 style=3D'width:60pt'>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6626898 width=3D80 style=3D'height:12.75pt;widt=
h:60pt'>Comprobante:</td>
  <td class=3Dxl1526898 width=3D80 style=3D'width:60pt'></td>
  <td class=3Dxl6526898 width=3D80 style=3D'width:60pt'>00-00003</td>
  <td class=3Dxl1526898 width=3D80 style=3D'width:60pt'></td>
  <td class=3Dxl1526898 width=3D80 style=3D'width:60pt'></td>
  <td class=3Dxl1526898 width=3D80 style=3D'width:60pt'></td>
  <td class=3Dxl1526898 width=3D80 style=3D'width:60pt'></td>
  <td class=3Dxl1526898 width=3D80 style=3D'width:60pt'></td>
  <td class=3Dxl1526898 width=3D80 style=3D'width:60pt'></td>
  <td class=3Dxl1526898 width=3D80 style=3D'width:60pt'></td>
  <td class=3Dxl1526898 width=3D80 style=3D'width:60pt'></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6626898 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl7826898>0703/011P</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6726898 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6726898>Criterio 1</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl6726898>Criterio 2</td>
  <td class=3Dxl6726898>Criterio 3</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl6826898>Débito</td>
  <td class=3Dxl6826898>Crédito</td>
  <td class=3Dxl6926898>Obligación</td>
  <td class=3Dxl6726898>Fecha</td>
  <td class=3Dxl6726898>Detalle</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1526898 style=3D'height:12.75pt'></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6526898 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6526898>0008069496</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl7026898>329,20 </td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6526898 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6526898>0008658207</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl7026898>823,00 </td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6526898 style=3D'height:12.75pt'>405 / 10040</t=
d>
  <td class=3Dxl6526898>31400800070</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl7026898>1.152,20 </td>
  <td class=3Dxl7926898>0703/011P</td>
  <td class=3Dxl7126898 align=3Dright>31/01/2022</td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1526898 style=3D'height:12.75pt'></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl7226898>1.152,20 </td>
  <td class=3Dxl7226898>1.152,20 </td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1526898 style=3D'height:12.75pt'></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6626898 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl7826898>0706-003H</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6726898 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6726898>Criterio 1</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl6726898>Criterio 2</td>
  <td class=3Dxl6726898>Criterio 3</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl6826898>Débito</td>
  <td class=3Dxl6826898>Crédito</td>
  <td class=3Dxl6926898>Obligación</td>
  <td class=3Dxl6726898>Fecha</td>
  <td class=3Dxl6726898>Detalle</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1526898 style=3D'height:12.75pt'></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6526898 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6526898>0008658165</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl7026898>2.469,00 </td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6526898 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6526898>0008658207</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl7026898>1.777,60 </td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6526898 style=3D'height:12.75pt'>405 / 10040</t=
d>
  <td class=3Dxl6526898>31400800070</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl7026898>4.246,60 </td>
  <td class=3Dxl7926898>0706-003H</td>
  <td class=3Dxl7126898 align=3Dright>31/01/2022</td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1526898 style=3D'height:12.75pt'></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl7226898>4.246,60 </td>
  <td class=3Dxl7226898>4.246,60 </td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1526898 style=3D'height:12.75pt'></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6626898 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl7826898>DIESEL</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6726898 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6726898>Criterio 1</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl6726898>Criterio 2</td>
  <td class=3Dxl6726898>Criterio 3</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl6826898>Débito</td>
  <td class=3Dxl6826898>Crédito</td>
  <td class=3Dxl6926898>Obligación</td>
  <td class=3Dxl6726898>Fecha</td>
  <td class=3Dxl6726898>Detalle</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1526898 style=3D'height:12.75pt'></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6526898 style=3D'height:12.75pt'>162 / 10002</t=
d>
  <td class=3Dxl6526898>0008069496</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl7026898>321,77 </td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6526898 style=3D'height:12.75pt'>731 / 10001</t=
d>
  <td class=3Dxl6526898>0112</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl6526898>3002021</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl7026898>321,77 </td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6526898 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6526898>0008069496</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl7026898>321,77 </td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6526898 style=3D'height:12.75pt'>162 / 10002</t=
d>
  <td class=3Dxl6526898>0008069496</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl7026898>321,77 </td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1526898 style=3D'height:12.75pt'></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl7226898>643,54 </td>
  <td class=3Dxl7226898>643,54 </td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1526898 style=3D'height:12.75pt'></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6626898 style=3D'height:12.75pt'>Documento:</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl7826898>GAS REGULA</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6726898 style=3D'height:12.75pt'>Cuenta</td>
  <td class=3Dxl6726898>Criterio 1</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl6726898>Criterio 2</td>
  <td class=3Dxl6726898>Criterio 3</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl6826898>Débito</td>
  <td class=3Dxl6826898>Crédito</td>
  <td class=3Dxl6926898>Obligación</td>
  <td class=3Dxl6726898>Fecha</td>
  <td class=3Dxl6726898>Detalle</td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1526898 style=3D'height:12.75pt'></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6526898 style=3D'height:12.75pt'>162 / 10002</t=
d>
  <td class=3Dxl6526898>0008658207</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl7026898>2.600,60 </td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6526898 style=3D'height:12.75pt'>162 / 10002</t=
d>
  <td class=3Dxl6526898>0008658165</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl7026898>2.469,00 </td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6526898 style=3D'height:12.75pt'>731 / 10001</t=
d>
  <td class=3Dxl6526898>0112</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl6526898>3002011</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl7026898>5.069,60 </td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6526898 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6526898>0008658207</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl7026898>2.600,60 </td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6526898 style=3D'height:12.75pt'>106 / 10001</t=
d>
  <td class=3Dxl6526898>0008658165</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl7026898>2.469,00 </td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6526898 style=3D'height:12.75pt'>162 / 10002</t=
d>
  <td class=3Dxl6526898>0008658207</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl7026898>2.600,60 </td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6526898 style=3D'height:12.75pt'>162 / 10002</t=
d>
  <td class=3Dxl6526898>0008658165</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl7026898>2.469,00 </td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1526898 style=3D'height:12.75pt'></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl7226898>10.139,20 </td>
  <td class=3Dxl7226898>10.139,20 </td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1526898 style=3D'height:12.75pt'></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1526898 style=3D'height:12.75pt'></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1526898 style=3D'height:12.75pt'></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl7326898>16.181,54 </td>
  <td class=3Dxl7326898>16.181,54 </td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1526898 style=3D'height:12.75pt'></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1526898 style=3D'height:12.75pt'></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl6626898 style=3D'height:12.75pt'>Explicación:</=
td>
  <td class=3Dxl6526898 colspan=3D7>CONTABILIZANDO CONSUMO DE COMBUSTIBLE
  CORRESPONDIENTE AL MES DE ENERO 2022<span style=3D'mso-spacerun:yes'>  </=
span></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1526898 style=3D'height:12.75pt'></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl7426898 colspan=3D2 style=3D'height:12.75pt'>Co=
nfeccionado
  por:</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl7426898 colspan=3D2>Aprobado por:</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl7426898>Revisado por:</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D21 style=3D'height:15.75pt'>
  <td height=3D21 class=3Dxl7526898 colspan=3D3 style=3D'height:15.75pt'>Ro=
das XXI -
  Contabilidad</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl7626898>00-00003</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
 </tr>
 <tr height=3D17 style=3D'height:12.75pt'>
  <td height=3D17 class=3Dxl1526898 style=3D'height:12.75pt'></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl6626898>Fecha:</td>
  <td class=3Dxl7126898 align=3Dright>01/02/2022</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl6626898>Comprobante:</td>
  <td class=3Dxl1526898></td>
  <td class=3Dxl6626898>Pág.</td>
  <td class=3Dxl7726898>1</td>
 </tr>
 <![if supportMisalignedColumns]>
 <tr height=3D0 style=3D'display:none'>
  <td width=3D80 style=3D'width:60pt'></td>
  <td width=3D80 style=3D'width:60pt'></td>
  <td width=3D80 style=3D'width:60pt'></td>
  <td width=3D80 style=3D'width:60pt'></td>
  <td width=3D80 style=3D'width:60pt'></td>
  <td width=3D80 style=3D'width:60pt'></td>
  <td width=3D80 style=3D'width:60pt'></td>
  <td width=3D80 style=3D'width:60pt'></td>
  <td width=3D80 style=3D'width:60pt'></td>
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