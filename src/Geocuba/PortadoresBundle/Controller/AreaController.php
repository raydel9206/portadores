<?php
/**
 * Created by PhpStorm.
 * User: kireny
 * Date: 5/11/15
 * Time: 10:50
 */

namespace Geocuba\PortadoresBundle\Controller;

use Doctrine\Common\CommonException;
use Doctrine\Common\Util\Debug;
use Geocuba\PortadoresBundle\Entity\Area;
use Geocuba\PortadoresBundle\Entity\Unidad;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\PortadoresBundle\Util\Utiles;


class AreaController extends Controller
{
    // See below how it is used.
    const FLUSH_THRESHOLD = 100;

    use ViewActionTrait;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $_nombre = trim($request->get('nombre'));
        $nunidadid = $request->get('unidadid');

        $start = $request->get('start');
        $limit = $request->get('limit');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $qb = $em->createQueryBuilder();
        $qb->select('area')
            ->from('PortadoresBundle:Area', 'area')
            ->Where($qb->expr()->in('area.unidad', $_unidades))
            ->andWhere($qb->expr()->eq('area.visible', 'true'));
        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('UPPER(area.nombre) like UPPER(:nombre)')
                ->setParameter('nombre', "%$_nombre%");
        }

        $entities = $qb->orderBy('area.nombre', 'ASC')
            ->getQuery()
            ->setMaxResults($limit)
            ->setFirstResult($start)
            ->getResult();

        $qb = $em->createQueryBuilder();
        $qb->select('count(area)')
            ->from('PortadoresBundle:Area', 'area')
            ->Where($qb->expr()->in('area.unidad', $_unidades))
            ->andWhere($qb->expr()->eq('area.visible', 'true'));
        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('UPPER(area.nombre) like UPPER(:nombre)')
                ->setParameter('nombre', "%$_nombre%");
        }
        $total = $qb->getQuery()->getSingleScalarResult();


        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre(),
                'unidad' => $entity->getUnidad()->getId()
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    public function loadComboAction(Request $request)
    {
        $nunidadid = $request->get('unidadid');

        $_data = array();
        $em = $this->getDoctrine()->getManager();

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $entities = $em->getRepository('PortadoresBundle:Area')->buscarAreaCombo($_unidades);

        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity['id'],
                'nombre' => $entity['nombre'],
                'nunidadid' => $entity['nunidadid'],
            );
        }

        return new JsonResponse(array('rows' => $_data));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $nombre = trim($request->get('nombre'));

        $nunidadid = $request->get('unidadid');

        $unidad = $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid);

        $repetido = $em->getRepository('PortadoresBundle:Area')->buscarAreaRepetido($nombre);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El área ya existe.'));

        $entity = new Area();
        $entity->setNombre($nombre);
        $entity->setUnidad($unidad);
        $entity->setVisible(true);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Área adicionada con éxito.'));
        } catch (CommonException $ex) {
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

        $_user = $this->get('security.token_storage')->getToken()->getUser();
        $nunidadid = $request->get('unidadid');

        $unidad = $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid);

        $repetido = $em->getRepository('PortadoresBundle:Area')->buscarAreaRepetido($nombre, $id);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El área ya existe.'));

        $entity = $em->getRepository('PortadoresBundle:Area')->find($id);
        $entity->setNombre($nombre);
        $entity->setUnidad($unidad);
        try {
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Área modificada con éxito.'));
            return $response;
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function delAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $area_er = $em->getRepository('PortadoresBundle:Area');

        try {
            $em->transactional(function () use ($request, $area_er) {
                /** @var EntityManager $em */
                $em = func_get_arg(0);

                foreach ($request->request->get('ids') as $area_id) {

                    $area = $area_er->find($area_id);

                    if (!$area) {
                        throw new NotFoundHttpException(sprintf('No existe la área con identificador <strong>%s</strong>', $area_id));
                    }

                    $em->persist(
                        $area->setVisible(false)
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

        return new JsonResponse(['success' => true, 'message' => count($request->request->get('ids')) === 1 ? 'Área eliminada con éxito' : 'Áreas eliminadas con éxito']);
    }
}