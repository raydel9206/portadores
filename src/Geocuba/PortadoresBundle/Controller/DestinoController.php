<?php
/**
 * Created by PhpStorm.
 * User: javier
 * Date: 20/05/2016
 * Time: 14:12
 */


namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\PortadoresBundle\Entity\Destino;
use Doctrine\Common\CommonException;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\PortadoresBundle\Util\Utiles;


class DestinoController extends Controller
{
   use ViewActionTrait;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $_nombre = trim($request->get('nombre'));

        $start = $request->get('start');
        $limit = $request->get('limit');

        $entities = $em->getRepository('PortadoresBundle:Destino')->buscarDestino($_nombre, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:Destino')->buscarDestino($_nombre, $start, $limit, true);

        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre()
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $nombre = trim($request->get('nombre'));

        $entities_total = $em->getRepository('PortadoresBundle:Destino')->findByVisible(true);
        for($i=0;$i<count($entities_total);$i++){
            if(strcasecmp($entities_total[$i]->getNombre(),$nombre) == 0)
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El Destino ya existe.'));
        }

        $entities = $em->getRepository('PortadoresBundle:Destino')->findByNombre($nombre);
        if($entities){
            if($entities[0]->getVisible())
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El Destino  ya existe.'));
            else{
                $entities[0]->setVisible(true);
                $em->persist($entities[0]);
                $em->flush();
                return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Destino  adicionado con éxito.'));
            }
        }
        $entity = new Destino();
        $entity->setNombre($nombre);
        $entity->setVisible(true);
        try{
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Destino  adicionado con éxito.'));
        }
        catch(CommonException $ex){
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function modAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $nombre = trim($request->get('nombre'));
        $entities = $em->getRepository('PortadoresBundle:Destino')->findByNombre($nombre);
        if($entities)
            if($entities[0]->getId() != $id)
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Existe un Destino  con ese nombre.'));
        $entity = $em->getRepository('PortadoresBundle:Destino')->find($id);
        $entity->setNombre($nombre);
        try{
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Destino  modificado con éxito.'));
            return $response;
        }
        catch(CommonException $ex){
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function delAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $destino_er = $em->getRepository('PortadoresBundle:Destino');

        try {
            $em->transactional(function () use ($request, $destino_er) {
                /** @var EntityManager $em */
                $em = func_get_arg(0);

                foreach ($request->request->get('ids') as $destino_id) {

                    $destino = $destino_er->find($destino_id);

                    if (!$destino) {
                        throw new NotFoundHttpException(sprintf('No existe el destino con identificador <strong>%s</strong>', $destino_id));
                    }

                    $em->persist(
                        $destino->setVisible(false)
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

        return new JsonResponse(['success' => true, 'message' => count($request->request->get('ids')) === 1 ? 'Destino eliminado con éxito' : 'Destinos eliminadas con éxito']);
    }
}