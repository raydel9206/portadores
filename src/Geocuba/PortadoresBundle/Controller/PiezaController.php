<?php
/**
 * Created by PhpStorm.
 * User: javier
 * Date: 20/05/2016
 * Time: 14:12
 */


namespace Geocuba\PortadoresBundle\Controller;
use Geocuba\AdminBundle\Util\Util;
use Geocuba\PortadoresBundle\Entity\Pieza;
use Doctrine\Common\CommonException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;

class PiezaController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $_nombre = trim($request->get('nombre'));

        $start = $request->get('start');
        $limit = $request->get('limit');

        $entities = $em->getRepository('PortadoresBundle:Pieza')->buscarPieza($_nombre, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:Pieza')->buscarPieza($_nombre, $start, $limit, true);

        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre()
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }
    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $nombre = trim($request->get('nombre'));

        $entities_total = $em->getRepository('PortadoresBundle:Pieza')->findByVisible(true);
        for($i=0;$i<count($entities_total);$i++){
            if(strcasecmp($entities_total[$i]->getNombre(),$nombre) == 0)
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'La Pieza ya existe.'));
        }

        $entities = $em->getRepository('PortadoresBundle:Pieza')->findByNombre($nombre);
        if($entities){
            if($entities[0]->getVisible())
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'La Pieza  ya existe.'));
            else{
                $entities[0]->setVisible(true);
                $em->persist($entities[0]);
                $em->flush();
                return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Pieza adicionada con éxito.'));
            }
        }
        $entity = new Pieza();
        $entity->setNombre($nombre);
        $entity->setVisible(true);
        try{
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Pieza adicionada con éxito.'));
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
        $entities = $em->getRepository('PortadoresBundle:Pieza')->findByNombre($nombre);
        if($entities)
            if($entities[0]->getId() != $id)
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Existe una Pieza con ese nombre.'));
        $entity = $em->getRepository('PortadoresBundle:Pieza')->find($id);
        $entity->setNombre($nombre);
        try{
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Pieza modificada con éxito.'));
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
        $pieza_er = $em->getRepository('PortadoresBundle:Pieza');

        try {
            $em->transactional(function () use ($request, $pieza_er) {
                /** @var EntityManager $em */
                $em = func_get_arg(0);

                foreach ($request->request->get('ids') as $pieza_id) {

                    $pieza = $pieza_er->find($pieza_id);

                    if (!$pieza) {
                        throw new NotFoundHttpException(sprintf('No existe la pieza con identificador <strong>%s</strong>', $pieza_id));
                    }

                    $em->persist(
                        $pieza->setVisible(false)
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

        return new JsonResponse(['success' => true, 'message' => count($request->request->get('ids')) === 1 ? 'Pieza eliminada con éxito' : 'Piezas eliminadas con éxito']);
    }
}