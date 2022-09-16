<?php
/**
 * Created by PhpStorm.
 * User: adonis
 * Date: 25/09/2015
 * Time: 11:49 AM
 */

namespace Geocuba\PortadoresBundle\Controller;


use Geocuba\PortadoresBundle\Entity\MarcaVehiculo;
use Doctrine\Common\CommonException;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Geocuba\PortadoresBundle\Util\Utiles;

class MarcaVehiculoController extends Controller
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
        $_tipo_equipo = trim($request->get('tipoequipo'));

        $start = $request->get('start');
        $limit = $request->get('limit');

        $entities = $em->getRepository('PortadoresBundle:MarcaVehiculo')->buscarMarcaVehiculo($_nombre, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:MarcaVehiculo')->buscarMarcaVehiculo($_nombre, $start, $limit, true);

        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre(),

            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    public function loadComboAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('PortadoresBundle:MarcaVehiculo')->buscarMarcaCombo();

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


        $entities_total = $em->getRepository('PortadoresBundle:MarcaVehiculo')->findByVisible(true);
        for ($i = 0; $i < count($entities_total); $i++) {
            if (strcasecmp($entities_total[$i]->getNombre(), $nombre) == 0)
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'La marca de vehículo ya existe.'));
        }

        $entities = $em->getRepository('PortadoresBundle:MarcaVehiculo')->findByNombre($nombre);
        if ($entities) {
            if ($entities[0]->getVisible())
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'La marca de vehículo ya existe.'));
            else {
                $entities[0]->setVisible(true);
                $em->persist($entities[0]);
                $em->flush();
                return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Marca de vehículo adicionada con éxito.'));
            }
        }
        $entity = new MarcaVehiculo();
        $entity->setNombre($nombre);

        $entity->setVisible(true);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Marca de vehículo adicionada con éxito.'));
        } catch (\Exception $ex) {
            if ($ex instanceof HttpException) {
                return new JsonResponse(['success' => false, 'message' => $ex->getMessage()]);
            } else {
                throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $ex->getMessage(), $ex);
            }
        }
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

        $entities = $em->getRepository('PortadoresBundle:MarcaVehiculo')->findByNombre($nombre);
        if ($entities)
            if ($entities[0]->getId() != $id)
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Existe otra marca de vehículo con ese nombre.'));
        $entity = $em->getRepository('PortadoresBundle:MarcaVehiculo')->find($id);
        $entity->setNombre($nombre);

        try {
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Marca de vehículo modificada con éxito.'));
            return $response;
        } catch (\Exception $ex) {
            if ($ex instanceof HttpException) {
                return new JsonResponse(['success' => false, 'message' => $ex->getMessage()]);
            } else {
                throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $ex->getMessage(), $ex);
            }
        }
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function delAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $marca_er = $em->getRepository('PortadoresBundle:MarcaVehiculo');

        try {
            $em->transactional(function () use ($request, $marca_er) {
                /** @var EntityManager $em */
                $em = func_get_arg(0);

                foreach ($request->request->get('ids') as $marca_id) {

                    $marca = $marca_er->find($marca_id);

                    if (!$marca) {
                        throw new NotFoundHttpException(sprintf('No existe la Marca con identificador <strong>%s</strong>', $marca_id));
                    }

                    $em->persist(
                        $marca->setVisible(false)
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

        return new JsonResponse(['success' => true, 'message' => count($request->request->get('ids')) === 1 ? 'Marca eliminada con éxito' : 'Marcas eliminadas con éxito']);

    }
}