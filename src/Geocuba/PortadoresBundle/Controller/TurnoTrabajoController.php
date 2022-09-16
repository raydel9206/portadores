<?php
/**
 * Created by PhpStorm.
 * User: yosley
 * Date: 23/05/2016
 * Time: 15:46
 */

namespace Geocuba\PortadoresBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Doctrine\Common\CommonException;

use Geocuba\AdminBundle\Util\Util;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Geocuba\Utils\ViewActionTrait;


use Geocuba\PortadoresBundle\Entity\TurnoTrabajo;

class TurnoTrabajoController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('PortadoresBundle:TurnoTrabajo')->findAll();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'turno' => $entity->getTurno(),
                'horas'=>$entity->getHoras()
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => count($_data)));
    }

    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $turno = trim($request->get('turno'));
        $horas = trim($request->get('horas'));

        $entities_total = $em->getRepository('PortadoresBundle:TurnoTrabajo')->findBy(
            array(
                'turno'=>$turno,
                'horas'=>$horas
            )
        );
        for($i=0;$i<count($entities_total);$i++){
            if(strcasecmp($entities_total[$i]->getTurno(),$turno) == 0)
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El Turno de trabajo ya existe.'));
        }

        $entity = new TurnoTrabajo();
        $entity->setTurno($turno);
        $entity->setHoras($horas);
        try{
            $em->persist($entity);
            $em->flush();
            return new Response(json_encode(array('success' => true, 'cls' => 'success', 'message' => 'Turno de trabajo adicionado con éxito.'), JSON_UNESCAPED_UNICODE));
        }
        catch(CommonException $ex){
            return new Response(json_encode(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'), JSON_UNESCAPED_UNICODE));
        }
    }
    public function modAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $turno = trim($request->get('turno'));
        $horas = trim($request->get('horas'));
        $entities = $em->getRepository('PortadoresBundle:TurnoTrabajo')->findByTurno($turno);
        if($entities)
            if($entities[0]->getId() != $id)
                return new Response(json_encode(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe ese Turno de trabajo.'), JSON_UNESCAPED_UNICODE));
        $entity = $em->getRepository('PortadoresBundle:TurnoTrabajo')->find($id);
        $entity->setTurno($turno);
        $entity->setHoras($horas);
        try{
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Turno de trabajo modificado con éxito.'));
            return $response;
        }
        catch(CommonException $ex){
            return new Response(json_encode(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'), JSON_UNESCAPED_UNICODE));
        }
    }
    public function delAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $entity = $em->getRepository('PortadoresBundle:TurnoTrabajo')->find($id);
        $em->remove($entity);
        try{
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Turno de trabajo eliminado con éxito.'));
        }
        catch(CommonException $ex){
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }







} 