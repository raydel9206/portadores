<?php
/**
 * Created by PhpStorm.
 * User: orlando
 * Date: 06/10/2015
 * Time: 9:16
 */


namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\PortadoresBundle\Entity\ChequeDesglose;
use Geocuba\PortadoresBundle\Entity\CuentaRecarga;
use Geocuba\PortadoresBundle\Entity\ChequeFincimex;
use Geocuba\PortadoresBundle\Entity\SolicitudCompraDesglose;
use Geocuba\PortadoresBundle\PortadoresBundle;
use Doctrine\Common\CommonException;
use Geocuba\PortadoresBundle\Util\MonedaEnum;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\HttpFoundation\Response;
use Geocuba\PortadoresBundle\Util\Utiles;


class ChequeFincimexController extends Controller
{
    use ViewActionTrait;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $_no_cheque = trim($request->get('no_cheque'));
        $nunidadid = trim($request->get('unidad'));

        $start = $request->get('start');
        $limit = $request->get('limit');

        $_unidades[0] = $nunidadid;

        $entities = $em->getRepository('PortadoresBundle:ChequeFincimex')->buscarChequeFincimex($_no_cheque, $_unidades, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:ChequeFincimex')->buscarChequeFincimex($_no_cheque, $_unidades, $start, $limit, true);

        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'no_cheque' => $entity->getNoCheque(),
                'monto_total' => $entity->getMontoTotal(),
                'fecha_registro' => $entity->getFechaRegistro()->format('d/m/Y'),
                'fecha_deposito' => null === $entity->getFechaDeposito() ? '' : $entity->getFechaDeposito()->format('d/m/Y'),
                'unidadid' => $entity->getNunidadid()->getId(),
                'nombreunidadid' => $entity->getNunidadid()->getNombre(),
                'moneda_id' => $entity->getMoneda()->getId(),
                'moneda_nombre' => $entity->getMoneda()->getNombre(),
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loadLastSolicitudCompraAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $monedaid = trim($request->get('monedaid'));
        $nunidadid = trim($request->get('nunidadid'));

        $solicitudCompra = $em->getRepository('PortadoresBundle:SolicitudCompra')->findBy(['nunidadid' => $nunidadid, 'visible' => true], ['fecha' => 'DESC'], 1, 0);
        if (!$solicitudCompra) return new JsonResponse(['success' => false, 'message' => 'No hay solicitudes de compra hechas']);

        $desglose = $em->getRepository('PortadoresBundle:SolicitudCompraDesglose')->findBy(['solicitud' => $solicitudCompra[0]->getId(), 'moneda' => $monedaid]);

        $_data = array_map(function ($item) {
            /** @var SolicitudCompraDesglose $item */
            return [
                'id' => $item->getId(),
                'tipo_combustible' => $item->getTipoCombustible()->getCodigo(),
                'tipo_combustible_id' => $item->getTipoCombustible()->getId(),
                'monto' => $item->getMonto(),
                'litros' => $item->getCantLitros()
            ];
        }, $desglose);

        return new JsonResponse(['success' => true, 'rows' => $_data]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $no_cheque = trim($request->get('no_cheque'));
        $moneda_id = trim($request->get('moneda_id'));
        $unidad = $request->get('unidadid');
        $fecha_registro = $request->get('fecha_registro');
        $fecha_registro_format = date_create_from_format('d/m/Y', $fecha_registro);
        $repetido = $em->getRepository('PortadoresBundle:ChequeFincimex')->buscarChequeFincimexRepetido($no_cheque, $unidad);
        if ($no_cheque != '########') {
            if ($repetido > 0)
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe un cheque con el mismo número en la unidad seleccionada.'));
        }

        $store = json_decode($request->get('store'));

        $entity = new ChequeFincimex();
        $entity->setNoCheque($no_cheque);
        $entity->setMontoTotal(0);
        $entity->setFechaRegistro($fecha_registro_format);
        $entity->setNunidadid($em->getRepository('PortadoresBundle:Unidad')->find($unidad));
        $entity->setMoneda($em->getRepository('PortadoresBundle:Moneda')->find($moneda_id));
        $entity->setVisible(true);

        $em->persist($entity);

        $total = 0;

        for ($i = 0; $i < sizeof($store); $i++) {
            $total += $store[$i]->monto;

            $tipoCombustible = $em->getRepository('PortadoresBundle:TipoCombustible')->find($store[$i]->tipo_combustible_id);

            $last_asignacion = $em->getRepository('PortadoresBundle:Asignacion')->findOneBy(array('unidad'=>$unidad,'tipoCombustible'=>$tipoCombustible,'visible'=>true),array('fecha'=>'DESC'));

            if($last_asignacion->getDisponible()-$store[$i]->monto<0)
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'La cantidad a comprar de '.$tipoCombustible->getNombre().' excede el plan disponible en Fincimex'));

            if($tipoCombustible){
                $desglose = new ChequeDesglose();
                $desglose->setCheque($entity);
                $desglose->setTipoCombustible($tipoCombustible);
                $desglose->setMonto($store[$i]->monto);
                $desglose->setLitros($store[$i]->litros);

                $em->persist($desglose);
            }
            else{
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
            }

        }

