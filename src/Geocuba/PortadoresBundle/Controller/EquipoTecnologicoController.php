<?php

namespace Geocuba\PortadoresBundle\Controller;

use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Geocuba\PortadoresBundle\Entity\Caldera;
use Geocuba\PortadoresBundle\Entity\EquipoTecnologico;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\PortadoresBundle\Util\Utiles;
use Symfony\Component\HttpKernel\Exception\HttpException;


class EquipoTecnologicoController extends Controller
{
    use ViewActionTrait;

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function loadAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $nunidadid = trim($request->get('unidad'));
        $tipo = $request->get('tipo');

        $start = $request->get('start');
        $limit = $request->get('limit');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $entities = $em->getRepository('PortadoresBundle:EquipoTecnologico')->findAllBy($_unidades, $tipo, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:EquipoTecnologico')->findAllBy($_unidades, $tipo, $start, $limit, true);

//        var_dump($entities);die;

        $_data = array_map(static function ($entity) {
            /** @var EquipoTecnologico $entity */
            /** @var Caldera|null $caldera */
            $caldera = $entity instanceof Caldera ? $entity : null;

            return [
                'id' => $entity->getId(),
                'numero_inventario' => $entity->getNroInventario(),
                'descripcion' => $entity->getDescripcion(),
                'norma' => $entity->getNorma(),
                'norma_fabricante' => $entity->getNormaFabricante(),
                'tipo_combustible_id' => $entity->getTipoCombustible()->getId(),
                'tipo_combustible_nombre' => $entity->getTipoCombustible()->getNombre(),
                'actividad_id' => $entity->getActividad()->getId(),
                'actividad_nombre' => $entity->getActividad()->getNombre(),
                'modelo_id' => $entity->getModeloTecnologico()->getId(),
                'modelo_nombre' => $entity->getModeloTecnologico()->getNombre(),
                'marca_id' => $entity->getModeloTecnologico()->getMarcaTecnologica()->getId(),
                'marca_nombre' => $entity->getModeloTecnologico()->getMarcaTecnologica()->getNombre(),
                'unidad_id' => $entity->getUnidad()->getId(),
                'unidad_nombre' => $entity->getUnidad()->getNombre(),
                'denominacion_id' => $entity->getDenominacionTecnologica()->getId(),
                'denominacion_nombre' => $entity->getDenominacionTecnologica()->getNombre(),
                // Calderas
                'norma_recirculacion' => $caldera ? $caldera->getNormaRecirculacion() : null,
                'norma_fabricante_recirculacion' => $caldera ? $caldera->getNormaRecirculacionFabricante() : null,
                'tipo_combustible_recirculacion_id' => $caldera ? $caldera->getTipoCombustible()->getId() : null,
                'tipo_combustible_recirculacion_nombre' => $caldera ? $caldera->getTipoCombustible()->getNombre() : null,
            ];
        }, $entities);

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addAction(Request $request): ?JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $nroInventario = trim($request->get('numero_inventario'));
        $descripcion = trim($request->get('descripcion'));
        $norma = trim($request->get('norma'));
        $normaFabricante = trim($request->get('norma_fabricante'));
        $tipoCombustibleId = trim($request->get('tipo_combustible_id'));
        $actividadId = trim($request->get('actividad_id'));
        $modeloId = trim($request->get('modelo_id'));
        $denominacionId = trim($request->get('denominacion_id'));
        $unidadId = trim($request->get('unidad_id'));
        // Calderas
        $normaRecirculacion = trim($request->get('norma_recirculacion'));
        $normaRecirculacionFabricante = trim($request->get('norma_recirculacion_fabricante'));
        $tipoCombustibleRecirculacionId = trim($request->get('tipo_combustible_recirculacion_id'));

        $duplicates = $em->getRepository('PortadoresBundle:EquipoTecnologico')->findDuplicate($nroInventario, $descripcion, $unidadId);
        if ($duplicates) return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe un equipo tecnológico con el mismo numero de inventario o descripción en la unidad seleccionada.'));

        if ($denominacionId === 'static_tec_denomination_1') $entity = new Caldera();
        else $entity = new EquipoTecnologico();

        $entity
            ->setDenominacionTecnologica($em->getRepository('PortadoresBundle:DenominacionTecnologica')->find($denominacionId))
            ->setNroInventario($nroInventario)
            ->setDescripcion($descripcion)
            ->setNorma($norma)
            ->setNormaFabricante($normaFabricante)
            ->setTipoCombustible($em->getRepository('PortadoresBundle:TipoCombustible')->find($tipoCombustibleId))
            ->setActividad($em->getRepository('PortadoresBundle:Actividad')->find($actividadId))
            ->setUnidad($em->getRepository('PortadoresBundle:Unidad')->find($unidadId))
            ->setModeloTecnologico($em->getRepository('PortadoresBundle:ModeloTecnologico')->find($modeloId));
        // Calderas
        if ($entity instanceof Caldera) {
            $entity
                ->setNormaRecirculacion($normaRecirculacion)
                ->setNormaRecirculacionFabricante($normaRecirculacionFabricante)
                ->setTipoCombustibleRecirculacion($em->getRepository('PortadoresBundle:TipoCombustible')->find($tipoCombustibleRecirculacionId));
        }

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Equipo tecnológico adicionado con éxito.'));
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
        $nroInventario = trim($request->get('numero_inventario'));
        $descripcion = trim($request->get('descripcion'));
        $norma = trim($request->get('norma'));
        $normaFabricante = trim($request->get('norma_fabricante'));
        $tipoCombustibleId = trim($request->get('tipo_combustible_id'));
        $actividadId = trim($request->get('actividad_id'));
        $modeloId = trim($request->get('modelo_id'));
        $unidadId = trim($request->get('unidad_id'));
        // Calderas
        $normaRecirculacion = trim($request->get('norma_recirculacion'));
        $normaRecirculacionFabricante = trim($request->get('norma_recirculacion_fabricante'));
        $tipoCombustibleRecirculacionId = trim($request->get('tipo_combustible_recirculacion_id'));

        $duplicates = $em->getRepository('PortadoresBundle:EquipoTecnologico')->findDuplicate($nroInventario, $descripcion, $unidadId, $id);
        if ($duplicates) return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe un equipo tecnológico con el mismo numero de inventario o descripción en la unidad seleccionada.'));

        /** @var EquipoTecnologico $entity */
        $entity = $em->getRepository('PortadoresBundle:EquipoTecnologico')->find($id);

        $entity
            ->setNroInventario($nroInventario)
            ->setDescripcion($descripcion)
            ->setNorma($norma)
            ->setNormaFabricante($normaFabricante)
            ->setTipoCombustible($em->getRepository('PortadoresBundle:TipoCombustible')->find($tipoCombustibleId))
            ->setActividad($em->getRepository('PortadoresBundle:Actividad')->find($actividadId))
            ->setUnidad($em->getRepository('PortadoresBundle:Unidad')->find($unidadId))
            ->setModeloTecnologico($em->getRepository('PortadoresBundle:ModeloTecnologico')->find($modeloId));
        // Calderas
        if ($entity instanceof Caldera) {
            $entity
                ->setNormaRecirculacion($normaRecirculacion)
                ->setNormaRecirculacionFabricante($normaRecirculacionFabricante)
                ->setTipoCombustibleRecirculacion($em->getRepository('PortadoresBundle:TipoCombustible')->find($tipoCombustibleRecirculacionId));
        }

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Equipo tecnológico modificado con éxito.'));
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

        $entity = $em->getRepository('PortadoresBundle:EquipoTecnologico')->find($id);
        if (!$entity)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El equipo tecnológico no se encuentra disponible.'));

        $em->remove($entity);

        try {
            $em->flush();
        } catch (Exception $ex) {
            if (strpos($ex->getMessage(), 'SQLSTATE[23503]')) {
                throw new HttpException(500, sprintf('El equipo %s no se puede eliminar porque se encuentra en uso', $entity->getDescripcion()));
            }
        }

        return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Equipo tecnológico eliminado con éxito.'));
    }
}