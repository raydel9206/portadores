<?php

namespace Geocuba\PortadoresBundle\Controller;

use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Geocuba\PortadoresBundle\Entity\MedicionAfore;
use Geocuba\PortadoresBundle\Entity\Tanque;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\PortadoresBundle\Util\Utiles;
use Symfony\Component\HttpKernel\Exception\HttpException;


class TanquesController extends Controller
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

        $start = $request->get('start');
        $limit = $request->get('limit');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $entities = $em->getRepository('PortadoresBundle:Tanque')->findAllBy($_unidades, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:Tanque')->findAllBy($_unidades, $start, $limit, true);

        $_data = array();
        foreach ($entities as $entity) {
            /** @var Tanque $entity */
            $_data[] = array(
                'id' => $entity->getId(),
                'numero_inventario' => $entity->getNumeroInventario(),
                'descripcion' => $entity->getDescripcion(),
                'capacidad' => $entity->getCapacidad(),
                'existencia' => $entity->calcularExistencia($em),
                'cilindro' => $entity->getCilindro(),
                'tipo_combustible_id' => $entity->getTipoCombustible()->getId(),
                'tipo_combustible_nombre' => $entity->getTipoCombustible()->getNombre(),
                'unidad_id' => $entity->getUnidad()->getId(),
                'unidad_nombre' => $entity->getUnidad()->getNombre(),
                'unidad_medida_id' => $entity->getUnidadMedida()->getId(),
                'unidad_medida_nombre' => $entity->getUnidadMedida()->getNombre()
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loadMedicionesAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $tanqueId = $request->get('tanque_id');

        /** @var Tanque $tanque */
        $tanque = $em->getRepository('PortadoresBundle:Tanque')->find($tanqueId);
        if (!$tanque)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El tanque no se encuentra disponible.'));
//        $mediciones = $em->getRepository('PortadoresBundle:MedicionAfore')->findBy(['tanqueId' => $tanqueId], ['nivelCm' => 'ASC']);
        $_data = array_map(static function ($medicion) {
            /** @var MedicionAfore $medicion */
            return [
                'id' => $medicion->getId(),
                'nivel' => $medicion->getNivel(),
                'existencia' => $medicion->getExistencia(),
                'tanque_id' => $medicion->getTanque()->getId(),
            ];
        }, $tanque->getMedicionesAfore()->toArray());

        return new JsonResponse(['rows' => $_data]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addAction(Request $request): ?JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $numeroInventario = trim($request->get('numero_inventario'));
        $descripcion = trim($request->get('descripcion'));
        $existencia = trim($request->get('existencia'));
        $capacidad = trim($request->get('capacidad'));
        $cilindro = trim($request->get('cilindro'));
        $tipoCombustibleId = trim($request->get('tipo_combustible_id'));
        $unidadId = trim($request->get('unidad_id'));
        $unidadMedidaId = trim($request->get('unidad_medida_id'));

        if ((float)$capacidad < (float)$existencia) return new JsonResponse(['success' => false, 'message' => sprintf('La capacidad no puede ser menor que la existencia.')]);

        $duplicates = $em->getRepository('PortadoresBundle:Tanque')->findDuplicate($numeroInventario, $unidadId);
        if ($duplicates)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe un tanque con el mismo numero de serie en la unidad seleccionada.'));

        $entity = new Tanque();
        $entity->setNumeroInventario($numeroInventario);
        $entity->setDescripcion($descripcion);
        $entity->setCapacidad($capacidad);
        $entity->setExistencia($existencia);
        $entity->setCilindro($cilindro ? true : false);
        $entity->setUnidad($em->getRepository('PortadoresBundle:Unidad')->find($unidadId));
        $entity->setUnidadMedida($em->getRepository('PortadoresBundle:UnidadMedida')->find($unidadMedidaId));
        $entity->setTipoCombustible($em->getRepository('PortadoresBundle:TipoCombustible')->find($tipoCombustibleId));
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Tanque adicionado con éxito.'));
        } catch (Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addMedicionAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $tanqueId = trim($request->get('tanque_id'));
        $nivel = $request->get('nivel');
        $existencia = $request->get('existencia');

        /** @var Tanque $tanque */
        $tanque = $em->getRepository('PortadoresBundle:Tanque')->find($tanqueId);
        if (!$tanque)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El tanque no se encuentra disponible.'));

        if ($tanque->getCapacidad() < (float) $existencia)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El valor excede la capacidad del tanque.'));

        $duplicates = $em->getRepository('PortadoresBundle:MedicionAfore')->findDuplicates($nivel, $existencia, $tanqueId, true);
        if ($duplicates)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe una medición con alguno de esos valores.'));

        $medicion = new MedicionAfore();
        $medicion->setNivel($nivel);
        $medicion->setExistencia($existencia);

        $tanque->addMedicionAfore($medicion);

        $em->persist($tanque);

        try {
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Medición adicionada con éxito.'));
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
        $numeroInventario = trim($request->get('numero_inventario'));
        $descripcion = trim($request->get('descripcion'));
//        $existencia = trim($request->get('existencia'));
        $capacidad = trim($request->get('capacidad'));
        $cilindro = trim($request->get('cilindro'));
        $tipoCombustibleId = trim($request->get('tipo_combustible_id'));
        $unidadId = trim($request->get('unidad_id'));
        $unidadMedidaId = trim($request->get('unidad_medida_id'));

        $entity = $em->getRepository('PortadoresBundle:Tanque')->find($id);
        if (!$entity)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El tanque no se encuentra disponible.'));
        $duplicates = $em->getRepository('PortadoresBundle:Tanque')->findDuplicate($numeroInventario, $unidadId, $id);
        if ($duplicates)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe un tanque con el mismo numero de serie en la unidad seleccionada.'));

        if ((float)$capacidad < (float)$entity->getExistencia()) return new JsonResponse(['success' => false, 'message' => sprintf('La capacidad no puede ser menor que la existencia actual: %s', round($entity->getExistencia(), 2))]);

        $entity->setNumeroInventario($numeroInventario);
        $entity->setDescripcion($descripcion);
        $entity->setCapacidad($capacidad);
//        $entity->setExistencia($existencia);
        $entity->setCilindro($cilindro ? true : false);
        $entity->setUnidad($em->getRepository('PortadoresBundle:Unidad')->find($unidadId));
        $entity->setUnidadMedida($em->getRepository('PortadoresBundle:UnidadMedida')->find($unidadMedidaId));
        $entity->setTipoCombustible($em->getRepository('PortadoresBundle:TipoCombustible')->find($tipoCombustibleId));
        $em->persist($entity);

        try {
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Tanque modificado con éxito.'));
        } catch (Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updMedicionAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $tanqueId = trim($request->get('tanque_id'));
        $nivel = $request->get('nivel');
        $existencia = $request->get('existencia');

        $tanque = $em->getRepository('PortadoresBundle:Tanque')->find($tanqueId);
        if (!$tanque)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El tanque no se encuentra disponible.'));

        if ($tanque->getCapacidad() < (float) $existencia)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El valor excede la capacidad del tanque.'));

        $medicion = $em->getRepository('PortadoresBundle:MedicionAfore')->find($id);
        if (!$medicion)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'La medición no se encuentra disponible.'));

        $duplicates = $em->getRepository('PortadoresBundle:MedicionAfore')->findDuplicates($nivel, $existencia, $id);
        if ($duplicates)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe una medición con alguno de esos valores.'));

        $medicion->setNivel($nivel);
        $medicion->setExistencia($existencia);
        $em->persist($medicion);

        try {
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Medicion modificada con éxito.'));
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

        $entity = $em->getRepository('PortadoresBundle:Tanque')->find($id);
        if (!$entity)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El tanque no se encuentra disponible.'));

        $em->remove($entity);

        try {
            $em->flush();
        } catch (Exception $ex) {
            if (strpos($ex->getMessage(), 'SQLSTATE[23503]')) {
                throw new HttpException(500, sprintf('El tanque %s no se puede eliminar porque se encuentra en uso', $entity->getNombre()));
            }
        }

        return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Tanque eliminado con éxito.'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteMedicionAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $id = trim($request->get('id'));
        $tanqueId = trim($request->get('tanque_id'));

        /** @var Tanque $tanque */
        $tanque = $em->getRepository('PortadoresBundle:Tanque')->find($tanqueId);
        if (!$tanque)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El tanque asociado no se encuentra disponible.'));

        $result = $tanque->removeMedicionAforeById($id);
        if (!$result)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'No es posible eliminar la medición.'));

        $em->persist($tanque);

        try {
            $em->flush();
        } catch (Exception $ex) {
                throw new HttpException(500, $ex->getMessage());
        }

        return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Tanque eliminado con éxito.'));
    }
}