        $entity->setMontoTotal($total);
        $em->persist($entity);


        $html = $this->pintarcomprobanteAction($entity);

        try {
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Cheque adicionado con éxito.', 'html' => $html));
        } catch (\Exception $ex) {
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
        $no_cheque = trim($request->get('no_cheque'));
        $moneda_id = trim($request->get('moneda_id'));
        $monto_total = trim($request->get('monto_total'));
        $fecha_registro = $request->get('fecha_registro');
        $fecha_registro_format = date_create_from_format('d/m/Y', $fecha_registro);
        $unidad = $request->get('unidadid');

        if ($no_cheque != '########') {
            $repetido = $em->getRepository('PortadoresBundle:ChequeFincimex')->buscarChequeFincimexRepetido($no_cheque, $unidad, $id);
            if ($repetido > 0) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe un cheque con el mismo número en la unidad seleccionada.'));
            }
        }

        $store = json_decode($request->get('store'));

        $entity = $em->getRepository('PortadoresBundle:ChequeFincimex')->find($id);
        $entity->setNoCheque($no_cheque);
        $entity->setMontoTotal($monto_total);
        $entity->setFechaRegistro($fecha_registro_format);
        $entity->setMoneda($em->getRepository('PortadoresBundle:Moneda')->find($moneda_id));

        $em->persist($entity);

        $desgloses = $em->getRepository('PortadoresBundle:ChequeDesglose')->findBy(array('cheque'=>$entity));

        foreach ($desgloses as $desglose){
            $em->remove($desglose);
        }

        try {
            $em->flush();
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }

        $total = 0;

        for ($i = 0; $i < sizeof($store); $i++) {
            $total += $store[$i]->monto;
            $tipoCombustible = $em->getRepository('PortadoresBundle:TipoCombustible')->find($store[$i]->tipo_combustible_id);
            if($tipoCombustible){
                $desglose = new ChequeDesglose();
                $desglose->setCheque($entity);
                $desglose->setTipoCombustible($tipoCombustible);
                $desglose->setMonto($store[$i]->monto);
                $desglose->setLitros($store[$i]->litros);

                $em->persist($desglose);
            }
            else{
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
            }

        }

        $entity->setMontoTotal($total);
        $em->persist($entity);

        try {
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Cheque modificado con éxito.'));
            return $response;
        } catch (\Exception $ex) {
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

        $entity = $em->getRepository('PortadoresBundle:ChequeFincimex')->find($id);
        $entity->setVisible(false);

        $desgloses = $em->getRepository('PortadoresBundle:ChequeDesglose')->findBy(array('cheque'=>$entity));

        foreach ($desgloses as $desglose){
            $em->remove($desglose);
        }

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Cheque eliminado con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function comprobanteAction(Request $request)
    {
        $cheque_id = $request->get('cheque_id');
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('PortadoresBundle:ChequeFincimex')->find($cheque_id);

        $html = $this->pintarcomprobanteAction($entity);

        return new JsonResponse(array('html' => $html));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function depositarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $cheque_id = $request->get('cheque_id');
        $fecha_deposito = $request->get('fecha_deposito');
        $fecha_deposito_format = date_create_from_format('d/m/Y', $fecha_deposito);

        $cheque = $em->getRepository('PortadoresBundle:ChequeFincimex')->find($cheque_id);

        $cheque->setFechaDeposito($fecha_deposito_format);

        $em->persist($cheque);

        $desgloses = $em->getRepository('PortadoresBundle:ChequeDesglose')->findBy(array('cheque'=>$cheque));

        foreach ($desgloses as $desglose){
            $entity_cuenta = $em->getRepository('PortadoresBundle:CuentaRecarga')->findOneBy(array('unidad' => $cheque->getNunidadid(), 'moneda' => $cheque->getMoneda(), 'tipoCombustible'=>$desglose->getTipoCombustible()));
            if($entity_cuenta){
                $entity_cuenta->setMonto($entity_cuenta->getMonto()+$desglose->getMonto());
            }else{
                $entity_cuenta = new CuentaRecarga();
                $entity_cuenta->setUnidad($cheque->getNunidadid());
                $entity_cuenta->setTipoCombustible($desglose->getTipoCombustible());
                $entity_cuenta->setMoneda($cheque->getMoneda());
                $entity_cuenta->setMonto($desglose->getMonto());
            }

            $em->persist($entity_cuenta);

            $last_asignacion = $em->getRepository('PortadoresBundle:Asignacion')->findOneBy(array('tipoCombustible' => $desglose->getTipoCombustible(), 'unidad' => $cheque->getNunidadid(), 'visible' => true), array('fecha' => 'DESC'));
            if($last_asignacion){
                $last_asignacion->setDisponible($last_asignacion->getDisponible() - $desglose->getLitros());
                $em->persist($last_asignacion);
            }

        }


        try {
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Cheque depositado con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function loadChequeDesgloseAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $chequeid = trim($request->get('chequeid'));

        $entities = $em->getRepository('PortadoresBundle:ChequeDesglose')->findBy(array('cheque'=>$chequeid));

        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'cheque_id' => $entity->getCheque()->getId(),
                'monto' => $entity->getMonto(),
                'litros' => $entity->getLitros(),
                'tipo_combustible' => $entity->getTipoCombustible()->getCodigo(),
                'tipo_combustible_id' => $entity->getTipoCombustible()->getId(),
            );
        }

        return new JsonResponse(array('rows' => $_data));
    }

