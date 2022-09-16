<?php

namespace Geocuba\PortadoresBundle\Controller;

use Exception;
use Geocuba\PortadoresBundle\Entity\PlanificacionCombustibleTecn;
use Geocuba\PortadoresBundle\Entity\PlanificacionCombustibleTecnDesglose;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\PortadoresBundle\Util\Utiles;


class PlanificacionCombustibleTecnController extends Controller
{
    use ViewActionTrait;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loadAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $unidadId = trim($request->get('unidad_id'));
        $tipoComb = $request->get('tipo_combustibleid');
        $searchText = $request->get('search_text');
        $anno = $request->get('anno');
        $mes = $request->get('mes');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($unidadId), $_unidades);
        $_unidadesStr = array_reduce($_unidades, static function($temp, $item) {
            return $temp . "'$item',";
        }, '(');
        $_unidadesStr .= "'_')";

        $entities = $em->getRepository('PortadoresBundle:PlanificacionCombustibleTecn')->findAllBy($_unidadesStr, $tipoComb, $anno, $searchText);

        $meses = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
        foreach ($entities as &$entity){
            $mesesCombustible = explode(',', $entity['cantidades_combustible']);
            $nivelesActividad = explode(',', $entity['niveles_actividad']);
            foreach ($meses as $index => $mes) {
                $entity['combustible_' . $mes] = $mesesCombustible[$index];
                $entity['nivel_actividad_' . $mes] = $nivelesActividad[$index];
            }
            unset($entity['cantidades_combustible'], $entity['niveles_actividad']);
        }
        unset($entity);

        return new JsonResponse(['rows' => $entities]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function generateAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $anno = $request->get('anno');
        $unidadId = $request->get('unidad_id');
        $tipoCombustibleId = $request->get('tipo_combustible_id');

//        $em->transaction()
        $planificaciones = $em->getRepository('PortadoresBundle:PlanificacionCombustibleTecn')->findBy(['anno' => $anno, 'tipoCombustible' => $tipoCombustibleId, 'unidad' => $unidadId]);
        foreach ($planificaciones as $planificacion) $em->remove($planificacion);

        $sql = "SELECT eq.id FROM nomencladores.equipos_tecnologicos eq LEFT JOIN nomencladores.calderas c on c.id = eq.id
                WHERE eq.unidad_id = '$unidadId' 
                AND (eq.tipo_combustible_id = '$tipoCombustibleId' OR c.tipo_combustible_recirculacion_id = '$tipoCombustibleId')";

        $equipos = $this->getDoctrine()->getConnection()->fetchAll($sql);

        foreach ($equipos as $equipo){
            $plan = new PlanificacionCombustibleTecn();
            $plan->setAnno($anno)
                ->setUnidad($em->find('PortadoresBundle:Unidad',$unidadId))
                ->setEquipoTecnologico($em->find('PortadoresBundle:EquipoTecnologico', $equipo['id']))
                ->setTipoCombustible($em->find('PortadoresBundle:TipoCombustible', $tipoCombustibleId))
                ->setCantCombustible(0)
                ->setNivelActividad(0);

            for ($i = 1; $i <= 12; $i++) {
                $planDesglose = new PlanificacionCombustibleTecnDesglose();
                $planDesglose->setMes($i)
                    ->setNivelActividad(0)
                    ->setCantCombustible(0);
                $plan->addDesglose($planDesglose);
            }

            $em->persist($plan);
        }

        try {
            $em->flush();
        } catch (Exception $e) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }

        return new JsonResponse(['success' => true]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function saveAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $planificaciones = json_decode($request->get('store'), true);

        $meses = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
        foreach ($planificaciones as $storePlan) {
            /** @var PlanificacionCombustibleTecn $planDb */
            $planDb = $em->find('PortadoresBundle:PlanificacionCombustibleTecn', $storePlan['id']);
            $planDb->setCantCombustible($storePlan['combustible_total'])
                ->setNivelActividad($storePlan['nivel_actividad_total']);

            $em->persist($planDb);

            for ($i = 1; $i <= 12; $i++) {
                /** @var PlanificacionCombustibleTecnDesglose[] $desglose */
               $desglose = $em->getRepository('PortadoresBundle:PlanificacionCombustibleTecnDesglose')->findBy(['planificacionCombustibleTecn' => $storePlan['id'], 'mes' => $i]);
               $desglose[0]->setCantCombustible($storePlan['combustible_' . $meses[$i-1]])
                   ->setNivelActividad($storePlan['nivel_actividad_' . $meses[$i-1]]);

               $em->persist($desglose[0]);
            }
        }

        try {
            $em->flush();
        } catch (Exception $e) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }

        return new JsonResponse(['success' => true]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function aprobarAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $store = json_decode($request->get('store'));

        for ($i = 0, $iMax = count($store); $i < $iMax; $i++) {
            $entity = $em->getRepository('PortadoresBundle:PlanificacionCombustibleTecn')->find($store[$i]->id);
            $entity->setAprobada(true);
            try {
                $em->persist($entity);
                $em->flush();
            } catch (Exception $ex) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
            }
        }
        return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Planificación aprobada con éxito.'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function desaprobarAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $store = json_decode($request->get('store'));
        for ($i = 0, $iMax = count($store); $i < $iMax; $i++) {
            $entity = $em->getRepository('PortadoresBundle:PlanificacionCombustibleTecn')->find($store[$i]->id);
            $entity->setAprobada(false);
            try {
                $em->persist($entity);
                $em->flush();
            } catch (Exception $ex) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
            }
        }
        return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Planificaciones desaprobada con éxito.'));
    }
}