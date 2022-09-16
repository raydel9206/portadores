<?php
/**
 * Created by PhpStorm.
 * User: yosley
 * Date: 07/10/2015
 * Time: 9:58
 */

namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\PortadoresBundle\Entity\Chofer;
use Geocuba\PortadoresBundle\Entity\VehiculoPersona;
use Doctrine\Common\CommonException;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\PortadoresBundle\Util\Utiles;


class ChoferController extends Controller
{
   use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $_nombre = trim($request->get('nombre'));
        $start = $request->get('start');
        $limit = $request->get('limit');

        $_data = array();
        $em = $this->getDoctrine()->getManager();

        $_user = $this->get('security.token_storage')->getToken()->getUser();

        $unidades = $em->getRepository('AdminBundle:UsuarioUnidad')->findBy(array('usuario'=>$_user->getId()));

        /** @var UsuarioUnidad $unidad */
        foreach ($unidades as $unidad) {
            $_unidades[] = $unidad->getUnidad()->getId();
        }

        $entities = $em->getRepository('PortadoresBundle:Chofer')->buscarChofer($_nombre, $_unidades, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:Chofer')->buscarChofer($_nombre, $_unidades, $start, $limit, true);

        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'npersonaid' => $entity->getPersonaid()->getId(),
                'npersonanombre' => $entity->getPersonaid()->getNombre(),
                'fecha_expiracion_licencia' => $entity->getFechaExpiracionLicencia()->format('d/m/Y'),
                'nro_licencia' => $entity->getNroLicencia()
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }


    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $nro_licencia = trim($request->get('nro_licencia'));
        $npersonaid = trim($request->get('npersonaid'));
        $fechaExpiracionLicencia=trim($request->get('fecha_expiracion_licencia'));
        $id_vehiculo=trim($request->get('id_vehiculo'));


        $entities_lic = $em->getRepository('PortadoresBundle:VehiculoPersona')->findBy(array(
            'idvehiculo'=>$id_vehiculo,
            'idpersona'=>$npersonaid
        ));
        if($entities_lic)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Este vehículo ya fue asignado a esta persona.'));

        $var = date_create_from_format('d/m/Y',$fechaExpiracionLicencia);

        $entity = new Chofer();
        $entity->setNroLicencia($nro_licencia);
        $entity->setPersonaid($em->getRepository('PortadoresBundle:Persona')->find($npersonaid));
        $entity->setFechaExpiracionLicencia($var);

       $entity_person=new VehiculoPersona();
        $entity_person->setIdpersona($em->getRepository('PortadoresBundle:Persona')->find($npersonaid));
        $entity_person->setIdvehiculo($em->getRepository('PortadoresBundle:Vehiculo')->find($id_vehiculo));
        $entity_person->setVisible(true);

        try{
            $em->persist($entity);
            $em->persist($entity_person);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Vehículo Asignado con éxito.'));
        }
        catch(CommonException $ex){
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }
    public function modAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $nro_licencia = trim($request->get('nro_licencia'));
        $npersonaid = trim($request->get('npersonaid'));
        $fechaExpiracionLicencia=trim($request->get('fecha_expiracion_licencia'));

        $var = date_create_from_format('d/m/Y',$fechaExpiracionLicencia);
        $entities = $em->getRepository('PortadoresBundle:Chofer')->findByPersonaid($npersonaid);
        if($entities)
            if($entities[0]->getId() != $id)
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Existe un Chofer  con ese nombre.'));
        $entity = $em->getRepository('PortadoresBundle:Chofer')->find($id);
        $entity->setNroLicencia($nro_licencia);
        $entity->setPersonaid($em->getRepository('PortadoresBundle:Persona')->find($npersonaid));
        $entity->setFechaExpiracionLicencia($var);
        try{
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Chofer modificado con éxito.'));
            return $response;
        }
        catch(CommonException $ex){
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }
    public function delAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');

        $entity = $em->getRepository('PortadoresBundle:Chofer')->find($id);

        $entities = $em->getRepository('PortadoresBundle:Anticipo')->findByChofer($entity);
        if($entities){
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'No es posible eliminar el chofer,
            hay anticipos que dependen de él.'));
        }

        $em->remove($entity);

        try{
            //$em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Chofer eliminado con éxito.'));
        }
        catch(CommonException $ex){
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }
}