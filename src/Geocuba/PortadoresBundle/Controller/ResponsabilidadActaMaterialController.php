<?php
/**
 * Created by PhpStorm.
 * User: kireny
 * Date: 3/10/16
 * Time: 12:12
 */
namespace Geocuba\PortadoresBundle\Controller;
use Geocuba\PortadoresBundle\Entity\ResponsabilidadActaMaterial;
use Doctrine\Common\CommonException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Geocuba\AdminBundle\Util\Util;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;

class ResponsabilidadActaMaterialController extends Controller
{

    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $_nombre = trim($request->get('nombre'));

        $start = $request->get('start');
        $limit = $request->get('limit');

        $entities = $em->getRepository('PortadoresBundle:ResponsabilidadActaMaterial')->buscarResponsabilidadActaMaterial($_nombre, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:ResponsabilidadActaMaterial')->buscarResponsabilidadActaMaterial($_nombre, $start, $limit, true);

        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre(),
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $nombre = trim($request->get('nombre'));

        $repetido = $em->getRepository('PortadoresBundle:ResponsabilidadActaMaterial')->buscarResponsabilidadActaMaterialRepetido($nombre);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe un responsabilidad de acta material similar.'));

        $entity = new ResponsabilidadActaMaterial();
        $entity->setNombre($nombre);
        $entity->setVisible(true);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Responsabilidad adicionada con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function modAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $nombre = trim($request->get('nombre'));

        $entity = $em->getRepository('PortadoresBundle:ResponsabilidadActaMaterial')->find($id);
        $entity->setNombre($nombre);
        try {
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Responsabilidad  modificada con éxito.'));
            return $response;
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function delAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $responsabilidad_er = $em->getRepository('PortadoresBundle:ResponsabilidadActaMaterial');

        try {
            $em->transactional(function () use ($request, $responsabilidad_er) {
                /** @var EntityManager $em */
                $em = func_get_arg(0);

                foreach ($request->request->get('ids') as $responsabilidad_id) {

                    $responsabilidad = $responsabilidad_er->find($responsabilidad_id);

                    if (!$responsabilidad) {
                        throw new NotFoundHttpException(sprintf('No existe la responsabilidad con identificador <strong>%s</strong>', $responsabilidad_id));
                    }

                    $em->persist(
                        $responsabilidad->setVisible(false)
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

        return new JsonResponse(['success' => true, 'message' => count($request->request->get('ids')) === 1 ? 'Responsabilidad eliminada con éxito' : 'Responsabilidades eliminadas con éxito']);
    }
}