     /**
     * @param Request $request
     * @return JsonResponse
     */
    public function load_saldoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $unidad_id = $request->get('unidad');
        $unidad = $em->getRepository('PortadoresBundle:Unidad')->find($unidad_id);

        $tipos_combustible = $em->getRepository('PortadoresBundle:TipoCombustible')->findBy(array('visible' => true));
        $monedas = $em->getRepository('PortadoresBundle:Moneda')->findBy(array('visible' => true));

        $_data = array();
        foreach ($tipos_combustible as $tipo_combustible) {
            $cuenta_1 = $em->getRepository('PortadoresBundle:CuentaRecarga')->findOneBy(array('unidad' => $unidad, 'moneda' => $monedas[0], 'tipoCombustible' => $tipo_combustible));
            $cuenta_2 = $em->getRepository('PortadoresBundle:CuentaRecarga')->findOneBy(array('unidad' => $unidad, 'moneda' => $monedas[1], 'tipoCombustible' => $tipo_combustible));

            $saldo_1 = $cuenta_1 ? $cuenta_1->getMonto() : 0;
            $saldo_2 = $cuenta_2 ? $cuenta_2->getMonto() : 0;

            $_data[] = array(
                'tipo_combustible' => $tipo_combustible->getNombre(),
                'saldo_1' => $monedas[0]->getId() == MonedaEnum::cup ? $saldo_1 : $saldo_2,
                'saldo_2' => $monedas[1]->getId() == MonedaEnum::cuc ? $saldo_2 : $saldo_1,
                'total' => $saldo_1 + $saldo_2
            );
        }

