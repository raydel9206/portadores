<?php
/**
 * Created by PhpStorm.
 * User: yosley
 * Date: 06/10/2015
 * Time: 16:36
 */


namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\AdminBundle\Util\Util;
use Geocuba\PortadoresBundle\Entity\DemandaCombustible;
use Geocuba\PortadoresBundle\Entity\Persona;
use Doctrine\Common\CommonException;
use Geocuba\PortadoresBundle\Entity\SolicitudCompra;
use Geocuba\PortadoresBundle\Entity\SolicitudCompraDesglose;
use Geocuba\PortadoresBundle\Util\Datos;
use Geocuba\PortadoresBundle\Util\MonedaEnum;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;

class SolicitudCompraController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $nunidadid = $request->get('unidadid');

        $em = $this->getDoctrine()->getManager();

        $start = $request->get('start');
        $limit = $request->get('limit');

        $solicitudes = $em->getRepository('PortadoresBundle:SolicitudCompra')->findBy(array('nunidadid' => $nunidadid, 'visible' => true), array('fecha' => 'DESC'), $limit, $start);
        $total = count($em->getRepository('PortadoresBundle:SolicitudCompra')->findBy(array('nunidadid' => $nunidadid, 'visible' => true), array('fecha' => 'DESC')));

        $_data = array();
        foreach ($solicitudes as $solicitud) {
            $_data[] = array(
                'id' => $solicitud->getId(),
                'fecha' => date_format($solicitud->getFecha(), 'd/m/Y'),
                'monto_cup' => $solicitud->getMontoCup(),
                'monto_cuc' => $solicitud->getMontoCuc(),
                'aprobado' => $solicitud->getAprobado()
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $nunidadid = $request->get('nunidadid');
        $fecha = $request->get('fecha');

        $fecha_format = date_create_from_format('d/m/Y', $fecha);


        $entity = new SolicitudCompra();
        $entity->setFecha($fecha_format);
        $entity->setMontoCup(0);
        $entity->setMontoCuc(0);
        $entity->setAprobado(false);
        $entity->setNunidadid($em->getRepository('PortadoresBundle:Unidad')->find($nunidadid));
        $entity->setVisible(true);

        $em->persist($entity);

        try {
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Solcitud de compra de combustible adicionada con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }

    }

    public function modAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $fecha = $request->get('fecha');
        $id = $request->get('id');

        $fecha_format = date_create_from_format('d/m/Y', $fecha);

        $entity = $em->getRepository('PortadoresBundle:SolicitudCompra')->find($id);

        $entity->setFecha($fecha_format);

        $em->persist($entity);

        try {
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Solcitud de compra de combustible adicionada con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }

    }

    public function delAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = $request->get('id');
        $entity = $em->getRepository('PortadoresBundle:SolicitudCompra')->find($id);
        $entity->setVisible(false);

        $desgloses = $em->getRepository('PortadoresBundle:SolicitudCompraDesglose')->findBy(array('solicitud' => $entity));

        foreach ($desgloses as $desglose) {
            $em->remove($desglose);
        }

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Solicitud de Compra eliminada con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function aprobarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = $request->get('id');
        $entity = $em->getRepository('PortadoresBundle:SolicitudCompra')->find($id);
        $entity->setAprobado(true);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Solicitud de Compra aprobada con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function desaprobarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = $request->get('id');
        $entity = $em->getRepository('PortadoresBundle:SolicitudCompra')->find($id);
        $entity->setAprobado(false);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Solicitud de Compra desaprobada con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function loadDesgloseAction(Request $request)
    {
        $solicitud_id = $request->get('solicitud_id');
        $nunidadid = $request->get('unidadid');

        $_data = array();
        $em = $this->getDoctrine()->getManager();

        //Cargar Todas
        $entities = $em->getRepository('PortadoresBundle:TipoCombustible')->findBy(array('visible' => true));
        $monedas = $em->getRepository('PortadoresBundle:Moneda')->findBy(array('visible' => true));

        foreach ($entities as $entity) {
            $desglose1 = $em->getRepository('PortadoresBundle:SolicitudCompraDesglose')->findOneBy(array('solicitud' => $solicitud_id, 'tipoCombustible' => $entity, 'moneda' => $monedas[0]));
            $desglose2 = $em->getRepository('PortadoresBundle:SolicitudCompraDesglose')->findOneBy(array('solicitud' => $solicitud_id, 'tipoCombustible' => $entity, 'moneda' => $monedas[1]));

            $litros1 = $desglose1 ? $desglose1->getCantLitros() : 0;
            $litros2 = $desglose2 ? $desglose2->getCantLitros() : 0;
            $monto1 = $desglose1 ? $desglose1->getMonto() : 0;
            $monto2 = $desglose2 ? $desglose2->getMonto() : 0;


            if ($desglose1 || $desglose2) {
                $_data[] = array(
                    'solicitud_id' => $desglose1->getSolicitud()->getId(),
                    'tipo_combustible_id' => $entity->getId(),
                    'tipo_combustible' => $entity->getCodigo(),
                    'tipo_combustible_nombre' => $entity->getNombre(),
                    'tipo_combustible_precio' => $entity->getPrecio(),
                    'monto_cup' => $monedas[0]->getId() == MonedaEnum::cup ? $monto1 : $monto2,
                    'litros_cup' => $monedas[0]->getId() == MonedaEnum::cup ? $litros1 : $litros2,
                    'monto_cuc' => $monedas[0]->getId() == MonedaEnum::cup ? $monto2 : $monto1,
                    'litros_cuc' => $monedas[0]->getId() == MonedaEnum::cup ? $litros2 : $litros1,
                    'propuesta' => $desglose1->getCombDistribuido() - $desglose1->getSaldoFincimex() - $desglose1->getSaldoCaja(),
                    'comb_distribuido' => $desglose1->getCombDistribuido(),
                    'disponible_fincimex' => $desglose1->getDisponibleFincimex(),
                    'saldo_fincimex' => $desglose1->getSaldoFincimex(),
                    'saldo_caja' => $desglose1->getSaldoCaja(),
                );
            } else {
                //Obtener Ultimo Combustible Distribuido
                $comb_distribuido = $this->cantDistribuida($entity, $nunidadid);

                //Obtener Disponible Fincimex
                $disponible_fincimex = Datos::getPlanDisponibleFincimex($em, $nunidadid, $entity);

                //Obtener Saldo Fincimex
                $saldo_fincimex = Datos::getSaldoDisponibleFincimex($em, $nunidadid, $entity);

                //Obtener Saldo caja
                $saldo_caja = Datos::getSaldoCaja($em, $nunidadid, $entity);

                $_data[] = array(
                    'solicitud_id' => '',
                    'tipo_combustible_id' => $entity->getId(),
                    'tipo_combustible' => $entity->getCodigo(),
                    'tipo_combustible_nombre' => $entity->getNombre(),
                    'tipo_combustible_precio' => $entity->getPrecio(),
                    'monto_cup' => 0,
                    'litros_cup' => 0,
                    'monto_cuc' => 0,
                    'litros_cuc' => 0,
                    'propuesta' => $comb_distribuido - $saldo_fincimex - $saldo_caja,
                    'comb_distribuido' => $comb_distribuido,
                    'disponible_fincimex' => $disponible_fincimex,
                    'saldo_fincimex' => $saldo_fincimex,
                    'saldo_caja' => $saldo_caja,
                    'unidad_id' => $nunidadid,
                );
            }
        }

        return new JsonResponse(array('rows' => $_data));
    }

    public function loadDesgloseChequeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $nunidadid = $request->get('unidadid');
        $moneda_id = $request->get('moneda_id');

        $solicitud = $em->getRepository('PortadoresBundle:SolicitudCompra')->findOneBy(array('unidad'=>$nunidadid,'visible'=>true));

        $desgloses = $em->getRepository('PortadoresBundle:SolicitudCompraDesglose')->findBy(array('solicitud'=>$solicitud,'moneda'=>$moneda_id,'visible'=>true));

        $_data = array();

        foreach ($desgloses as $desglose){
            $_data[] = array(
                'id' => $desglose->getId(),
                'cheque_id' => '',
                'monto' => $desglose->getMonto(),
                'litros' => $desglose->getLitros(),
                'tipo_combustible' => $desglose->getTipoCombustible()->getCodigo(),
                'tipo_combustible_id' => $desglose->getTipoCombustible()->getId(),
            );
        }

        return new JsonResponse(array('rows' => $_data));
    }

    public function guardarDesgloseAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $solicitud_id = $request->get('solicitud_id');
        $datos_desgloses = json_decode($request->get('desgloses'));

        $solicitud = $em->getRepository('PortadoresBundle:SolicitudCompra')->find($solicitud_id);
        $monedas = $em->getRepository('PortadoresBundle:Moneda')->findBy(array('visible' => true));

        $suma_monto_cup = 0;
        $suma_monto_cuc = 0;

        for ($i = 0, $iMax = \count($datos_desgloses); $i < $iMax; $i++) {
            $tipo_combustible = $em->getRepository('PortadoresBundle:TipoCombustible')->find($datos_desgloses[$i]->tipo_combustible_id);

            $suma_monto_cup += $datos_desgloses[$i]->monto_cup;
            $suma_monto_cuc += $datos_desgloses[$i]->monto_cuc;

            foreach ($monedas as $moneda) {
                $litros = $moneda->getId() == MonedaEnum::cup ? $datos_desgloses[$i]->litros_cup : $datos_desgloses[$i]->litros_cuc;
                $monto = $moneda->getId() == MonedaEnum::cup  ? $datos_desgloses[$i]->monto_cup : $datos_desgloses[$i]->monto_cuc;

                $desglose = $em->getRepository('PortadoresBundle:SolicitudCompraDesglose')->findOneBy(array('tipoCombustible' => $tipo_combustible, 'moneda' => $moneda, 'solicitud' => $solicitud));

                if (!$desglose && $litros != 0) {
                    $desglose = new SolicitudCompraDesglose();
                    $desglose->setSolicitud($solicitud);
                    $desglose->setMoneda($moneda);
                    $desglose->setTipoCombustible($tipo_combustible);
                    $desglose->setCombDistribuido($datos_desgloses[$i]->comb_distribuido);
                    $desglose->setDisponibleFincimex($datos_desgloses[$i]->disponible_fincimex);
                    $desglose->setSaldoFincimex($datos_desgloses[$i]->saldo_fincimex);
                    $desglose->getSaldoCaja($datos_desgloses[$i]->saldo_caja);
                    $desglose->setCantLitros($litros);
                    $desglose->setMonto($monto);
                    $em->persist($desglose);
                } elseif ($litros != 0) {
                    $desglose->setCantLitros($litros);
                    $desglose->setMonto($monto);
                    $em->persist($desglose);
                } elseif ($desglose && $litros == 0) {
                    $em->remove($desglose);
                }
            }
        }

        $solicitud->setMontoCup($suma_monto_cup);
        $solicitud->setMontoCuc($suma_monto_cuc);

        $em->persist($solicitud);

        try {
            $em->flush();
        } catch (Exception $exceptione) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
        return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Desglose de Solicitud de Compra realizado con éxito.'));
    }

    public function delDesgloseAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $solicitud_id = $request->get('solicitud_id');

        $solicitud = $em->getRepository('PortadoresBundle:SolicitudCompra')->find($solicitud_id);

        $desgloses = $em->getRepository('PortadoresBundle:SolicitudCompraDesglose')->findBy(array('solicitud'=>$solicitud));

        foreach ($desgloses as $desglose){
            $em->remove($desglose);
        }

        $solicitud->setMontoCup(0);
        $solicitud->setMontoCuc(0);

        $em->persist($solicitud);

        try {
            $em->flush();
        } catch (Exception $exceptione) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
        return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Desglose de Solicitud de Compra reiniciado con éxito.'));
    }

    private function cantDistribuida($tipoCombustible, $unidad)
    {
        $em = $this->getDoctrine()->getManager();

        $distribucion = $em->getRepository('PortadoresBundle:DistribucionCombustible')->findOneBy(array('nunidadid' => $unidad, 'tipoCombustible' => $tipoCombustible, 'aprobada' => true), array('fecha' => 'DESC'));

        return $distribucion ? $distribucion->getCantidad() : 0;
    }

    private function unidadesToString($_unidades)
    {
        $_string_unidades = "'" . $_unidades[0] . "'";
        for ($i = 1; $i < count($_unidades); $i++) {
            $_string_unidades .= ",'" . $_unidades[$i] . "'";
        }
        return $_string_unidades;
    }


}