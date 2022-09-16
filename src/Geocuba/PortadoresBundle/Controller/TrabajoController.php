<?php
/**
 * Created by PhpStorm.
 * User: yosley
 * Date: 05/10/2015
 * Time: 14:12
 */


namespace Geocuba\PortadoresBundle\Controller;
use Doctrine\Common\Util\Debug;
use Geocuba\AdminBundle\Util\Util;
use Geocuba\AdminBundle\Util\FechaUtil;
use Geocuba\PortadoresBundle\Entity\Trabajo;
use Geocuba\PortadoresBundle\Entity\TrabajoAsignacionCombustible;
use Doctrine\Common\CommonException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;


class TrabajoController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $_nombre = trim($request->get('nombre'));
        $_fechaStr = trim($request->get('fecha'));
        $nunidadid = $request->get('unidadid');
        if (isset($_fechaStr) && $_fechaStr != '') {
            $_fecha = date_create_from_format('d/m/Y', $_fechaStr);
        } else {
            $session = $request->getSession();
            $anno = $session->get('current_year');
            $mes = $session->get('current_month');
            $_fecha = $anno.'-'.$mes.'-1';
        }
        $start = $request->get('start');
        $limit = $request->get('limit');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $entities = $em->getRepository('PortadoresBundle:Trabajo')->buscarTrabajo($_nombre, $_unidades, $_fecha, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:Trabajo')->buscarTrabajo($_nombre, $_unidades, $_fecha, $start, $limit, true);

        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'codigo' => $entity->getCodigo(),
                'nombre' => $entity->getNombre(),
                'codigo_nombre' => $entity->getCodigo().'-'.$entity->getNombre(),
                'nunidadid' => $entity->getNcentrocostoid()->getNunidadid()->getNombre(),
                'ncentrocosto' => $entity->getNcentrocostoid()->getId(),
                'centro_costo' => $entity->getNcentrocostoid()->getNombre(),
                'destinoid' => $entity->getNdestinoid()->getId(),
                'destino' => $entity->getNdestinoid()->getNombre(),
                'fecha_ini' => date_format($entity->getFechaInicio(), 'd/m/Y'),
                'fecha_fin' => date_format($entity->getFechaFin(), 'd/m/Y')
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    public function loadTipoCombustibleAction()
    {
        $entities = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:TipoCombustible')->findByVisible(true);
        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre(),
                'codigo' => $entity->getCodigo(),
                'precio' => $entity->getPrecio(),
                'maximo_tarjeta' => $entity->getMaximoTarjeta(),
                'nro' => $entity->getNro()
            );
        }
        return new JsonResponse(array('rows' => $_data, 'total' => count($_data)));
    }

    public function loadTrabajoMonedaAction()
    {
        $entities = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:Moneda')->findByVisible(true);
        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre(),
                'unica' => $entity->getUnica()
            );
        }
        return new JsonResponse(array('rows' => $_data, 'total' => count($_data)));
    }

    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $codigo = trim($request->get('codigo'));
        $nombre = trim($request->get('nombre'));
        $centroCosto = trim($request->get('ncentrocosto'));
        $destino = trim($request->get('destinoid'));
        $fecha_ini = date_create_from_format('d/m/Y', $request->get('fecha_ini'));
        $fecha_fin = date_create_from_format('d/m/Y', $request->get('fecha_fin'));

//        $repetido = $em->getRepository('PortadoresBundle:Trabajo')->buscarTrabajoRepetido($nombre);
//        if ($repetido > 0)
//            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe un trabajo con el mismo nombre.'));
        $repetido = $em->getRepository('PortadoresBundle:Trabajo')->buscarTrabajoRepetido('', $codigo);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe un trabajo con el mismo código.'));
        $entity = new Trabajo();
        $entity->setFechaInicio($fecha_ini);
        $entity->setFechaFin($fecha_fin);
        $entity->setCodigo($codigo);
        $entity->setNombre($nombre);
        $entity->setNcentrocostoid($em->getRepository('PortadoresBundle:CentroCosto')->find($centroCosto));
        $entity->setNdestinoid($em->getRepository('PortadoresBundle:Destino')->find($destino));
        $entity->setVisible(true);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Trabajo adicionado con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function modAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $codigo = trim($request->get('codigo'));
        $nombre = trim($request->get('nombre'));
        $centroCosto = trim($request->get('ncentrocosto'));
        $destino = trim($request->get('destinoid'));
        $fecha_ini = date_create_from_format('d/m/Y', $request->get('fecha_ini'));
        $fecha_fin = date_create_from_format('d/m/Y', $request->get('fecha_fin'));

