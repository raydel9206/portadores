<?php

namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\PortadoresBundle\Entity\DenominacionVehiculo;
use Doctrine\Common\CommonException;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\PortadoresBundle\Util\Utiles;

class DenominacionVehiculoController extends Controller
{
    use ViewActionTrait;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $_nombre = trim($request->get('nombre'));

        $start = $request->get('start');
        $limit = $request->get('limit');

        $entities = $em->getRepository('PortadoresBundle:DenominacionVehiculo')->buscarDenominacionVehiculo($_nombre, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:DenominacionVehiculo')->buscarDenominacionVehiculo($_nombre, $start, $limit, true);

        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre(),
                'orden' => $entity->getOrden()
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    public function loadComboAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('PortadoresBundle:DenominacionVehiculo')->buscarDenominacionVehiculoCombo();

        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity['id'],
                'nombre' => $entity['nombre'],
            );
        }

        return new JsonResponse(array('rows' => $_data));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $nombre = trim($request->get('nombre'));
        $orden = trim($request->get('orden'));
        $entities = $em->getRepository('PortadoresBundle:DenominacionVehiculo')->findByNombre($nombre);

        $entities_total = $em->getRepository('PortadoresBundle:DenominacionVehiculo')->findByVisible(true);
        for ($i = 0; $i < count($entities_total); $i++) {
            if (strcasecmp($entities_total[$i]->getNombre(), $nombre) == 0)
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'La denominación de vehículo ya existe.'));
        }

        if ($entities) {
            if ($entities[0]->getVisible())
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'La denominación de vehículo ya existe.'));
            else {
                $entities[0]->setVisible(true);
                $em->persist($entities[0]);
                $em->flush();
                return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Denominación de vehículo adicionada con éxito.'));
            }
        }
        $entity = new DenominacionVehiculo();
        $entity->setNombre($nombre);
        $entity->setOrden($orden);
        $entity->setVisible(true);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Denominación de vehículo adicionada con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function modAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $nombre = trim($request->get('nombre'));
        $orden = trim($request->get('orden'));
        $entities = $em->getRepository('PortadoresBundle:DenominacionVehiculo')->findByNombre($nombre);
        if ($entities)
            if ($entities[0]->getId() != $id)
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Existe otra denominación de vehículo con ese nombre.'));
        $entity = $em->getRepository('PortadoresBundle:DenominacionVehiculo')->find($id);
        $entity->setNombre($nombre);
        $entity->setOrden($orden);
        try {
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Denominación de vehículo modificada con éxito.'));
            return $response;
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function delAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $entity = $em->getRepository('PortadoresBundle:DenominacionVehiculo')->find($id);
        $entity->setVisible(false);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Denominación de vehículo eliminada con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }
}