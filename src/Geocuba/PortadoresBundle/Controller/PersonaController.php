<?php
/**
 * Created by PhpStorm.
 * User: yosley
 * Date: 06/10/2015
 * Time: 16:36
 */


namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\AdminBundle\Util\Util;
use Geocuba\PortadoresBundle\Entity\Persona;
use Doctrine\Common\CommonException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;

class PersonaController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $_nombre = trim($request->get('nombre'));
        $_operarioTaller = trim($request->get('operarioTaller'));
        $nunidadid = $request->get('unidadid');
        $start = $request->get('start');
        $limit = $request->get('limit');

        $_data = array();
        $em = $this->getDoctrine()->getManager();

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $entities = $em->getRepository('PortadoresBundle:Persona')->buscarPersona($_nombre, $_operarioTaller, $_unidades, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:Persona')->buscarPersona($_nombre, $_operarioTaller, $_unidades, $start, $limit, true);

        foreach ($entities as $entity) {
            $cargoid = '';
            $cargo = '';
            if ($entity->getCargoid()) {
                $cargoid = $entity->getCargoid()->getId();
                $cargo = $entity->getCargoid()->getNombre();
            }
            $telefono = explode('-', $entity->getTelefono());
            if (count($telefono) == 2) {
                $codigo1 = ($telefono[0] != '#') ? $telefono[0] : '';
                $codigo2 = ($telefono[1] != '#') ? $telefono[1] : '';
            } else {
                $codigo1 = '';
                $codigo2 = '';
            }

            $_data[] = array(
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre(),
                'cargoid' => $cargoid,
                'cargo' => $cargo,
                'operarioTaller' => $entity->getOperarioTaller(),
                'ci' => $entity->getCi(),
                'direccion' => $entity->getDireccion(),
                'telefono' => $codigo1 . $codigo2,
                'codigo-1' => $codigo1,
                'numero-2' => $codigo2,
                'unidadid' => $entity->getNunidadid()->getId(),
                'nombreunidadid' => $entity->getNunidadid()->getNombre()
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    public function loadComboAction(Request $request)
    {
        $nunidadid = $request->get('unidadid');

        $_data = array();
        $em = $this->getDoctrine()->getManager();

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $entities = $em->getRepository('PortadoresBundle:Persona')->buscarPersonaCombo($_unidades);

        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity['id'],
                'nombre' => $entity['nombre'],
                'nunidadid' => $entity['nunidadid'],
            );
        }

        return new JsonResponse(array('rows' => $_data));
    }

    public function loadPersonaAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('PortadoresBundle:Cargo')->findByVisible(true);
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
        $nombre = trim($request->get('nombre'));
        $ci = trim($request->get('ci'));
        $nunidadid = trim($request->get('unidadid'));
        $cargoid = $request->get('cargoid');
        $operariotaller = $request->get('operarioTaller');
        $direccion = trim($request->get('direccion'));
        $codigo_telf = (trim($request->get('codigo-1')) != '') ? trim($request->get('codigo-1')) : '#';
        $numero_telf = (trim($request->get('numero-2')) != '') ? trim($request->get('numero-2')) : '#';
        $telefono = $codigo_telf . '-' . $numero_telf;

        $repetido = $em->getRepository('PortadoresBundle:Persona')->buscarPersonaRepetido($ci);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe una persona con el mismo carnet de identidad.'));

        $entity = new Persona();
        $entity->setNombre($nombre);
        $entity->setCargoid($em->getRepository('PortadoresBundle:Cargo')->find($cargoid));
        $entity->setOperarioTaller($operariotaller);
        $entity->setCi($ci);
        $entity->setDireccion($direccion);
        $entity->setTelefono($telefono);
        $entity->setNunidadid($em->getRepository('PortadoresBundle:Unidad')->find($nunidadid));
        $entity->setVisible(true);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Persona adicionada con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function modAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $nombre = trim($request->get('nombre'));
        $ci = trim($request->get('ci'));
        $cargoid = $request->get('cargoid');
        $operariotaller = $request->get('operarioTaller');

        $codigo_telf = (trim($request->get('codigo-1')) != '') ? trim($request->get('codigo-1')) : '#';
        $numero_telf = (trim($request->get('numero-2')) != '') ? trim($request->get('numero-2')) : '#';
        $telefono = $codigo_telf . '-' . $numero_telf;
        $nunidadid = trim($request->get('unidadid'));
        $direccion = trim($request->get('direccion'));

        $repetido = $em->getRepository('PortadoresBundle:Persona')->buscarPersonaRepetido($ci, $id);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe una persona con el mismo carnet de identidad.'));

        $entity = $em->getRepository('PortadoresBundle:Persona')->find($id);

        $entity->setNombre($nombre);
        $entity->setCargoid($em->getRepository('PortadoresBundle:Cargo')->find($cargoid));
        $entity->setOperarioTaller($operariotaller);
        $entity->setCi($ci);
        $entity->setDireccion($direccion);
        $entity->setTelefono($telefono);
        $entity->setNunidadid($em->getRepository('PortadoresBundle:Unidad')->find($nunidadid));

        try {
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Persona modificada con éxito.'));
            return $response;
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function delAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = $request->get('id');
        $entity = $em->getRepository('PortadoresBundle:Persona')->find($id);
        $entity->setVisible(false);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Persona eliminada con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }
}