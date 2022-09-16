<?php
/**
 * Created by PhpStorm.
 * User: kireny
 * Date: 4/11/15
 * Time: 14:28
 */

namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\AdminBundle\Util\Util;
use Geocuba\PortadoresBundle\Entity\SubActividad;
use Doctrine\Common\CommonException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\Utils\ViewActionTrait;

class SubActividadController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $_nombre = trim($request->get('nombre'));

        $start = $request->get('start');
        $limit = $request->get('limit');

        $entities = $em->getRepository('PortadoresBundle:SubActividad')->buscarSubactivudades($_nombre, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:SubActividad')->buscarSubactivudades($_nombre, $start, $limit, true);

        $_data = array();

        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre(),
                'nactividadid' => $entity->getNactividadid()->getId(),
                'nactividadnombre' => $entity->getNactividadid()->getNombre()
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $nombre = trim($request->get('nombre'));
        $nactividadid = trim($request->get('nactividadid'));

        $repetido = $em->getRepository('PortadoresBundle:SubActividad')->buscarSubactivudadesRepetido($nombre, $nactividadid);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe una subactividad con el mismo nombre para la actividad seleccionada.'));

        $entity = new SubActividad();
        $entity->setNombre($nombre);
        $entity->setNactividadid($em->getRepository('PortadoresBundle:Actividad')->find($nactividadid));
        $entity->setVisible(true);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Subactividad adicionado con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function modAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $nombre = trim($request->get('nombre'));
        $nactividadid = trim($request->get('nactividadid'));

        $repetido = $em->getRepository('PortadoresBundle:SubActividad')->buscarSubactivudadesRepetido($nombre, $nactividadid,$id);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe una subactividad con el mismo nombre para la actividad seleccionada.'));

        $entity = $em->getRepository('PortadoresBundle:SubActividad')->find($id);
        $entity->setNombre($nombre);
        $entity->setNactividadid($em->getRepository('PortadoresBundle:Actividad')->find($nactividadid));

        try {
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Subactividad modificada con éxito.'));
            return $response;
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function delAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = $request->get('id');
        $entity = $em->getRepository('PortadoresBundle:SubActividad')->find($id);
        $entity->setVisible(false);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Subactividad eliminado con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }
}