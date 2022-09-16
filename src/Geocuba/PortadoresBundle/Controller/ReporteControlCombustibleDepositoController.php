<?php
/**
 * Created by PhpStorm.
 * User: pfcadenas
 * Date: 31/08/2016
 * Time: 9:58
 */

namespace Geocuba\PortadoresBundle\Controller;

use function DeepCopy\deep_copy;
use Doctrine\Common\Util\Debug;
use Geocuba\AdminBundle\Util\Util;
use Geocuba\PortadoresBundle\Entity\Anticipo;
use Geocuba\PortadoresBundle\Entity\HistorialTarjeta;
use Geocuba\PortadoresBundle\Entity\Tarjeta;
use Geocuba\PortadoresBundle\Util\FechaUtil;
use Geocuba\PortadoresBundle\Util\Utiles;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Geocuba\PortadoresBundle\Util\DocumentosEnum;

use Doctrine\Common\CommonException;


class ReporteControlCombustibleDepositoController extends Controller
{

    use ViewActionTrait;

    /**
     * @param Request $request
     * @return array|JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = trim($request->get('tarjeta'));

        if (is_null($id) || $id == '') {
            return new JsonResponse(array('rows' => array(), 'total' => 0));
        }
        $export = $request->get('export');
        $anno = $request->get('anno');
        $mes = $request->get('mes');


        /**@var Tarjeta $tarjeta */
        $tarjeta = $em->getRepository('PortadoresBundle:Tarjeta')->findOneBy(array('id' => $id));
        $numero = $tarjeta->getNroTarjeta();
        $_data = array();
        /**@var HistorialTarjeta $historial */
        $historial = $em->getRepository('PortadoresBundle:HistorialTarjeta')->findBy(array('tarjetaid' => $id, 'mes' => $mes, 'anno' => $anno), array('fecha' => 'ASC'));

        if (!$historial) {
            $_data[] = array(
                'anticipo' => '',
                'vale' => '',
                'fecha' => '',
                'entrada_litros' => '',
                'entrada_importe' => '',
                'salida_litros' => '',
                'salida_importe' => '',
                'existencia_importe' => $tarjeta->getImporte(),
                'existencia_litros' => round($tarjeta->getImporte() / $tarjeta->getTipoCombustibleid()->getPrecio(), 2),
                'chapa' => '',
                'combustible' => $tarjeta->getTipoCombustibleid()->getNombre(),
                'centro_costo' => $tarjeta->getCentrocosto()->getNombre());
        } else {
            foreach ($historial as $entity) {
                /**@var HistorialTarjeta $entity */
                $chapa = '';
                $anticipo = '';
                if (null !== $entity->getLiquidacionid()) {
                    $anticipo = $em->getRepository('PortadoresBundle:Anticipo')->findOneBy(array('id' => $entity->getLiquidacionid()->getAnticipo()))->getNoVale();
                    $chapa = $em->getRepository('PortadoresBundle:Vehiculo')->findOneBy(array('id' => $entity->getLiquidacionid()->getVehiculoid()))->getMatricula();
                }
                /**@var Anticipo $anticipo */
                $_data[] = array(
                    'anticipo' => $anticipo,
                    'vale' => $entity->getNroVale(),
                    'fecha' => $entity->getFecha()->format('d/m/Y'),
                    'entrada_litros' => $entity->getEntradaCantidad(),
                    'entrada_importe' => $entity->getEntradaImporte(),
                    'salida_litros' => $entity->getSalidaCantidad(),
                    'salida_importe' => $entity->getSalidaImporte(),
                    'existencia_litros' => $entity->getExistenciaCantidad(),
                    'existencia_importe' => round($entity->getExistenciaImporte(), 2),
                    'chapa' => $chapa,
                    'combustible' => $entity->getTarjetaid()->getTipoCombustibleid()->getNombre(),
                    'centro_costo' => $entity->getTarjetaid()->getCentrocosto()->getNombre(),
                );
            }
        }

