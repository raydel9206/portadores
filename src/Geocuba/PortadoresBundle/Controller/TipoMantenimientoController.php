<?php

namespace Geocuba\PortadoresBundle\Controller;
use Doctrine\Common\Util\Debug;
use Geocuba\PortadoresBundle\Entity\Norma;
use Geocuba\PortadoresBundle\Entity\TipoMantenimiento;
use Doctrine\Common\CommonException;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class TipoMantenimientoController extends Controller
{
  
    use ViewActionTrait;
    
    public function loadAction(Request $request)
    {
        $clasificacion  = $request->get('clasificacion');

        $start = $request->get('start');
        $limit = $request->get('limit');

        $entities = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:TipoMantenimiento')->buscarTipoMantenimiento($clasificacion, $start, $limit );
        $count = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:TipoMantenimiento')->buscarTipoMantenimiento($clasificacion, $start, $limit, true );

        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre(),
                'clasificacionid' => $entity->getClasificacion()->getId(),
                'clasificacion' => $entity->getClasificacion()->getNombre()
            );
        }
        return new JsonResponse(array('rows' => $_data, 'total' => $count));
    }

    public function loadComboAction(Request $request)
    {
        $clasificacion  = $request->get('clasificacion');
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('PortadoresBundle:TipoMantenimiento')->buscarTipoMantenimientoCombo($clasificacion);

        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity['id'],
                'nombre' => $entity['nombre'],
            );
        }

        return new JsonResponse(array('rows' => $_data));
    }

    public function loadTipoMantenimientoClasificacionAction()
    {
        $entities = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:TipoMantenimientoClasificacion')->findByVisible(true);
        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre()
            );
        }
        return new JsonResponse(array('rows' => $_data, 'total' => count($_data)));
    }
    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $nombre = trim($request->get('nombre'));
        $clasificacion = trim($request->get('clasificacionid'));

        $entities = $em->getRepository('PortadoresBundle:TipoMantenimiento')->findByNombre($nombre);
        if($entities){
            if($entities[0]->getVisible())
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El tipo de mantenimiento ya existe.'));
            else{
                $entities[0]->setVisible(true);
                $em->persist($entities[0]);
                $em->flush();
                return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Tipo de mantenimiento adicicionado con éxito.'));
            }
        }
        $entity = new TipoMantenimiento();
        $entity->setNombre($nombre);
        $entity->setClasificacion($em->getRepository('PortadoresBundle:TipoMantenimientoClasificacion')->find($clasificacion));

        $entity->setVisible(true);
        try{
            $em->persist($entity);

            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Tipo de mantenimiento  adicionado con éxito.'));
        }
        catch(CommonException $ex){
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }
    public function modAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $nombre = trim($request->get('nombre'));
        $clasificacion = trim($request->get('clasificacionid'));

        $entities = $em->getRepository('PortadoresBundle:TipoMantenimiento')->findByNombre($nombre);
        if($entities)
            if($entities[0]->getId() != $id)
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Existe tipo mantenimeinto  con ese nombre.'));
        $entity = $em->getRepository('PortadoresBundle:TipoMantenimiento')->find($id);
        $entity->setNombre($nombre);
        $entity->setClasificacion($em->getRepository('PortadoresBundle:TipoMantenimientoClasificacion')->find($clasificacion));

        try{
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Tipo de mantenimiento  modificado con éxito.'));
            return $response;
        }
        catch(CommonException $ex){
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }
    public function delAction(Request $request){
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $entity_er = $em->getRepository('PortadoresBundle:TipoMantenimiento');

        try {
            $em->transactional(function () use ($request, $entity_er ) {
                /** @var EntityManager $em */
                $em = func_get_arg(0);

                foreach ($request->request->get('ids') as $entity_id) {

                    $entity = $entity_er ->find($entity_id);

                    if (!$entity) {
                        throw new NotFoundHttpException(sprintf('No existe el tipo de mantenimiento con identificador <strong>%s</strong>', $entity_id));
                    }

                    $em->persist(
                        $entity->setVisible(false)
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
        return new JsonResponse(['success' => true, 'message' => count($request->request->get('ids')) === 1 ? 'Tipo de Mantenimiento eliminado con éxito' : 'Tipos de Mantenimiento eliminados con éxito']);
    }

    public function loadNormaMarcaAction(Request $request)
    {
        $tipoMantenimiento = $request->get('tipo_mantenimiento');


        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('PortadoresBundle:MarcaVehiculo')->findBy(array('visible' => true));

        $data = array();
        foreach ($entities as $entity) {
            $norma = $em->getRepository('PortadoresBundle:Norma')->findOneBy(array('tipoMantenimiento' => $tipoMantenimiento, 'marca' => $entity->getId()));
            if (is_null($norma)) {
                $norma = new Norma();
                $norma->setTipoMantenimiento($em->getRepository('PortadoresBundle:TipoMantenimiento')->findOneBy(array('id' => $tipoMantenimiento)));
                $norma->setMarca($em->getRepository('PortadoresBundle:MarcaVehiculo')->findOneBy(array('id' => $entity->getId())));
                $norma->setCantHoras(0);
                $em->persist($norma);
                $em->flush($norma);
                $cantHoras = $norma->getCantHoras();
            } else {
                $cantHoras = $norma->getCantHoras();
            }
            $data[] = array(
                'idmarca' => $entity->getId(),
                'marca' => $entity->getNombre(),
                'cant_horas' => $cantHoras
            );
        }

        return new JsonResponse(array('rows' => $data, 'total' => count($data)));
    }

    public function  modMarcaNormaAction(Request $request) {

        $valores = json_decode($request->get('datosmod'), true);
        $em = $this->getDoctrine()->getManager();

        for ($i = 0, $iMax = count($valores); $i < $iMax; $i++) {
            $norma = $em->getRepository('PortadoresBundle:Norma')->findOneBy(array('tipoMantenimiento' => $valores[$i]['tipoMantenimiento'], 'marca' => $valores[$i]['marca']));
            $norma->setCantHoras(intval($valores[$i]['cantHoras']));
            $em->persist($norma);
            $em->flush();
        }
        try{
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Norma modificada con éxito.'));
        }catch (Exception $ex){
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'No se pudo modificar la norma para la marca.'));
        }

    }
}