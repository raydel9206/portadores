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
use Geocuba\PortadoresBundle\Entity\ElementoGasto;
use Doctrine\Common\CommonException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;


class ElementoGastoController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $nunidadid = $request->get('unidadid');

        $unidad = $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid);

        $entities = $em->getRepository('PortadoresBundle:ElementoGasto')->findBy(array('unidad'=>$unidad,'visible'=>true),array('codigo'=>'ASC'));
        $_data = array();
        foreach ($entities as $entity) {
//            $portadores = '';
//            if(count($entity->getPortadores())>0){
//                $portadores.= $entity->getPortadores()[0]->getNombre();
//                for ($i = 1; $i<count($entity->getPortadores()); $i++){
//                    $portadores.=','.$entity->getPortadores()[$i]->getNombre();
//                }
//            }


            $_data[] = array(
                'id' => $entity->getId(),
                'codigo' => $entity->getCodigo(),
                'moneda_id' => $entity->getMoneda()->getId(),
                'moneda' => $entity->getMoneda()->getNombre(),
                'portadores' => count($entity->getPortadores())>0?'SI':'NO',
                'descripcion' => $entity->getDescripcion(),
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => count($_data)));
    }

    public function addAction(Request $request)
    {
        $codigo = trim($request->get('codigo'));
        $descripcion = trim($request->get('descripcion'));
        $nunidadid = $request->get('unidadid');

        $monedaid = trim($request->get('monedaid'));
        $em = $this->getDoctrine()->getManager();

        $repetido = $em->getRepository('PortadoresBundle:ElementoGasto')->buscarElementoGastoRepetido($nunidadid, $codigo);
        if ($repetido > 0)
        return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe una elemento de gasto con el mismo código registrado en el sistema.'));

        $unidad = $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid);
        $elementogasto = new ElementoGasto();
        $elementogasto->setCodigo($codigo);
        $elementogasto->setMoneda($em->getRepository('PortadoresBundle:Moneda')->find($monedaid));
        $elementogasto->setDescripcion($descripcion);
        $elementogasto->setUnidad($unidad);
        $elementogasto->setVisible(true);

        $elementogasto->getPortadores()->clear();
        if($request->get('combustible')){
            $portadores_ids = $this->loadPortadoresAction();
            foreach ($portadores_ids as $portador_id) {
                $portador = $em->getRepository('PortadoresBundle:Portador')->findOneBy(['id' => $portador_id['id'], 'visible' => true]);

                if (!$portador) {
                    throw new NotFoundHttpException(sprintf('No existe Portador con identificador <strong>%s</strong>', $portador_id));
                }
                $elementogasto->addPortadore($portador);
            }
        }


        try {
            $em->persist($elementogasto);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Elemento de gasto adicionado con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function modAction(Request $request)
    {
        $id = $request->get('id');
        $codigo = trim($request->get('codigo'));
        $descripcion = trim($request->get('descripcion'));
        $monedaid = trim($request->get('monedaid'));
        $em = $this->getDoctrine()->getManager();

        $repetido = $em->getRepository('PortadoresBundle:ElementoGasto')->buscarElementoGastoRepetido($codigo, $id);
        if ($repetido > 0)
        return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe una elemento de gasto con el mismo código registrado en el sistema.'));

        $elementogasto = $em->getRepository('PortadoresBundle:ElementoGasto')->find($id);
        $elementogasto->setCodigo($codigo);
        $elementogasto->setMoneda($em->getRepository('PortadoresBundle:Moneda')->find($monedaid));
        $elementogasto->setDescripcion($descripcion);
        $elementogasto->setVisible(true);

        $elementogasto->getPortadores()->clear();
        if($request->get('combustible')){
            $portadores_ids = $this->loadPortadoresAction();
            foreach ($portadores_ids as $portador_id) {
                $portador = $em->getRepository('PortadoresBundle:Portador')->findOneBy(['id' => $portador_id['id'], 'visible' => true]);

                if (!$portador) {
                    throw new NotFoundHttpException(sprintf('No existe Portador con identificador <strong>%s</strong>', $portador_id));
                }
                $elementogasto->addPortadore($portador);
            }
        }

        try {
            $em->persist($elementogasto);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Elemento de gasto modificado con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function delAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $moneda_er = $em->getRepository('PortadoresBundle:ElementoGasto');

        try {
            $em->transactional(function () use ($request, $moneda_er) {
                /** @var EntityManager $em */
                $em = func_get_arg(0);

                foreach ($request->request->get('ids') as $moneda_id) {

                    $moneda = $moneda_er->find($moneda_id);

                    if (!$moneda) {
                        throw new NotFoundHttpException(sprintf('No existe el elemento de gasto con identificador <strong>%s</strong>', $moneda_id));
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

        return new JsonResponse(['success' => true, 'message' => count($request->request->get('ids')) === 1 ? 'Elemento de gasto eliminado con éxito' : 'Elementos de gasto eliminados con éxito']);
    }

    private function loadPortadoresAction()
    {
        $em = $this->getDoctrine()->getManager();

        return $em->getRepository('PortadoresBundle:TipoCombustible')->buscarPortadoresconTComb();
//        Debug::dump($entities);
//        $_data = array();
//
//        foreach ($entities as $entity) {
//            if($em->getRepository('PortadoresBundle:TipoCombustible')->findOneBy(array('portadorid'=>$entity)))
//            $_data[] = array(
//                'id' => $entity->getId(),
//                'nombre' => $entity->getNombre(),
//                'unidad_medidaid' => $entity->getUnidadMedida()->getId(),
//                'unidad_medida' => $entity->getUnidadMedida()->getNombre()
//            );
//        }
//
//        return new JsonResponse(array('rows' => $_data));
    }
}