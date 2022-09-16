<?php
/**
 * Created by PhpStorm.
 * User: orlando
 * Date: 06/01/2017
 * Time: 10:23
 */


namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\AdminBundle\Util\FechaUtil;
use Geocuba\AdminBundle\Util\Util;
use Doctrine\Common\Util\Debug;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;


class ReporteTarjetasPostController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $nunidadid = $request->get('nunidadid');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);
        
        $tarjetas = json_decode($request->get('tarjetas'));


        $mes = $request->get('mes');
        $anno = $request->get('anno');
        $total = sizeof($tarjetas);


        $_data = array();
        foreach ($tarjetas as $tarjeta){
            $tarjeta_con_movimiento = $em->getRepository('PortadoresBundle:Tarjeta')->findByNroTarjeta($tarjeta);
            $tarjetaid = $tarjeta_con_movimiento[0]->getId();
            $entity = $em->getRepository('PortadoresBundle:HistorialTarjeta')->postMensual($tarjetaid, $mes, $anno);
            $ultimo = count($entity);
            if ($entity) {
                $_data[] = array(
                    'id' => $tarjeta_con_movimiento[0]->getId(),
                    'nro_tarjeta' => floatval($tarjeta_con_movimiento[0]->getNroTarjeta()),
                    'tipo' => $tarjeta_con_movimiento[0]->getMonedaid()->getNombre(),
                    'importe' => $entity[$ultimo - 1]->getExistenciaImporte(),
                    'caja' => $tarjeta_con_movimiento[0]->getCajaid()->getNombre(),
                    'tipo_combustible' => $tarjeta_con_movimiento[0]->getTipoCombustibleid()->getNombre()
                );
            }else{
                $tarjeta_sin_movimiento = $em->getRepository('PortadoresBundle:Tarjeta')->buscarTarjetaConFechaPost($tarjeta, $_unidades);
                if($tarjeta_sin_movimiento){
                    $_data[] = array(
                        'id' => $tarjeta_sin_movimiento->getId(),
                        'nro_tarjeta' => floatval($tarjeta_sin_movimiento->getNroTarjeta()),
                        'tipo' => $tarjeta_sin_movimiento->getMonedaid()->getNombre(),
                        'importe' => $tarjeta_sin_movimiento->getImporte(),
                        'caja' => $tarjeta_sin_movimiento->getCajaid()->getNombre(),
                        'tipo_combustible' => $tarjeta_sin_movimiento->getTipoCombustibleid()->getNombre()
                    );}
            }
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    public function printAction(Request $request)
    {
        $data = json_decode($request->get('store'));
        $anno = $request->get('anno');
        $mesNombre = FechaUtil::getNombreMes($request->get('mes'));

        $_html = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
        <html xmlns='http://www.w3.org/1999/xhtml'>
        <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <title>Pase de las Tarjetas por el POST</title>
        <style>
            table {
                border:0 solid;
                border-radius:0;
                font-family: 'Arial', serif;
                font-size: 11px;
                border-collapse: collapse;
            }
            
        </style>
        </head>

        <body>
        <header>
            <img  src='../../assets/img/PNG/logo.png' height='60px' width='160px'>
        </header>
        <table cellspacing='0' cellpadding='5' border='1' width='100%'>

            <td colspan='5' style='text-align: center;border: none;font-size: 14px;'><strong>Pase de las Tarjetas por el POST</strong></td>
         </tr>
         <tr>
            <td colspan='5' style='text-align: left;border: none;font-size: 12px;'><strong>" . $mesNombre . " " . $anno . "</strong></td>
         </tr>

         <tr>
            <td width='25%' style='text-align: center;'><strong>Nro Tarjeta</strong></td>
            <td width='10%' style='text-align: center;'><strong>Tipo</strong></td>
            <td width='10%' style='text-align: center;'><strong>Importe</strong></td>
            <td width='25%' style='text-align: center;'><strong>Caja</strong></td>
            <td width='30%' style='text-align: center;'><strong>Tipo de Combustible</strong></td>
         </tr>";

        for ($i = 0; $i < count($data); $i++) {
            $_html .= "<tr>
                <td width='25%' style='text-align: center;'>" . $data[$i]->nro_tarjeta . "</td>
                <td width='10%' style='text-align: center;'>" . $data[$i]->tipo . "</td>
                <td width='10%' style='text-align: right;'>" . $data[$i]->importe . "</td>
                <td  width='25%' style='text-align: center;'>" . $data[$i]->caja . "</td>
                <td width='30%' style='text-align: center;'>" . $data[$i]->tipo_combustible . "</td>
            </tr>";
        }

        $_html .= "
            <tr>
                <td colspan='5' style='text-align: center;border: none;'></td>
            </tr>
            <tr>
                <td colspan='5' style='text-align: center;border: none;'></td>
            </tr>
            <br>
            <tr>
                <td colspan='2' style='text-align: center;border: none;'>Entrega: _______________________</td>
                <td colspan='1' style='text-align: right;border: none;'></td>
                <td colspan='1' style='text-align: center;border: none;'>Recibe:  ____________________________</td>
                <td colspan='1' style='text-align: center;border: none;'></td>
            </tr>";

        $_html .= "</table>
                </body>
            </html>";

        return new Response(json_encode(array('success' => true, 'html' => $_html)));
    }


}