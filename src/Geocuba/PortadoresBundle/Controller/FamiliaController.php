<?php
/**
 * Created by PhpStorm.
 * User: pfcadenas
 * Date: 5/11/15
 * Time: 10:50
 */

namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\AdminBundle\Util\Util;
use Geocuba\PortadoresBundle\Entity\Familia;
use Doctrine\Common\CommonException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;

class FamiliaController extends Controller
{

    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $nombre = $request->get('nombre');
        $start = $request->get('start');
        $limit = $request->get('limit');

        $entities = $em->getRepository('PortadoresBundle:Familia')->buscarFamilia($nombre, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:Familia')->buscarFamilia($nombre, $start, $limit, true);

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

        $repetido = $em->getRepository('PortadoresBundle:Familia')->buscarFamiliaRepetido($nombre);
        if($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Existe otra familia con igual nombre.'));

        $entity = new Familia();
        $entity->setNombre($nombre);
        $entity->setVisible(true);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Familia adicionada con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function modAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = $request->get('id');
        $nombre = trim($request->get('nombre'));

        $repetido = $em->getRepository('PortadoresBundle:Familia')->buscarFamiliaRepetido($nombre, $id);
        if($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Existe otra familia con igual nombre.'));

        $entity = $em->getRepository('PortadoresBundle:Familia')->find($id);
        $entity->setNombre($nombre);

        try {
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Familia modificada con éxito.'));
            return $response;
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function delAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $familia_er = $em->getRepository('PortadoresBundle:Familia');

        try {
            $em->transactional(function () use ($request, $familia_er) {
                /** @var EntityManager $em */
                $em = func_get_arg(0);

                foreach ($request->request->get('ids') as $familia_id) {

                    $familia = $familia_er->find($familia_id);

                    if (!$familia) {
                        throw new NotFoundHttpException(sprintf('No existe la familia de producto con identificador <strong>%s</strong>', $familia_id));
                    }

                    $em->persist(
                        $familia->setVisible(false)
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

        return new JsonResponse(['success' => true, 'message' => count($request->request->get('ids')) === 1 ? 'La Familia de Productos se ha eliminado con éxito' : 'Las Familias de Productos se han eliminado con éxito']);
    }
}