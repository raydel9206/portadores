<?php
/**
 * Created by PhpStorm.
 * User: kireny
 * Date: 3/11/15
 * Time: 14:12
 */

namespace Geocuba\PortadoresBundle\Controller;

use Doctrine\Common\Util\Debug;
use Geocuba\AdminBundle\Repository\QueryHelper;
use Geocuba\PortadoresBundle\Entity\ModeloVehiculo;
use Doctrine\Common\CommonException;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Geocuba\PortadoresBundle\Util\Utiles;

class ModeloVehiculoController extends Controller
{
    use ViewActionTrait;

    /**
     * @param $request
     * @return JsonResponse
     */
    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $_nombre = trim($request->get('nombre'));
        $_marca = trim($request->get('marca'));
        $_tipo_equipo = trim($request->get('tipoequipo'));

        $start = $request->get('start');
        $limit = $request->get('limit');

        $entities = $em->getRepository('PortadoresBundle:ModeloVehiculo')->buscarModelo($_nombre, $_marca, $_tipo_equipo, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:ModeloVehiculo')->buscarModelo($_nombre, $_marca, $_tipo_equipo, $start, $limit, true);

        $_data = array();
        /** @var ModeloVehiculo $entity */
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre(),
                'nmarca_vehiculoid' => $entity->getMarcaVehiculoid()->getId(),
                'nnombremarca' => $entity->getMarcaVehiculoid()->getNombre()
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    public function loadComboAction(Request $request)
    {
        $_marca = trim($request->get('marca'));
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('PortadoresBundle:ModeloVehiculo')->buscarModeloCombo($_marca);

        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity['id'],
                'nombre' => $entity['nombre'],
            );
        }

        return new JsonResponse(array('rows' => $_data));
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $nombre = trim($request->get('nombre'));
        $nmarca_vehiculoid = trim($request->get('nmarca_vehiculoid'));

        $repetido = $em->getRepository('PortadoresBundle:ModeloVehiculo')->buscarModeloRepetido($nombre,$nmarca_vehiculoid );
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe un modelo en la marca con el mismo nombre'));

        $entity_er = $em->getRepository('PortadoresBundle:ModeloVehiculo');
        $entities_total = $entity_er->findByVisible(true);
//        for ($i = 0; $i < count($entities_total); $i++) {
//            if (strcasecmp($entities_total[$i]->getNombre(), $nombre) == 0)
//                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El modelo de vehículo ya existe.'));
//        }

//        $entities = $em->getRepository('PortadoresBundle:ModeloVehiculo')->findByNombre($nombre);
//        if ($entities) {
//            if ($entities[0]->getVisible())
//                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El modelo de vehículo ya existe.'));
//            else {
//                $entities[0]->setVisible(true);
//                $em->persist($entities[0]);
//                $em->flush();
//                return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Modelo de vehículo adicionado con éxito.'));
//            }
//        }

        $entity = new ModeloVehiculo();
        $entity->setNombre($nombre);
        $entity->setMarcaVehiculoid($em->getRepository('PortadoresBundle:MarcaVehiculo')->find($nmarca_vehiculoid));
        $entity->setVisible(true);

        $limit = $request->get('limit');
        $order_by = 'nombre ASC';
        $criteria = array(
            'nmarca_vehiculoid' => $nmarca_vehiculoid,
            'visible' => 'true'
        );

        try {
            $em->persist($entity);
            $em->flush();
        } catch (\Exception $ex) {
            if ($ex instanceof HttpException) {
                return new JsonResponse(['success' => false, 'message' => $ex->getMessage()]);
            } else {
                throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $ex->getMessage(), $ex);
            }
        }

        try {
            $partition = $limit ? QueryHelper::findPartitionbyCriteria($em, $entity_er, $criteria, 'id', $entity->getId(), $limit, $order_by) : 1;
        } catch (NonUniqueResultException $e) {
            $partition = 1;
        }
        return new JsonResponse(array('success' => true, 'page' => $partition, 'cls' => 'success', 'message' => 'Modelo adicionado con éxito.'));
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function updAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $nombre = trim($request->get('nombre'));
        $nmarca_vehiculoid = trim($request->get('nmarca_vehiculoid'));


//        $entity_er = $em->getRepository('PortadoresBundle:ModeloVehiculo');
//        $entities = $entity_er->findByNombre($nombre);
//        if ($entities)
//            if ($entities[0]->getId() != $id)
//                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Existe otro modelo de vehículo con ese nombre.'));

        $repetido = $em->getRepository('PortadoresBundle:ModeloVehiculo')->buscarModeloRepetido($nombre,$nmarca_vehiculoid );
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe un modelo en la marca con el mismo nombre'));

        $entity = $em->getRepository('PortadoresBundle:ModeloVehiculo')->find($id);
        $entity->setNombre($nombre);
        $entity->setMarcaVehiculoid($entity->getMarcaVehiculoid());

//        $limit = $request->get('limit');
//        $order_by = 'nombre ASC';
//        $criteria = array(
//            'nmarca_vehiculoid' => $nmarca_vehiculoid,
//            'visible' => 'true'
//        );

        try {
            $em->persist($entity);
            $em->flush();
        } catch (\Exception $ex) {
            if ($ex instanceof HttpException) {
                return new JsonResponse(['success' => false, 'message' => $ex->getMessage()]);
            } else {
                throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $ex->getMessage(), $ex);
            }
        }

//        try {
//            $partition = $limit ? QueryHelper::findPartitionbyCriteria($em, $entity_er, $criteria, 'id', $entity->getId(), $limit, $order_by) : 1;
//        } catch (NonUniqueResultException $e) {
//            $partition = 1;
//        }
//        return new JsonResponse(array('success' => true, 'page' => $partition, 'cls' => 'success', 'message' => 'Modelo modificado con éxito.'));
        return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Modelo modificado con éxito.'));
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function delAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $modelo_er = $em->getRepository('PortadoresBundle:ModeloVehiculo');

        try {
            $em->transactional(function () use ($request, $modelo_er) {
                /** @var EntityManager $em */
                $em = func_get_arg(0);

                foreach ($request->request->get('ids') as $modelo_id) {

                    $modelo = $modelo_er->find($modelo_id);

                    $vehiculo = $em->getRepository('PortadoresBundle:Vehiculo')->findBy(array('nmodeloid' => $modelo_id, 'visible' => true));
                    if ($vehiculo) {
                        return new JsonResponse(array('success' => true, 'cls' => 'danger', 'message' => 'El modelo del vehículo no se puede eliminar porque está asociado a un vehículo.'));
                    }

//                    $vehiculos_particulares = $em->getRepository('PortadoresBundle:VehiculoParticular')->findBy(array('modelo' => $modelo_id, 'activo' => true));
//                    if ($vehiculos_particulares) {
//                        return new JsonResponse(array('success' => true, 'cls' => 'danger', 'message' => 'El modelo del vehículo no se puede eliminar porque está asociada a un vehículo particular.'));
//                    }

                    if (!$modelo) {
                        throw new NotFoundHttpException(sprintf('No existe la Marca con identificador <strong>%s</strong>', $modelo_id));
                    }

                    $em->persist(
                        $modelo->setVisible(false)
                    );
                }
            });

            $em->clear();
        } catch (\Exception $e) {
            $em->clear();

            if ($e instanceof HttpException) {
                return new JsonResponse(['success' => false, 'message' => $e->getMessage()]);
            } else {
                throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), $e);
            }
        }

        return new JsonResponse(['success' => true, 'message' => count($request->request->get('ids')) === 1 ? 'Modelo eliminado con éxito' : 'Modelos eliminados con éxito']);


    }
}