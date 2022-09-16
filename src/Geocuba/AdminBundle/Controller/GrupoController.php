<?php

namespace Geocuba\AdminBundle\Controller;

use Doctrine\ORM\EntityManager;
use Geocuba\AdminBundle\Entity\{Grupo, Usuario};
use Geocuba\AdminBundle\Repository\QueryHelper;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\HttpKernel\Exception\{HttpException, NotFoundHttpException, UnprocessableEntityHttpException};

/**
 * Class GrupoController
 * @package Geocuba\AdminBundle\Controller
 */
class GrupoController extends Controller
{
    use ViewActionTrait;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function listAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $grupo_er = $em->getRepository('AdminBundle:Grupo');

        $simple = $request->query->get('simple', false);
        $criteria = $request->query->get('all') ? [] : ['activo' => TRUE];
        $orderBy = ['nombre' => 'ASC'];

        $limited_result = $grupo_er->findBy($criteria, $orderBy, $request->query->get('limit'), $request->query->get('start'));
        $result = $request->query->get('limit') ? $grupo_er->findBy($criteria, $orderBy) : $limited_result;

        $rows = [];
        foreach ($limited_result as $obj) {
            /** @var Grupo $obj */
            $rows[] = $obj->toArray($simple);
            $em->detach($obj);
        }

        return new JsonResponse(['success' => true, 'rows' => $rows, 'total' => count($result)]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Throwable
     */
    public function addAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $grupo_er = $em->getRepository('AdminBundle:Grupo');

        $duplicates = QueryHelper::findByFieldValue($grupo_er, 'nombre', $request->request->get('nombre'), 'activo', true, null, null);

        if (!empty($duplicates)) {
            $message = sprintf('El Grupo <strong>%s</strong> ya existe', $request->request->get('nombre'));
            return new JsonResponse(['success' => false, 'message' => $message, 'errors' => ['nombre' => $message]]);
        }

        $grupo = new Grupo($request->request->get('nombre'));

        try {
            $em->transactional(function () use ($request, $grupo) {
                /** @var EntityManager $em */
                $em = func_get_arg(0);

                $em->persist(
                    $grupo
                        ->setNombre($request->request->get('nombre'))
                        ->setActivo(true)
                );
            });

            $em->clear();
        } catch (\Exception $e) {
            $em->clear();

            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), $e);
        }

        $partition = $request->request->get('limit') ? QueryHelper::findPartition($em, $grupo_er, 'id', $grupo->getId(), $request->request->get('limit'), 'nombre') : 1;

        return new JsonResponse(['success' => true, 'page' => $partition, 'message' => sprintf('El Grupo <strong>%s</strong> ha sido registrado', $request->request->get('nombre'))]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Throwable
     */
    public function editAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $grupo_er = $em->getRepository('AdminBundle:Grupo');

        $grupo = $grupo_er->find($request->request->get('id'));
        if (!$grupo) {
            throw new NotFoundHttpException(sprintf('No existe Grupo con identificador <strong>%s</strong>', $request->request->get('id')));
        }

        $duplicates = QueryHelper::findByFieldValue($grupo_er, 'nombre', $request->request->get('nombre'), 'activo', true, 'id', $request->request->get('id'));

        if (!empty($duplicates)) {
            $message = sprintf('El Grupo <strong>%s</strong> ya existe', $request->request->get('nombre'));
            return new JsonResponse(['success' => false, 'message' => $message, 'errors' => ['nombre' => $message]]);
        }

        try {
            $em->transactional(function () use ($request, $grupo) {
                /** @var EntityManager $em */
                $em = func_get_arg(0);

                $em->persist(
                    $grupo->setNombre($request->request->get('nombre'))
                );
            });
            $em->clear();
        } catch (\Exception $e) {
            $em->clear();

            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), $e);
        }

        $partition = $request->request->get('limit') ? QueryHelper::findPartition($em, $grupo_er, 'id', $grupo->getId(), $request->request->get('limit'), 'nombre') : 1;

        return new JsonResponse(['success' => true, 'page' => $partition, 'message' => sprintf('El Grupo <strong>%s</strong> ha sido modificado', $grupo->getNombre())]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Throwable
     */
    public function deleteAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $grupo_er = $em->getRepository('AdminBundle:Grupo');
        $recursos_er = $em->getRepository('AdminBundle:Recurso');

        try {
            $em->transactional(function () use ($request, $grupo_er, $recursos_er) {
                /** @var EntityManager $em */
                $em = func_get_arg(0);

                foreach ($request->request->get('ids') as $group_id) {
                    /** @var Grupo $grupo */
                    $grupo = $grupo_er->findOneBy(['id' => $group_id, 'activo' => true]);
                    if (!$grupo) {
                        throw new NotFoundHttpException(sprintf('No existe Grupo con identificador <strong>%s</strong>', $group_id));
                    }

                    $usuarios = $grupo->getUsuarios();
                    $idx = null;

                    if ($usuarios->exists(function ($key, $usuario) use (&$idx) {
                        $idx = $key;

                        /** @var Usuario $usuario */
                        return $usuario->getActivo();
                    })
                    ) {
                        throw new UnprocessableEntityHttpException(sprintf('El Grupo <strong>%s</strong> no se puede eliminar porque está relacionado con el Usuario <strong>%s</strong>', $grupo->getNombre(), $usuarios->get($idx)->getUsuario()));
                    }

                    foreach ($recursos_er->findByGrupos($group_id) as $resource) {
                        $em->remove($resource);
                    }

                    $em->persist(
                        $grupo->setActivo(false)
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

        return new JsonResponse(['success' => true, 'message' => count($request->request->get('ids')) === 1 ? 'El Grupo ha sido eliminado' : 'Los Grupos han sido eliminados']);
    }
}
