<?php

namespace Geocuba\SoporteBundle\Controller;

use Doctrine\Common\Util\Debug;
use Geocuba\AdminBundle\Entity\Notificacion;
use Geocuba\PortadoresBundle\Entity\AnticiposEliminados;
use Geocuba\PortadoresBundle\Entity\HistorialTarjeta;
use Geocuba\PortadoresBundle\Entity\Liquidacion;
use Geocuba\PortadoresBundle\Entity\LiquidacionesEliminadas;
use Geocuba\PortadoresBundle\Entity\RecargasEliminadas;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;


class TarjetaEventosController extends Controller
{
    use ViewActionTrait;

    public function loadHistorialAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $tarjeta = $request->get('tarjeta');
        $mes = intval($request->get('mes'));
        $anno = intval($request->get('anno'));

        $entities = $em->getRepository('PortadoresBundle:HistorialTarjeta')->findBy(array('tarjetaid' => $tarjeta, 'mes' => $mes, 'anno' => $anno));

        $_data = array();
        /** @var HistorialTarjeta $entity */
        foreach ($entities as $entity) {
            $_liq = $entity->getLiquidacionid();
            $_data[] = array(
                'id' => $entity->getId(),
                'imp_entrada' => (float)$entity->getEntradaImporte(),
                'imp_salida' => (float)$entity->getSalidaImporte(),
                'cant_entrada' => (float)$entity->getEntradaCantidad(),
                'cant_salida' => (float)$entity->getSalidaCantidad(),
                'accion' => isset($_liq) ? 'Liquidación' : 'Recarga',
                'fecha' => $entity->getFecha()->format('d-m-Y'),
                'date' => $entity->getFecha()->format('Y-m-d'),
                'hour' => $entity->getFecha()->format('g:i A'),
                'saldo' => (float)$entity->getExistenciaImporte(),
                'estado' => $entity->getTarjetaid()->getEstado()
            );
        }

