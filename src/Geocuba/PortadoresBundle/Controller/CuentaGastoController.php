<?php
/**
 * Created by PhpStorm.
 * User: orlando
 * Date: 06/10/2015
 * Time: 9:16
 */


namespace Geocuba\PortadoresBundle\Controller;
use Geocuba\AdminBundle\Util\Util;
use Geocuba\PortadoresBundle\Entity\NcentroCosto;
use Geocuba\PortadoresBundle\Entity\CuentaGasto;
use Doctrine\Common\CommonException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;

class CuentaGastoController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $_no_cuenta = trim($request->get('no_cuenta'));
        $nunidadid = trim($request->get('unidadid'));

        $start = $request->get('start');
        $limit = $request->get('limit');

        $_unidades[0] = $nunidadid;
//        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $entities = $em->getRepository('PortadoresBundle:CuentaGasto')->buscarCuentaGasto($_no_cuenta, $_unidades, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:CuentaGasto')->buscarCuentaGasto($_no_cuenta, $_unidades, $start, $limit, true);

        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'no_cuenta' => $entity->getNoCuenta(),
                'descripcion' => $entity->getDescripcion(),
                'centro_costo_id' => $entity->getCentroCosto()->getId(),
                'centro_costo_codigo' => $entity->getCentroCosto()->getCodigo(),
                'centro_costo_descripcion' => $entity->getCentroCosto()->getNombre(),
                'elemento_gasto_id' => $entity->getElementoGasto()->getId(),
                'elemento_gasto_codigo' => $entity->getElementoGasto()->getCodigo(),
                'elemento_gasto_descripcion' => $entity->getElementoGasto()->getDescripcion(),
                'detalle_gasto_id' => $entity->getDetalleGasto()->getId(),
                'detalle_gasto_codigo' => $entity->getDetalleGasto()->getCodigo(),
                'detalle_gasto_descripcion' => $entity->getDetalleGasto()->getDescripcion(),
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    public function addAction(Request $request)
    {
        $session = $request->getSession();

        $em = $this->getDoctrine()->getManager();
        $no_cuenta = trim($request->get('no_cuenta'));
        $descripcion = trim($request->get('descripcion'));
        $centrocosto_id = $request->get('centrocostoid');
        $elementogasto_id = $request->get('elementogastoid');
        $detallegasto_id = $request->get('detallegastoid');

        $centro_costo = $em->getRepository('PortadoresBundle:CentroCosto')->find($centrocosto_id);
        $unidad = $centro_costo->getNunidadid()->getId();

        $repetido = $em->getRepository('PortadoresBundle:CuentaGasto')->buscarCuentaGastoRepetido($no_cuenta, $unidad);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe una cuenta de gasto con el mismo número registrada en el sistema.'));

        $entity = new CuentaGasto();
        $entity->setNoCuenta($no_cuenta);
        $entity->setDescripcion($descripcion);
        $entity->setCentroCosto($centro_costo);
        $entity->setElementoGasto($em->getRepository('PortadoresBundle:ElementoGasto')->find($elementogasto_id));
        $entity->setDetalleGasto($em->getRepository('PortadoresBundle:DetalleGasto')->find($detallegasto_id));
        $entity->setVisible(true);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Cuenta de gasto adicionada con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function modAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = $request->get('id');
        $no_cuenta = trim($request->get('no_cuenta'));
        $descripcion = trim($request->get('descripcion'));
        $centrocosto_id = $request->get('centrocostoid');
        $elementogasto_id = $request->get('elementogastoid');
        $detallegasto_id = $request->get('detallegastoid');

        $centro_costo = $em->getRepository('PortadoresBundle:CentroCosto')->find($centrocosto_id);
        $unidad = $centro_costo->getNunidadid()->getId();

        $repetido = $em->getRepository('PortadoresBundle:CuentaGasto')->buscarCuentaGastoRepetido($no_cuenta, $unidad, $id);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe una cuenta de gasto con el mismo número de cuenta en la unidad seleccionada.'));

        $entity = $em->getRepository('PortadoresBundle:CuentaGasto')->find($id);
        $entity->setNoCuenta($no_cuenta);
        $entity->setDescripcion($descripcion);
        $entity->setCentroCosto($centro_costo);
        $entity->setElementoGasto($em->getRepository('PortadoresBundle:ElementoGasto')->find($elementogasto_id));
        $entity->setDetalleGasto($em->getRepository('PortadoresBundle:DetalleGasto')->find($detallegasto_id));
        try {
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Cuenta de gasto modificada con éxito.'));
            return $response;
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function delAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = $request->get('id');
        $entity = $em->getRepository('PortadoresBundle:CuentaGasto')->find($id);
        $entity->setVisible(false);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Cuenta de gasto eliminada con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }
}