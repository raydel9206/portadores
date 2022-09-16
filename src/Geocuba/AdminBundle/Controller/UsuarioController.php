<?php

namespace Geocuba\AdminBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Util\Debug;
use Geocuba\AdminBundle\Entity\{Usuario};
use Geocuba\AdminBundle\Entity\UsuarioUnidad;
use Geocuba\AdminBundle\Repository\QueryHelper;
use Geocuba\Utils\Constants;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\HttpKernel\Exception\{
    HttpException, NotFoundHttpException
};

/**
 * Class UserController
 * @package Geocuba\AdminBundle\Controller
 */
class UsuarioController extends Controller
{
    // See below how it is used.
    const FLUSH_THRESHOLD = 100;

    use ViewActionTrait;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function listAction(Request $request)
    {
        $usuario_er = $this->getDoctrine()->getRepository('AdminBundle:Usuario');

        $simple = $request->query->get('simple', false);
        $query = mb_strtolower(trim($request->query->get('query'), 'UTF-8'));
        $criteria = $request->query->get('all') ? [] : ['activo' => TRUE];
        $orderBy = ['usuario' => 'ASC'];

        $limited_result = $usuario_er->findBy($criteria, $orderBy, $request->query->get('limit'), $request->query->get('start'));
        $result = $request->query->get('limit') ? $usuario_er->findBy($criteria, $orderBy) : $limited_result;
//        Debug::dump($result);

        $filter_handler = function (&$result, $query, $simple) {
            foreach ($result as $key => &$obj) {
                /** @var Usuario $obj */
                $obj = $obj->toArray($simple);

                /** @var array $obj */
                if (!empty($query)) {
                    $values = array_values($obj);
                    $match = false;

                    array_walk_recursive($values, function ($value) use ($query, &$match) {
                        $match = $match || strpos(mb_strtolower(trim($value), 'UTF-8'), $query) !== false;
                    });

                    if (!$match) {
                        unset($result[$key]);
                    }
                }
            }

            return $result;
        };

        $limited_result = $filter_handler($limited_result, $query, $simple);
        $result = !empty($query) ? $filter_handler($result, $query, $simple) : $result;

        return new JsonResponse(['success' => true, 'rows' => $limited_result, 'total' => count($result)]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function addAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $usuario_er = $em->getRepository('AdminBundle:Usuario');

        if (trim(strtolower($request->request->get('usuario'))) === 'admin') {
            $message = 'El nombre de usuario <strong>admin</strong> está reservado';
            return new JsonResponse(['success' => false, 'message' => $message, 'errors' => ['usuario' => $message]]);
        }

        $duplicates = QueryHelper::findByFieldValue($usuario_er, 'usuario', $request->request->get('usuario'), 'activo', true, null, null);

        if (!empty($duplicates)) {
            $message = sprintf('El Usuario <strong>%s</strong> ya existe', $request->request->get('usuario'));
            return new JsonResponse(['success' => false, 'message' => $message, 'errors' => ['usuario' => $message]]);
        }

        $encoder = $this->container->get('security.password_encoder');
        $usuario = new Usuario();

        try {
            $em->transactional(function () use ($request, $usuario, $encoder) {
                /** @var EntityManager $em */
                $em = func_get_arg(0);
                $grupo_er = $em->getRepository('AdminBundle:Grupo');

                foreach ($request->request->get('grupos_ids') as $group_id) {
                    $grupo = $grupo_er->findOneBy(['id' => $group_id, 'activo' => true]);

                    if (!$grupo) {
                        throw new NotFoundHttpException(sprintf('No existe Grupo con identificador <strong>%s</strong>', $group_id));
                    }

                    $usuario->addGrupo($grupo);
                }

                $em->persist(
                    $usuario
                        ->setNombreCompleto($request->request->get('nombre_completo'))
                        ->setCargo($request->request->get('cargo'))
                        ->setUsuario($request->request->get('usuario'))
                        ->setEmail($request->request->get('email'))
                        ->setFechaModificacion(new \DateTime())
                        ->setContrasena($encoder->encodePassword($usuario, $request->request->get('contrasena')))
                        ->setActivo(true)
                        ->setUnidad($em->getRepository('PortadoresBundle:Unidad')->find($request->request->get('unidad_id')))
                );

                $array_dominio = json_decode($request->request->get('dominio'));
                if(sizeof($array_dominio) != 0){
                    foreach ($array_dominio as $dominio){
                        $entity_dominio = new UsuarioUnidad();
                        $entity_dominio->setUsuario($usuario);
                        $entity_dominio->setUnidad($em->getRepository('PortadoresBundle:Unidad')->find($dominio->id));
                        $em->persist($entity_dominio);
                    }
                }
            });

            $em->clear();
        } catch (\Exception $e) {
            if ($e instanceof HttpException) {
                return new JsonResponse(['success' => false, 'message' => $e->getMessage()]);
            } else {
                throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), $e);
            }
        }

