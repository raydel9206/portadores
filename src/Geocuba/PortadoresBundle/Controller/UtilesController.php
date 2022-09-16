<?php
/**
 * Created by PhpStorm.
 * User: adonis
 * Date: 18/12/2015
 * Time: 03:11 PM
 */

namespace Geocuba\PortadoresBundle\Controller;


use Doctrine\Common\Util\Debug;
use Geocuba\AdminBundle\Entity\Notificacion;
use Geocuba\PortadoresBundle\Entity\CierreMes;
use Geocuba\PortadoresBundle\Util\Utiles;
use Geocuba\Utils\ViewActionTrait;
use Proxies\__CG__\Geocuba\PortadoresBundle\Entity\Unidad;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UtilesController extends Controller
{
    use ViewActionTrait;


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getCurrentPeriodoAction(Request $request)
    {

        $_user = $this->get('security.token_storage')->getToken()->getUser();
        $unidadUser = $_user->getUnidad()->getId();
        $entity = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:CierreMes')->findOneBy(array('cerrado' => false, 'disponible' => true, 'idunidad' => $unidadUser));


        $session = $request->getSession();
        $session->set('current_year', $entity->getAnno());
        $session->set('current_month', $entity->getMes());


        $minPeriodo = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:CierreMes')->findBy(array(), array(
            'anno' => 'ASC',
            'mes' => 'ASC'
        ));

        $session->set('min_year', $minPeriodo[0]->getAnno());
        $session->set('min_month', $minPeriodo[0]->getMes());

        return new JsonResponse(array('anno' => $entity->getAnno(), 'mes' => $entity->getMes(), 'min_year' => $minPeriodo[0]->getAnno(), 'min_month' => $minPeriodo[0]->getMes()));
    }

    public function cambiarPeriodoAction(Request $request)
    {
        $session = $request->getSession();
        $session->set('selected_year', (int)$request->get('anno_seleccionado'));
        $session->set('selected_month', (int)$request->get('mes_seleccionado') + 1);

        return new RedirectResponse($request->server->get('HTTP_REFERER'));
    }

    public function cerrarPeriodoAction(Request $request)
    {
        $session = $request->getSession();
        $mes_abierto = $session->get('current_month');
        $anno_abierto = $session->get('current_year');

        $mes_cerrar = ($mes_abierto == 12) ? 1 : $mes_abierto + 1;
        $anno_cerrar = ($mes_abierto == 12) ? $anno_abierto + 1 : $anno_abierto;
        $em = $this->getDoctrine()->getManager();
        $_user = $this->get('security.token_storage')->getToken()->getUser();
        $unidadUser = $_user->getUnidad()->getId();


        $unidad = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:Unidad')->findOneBy(array('id' => $unidadUser));
        $entity = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:CierreMes')->findOneBy(array('cerrado' => false, 'disponible' => true, 'idunidad' => $unidadUser));

//        $anticipos = $em->getRepository('PortadoresBundle:Anticipo')->buscarAnticipo('', '', '', 1 , $unidadUser, '', '');
//        if ($anticipos) {
//            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Existen anticipos abiertos. Verifique por favor'));
//        }

        if (!$entity) {
            $entity = new CierreMes();
            $entity->setMes($mes_abierto);
            $entity->setAnno($anno_abierto);
            $entity->setIdunidad($unidad);
        }

        $entity->setCerrado(true);
        $entity->setDisponible(false);

        $cierreMes = new CierreMes();
        $cierreMes->setMes($mes_cerrar);
        $cierreMes->setAnno($anno_cerrar);
        $cierreMes->setCerrado(false);
        $cierreMes->setDisponible(true);
        $cierreMes->setIdunidad($entity->getIdunidad());

        try {
            $em->persist($entity);
            $em->persist($cierreMes);
            $em->flush();
        } catch (\Exception $ex) {
            if ($ex instanceof HttpException) {
                return new JsonResponse(['success' => false, 'message' => $ex->getMessage()]);
            } else {
                throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $ex->getMessage(), $ex);
            }
        }

        try {
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Cierre realizado con éxito.'));
        } catch (\Exception $ex) {
            if ($ex instanceof HttpException) {
                return new JsonResponse(['success' => false, 'message' => $ex->getMessage()]);
            } else {
                throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $ex->getMessage(), $ex);
            }
        }

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function selectPeriodoAction(Request $request)
    {
        $session = $request->getSession();
        $session->set('selected_year', $request->get('anno'));
        $session->set('selected_month', $request->get('mes'));

        return new Response('ok');
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loadnotificacionesAction()
    {
        $em = $this->getDoctrine()->getManager();

        $_user = $this->get('security.token_storage')->getToken()->getUser();
        $unidades = $em->getRepository('AdminBundle:UsuarioUnidad')->findBy(array('usuario' => $_user->getId()));
        foreach ($unidades as $unidad) {
            $_unidades[] = $unidad->getUnidad()->getId();
        }

        $tipoNotificaciones = array();
        foreach ($_user->getTipo_notificacionesid() as $tipoNotificacion) {
            $tipoNotificaciones [] = $tipoNotificacion->getId();
        }

        $entities = $em->getRepository('PortadoresBundle:Notificaciones')->buscarNotificacion($tipoNotificaciones, $_unidades);
        $total = $em->getRepository('PortadoresBundle:Notificaciones')->buscarNotificacion($tipoNotificaciones, $_unidades, true);

        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'titulo' => $entity->getTitulo(),
                'descripcion' => $entity->getDescripcion(),
                'tipo_notificacionid' => $entity->getTipoNotificacion()->getId(),
                'tipo_notificacion' => $entity->getTipoNotificacion()->getTipoNotificacion(),
                'fecha' => $entity->getFecha()->format('D,j M, Y, h:i:s'),
                'idunidad' => $entity->getIdunidad()->getId(),
                'idunidad_nombre' => $entity->getIdunidad()->getNombre(),
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function delnotificacionesAction()
    {
        $em = $this->getDoctrine()->getManager();

        $_user = $this->get('security.token_storage')->getToken()->getUser();
        $unidades = $em->getRepository('AdminBundle:UsuarioUnidad')->findBy(array('usuario' => $_user->getId()));
        foreach ($unidades as $unidad) {
            $_unidades[] = $unidad->getUnidad()->getId();
        }

        $tipoNotificaciones = array();
        foreach ($_user->getTipo_notificacionesid() as $tipoNotificacion) {
            $tipoNotificaciones [] = $tipoNotificacion->getId();
        }

        $entities = $em->getRepository('PortadoresBundle:Notificaciones')->buscarNotificacion($tipoNotificaciones, $_unidades);

        foreach ($entities as $entity) {
            $entity->setActiva(false);
            $em->persist($entity);
        }

        try {

            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => ''));
        } catch (\Exception $ex) {
            if ($ex instanceof HttpException) {
                return new JsonResponse(['success' => false, 'message' => $ex->getMessage()]);
            } else {
                throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $ex->getMessage(), $ex);
            }
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function cambiarestadoAction(Request $request)
    {
        $session = $request->getSession();
        $session->set('SHOWNOTIF', 'true');
        return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => ''));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getunidadloginAction()
    {
        $em = $this->getDoctrine()->getManager();

        $_user = $this->get('security.token_storage')->getToken()->getUser();
        $unidades = $em->getRepository('AdminBundle:UsuarioUnidad')->findBy(array('usuario' => $_user->getId()));

        foreach ($unidades as $unidad) {
            $_unidades[] = $unidad->getUnidad()->getId();
        }

        return new JsonResponse(array('rows' => $_unidades, 'total' => $_unidades));
    }


    /**
     * Función que se encarga de cargar las entidades
     *
     * @param Request $request
     * @return Response
     */
    public function loadTreeAction(Request $request): Response
    {
        $arr_mes = array(1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre');
        $unidadid = $request->get('unidad_id');
        $em = $this->getDoctrine()->getManager();
        $_user = $this->get('security.token_storage')->getToken()->getUser();

        if ($unidadid !== null && $unidadid !== '') {
            /** @var Unidad $unidadRoot */
            $unidadRoot = $em->getRepository('PortadoresBundle:Unidad')->find($unidadid);
        } else if ($_user->getUsername() === 'admin') {
            /** @var Unidad $unidadRoot */
            $unidadRoot = $em->getRepository('PortadoresBundle:Unidad')->find('apr_portadores_0');
        } else {
            /** @var Unidad $unidadRoot */
            $unidadRoot = $em->getRepository('PortadoresBundle:Unidad')->find($_user->getUnidad());
        }

        $anticipos = $em->getRepository('PortadoresBundle:Anticipo')->buscarAnticipo('', '', '', '', $_user->getUnidad(), '', '');
        $disponible = $em->getRepository('PortadoresBundle:CierreMes')->findOneBy(array('disponible' => true, 'idunidad' => $unidadRoot->getId()));

        $checked = $request->get('checked') ? false : null;
        $_data = array(
            'id' => $unidadRoot->getId(),
            'nombre' => $unidadRoot->getNombre(),
            'text' => $unidadRoot->getNombre(),
            'siglas' => $unidadRoot->getSiglas(),
            'codigo' => $unidadRoot->getCodigo(),
            'provincia' => $unidadRoot->getMunicipio()->getProvinciaid()->getId(),
            'provincia_nombre' => $unidadRoot->getMunicipio()->getProvinciaid()->getNombre(),
            'municipio' => $unidadRoot->getMunicipio()->getId(),
            'municipio_nombre' => $unidadRoot->getMunicipio()->getNombre(),
            'checked' => $checked,
            'mes_anno' => isset($disponible) ? $arr_mes[intval($disponible->getMes())] . ' / ' . $disponible->getAnno() : '-',
            'expanded' => true,
            'mixta' => $unidadRoot->getMixta(),
            'nivel' => $unidadRoot->getNivel() ?: null,
            'readyToClose' => (count($anticipos) > 0) ? false : true

        );
        $firstLevelOnly = $request->query->get('first_level_only') ? 1 : null;
        $tree = $this->findHijos($_data, $em, $checked, $firstLevelOnly);

        return new JsonResponse(array('children' => $tree));
    }

    /**
     * @param $unidad
     * @param $em
     * @param $checked
     * @param $firstLevelOnly
     * @return mixed
     */
    private function findHijos($unidad, $em, $checked, $firstLevelOnly)
    {
        $entitiesHijas = $em->getRepository('PortadoresBundle:Unidad')->findBy(array('padreid' => $unidad['id'], 'visible' => true));
        $arr_mes = array(1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre');
        if (\count($entitiesHijas) === 0) {
            $unidad['leaf'] = true;
            unset($unidad['children'], $unidad['expanded']);
        } else {
            foreach ($entitiesHijas as $entityHija) {
                $anticipos = $em->getRepository('PortadoresBundle:Anticipo')->buscarAnticipo('', '', '', '', $entityHija->getId(), '', '');
                $disponible = $em->getRepository('PortadoresBundle:CierreMes')->findOneBy(array('disponible' => true, 'idunidad' => $entityHija->getId()));
                $arrayHija = array(
                    'id' => $entityHija->getId(),
                    'nombre' => $entityHija->getNombre(),
                    'text' => $entityHija->getNombre(),
                    'siglas' => $entityHija->getSiglas(),
                    'mes_anno' => isset($disponible) ? $arr_mes[intval($disponible->getMes())] . ' / ' . $disponible->getAnno() : '-',
                    'codigo' => $entityHija->getCodigo(),
                    'provincia' => $entityHija->getMunicipio()->getProvinciaid()->getId(),
                    'provincia_nombre' => $entityHija->getMunicipio()->getProvinciaid()->getNombre(),
                    'municipio' => $entityHija->getMunicipio()->getId(),
                    'municipio_nombre' => $entityHija->getMunicipio()->getNombre(),
                    'checked' => $checked,
                    'expanded' => true,
                    'mixta' => $entityHija->getMixta(),
                    'nivel' => $entityHija->getNivel() ?: null,
                    'iconCls' => 'sds',
                    'readyToClose' => (count($anticipos) > 0) ? false : true
                );

                $arrayHija['children'] = array();
                if ($firstLevelOnly === null) {
                    $unidad['children'][] = $this->findHijos($arrayHija, $em, $checked, null);
                } else if ($firstLevelOnly === 2) {
                    $unidad['leaf'] = true;
                    unset($unidad['expanded']);
                } else {
                    $unidad['children'][] = $this->findHijos($arrayHija, $em, $checked, 2);
                }
            }
        }
        return $unidad;
    }
}