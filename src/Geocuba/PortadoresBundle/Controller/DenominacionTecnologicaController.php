<?php

namespace Geocuba\PortadoresBundle\Controller;

use Exception;
use Geocuba\AdminBundle\Repository\QueryHelper;
use Geocuba\PortadoresBundle\Entity\DenominacionTecnologica;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DenominacionTecnologicaController extends Controller
{
    use ViewActionTrait;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loadAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $_nombre = trim($request->get('nombre'));

        $start = $request->get('start');
        $limit = $request->get('limit');

        $entities = $em->getRepository('PortadoresBundle:DenominacionTecnologica')->findAllBy($_nombre, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:DenominacionTecnologica')->findAllBy($_nombre, $start, $limit, true);

        $_data = array_map(static function($entity) {
            /** @var DenominacionTecnologica $entity */
            return [
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre()
            ];
        }, $entities);

        return new JsonResponse(['success' => true, 'rows' => $_data, 'total' => $total]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $nombre = trim($request->get('nombre'));

        $denominacion_er = $em->getRepository('PortadoresBundle:DenominacionTecnologica');
        $duplicates = QueryHelper::findByFieldValue($denominacion_er, 'nombre', $nombre, 'visible', true, null, null);
        if ($duplicates) return new JsonResponse(['success' => false, 'message' => 'Ya existe una denominación con ese nombre.']);

        $entity = new DenominacionTecnologica();
        $entity->setNombre($nombre);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Denominación tecnológica adicionada con éxito.'));
        } catch (Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $denominacion_er = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:DenominacionTecnologica');

        $id = $request->get('id');
        $nombre = trim($request->get('nombre'));

        $entity = $denominacion_er->find($id);
        if (!$entity) return new JsonResponse(['success' => false, 'message' => 'La denominación no se encuentra disponible']);

        $duplicates = QueryHelper::findByFieldValue($denominacion_er, 'nombre', $nombre, 'visible', true, null, null);
        if ($duplicates) return new JsonResponse(['success' => false, 'message' => 'Ya existe una denominación con ese nombre.']);

        $entity->setNombre($nombre);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(['success' => true, 'cls' => 'success', 'message' => 'Denominación de vehículo modificada con éxito.']);
        } catch (Exception $ex) {
            return new JsonResponse(['success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.']);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function delAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
//        $staticDenominations = ['static_tec_denomination_1', 'static_tec_denomination_2', 'static_tec_denomination_3'];
//        $exist = array_filter($staticDenominations, static function($item) use($id) { return $item === $id; });
        if (strpos($id, 'static') !== false)
            return new JsonResponse(['success' => false, 'message' => 'La denominación seleccionada es resevada del sistema. No es posible eliminarla.']);

        $entity = $em->getRepository('PortadoresBundle:DenominacionTecnologica')->find($id);
        if (!$entity) return new JsonResponse(['success' => false, 'message' => 'La denominación no se encuentra disponible']);

        $em->remove($entity);
        try {
            $em->flush();
            return new JsonResponse(['success' => true, 'cls' => 'success', 'message' => 'Denominación de vehículo eliminada con éxito.']);
        } catch (Exception $ex) {
            if (strpos($ex->getMessage(), 'SQLSTATE[23503]')) {
                throw new HttpException(500, sprintf('La denominación %s no se puede eliminar porque se encuentra en uso', $entity->getNombre()));
            }
            return new JsonResponse(['success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.']);
        }
    }
}