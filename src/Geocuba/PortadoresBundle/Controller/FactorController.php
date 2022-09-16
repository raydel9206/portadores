<?php
/**
 * Created by PhpStorm.
 * User: kireny
 * Date: 4/11/15
 * Time: 11:27
 */

namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\AdminBundle\Util\Util;
use Geocuba\PortadoresBundle\Entity\Factor;
use Doctrine\Common\CommonException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;

class FactorController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $start = $request->get('start');
        $limit = $request->get('limit');
        $entities = $em->getRepository('PortadoresBundle:Factor')->FindListFactor($start, $limit);
        $entitiesTotal = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:Factor')->findByVisible(true);
        $_data = array();
        if(!$id)
            foreach ($entities as $entity){
//            \Doctrine\Common\Util\Debug::dump('aaaa');
//            die;
                $_data[] = array(
                    'id' => $entity->getId(),
                    'portador' =>$entity->getPortador(),
                    'unidad_medida_id1' => $entity->getUnidadMedida1()->getId(),
                    'unidad_medida_nombre1' => $entity->getUnidadMedida1()->getNombre(),
                    'factor_id1' => $entity->getFactorId1(),
                    'unidad_medida_id2' => $entity->getUnidadMedida2()->getId(),
                    'unidad_medida_nombre2' => $entity->getUnidadMedida2()->getNombre(),
                    'factor_id2' => $entity->getFactorId2()
                );
            }
        else
            foreach ($entitiesTotal as $entity){
                $_data[] = array(
                    'id' => $entity->getId(),
                    'portador' =>$entity->getPortador(),
                    'unidad_medida_id1' => $entity->getUnidadMedida1()->getId(),
                    'unidad_medida_nombre1' => $entity->getUnidadMedida1()->getNombre(),
                    'factor_id1' => $entity->getFactorId1(),
                    'unidad_medida_id2' => $entity->getUnidadMedida2()->getId(),
                    'unidad_medida_nombre2' => $entity->getUnidadMedida2()->getNombre(),
                    'factor_id2' => $entity->getFactorId2()
                );
            }
        return new JsonResponse(array('success'=>true,'rows' => $_data, 'total' => count($entitiesTotal)));
    }

    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $portador = trim($request->get('portador'));
        $unidad_medida_id1 = trim($request->get('unidad_medida_id1'));
        $factor_id1 = trim($request->get('factor_id1'));
        $unidad_medida_id2 = trim($request->get('unidad_medida_id2'));
        $factor_id2 = trim($request->get('factor_id2'));

        if($unidad_medida_id1 == $unidad_medida_id2)
        {
            return new JsonResponse(array('success'=>false, 'cls'=>'danger','message'=>'Las dos unidades de medidas no pueden ser iguales.'));
        }

        $entities = $em->getRepository('PortadoresBundle:Factor')->findBy(array(
            'portador'=>$portador,
            'unidadMedida1'=>$unidad_medida_id1,
            'unidadMedida2'=>$unidad_medida_id2
        ));
        if($entities){
            if($entities[0]->getVisible())
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ese factor de conversión ya existe para ese portador.'));
            else{
                $entities[0]->setVisible(true);
                $entities[0]->setPortador($portador);
                $entities[0]->setUnidadMedida1($em->getRepository('PortadoresBundle:UnidadMedida')->find($unidad_medida_id1));
                $entities[0]->setFactorId1($factor_id1);
                $entities[0]->setUnidadMedida2($em->getRepository('PortadoresBundle:UnidadMedida')->find($unidad_medida_id2));
                $entities[0]->setFactorId2($factor_id2);
                $em->persist($entities[0]);
                $em->flush();
                return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Factor adicionado con éxito.'));
            }
        }

        $entity = new Factor();
        $entity->setPortador($portador);
        $entity->setUnidadMedida1($em->getRepository('PortadoresBundle:UnidadMedida')->find($unidad_medida_id1));
        $entity->setFactorId1($factor_id1);
        $entity->setUnidadMedida2($em->getRepository('PortadoresBundle:UnidadMedida')->find($unidad_medida_id2));
        $entity->setFactorId2($factor_id2);
        try{
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Factor de conversión adicionado con éxito.'));
        }
        catch(CommonException $ex){
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function modAction (Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $portador = trim($request->get('portador'));
        $unidad_medida_id1 = trim($request->get('unidad_medida_id1'));
        $factor_id1 = trim($request->get('factor_id1'));
        $unidad_medida_id2= trim($request->get('unidad_medida_id2'));
        $factor_id2 = trim($request->get('factor_id2'));

        if($unidad_medida_id1 == $unidad_medida_id2 )
        {
            return new JsonResponse(array('success'=>false, 'cls'=>'danger','message'=>'Las dos unidades de medidas no pueden ser iguales.'));
        }
        $entities = $em->getRepository('PortadoresBundle:Factor')->findBy(
            array(
                'portador'=>$portador,
                'unidadMedida1'=>$unidad_medida_id1,
                'unidadMedida2'=>$unidad_medida_id2
            ));
        if($entities)
            if($entities[0]->getId() != $id)
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Existe un factor de conversión con ese portador.'));

        $entity = $em->getRepository('PortadoresBundle:Factor')->find($id);
        $entity->setPortador($portador);
        $entity->setUnidadMedida1($em->getRepository('PortadoresBundle:UnidadMedida')->find($unidad_medida_id1));
        $entity->setFactorId1($factor_id1);
        $entity->setUnidadMedida2($em->getRepository('PortadoresBundle:UnidadMedida')->find($unidad_medida_id2));
        $entity->setFactorId2($factor_id2);

        try{
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Factor de conversión modificado con éxito.'));
            return $response;
        }
        catch(CommonException $ex){
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function delAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $entity = $em->getRepository('PortadoresBundle:Factor')->find($id);
        $em->remove($entity);
        try{

            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Factor de conversión eliminado con éxito.'));
        }
        catch(CommonException $ex){
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }
}