        $partition = $request->request->get('limit') ? QueryHelper::findPartition($em, $usuario_er, 'id', $usuario->getId(), $request->request->get('limit'), 'usuario') : 1;

        return new JsonResponse(['success' => true, 'page' => $partition, 'message' => sprintf('El usuario <strong>%s</strong> ha sido registrado', $request->request->get('usuario'))]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Throwable
     */
    public function editAction(Request $request)
    {
        if (trim(strtolower($request->request->get('usuario'))) === 'admin') {
            $message = 'El nombre de usuario <strong>admin</strong> está reservado';
            return new JsonResponse(['success' => false, 'message' => $message, 'errors' => ['usuario' => $message]]);
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $usuario_er = $em->getRepository('AdminBundle:Usuario');

        $usuario = $usuario_er->find($request->request->get('id'));
        if (!$usuario) {
            throw new NotFoundHttpException(sprintf('No existe Usuario con identificador <strong>%s</strong>', $request->request->get('id')));
        }

        $duplicates = QueryHelper::findByFieldValue($usuario_er, 'usuario', $request->request->get('usuario'), 'activo', true, 'id', $request->request->get('id'));

        if (!empty($duplicates)) {
            $message = sprintf('El Usuario <strong>%s</strong> ya existe', $request->request->get('usuario'));
            return new JsonResponse(['success' => false, 'message' => $message, 'errors' => ['usuario' => $message]]);
        }

        $encoder = $this->container->get('security.password_encoder');

        try {
            $em->transactional(function () use ($request, $usuario, $encoder) {
                /** @var EntityManager $em */
                $em = func_get_arg(0);
                $grupo_er = $em->getRepository('AdminBundle:Grupo');

                $usuario->getGrupos()->clear();
                foreach ($request->request->get('grupos_ids') as $grupo_id) {
                    $usuario->addGrupo($grupo_er->find($grupo_id));
                }

                if ($request->request->get('contrasena')) {
                    $usuario->setContrasena($encoder->encodePassword($usuario, $request->request->get('contrasena')));
                }

                $em->persist(
                    $usuario
                        ->setNombreCompleto($request->request->get('nombre_completo'))
                        ->setCargo($request->request->get('cargo'))
                        ->setUsuario($request->request->get('usuario'))
                        ->setEmail($request->request->get('email'))
                        ->setFechaModificacion(new \DateTime())
                        ->setUnidad($em->getRepository('PortadoresBundle:Unidad')->find($request->request->get('unidad_id')))
                );

                $entities_dominio = $em->getRepository('AdminBundle:UsuarioUnidad')->findBy(array('usuario' => $usuario->getId()));
                if (sizeof($entities_dominio) > 0)
                    foreach ($entities_dominio as $dominio)
                        $em->remove($dominio);

                $array_dominio = json_decode($request->request->get('dominio'));
                if(sizeof($array_dominio) != 0){
                    foreach ($array_dominio as $dominio){
                        $entity_dominio = new UsuarioUnidad();
                        $entity_dominio->setUsuario($usuario);
                        $entity_dominio->setUnidad($em->getRepository('PortadoresBundle:Unidad')->findOneBy(array('id' => $dominio->id)));
                        $em->persist($entity_dominio);
                    }
                }
            });

            $em->clear();
        } catch (\Exception $e) {
            $em->clear();

            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), $e);
        }

        $partition = $request->request->get('limit') ? QueryHelper::findPartition($em, $usuario_er, 'id', $usuario->getId(), $request->request->get('limit'), 'usuario') : 1;

        return new JsonResponse(['success' => true, 'page' => $partition, 'message' => sprintf('El Usuario <strong>%s</strong> ha sido modificado', $usuario->getUsuario())]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Throwable
     */
    public function resetAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $usuario_er = $em->getRepository('AdminBundle:Usuario');

        $usuario = $usuario_er->find($request->request->get('id'));
        if (!$usuario) {
            throw new NotFoundHttpException(sprintf('No existe Usuario con identificador <strong>%s</strong>', $request->request->get('id')));
        }

        $encoder = $this->container->get('security.password_encoder');

        try {
            $em->transactional(function () use ($request, $usuario, $encoder) {
                /** @var EntityManager $em */
                $em = func_get_arg(0);

                $em->persist(
                    $usuario->setContrasena($encoder->encodePassword($usuario, $request->request->get('contrasena')))
                );
            });

            $em->clear();
        } catch (\Exception $e) {
            $em->clear();

            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), $e);
        }

