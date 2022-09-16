<?php

namespace Geocuba\PortadoresBundle\Controller;

use Doctrine\Common\Util\Debug;
use Geocuba\PortadoresBundle\Entity\Caja;
use Doctrine\Common\CommonException;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\PortadoresBundle\Util\Utiles;


class CajaController extends Controller
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
        $nunidadid = trim($request->get('unidad'));

        $start = $request->get('start');
        $limit = $request->get('limit');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $entities = $em->getRepository('PortadoresBundle:Caja')->buscarCaja($_nombre, $_unidades, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:Caja')->buscarCaja($_nombre, $_unidades, $start, $limit, true);

        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre(),
                'nunidadid' => $entity->getNunidadid()->getId(),
                'nnombreunidadid' => $entity->getNunidadid()->getNombre()
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

        $entities = $em->getRepository('PortadoresBundle:Caja')->buscarCajaCombo($_unidades);
//        Debug::dump($entities);

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
        $unidadid = trim($request->get('nunidadid'));

        $repetido = $em->getRepository('PortadoresBundle:Caja')->buscarCajaRepetido($nombre, $unidadid);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe una caja con el mismo nombre en la unidad seleccionada.'));

        $unidad = $em->getRepository('PortadoresBundle:Unidad')->find($unidadid);
        $entity = new Caja();
        $entity->setNombre($nombre);
        $entity->setNunidadid($unidad);
        $entity->setVisible(true);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => ' Caja  adicionada con éxito.'));
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
        $unidadid = trim($request->get('nunidadid'));

        $repetido = $em->getRepository('PortadoresBundle:Caja')->buscarCajaRepetido($nombre, $unidadid, $id);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe una caja con el mismo nombre en la unidad seleccionada.'));

        $entity = $em->getRepository('PortadoresBundle:Caja')->find($id);
        $entity->setNombre($nombre);
        $entity->setNunidadid($em->getRepository('PortadoresBundle:Unidad')->find($unidadid));
        try {
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Caja  modificada con éxito.'));
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
        $caja_er = $em->getRepository('PortadoresBundle:Caja');

        try {
            $em->transactional(function () use ($request, $caja_er) {
                /** @var EntityManager $em */
                $em = func_get_arg(0);

                foreach ($request->request->get('ids') as $caja_id) {

                    $caja = $caja_er->find($caja_id);

                    if (!$caja) {
                        throw new NotFoundHttpException(sprintf('No existe la Caja con identificador <strong>%s</strong>', $caja_id));
                    }

                    $em->persist(
                        $caja->setVisible(false)
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

        return new JsonResponse(['success' => true, 'message' => count($request->request->get('ids')) === 1 ? 'Caja eliminada con éxito' : 'Cajas eliminadas con éxito']);
    }

    public function loadByRootAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $_nombre = trim($request->get('nombre'));
        $nunidadid = trim($request->get('unidad'));

        $start = $request->get('start');
        $limit = $request->get('limit');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $unidadSelected = $em->getRepository('PortadoresBundle:Unidad')->findOneBy(array('id' => $nunidadid));
        $firstPadre = $unidadSelected->getPadreId()->getId();
        $unidadSecond = $em->getRepository('PortadoresBundle:Unidad')->findOneBy(array('id' => $firstPadre));
        $secondPadre = $unidadSecond->getPadreId()->getId();

        if ($firstPadre && $secondPadre) {
            $entities = $em->getRepository('PortadoresBundle:Caja')->buscarCaja($_nombre, $firstPadre, $start, $limit);
            $total = $em->getRepository('PortadoresBundle:Caja')->buscarCaja($_nombre, $firstPadre, $start, $limit, true);
        } else {
            $entities = $em->getRepository('PortadoresBundle:Caja')->buscarCaja($_nombre, $_unidades, $start, $limit);
            $total = $em->getRepository('PortadoresBundle:Caja')->buscarCaja($_nombre, $_unidades, $start, $limit, true);
        }


        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre(),
                'nunidadid' => $entity->getNunidadid()->getId(),
                'nnombreunidadid' => $entity->getNunidadid()->getNombre()
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loadRootAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $nunidadid = trim($request->get('unidadid'));

        $unidadSelected = $em->getRepository('PortadoresBundle:Unidad')->findOneBy(array('id' => $nunidadid));
        $firstPadre = ($unidadSelected) ? $unidadSelected->getPadreId() : null;
        $unidadSecond = ($firstPadre) ? $em->getRepository('PortadoresBundle:Unidad')->findOneBy(array('id' => $firstPadre->getId())): null;
        $secondPadre = ($unidadSecond) ? $unidadSecond->getPadreId(): null;

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $entities = $em->getRepository('PortadoresBundle:Caja')->buscarCaja('', $_unidades);

        if ($firstPadre && $secondPadre) {
            $_unidades = [];
            Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($firstPadre->getId()), $_unidades);

            $entities = $em->getRepository('PortadoresBundle:Caja')->buscarCaja('', $_unidades);
        }

        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre(),
                'nunidadid' => $entity->getNunidadid()->getId(),
                'nnombreunidadid' => $entity->getNunidadid()->getNombre()
            );
        }

        return new JsonResponse(array('rows' => $_data));
    }
}