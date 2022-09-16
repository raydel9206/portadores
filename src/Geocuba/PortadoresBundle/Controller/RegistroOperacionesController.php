<?php

namespace Geocuba\PortadoresBundle\Controller;

use Exception;
use Geocuba\PortadoresBundle\Entity\Caldera;
use Geocuba\PortadoresBundle\Entity\EquipoTecnologico;
use Geocuba\PortadoresBundle\Entity\RegistroOperacion;
use Geocuba\PortadoresBundle\Entity\RegistroOperacionCaldera;
use Geocuba\PortadoresBundle\Entity\RegistroOperacionMontacarga;
use Geocuba\PortadoresBundle\Util\FechaUtil;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class RegistroOperacionesController extends Controller
{
    use ViewActionTrait;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loadAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $tipo = $request->get('tipo');
        $equipoId = $request->get('equipo_tecnologico_id');
        $mes = $request->get('mes');
        $anno = $request->get('anno');
        $inicioMes = date_create_from_format('Y-m-d H:i:s', $anno . '-' . $mes . '-01 00:00:00');
        $finalMes = date_create_from_format('Y-m-d H:i:s', FechaUtil::getUltimoDiaMes($mes, $anno));

        switch ($tipo) {
            case 'calderas':
                /** @var RegistroOperacionCaldera $registro_er */
                $registro_er = $em->getRepository('PortadoresBundle:RegistroOperacionCaldera');
                break;
            case 'montacargas':
                /** @var RegistroOperacionMontacarga $registro_er */
                $registro_er = $em->getRepository('PortadoresBundle:RegistroOperacionMontacarga');
                break;
            default:
                /** @var RegistroOperacion $registro_er */
                $registro_er = $em->getRepository('PortadoresBundle:RegistroOperacion');
        }

        $entities = $registro_er->findAllBy($equipoId, $inicioMes, $finalMes);

        $_data = array_map(static function ($entity) {
            /** @var RegistroOperacion $entity */
            $data = [
                'id' => $entity->getId(),
                'fecha' => $entity->getFecha()->format('d/m/Y'),
                'hora_arranque' => $entity->getHoraArranque()->format('g:i A'),
                'hora_parada' => $entity->getHoraParada()->format('g:i A'),
                'consumo_real' => $entity->getConsumoReal(),
                'consumo_normado' => $entity->getConsumoNormado(),
                'combustible_inicial' => $entity->getCombustibleInicial(),
                'combustible_abastecido' => $entity->getCombustibleAbastecido(),
                'combustible_final' => $entity->getCombustibleFinal(),
                'equipo_tecnologico_id' => $entity->getEquipoTecnologico()->getId(),
                'equipo_tecnologico_nombre' => $entity->getEquipoTecnologico()->getDescripcion(),
                'actividad_id' => $entity->getActividad() ? $entity->getActividad()->getId() : null,
                'actividad_nombre' => $entity->getActividad() ? $entity->getActividad()->getNombre() : null
            ];

            if ($entity instanceof RegistroOperacionCaldera) {
                /** @var RegistroOperacionCaldera $entity */
                $data = array_merge($data, [
                    'hora_arranque_recirculacion' => $entity->getHoraArranqueRecirculacion()->format('g:i A'),
                    'hora_parada_recirculacion' => $entity->getHoraParadaRecirculacion()->format('g:i A'),
                    'consumo_real_recirculacion' => $entity->getConsumoRealRecirculacion(),
                    'consumo_normado_recirculacion' => $entity->getConsumoNormadoRecirculacion()
                ]);
            } else if ($entity instanceof RegistroOperacionMontacarga) {
                /** @var RegistroOperacionMontacarga $entity */
                $data = array_merge($data, [
                    'horametro_arranque' => $entity->getHorametroArranque(),
                    'horametro_parada' => $entity->getHorametroParada(),
                    'horas_trabajadas' => $entity->getTiempoTrabajado()
                ]);
            }

            return $data;
        }, $entities);

        return new JsonResponse(['success' => true, 'rows' => $_data]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $equipoTecnologicoId = trim($request->get('equipo_tecnologico_id'));
        $actividadId = trim($request->get('actividad_id'));
        $fecha = trim($request->get('fecha'));
        $consumoReal = trim($request->get('consumo_real'));
        $horaArranque = date_create_from_format('g:i A', trim($request->get('hora_arranque')));
        $horaParada = date_create_from_format('g:i A', trim($request->get('hora_parada')));
//        $consumoNormado = trim($request->get('consumo_normado'));
        $combustibleInicial = trim($request->get('combustible_inicial'));
        $combustibleAbastecido = trim($request->get('combustible_abastecido'));
        $combustibleFinal = trim($request->get('combustible_final'));

        // Caldera
        $horaArranqueRecirculacion = date_create_from_format('g:i A', trim($request->get('hora_arranque_recirculacion')));
        $horaParadaRecirculacion = date_create_from_format('g:i A', trim($request->get('hora_parada_recirculacion')));
        $consumoRealRecirculacion = trim($request->get('consumo_real'));
//        $consumoNormadoRecirculacion = trim($request->get('consumo_normado'));

        // Montacarga
        $horametroArranque = trim($request->get('horametro_arranque'));
        $horametroParada = trim($request->get('horametro_parada'));

        /** @var EquipoTecnologico $equipoTecnologico */
        $equipoTecnologico = $em->getRepository('PortadoresBundle:EquipoTecnologico')->find($equipoTecnologicoId);
        $fechaObj = date_create_from_format('d/m/Y', $fecha);

        switch ($equipoTecnologico->getDenominacionTecnologica()->getId()) {
            case 'static_tec_denomination_1':
                $registro = new RegistroOperacionCaldera();
                break;
            case 'static_tec_denomination_3':
                $registro = new RegistroOperacionMontacarga();
                break;
            default:
                $registro = new RegistroOperacion();
                if ($combustibleInicial !== null && $combustibleInicial !== '') $consumoReal = $combustibleInicial + $combustibleAbastecido - $combustibleFinal;
        }

        /** @var RegistroOperacion $registro */
        $registro
            ->setFecha($fechaObj)
            ->setEquipoTecnologico($equipoTecnologico)
            ->setActividad($em->find('PortadoresBundle:Actividad', $actividadId))
            ->setConsumoReal($consumoReal)
            ->setHoraArranque($horaArranque ?: date_create_from_format('g:i A', '00:00 AM'))
            ->setHoraParada($horaParada ?: date_create_from_format('g:i A', '00:00 AM'))
            ->setCombustibleInicial($combustibleInicial !== '' ?: 0)
            ->setCombustibleAbastecido($combustibleAbastecido !== '' ?: 0)
            ->setCombustibleFinal($combustibleFinal !== '' ?: 0)
            ->setIndiceNormado($equipoTecnologico->getNorma());

        $tiempoTrabajado = $registro->getTiempoTrabajado(false) / 60;
        $registro->setNivelActividadReal($tiempoTrabajado);

        if ($registro instanceof RegistroOperacionCaldera) {
            /** @var RegistroOperacionCaldera $registro */
            $registro
                ->setHoraArranqueRecirculacion($horaArranqueRecirculacion)
                ->setHoraParadaRecirculacion($horaParadaRecirculacion)
                ->setConsumoRealRecirculacion($consumoRealRecirculacion);
            $tiempoTrabajadoRecirc = $registro->getTiempoTrabajadoRecirc(false) / 60;
            /** @var Caldera $equipoTecnologico */
            $registro
                ->setConsumoNormadoRecirculacion($tiempoTrabajadoRecirc * $equipoTecnologico->getNormaRecirculacion())
                ->setNivelActRealRecirculacion($tiempoTrabajadoRecirc)
                ->setIndiceNormadoRecirculacion($equipoTecnologico->getNormaRecirculacion());
        } elseif ($registro instanceof RegistroOperacionMontacarga) {
            /** @var RegistroOperacionMontacarga $registro */
            $registro
                ->setHorametroArranque($horametroArranque)
                ->setHorametroParada($horametroParada);
        }

        $registro->setConsumoNormado($tiempoTrabajado * $equipoTecnologico->getNorma());

        try {
            $em->persist($registro);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Operación adicionada con éxito.'));
        } catch (Exception $ex) {
            var_dump($ex->getMessage());
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $id = trim($request->get('id'));

        $entity = $em->getRepository('PortadoresBundle:RegistroOperacion')->find($id);
        if (!$entity)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El registro no se encuentra disponible.'));

        $em->remove($entity);

        try {
            $em->flush();
        } catch (Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error eliminando los datos, si el error persiste contacte a su administrador.'));
        }

        return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Registro eliminado con éxito.'));
    }
}