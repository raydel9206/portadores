<?php
/**
 * Created by PhpStorm.
 * User: pfcadenas
 * Date: 25/05/2017
 * Time: 13:00
 */

namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\AdminBundle\Util\Util;
use Geocuba\PortadoresBundle\Entity\PieFirma;
use Geocuba\PortadoresBundle\Util\DocumentosEnum;
use Doctrine\Common\CommonException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;

class PieFirmaController extends Controller
{

    use ViewActionTrait;
    
    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $nombre = $request->get('nombre');
        $nunidadid = $request->get('unidadid');
        $start = $request->get('start');
        $limit = $request->get('limit');
//var_dump($nunidadid);die;
        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $entities = $em->getRepository('PortadoresBundle:PieFirma')->buscarPieFirma($_unidades, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:PieFirma')->buscarPieFirma($_unidades, $start, $limit, true);

        $_data = array();

        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'documento' => $entity->getDocumento(),
                'documentonombre' => DocumentosEnum::getNombre($entity->getDocumento()),
                'confecciona' => is_null($entity->getConfecciona()) ? '' : $entity->getConfecciona()->getId(),
                'confeccionanombre' => is_null($entity->getConfecciona()) ? '' : $entity->getConfecciona()->getNombre(),
                'revisa' => is_null($entity->getRevisa()) ? '' : $entity->getRevisa()->getId(),
                'revisanombre' => is_null($entity->getRevisa()) ? '' : $entity->getRevisa()->getNombre(),
                'autoriza' => is_null($entity->getAutoriza()) ? '' : $entity->getAutoriza()->getId(),
                'autorizanombre' => is_null($entity->getAutoriza()) ? '' : $entity->getAutoriza()->getNombre(),
                'cajera' => is_null($entity->getCajera()) ? '' : $entity->getCajera()->getId(),
                'cajeranombre' => is_null($entity->getCajera()) ? '' : $entity->getCajera()->getNombre()
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $documento = trim($request->get('documento'));
        $confecciona = trim($request->get('confecciona'));
        $cajera = trim($request->get('cajera'));
        $revisa = trim($request->get('revisa'));
        $autoriza = trim($request->get('autoriza'));
        $nunidadid = trim($request->get('unidadid'));

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $repetido = $em->getRepository('PortadoresBundle:PieFirma')->buscarPieFirmaRepetido($documento, $_unidades);

        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe un pie de firma para este documento en la Unidad.'));

        $unidad = $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid);

        $entity = new PieFirma();
        $entity->setDocumento($documento);
        $entity->setConfecciona($em->getRepository('PortadoresBundle:Persona')->find($confecciona));
        $entity->setCajera($em->getRepository('PortadoresBundle:Persona')->find($cajera));
        $entity->setRevisa($em->getRepository('PortadoresBundle:Persona')->find($revisa));
        $entity->setAutoriza($em->getRepository('PortadoresBundle:Persona')->find($autoriza));
        $entity->setNunidadid($unidad);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Pie de Firma adicionado con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function modAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = $request->get('id');
        $documento = trim($request->get('documento'));
        $confecciona = trim($request->get('confecciona'));
        $cajera = trim($request->get('cajera'));
        $revisa = trim($request->get('revisa'));
        $autoriza = trim($request->get('autoriza'));
        $nunidadid = trim($request->get('unidadid'));

        if ($nunidadid) {
            $_unidades[0] = $nunidadid;
//            $unidades = $em->getRepository('PortadoresBundle:Unidad')->findBy(array('padreid' => $nunidadid));
//            $i = 1;
//            /** @var Unidad $unidad */
//            foreach ($unidades as $unidad) {
//                $_unidades[$i] = $unidad->getId();
//                $i++;
//            }
        } else {
            $_user = $this->get('security.token_storage')->getToken()->getUser()->get;
            $unidades = $em->getRepository('AdminBundle:UsuarioUnidad')->findBy(array('usuario' => $_user->getId()));
            foreach ($unidades as $unidad) {
                $_unidades[] = $unidad->getUnidad()->getId();
            }
        }

        $repetido = $em->getRepository('PortadoresBundle:PieFirma')->buscarPieFirmaRepetido($documento,$_unidades, $id);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe un pie de firma para este documento en la Unidad.'));

        $entity = $em->getRepository('PortadoresBundle:PieFirma')->find($id);
        $entity->setDocumento($documento);
        $entity->setConfecciona($em->getRepository('PortadoresBundle:Persona')->find($confecciona));
        $entity->setCajera($em->getRepository('PortadoresBundle:Persona')->find($cajera));
        $entity->setRevisa($em->getRepository('PortadoresBundle:Persona')->find($revisa));
        $entity->setAutoriza($em->getRepository('PortadoresBundle:Persona')->find($autoriza));

        try {
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Pie de Firma modificado con éxito.'));
            return $response;
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function delAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = $request->get('id');
        $entity = $em->getRepository('PortadoresBundle:PieFirma')->find($id);

        try {
            $em->remove($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Pie de Firma eliminado con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }
}