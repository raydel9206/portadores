<?php

namespace Geocuba\PortadoresBundle\Controller;

use DateInterval;
use Doctrine\ORM\EntityManager;
use Exception;
use Geocuba\PortadoresBundle\Entity\EntradaSalidaCombustible;
use Geocuba\PortadoresBundle\Entity\FactorConversion;
use Geocuba\PortadoresBundle\Entity\MedicionDiariaTanque;
use Geocuba\PortadoresBundle\Entity\Tanque;
use Geocuba\PortadoresBundle\Util\FechaUtil;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

class MedicionDiariaTanqueController extends Controller
{
    use ViewActionTrait;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loadAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $tanqueId = $request->get('tanque_id');
        $mes = $request->get('mes');
        $anno = $request->get('anno');
        $inicioMes = date_create_from_format('Y-m-d H:i:s', $anno . '-' . $mes . '-01 00:00:00');
        $finalMes = date_create_from_format('Y-m-d H:i:s', FechaUtil::getUltimoDiaMes($mes, $anno));

        $mediciones = $em->getRepository('PortadoresBundle:MedicionDiariaTanque')->findAllBy($tanqueId, $inicioMes, $finalMes);

        $_data = array_map(static function ($medicion) {
            /** @var MedicionDiariaTanque $medicion */
            return [
                'id' => $medicion->getId(),
                'fecha' => $medicion->getFecha()->format('d/m/Y'),
                'medicion' => $medicion->getMedicion(),
                'existencia' => $medicion->getExistencia(),
                'consumo' => $medicion->getConsumo(),
                'tanque_id' => $medicion->getTanque()->getId(),
                'tanque_numero_inventario' => $medicion->getTanque()->getNumeroInventario(),
                'descripcion' => $medicion->getTanque()->getDescripcion(),
            ];
        }, $mediciones);

