<?php
/**
 * Created by PhpStorm.
 * User: orlando
 * Date: 06/10/2015
 * Time: 9:16
 */


namespace Geocuba\PortadoresBundle\Controller;
use Doctrine\Common\Util\Debug;
use Geocuba\AdminBundle\Util\Util;
use Geocuba\PortadoresBundle\Entity\NcentroCosto;
use Geocuba\PortadoresBundle\Entity\DetalleGasto;
use Doctrine\Common\CommonException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;


class DetalleGastoController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {

        $elementogasto_id = $request->get('elementogastoid');
        $em = $this->getDoctrine()->getManager();
        $monedaid = '';

        $nunidadid = $request->get('unidadid');

        $_portadores = array();
        if($elementogasto_id != null){
            $elementogasto = $em->getRepository('PortadoresBundle:ElementoGasto')->find($elementogasto_id);
            $monedaid = $elementogasto->getMoneda()->getId();
            foreach ($elementogasto->getPortadores() as $portador){
                $_portadores[] = $portador->getId();
            }
        }

        $_data = array();
        if($elementogasto_id != null) {
            $entities = $em->getRepository('PortadoresBundle:DetalleGasto')->buscarDetalleGasto($nunidadid, $_portadores, $monedaid);
            if(count($_portadores)>0){
                foreach ($entities as $entity) {
                    $_data[] = array(
                        'id' => $entity->getId(),
                        'codigo' => $entity->getCodigo(),
                        'descripcion' => $entity->getDescripcion(),
                        'monedaid' => $entity->getMoneda()->getId(),
                        'moneda' => $entity->getMoneda()->getNombre(),
                        'tipo_combustible_id' => $entity->getNtipoCombustibleid()!=null?$entity->getNtipoCombustibleid()->getId():'',
                        'tipo_combustible' => $entity->getNtipoCombustibleid()!=null?$entity->getNtipoCombustibleid()->getNombre():'',
                    );
                }
            }
            else{
                foreach ($entities as $entity) {
                    if(!$entity->getNtipoCombustibleid())
                    $_data[] = array(
                        'id' => $entity->getId(),
                        'codigo' => $entity->getCodigo(),
                        'descripcion' => $entity->getDescripcion(),
                        'monedaid' => $entity->getMoneda()->getId(),
                        'moneda' => $entity->getMoneda()->getNombre(),
                        'tipo_combustible_id' => $entity->getNtipoCombustibleid()!=null?$entity->getNtipoCombustibleid()->getId():'',
                        'tipo_combustible' => $entity->getNtipoCombustibleid()!=null?$entity->getNtipoCombustibleid()->getNombre():'',
                    );
                }
            }

        }
        else{
            $entities = $em->getRepository('PortadoresBundle:DetalleGasto')->buscarDetalleGasto($nunidadid);
            foreach ($entities as $entity) {
                $_data[] = array(
                    'id' => $entity->getId(),
                    'codigo' => $entity->getCodigo(),
                    'descripcion' => $entity->getDescripcion(),
                    'monedaid' => $entity->getMoneda()->getId(),
                    'moneda' => $entity->getMoneda()->getNombre(),
                    'tipo_combustible_id' => $entity->getNtipoCombustibleid()!=null?$entity->getNtipoCombustibleid()->getId():'',
                    'tipo_combustible' => $entity->getNtipoCombustibleid()!=null?$entity->getNtipoCombustibleid()->getNombre():'',
                );
            }
        }

//        Debug::dump($entities);




        return new JsonResponse(array('rows' => $_data, 'total' => count($_data)));
    }

    public function addAction(Request $request)
    {
        $codigo = $request->get('codigo');
        $tipocombustibleid = $request->get('tipocombustibleid');
        $monedaid = $request->get('monedaid');
        $descripcion = $request->get('descripcion');
        $nunidadid = $request->get('unidadid');

        $em = $this->getDoctrine()->getManager();

        $repetido = $em->getRepository('PortadoresBundle:DetalleGasto')->buscarDetalleGastoRepetido($nunidadid, $codigo);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe un detalle del gasto con el mismo código registrado en el sistema.'));

        $unidad = $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid);
        $entity = new DetalleGasto();
        $entity->setCodigo($codigo);
        $entity->setDescripcion($descripcion);
        $entity->setNtipoCombustibleid($em->getRepository('PortadoresBundle:TipoCombustible')->find($tipocombustibleid));
        $entity->setMoneda($em->getRepository('PortadoresBundle:Moneda')->find($monedaid));
        $entity->setUnidad($unidad);
        $entity->setVisible(true);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Detalle del gasto adicionado con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function modAction(Request $request)
    {
        $id = $request->get('id');
        $codigo = $request->get('codigo');
        $tipocombustibleid = $request->get('tipocombustibleid');
        $monedaid = $request->get('monedaid');
        $descripcion = $request->get('descripcion');

        $em = $this->getDoctrine()->getManager();

        $repetido = $em->getRepository('PortadoresBundle:DetalleGasto')->buscarDetalleGastoRepetido($codigo,$id);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe un detalle del gasto con el mismo código registrado en el sistema.'));

        $entity = $em->getRepository('PortadoresBundle:DetalleGasto')->find($id);
        $entity->setCodigo($codigo);
        $entity->setDescripcion($descripcion);
        $entity->setNtipoCombustibleid($em->getRepository('PortadoresBundle:TipoCombustible')->find($tipocombustibleid));
        $entity->setMoneda($em->getRepository('PortadoresBundle:Moneda')->find($monedaid));
        $entity->setVisible(true);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Detalle del gasto modificado con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function delAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $moneda_er = $em->getRepository('PortadoresBundle:DetalleGasto');

        try {
            $em->transactional(function () use ($request, $moneda_er) {
                /** @var EntityManager $em */
                $em = func_get_arg(0);

                foreach ($request->request->get('ids') as $moneda_id) {

                    $moneda = $moneda_er->find($moneda_id);

                    if (!$moneda) {
                        throw new NotFoundHttpException(sprintf('No existe el detalle del gasto con identificador <strong>%s</strong>', $moneda_id));
                    }

                    $em->persist(
                        $moneda->setVisible(false)
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

        return new JsonResponse(['success' => true, 'message' => count($request->request->get('ids')) === 1 ? 'Detalle del gasto eliminado con éxito' : 'Detalles del gasto eliminados con éxito']);
    }
}