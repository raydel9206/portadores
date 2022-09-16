<?php

namespace Geocuba\PortadoresBundle\Controller;

use Doctrine\Common\Util\Debug;
use Exception;
use Geocuba\PortadoresBundle\Entity\EntradaSalidaCombustible;
use Geocuba\PortadoresBundle\Entity\FactorConversion;
use Geocuba\PortadoresBundle\Entity\Tanque;
use Geocuba\PortadoresBundle\Util\FechaUtil;
use Geocuba\Utils\ViewActionTrait;
use function PhpParser\canonicalize;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class EntradaSalidaCombustibleController extends Controller
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

        $entradasSalidas = $em->getRepository('PortadoresBundle:EntradaSalidaCombustible')->findAllBy($tanqueId, $inicioMes, $finalMes);

        $_data = array_map(static function ($entradaSalida) {
            /** @var EntradaSalidaCombustible $entradaSalida */
            return [
                'id' => $entradaSalida->getId(),
                'fecha' => $entradaSalida->getFecha()->format('d/m/Y'),
                'medicion_antes' => $entradaSalida->getMedicionAntes(),
                'medicion_despues' => $entradaSalida->getMedicionDespues(),
                'existencia_antes' => $entradaSalida->getExistenciaAntes(),
                'existencia_despues' => $entradaSalida->getExistenciaDespues(),
                'cantidad' => $entradaSalida->getCantidad(),
                'tanque_id' => $entradaSalida->getTanque()->getId(),
                'tanque_numero_inventario' => $entradaSalida->getTanque()->getNumeroInventario(),
                'descripcion' => $entradaSalida->getTanque()->getDescripcion(),
            ];
        }, $entradasSalidas);

        return new JsonResponse(['rows' => $_data]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function ultimaMedidaAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $tanqueId = $request->get('tanque_id');
        $mes = $request->get('mes');
        $anno = $request->get('anno');
        $inicio = date_create_from_format('Y-m-d H:i:s', $anno . '-01-01 00:00:00');
        $final = date_create_from_format('Y-m-d H:i:s', FechaUtil::getUltimoDiaMes($mes, $anno));

        $entradasSalidas = $em->getRepository('PortadoresBundle:EntradaSalidaCombustible')->findAllBy($tanqueId, $inicio, $final, 'DESC');
        $medicionesDiarias = $em->getRepository('PortadoresBundle:MedicionDiariaTanque')->findAllBy($tanqueId, $inicio, $final, 'DESC');

        if (count($entradasSalidas) && count($medicionesDiarias))
            $medicionInicial = $entradasSalidas[0]->getFecha()->format('Y-m-d') >= $medicionesDiarias[0]->getFecha()->format('Y-m-d') ? $entradasSalidas[0]->getMedicionDespues() : $medicionesDiarias[0]->getMedicion();
        elseif (count($entradasSalidas))
            $medicionInicial = $entradasSalidas[0]->getMedicionDespues();
        elseif (count($medicionesDiarias))
            $medicionInicial = $medicionesDiarias[0]->getMedicion();
        else
            $medicionInicial = 0;

        return new JsonResponse(['success' => true, 'data' => $medicionInicial]);
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
//        var_dump($request->get('fecha'));die;
        $fecha = date_create_from_format('d/m/Y', $request->get('fecha'));
        $medicionAntes = $request->get('medicion_antes');
        $medicionDespues = $request->get('medicion_despues');
        $existenciaAntes = $request->get('existencia_antes');
        $existenciaDespues = $request->get('existencia_despues');
        $cantidad = $request->get('cantidad');
//        $entrada = $request->get('entrada') ? true : false;

        /** @var Tanque $tanque */
        $tanque = $em->getRepository('PortadoresBundle:Tanque')->find($tanqueId);
        if (!$tanque) return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El tanque no se encuentra disponible.'));

        $entradaSalida = new EntradaSalidaCombustible();
        $entradaSalida->setFecha($fecha)
            ->setMedicionAntes($medicionAntes)
            ->setMedicionDespues($medicionDespues)
            ->setExistenciaAntes($existenciaAntes)
            ->setExistenciaDespues($existenciaDespues)
            ->setCantidad($cantidad)
            ->setEntrada($cantidad > 0);

        $tanque->addEntradaSalida($entradaSalida);
        $em->persist($tanque);

        try {
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Entrada/Salida adicionada con éxito.'));
        } catch (Exception $ex) {
            Debug::dump($ex);
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function updAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $medicionAntes = $request->get('medicion_antes');
        $medicionDespues = $request->get('medicion_despues');
        $existenciaAntes = $request->get('existencia_antes');
        $existenciaDespues = $request->get('existencia_despues');
        $cantidad = $request->get('cantidad');
        $entrada = $request->get('entrada') ? true : false;

        /** @var EntradaSalidaCombustible $entradaSalida */
        $entradaSalida = $em->getRepository('PortadoresBundle:EntradaSalidaCombustible')->find($id);
        $entradaSalida->setMedicionAntes($medicionAntes)
            ->setMedicionDespues($medicionDespues)
            ->setExistenciaAntes($existenciaAntes)
            ->setExistenciaDespues($existenciaDespues)
            ->setCantidad($cantidad)
            ->setEntrada($entrada);
        $em->persist($entradaSalida);

        try {
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Entrada/Salida adicionada con éxito.'));
        } catch (Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function deleteAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Tanque $tanque */
        $entradaSalida = $em->getRepository('PortadoresBundle:EntradaSalidaCombustible')->find($request->get('id'));
        if (!$entradaSalida) return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'La entrada/salida no se encuentra disponible.'));

        try{
            $em->remove($entradaSalida);
            $em->flush();
            return new JsonResponse(['success' => true, 'message' => 'La medición ha sido eliminada con éxito']);
        } catch (Exception $e){
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function calculateDataAction(Request $request): JsonResponse{
        $em = $this->getDoctrine()->getManager();
        $medicionAntes = $request->get('medicion_antes');
        $medicionDespues = $request->get('medicion_despues');

        $tanque = $em->getRepository('PortadoresBundle:Tanque')->find($request->get('tanque_id'));
        if (!$tanque) return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El tanque no se encuentra disponible.'));

        if (!$tanque->getCilindro()) {
            $existenciaAntes = $medicionAntes ? $tanque->calcularNivel($medicionAntes) : 0;
            $existenciaDespues = $medicionDespues ? $tanque->calcularNivel($medicionDespues) : 0;
        } else {
            $factor = FactorConversion::getFactorByUm(
                $em,
                $tanque->getTipoCombustible()->getPortadorid()->getId(),
                'static_measure_unit_1',
                $tanque->getUnidadMedida()->getId());

            if (!$factor)
                return new JsonResponse(['success' => false, 'message' => sprintf('No esta registrada en el sistema la conversión de litros a <strong>%s</strong>', $tanque->getUnidadMedida()->getNombre())]);

            $existenciaAntes = $medicionAntes ? round($medicionAntes * $factor, 4) : 0;
            $existenciaDespues = $medicionDespues ? round($medicionDespues * $factor, 4) : 0;
        }

        return new JsonResponse([
            'success' => true,
            'data' => [
                'existencia_antes' => $existenciaAntes !== -1 ? $existenciaAntes : 0,
                'existencia_despues' => $existenciaDespues !== -1 ? $existenciaDespues : 0,
                'cantidad' => $existenciaDespues - $existenciaAntes
            ]
        ]);
    }
}