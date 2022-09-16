<?php
/**
 * Created by PhpStorm.
 * User: kireny
 * Date: 06/05/16
 * Time: 08:45
 */

namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\AdminBundle\Util\Util;
use Geocuba\PortadoresBundle\Entity\BancoTransformadores;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Geocuba\Utils\ViewActionTrait;


class BancoTransformadoresController extends Controller
{

    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $start = $request->get('start');
        $limit = $request->get('limit');
        $entities = $em->getRepository('PortadoresBundle:BancoTransformadores')->FindListBancoTransformadores($start, $limit);
        $entitiesTotal = $em->getRepository('PortadoresBundle:BancoTransformadores')->findByVisible(true);
        $_data = array();
        if(!$id)
            foreach ($entities as $entity) {
                $_data[] = array(
                    'id' => $entity->getId(),
                    'capacidad' => $entity->getCapacidad(),
                    'tipo'=>$entity->getTipo(),
                    'pfe'=>$entity->getPfe(),
                    'pcu'=>$entity->getPcu()
                );
            }
        else
            foreach ($entitiesTotal as $entity) {
                $_data[] = array(
                    'id' => $entity->getId(),
                    'capacidad' => $entity->getCapacidad(),
                    'tipo'=>$entity->getTipo(),
                    'pfe'=>$entity->getPfe(),
                    'pcu'=>$entity->getPcu()
                );
            }
       // print_r($_data);die;
        return new Response(json_encode(array('rows' => $_data, 'total' => count($entitiesTotal))));
    }
    public function addAction(Request $request)
    {
        //print_r($request);die;
        $em = $this->getDoctrine()->getManager();
        $capacidad = trim($request->get('capacidad'));
        $tipo=trim($request->get('tipo'));
        $pfe=trim($request->get('pfe'));
        $pcu=trim($request->get('pcu'));
//        $arr = explode(',',$capacidad);
//        $capacidad = $arr[0].'.'.$arr[1];
        $entities = $em->getRepository('PortadoresBundle:BancoTransformadores')->findBy(array(
            'capacidad'=>$capacidad,
            'tipo'=>$tipo,
        ));
        if($entities){
            if($entities[0]->getVisible())
                return new Response(json_encode(array('success' => false, 'cls' => 'danger', 'message' => 'La capacidad ya existe.'), JSON_UNESCAPED_UNICODE));
            else{
                $entities[0]->setVisible(true);
                $entities[0]->setCapacidad($capacidad);
                $entities[0]->setTipo($tipo);
                $entities[0]->setPfe($pfe);
                $entities[0]->setPcu($pcu);
                $em->persist($entities[0]);
                $em->flush();
                return new Response(json_encode(array('success' => true, 'cls' => 'success', 'message' => 'Capacidad adicionada con éxito.'), JSON_UNESCAPED_UNICODE));
            }
        }

        $entity = new BancoTransformadores();
        $entity->setCapacidad($capacidad);
        $entity->setTipo($tipo);
        $entity->setPfe($pfe);
        $entity->setPcu($pcu);
        $entity->setVisible(true);
        try{
            $em->persist($entity);
            $em->flush();
            return new Response(json_encode(array('success' => true, 'cls' => 'success', 'message' => 'Capacidad adicionada con éxito.'), JSON_UNESCAPED_UNICODE));
        }
        catch(CommonException $ex){
            return new Response(json_encode(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'), JSON_UNESCAPED_UNICODE));
        }
    }
    public function modAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $capacidad = trim($request->get('capacidad'));
        $tipo=trim($request->get('tipo'));
        $pfe=trim($request->get('pfe'));
        $pcu=trim($request->get('pcu'));
//        $arr = explode(',',$capacidad);
//        $capacidad = $arr[0].'.'.$arr[1];
        $entities = $em->getRepository('PortadoresBundle:BancoTransformadores')->findByCapacidad($capacidad);
        if($entities){
            if($entities[0]->getId() != $id)
                return new Response(json_encode(array('success' => false, 'cls' => 'danger', 'message' => 'Existe otra capacidad con ese dato.'), JSON_UNESCAPED_UNICODE));
        }

        $entity = $em->getRepository('PortadoresBundle:BancoTransformadores')->find($id);
        $entity->setCapacidad($capacidad);
        $entity->setTipo($tipo);
        $entity->setPfe($pfe);
        $entity->setPcu($pcu);
        try{
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Capacidad modificada con éxito.'));
            return $response;
        }
        catch(CommonException $ex){
            return new Response(json_encode(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'), JSON_UNESCAPED_UNICODE));
        }
    }
    public function delAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $entity = $em->getRepository('PortadoresBundle:BancoTransformadores')->find($id);
        $entity->setVisible(false);
        try{
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Capacidad eliminada con éxito.'));
        }
        catch(CommonException $ex){
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }
}