<?php

namespace Geocuba\AdminBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Geocuba\AdminBundle\Entity\Recurso;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\HttpKernel\Exception\{HttpException, NotFoundHttpException};

/**
 * Class RecursoController
 * @package Geocuba\AdminBundle\Controller
 */
class RecursoController extends Controller
{
    use ViewActionTrait;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function listAction(Request $request)
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getDoctrine()->getManager();

        $grupo_er = $em->getRepository('AdminBundle:Grupo');
        $recurso_er = $em->getRepository('AdminBundle:Recurso');

        $grupo = $grupo_er->findOneBy(['id' => $request->get('grupo_id'), 'activo' => true]);

        if (!$grupo) {
            throw new NotFoundHttpException(sprintf('No existe Grupo con identificador <strong>%s</strong>', $request->get('group_id')));
        }

        $modules = [];
        foreach ($this->get('app.service.security')->getValidRoutes() as $route_key => $route_obj) {
            /** @var \Symfony\Component\Routing\Route $route_obj */

            $route = $route_obj->getOption('text');
            $module = $route_obj->getOption('module');

            if (!$route || !is_string($route) || !$module || !is_string($module)) {
                continue;
            }

            $children = [];
            $is_route_checked = false;

            if ($route_obj->hasOption('resources') && is_array($route_obj->getOption('resources'))) {
                foreach ($route_obj->getOption('resources') as $resource_key => $resource_data) {
                    $is_resource_checked = !is_null($recurso_er->findOneBy(['ruta' => $route_key, 'nombre' => $resource_key, 'grupo' => $grupo]));
                    $is_route_checked = $is_route_checked || $is_resource_checked;

                    $dependent_of = array_key_exists('dependent_of', $resource_data) ? $resource_data['dependent_of'] : null;
                    $files = array_key_exists('files', $resource_data) ? $resource_data['files'] : null;
                    $description = array_key_exists('description', $resource_data) ? $resource_data['description'] : null;

                    $children[] = ['nombre' => $resource_key, 'dependent_of' => $dependent_of, 'descripcion' => $description, 'checked' => $is_resource_checked, 'archivos' => $files, 'leaf' => true];
                }

                if (!empty($children)) {
                    $dropdown = $route_obj->getOption('dropdown');

                    $route_node = ['ruta' => $route_key, 'nombre' => $route, 'checked' => $is_route_checked, 'dropdown' => is_array($dropdown) ? implode(',', $dropdown) : $dropdown];
                    $route_node = array_merge($route_node, !empty($children) ? ['expanded' => false, 'children' => $children] : ['leaf' => true]);

                    if (!array_key_exists($module, $modules)) {
                        $modules[$module] = ['nombre' => $module, 'checked' => $is_route_checked, 'expanded' => true, 'children' => [$route_node]];
                    } else {
                        $modules[$module]['checked'] = $modules[$module]['checked'] || $is_route_checked;
                        $modules[$module]['expanded'] = true;
                        $modules[$module]['children'][] = $route_node;
                    }
                }
            }
        }

        return new JsonResponse(['success' => true, 'children' => array_values($modules)]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function registerAction(Request $request)
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getDoctrine()->getManager();

        $grupo_er = $em->getRepository('AdminBundle:Grupo');
        $resource_er = $em->getRepository('AdminBundle:Recurso');

        $grupo = $grupo_er->find($request->get('grupo_id'));

        if (!$grupo) {
            throw new NotFoundHttpException(sprintf('No existe Grupo con identificador <strong>%s</strong>', $request->get('group_id')));
        }

        /** @var \Geocuba\AdminBundle\Entity\Recurso[] $snapshot */
        $snapshot = $resource_er->findByGrupo($grupo);

        /** @var \Geocuba\AdminBundle\Entity\Recurso[] $resources */
        $resources = [];

        try {
            foreach (json_decode($request->get('datos'), true) as $route_data) {
                // error_log(print_r($routeData, true));
                $route_key = $route_data['ruta'];

                foreach ($route_data['recursos'] as $resource_data) {
                    $resources[] = new Recurso($route_key, $resource_data['nombre'], $grupo);
                }
            }

            $delete_diff = !empty($resources) ? array_filter($snapshot, function ($value) use ($resources) {
                foreach ($resources as $resource) {
                    if ($resource->equalTo($value)) {
                        return false;
                    }
                }
                return true;
            }) : $snapshot;

            $insert_diff = array_filter($resources, function ($value) use ($snapshot) {
                foreach ($snapshot as $resource) {
                    if ($resource->equalTo($value)) {
                        return false;
                    }
                }
                return true;
            });

            $em->transactional(function () use ($delete_diff, $insert_diff) {
                /** @var EntityManagerInterface $em */
                $em = func_get_arg(0);

                foreach ($delete_diff as $resource) {
                    // error_log(print_r('delete=> ' . $resource->__toString(), true));
                    $em->remove($resource);
                }

                foreach ($insert_diff as $resource) {
                    // error_log(print_r('insert=> ' . $resource->__toString(), true));
                    $em->persist($resource);
                }
            });

            $em->clear();
        } catch (\Exception $e) {
            $em->clear();

            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), $e);
        }

        return new JsonResponse(['success' => true, 'message' => sprintf('Los recursos del grupo <strong>%s</strong> han sido registrados', $grupo->getNombre())]);
    }
}