        return new JsonResponse(array('rows' => $_data));


    }

    /**
     * @param Request $request
     * @return Response
     */
    public function printAction(Request $request)
    {
        $data = json_decode($request->get('store'));

        $_html = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
        <html xmlns='http://www.w3.org/1999/xhtml'>
         <head>
          <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
           <title>Cheques</title>
            <style>
            table {
                border:0 solid;
                border-radius:0;
                font-family: 'Arial', serif;
                font-size: 14px;
                border-collapse: collapse;
            }
            td{
                height: 10px;
                padding: 2px;
            }
        </style>
         </head>
          <body>
           <table cellspacing='0' cellpadding='5' border='1' width='100%'>
             <tr>
             <td style='text-align: center;border: none; padding: 0' width='100%'><strong><i> " . $data[0]->nombreunidadid . "</i></strong></td>
              </tr>
             <tr>
              <td style='text-align: left;border: none; padding: 0' width='100%'><img src='../../image/logo_almest.png' height='44px' style='margin-left: 40px'><strong><i style='margin-center: 60px'>CHEQUES DE FINCIMEX</i></strong></td>
              </tr>
              </table>
              ";


        $_html .= "<table cellspacing='0' cellpadding='5' border='1' width='100%' >
              <tr>
              <td width='10%' align='center' ><strong>NO.CHEQUE</strong></td>
              <td width='10%' align='center' ><strong>Moneda</strong></td>
              <td width='10%' align='center' ><strong>Monto</strong></td>
              <td width='10%' align='center' ><strong>FECHA REGISTRO</strong></td>
              <td width='10%' align='center' ><strong>FECHA DEPOSITADO</strong></td>
             </tr>";


        for ($i = 0, $iMax = \count($data); $i < $iMax; $i++) {
            $_html .= " <tr>

              <td width='10%' align='center'>" . $data[$i]->no_cheque . "</td>
              <td width='10%' align='center'>" . $data[$i]->moneda_nombre . "</td>
              <td width='10%' align='center'>" . $data[$i]->monto_total . "</td>
              <td width='10%' align='center' >" . $data[$i]->fecha_registro . "</td>
              <td width='10%' align='center' >" . $data[$i]->fecha_deposito . '</td>

             </tr>';
        }
        $_html .= '
             </table>
          </body>
             </html>';
        return new Response(json_encode(array('success' => true, 'html' => $_html)));
    }

    public function deshacerAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $cheque = $request->get('cheque_id');

        $entity = $em->getRepository('PortadoresBundle:ChequeFincimex')->find($cheque);

        $distribuciones = $em->getRepository('PortadoresBundle:DistribucionCombustible')->findBy(array('cheque' => $entity, 'visible' => true));

        foreach ($distribuciones as $distribucion) {
            $distribucion->setCheque(null);
            $em->persist($distribucion);
        }
        try {
            $em->flush();
            return new JsonResponse(array('success' => true));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function restaurarDistribucionesAction(Request $request)
    {
        $cheque_id = trim($request->get('cheque_id'));

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('PortadoresBundle:ChequeFincimex')->find($cheque_id);

        $distribuciones = $request->get('distribuciones');
        foreach ($distribuciones as $distribucion) {
            $distribucion = $em->getRepository('PortadoresBundle:DistribucionCombustible')->find($distribucion);
            $distribucion->setCheque($entity);
            $em->persist($distribucion);
        }
        try {
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Cheque restaurado con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }

    }

    private function pintarComprobanteAction($entity)
    {
        $moneda = $entity->getMoneda()->getId();
        $pago_anticipado = $moneda == MonedaEnum::cup ? '14602039005' : '14702039005';
        $efectivo_banco = $moneda == MonedaEnum::cup ? '111100' : '113200';
        $monto = number_format(floatval($entity->getMontoTotal()), 2);
        $html = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
            <html xmlns='http://www.w3.org/1999/xhtml'>
            <head>
            <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
            <title>Comprobante Pago Fincimex {$entity->getNoCheque()}</title>
            <style>
                table {
                    border:0 solid;
                    border-radius:0;
                    font-family: 'Arial', serif;
                    font-size: 11px;
                    border-collapse: collapse;
                }
                td{
                    height: 10px;
                    padding: 2px;
                    text-align: center;
                }
            </style>
            </head>

            <body>
            <table cellspacing='0' cellpadding='5' border='1' width='100%'>
              <tr>
                <td>Consecutivo</td>
                <td>Código Moneda</td>
                <td>Código Cuenta</td>
                <td>Código Centro de costo</td>
                <td>Código Acreedor\Deudor</td>
                <td>Documento</td>
                <td>Débito</td>
                <td>Crédito</td>
                <td>Fecha Valor</td>
                <td>Fecha</td>
                <td>Código Diario Contable</td>
                <td>Descripción del Apunte</td>
                <td>Título del Comprobante</td>
                <td>Descripción general del comprobante</td>
              </tr>
              <tr>
                <td align='center'>1</td>
                <td>{$entity->getMoneda()->getNombre()}</td>
                <td>$pago_anticipado</td>
                <td></td>
                <td></td>
                <td>{$entity->getNoCheque()}</td>
                <td style='text-align: right;'>{$monto} </td>
                <td style='text-align: right;'>0,00 </td>
                <td></td>
                <td>{$entity->getFechaRegistro()->format('d/m/Y')}</td>
                <td>LG</td>
                <td>Anticipo</td>
                <td>Anticipo</td>
                <td>CONTABILIZANDO Anticipo</td>
              </tr>
              <tr>
                <td align='center'>1</td>
                <td>{$entity->getMoneda()->getNombre()}</td>
                <td>$efectivo_banco</td>
                <td></td>
                <td></td>
                <td>{$entity->getNoCheque()}</td>
                <td style='text-align: right;'>0,00 </td>
                <td style='text-align: right;'>{$monto} </td>
                <td></td>
                <td></td>
                <td>LG</td>
                <td>Anticipo</td>
                <td>Anticipo</td>
                <td>CONTABILIZANDO Anticipo</td>
              </tr>
            </table>
            </body>
        </html>";

        return $html;
    }
}