//        $repetido = $em->getRepository('PortadoresBundle:Trabajo')->buscarTrabajoRepetido($nombre, '', $id);
//        if ($repetido > 0)
//            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe un trabajo con el mismo nombre.'));

        $repetido = $em->getRepository('PortadoresBundle:Trabajo')->buscarTrabajoRepetido('', $codigo, $id);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe un trabajo con el mismo código.'));

        $entity = $em->getRepository('PortadoresBundle:Trabajo')->find($id);
        $entity->setFechaInicio($fecha_ini);
        $entity->setFechaFin($fecha_fin);
        $entity->setCodigo($codigo);
        $entity->setNombre($nombre);
        $entity->setNcentrocostoid($em->getRepository('PortadoresBundle:CentroCosto')->find($centroCosto));
        $entity->setNdestinoid($em->getRepository('PortadoresBundle:Destino')->find($destino));
        try {
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Trabajo  modificado con éxito.'));
            return $response;
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function delAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = $request->get('id');
        $entity = $em->getRepository('PortadoresBundle:Trabajo')->find($id);
        $entity->setVisible(false);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Trabajo  eliminado con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function loadAsignacionAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');

        $entities = $em->getRepository('PortadoresBundle:TrabajoAsignacionCombustible')->buscarAsignacion($id,'','');
        $total = $em->getRepository('PortadoresBundle:TrabajoAsignacionCombustible')->buscarAsignacion($id,'','', true);

        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'trabajoid' => $entity->getTrabajoid()->getId(),
                'tipo_combustibleid' => $entity->getTipoCombustible()->getId(),
                'tipo_combustible' => $entity->getTipoCombustible()->getNombre(),
                'monedaid' => $entity->getMoneda()->getId(),
                'moneda' => $entity->getMoneda()->getNombre(),
                'cantidad' => $entity->getCantidad()
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    public function addAsignacionAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $trabajoid = $request->get('trabajoid');
        $tipo_combustibleid = $request->get('tipo_combustibleid');
        $monedaid = $request->get('monedaid');
        $cantidad = $request->get('cantidad');

        $trabajo = $em->getRepository('PortadoresBundle:Trabajo')->find($trabajoid);
        $tipo_combustible = $em->getRepository('PortadoresBundle:TipoCombustible')->find($tipo_combustibleid);
        $moneda = $em->getRepository('PortadoresBundle:Moneda')->find($monedaid);

        $entities_total = $em->getRepository('PortadoresBundle:TrabajoAsignacionCombustible')->findBy(array(
            'trabajoid' => $trabajoid,
            'tipoCombustible' => $tipo_combustibleid,
            'moneda' => $monedaid
        ));
        if ($entities_total)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El Tipo de combustible y moneda ya existe.'));

        $entity = new TrabajoAsignacionCombustible();
        $entity->setTrabajoid($trabajo);
        $entity->setTipoCombustible($tipo_combustible);
        $entity->setMoneda($moneda);
        $entity->setCantidad($cantidad);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Asignación adicionada con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function modAsignacionAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $trabajoid = $request->get('trabajoid');
        $tipo_combustibleid = $request->get('tipo_combustibleid');
        $monedaid = $request->get('monedaid');
        $cantidad = $request->get('cantidad');

        $trabajo = $em->getRepository('PortadoresBundle:Trabajo')->find($trabajoid);
        $tipo_combustible = $em->getRepository('PortadoresBundle:TipoCombustible')->find($tipo_combustibleid);
        $moneda = $em->getRepository('PortadoresBundle:Moneda')->find($monedaid);

        $entities_total = $em->getRepository('PortadoresBundle:TrabajoAsignacionCombustible')->findBy(array(
            'trabajoid' => $trabajoid,
            'tipoCombustible' => $tipo_combustibleid,
            'moneda' => $monedaid
        ));
        if ($entities_total)
            if ($entities_total[0]->getId() != $id)
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El Tipo de combustible
                 y moneda ya existe.'));

        $entity = $em->getRepository('PortadoresBundle:TrabajoAsignacionCombustible')->find($id);
        $entity->setTrabajoid($trabajo);
        $entity->setTipoCombustible($tipo_combustible);
        $entity->setMoneda($moneda);
        $entity->setCantidad($cantidad);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Asignación modificada con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function delAsignacionAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = $request->get('id');
        $entity = $em->getRepository('PortadoresBundle:TrabajoAsignacionCombustible')->find($id);

        try {
            $em->remove($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Asignación eliminada con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }
}