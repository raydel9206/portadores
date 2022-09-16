<?php
/**
 * Created by PhpStorm.
 * User: yosley
 * Date: 03/11/2015
 * Time: 10:05
 */

namespace Geocuba\PortadoresBundle\Controller;
use Geocuba\AdminBundle\Util\Util;
use Geocuba\PortadoresBundle\Entity\UMNivelActividad;
use Doctrine\Common\CommonException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\Utils\ViewActionTrait;

class UMNivelActividadController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $_nombre = trim($request->get('nombre'));

        $start = $request->get('start');
        $limit = $request->get('limit');

        $entities = $em->getRepository('PortadoresBundle:UMNivelActividad')->buscarUmNivelActividad($_nombre, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:UMNivelActividad')->buscarUmNivelActividad($_nombre, $start, $limit, true);

        $_data = array();

        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'nivel_actividad' => $entity->getNivelActividad()
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $nivel_actividad = trim($request->get('nivel_actividad'));

        $repetido = $em->getRepository('PortadoresBundle:UMNivelActividad')->buscarUmNivelActividadRepetido($nivel_actividad);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe una UM del nivel de actividad con el mismo nombre.'));

        $entity = new UMNivelActividad();
        $entity->setNivelActividad($nivel_actividad);

        $entity->setVisible(true);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'UM del nivel de actividad adicionada con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function modAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $nivel_actividad = trim($request->get('nivel_actividad'));

        $repetido = $em->getRepository('PortadoresBundle:UMNivelActividad')->buscarUmNivelActividadRepetido($nivel_actividad, $id);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe una UM del nivel de actividad con el mismo nombre.'));

        $entity = $em->getRepository('PortadoresBundle:UMNivelActividad')->find($id);
        $entity->setNivelActividad($nivel_actividad);

        try {
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'UM del nivel de actividad modificado con éxito.'));
            return $response;
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function delAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $um_nivel_actividad_er = $em->getRepository('PortadoresBundle:UMNivelActividad');

        try {
            $em->transactional(function () use ($request, $um_nivel_actividad_er) {
                /** @var EntityManager $em */
                $em = func_get_arg(0);

                foreach ($request->request->get('ids') as $um_nivel_actividad_id) {

                    $um_nivel_actividad = $um_nivel_actividad_er->find($um_nivel_actividad_id);

                    if (!$um_nivel_actividad) {
                        throw new NotFoundHttpException(sprintf('No existe la UM con identificador <strong>%s</strong>', $um_nivel_actividad_id));
                    }

                    $em->persist(
                        $um_nivel_actividad->setVisible(false)
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

        return new JsonResponse(['success' => true, 'message' => count($request->request->get('ids')) === 1 ? 'UM eliminada con éxito' : 'UMs eliminadas con éxito']);
    }
}