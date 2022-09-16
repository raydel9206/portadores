<?php

namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\PortadoresBundle\Entity\Producto;
use Doctrine\Common\CommonException;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ProductoController extends Controller
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
        $fila = trim($request->get('fila'));

        $start = $request->get('start');
        $limit = $request->get('limit');

        $entities = $em->getRepository('PortadoresBundle:Producto')->buscarProducto($_nombre, $fila, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:Producto')->buscarProducto($_nombre, $fila, $start, $limit, true);

        $_data = array();
        /** @var Producto $entity */
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre(),
                'fila' => $entity->getFila(),
                'um' => $entity->getUm()->getNombre(),
                'enblanco' => $entity->getEnblanco(),
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

//    public function loadComboAction(Request $request)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $entities = $em->getRepository('PortadoresBundle:Producto')->buscarProductoCombo();
//
//        foreach ($entities as $entity) {
//            $_data[] = array(
//                'id' => $entity['id'],
//                'nombre' => $entity['nombre'],
//                'precio' => $entity['precio'],
//                'codigo' => $entity['codigo']
//            );
//        }
//
//        return new JsonResponse(array('rows' => $_data));
//    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $nombre = trim($request->get('nombre'));
        $fila = trim($request->get('fila'));
        $enblanco = trim($request->get('enblanco'));
        $um = trim($request->get('um'));

        $en_blanco = $enblanco?true:false;

        $um_entity = $em->getRepository('PortadoresBundle:UnidadMedida')->find($um);

        $repetido = $em->getRepository('PortadoresBundle:Producto')->buscarProductoRepetido($nombre, $fila);
        if ($repetido > 0) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe un producto con el mismo nombre o mismo número de fila.'));
        }

        $entity = new Producto();
        $entity->setNombre($nombre);
        $entity->setVisible(true);
        $entity->setFila($fila);
        $entity->setUM($um_entity);
        $entity->setEnblanco($en_blanco);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Producto  adicionado con éxito.'));
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
        $fila = trim($request->get('fila'));
        $um = trim($request->get('um'));
        $enblanco = trim($request->get('enblanco'));

        $en_blanco = $enblanco?true:false;

        $um_entity = $em->getRepository('PortadoresBundle:UnidadMedida')->find($um);

        $repetido = $em->getRepository('PortadoresBundle:Producto')->buscarProductoRepetido($nombre, $fila, $id);
        if ($repetido > 0) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe un producto con el mismo nombre.'));
        }

        $entity = $em->getRepository('PortadoresBundle:Producto')->find($id);
        $entity->setNombre($nombre);
        $entity->setFila($fila);
        $entity->setUM($um_entity);
        $entity->setEnblanco($en_blanco);
        try {
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Producto modificado con éxito.'));
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

        $entity = $em->getRepository('PortadoresBundle:Producto')->find($id);
        $entity->setVisible(false);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Producto eliminado con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }

    }
}