<?php

namespace Geocuba\PortadoresBundle\Controller;

use Exception;
use Geocuba\PortadoresBundle\Entity\AsignacionTecnologicos;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\PortadoresBundle\Util\Utiles;
use Symfony\Component\HttpKernel\Exception\HttpException;


class AsignacionTecnologicosController extends Controller
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
//        $tipoCombustibleId = trim($request->get('tipo_combustible_id'));
        $anno = $request->get('anno');
        $mes = $request->get('mes');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($unidadId), $_unidades);

        $entities = $em->getRepository('PortadoresBundle:AsignacionTecnologicos')->findAllBy($_unidades, $mes, $anno);

        //var_dump($entities);

        $_data = array_map(static function ($entity) {
            /** @var AsignacionTecnologicos $entity */
            return [
                'id' => $entity->getId(),
                'tipo_combustible_id' => $entity->getTipoCombustible()->getId(),
                'tipo_combustible_nombre' => $entity->getTipoCombustible()->getNombre(),
                'unidad_id' => $entity->getUnidad()->getId(),
                'unidad_nombre' => $entity->getUnidad()->getNombre(),
                'cantidad' => $entity->getCantidad(),
                'fecha' => $entity->getFecha()->format('d/m/Y')
            ];
        }, $entities);

        return new JsonResponse(['success' => true, 'rows' => $_data]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addAction(Request $request): ?JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $fecha = trim($request->get('fecha'));
        $cantidad = trim($request->get('cantidad'));
        $tipoCombustibleId = trim($request->get('tipo_combustible_id'));
        $unidadId = trim($request->get('unidad_id'));

        $entity = new AsignacionTecnologicos();
        $entity->setFecha(date_create_from_format('d/m/Y', $fecha));
        $entity->setCantidad($cantidad);
        $entity->setUnidad($em->getRepository('PortadoresBundle:Unidad')->find($unidadId));
        $entity->setTipoCombustible($em->getRepository('PortadoresBundle:TipoCombustible')->find($tipoCombustibleId));

        $em->persist($entity);
        try {
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Asignación adicionada con éxito.'));
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
        $id = trim($request->get('id'));
        $fecha = trim($request->get('fecha'));
        $cantidad = trim($request->get('cantidad'));
        $tipoCombustibleId = trim($request->get('tipo_combustible_id'));
        $unidadId = trim($request->get('unidad_id'));

        $entity = $em->find('PortadoresBundle:AsignacionTecnologicos', $id);
        if (!$entity) return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'La asignación no se encuentra disponible.'));

        $entity->setFecha(date_create_from_format('d/m/Y', $fecha));
        $entity->setCantidad($cantidad);
        $entity->setUnidad($em->getRepository('PortadoresBundle:Unidad')->find($unidadId));
        $entity->setTipoCombustible($em->getRepository('PortadoresBundle:TipoCombustible')->find($tipoCombustibleId));

        $em->persist($entity);
        try {
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Asignación modificada con éxito.'));
        } catch (Exception $ex) {
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

        $entity = $em->find('PortadoresBundle:AsignacionTecnologicos', $id);
        if (!$entity)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'La asignación no se encuentra disponible.'));

        $em->remove($entity);

        try {
            $em->flush();
        } catch (Exception $ex) {
            if (strpos($ex->getMessage(), 'SQLSTATE[23503]')) {
                throw new HttpException(500, 'La asignación no se puede eliminar porque se encuentra en uso');
            }
        }

        return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Asignación eliminada con éxito.'));
    }
}