<?php
/**
 * Created by PhpStorm.
 * User: yosley
 * Date: 07/10/2015
 * Time: 14:48
 */

namespace Geocuba\PortadoresBundle\Controller;

use Doctrine\Common\Util\Debug;
use Geocuba\PortadoresBundle\Entity\Actividad;
use Doctrine\Common\CommonException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Geocuba\AdminBundle\Util\Util;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;

class ActividadController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $_nombre = trim($request->get('nombre'));
        $portadorid = $request->get('portadorid');

        $start = $request->get('start');
        $limit = $request->get('limit');

        $entities = $em->getRepository('PortadoresBundle:Actividad')->buscarActividad($_nombre, $portadorid, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:Actividad')->buscarActividad($_nombre, $portadorid, $start, $limit, true);

        $_data = array();

        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre(),
                'codigogae' => $entity->getCodigogae(),
                'codigomep' => $entity->getCodigomep(),
                'administrativa' => $entity->getAdministrativa(),
                'inversion' => $entity->getInversiones(),
                'trafico' => $entity->getTrafico(),
                'um_actividad' => $entity->getUmActividad()->getId(),
                'um_actividad_nombre' => $entity->getUmActividad()->getNivelActividad(),
                'id_portador' => $entity->getPortadorid()->getId(),
                'portadornombre' => $entity->getPortadorid()->getNombre()
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    public function loadComboAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $tipo_combustible_id=$request->get('tipo_combustibleid');

        $portadorid = ($tipo_combustible_id && $tipo_combustible_id!='')?$em->getRepository('PortadoresBundle:Portador')->find($em->getRepository('PortadoresBundle:TipoCombustible')->find($tipo_combustible_id)->getPortadorid())->getId():'';
        $entities = $em->getRepository('PortadoresBundle:Actividad')->buscarActividadCombo($portadorid);
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity['id'],
                'nombre' => $entity['nombre'].' ('.$entity['portador_nombre'].')',
            );
        }

        return new JsonResponse(array('rows' => $_data));
    }

    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $nombre = trim($request->get('nombre'));
        $codigogae = trim($request->get('codigogae'));
        $codigomep = trim($request->get('codigomep'));
        $administrativa = trim($request->get('administrativa'));
        $um_actividad = trim($request->get('um_actividad'));
        $inversion = trim($request->get('inversion'));
        $trafico = trim($request->get('trafico'));
        $portador = trim($request->get('id_portador'));

        if ($administrativa == 1) {
            $var = true;
        } elseif ($administrativa == 0) {
            $var = false;
        }

        if ($inversion == 1) {
            $inv = true;
        } elseif ($inversion == 0) {
            $inv = false;
        }

        if ($trafico == 1) {
            $tra = true;
        } elseif ($trafico == 0) {
            $tra = false;
        }

        $repetido = $em->getRepository('PortadoresBundle:Actividad')->buscarActividadRepetido($nombre, $um_actividad, $portador);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe una actividad con el mismo nombre.'));

        $entity = new Actividad();
        $entity->setNombre($nombre);
        $entity->setVisible(true);
        $entity->setCodigogae($codigogae);
        $entity->setCodigomep($codigomep);
        $entity->setAdministrativa($var);
        $entity->setInversiones($inv);
        $entity->setTrafico($tra);
        $entity->setPortadorid($em->getRepository('PortadoresBundle:Portador')->find($portador));
        $entity->setUmActividad($em->getRepository('PortadoresBundle:UMNivelActividad')->find($um_actividad));
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Actividad  adicionada con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function modAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $nombre = trim($request->get('nombre'));
        $codigogae = trim($request->get('codigogae'));
        $codigomep = trim($request->get('codigomep'));
        $administrativa = $request->get('administrativa');
        $um_actividad = trim($request->get('um_actividad'));
        $inversion = trim($request->get('inversion'));
        $trafico = trim($request->get('trafico'));
        $portador = trim($request->get('id_portador'));
        if ($administrativa == 1) {
            $var = true;
        } elseif ($administrativa == 0) {
            $var = false;
        }

        if ($inversion == 1) {
            $inv = true;
        } elseif ($inversion == 0) {
            $inv = false;
        }

        if ($trafico == 1) {
            $tra = true;
        } elseif ($trafico == 0) {
            $tra = false;
        }

        $repetido = $em->getRepository('PortadoresBundle:Actividad')->buscarActividadRepetido($nombre, $um_actividad, $portador, $id);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe una actividad con el mismo nombre.'));

        $entity = $em->getRepository('PortadoresBundle:Actividad')->find($id);
        $entity->setNombre($nombre);
        $entity->setCodigogae($codigogae);
        $entity->setCodigomep($codigomep);
        $entity->setAdministrativa($var);
        $entity->setInversiones($inv);
        $entity->setTrafico($tra);
        $entity->setPortadorid($em->getRepository('PortadoresBundle:Portador')->find($portador));
        $entity->setUmActividad($em->getRepository('PortadoresBundle:UMNivelActividad')->find($um_actividad));
        try {
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Actividad Modificada con éxito.'));
            return $response;
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function delAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = $request->get('id');
        $entity = $em->getRepository('PortadoresBundle:Actividad')->find($id);
        $entity->setVisible(false);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Actividad  eliminada con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }
}