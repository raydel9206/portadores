<?php
/**
 * Created by PhpStorm.
 * User: Mire
 * Date: 16/04/2020
 * Time: 10:13
 */

namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\AdminBundle\Util\Util;
use Geocuba\AdminBundle\Util\FechaUtil;
use Geocuba\PortadoresBundle\Entity\Asignacion;
use Geocuba\PortadoresBundle\Entity\Cuenta;
use Geocuba\PortadoresBundle\Entity\PlanificacionCombustible;
use Geocuba\PortadoresBundle\Entity\PlanificacionCombustibleCuc;
use Geocuba\PortadoresBundle\Entity\SubCuenta;
use Geocuba\PortadoresBundle\Util\Datos;
use Geocuba\PortadoresBundle\Util\DocumentosEnum;
use Doctrine\Common\CommonException;
use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;
use Doctrine\Common\Util\Debug;


class CuentaController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $_no_cuenta = trim($request->get('no_cuenta'));
//        $nunidadid = trim($request->get('unidadid'));

        $start = $request->get('start');
        $limit = $request->get('limit');

//        $_unidades[0] = $nunidadid;
        $entities = $em->getRepository('PortadoresBundle:Cuenta')->buscarCuenta($_no_cuenta, null, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:Cuenta')->buscarCuenta($_no_cuenta, null, $start, $limit, true);

        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'no_cuenta' => $entity->getNroCuenta(),
                'descripcion' => $entity->getDescripcion(),
                'clasificador' => $entity->getClasificador()->getId(),
                'clasificador_nombre' => $entity->getClasificador()->getNombre(),
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $no_cuenta = trim($request->get('no_cuenta'));
        $descripcion = trim($request->get('descripcion'));
        $clasificador = trim($request->get('clasificador'));
//        $unidad = $request->get('unidad');

        $repetido = $em->getRepository('PortadoresBundle:Cuenta')->buscarCuentaRepetido($no_cuenta, null);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe una cuenta con el mismo número registrada en el sistema.'));

        $entity = new Cuenta();
        $entity->setNroCuenta($no_cuenta);
        $entity->setDescripcion($descripcion);
//        $entity->setUnidad($em->getRepository('PortadoresBundle:Unidad')->find($unidad));
        $entity->setClasificador($em->getRepository('PortadoresBundle:Clasificador')->find($clasificador));
        $entity->setVisible(true);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Cuenta adicionada con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function modAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = $request->get('id');
        $no_cuenta = trim($request->get('no_cuenta'));
        $descripcion = trim($request->get('descripcion'));
        $clasificador = trim($request->get('clasificador'));
//        $unidad = $request->get('unidad');

        $repetido = $em->getRepository('PortadoresBundle:Cuenta')->buscarCuentaRepetido($no_cuenta, null, $id);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe una cuenta con el mismo número de cuenta en la unidad seleccionada.'));

        $entity = $em->getRepository('PortadoresBundle:Cuenta')->find($id);
        $entity->setNroCuenta($no_cuenta);
        $entity->setDescripcion($descripcion);
        $entity->setClasificador($em->getRepository('PortadoresBundle:Clasificador')->find($clasificador));
        $entity->setVisible(true);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Cuenta modificada con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function delAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = $request->get('id');
        $entity = $em->getRepository('PortadoresBundle:Cuenta')->find($id);
        $entity->setVisible(false);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Cuenta eliminada con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function loadSubAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $cuenta = trim($request->get('cuenta'));

        $start = $request->get('start');
        $limit = $request->get('limit');

        $entities = $em->getRepository('PortadoresBundle:SubCuenta')->buscarSubCuenta($cuenta, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:SubCuenta')->buscarSubCuenta($cuenta, $start, $limit, true);

        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'no_cuenta' => $entity->getNroSubcuenta(),
                'moneda_id' => $entity->getMoneda()->getId(),
                'moneda' => $entity->getMoneda()->getNombre(),
                'cuenta_id'=> $entity->getMoneda()->getId(),
                'cuenta'=> $entity->getCuenta()->getNroCuenta(),
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    public function addSubAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $subcuenta = trim($request->get('no_cuenta'));
        $moneda = $request->get('moneda_id');
        $cuenta = $request->get('cuenta');


        $repetido = $em->getRepository('PortadoresBundle:SubCuenta')->buscarSubCuentaRepetido($subcuenta, $cuenta);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe una subcuenta con el mismo número registrada en el sistema.'));


        $entity = new SubCuenta();
        $entity->setCuenta($em->getRepository('PortadoresBundle:Cuenta')->find($cuenta));
        $entity->setMoneda($em->getRepository('PortadoresBundle:Moneda')->find($moneda));
        $entity->setNroSubcuenta($subcuenta);
        $entity->setVisible(true);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Subcuenta adicionada con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function modSubAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = $request->get('id');
        $subcuenta = trim($request->get('no_cuenta'));
        $moneda = $request->get('moneda_id');
        $cuenta = $request->get('cuenta');

        $repetido = $em->getRepository('PortadoresBundle:Cuenta')->buscarCuentaRepetido($subcuenta, $cuenta, $id);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe una subcuenta con el mismo número en la unidad seleccionada.'));

        $entity = $em->getRepository('PortadoresBundle:SubCuenta')->find($id);
        $entity->setMoneda($em->getRepository('PortadoresBundle:Moneda')->find($moneda));
        $entity->setNroSubcuenta($subcuenta);
        $entity->setVisible(true);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Subcuenta modificada con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function delSubAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = $request->get('id');
        $entity = $em->getRepository('PortadoresBundle:SubCuenta')->find($id);
        $entity->setVisible(false);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Cuenta eliminada con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }
}