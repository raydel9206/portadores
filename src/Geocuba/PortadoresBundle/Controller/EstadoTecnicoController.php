<?php
/**
 * Created by PhpStorm.
 * User: yosley
 * Date: 05/10/2015
 * Time: 11:09
 */

namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\PortadoresBundle\Entity\EstadoTecnico;
use Doctrine\Common\CommonException;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Geocuba\PortadoresBundle\Util\Utiles;

class EstadoTecnicoController extends Controller
{
    use ViewActionTrait;

    /**
     * @param $request
     * @return JsonResponse
     */
    public function loadAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('PortadoresBundle:EstadoTecnico')->findBy(array('visible'=>true));

        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre()
            );
        }
        return new JsonResponse(array('rows' => $_data, 'total' => count($_data)));
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $nombre = trim($request->get('nombre'));

        $entities_total = $em->getRepository('PortadoresBundle:EstadoTecnico')->buscarEstadoTecnicoRepetido($nombre);
        if ($entities_total)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El Estado Técnico ya existe.'));


        $entities = $em->getRepository('PortadoresBundle:EstadoTecnico')->findByNombre($nombre);
        if ($entities) {
            $entities[0]->setVisible(true);
            $em->persist($entities[0]);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Estado Técnico  adicionada con éxito.'));

        }
        $entity = new EstadoTecnico();
        $entity->setNombre($nombre);
        $entity->setVisible(true);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Estado Técnico  adicionada con éxito.'));
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

        $entities_total = $em->getRepository('PortadoresBundle:EstadoTecnico')->buscarEstadoTecnicoRepetido($nombre);
        if ($entities_total)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El Estado Técnico ya existe.'));


        $entities = $em->getRepository('PortadoresBundle:EstadoTecnico')->findByNombre($nombre);

        if ($entities)
            if ($entities[0]->getId() != $id)
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Existe Estado Técnico  con ese nombre.'));
        $entity = $em->getRepository('PortadoresBundle:EstadoTecnico')->find($id);
        $entity->setNombre($nombre);

        try {
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Estado Técnico  modificado con éxito.'));
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
        $estado_tecnico_er = $em->getRepository('PortadoresBundle:EstadoTecnico');

        try {
            $em->transactional(function () use ($request, $estado_tecnico_er) {
                /** @var EntityManager $em */
                $em = func_get_arg(0);

                foreach ($request->request->get('ids') as $estado_tecnico_id) {

                    $estado_tecnico = $estado_tecnico_er->find($estado_tecnico_id);

                    if (!$estado_tecnico) {
                        throw new NotFoundHttpException(sprintf('No existe el Centro de Costo con identificador <strong>%s</strong>', $estado_tecnico_id));
                    }


                    $vehiculo = $em->getRepository('PortadoresBundle:Vehiculo')->findBy(array('nestadoTecnicoid' => $estado_tecnico_id, 'visible' => true));
                    if ($vehiculo) {
                        return new JsonResponse(array('success' => true, 'cls' => 'danger', 'message' => 'El estado técnico no se puede eliminar porque está asociado a un vehículo.'));
                    }

                    $em->persist(
                        $estado_tecnico->setVisible(false)
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

        return new JsonResponse(['success' => true, 'message' => count($request->request->get('ids')) === 1 ? 'Estado técnico eliminado con éxito' : 'Estados técnicos eliminados con éxito']);
    }
}