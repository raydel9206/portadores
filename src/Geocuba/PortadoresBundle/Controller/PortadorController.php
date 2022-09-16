<?php
/**
 * Created by PhpStorm.
 * User: kireny
 * Date: 17/02/16
 * Time: 03:42 PM
 */

namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\PortadoresBundle\Entity\Portador;
use Doctrine\Common\CommonException;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class PortadorController extends Controller
{
    // See below how it is used.
    const FLUSH_THRESHOLD = 100;

    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $_nombre = trim($request->get('nombre'));

        $start = $request->get('start');
        $limit = $request->get('limit');

        $entities = $em->getRepository('PortadoresBundle:Portador')->buscarPortador($_nombre, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:Portador')->buscarPortador($_nombre, $start, $limit, true);

        $_data = array();

        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre(),
                'unidad_medidaid' => $entity->getUnidadMedida()->getId(),
                'unidad_medida' => $entity->getUnidadMedida()->getNombre()
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $nombre = trim($request->get('nombre'));
        $unidad_medida = $request->get('unidad_medidaid');

        $repetido = $em->getRepository('PortadoresBundle:Portador')->buscarPortadorRepetido($nombre);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe un portador con el mismo nombre.'));

        $entity = new Portador();
        $entity->setNombre($nombre);
        $entity->setUnidadMedida($em->getRepository('PortadoresBundle:UnidadMedida')->find($unidad_medida));
        $entity->setVisible(true);
        try {
            $em->persist($entity);
            $em->flush();
            return new Response(json_encode(array('success' => true, 'cls' => 'success', 'message' => 'Portador adicionado con éxito.'), JSON_UNESCAPED_UNICODE));
        } catch (CommonException $ex) {
            return new Response(json_encode(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'), JSON_UNESCAPED_UNICODE));
        }
    }

    public function modAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $nombre = trim($request->get('nombre'));
        $unidad_medida = $request->get('unidad_medidaid');

        $repetido = $em->getRepository('PortadoresBundle:Portador')->buscarPortadorRepetido($nombre, $id);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe un portador con el mismo nombre.'));

        $entity = $em->getRepository('PortadoresBundle:Portador')->find($id);
        $entity->setNombre($nombre);
        $entity->setUnidadMedida($em->getRepository('PortadoresBundle:UnidadMedida')->find($unidad_medida));
        try {
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Portador modificado con éxito.'));
            return $response;
        } catch (CommonException $ex) {
            return new Response(json_encode(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'), JSON_UNESCAPED_UNICODE));
        }
    }

    public function delAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = $request->get('id');
        $entity = $em->getRepository('PortadoresBundle:Portador')->find($id);
        $entity->setVisible(false);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Portador eliminado con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }
}