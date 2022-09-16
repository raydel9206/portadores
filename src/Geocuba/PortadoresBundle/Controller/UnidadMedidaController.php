<?php
/**
 * Created by PhpStorm.
 * User: kireny
 * Date: 4/11/15
 * Time: 9:06
 */

namespace Geocuba\PortadoresBundle\Controller;


use Doctrine\Common\Util\Debug;
use Exception;
use Geocuba\AdminBundle\Util\Util;
use Geocuba\PortadoresBundle\Entity\UnidadMedida;
use Doctrine\Common\CommonException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UnidadMedidaController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $_nombre = trim($request->get('nombre'));

        $start = $request->get('start');
        $limit = $request->get('limit');

        $entities = $em->getRepository('PortadoresBundle:UnidadMedida')->buscarUnidadMedida($_nombre, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:UnidadMedida')->buscarUnidadMedida($_nombre, $start, $limit, true);

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

        $repetido = $em->getRepository('PortadoresBundle:UnidadMedida')->buscarUnidadMedidaRepetido($nombre);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe la unidad de medida.'));

        $entity = new UnidadMedida();
        $entity->setNombre($nombre);
        $entity->setVisible(true);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Unidad de medida adicionada con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function modAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $nombre = trim($request->get('nombre'));

        $repetido = $em->getRepository('PortadoresBundle:UnidadMedida')->buscarUnidadMedidaRepetido($nombre, $id);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe la unidad de medida.'));

        $entity = $em->getRepository('PortadoresBundle:UnidadMedida')->find($id);
        $entity->setNombre($nombre);
        try {
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Unidad de medida modificada con éxito.'));
            return $response;
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function delAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $unidad_medida_er = $em->getRepository('PortadoresBundle:UnidadMedida');

        try {
            $em->transactional(function () use ($request, $unidad_medida_er) {
                /** @var EntityManager $em */
                $em = func_get_arg(0);

                foreach ($request->request->get('ids') as $unidad_medida_id) {

                    $unidad_medida = $unidad_medida_er->find($unidad_medida_id);

                    if (!$unidad_medida) {
                        throw new NotFoundHttpException(sprintf('No existe la unidad me medida con identificador <strong>%s</strong>', $unidad_medida_id));
                    }

                    if (strpos($unidad_medida_id, 'static') !== false)
                        throw new HttpException(500, sprintf('La unidad de medida <strong>%s</strong> es resevada del sistema. No es posible eliminarla.', $unidad_medida->getNombre()));


                    $em->persist(
                        $unidad_medida->setVisible(false)
                    );
                }
            });

            $em->clear();
        } catch (Exception $e) {
            $em->clear();

            if ($e instanceof HttpException) {
                return new JsonResponse(['success' => false, 'message' => $e->getMessage()]);
            }

            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), $e);
        }

        return new JsonResponse(['success' => true, 'message' => count($request->request->get('ids')) === 1 ? 'Unidad de Medida eliminada con éxito' : 'Unidades de Medida eliminadas con éxito']);
    }
}