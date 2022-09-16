<?php
/**
 * Created by PhpStorm.
 * User: javier
 * Date: 20/05/2016
 * Time: 14:12
 */


namespace Geocuba\PortadoresBundle\Controller;
use Geocuba\AdminBundle\Util\Util;
use Geocuba\PortadoresBundle\Entity\Cargo;
use Doctrine\Common\CommonException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;


class CargoController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $_nombre = trim($request->get('nombre'));

        $start = $request->get('start');
        $limit = $request->get('limit');

        $entities = $em->getRepository('PortadoresBundle:Cargo')->buscarCargo($_nombre, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:Cargo')->buscarCargo($_nombre, $start, $limit, true);

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

        $repetido = $em->getRepository('PortadoresBundle:Cargo')->buscarCargoRepetido($nombre);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe un cargo con el mismo nombre.'));

        $entity = new Cargo();
        $entity->setNombre($nombre);
        $entity->setVisible(true);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Cargo  adicionado con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function modAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $nombre = trim($request->get('nombre'));

        $repetido = $em->getRepository('PortadoresBundle:Cargo')->buscarCargoRepetido($nombre);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe un cargo con el mismo nombre.'));

        $entity = $em->getRepository('PortadoresBundle:Cargo')->find($id);
        $entity->setNombre($nombre);
        try {
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Cargo  modificado con éxito.'));
            return $response;
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function delAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $cargo_er = $em->getRepository('PortadoresBundle:Cargo');

        try {
            $em->transactional(function () use ($request, $cargo_er) {
                /** @var EntityManager $em */
                $em = func_get_arg(0);

                foreach ($request->request->get('ids') as $cargo_id) {

                    $cargo = $cargo_er->find($cargo_id);

                    if (!$cargo) {
                        throw new NotFoundHttpException(sprintf('No existe el cargo identificador <strong>%s</strong>', $cargo_id));
                    }

                    $em->persist(
                        $cargo->setVisible(false)
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

        return new JsonResponse(['success' => true, 'message' => count($request->request->get('ids')) === 1 ? 'Cargo eliminado con éxito' : 'Cargos eliminados con éxito']);
    }
}