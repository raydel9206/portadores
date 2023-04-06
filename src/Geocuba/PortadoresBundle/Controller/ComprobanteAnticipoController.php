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
}