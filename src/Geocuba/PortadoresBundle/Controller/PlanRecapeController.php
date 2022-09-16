<?php
/**
 * Created by PhpStorm.
 * User: kireny
 * Date: 06/07/2017
 * Time: 14:18
 */

namespace Geocuba\PortadoresBundle\Controller;

use Doctrine\Common\Util\Debug;
use Geocuba\AdminBundle\Util\Util;
use Geocuba\PortadoresBundle\Entity\PlanRecape;
use Geocuba\PortadoresBundle\Entity\VehiculoRecape;
use Geocuba\PortadoresBundle\Entity\Vehiculo;
use Doctrine\Common\CommonException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;

class PlanRecapeController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $start = $request->get('start');
        $limit = $request->get('limit');
        $nunidadid = trim($request->get('unidadid'));

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $qb = $em->createQueryBuilder();
        $qb->select('planrecape')
            ->from('PortadoresBundle:PlanRecape', 'planrecape')
            ->Where($qb->expr()->in('planrecape.unidad', $_unidades))
            ->andWhere($qb->expr()->eq('planrecape.visible', 'true'));

        $entities = $qb->orderBy('planrecape.nombre', 'ASC')
            ->getQuery()
            ->setMaxResults($limit)
            ->setFirstResult($start)
            ->getResult();

        $qb = $em->createQueryBuilder();
        $qb->select('count(planrecape)')
            ->from('PortadoresBundle:PlanRecape', 'planrecape')
            ->Where($qb->expr()->in('planrecape.unidad', $_unidades))
            ->andWhere($qb->expr()->eq('planrecape.visible', 'true'));

        $total = $qb->getQuery()->getSingleScalarResult();

        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre(),
                'unidad_id' => $entity->getUnidad()->getId(),
                'unidad' => $entity->getUnidad()->getNombre()
            );
        }
        return new JsonResponse(array('rows' => $_data, 'total' => count($_data)));
    }

    public function loadVehiculoRecapeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = trim($request->get('id_recape'));

        $entities = $em->getRepository('PortadoresBundle:VehiculoRecape')->findBy(
            array(
                'idPlanRecape' => $id,
                'visible' => true
            )
        );
        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'mes'=>$entity->getMes(),
                'vehiculo_id' => $entity->getIdVehiculo()->getId(),
                'matricula' => $entity->getIdVehiculo()->getMatricula(),
                'marca' => $entity->getMarcaNeumatico(),
                'medidas' => $entity->getMedidasNeumatico(),
                'fecha_rotacion' => $entity->getFechaRotacionNeumaticos()->format('d/m/Y'),
                'cant_neumaticos' => $entity->getCantNeumaticos()
            );
        }
        return new JsonResponse(array('rows' => $_data, 'total' => count($_data)));
    }

    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $nombre = trim($request->get('nombre'));
        $send_vehiculos = trim($request->get('send_vehiculos'));
        $vehiculos = json_decode($send_vehiculos);
        $nunidadid = trim($request->get('unidadid'));

        $entity = new PlanRecape();
        $entity->setNombre($nombre);
        $entity->setUnidad($em->getRepository('PortadoresBundle:Unidad')->find($nunidadid));
        $entity->setVisible(true);
        try {
            $em->persist($entity);
            $entity_vr = new VehiculoRecape();
            foreach ($vehiculos as $ve) {
                $entities_vehiculo = $em->getRepository('PortadoresBundle:Vehiculo')->findByMatricula($ve->matricula);
                //TODO Error sino se inserta ningun vehiculo da error
                $vehiculo_id = $entities_vehiculo[0]->getId();
                $picar = explode('T', $ve->fecha_rotacion);
                $fecha_1 = $picar[0];
                $fecha = strpos('/', $fecha_1) ? date_create_from_format('d/m/Y', $fecha_1) : date_create_from_format('Y-m-d', $fecha_1);
                $entity_vr->setIdPlanRecape($em->getRepository('PortadoresBundle:PlanRecape')->find($entity->getId()));
                $entity_vr->setIdVehiculo($em->getRepository('PortadoresBundle:Vehiculo')->find($vehiculo_id));
                $entity_vr->setMes($ve->mes);
                $entity_vr->setMarcaNeumatico($ve->marca);
                $entity_vr->setMedidasNeumatico($ve->medidas);
                $entity_vr->setFechaRotacionNeumaticos($fecha);
                $entity_vr->setVisible(true);
                $entity_vr->setCantNeumaticos($ve->cant_neumaticos);
                try {
                    $em->merge($entity_vr);
                } catch (CommonException $ex) {
                    $em->clear();
                    return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
                }
            }
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Plan de Recape adicionado con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function modAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = trim($request->get('id'));
        $nombre = trim($request->get('nombre'));
        $send_vehiculos = trim($request->get('send_vehiculos'));
        $vehiculos = json_decode($send_vehiculos);
        $sol = array();
        foreach ($vehiculos as $d) {
            if (array_search($d->id, $sol) === false)
                $sol[] = $d->id;
        }
        $entity = $em->getRepository('PortadoresBundle:PlanRecape')->find($id);
        $entity->setNombre($nombre);
        try {
            $em->persist($entity);
            $em->flush();
            $entities_vr = $em->getRepository('PortadoresBundle:VehiculoRecape')->findBy(
                array(
                    'idPlanRecape' => $id,
                    'visible' => true
                ));
            $v = array();
            foreach ($entities_vr as $vr) {
                if (array_search($vr->getId(), $v) === false)
                    $v[] = $vr->getId();
            }
            if (count($entities_vr) > count($vehiculos)) {
                foreach ($v as $vl) {
                    if (!in_array($vl, $sol)) {
                        $entity_vrecape = $em->getRepository('PortadoresBundle:VehiculoRecape')->find($vl);
                        $entity_vrecape->setVisible(false);
                    }
                }
            }
            foreach ($vehiculos as $veh) {
                $entities_vehiculo = $em->getRepository('PortadoresBundle:Vehiculo')->findByMatricula($veh->matricula);
                $vehiculo_id = $entities_vehiculo[0]->getId();

                $picar = explode('T', $veh->fecha_rotacion);
                $fecha_1 = $picar[0];
                $fecha = strpos($fecha_1, '/') ? date_create_from_format('d/m/Y', $fecha_1) : date_create_from_format('Y-m-d', $fecha_1);
                if ($em->getRepository('PortadoresBundle:VehiculoRecape')->find($veh->id) == null) {
                    $entity_vrecape = new VehiculoRecape();
                    $entity_vrecape->setIdPlanRecape($em->getRepository('PortadoresBundle:PlanRecape')->find($id));
                    $entity_vrecape->setIdVehiculo($em->getRepository('PortadoresBundle:Vehiculo')->find($vehiculo_id));
                    $entity_vrecape->setMarcaNeumatico($veh->marca);
                    $entity_vrecape->setMedidasNeumatico($veh->medidas);
                    $entity_vrecape->setFechaRotacionNeumaticos($fecha);
                    $entity_vrecape->setVisible(true);
                    $entity_vrecape->setMes($veh->mes);
                    $entity_vrecape->setCantNeumaticos($veh->cant_neumaticos);
                } else {
                    $entity_vrecape = $em->getRepository('PortadoresBundle:VehiculoRecape')->find($veh->id);
                    $entity_vrecape->setIdVehiculo($em->getRepository('PortadoresBundle:Vehiculo')->find($vehiculo_id));
                    $entity_vrecape->setMarcaNeumatico($veh->marca);
                    $entity_vrecape->setMedidasNeumatico($veh->medidas);
                    $entity_vrecape->setFechaRotacionNeumaticos($fecha);
                    $entity_vrecape->setMes($veh->mes);
                    $entity_vrecape->setCantNeumaticos($veh->cant_neumaticos);
                }
                $em->persist($entity_vrecape);
            }
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Plan de Recape adicionado con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function delAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $entity = $em->getRepository('PortadoresBundle:PlanRecape')->find($id);
        $entity->setVisible(false);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'El Plan de Recape fue eliminado con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

}