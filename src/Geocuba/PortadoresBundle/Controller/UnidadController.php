<?php
/**
 * Created by PhpStorm.
 * User: orlando
 * Date: 14/04/14
 * Time: 11:53
 */

namespace Geocuba\PortadoresBundle\Controller;

use Doctrine\Common\CommonException;
use Doctrine\Common\Util\Debug;
use Geocuba\PortadoresBundle\Entity\Unidad;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;



/**
 * Class UnidadController
 * @package Geocuba\PortadoresBundle\Controller
 */
class UnidadController extends Controller
{
    use ViewActionTrait;

    /**
     * Función que se encarga de cargar las entidades
     *
     * @param Request $request
     * @return Response
     */
public function loadTreeAction(Request $request): Response
    {
        $unidadid = $request->get('unidad_id');
        $em = $this->getDoctrine()->getManager();
        $_user = $this->get('security.token_storage')->getToken()->getUser();

        if ($unidadid !== null && $unidadid !== '') {
            /** @var Unidad $unidadRoot */
            $unidadRoot = $em->getRepository('PortadoresBundle:Unidad')->find($unidadid);
        }
        else if ($_user->getUsername() === 'admin') {
            /** @var Unidad $unidadRoot */
            $unidadRoot = $em->getRepository('PortadoresBundle:Unidad')->find('apr_portadores_0');
        }
        else {
            /** @var Unidad $unidadRoot */
            $unidadRoot = $em->getRepository('PortadoresBundle:Unidad')->find($_user->getUnidad());
        }

        $checked = $request->get('checked') ? false : null;
        $_data = array(
            'id' => $unidadRoot->getId(),
            'nombre' => $unidadRoot->getNombre(),
            'text' => $unidadRoot->getNombre(),
            'siglas' => $unidadRoot->getSiglas(),
            'codigo' => $unidadRoot->getCodigo(),
            'codfincimex' => $unidadRoot->getCodfincimex(),
            'provincia' => $unidadRoot->getMunicipio()->getProvinciaid()->getId(),
            'provincia_nombre' => $unidadRoot->getMunicipio()->getProvinciaid()->getNombre(),
            'municipio' => $unidadRoot->getMunicipio()->getId(),
            'municipio_nombre' => $unidadRoot->getMunicipio()->getNombre(),
            'checked' => $checked,
            'expanded' => true,
            'mixta' => $unidadRoot->getMixta(),
            'nivel' => $unidadRoot->getNivel() ?: null,
//            'iconCls'=> ''
        );
        $firstLevelOnly = $request->query->get('first_level_only') ? 1 : null;
        $tree = $this->findHijos($_data, $em, $checked, $firstLevelOnly);

        return new JsonResponse(array('children' => $tree));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $nombre = $request->get('nombre');
        $siglas = $request->get('siglas');
        $codigo = $request->get('codigo');
        $codfincimex = $request->get('codfincimex');
        $padreid = $request->get('padreid');
        $municipio_id = $request->get('municipio');
        $municipio = $em->getRepository('PortadoresBundle:Municipio')->find($municipio_id);
        $children = $em->getRepository('PortadoresBundle:Unidad')->findBy(array('padreid' => $padreid));
        if ($children !== null){
            foreach ($children as $entity) {
                if (strcasecmp($entity->getNombre(), $nombre) === 0) {
                    return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Una unidad con este nombre ya existe.'));
                }
            }
        }

        $unidad = new Unidad();
        $unidad->setNombre($nombre);
        $unidad->setSiglas($siglas);
        $unidad->setCodigo($codigo);
        $unidad->setCodigo($codigo);
        $unidad->setCodfincimex($codfincimex);
        $unidad->setPadreid($padreid ? $em->getRepository('PortadoresBundle:Unidad')->find($padreid) : null);
        $unidad->setMunicipio($municipio);
        $unidad->setVisible(true);
        $unidad->setMixta($request->request->get('mixta') ?? false);
        $unidad->setNivel($request->request->get('nivel'));

        try{
            $em->persist($unidad);
            $em->flush();
        }
        catch(\Exception $ex){
            return new JsonResponse(array('success' => false, 'cls' => 'danger','message'=>'Datos incorrectos o imposible establecer comunicación con el servidor en estos momentos.'));

        }
        return new JsonResponse(array('success' => true, 'cls' => 'success','message'=>'Unidad adicionada con éxito.'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $nombre = $request->get('nombre');
        $siglas = $request->get('siglas');
        $codigo = $request->get('codigo');
        $codfincimex = $request->get('codfincimex');
        $municipio_id = $request->get('municipio');
        $municipio = $em->getRepository('PortadoresBundle:Municipio')->find($municipio_id);
        $unidad = $em->getRepository('PortadoresBundle:Unidad')->find($id);

        $unidadPadre = $unidad->getPadreid() ? $unidad->getPadreid()->getId() : $unidad->getId();
        $children = $em->getRepository('PortadoresBundle:Unidad')->findBy(array('padreid' => $unidadPadre));
        if ($children !== null){
            foreach ($children as $entity) {
                if ($entity->getId() != $id && strcasecmp($entity->getNombre(), $nombre) === 0) {
                    return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Una unidad con ese nombre ya existe.'));
                }
            }
        }

        $unidad->setNombre($nombre);
        $unidad->setSiglas($siglas);
        $unidad->setCodigo($codigo);
        $unidad->setCodfincimex($codfincimex);
        $unidad->setMunicipio($municipio);
        $unidad->setMixta($request->request->get('mixta') ?: false);
        $unidad->setNivel($request->request->get('nivel'));

        try{
            $em->persist($unidad);
            $em->flush();
        }
        catch(\Exception $ex){
            return new JsonResponse(array('success' => false, 'cls' => 'danger','message'=>'Datos incorrectos o imposible establecer comunicación con el servidor en estos momentos.'));

        }
        return new JsonResponse(array('success' => true, 'cls' => 'success', 'message'=>'Unidad modificada con éxito.'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function delAction(Request $request): JsonResponse
    {
        $id = $request->get('id');
        if($id === 'apr_portadores_0'){
            return new JsonResponse(array('success' => false, 'cls' => 'danger','message'=>'No se puede eliminar la unidad raíz.'));
        }
        $em = $this->getDoctrine()->getManager();
        $unidad = $em->getRepository('PortadoresBundle:Unidad')->find($id);
        $unidad->setVisible(false);
        try{
            $em->persist($unidad);
            $em->flush();
        }
        catch(\Exception $ex){
            return new JsonResponse(array('success' => false, 'cls' => 'danger','message'=>'Imposible establecer comunicación con el servidor en estos momentos.'));
        }
        return new JsonResponse(array('success' => true, 'cls' => 'success', 'message'=>'Unidad eliminada con éxito.'));
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
        if (\count($entitiesHijas) === 0) {
            $unidad['leaf'] = true;
            unset($unidad['children'], $unidad['expanded']);
        } else {
            foreach ($entitiesHijas as $entityHija) {
                $arrayHija = array(
                    'id' => $entityHija->getId(),
                    'nombre' => $entityHija->getNombre(),
                    'text' => $entityHija->getNombre(),
                    'siglas' => $entityHija->getSiglas(),
                    'codigo' => $entityHija->getCodigo(),
                    'codfincimex' => $entityHija->getCodfincimex(),
                    'provincia' => $entityHija->getMunicipio()->getProvinciaid()->getId(),
                    'provincia_nombre' => $entityHija->getMunicipio()->getProvinciaid()->getNombre(),
                    'municipio' => $entityHija->getMunicipio()->getId(),
                    'municipio_nombre' => $entityHija->getMunicipio()->getNombre(),
                    'checked' => $checked,
                    'expanded' => true,
                    'mixta' => $entityHija->getMixta(),
                    'nivel' => $entityHija->getNivel() ?: null,
                    'iconCls' => 'sds'
                );

                $arrayHija['children'] = array();
                if ($firstLevelOnly === null) {
                    $unidad['children'][] = $this->findHijos($arrayHija, $em, $checked, null);
                }
                else if ($firstLevelOnly === 2){
                    $unidad['leaf'] = true;
                    unset($unidad['expanded']);
                }
                else{
                    $unidad['children'][] = $this->findHijos($arrayHija, $em, $checked, 2);
                }
            }
        }
        return $unidad;
    }
}