        if ($export != true) {
            $sql = "select count(*)
                    from datos.historial_tarjeta as ht
                       left join datos.liquidacion as lq on ht.liquidacionid = lq.id
       left join datos.anticipo as ant on ant.id = lq.anticipo
       inner join nomencladores.tarjeta as t on ht.tarjetaid = t.id
       inner join nomencladores.tipo_combustible as tc on t.ntipo_combustibleid = tc.id
       left join nomencladores.centro_costo as cc on lq.ncentrocostoid = cc.id
       left join nomencladores.vehiculo as v on lq.nvehiculoid = v.id
                    where
                    date_part('month',ht.fecha) = $mes and date_part('year',ht.fecha)=$anno and t.nro_tarjeta = '$numero'";

            $total = $this->getDoctrine()->getConnection()->fetchAll($sql);

        } else {
            return $_data;
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total[0]['count']));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loadTarjetaAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $unidadid = $request->get('unidadid');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($unidadid), $_unidades);

        $entities = $em->getRepository('PortadoresBundle:Tarjeta')->buscarTarjetaCombo(null, $_unidades);
        $_data = array();
        /** @var Tarjeta $entity */
        foreach ($entities as $entity) {
            $_data[] = array(
                'nro_tarjeta' => $entity['nro_tarjeta'],
                'id' => $entity['id']
            );
        }

        return new JsonResponse(array('rows' => $_data));
    }

    public function printAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $datos = $this->loadAction($request);
        $tarjeta = trim($request->get('tarjeta'));

        $entities = $em->getRepository('PortadoresBundle:Tarjeta')->findOneBy(array('id' => $tarjeta));
        $numero = $entities->getNroTarjeta();

        $anno = $request->get('anno');
        $mes = $request->get('mes');
        $mesNombre = FechaUtil::getNombreMes($mes);

        $_html = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
        <html xmlns='http://www.w3.org/1999/xhtml'>
        <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <title>Control Combustible por Dep&oacute;sitos</title>
        <style>
            table {
                border:0 solid;
                border-radius:0;
                font-family: 'Arial', serif;
                font-size: 11px;
                border-collapse: collapse;
            }
            td{             
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
            <td colspan='12' style='text-align: left;border: none; padding: 0;'><!--<img src='../../assets/img/PNG/logo.png' width='166' height='90'>--></td>
         </tr>
         <tr>
            <td colspan='12' style='text-align: center;border: none; font-size:14px;'><strong>Control Combustible por Dep&oacute;sitos</strong></td>
         </tr>

         <tr>
            <td colspan='5' style='text-align: center;border: none;font-size: 12px;'><strong>Año:</strong><strong>$anno</strong></td>
            <td colspan='3' style='text-align: center;border: none;font-size: 12px;'><strong>Mes:</strong><strong>$mesNombre</strong></td>
            <td colspan='4' style='text-align: center;border: none;font-size: 12px;'><strong>Tarjeta:</strong><strong>$numero</strong></td>
         </tr>

         <tr>
            <td style='text-align: center; width: 10%;'><strong>Anticipo</strong></td>
            <td style='text-align: center; width: 4%;'><strong>Vale</strong></td>
            <td style='text-align: center; width: 7%;'><strong>Fecha</strong></td>
            <td style='text-align: center; width: 7%'><strong>Entrada (Litros)</strong></td>
            <td style='text-align: center; width: 7%'><strong>Entrada Importe</strong></td>
            <td style='text-align: center; width: 7%'><strong>Salida (Litros)</strong></td>
            <td style='text-align: center; width: 7%'><strong>Salida Importe</strong></td>
            <td style='text-align: center; width: 8%'><strong>Existencia Cantidad</strong></td>
            <td style='text-align: center; width: 8%'><strong>Existencia Importe</strong></td>
            <td style='text-align: center; width: 10%'><strong>Chapa</strong></td>
            <td style='text-align: center; width: 10%'><strong>Combustible</strong></td>
            <td style='text-align: center; width: 15%'><strong>Centro Costo</strong></td>
         </tr>";

        foreach ($datos as $entity) {
            $_html .= "<tr>
                <td style='text-align: center;'>" . $entity['anticipo'] . "</td>
                <td style='text-align: center;'>" . $entity['vale'] . "</td>
                <td style='text-align: center;'>" . $entity['fecha'] . "</td>
                <td style='text-align: right;'>" . $entity['entrada_litros'] . "</td>
                <td style='text-align: right;'>" . $entity['entrada_importe'] . "</td>
                <td style='text-align: right;'>" . $entity['salida_litros'] . "</td>
                <td style='text-align: right;'>" . $entity['salida_importe'] . "</td>
                <td style='text-align: right;'>" . $entity['existencia_litros'] . "</td>
                <td style='text-align: right;'>" . $entity['existencia_importe'] . "</td>
                <td style='text-align: center;'>" . $entity['chapa'] . "</td>
                <td style='text-align: center;'>" . $entity['combustible'] . "</td>
                <td style='text-align: left;'>" . $entity['centro_costo'] . "</td>
            </tr>";
        }
        $_html .= "
        </table>
        ";

        $pieFirma = $this->get('portadores.piefirma')->getPieFirmaDistribucion(DocumentosEnum::controlCombustibleDepositos, $request->get('unidadid'));
        $_html .= "
        <br>
        <br>
        
        $pieFirma";

        $_html .= "
        </body>
        </html>";

        return new Response(json_encode(array('success' => true, 'html' => $_html)));
    }
}