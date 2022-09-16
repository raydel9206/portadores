<?php
//
namespace Geocuba\AdminBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Geocuba\AdminBundle\Entity\Notificacion;
use Geocuba\AdminBundle\Repository\QueryHelper;
use Geocuba\Utils\{Constants, ViewActionTrait};
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\HttpKernel\Exception\{BadRequestHttpException, HttpException, NotFoundHttpException};

/**
 * Class NotificacionController
 * @package Geocuba\AdminBundle\Controller
 */
class NotificacionController extends Controller
{
    use ViewActionTrait;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function listAction(Request $request)
    {
        $notifier = $this->get('app.service.notifier');
        $scope = $request->get('scope');

        $limited_result = $notifier->getAll($scope !== 'all', $request->get('limit'), $request->get('start'));
        $result = $notifier->getAll($scope !== 'all', null, null);

        return new JsonResponse(['success' => true, 'rows' => $limited_result, 'total' => count($result)]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function readAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $notificacion_er = $em->getRepository('AdminBundle:Notificacion');

        $notificaciones_ids = $request->get('ids');

        try {
            $em->transactional(function (ObjectManager $em) use ($notificacion_er, $notificaciones_ids) {
                /** @var EntityManager $em */
                $em = func_get_arg(0);

                foreach ($notificaciones_ids as $notification_id) {
                    if ($notificacion = $notificacion_er->find($notification_id)) {
                        /** @var Notificacion $notificacion */
                        $notificacion->setFechaAceptacion(new \DateTime());
                    }
                }
            });

            $em->clear();
        } catch (\Exception $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), $e);
        }

        return new JsonResponse(['success' => true, 'message' => count($notificaciones_ids) === 1 ? 'La Notificación ha sido modificada' : 'Las Notificaciones han sido modificadas']);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $notificacion_er = $em->getRepository('AdminBundle:Notificacion');

        $notifier = $this->get('app.service.notifier');

        try {
            $mensaje = trim($request->get('mensaje'));

            switch (intval($request->get('tipo'))) {
                case Constants::NOTIFICACION_GLOBAL:
                    $notificaciones = $notifier->notifyAll($mensaje);

                    if (empty($notificaciones)) {
                        $msg = 'No existe Usuarios para notificar';

                        return new JsonResponse(['success' => false, 'message' => $msg]);
                    } else {
                        $notificacion = $notificaciones[0];
                    }

                    $msg = sprintf('Las Notificaciones (%s) a los usuarios han sido registradas', count($notificaciones));

                    break;
                case Constants::NOTIFICACION_GRUPO:
                    $notificaciones = $notifier->notifyGroups($mensaje, $request->get('grupos_ids'));

                    if (empty($notificaciones)) {
                        $msg = 'No existe Usuarios asociados a los Grupos especificados';

                        return new JsonResponse(['success' => false, 'message' => $msg, 'errors' => ['grupos_ids' => $msg]]);
                    } else {
                        $notificacion = $notificaciones[0];
                    }

                    $msg = sprintf('Las Notificaciones (%s) a los grupos han sido registradas', count($notificaciones));

                    break;
                case Constants::NOTIFICACION_USUARIO:
                    $notificaciones = $notifier->notifyUsers($mensaje, $request->get('usuarios_ids'));

                    if (empty($notificaciones)) {
                        $msg = 'No existe Usuarios para notificar';

                        return new JsonResponse(['success' => false, 'message' => $msg]);
                    } else {
                        $notificacion = $notificaciones[0];
                    }

                    $msg = sprintf('Las Notificaciones (%s) a los usuarios han sido registradas', count($notificaciones));

                    break;
                default:
                    throw new BadRequestHttpException(sprintf('No existe implementación para el tipo de notificación \'%s\'', $request->get('tipo')));
            }
        } catch (\Exception $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), $e);
        }

        $partition = $request->get('limit') ? QueryHelper::findPartition($em, $notificacion_er, 'id', $notificacion->getId(), $request->get('limit'), 'fecha_creacion DESC, fecha_aceptacion DESC, mensaje DESC') : 1;

        return new JsonResponse(['success' => true, 'page' => $partition, 'message' => $msg]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function editAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $notificacion_er = $em->getRepository('AdminBundle:Notificacion');

        $notificacion = $notificacion_er->find($request->get('id'));
        if (!$notificacion) {
            throw new NotFoundHttpException(sprintf('No existe Notificación con identificador %s', $request->get('id')));
        }

        try {
            $em->transactional(function () use ($request, $notificacion) {
                /** @var EntityManager $em */
                $em = func_get_arg(0);

                $em->persist(
                    $notificacion
                        ->setMensaje(trim($request->get('mensaje')))
                        ->setFechaAceptacion(null)
                );
            });

            $em->clear();
        } catch (\Exception $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), $e);
        }

        $partition = $request->get('limit') ? QueryHelper::findPartition($em, $notificacion_er, 'id', $notificacion->getId(), $request->get('limit'), 'fecha_creacion DESC, fecha_aceptacion DESC, mensaje DESC') : 1;

        return new JsonResponse(['success' => true, 'page' => $partition, 'message' => 'La Notificación ha sido modificada']);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function deleteAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $notificacion_er = $em->getRepository('AdminBundle:Notificacion');

        try {
            $em->transactional(function () use ($request, $notificacion_er) {
                /** @var EntityManager $em */
                $em = func_get_arg(0);

                foreach ($request->get('ids') as $notification_id) {
                    /** @var Notificacion $notificacion */
                    $notificacion = $notificacion_er->find($notification_id);

                    if (!$notificacion) {
                        throw new NotFoundHttpException(sprintf('No existe Notificación con identificador %s', $notification_id));
                    }

                    $em->remove($notificacion);
                }
            });

            $em->clear();
        } catch (\Exception $e) {
            $em->clear();

            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), $e);
        }

        return new JsonResponse(['success' => true, 'message' => count($request->get('ids')) === 1 ? 'La Notificación ha sido eliminada' : 'Las Notificaciones han sido eliminadas']);
    }
}
