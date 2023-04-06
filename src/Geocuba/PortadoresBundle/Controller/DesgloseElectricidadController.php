<?php
/**
 * Created by PhpStorm.
 * User: yosley
 * Date: 30/11/2015
 * Time: 14:30
 */

namespace Geocuba\PortadoresBundle\Controller;

use Doctrine\Common\Collections\ExpressionBuilder;
use Geocuba\PortadoresBundle\Entity\DenominacionVehiculo;
use Doctrine\Common\CommonException;
use Doctrine\Common\Util\Debug;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Entity\DesgloseServicios;
use Geocuba\PortadoresBundle\Entity\DesgloseElectricidad;
use Geocuba\PortadoresBundle\Entity\Servicio;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class DesgloseElectricidadController extends Controller
{
    use ViewActionTrait;

    public function desgloselectricidadAction(Request $request)
    {

        $valores = json_decode($request->get('datos'), true);
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $anno = $anno = $session->get('SELECTED_YEAR');

        $var = array();


        $fecha_lectura = new \DateTime();
        //$anno = $fecha_lectura->format('Y');

        for ($i = 0, $iMax = count($valores); $i < $iMax; $i++) {
            $entity = new DesgloseElectricidad();
            $entity->setFechaDesglose(new \DateTime($valores[$i]['dia']));
            $entity->setPlanDiario($valores[$i]['plan_diario']);
            $entity->setPlanPico($valores[$i]['plan_pico']);
            $entity->setPerdidast($valores[$i]['perdidasT']);
            $entity->setMes($valores[$i]['nromes']);
            $entity->setAnno($valores[$i]['anno_desglose']);
            $entity->setIddesgloseServicios($em->getRepository('PortadoresBundle:DesgloseServicios')->find($valores[$i]['id_desglose']));
            $em->persist($entity);
            $em->flush();
        }
        try {
            //return new Response(json_encode(array('success' => true, 'cls' => 'success', 'message' => 'Autolectura realizada con éxito.'), JSON_UNESCAPED_UNICODE));
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Desglose realizado con éxito.'));
        } catch (\Exception $ex) {
            // return new Response(json_encode(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'), JSON_UNESCAPED_UNICODE));
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));

        }
    }

    public function desgloselectricidadModAction(Request $request)
    {
        $valores = json_decode($request->get('datosmod'), true);
        $em = $this->getDoctrine()->getManager();
        for ($i = 0, $iMax = count($valores); $i < $iMax; $i++) {
            $entity = $em->getRepository('PortadoresBundle:DesgloseElectricidad')->find($valores[$i]['id']);
            $entity->setPlanDiario($valores[$i]['plan_diario']);
            $entity->setPlanPico($valores[$i]['plan_pico']);
            $entity->setPerdidast($valores[$i]['perdidasT']);
            $em->persist($entity);
            $em->flush();
        }
        try {

            //return new Response(json_encode(array('success' => true, 'cls' => 'success', 'message' => 'Autolectura Realizada con éxito.'), JSON_UNESCAPED_UNICODE));
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Desglose modificado con éxito.'));
        } catch (\Exception $ex) {
            // return new Response(json_encode(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'), JSON_UNESCAPED_UNICODE));
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));

        }

    }

    public function damemesAction($Nromes)
    {
        if ($Nromes == 1) {
            $nombremes = 'Enero';

        } elseif ($Nromes == 2) {
            $nombremes = 'Febrero';

        } elseif ($Nromes == 3) {
            $nombremes = 'Marzo';


        } elseif ($Nromes == 4) {
            $nombremes = 'Abril';


        } elseif ($Nromes == 5) {
            $nombremes = 'Mayo';

        } elseif ($Nromes == 6) {
            $nombremes = 'Junio';


        } elseif ($Nromes == 7) {
            $nombremes = 'Julio';


        } elseif ($Nromes == 8) {
            $nombremes = 'Agosto';


        } elseif ($Nromes == 9) {
            $nombremes = 'Septiembre';


        } elseif ($Nromes == 10) {
            $nombremes = 'Octubre';


        } elseif ($Nromes == 11) {

            $nombremes = 'Noviembre';


        } elseif ($Nromes == 12) {
            $nombremes = 'Diciembre';


        }

        return $nombremes;
    }

    public function loaddesgloseAction()
    {

        $entities = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:DesgloseElectricidad')->findAll();
        $datoscompletos = array();

        if ($entities) {
            foreach ($entities as $entity) {
                $datoscompletos[] = array(
                    'plan_diario' => $entity->getPlanDiario(),
                    'plan_pico' => $entity->getPlanPico(),
                    'perdidasT' => $entity->getPerdidast(),
                    'fecha' => $entity->getFechaDesglose()->format('Y-m-j'),
                    'id' => $entity->getId(),
                    'mes' => $entity->getMes(),
                    'nombre_mes' => $this->damemesAction($entity->getMes()),
                    'id_desglose_servicio' => $entity->getIddesgloseServicios()->getId()
                );
            }
        }
        return new JsonResponse(array('rows' => $datoscompletos, 'total' => count($datoscompletos)));


    }

    public function loaddesglosemesAction(Request $request)
    {


        $em = $this->getDoctrine()->getManager();
        $id_desglose = trim($request->get('id_desglose'));
        $servicio = trim($request->get('servicio'));
        $fechaLastLect = null;

        $entities_menor = $em->getRepository('PortadoresBundle:Autolecturaprepago')->findOneBy(array('serviciosid' => $servicio), array('fechaLectura' => 'DESC'), 1, 0);
        $entities_mayor = $em->getRepository('PortadoresBundle:AutolecturaTresescalas')->findOneBy(array('serviciosid' => $servicio), array('fechaLectura' => 'DESC'), 1, 0);

        if($entities_menor){
           $fechaLastLect = $entities_menor->getFechaLectura();
        }
        if($entities_mayor){
           $fechaLastLect = $entities_mayor->getFechaLectura();
        }

        $entities = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:DesgloseElectricidad')->findByIddesgloseServicios($id_desglose);
        $datoscompletos = array();
        if ($entities) {
            foreach ($entities as $entity) {
                $datoscompletos[] = array(
                    'id' => $entity->getId(),
                    'fecha_desglose' => $entity->getFechaDesglose()->format('Y-m-d'),
                    'plan_diario' => $entity->getPlanDiario(),
                    'plan_pico' => $entity->getPlanPico(),
                    'perdidasT' => $entity->getPerdidast(),
                    'fechaLastLect' => ($fechaLastLect !== null) ? $fechaLastLect->format('Y-m-d'): null,
                    'mes' => $entity->getMes()
                );
            }
        }

        usort($datoscompletos, function ($a, $b) {
            return $a['fecha_desglose'] > $b['fecha_desglose'];
        });

        return new JsonResponse(array('success' => true, 'rows' => $datoscompletos, 'total' => \count($datoscompletos)));
    }

    public function desgloseserviciosAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $accion = trim($request->get('accion'));
        if ($accion !== 'Mod') {
            $mes = trim($request->get('mes'));
            $plan_total = trim($request->get('plan_total'));
            $plan_pico = trim($request->get('plan_pico'));
            $perdidasT = trim($request->get('perdidasT'));
            $servicio = trim($request->get('servicio'));
            $fecha_desglose = new \DateTime();
            $anno = trim($request->get('anno_desglose'));

            $entity = new DesgloseServicios();
            $entity->setPlanPico($plan_pico);
            $entity->setPlanTotal($plan_total);
            $entity->setPerdidast($perdidasT);
            $entity->setMes($mes);
            $entity->setAnno($anno);
            $entity->setFecha($fecha_desglose);
            $entity->setIdservicio($em->getRepository('PortadoresBundle:Servicio')->findOneBy(array('id' => $servicio)));
            try {
                $em->persist($entity);
                $em->flush();
                return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Desglose realizado con éxito.'));
            } catch (\Exception $ex) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));

            }

        } else {
            $id = trim($request->get('id'));

            $plan_total = trim($request->get('plan_total'));
            $plan_pico = trim($request->get('plan_pico'));
            $perdidasT = trim($request->get('perdidasT'));
            $servicio = trim($request->get('servicio'));

            $entities = $em->getRepository('PortadoresBundle:DesgloseServicios')->find($id);
            $desglose_diario = $em->getRepository('PortadoresBundle:DesgloseElectricidad')->findByIddesgloseServicios($id);
            $entities_servi = $em->getRepository('PortadoresBundle:Servicio')->find($servicio);
            $entities_servi->setMaximaDemandaContratada($plan_total);
            $entities->setPlanPico($plan_pico);
            $entities->setPlanTotal($plan_total);
            $entities->setPerdidast($perdidasT);

            $em->persist($entities);

            try {
                $em->persist($entities_servi);
                $em->flush();
                return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Desglose realizado con éxito.'));
            } catch (\Exception $ex) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
            }
        }

    }

    public function loaddesgloseserviciosAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();

        $idservicios = trim($request->get('idservicios'));

        $entitiesServ = $em->getRepository('PortadoresBundle:Servicio')->findOneBy(array('id' => $idservicios));
        $anno_abierto = trim($session->get('selected_year'));

        if (!$entitiesServ) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'No existen servicios registrados'));
        }

        $_data = array();
        $entitiesdesglose = $em->getRepository('PortadoresBundle:DesgloseServicios')->findBy(array('idservicio' => $entitiesServ->getid(), 'anno' => $anno_abierto));
        if ($entitiesdesglose) {
            foreach ($entitiesdesglose as $entityDes) {
                $_data[] = array(
                    'id' => $entityDes->getId(),
                    'plan_total' => $entityDes->getPlanTotal(),
                    'plan_pico' => $entityDes->getPlanPico(),
                    'perdidasT' => $entityDes->getPerdidast(),
                    'mes' => $entityDes->getMes(),
                    'nombre_mes' => $this->damemesAction($entityDes->getMes()),
                    'fecha' => $entityDes->getFecha(),
                    'idservicios' => $entityDes->getIdservicio()->getId(),
                );
            }
        }
        return new JsonResponse(array('rows' => $_data, 'total' => \count($_data)));
    }

} 