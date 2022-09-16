<?php

namespace Geocuba\AdminBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Geocuba\AdminBundle\Entity\{Evento, Usuario};
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\HttpKernel\Exception\{HttpException, NotFoundHttpException};

/**
 * Class EventoController
 * @package Geocuba\AdminBundle\Controller
 */
class EventoController extends Controller
{
    use ViewActionTrait;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function listAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $evento_er = $em->getRepository('AdminBundle:Evento');
        $usuario_er = $em->getRepository('AdminBundle:Usuario');

        $fecha_inicio = $request->get('fecha_inicio');
        $fecha_fin = $request->get('fecha_fin');
        $tipo = $request->get('tipo');
        $usuario_id = $request->get('usuario_id');
        $entidad = $request->get('entidad');

        if ($fecha_inicio && $fecha_inicio = date_create_from_format('Y-m-d\TH:i:s', $fecha_inicio)) {
            $fecha_inicio->setTime(0, 0, 0);
        }
        if ($fecha_fin && $fecha_fin = date_create_from_format('Y-m-d\TH:i:s', $fecha_fin)) {
            $fecha_fin->setTime(0, 0, 0);
            $fecha_fin->modify('+1 day');
        }

        $limited_result = $evento_er->findAllBy($fecha_inicio, $fecha_fin, $tipo, $usuario_id, $entidad, $request->get('limit'), $request->get('start'));
        $result = $evento_er->findAllBy($fecha_inicio, $fecha_fin, $tipo, $usuario_id, $entidad, null, null);
        $rows = [];

        foreach ($limited_result as $evento) {
            /** @var Evento $evento */
            $evento_data = $evento->toArray();

            /** @var Usuario $usuario */
            $usuario = $evento_data['usuario'] === 'admin' ? null : $usuario_er->find($evento_data['usuario']);

            $evento_data['usuario'] = $evento_data['usuario'] === 'admin' ? 'admin' : ($usuario ? $usuario->getUsuario() : null);
            $evento_data['usuario_nombre_completo'] = $evento_data['usuario'] === 'admin' ? 'admin' : ($usuario ? $usuario->getNombreCompleto() : null);

            $rows[] = $evento_data;

            $em->detach($evento);
        }

        return new JsonResponse(['success' => true, 'rows' => $rows, 'total' => count($result)]);
    }

    /**
     * @return JsonResponse
     */
    public function entityListAction()
    {
        $container = $this->get('service_container');
        $rows = [];

        // TODO: get all entities from fs

        foreach ($container->hasParameter('audited_entities') ? array_values($container->getParameter('audited_entities')) : [] as $row) {
            $rows[] = ['name' => $row];
        }

        return new JsonResponse(['rows' => $rows]);
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
        $evento_er = $em->getRepository('AdminBundle:Evento');

        try {
            $em->transactional(function (ObjectManager $em) use ($request, $evento_er) {
                /** @var Evento $evento */
                $evento = $evento_er->find($request->get('id'));

                if (!$evento) {
                    throw new NotFoundHttpException(sprintf('No existe registro del Evento con identificador %s', $request->get('id')));
                }

                $em->remove($evento);
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

        return new JsonResponse(['success' => true, 'message' => 'El registro del Evento ha sido eliminado']);
    }
}