        return new JsonResponse(array('rows' => $_data));
    }

    public function cleanAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $_id = $request->get('tarjeta');
        $nunidadid = trim($request->get('unidadid'));
        $date = $request->get('date');
        $hour = $request->get('hour');

        $conn = $this->get('database_connection');
        $fechaObj = date_create_from_format('Y-m-d g:i A', $date . ' ' . $hour);
        $fecha = $fechaObj->format('Y-m-d g:i A');
        $anno = $fechaObj->format('Y');
        $mes = $fechaObj->format('m');

        $entities_historiales = $conn->fetchAll("SELECT id, tarjetaid, fecha, entrada_importe, entrada_cantidad, salida_importe, 
                                               salida_cantidad, existencia_importe, existencia_cantidad, nro_vale, 
                                               nro_factura, cancelado, mes, anno, liquidacionid, saldo_inicial, 
                                               saldo_inicial_cantidad, racargaid
                                               FROM datos.historial_tarjeta where  tarjetaid='$_id' and fecha >= '$fecha';");

        $entities_cargas = $conn->fetchAll("SELECT  tarjetaid,sum(entrada_importe) as cargas
                                          FROM datos.historial_tarjeta where tarjetaid='$_id'  and fecha >= '$fecha'
                                          group by tarjetaid;");

        $entities_salidas = $conn->fetchAll("SELECT  tarjetaid,sum(salida_importe) as salidas
                                             FROM datos.historial_tarjeta where tarjetaid='$_id' and fecha >= '$fecha'
                                             group by tarjetaid;");

        $cargas = ($entities_cargas) ? $entities_cargas[0]['cargas'] : 0;
        $salidas = ($entities_salidas) ? $entities_salidas[0]['salidas'] : 0;

        $tarjeta = $em->getRepository('PortadoresBundle:Tarjeta')->findOneBy(array('id' => $_id));

        $entity_cuenta = $em->getRepository('PortadoresBundle:CuentaRecarga')->findOneBy(array('unidad' => $nunidadid, 'moneda' => $tarjeta->getMonedaid(), 'tipoCombustible' => $tarjeta->getTipoCombustibleid()));
        $last_asignacion = $em->getRepository('PortadoresBundle:Asignacion')->findOneBy(array('tipoCombustible' => $tarjeta->getTipoCombustibleid(), 'unidad' => $nunidadid, 'moneda' => $tarjeta->getMonedaid(), 'visible' => true), array('fecha' => 'DESC'));

        //Actualizando la cuenta
        if ($entity_cuenta) {
            $entity_cuenta->setMonto($entity_cuenta->getMonto() + $cargas);
            $em->persist($entity_cuenta);
        } else {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }

        //Actualizando la Disponibilidad
        if ($last_asignacion) {
            if(((double)$last_asignacion->getDisponible() + (double)$cargas / (double)$tarjeta->getTipoCombustibleid()->getPrecio()) >= 0){
                $last_asignacion->setDisponible((double)$last_asignacion->getDisponible() + (double)$cargas / (double)$tarjeta->getTipoCombustibleid()->getPrecio());
                $em->persist($last_asignacion);
            }else{
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Existe un problema con la actualización de la disponibilidad de combustible. Verifique el historial de la tarjeta'));
            }
        }else{
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Solicite una asignación de combustible '));
        }

        $importe_actual = $tarjeta->getImporte();
        $importe_mas_salidas = $importe_actual + $salidas;
        $importe_original = $importe_mas_salidas - $cargas;

        $tarjeta->setImporte($importe_original);
        $em->persist($tarjeta);

        if ($entities_historiales) {
            foreach ($entities_historiales as $historial) {
                if (!is_null($historial['liquidacionid'])) {
                    //Eliminando Liquidaciones
                    /** @var Liquidacion $entiti_liquida */
                    $entiti_liquida = $em->getRepository('PortadoresBundle:Liquidacion')->find($historial['liquidacionid']);
                    $liquidacioneseliminadas = new LiquidacionesEliminadas();
                    $liquidacioneseliminadas->setNvehiculoid($entiti_liquida->getNvehiculoid());
                    $liquidacioneseliminadas->setNroVale($entiti_liquida->getNroVale());
                    $liquidacioneseliminadas->setNpersonaid($entiti_liquida->getNpersonaid());
                    $liquidacioneseliminadas->setImporte($entiti_liquida->getImporte());
                    $liquidacioneseliminadas->setImporteInicial($entiti_liquida->getImporteInicial());
                    $liquidacioneseliminadas->setImporteFinal($entiti_liquida->getImporteInicial());
                    $liquidacioneseliminadas->setAnticipo($entiti_liquida->getAnticipo()->getId());
                    $liquidacioneseliminadas->setCantLitros($entiti_liquida->getCantLitros());
                    $liquidacioneseliminadas->setFechaRegistro($entiti_liquida->getFechaRegistro());
                    $liquidacioneseliminadas->setFechaVale($entiti_liquida->getFechaVale());
                    $liquidacioneseliminadas->setNunidadid($entiti_liquida->getUnidadid()->getId());
                    $liquidacioneseliminadas->setNtarjetaid($entiti_liquida->getTarjetaid());
                    $liquidacioneseliminadas->setNservicentroid($entiti_liquida->getServicentroid());
                    $liquidacioneseliminadas->setNfamiliaid($entiti_liquida->getNfamiliaid());
                    $liquidacioneseliminadas->setNcentrocostoid($entiti_liquida->getNcentrocostoid());
                    $liquidacioneseliminadas->setNactividadid($entiti_liquida->getNactividadid());
                    $liquidacioneseliminadas->setVisible($entiti_liquida->getVisible());

                    $ent_notificacion = new Notificacion();
                    $ent_notificacion->setFechaCreacion(new \DateTime());
                    $ent_notificacion->setTipo(3);
                    $ent_notificacion->setUsuario($this->get('security.token_storage')->getToken()->getUser());
                    $ent_notificacion->setMensaje('Se ha eliminado la liquidacion del anticipo - ' . $entiti_liquida->getAnticipo()->getNoVale() . '- de la tarjeta - ' . $tarjeta->getNroTarjeta());

                    $em->persist($liquidacioneseliminadas);
                    $em->persist($ent_notificacion);
                    $em->remove($entiti_liquida);

                } else {
                    //Eliminando Historial de Recarga
                    /** @var RecargasEliminadas $entities_Historial_recarga */
                    $entities_Historial_recarga = $em->getRepository('PortadoresBundle:HistorialContableRecarga')->find($historial['racargaid']);
                    $recargaseliminadas = new RecargasEliminadas();
                    $recargaseliminadas->setFecha($entities_Historial_recarga->getFecha());
                    $recargaseliminadas->setIdTarjeta($entities_Historial_recarga->getIdTarjeta());
                    $recargaseliminadas->setMontoRecarga($entities_Historial_recarga->getMontoRecarga());
                    $recargaseliminadas->setMontoRestante($entities_Historial_recarga->getMontoRestante());
                    $recargaseliminadas->setMontoRecargaLitros($entities_Historial_recarga->getMontoRecargaLitros());
                    $recargaseliminadas->setMontoRestanteLitros($entities_Historial_recarga->getMontoRestanteLitros());
                    $recargaseliminadas->setNombreUsuario($entities_Historial_recarga->getNombreUsuario());
                    $recargaseliminadas->setNroFactura($historial['nro_factura']);
                    $recargaseliminadas->setNroVale($historial['nro_vale']);

                    $ent_notificacion = new Notificacion();
                    $ent_notificacion->setFechaCreacion(new \DateTime());
                    $ent_notificacion->setTipo(3);
                    $ent_notificacion->setUsuario($this->get('security.token_storage')->getToken()->getUser());
                    $ent_notificacion->setMensaje('Se ha eliminado la recarga - ' . $historial['nro_factura'] . '- de la tarjeta - ' . $tarjeta->getNroTarjeta().'correspondiente a la fecha - '.$entities_Historial_recarga->getFecha()->format('Y-m-d'));

                    $em->persist($ent_notificacion);
                    $em->persist($recargaseliminadas);
                    $em->remove($entities_Historial_recarga);
                }
                //Eliminando Historial de Tarjeta
                $enti_his = $em->getRepository('PortadoresBundle:HistorialTarjeta')->find($historial['id']);
                $em->remove($enti_his);
            }

            //Eliminando Anticipos
            $sql_anticipos = $conn->fetchAll("SELECT id as id , fecha as fecha
                                              FROM datos.anticipo where tarjeta='$_id' and fecha >= '$fecha'");
            $cant_ant_del = 0;
            if (count($sql_anticipos) > 0) {
                foreach ($sql_anticipos as $item) {
                    $cant_ant_del ++;
                    /** @var AnticiposEliminados $ent_ant */
                    $ent_ant = $em->getRepository('PortadoresBundle:Anticipo')->find($item['id']);
                    $ant_del = new AnticiposEliminados();
                    $ant_del->setAnno($anno);
                    $ant_del->setMes($mes);
                    $ant_del->setConsecutivo((int)explode('-', $ent_ant->getNoVale())[1]);
                    $ant_del->setTarjeta($tarjeta);
                    $ant_del->setAbierto($ent_ant->getAbierto());
                    $ant_del->setCantidad($ent_ant->getCantidad());
                    $ant_del->setAnticipo($ent_ant->getId());
                    $ant_del->setExcepcional($ent_ant->getExcepcional());
                    $ant_del->setFecha($ent_ant->getFecha());
                    $ant_del->setImporte($ent_ant->getImporte());
                    $ant_del->setMotivo($ent_ant->getMotivo());
                    $ant_del->setNoVale($ent_ant->getNoVale());
                    $ant_del->setNpersonaid($ent_ant->getNpersonaid());
                    $ant_del->setTerceros($ent_ant->getTerceros());
                    $ant_del->setVehiculo($ent_ant->getVehiculo());
                    $ant_del->setTransito($ent_ant->getTransito());
                    $ant_del->setTrabajo($ent_ant->getTrabajo());
                    $ant_del->setToRestore(false);

                    $ent_notificacion = new Notificacion();
                    $ent_notificacion->setFechaCreacion(new \DateTime());
                    $ent_notificacion->setTipo(3);
                    $ent_notificacion->setUsuario($this->get('security.token_storage')->getToken()->getUser());
                    $ent_notificacion->setMensaje('Se ha eliminado el anticipo - ' . $ent_ant->getNoVale() . '- para la tarjeta - ' . $tarjeta->getNroTarjeta());

                    $em->remove($ent_ant);
                    $em->persist($ent_notificacion);
                    $em->persist($ant_del);
                }
            }
        }
        try {
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Tarjeta corregida con éxito. Se han eliminado'.$cant_ant_del.'anticipos'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }
}