        $partition = $request->request->get('limit') ? QueryHelper::findPartition($em, $usuario_er, 'id', $usuario->getId(), $request->request->get('limit'), 'usuario') : 1;

        return new JsonResponse(['success' => true, 'page' => $partition, 'message' => sprintf('La contraseña del Usuario <strong>%s</strong> ha sido modificada', $usuario->getUsuario())]);
    }

    /**
     * Los usuarios se eliminan estableciendo el campo active = FALSE, pero se mantienen las relaciones con la entidad Grupo.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Throwable
     */
    public function deleteAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $usuario_er = $em->getRepository('AdminBundle:Usuario');

        try {
            $em->transactional(function () use ($request, $usuario_er) {
                /** @var EntityManager $em */
                $em = func_get_arg(0);

                foreach ($request->request->get('ids') as $user_id) {
                    /** @var Usuario $usuario */
                    $usuario = $usuario_er->find($user_id);

                    if (!$usuario) {
                        throw new NotFoundHttpException(sprintf('No existe Usuario con identificador <strong>%s</strong>', $user_id));
                    }

                    $em->persist(
                        $usuario->setActivo(false)
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

        return new JsonResponse(['success' => true, 'message' => count($request->request->get('ids')) === 1 ? 'El Usuario ha sido eliminado' : 'Los Usuarios han sido eliminados']);
    }

    //Public function to load the domain of an user
    public function loadDominioAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $userid = $request->get('userid');
        $entities = $em->getRepository('AdminBundle:UsuarioUnidad')->findBy(array('usuario' => $userid));
        $data = array();
        foreach ($entities as $entity){
            $data[] = $entity->getUnidad()->getId();
        }
        return new Response(json_encode(array('success' => true, 'unidades' => $data)));
    }

//    private function persistDominio($user, $unidad_id, $unidades_dominio){
//        $em = $this->getDoctrine()->getManager();
//        $dominio = $em->getRepository('AdminBundle:Dominio')->findByUsersid($user->getId());
//        $entity_dominio = null;
//        if($dominio){
//            $entity_dominio = $dominio[0];
//            $unidad = $em->getRepository('PortadoresBundle:Nunidad')->find($unidad_id);
//            $entity_dominio->setUserUnidadid($unidad);
//            $em->persist($entity_dominio);
//        }
//        else{
//            $entity_dominio = new Dominio();
//            $entity_dominio->setUsersid($user);
//            $unidad = $em->getRepository('PortadoresBundle:Nunidad')->find($unidad_id);
//            $entity_dominio->setUserUnidadid($unidad);
//            $em->persist($entity_dominio);
//        }
//        $dominios = $em->getRepository('AdminBundle:DominioUnidades')->findByDominioid($entity_dominio->getId());
//        foreach ($dominios as $dominio) {
//            $em->remove($dominio);
//        }
//        foreach ($unidades_dominio as $unidad) {
//            $entity_unidad = $em->getRepository('PortadoresBundle:Nunidad')->find($unidad);
//            $entity_dominio_unidades = new DominioUnidades();
//            $entity_dominio_unidades->setDominioid($entity_dominio);
//            $entity_dominio_unidades->setUnidadid($entity_unidad);
//            $em->persist($entity_dominio_unidades);
//        }
//        try{
//            $em->flush();
//            return true;
//        }
//        catch(CommonException $ex){
//            return false;
//        }
//    }
//
//    public function getDominioUnidadesAction(Request $request){
//        $user_id = $request->get('user_id');
//        $em = $this->getDoctrine()->getManager();
//        $dominio = $em->getRepository('AdminBundle:Dominio')->findByUsersid($user_id);
//        $dominios = $em->getRepository('AdminBundle:DominioUnidades')->findByDominioid($dominio[0]->getId());
//        $unidades = array();
//        foreach ($dominios as $dominio_entity) {
//            $unidades[] = $dominio_entity->getUnidadid()->getId();
//        }
//        return new Response(json_encode(array('success' => true,'user_unidad_id' => $dominio[0]->getUserUnidadid()->getId(), 'unidades_dominio' => $unidades)));
//    }

}
