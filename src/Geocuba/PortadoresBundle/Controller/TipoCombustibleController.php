<?php

namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\PortadoresBundle\Entity\TipoCombustible;
use Doctrine\Common\CommonException;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TipoCombustibleController extends Controller
{
    use ViewActionTrait;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $_nombre = trim($request->get('nombre'));

        $start = $request->get('start');
        $limit = $request->get('limit');

        $entities = $em->getRepository('PortadoresBundle:TipoCombustible')->buscarTipoCombustible($_nombre, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:TipoCombustible')->buscarTipoCombustible($_nombre, $start, $limit, true);

        $_data = array();
        /** @var TipoCombustible $entity */
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre(),
                'codigo' => $entity->getCodigo(),
                'precio' => $entity->getPrecio(),
                'precio_tiro_directo' => $entity->getPrecioTD(),
                'maximo_tarjeta_dinero' => $entity->getMaximoTarjetaDinero(),
                'maximo_tarjeta_litro' => $entity->getMaximoTarjetaLitro(),
                'portador_id' => ($entity->getPortadorid()) ? $entity->getPortadorid()->getId() : '',
                'portador_nombre' => ($entity->getPortadorid()) ? $entity->getPortadorid()->getNombre() : '',
                'filaid' => $entity->getFila(),
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    public function loadComboAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('PortadoresBundle:TipoCombustible')->buscarTipoCombustibleCombo();

        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity['id'],
                'nombre' => $entity['nombre'],
                'precio' => $entity['precio'],
                'codigo' => $entity['codigo']
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
        $precio = (float)trim($request->get('precio'));
        $precioTiroDirecto = (float)trim($request->get('precio_tiro_directo'));
        $maximo_tarjeta_litro = trim($request->get('maximo_tarjeta_litro'));
        $maximo_tarjeta_dinero = (float)trim($request->get('maximo_tarjeta_dinero'));
        $codigo = trim($request->get('codigo'));
        $portadorid = trim($request->get('portador_id'));
//        $monedaid = trim($request->get('monedaid'));
        $fila = trim($request->get('filaid'));


        $repetido = $em->getRepository('PortadoresBundle:TipoCombustible')->buscarTipoCombustibleRepetido($nombre);
        if ($repetido > 0) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe un tipo de combustible con el mismo nombre.'));
        }

        $entity = new TipoCombustible();
        $entity->setNombre($nombre);
        $entity->setPrecio($precio);
        $entity->setPrecioTD($precioTiroDirecto);
        $entity->setMaximoTarjetaDinero($maximo_tarjeta_dinero);
        $entity->setMaximoTarjetaLitro($maximo_tarjeta_litro);
        $entity->setVisible(true);
        $entity->setCodigo($codigo);
        $entity->setPortadorid($em->getRepository('PortadoresBundle:Portador')->find($portadorid));
//        $entity->setMoneda($em->getRepository('PortadoresBundle:Moneda')->find($monedaid));
        $entity->setFila($fila);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Tipo de combustible  adicionado con éxito.'));
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
        $precio = trim($request->get('precio'));
        $precioTiroDirecto = trim($request->get('precio_tiro_directo'));
        $maximo_tarjeta_litro = trim($request->get('maximo_tarjeta_litro'));
        $maximo_tarjeta_dinero = (float)trim($request->get('maximo_tarjeta_dinero'));
        $codigo = trim($request->get('codigo'));
        $portadorid = trim($request->get('portador_id'));
        $fila = trim($request->get('filaid'));

        $repetido = $em->getRepository('PortadoresBundle:TipoCombustible')->buscarTipoCombustibleRepetido($nombre, $id);
        if ($repetido > 0) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe un tipo de combustible con el mismo nombre.'));
        }

        $entity = $em->getRepository('PortadoresBundle:TipoCombustible')->find($id);
        $entity->setNombre($nombre);
        $entity->setPrecio($precio);
        $entity->setPrecioTD($precioTiroDirecto);
        $entity->setMaximoTarjetaDinero($maximo_tarjeta_dinero);
        $entity->setMaximoTarjetaLitro($maximo_tarjeta_litro);
        $entity->setCodigo($codigo);
        $entity->setPortadorid($em->getRepository('PortadoresBundle:Portador')->find($portadorid));
        $entity->setFila($fila);
        try {
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Tipo de combustible modificado con éxito.'));
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
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');

        $tipo_combustible = $em->getRepository('PortadoresBundle:TipoCombustible')->find($id);
        $entity_asig = $em->getRepository('PortadoresBundle:TrabajoAsignacionCombustible')->findOneBy(array('tipoCombustible' => $tipo_combustible));
        if ($entity_asig) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Este combustible tiene al menos una asignación.'));
        }

        $entity = $em->getRepository('PortadoresBundle:TipoCombustible')->find($id);
        $entity->setVisible(false);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Tipo de combustible eliminado con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }

    }
}