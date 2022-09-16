<?php
/**
 * Created by PhpStorm.
 * User: kireny
 * Date: 5/11/15
 * Time: 13:49
 */

namespace Geocuba\PortadoresBundle\Controller;

use Doctrine\Common\Util\Debug;
use Geocuba\PortadoresBundle\Entity\AreaMedida;
use Doctrine\Common\CommonException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Geocuba\AdminBundle\Util\Util;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;


class AreaMedidaController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        $_nombre = trim($request->get('nombre'));
        $_area = trim($request->get('nombre'));
        $nunidadid = $request->get('unidadid');

        $start = $request->get('start');
        $limit = $request->get('limit');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $entities = $em->getRepository('PortadoresBundle:AreaMedida')->buscarAreaMedidas($_area, $_unidades, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:AreaMedida')->buscarAreaMedidas($_area, $_unidades, $start, $limit, true);

        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre(),
                'nlista_areaid' => $entity->getNlistaAreaid()->getId(),
                'nlista_areanombre' => $entity->getNlistaAreaid()->getNombre(),
                'invalidante' => $entity->getInvalidante()
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $nombre = trim($request->get('nombre'));
        $nlista_areaid = trim($request->get('nlista_areaid'));
        $invalidante = trim($request->get('invalidante'));
        if ($invalidante) {
            $invalida = true;
        } else
            $invalida = false;

        $repetido = $em->getRepository('PortadoresBundle:AreaMedida')->buscarAreaMedidasRepetido($nombre, $nlista_areaid);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe una acción con el mismo nombre en el área seleccionada.'));

        $entity = new AreaMedida();
        $entity->setNombre($nombre);
        $entity->setInvalidante($invalida);
        $entity->setNlistaAreaid($em->getRepository('PortadoresBundle:Area')->find($nlista_areaid));
        $entity->setVisible(true);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Acción adicionada con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function modAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $nombre = trim($request->get('nombre'));
        $nlista_areaid = trim($request->get('nlista_areaid'));
        $invalidante = trim($request->get('invalidante'));
        if ($invalidante) {
            $invalida = true;
        } else
            $invalida = false;

        $repetido = $em->getRepository('PortadoresBundle:AreaMedida')->buscarAreaMedidasRepetido($nombre, $nlista_areaid, $id);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe una acción con el mismo nombre en el área seleccionada.'));

        $entity = $em->getRepository('PortadoresBundle:AreaMedida')->find($id);
        $entity->setNombre($nombre);
        $entity->setInvalidante($invalida);
        $entity->setNlistaAreaid($em->getRepository('PortadoresBundle:Area')->find($nlista_areaid));

        try {
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Acción modificada con éxito.'));
            return $response;
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function delAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $accion_er = $em->getRepository('PortadoresBundle:AreaMedida');

        try {
            $em->transactional(function () use ($request, $accion_er) {
                /** @var EntityManager $em */
                $em = func_get_arg(0);

                foreach ($request->request->get('ids') as $accion_id) {

                    $accion = $accion_er->find($accion_id);

                    if (!$accion) {
                        throw new NotFoundHttpException(sprintf('No existe la Medida con identificador <strong>%s</strong>', $accion_id));
                    }

                    $em->persist(
                        $accion->setVisible(false)
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

        return new JsonResponse(['success' => true, 'message' => count($request->request->get('ids')) === 1 ? 'Medida eliminada con éxito' : 'Medidas eliminadas con éxito']);
    }
}