        return new JsonResponse(['rows' => $_data]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function addAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $tanqueId = $request->get('tanque_id');
        $medicion = $request->get('medicion');

        $mes = $request->get('mes');
        $anno = $request->get('anno');
        $inicioMes = date_create_from_format('Y-m-d H:i:s', $anno . '-' . $mes . '-01 00:00:00');
        $finalMes = date_create_from_format('Y-m-d H:i:s', FechaUtil::getUltimoDiaMes($mes, $anno));

        /** @var Tanque $tanque */
        $tanque = $em->getRepository('PortadoresBundle:Tanque')->find($tanqueId);
        if (!$tanque) return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El tanque no se encuentra disponible.'));

        // Existencia segun nivel
        if (!$tanque->getCilindro()) $existencia = $tanque->calcularNivel($medicion);
        else {
            $factor = FactorConversion::getFactorByUm(
                $em,
                $tanque->getTipoCombustible()->getPortadorid()->getId(),
                'static_measure_unit_1',
                $tanque->getUnidadMedida()->getId());

            if (!$factor)
                return new JsonResponse(['success' => false, 'message' => sprintf('No esta registrada en el sistema la conversión de litros a <strong>%s</strong>', $tanque->getUnidadMedida()->getNombre())]);
            $existencia = round($medicion * $factor, 4);
        }

        // Fecha a continuacion de la ultima medicion
        $medicionesAnteriores = $em->getRepository('PortadoresBundle:MedicionDiariaTanque')->findAllBy($tanqueId, $inicioMes, $finalMes, 'DESC');
        $fecha = !$medicionesAnteriores ? $inicioMes : date_create_from_format('d/m/Y H:i:s', $medicionesAnteriores[0]->getFecha()->format('d/m/Y H:i:s'))->add(new DateInterval('P1D'));

        // Insertando datos
        /** @var EntityManager $em */
        try {
            $em->transactional(static function ($em) use ($fecha, $existencia, $medicion, $tanque) {
                /** @var EntityManager $em */

                $medicionDiaria = new MedicionDiariaTanque();
                $medicionDiaria->setFecha($fecha)
                    ->setExistencia($existencia)
                    ->setMedicion($medicion);
                $tanque->addMedicionDiaria($medicionDiaria);
                $em->persist($tanque);

                $fechaAnterior = date_create_from_format('d/m/Y H:i:s', $medicionDiaria->getFecha()->format('d/m/Y H:i:s'))->sub(new DateInterval('P1D'));
                $medicionAnterior = $em->getRepository('PortadoresBundle:MedicionDiariaTanque')->findOneBy(['tanque' => $tanque->getId(), 'fecha' => $fechaAnterior]);

                if ($medicionAnterior) {
                    $entradasDiaAnterior = $em->getRepository('PortadoresBundle:EntradaSalidaCombustible')->findBy(['entrada' => true, 'fecha' => $fechaAnterior]);
                    $totalEntrada = array_reduce($entradasDiaAnterior, static function ($total, $entrada) {
                        /** @var EntradaSalidaCombustible $entrada */
                        return (float)$total + (float)$entrada->getCantidad();
                    }, 0);

                    $consumo = $medicionAnterior->getExistencia() + $totalEntrada - $medicionDiaria->getExistencia();
                    $medicionAnterior->setConsumo($consumo);
                    $em->persist($medicionAnterior);
                }

            });
            $em->clear();
        } catch (Throwable $e) {
            var_dump($e->getMessage());
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }

        return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Medición adicionada con éxito.'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $mes = $request->get('mes');
        $anno = $request->get('anno');
        $finalMes = date_create_from_format('Y-m-d H:i:s', FechaUtil::getUltimoDiaMes($mes, $anno));

        /** @var Tanque $tanque */
        $tanque = $em->getRepository('PortadoresBundle:Tanque')->find($request->get('tanque_id'));
        if (!$tanque) return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El tanque no se encuentra disponible.'));

        $medicionDiaria = $em->getRepository('PortadoresBundle:MedicionDiariaTanque')->find($request->get('id'));
        if (!$medicionDiaria) return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'La medición no se encuentra disponible.'));

        //si hay mediciones en fechas mayores no se puede eliminar
        $medicionesPosteriores = $tanque->getMedicionesDiarias()->filter(static function ($md) use ($medicionDiaria, $finalMes) {
            /** @var MedicionDiariaTanque $md */
            return $md->getFecha() > $medicionDiaria->getFecha() && $md->getFecha() <= $finalMes;
        })->toArray();
        if (count($medicionesPosteriores) > 0) return new JsonResponse(['success' => false, 'message' => 'No se puede eliminar. Elimine las mediciones con fecha posterior primero.']);

        try {
            /** @var EntityManager $em */
            $em->transactional(static function ($em) use ($tanque, $medicionDiaria) {
                /** @var EntityManager $em */
                // remover el consumo del dia anterior
                $fechaAnterior = date_create_from_format('d/m/Y H:i:s', $medicionDiaria->getFecha()->format('d/m/Y H:i:s'))->sub(new DateInterval('P1D'));
                $medicionAnterior = $tanque->getMedicionesDiarias()->filter(static function ($medicionDiaria) use ($fechaAnterior) {
                    /** @var MedicionDiariaTanque $medicionDiaria */
                    return $medicionDiaria->getFecha() == $fechaAnterior;
                });
                if (!$medicionAnterior->isEmpty()) $em->persist($medicionAnterior->first()->setConsumo(null));

                // remover medicion
                $tanque->removeMedicionDiaria($medicionDiaria);
                $em->persist($tanque);
            });
        } catch (Throwable $e) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }

        return new JsonResponse(['success' => true, 'message' => 'La medición ha sido eliminada con éxito']);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function recalcularConsumoAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();

        /** @var MedicionDiariaTanque $medicionDiaria */
        $medicionDiaria = $em->getRepository('PortadoresBundle:MedicionDiariaTanque')->find($request->get('medicion_id'));
        if (!$medicionDiaria) return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'La medición no se encuentra disponible.'));

        $fechaSiguiente = date_create_from_format('d/m/Y H:i:s', $medicionDiaria->getFecha()->format('d/m/Y H:i:s'))->add(new DateInterval('P1D'));

        /** @var MedicionDiariaTanque[] $medicionDiariaSiguiente */
        $medicionDiariaSiguiente = $em->getRepository('PortadoresBundle:MedicionDiariaTanque')->findByDate($fechaSiguiente);
        if (!$medicionDiariaSiguiente) return new JsonResponse(['success' => false, 'message' => 'No existen datos suficientes para calcular el consumo.']);

        $entradasDia = $em->getRepository('PortadoresBundle:EntradaSalidaCombustible')->findBy(['entrada' => true, 'fecha' => $medicionDiaria->getFecha()]);
        $totalEntrada = array_reduce($entradasDia, static function ($total, $entrada) {
            /** @var EntradaSalidaCombustible $entrada */
            return (float)$total + (float)$entrada->getCantidad();
        }, 0);

        $consumo = (float)$medicionDiaria->getExistencia() + (float)$totalEntrada - (float)$medicionDiariaSiguiente[0]->getExistencia();
        $medicionDiaria->setConsumo($consumo);

        try {
            $em->persist($medicionDiaria);
            $em->flush();
            return new JsonResponse(['success' => true, 'message' => 'Consumo calculado con éxito.']);
        } catch (Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }
}