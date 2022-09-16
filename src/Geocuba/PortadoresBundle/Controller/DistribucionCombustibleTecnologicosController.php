<?php

namespace Geocuba\PortadoresBundle\Controller;

use Doctrine\ORM\EntityManager;
use Exception;
use Geocuba\PortadoresBundle\Entity\AsignacionTecnologicos;
use Geocuba\PortadoresBundle\Entity\DistribucionCombustibleTecnologicos;
use Geocuba\PortadoresBundle\Entity\DistribucionCombustibleTecnologicosDesglose;
use Geocuba\PortadoresBundle\Entity\EquipoTecnologico;
use Geocuba\PortadoresBundle\Entity\PlanificacionCombustibleTecnDesglose;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DistribucionCombustibleTecnologicosController extends Controller
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
        $tipoCombustibleId = trim($request->get('tipo_combustible_id'));
        $anno = $request->get('anno');
        $mes = $request->get('mes');

        /** @var DistribucionCombustibleTecnologicos $distribucion */
        $distribucion = $em->getRepository('PortadoresBundle:DistribucionCombustibleTecnologicos')->findOneBy(['unidad' => $unidadId, 'mes' => $mes, 'anno' => $anno, 'tipoCombustible' => $tipoCombustibleId]);
        if (!$distribucion) return new JsonResponse(['success' => true, 'rows' => [], 'cls', 'warning', 'message' => 'No se ha distribuido combustible para el mes y tipo de combustible seleccionado.']);

        /** @var EntityManager $em */
        $qb = $em->createQueryBuilder();
        $desglose = $qb->select('desglose')
            ->from('PortadoresBundle:DistribucionCombustibleTecnologicosDesglose', 'desglose')
            ->join('desglose.equipo', 'equipo')
            ->where($qb->expr()->eq('desglose.distribucionCombustibleTecnologicos', ':distribucion'))
            ->setParameter('distribucion', $distribucion->getId())
            ->orderBy('equipo.descripcion', 'asc')
            ->getQuery()->getResult();

        $_data = [];
        foreach ($desglose as $entity) {
            /** @var DistribucionCombustibleTecnologicosDesglose $entity */

            $plan = $em->getRepository('PortadoresBundle:PlanificacionCombustibleTecn')->findOneBy(['equipoTecnologico' => $entity->getEquipo(), 'anno' => $anno, 'tipoCombustible' => $tipoCombustibleId]);
            if ($plan) {
                /** @var PlanificacionCombustibleTecnDesglose $planMes */
                $planMes = $em->getRepository('PortadoresBundle:PlanificacionCombustibleTecnDesglose')->findOneBy(['planificacionCombustibleTecn' => $plan, 'mes' => $mes]);
                $cantidadPlan = $planMes->getCantCombustible();

                $_data[] = [
                    'id' => $entity->getId(),
                    'cantidad' => (float) $entity->getCantidad(),
                    'cantidad_plan' => (float) $cantidadPlan,
                    'incremento_reduccion' => (float) $entity->getCantidad() - $cantidadPlan,
                    'equipo_id' => $entity->getEquipo()->getId(),
                    'equipo_desc' => $entity->getEquipo()->getDescripcion(),
                    'indice_consumo' => $entity->getIndiceConsumo(),
                    'precio_comb' => $entity->getPrecioComb()
                ];
            }
        }

        $asignaciones = $em->getRepository('PortadoresBundle:AsignacionTecnologicos')->findAllBy([$unidadId], $mes, $anno, $tipoCombustibleId);
        $cantidadAsignacion = array_reduce($asignaciones, static function($temp, $asignacion) {
            /** @var AsignacionTecnologicos $asignacion */
            return $temp + (float) $asignacion->getCantidad();
        }, 0);

        $extraData = [
            'tipo_combustible_id' => $distribucion->getTipoCombustible()->getId(),
            'tipo_combustible_nombre' => $distribucion->getTipoCombustible()->getNombre(),
            'cantidad_asignacion' => $cantidadAsignacion
        ];

        return new JsonResponse(['success' => true, 'rows' => $_data, 'extraData' => $extraData]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function generateAction(Request $request): JsonResponse
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        try {
            $em->transactional(static function ($em) use ($request) {
                /** @var EntityManager $em */
                $tipoCombustibleId = trim($request->get('tipo_combustible_id'));
                $unidadId = trim($request->get('unidad_id'));
                $mes = trim($request->get('mes'));
                $anno = trim($request->get('anno'));

                $distribucion = $em->getRepository('PortadoresBundle:DistribucionCombustibleTecnologicos')->findBy(['unidad' => $unidadId, 'mes' => $mes, 'anno' => $anno, 'tipoCombustible' => $tipoCombustibleId]);
                if ($distribucion)
                    foreach ($distribucion as $d) $em->remove($d);

                $equipos = $em->getRepository('PortadoresBundle:EquipoTecnologico')->findBy(['unidad' => $unidadId, 'tipoCombustible' => $tipoCombustibleId]);
                if (!$equipos){
                    $equipos = $em->getRepository('PortadoresBundle:Caldera')->findBy(['unidad' => $unidadId, 'tipoCombustibleRecirculacion' => $tipoCombustibleId]);
                    if (!$equipos) throw new Exception('No exiten equipos tecnológicos registrados en la unidad y el tipo de combustible seleccionado.', 1000);
                }

                $tipoCombustible = $em->find('PortadoresBundle:TipoCombustible', $tipoCombustibleId);
                $distribucion = new DistribucionCombustibleTecnologicos();
                $distribucion->setMes($mes)
                    ->setAnno($anno)
                    ->setUnidad($em->find('PortadoresBundle:Unidad', $unidadId))
                    ->setTipoCombustible($tipoCombustible);

                $em->persist($distribucion);

                $count = 0;
                foreach ($equipos as $equipo) {
                    /** @var EquipoTecnologico $equipo */
                    $plan = $em->getRepository('PortadoresBundle:PlanificacionCombustibleTecn')->findOneBy(['equipoTecnologico' => $equipo, 'anno' => $anno, 'aprobada' => true, 'tipoCombustible' => $tipoCombustibleId]);
                    if ($plan) {
                        $count++;
                        /** @var PlanificacionCombustibleTecnDesglose $planMes */
                        $planMes = $em->getRepository('PortadoresBundle:PlanificacionCombustibleTecnDesglose')->findOneBy(['planificacionCombustibleTecn' => $plan, 'mes' => $mes]);
                        $cantidadPlan = $planMes->getCantCombustible();

                        $desglose = new DistribucionCombustibleTecnologicosDesglose();
                        $desglose->setEquipo($equipo)
                            ->setIndiceConsumo($equipo->getNorma())
                            ->setPrecioComb($tipoCombustible->getPrecioTD())
                            ->setCantidad($cantidadPlan)
                            ->setDistribucionCombustibleTecnologicos($distribucion);

                        $em->persist($desglose);
                    }
                }

                if ($count === 0)
                    throw new Exception('No exiten datos suficientes para generar una distribución. Verifique que exista una planificación y esté aprobada.', 1000);

            });

            $em->clear();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Distribución generada con éxito.'));
        } catch (\Throwable $e) {
            if ($e->getCode() === 1000) return new JsonResponse(['success' => false, 'cls' => 'warning', 'message' => $e->getMessage()]);

            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }

//        try {
//            $em->flush();
//            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Distribución generada con éxito.'));
//        } catch (Exception $ex) {
//        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function saveAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $records = json_decode($request->get('data'), true);

        foreach ($records as $record) {
            /** @var DistribucionCombustibleTecnologicosDesglose $recordDb */
            $recordDb = $em->find('PortadoresBundle:DistribucionCombustibleTecnologicosDesglose', $record['id']);
            $recordDb->setCantidad($record['cantidad']);

            $em->persist($recordDb);
        }

        try {
            $em->flush();
        } catch (Exception $e) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }

        return new JsonResponse(['success' => true, 'message' => 'Datos actualizados con éxito']);
    }
}