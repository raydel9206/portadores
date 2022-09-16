<?php
/**
 * Created by PhpStorm.
 * User: yosley
 * Date: 07/10/2015
 * Time: 14:48
 */

namespace Geocuba\PortadoresBundle\Controller;
use Geocuba\PortadoresBundle\Entity\ServicioAgua;
use Doctrine\Common\CommonException;
use Doctrine\Common\Util\Debug;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Geocuba\AdminBundle\Util\Util;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;

class ServicioAguaController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $entities = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:ServicioAgua')->findBy(array(
            'unidad'=>$request->get('id'),
            'visible'=>true
        ));
        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre(),
                'direccion'=>$entity->getDireccion(),
                'unidad_id'=>$entity->getUnidad()->getId(),
                'metrado'=>$entity->getMetrado(),
                'codigo'=>$entity->getCodigo(),
                'lectura_inicial'=>$entity->getLecturaInicial()
            );
        }
        return new JsonResponse(array('success' => true, 'rows' => $_data, 'total' => count($_data)));
    }

    public function loadUnidadesAction(Request $request)
    {
        $entities = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:Unidad')->findByVisible(true);
        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre()
            );
        }
        return new JsonResponse(array('rows' => $_data, 'total' => count($_data)));
    }

    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $unidad_id = trim($request->get('unidad_id'));
        $nombre = trim($request->get('nombre'));
        $direccion = trim($request->get('direccion'));
        $metrado = trim($request->get('metrado'));
        $codigo = trim($request->get('codigo'));
        $lectura_inicial = trim($request->get('lectura_inicial'));

        $unidad = $em->getRepository('PortadoresBundle:Unidad')->find($unidad_id);


        $entities = $em->getRepository('PortadoresBundle:ServicioAgua')->findBy(array(
            'nombre' => $nombre,
            'unidad' => $unidad_id
        ));

        $entities_codigo = $em->getRepository('PortadoresBundle:ServicioAgua')->findBy(array(
            'codigo' => $codigo,
            'unidad' => $unidad_id
        ));


        if($entities || $entities_codigo){
            if($entities)
            if($entities[0]->getVisible())
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe un servicio con ese nombre en la unidad seleccionada.'));
            elseif($entities_codigo[0]->getVisible())
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe un servicio con ese código en la unidad seleccionada.'));
            else{
                $entities[0]->setVisible(true);
                $entities[0]->setNombre($nombre);
                $entities[0]->setUnidad($unidad);
                $entities[0]->setDireccion($direccion);
                $entities[0]->setMetrado($metrado);
                $entities[0]->setCodigo($codigo);
                $entities[0]->setLecturaInicial($lectura_inicial);
                $em->persist($entities[0]);
                $em->flush();
                return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Servicio de Agua adicionado con éxito.'));
            }
        }
        if ($metrado != 1) {
            $metrado = false;
        } else {
            $metrado = true;
        }

        if(!$lectura_inicial)
            $lectura_inicial = 0;

        $entity = new ServicioAgua();
        $entity->setNombre($nombre);
        $entity->setUnidad($unidad);
        $entity->setDireccion($direccion);
        $entity->setMetrado($metrado);
        $entity->setCodigo($codigo);
        $entity->setLecturaInicial($lectura_inicial);
        $entity->setVisible(true);
        try{
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Servicio de Agua  adicionado con éxito.'));
        }
        catch(CommonException $ex){
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function modAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = trim($request->get('id'));
        $unidad_id = trim($request->get('unidad_id'));
        $nombre = trim($request->get('nombre'));
        $direccion = trim($request->get('direccion'));
        $metrado = $request->get('metrado');
        $codigo = trim($request->get('codigo'));
        $lectura_inicial = trim($request->get('lectura_inicial'));

        $unidad = $em->getRepository('PortadoresBundle:Unidad')->find($unidad_id);

        $entities = $em->getRepository('PortadoresBundle:ServicioAgua')->findBy(array(
            'nombre' => $nombre,
            'unidad' => $unidad_id,
            'visible'=>true
        ));
        $entities_codigo = $em->getRepository('PortadoresBundle:ServicioAgua')->findBy(array(
            'codigo' => $codigo,
            'unidad' => $unidad_id,
            'visible'=>true
        ));

        if ($metrado != 1) {
            $metrado = false;
        } else {
            $metrado = true;
        }


        if(count($entities)>1)
            if($entities[0]->getId() != $id)
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe un servicio con ese nombre en la unidad seleccionada.'));
        elseif(count($entities_codigo)>1)
            if($entities[0]->getCodigo() != $codigo)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe un servicio con ese código en la unidad seleccionada.'));

        $entity = $em->getRepository('PortadoresBundle:ServicioAgua')->find($id);

        if(!$lectura_inicial)
            $lectura_inicial = 0;
        $entity->setNombre($nombre);
        $entity->setUnidad($unidad);
        $entity->setDireccion($direccion);
        $entity->setMetrado($metrado);
        $entity->setCodigo($codigo);
        $entity->setLecturaInicial($lectura_inicial);
        $entity->setVisible(true);
        try{
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Servicio de Agua  modificado con éxito.'));
        }
        catch(CommonException $ex){
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function delAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $entity = $em->getRepository('PortadoresBundle:ServicioAgua')->find($id);
        $entity->setVisible(false);
        try{
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Servicio de Agua  eliminado con éxito.'));
        }
        catch(CommonException $ex){
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }
}