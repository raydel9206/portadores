<?php
/**
 * Created by PhpStorm.
 * User: yosley
 * Date: 06/10/2015
 * Time: 9:16
 */


namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\AdminBundle\Entity\UsuarioUnidad;
use Geocuba\PortadoresBundle\Entity\CentroCosto;
use Doctrine\Common\CommonException;
use Geocuba\PortadoresBundle\Util\Utiles;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CentroCostoController extends Controller
{
    // See below how it is used.
    const FLUSH_THRESHOLD = 100;

    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $_nombre = trim($request->get('nombre'));
        $nunidadid = trim($request->get('unidadid'));

        $start = $request->get('start');
        $limit = $request->get('limit');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $entities = $em->getRepository('PortadoresBundle:CentroCosto')->buscarCentroCosto($_nombre, $_unidades, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:CentroCosto')->buscarCentroCosto($_nombre, $_unidades, $start, $limit, true);

        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre(),
                'codigo' => $entity->getCodigo(),
                'unidadid' => $entity->getNunidadid()->getId(),
                'nombreunidadid' => $entity->getNunidadid()->getNombre()
            );
        }

        return new JsonResponse(array('success' => true, 'rows' => $_data, 'total' => $total));
    }

    public function loadComboAction(Request $request)
    {
        $nunidadid = $request->get('unidadid');

        $_data = array();
        $em = $this->getDoctrine()->getManager();

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $entities = $em->getRepository('PortadoresBundle:CentroCosto')->buscarCentroCostoCombo($_unidades);
//        Debug::dump($entities);

        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity['id'],
                'nombre' => $entity['nombre'],
                'codigo' => $entity['codigo'],
                'nunidadid' => $entity['nunidadid'],
            );
        }

        return new JsonResponse(array('rows' => $_data));
    }

    public function addAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $nombre = trim($request->get('nombre'));
        $codigo = trim($request->get('codigo'));
        $unidad = $request->get('unidadid');

        $repetido = $em->getRepository('PortadoresBundle:CentroCosto')->buscarCentroCostoRepetido($nombre, $unidad);

//        var_dump($repetido);die;

        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe un centro de costo con el mismo nombre en la unidad seleccionada.'));

        $entity = new CentroCosto();
        $entity->setNombre($nombre);
        $entity->setCodigo($codigo);
        $entity->setNunidadid($em->getRepository('PortadoresBundle:Unidad')->find($unidad));
        $entity->setVisible(true);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Centro de Costo adicionado con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function modAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $nombre = trim($request->get('nombre'));
        $codigo = trim($request->get('codigo'));
        $unidad = trim($request->get('unidadid'));

        $repetido = $em->getRepository('PortadoresBundle:CentroCosto')->buscarCentroCostoRepetido($nombre, $unidad, $id);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe un centro de costo con el mismo nombre en la unidad seleccionada.'));

        $entity = $em->getRepository('PortadoresBundle:CentroCosto')->find($id);
        $entity->setNombre($nombre);
        $entity->setCodigo($codigo);
        $entity->setNunidadid($em->getRepository('PortadoresBundle:Unidad')->find($unidad));
        try {
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Centro de costo modificado con éxito.'));
            return $response;
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function delAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $centro_costo_er = $em->getRepository('PortadoresBundle:CentroCosto');

        try {
            $em->transactional(function () use ($request, $centro_costo_er) {
                /** @var EntityManager $em */
                $em = func_get_arg(0);

                foreach ($request->request->get('ids') as $centro_costo_id) {

                    $centro_costo = $centro_costo_er->find($centro_costo_id);

                    if (!$centro_costo) {
                        throw new NotFoundHttpException(sprintf('No existe el Centro de Costo con identificador <strong>%s</strong>', $centro_costo_id));
                    }

                    $em->persist(
                        $centro_costo->setVisible(false)
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

        return new JsonResponse(['success' => true, 'message' => count($request->request->get('ids')) === 1 ? 'El Centro de Costo ha sido eliminado' : 'Los centros de costos han sido eliminados']);
    }
}