<?php
/**
 * Created by PhpStorm.
 * User: asisoftware13
 * Date: 2/22/2021
 * Time: 10:11 a.m.
 */

namespace Geocuba\SoporteBundle\Controller;


use Geocuba\PortadoresBundle\Entity\ActaRespMaterial;
use Geocuba\PortadoresBundle\Util\MonedaEnum;
use Geocuba\PortadoresBundle\Entity\AnticiposEliminados;
use Geocuba\PortadoresBundle\Entity\CuentaGasto;
use Geocuba\PortadoresBundle\Entity\HistorialTarjeta;
use Geocuba\PortadoresBundle\Entity\HistorialContableRecarga;
use Geocuba\PortadoresBundle\Entity\Liquidacion;
use Geocuba\PortadoresBundle\Entity\Anticipo;
use Geocuba\PortadoresBundle\Entity\Consecutivos;
use Doctrine\Common\Util\Debug;
use Geocuba\PortadoresBundle\Entity\RecargasEliminadas;
use Geocuba\PortadoresBundle\Entity\Tarjeta;
use Geocuba\PortadoresBundle\Entity\Vehiculo;
use Geocuba\PortadoresBundle\Util\FechaUtil;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Datos;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Geocuba\PortadoresBundle\Util\DocumentosEnum;
use Symfony\Component\HttpKernel\Exception\{
    HttpException, NotFoundHttpException
};
use Geocuba\PortadoresBundle\Util\Utiles;

class RestoreHistorialController extends Controller
{
    use ViewActionTrait;

    public function restaurarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = trim($request->get('id'));
        $_fechaEmision = trim($request->get('fecha'));
        $_horaEmision = trim($request->get('hora'));
        $fecha = date_create_from_format('d/m/Y g:i A', $_fechaEmision . ' ' . $_horaEmision);
        $npersonaid = trim($request->get('npersonaid'));
        $vehiculoid = $request->get('vehiculoid');
        $tarjetaid = $request->get('tarjetaid');
        $importe = $request->get('importe');
        $consecutivo = (int)$request->get('consecutivo');
        $cantidad = $request->get('cantidad');
        $trabajoid = $request->get('trabajoid');
        $transito = $request->get('transito');
        $terceros = $request->get('terceros');
        $excepcional = $request->get('excepcional');

        $persona = $em->getRepository('PortadoresBundle:Persona')->find($npersonaid);
        $vehiculo = $em->getRepository('PortadoresBundle:Vehiculo')->find($vehiculoid);
        $tarjeta = $em->getRepository('PortadoresBundle:Tarjeta')->find($tarjetaid);
        $trabajo = $em->getRepository('PortadoresBundle:Trabajo')->find($trabajoid);

        $fechaDesde = $fecha->format('Y') . '-' . $fecha->format('n') . '-01 00:00:00';
        $fechaHasta = FechaUtil::getUltimoDiaMes($fecha->format('n'), $fecha->format('Y'));

        $entityValidar = $em->getRepository('PortadoresBundle:Anticipo')->findOneBy(array(
            'tarjeta' => $tarjeta,
            'abierto' => true,
            'visible' => true
        ));

        if ($entityValidar) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Existe un anticipo abierto para la tarjeta: ' . $tarjeta->getNroTarjeta() . '.'));
        }

        $entityValidar = $em->getRepository('PortadoresBundle:Anticipo')->findOneBy(array(
            'vehiculo' => $vehiculo,
            'abierto' => true,
            'visible' => true
        ));

        if ($entityValidar) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Existe un anticipo abierto para el vehículo: ' . $vehiculo->getMatricula() . '.'));
        }

        if ($vehiculo->getParalizado()) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El vehículo ' . $vehiculo->getMatricula() . ' se encuentra paralizado'));
        }

        //validar que la cantidad del anticipo no sobrepase lo que queda por consumir del proyecto seleccionado
        if (null !== $trabajo) {
            $cantAsignadaProyecto = 0;
            $asignacion = $em->getRepository('PortadoresBundle:TrabajoAsignacionCombustible')->buscarAsignacion($trabajo->getId(), $tarjeta->getTipoCombustibleid()->getId(), $tarjeta->getMonedaid()->getId());
            if (\count($asignacion) > 0) {
                $cantAsignadaProyecto = $asignacion[0]->getCantidad();
            }
            $cantConsumidaProyecto = $em->getRepository('PortadoresBundle:Liquidacion')->consumoProyecto($trabajo->getId(), $tarjeta->getTipoCombustibleid()->getId(), $fechaDesde, $fechaHasta, $tarjeta->getMonedaid()->getId());
            if (null === $cantConsumidaProyecto) {
                $cantConsumidaProyecto = 0;
            }

            if ($cantAsignadaProyecto - $cantConsumidaProyecto < $cantidad) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'La cantidad solicitada no puede exceder los ' . ($cantAsignadaProyecto - $cantConsumidaProyecto) . 'L porque sobrepasa el combustible asignado al proyecto seleccionado.'));
            }
        }

        //Validar que la cantidad del anticipo no sobrepase lo que queda por consumir del plan del vehiculo seleccionado
        if ($tarjeta->getMonedaid()->getId() == MonedaEnum::cup) {
            $cantPlanificadoVehiculo = $em->getRepository('PortadoresBundle:PlanificacionCombustible')->planificacionVehiculo($vehiculo->getId(), $fecha->format('m'), $fecha->format('Y'));
        } else {
            $cantPlanificadoVehiculo = $em->getRepository('PortadoresBundle:PlanificacionCombustibleCuc')->planificacionVehiculo($vehiculo->getId(), $fecha->format('m'), $fecha->format('Y'));
        }

        $cantConsumidaVehiculo = $em->getRepository('PortadoresBundle:Liquidacion')->consumoVehiculo($vehiculo->getId(), $tarjeta->getTipoCombustibleid()->getId(), $fechaDesde, $fechaHasta, $tarjeta->getMonedaid()->getId());
        if (null === $cantConsumidaVehiculo) {
            $cantConsumidaVehiculo = 0;
        }

        if ($cantPlanificadoVehiculo - $cantConsumidaVehiculo < $cantidad){
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'La cantidad solicitada no puede exceder los ' . ($cantPlanificadoVehiculo - $cantConsumidaVehiculo) . ' L porque sobrepasa el combustible planificado al vehículo seleccionado.'));
        }


        $entity_ant = new Anticipo();
        $entity_ant->setFecha($fecha);
        $entity_ant->setNpersonaid($persona);
        $entity_ant->setVehiculo($vehiculo);
        $entity_ant->setTarjeta($tarjeta);
        $entity_ant->setCantidad($cantidad);
        $entity_ant->setImporte($importe);
        $entity_ant->setTrabajo($trabajo);
        if ($transito) {
            $entity_ant->setTransito(true);
        } else {
            $entity_ant->setTransito(false);
        }
        if ($terceros) {
            $entity_ant->setTerceros(true);
        } else {
            $entity_ant->setTerceros(false);
        }
        $entity_ant->setExcepcional($excepcional);
        if ($excepcional)
            $entity_ant->setMotivo($request->get('motivo'));
        $entity_ant->setVisible(true);
        $entity_ant->setNoVale($vehiculo->getNunidadid()->getSiglas() . '-' . str_pad($consecutivo, 4, '0', STR_PAD_LEFT));

        $ent_ant_del = $em->getRepository('PortadoresBundle:AnticiposEliminados')->find($id);

        //Adicionar las liquidaciones asociadas al anticipo
        $anticipoid = $ent_ant_del->getAnticipo();

        $ant_liq = $em->getRepository('PortadoresBundle:LiquidacionesEliminadas')->findby(array('anticipo' => $anticipoid));
        $datos_liquidaciones = array();
        foreach ($ant_liq as $liq_del) {
            $datos_liquidaciones[] = array(
                'liquidacionid' => $liq_del->getId(),
                'nvehiculoid' => $liq_del->getNvehiculoid()->getId(),
                'ntarjetaid' => $liq_del->getNtarjetaid()->getId(),
                'ntarjetaidnro' => $liq_del->getNtarjetaid()->getNroTarjeta(),
                'npersonaid' => $liq_del->getNpersonaid()->getId(),
                'nombrepersonaid' => $liq_del->getNpersonaid()->getNombre(),
                'nsubactividadid' => $liq_del->getNactividadid() ? $liq_del->getNactividadid()->getId() : '',
                'nservicentroid' => $liq_del->getNservicentroid() ? $liq_del->getNservicentroid()->getId() : '',
                'nservicentroid_nombre' => $liq_del->getNservicentroid() ? $liq_del->getNservicentroid()->getNombre() : '',
                'ncentrocostoid' => $liq_del->getNcentrocostoid() ? $liq_del->getNcentrocostoid()->getId() : '',
                'nro_vale' => $liq_del->getNroVale(),
                'importe' => $liq_del->getImporte(),
                'importe_inicial' => $liq_del->getImporteInicial(),
                'importe_final' => $liq_del->getImporteFinal(),
                'cant_litros' => $liq_del->getCantLitros(),
                'tipo_combustible_id' => $liq_del->getNtarjetaid()->getTipoCombustibleid()->getId(),
                'tipo_combustible' => $liq_del->getNtarjetaid()->getTipoCombustibleid()->getNombre(),
                'matricula' => $liq_del->getNvehiculoid()->getMatricula(),
                'nombre_chofer' => $liq_del->getNpersonaid()->getNombre(),
                'fecha_servicio' => $liq_del->getFechaVale()->format('d/m/Y H:i:s'),
                'fecha_vale' => $liq_del->getFechaVale()->format('d/m/Y'),
                'hora_vale' => $liq_del->getFechaVale()->format('g:i A'),
                'fecha_registro' => $liq_del->getFechaRegistro()->format('d/m/Y'),
            );
        }

        $suma_importe = 0;
        foreach ($ant_liq as $liquid) {
            $suma_importe += $liquid->getImporte();
        }

        $tarjeta = $ent_ant_del->getTarjeta();
        $tarjeta->setImporte($tarjeta->getImporte() + $suma_importe);
        $em->persist($tarjeta);


        $vehiculo = $ent_ant_del->getVehiculo();

        for ($i = 0, $iMax = \count($datos_liquidaciones); $i < $iMax; $i++) {
            $liquidacionid = $datos_liquidaciones[$i]['liquidacionid'];
            $cant_litros = (float)$datos_liquidaciones[$i]['cant_litros'];
            $fecha = date_create_from_format('d/m/Y H:i:s', $datos_liquidaciones[$i]['fecha_servicio']);
            $nro_vale = $datos_liquidaciones[$i]['nro_vale'];
            $importe = (float)$datos_liquidaciones[$i]['importe'];
            $importe_inicial = (float)$datos_liquidaciones[$i]['importe_inicial'];
            $importe_final = (float)$datos_liquidaciones[$i]['importe_final'];
            $fecha_registro = date_create_from_format('d/m/Y', $datos_liquidaciones[$i]['fecha_registro']);
            $nservicentroid = $datos_liquidaciones[$i]['nservicentroid'];
            $nsubactividadid = $datos_liquidaciones[$i]['nsubactividadid'];
            $npersonaid = $datos_liquidaciones[$i]['npersonaid'];
            $nunidadid = $ent_ant_del->getTarjeta()->getUnidadid();
            $ncentrocostoid = $datos_liquidaciones[$i]['ncentrocostoid'];
            $nfamiliaid = $datos_liquidaciones[$i]['nfamilia'] ?? null;
            $fechaDesde = $fecha->format('Y') . '-' . '01-01 00:00:00';
            $fechaHasta = FechaUtil::getUltimoDiaMes(12, $fecha->format('Y'));

            $suma_importe += $importe;
            //validar la consecutividad de las acciones sobre la tarjeta
            $entityValidar = $em->getRepository('PortadoresBundle:HistorialTarjeta')->buscarHistorialValidar($tarjeta->getId(), $fecha->format('Y-m-d h:i:s'), false);
            $valido = true;
            foreach ($entityValidar as $entity_liquidacion) {
                if ($entity_liquidacion->getLiquidacionid() != null && $entity_liquidacion->getLiquidacionid()->getAnticipo()->getId() == $anticipoid) {
                    $historial = $entity_liquidacion;
                    $valido = false;
                }
            }
            if (!$valido) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'No se pueden crear liquidaciones con fecha anterior a ' . $historial->getFecha()->format('d/m/Y g:i A') . ' para la tarjeta: ' . $tarjeta->getNroTarjeta() . '.'));
            }

            $entity_liquidacion = new Liquidacion();

            $entity_liquidacion->setAnticipo($entity_ant);
            $entity_liquidacion->setNroVale($nro_vale);
            $entity_liquidacion->setImporte($importe);
            $entity_liquidacion->setImporteFinal($importe_final);
            $entity_liquidacion->setImporteInicial($importe_inicial);
            $entity_liquidacion->setCantLitros($cant_litros);
            $entity_liquidacion->setFechaVale($fecha);
            $entity_liquidacion->setFechaRegistro($fecha_registro);
            $entity_liquidacion->setNactividadid($em->getRepository('PortadoresBundle:Actividad')->find($nsubactividadid));
            $entity_liquidacion->setNpersonaid($em->getRepository('PortadoresBundle:Persona')->find($npersonaid));
            $entity_liquidacion->setNservicentroid($em->getRepository('PortadoresBundle:Servicentro')->find($nservicentroid));
            $entity_liquidacion->setNtarjetaid($tarjeta);
            $entity_liquidacion->setNvehiculoid($vehiculo);
            $entity_liquidacion->setVisible(true);
            $entity_liquidacion->setUnidadid($nunidadid);

            $entity_liquidacion->setNcentrocostoid($em->getRepository('PortadoresBundle:CentroCosto')->find($ncentrocostoid));
            if (isset($nfamiliaid) && !is_null($nfamiliaid))
                $entity_liquidacion->setNfamiliaid($em->getRepository('PortadoresBundle:Familia')->find($nfamiliaid));

            $tarjeta->setImporte($importe_final);

            $em->persist($entity_liquidacion);
            $em->persist($tarjeta);


            $precio_comb = $tarjeta->getTipoCombustibleid()->getPrecio();
            $session = $request->getSession();
            $mes = $session->get('current_month');
            $anno = $session->get('current_year');

            $entity_historial = new HistorialTarjeta();
            $entity_historial->setLiquidacionid($entity_liquidacion);
            $entity_historial->setFecha($fecha);
            $entity_historial->setSalidaImporte($importe);
            $entity_historial->setSalidaCantidad($cant_litros);
            $entity_historial->setExistenciaImporte($importe_final);
            $entity_historial->setExistenciaCantidad(round($importe_final / $precio_comb, 2));
            $entity_historial->setNroVale($nro_vale);
            $entity_historial->setCancelado(false);
            $entity_historial->setMes($mes);
            $entity_historial->setAnno($anno);
            $entity_historial->setTarjetaid($tarjeta);

            $em->persist($entity_historial);
        }
        try {
            $em->remove($ent_ant_del);
            $em->persist($entity_ant);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Anticipo restaurado con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function modAntRestAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $_fechaEmision = trim($request->get('fecha'));
        $_horaEmision = trim($request->get('hora'));
        $fecha = date_create_from_format('d/m/Y g:i A', $_fechaEmision . ' ' . $_horaEmision);
        $npersonaid = trim($request->get('npersonaid'));
        $vehiculoid = $request->get('vehiculoid');
        $tarjetaid = $request->get('tarjetaid');
        $importe = $request->get('importe');
        $cantidad = $request->get('cantidad');
        $trabajoid = $request->get('trabajoid');
        $transito = $request->get('transito');
        $terceros = $request->get('terceros');
        $excepcional = $request->get('excepcional');

        $persona = $em->getRepository('PortadoresBundle:Persona')->find($npersonaid);
        $vehiculo = $em->getRepository('PortadoresBundle:Vehiculo')->find($vehiculoid);
        $tarjeta = $em->getRepository('PortadoresBundle:Tarjeta')->find($tarjetaid);
        $trabajo = $em->getRepository('PortadoresBundle:Trabajo')->find($trabajoid);

        $fechaDesde = $fecha->format('Y') . '-' . $fecha->format('n') . '-01 00:00:00';
        $fechaHasta = FechaUtil::getUltimoDiaMes($fecha->format('n'), $fecha->format('Y'));

        $entity = $em->getRepository('PortadoresBundle:AnticiposEliminados')->find($id);

        if ($tarjetaid != $entity->getTarjeta()->getId()) {
            $entityValidar = $em->getRepository('PortadoresBundle:Anticipo')->findOneBy(array(
                'tarjeta' => $tarjeta,
                'abierto' => true,
                'visible' => true
            ));

            if ($entityValidar) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Existe un anticipo abierto para la tarjeta: ' . $tarjeta->getNroTarjeta() . '.'));
            }
        }

        if ($vehiculoid != $entity->getVehiculo()->getId()) {
            $entityValidar = $em->getRepository('PortadoresBundle:Anticipo')->findOneBy(array(
                'vehiculo' => $vehiculo,
                'abierto' => true,
                'visible' => true
            ));
            if ($entityValidar) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Existe un anticipo abierto para el vehículo: ' . $vehiculo->getMatricula() . '.'));
            }
        }

        //validar que la cantidad del anticipo no sobrepase lo que queda por consumir del proyecto seleccionado
        if (!is_null($trabajo)) {
            $cantAsignadaProyecto = 0;
            $asignacion = $em->getRepository('PortadoresBundle:TrabajoAsignacionCombustible')->buscarAsignacion($trabajo->getId(), $tarjeta->getTipoCombustibleid()->getId(), $tarjeta->getMonedaid()->getId());
            if (count($asignacion) > 0)
                $cantAsignadaProyecto = $asignacion[0]->getCantidad();
            $cantConsumidaProyecto = $em->getRepository('PortadoresBundle:Liquidacion')->consumoProyecto($trabajo->getId(), $tarjeta->getTipoCombustibleid()->getId(), $fechaDesde, $fechaHasta, $tarjeta->getMonedaid()->getId());
            if (is_null($cantConsumidaProyecto))
                $cantConsumidaProyecto = 0;
            if ($cantAsignadaProyecto - $cantConsumidaProyecto < $cantidad)
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'La cantidad solicitada no puede exceder los ' . ($cantAsignadaProyecto - $cantConsumidaProyecto) . 'L porque sobrepasa el combustible asignado al proyecto seleccionado.'));
        }

        $entity->setFecha($fecha);
        $entity->setNpersonaid($persona);
        $entity->setVehiculo($vehiculo);
        $entity->setTarjeta($tarjeta);
        $entity->setCantidad($cantidad);
        $entity->setImporte($importe);
        $entity->setTrabajo($trabajo);
        if ($transito)
            $entity->setTransito(true);
        else
            $entity->setTransito(false);
        if ($terceros)
            $entity->setTerceros(true);
        else
            $entity->setTerceros(false);
        $entity->setExcepcional($excepcional);
        if ($excepcional)
            $entity->setMotivo($request->get('motivo'));
        try {
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Anticipo  modificado con éxito.'));
            return $response;
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function restaurarRecAction(Request $request)
    {
        $message = '';
        $unidad = $request->get('nunidadid');
        $em = $this->getDoctrine()->getManager();
        $entrada = $request->get('entrada');
        $recarga = $request->get('recarga');
        $salida = $request->get('salida');
        $id = trim($request->get('id'));


        $tarjetaid = $request->get('tarjetaid');
        $entity = $em->getRepository('PortadoresBundle:Tarjeta')->findOneBy(array('id' => $tarjetaid));

        //Validar que no exista un Anticipo Abierto para esa Tarjeta
        $anticipo = $em->getRepository('PortadoresBundle:Anticipo')->findOneBy(array('tarjeta' => $entity, 'abierto' => true, 'visible' => true));

        if ($anticipo)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Existe un anticipo abierto para la tarjeta ' . $entity->getNroTarjeta()));

        $_user = $this->get('security.token_storage')->getToken()->getUser();

        //get precio coombustible actual
        $precio = Datos::getCombustibles($em, $entity->getTipoCombustibleid()->getId());


        //recargar
        if (isset($recarga, $entrada, $salida) && $recarga == 'true' && $entrada == 'false' && $salida == 'false') {

            $_fechaEmision = trim($request->get('fecha_recarga'));
            $_horaEmision = trim($request->get('hora_recarga'));
            $fecha_recarga = date_create_from_format('d/m/Y g:i A', $_fechaEmision . ' ' . $_horaEmision);

            $mes = $fecha_recarga->format('n');
            $anno = $fecha_recarga->format('Y');

            $importe_recarga = $request->get('importe_recarga');
            $no_vale = $request->get('no_vale');
            $no_factura = $request->get('no_factura');


            $entityValidar = $em->getRepository('PortadoresBundle:HistorialTarjeta')->buscarHistorialValidar($tarjetaid, $fecha_recarga->format('Y-m-d H:i:s'), false);

            if (\count($entityValidar) > 0) {
                $historial = $entityValidar[count($entityValidar) - 1];
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'No se pueden hacer recargas con fecha anterior a ' . $historial->getFecha()->format('d/m/Y g:i A') . ' para la tarjeta: ' . $entity->getNroTarjeta() . '.'));
            }

            $entityValidar = $em->getRepository('PortadoresBundle:HistorialTarjeta')->findBy(array(
                'tarjetaid' => $tarjetaid,
                'fecha' => $fecha_recarga,
                'nroVale' => $no_vale,
                'cancelado' => false));
            if (\count($entityValidar) > 0) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'La recarga solicitada ya fue introducida al sistema.'));
            }

            $entity_cuenta = $em->getRepository('PortadoresBundle:CuentaRecarga')->findOneBy(array('unidad' => $unidad, 'moneda' => $entity->getMonedaid(), 'tipoCombustible' => $entity->getTipoCombustibleid()));
            $last_asignacion = $em->getRepository('PortadoresBundle:Asignacion')->findOneBy(array('tipoCombustible' => $entity->getTipoCombustibleid()->getId(), 'unidad' => $unidad, 'moneda' => $entity->getMonedaid()->getId(), 'visible' => true), array('fecha' => 'DESC'));


            if ($last_asignacion) {
                if ((double)$last_asignacion->getDisponible() < (double)$importe_recarga) {
                    return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El importe de la recarga excede la disponibilidad.'));
                }
                $last_asignacion->setDisponible((double)$last_asignacion->getDisponible() - (double)$importe_recarga / (double)$entity->getTipoCombustibleid()->getPrecio());

                $entity_historial = new HistorialContableRecarga();
                $entity_historial->setFecha($fecha_recarga);
                $entity_historial->setIdTarjeta($entity);
                $entity_historial->setNombreUsuario($_user->getnombreCompleto());
                $entity_historial->setMontoRecarga($importe_recarga);
                $entity_historial->setMontoRestante($entity_cuenta->getMonto());
                $entity_historial->setMontoRecargaLitros(round($importe_recarga / $precio['precio'], 2));
                $entity_historial->setMontoRestanteLitros(round($entity_cuenta->getMonto() / $precio['precio'], 2));

                $em->persist($entity_historial);

                $entity_historial_id = $entity_historial->getId();

            } else {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Solicite una asignación de combustible '));
            }


            $precio_comb = $entity->getTipoCombustibleid()->getPrecio();

            $entity1 = new HistorialTarjeta();
            $entity1->setFecha($fecha_recarga);
            $entity1->setEntradaImporte($importe_recarga);
            $entity1->setEntradaCantidad(round($importe_recarga / $precio_comb, 2));
            $entity1->setExistenciaImporte($entity->getImporte() + $importe_recarga);
            $entity1->setExistenciaCantidad(round(($entity->getImporte() + $importe_recarga) / $precio_comb, 2));
            $entity1->setNroVale($no_vale);
            $entity1->setNroFactura($no_factura);
            $entity1->setCancelado(false);
            $entity1->setMes($mes);
            $entity1->setAnno($anno);
            $entity1->setTarjetaid($entity);
            $entity1->setRacargaid($em->getRepository('PortadoresBundle:HistorialContableRecarga')->find($entity_historial_id));
            $em->persist($entity1);
            try {
                $em->flush();
            } catch (\Exception $e) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
            }

            $entity->setImporte($entity->getImporte() + $importe_recarga);
            $em->persist($entity);
            try {
                $em->flush();
            } catch (\Exception $e) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
            }
            $ent_rec_del = $em->getRepository('PortadoresBundle:RecargasEliminadas')->find($id);
            $em->remove($ent_rec_del);

            $message = 'Tarjeta recargada con éxito.';

        }

        try {
            $em->flush();
        } catch (\Exception $e) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }

        return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => $message));
    }

    public function loadAntRestAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $mes = $request->get('mes');
        $anno = $request->get('anno');

        if (\is_null($mes) && \is_null($anno)) {
            $fecha = new \DateTime();
            $anno = $fecha->format('Y');
            $mes = $fecha->format('n');
        }

        $nunidadid = $request->get('unidadid');

        $start = $request->get('start');
        $limit = $request->get('limit');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);


        $qb = $em->createQueryBuilder();
        $qb->select('count(ant_eliminados)')
            ->from('PortadoresBundle:AnticiposEliminados', 'ant_eliminados')
            ->innerJoin('ant_eliminados.tarjeta', 'tarjeta')
            ->where($qb->expr()->in('tarjeta.nunidadid', ':unidades'))
            ->setParameter('unidades', $_unidades)
            ->setMaxResults($limit)
            ->setFirstResult($start);
        $total = $qb->getQuery()->getSingleScalarResult();

        $qb = $em->createQueryBuilder();
        $qb->select('ant_eliminados')
            ->from('PortadoresBundle:AnticiposEliminados', 'ant_eliminados')
            ->innerJoin('ant_eliminados.tarjeta', 'tarjeta')
            ->where($qb->expr()->in('tarjeta.nunidadid', ':unidades'))
            ->setParameter('unidades', $_unidades)
            ->setMaxResults($limit)
            ->setFirstResult($start);
        $datos = $qb->getQuery()->getResult();

        $_data = array();

        /** @var AnticiposEliminados $entity */
        foreach ($datos as $entity) {
            $id_tarjeta = $entity->getTarjeta()->getId();
            $tarjeta = $em->getRepository('PortadoresBundle:Tarjeta')->find($id_tarjeta);
            $importe_tarjeta = $tarjeta->getImporte();
            $_data[] = array(
                'id' => $entity->getId(),
                'anticipo' => $entity->getAnticipo(),
                'fecha' => date_format($entity->getFecha(), 'd/m/Y'),
                'hora' => date_format($entity->getFecha(), 'g:i A'),
                'fecha_anticipo' => date_format($entity->getFecha(), 'd/m/Y g:i A'),
                'no_vale' => $entity->getNoVale(),
                'importe_tarjeta' => $importe_tarjeta,
                'npersonaid' => $entity->getNpersonaid()->getId(),
                'nombrepersonaid' => $entity->getNpersonaid()->getNombre(),
                'vehiculoid' => $entity->getVehiculo()->getId(),
                'vehiculo' => $entity->getVehiculo()->getMatricula(),
                'tarjetaid' => $entity->getTarjeta()->getId(),
                'tarjeta' => $entity->getTarjeta()->getNroTarjeta(),
                'importe' => number_format($entity->getImporte(), 2),
                'cantidad' => number_format($entity->getCantidad(), 2),
                'abierto' => $entity->getAbierto(),
                'trabajoid' => empty($entity->getTrabajo()) ? '' : $entity->getTrabajo()->getId(),
                'trabajo' => empty($entity->getTrabajo()) ? '' : $entity->getTrabajo()->getNombre(),
                'transito' => $entity->getTransito(),
                'terceros' => $entity->getTerceros(),
                'to_restore' => $entity->getToRestore(),
                'excepcional' => $entity->getExcepcional(),
                'transito_plugin' => $entity->getTransito() ? 'SI' : 'NO',
                'terceros_plugin' => $entity->getTerceros() ? 'SI' : 'NO',
                'tipo_combustible_id' => $entity->getTarjeta()->getTipoCombustibleid()->getId(),
                'centrocostoid' => $entity->getTarjeta()->getCentrocosto()->getId(),
                'actividadid' => $entity->getVehiculo()->getActividad()->getId(),
                'excepcional_plugin' => $entity->getExcepcional() ? 'SI' : 'NO',
                'motivo' => $entity->getExcepcional() ? $entity->getMotivo() : '',
                'consecutivo' => $entity->getConsecutivo(),
            );
        }
        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    public function loadliqRestAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();

        $start = $request->get('start');
        $limit = $request->get('limit');

        $mes = $request->get('mes');
        $anno = $request->get('anno');

        $nunidadid = $request->get('unidadid');
        $nro_vale = $request->get('nro_vale');
        $tarjetaid = $request->get('tarjeta');
        $anticipoid = $request->get('anticipo');
//        Debug::dump($anticipoid);die;

        $fechaDesde = $mes ? $anno . '-' . $mes . '-' . '1' : '';
        $fechaHasta = $mes ? FechaUtil::getUltimoDiaMes($mes, $anno) : '';

        $total = 0;
        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);


        $entitiesLiquidaciones = $em->getRepository('PortadoresBundle:LiquidacionesEliminadas')->buscarLiq($_unidades, $nro_vale, $tarjetaid, $anticipoid, $fechaDesde, $fechaHasta);
        $anticipo = $em->getRepository('PortadoresBundle:AnticiposEliminados')->findOneBy(array('anticipo' => $anticipoid));


        $_data = array();
        foreach ($entitiesLiquidaciones as $entity) {
            $historial = $em->getRepository('PortadoresBundle:HistorialTarjeta')->findOneBy(array('liquidacionid' => $entity->getId()));
            $_data[] = array(
                'liquidacionid' => $entity->getId(),
                'nvehiculoid' => $entity->getNvehiculoid()->getId(),
                'ntarjetaid' => $entity->getNtarjetaid()->getId(),
                'ntarjetaidnro' => $entity->getNtarjetaid()->getNroTarjeta(),
                'npersonaid' => $entity->getNpersonaid()->getId(),
                'nombrepersonaid' => $entity->getNpersonaid()->getNombre(),
                'nsubactividadid' => $entity->getNactividadid() ? $entity->getNactividadid()->getId() : '',
                'nservicentroid' => $entity->getNservicentroid() ? $entity->getNservicentroid()->getId() : '',
                'nservicentroid_nombre' => $entity->getNservicentroid() ? $entity->getNservicentroid()->getNombre() : '',
                'ncentrocostoid' => $entity->getNcentrocostoid() ? $entity->getNcentrocostoid()->getId() : '',
                'nro_vale' => $entity->getNroVale(),
                'importe' => $entity->getImporte(),
                'importe_inicial' => $entity->getImporteInicial(),
                'importe_final' => $entity->getImporteFinal(),
                'cant_litros' => $entity->getCantLitros(),
                'tipo_combustible_id' => $entity->getNtarjetaid()->getTipoCombustibleid()->getId(),
                'tipo_combustible' => $entity->getNtarjetaid()->getTipoCombustibleid()->getNombre(),
                'matricula' => $entity->getNvehiculoid()->getMatricula(),
                'nombre_chofer' => $entity->getNpersonaid()->getNombre(),
                'fecha_servicio' => $entity->getFechaVale()->format('d/m/Y H:i:s'),
                'fecha_vale' => $entity->getFechaVale()->format('d/m/Y'),
                'hora_vale' => $entity->getFechaVale()->format('g:i A'),
                'fecha_registro' => $entity->getFechaRegistro()->format('d/m/Y'),
                'historial' => $historial !== null
            );
        }

        return new JsonResponse(array('success' => true, 'rows' => $_data, 'total' => $total));
    }

    public function loadRecRestAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $mes = $request->get('mes');
        $anno = $request->get('anno');

        if (\is_null($mes) && \is_null($anno)) {
            $fecha = new \DateTime();
            $anno = $fecha->format('Y');
            $mes = $fecha->format('n');
        }

        $nunidadid = $request->get('unidadid');

        $start = $request->get('start');
        $limit = $request->get('limit');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);


        $qb = $em->createQueryBuilder();
        $qb->select('count(rec_eliminadas)')
            ->from('PortadoresBundle:RecargasEliminadas', 'rec_eliminadas')
            ->innerJoin('rec_eliminadas.idTarjeta', 'tarjeta')
            ->where($qb->expr()->in('tarjeta.nunidadid', ':unidades'))
            ->setParameter('unidades', $_unidades)
            ->andWhere($qb->expr()->eq('rec_eliminadas.restaurada', 'false'))
            ->setMaxResults($limit)
            ->setFirstResult($start);
        $total = $qb->getQuery()->getSingleScalarResult();

        $qb = $em->createQueryBuilder();
        $qb->select('rec_eliminadas')
            ->from('PortadoresBundle:RecargasEliminadas', 'rec_eliminadas')
            ->innerJoin('rec_eliminadas.idTarjeta', 'tarjeta')
            ->where($qb->expr()->in('tarjeta.nunidadid', ':unidades'))
            ->setParameter('unidades', $_unidades)
            ->andWhere($qb->expr()->eq('rec_eliminadas.restaurada', 'false'))
            ->setMaxResults($limit)
            ->setFirstResult($start);
        $datos = $qb->getQuery()->getResult();

        $_data = array();

        /** @var RecargasEliminadas $entity */
        foreach ($datos as $entity) {
            $id_tarjeta = $entity->getIdTarjeta()->getId();
            $tarjeta = $em->getRepository('PortadoresBundle:Tarjeta')->find($id_tarjeta);
            $_data[] = array(
                'id' => $entity->getId(),
                'fecha_recarga' => date_format($entity->getFecha(), 'd/m/Y'),
                'hora_recarga' => date_format($entity->getFecha(), 'g:i A'),
                'importe_recarga' => $entity->getMontoRecarga(),
                'tarjeta' => $entity->getIdTarjeta()->getNroTarjeta(),
                'estado' => $entity->getIdTarjeta()->getEstado(),
                'monedaid' => $entity->getIdTarjeta()->getMonedaid(),
                'idtarjeta' => $entity->getIdTarjeta()->getId(),
                'no_vale' => $entity->getNroVale(),
                'no_factura' => $entity->getNroFactura()
            );
        }
        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    public function descartarRecAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $ent_rec_del = $em->getRepository('PortadoresBundle:RecargasEliminadas')->find($id);
        $em->remove($ent_rec_del);
        try {
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Recarga descartada con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function addLiqToRestoreAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        try {
            $em->transactional(function ($em) use ($request) {
                $anticipoid = $request->get('anticipoid');
                $datos_liquidaciones = json_decode($request->get('liquidaciones'));
                $anticipo = $em->getRepository('PortadoresBundle:AnticiposEliminados')->findOneBy(array('anticipo' => $anticipoid));
                $ant_liq = $em->getRepository('PortadoresBundle:LiquidacionesEliminadas')->findby(array('anticipo' => $anticipoid));

                $vehiculo = $anticipo->getVehiculo();
                $tarjeta = $anticipo->getTarjeta();

                for ($i = 0, $iMax = \count($datos_liquidaciones); $i < $iMax; $i++) {
                    $liquidacionid = $datos_liquidaciones[$i]->liquidacionid;
                    $cant_litros = (float)$datos_liquidaciones[$i]->cant_litros;
                    $fecha = date_create_from_format('d/m/Y H:i:s', $datos_liquidaciones[$i]->fecha_servicio);
                    $nro_vale = $datos_liquidaciones[$i]->nro_vale;
                    $importe = (float)$datos_liquidaciones[$i]->importe;
                    $importe_inicial = (float)$datos_liquidaciones[$i]->importe_inicial;
                    $importe_final = (float)$datos_liquidaciones[$i]->importe_final;
                    $fecha_registro = date_create_from_format('d/m/Y', $datos_liquidaciones[$i]->fecha_registro);
                    $nservicentroid = $datos_liquidaciones[$i]->nservicentroid;
                    $nsubactividadid = $datos_liquidaciones[$i]->nsubactividadid;
                    $npersonaid = $datos_liquidaciones[$i]->npersonaid;
                    $nunidadid = $anticipo->getTarjeta()->getUnidadid();
                    $ncentrocostoid = $datos_liquidaciones[$i]->ncentrocostoid;
                    $nfamiliaid = $datos_liquidaciones[$i]->nfamilia ?? null;

                    $entity = $em->getRepository('PortadoresBundle:LiquidacionesEliminadas')->find($liquidacionid);

                    $entity->setNroVale($nro_vale);
                    $entity->setImporte($importe);
                    $entity->setImporteFinal($importe_final);
                    $entity->setImporteInicial($importe_inicial);
                    $entity->setCantLitros($cant_litros);
                    $entity->setFechaVale($fecha);
                    $entity->setFechaRegistro($fecha_registro);

                    $entity->setNactividadid($em->getRepository('PortadoresBundle:Actividad')->find($nsubactividadid));
                    $entity->setNpersonaid($em->getRepository('PortadoresBundle:Persona')->find($npersonaid));
                    $entity->setNservicentroid($em->getRepository('PortadoresBundle:Servicentro')->find($nservicentroid));
                    $entity->setNtarjetaid($tarjeta);
                    $entity->setNvehiculoid($vehiculo);
                    $entity->setVisible(true);


                    $entity->setNcentrocostoid($em->getRepository('PortadoresBundle:CentroCosto')->find($ncentrocostoid));
                    if (isset($nfamiliaid) && !is_null($nfamiliaid))
                        $entity->setNfamiliaid($em->getRepository('PortadoresBundle:Familia')->find($nfamiliaid));


                    try {
                        $anticipo->setToRestore(true);
                        $em->persist($anticipo);
                        $em->persist($entity);
                    } catch (\Exception $ex) {
                        return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
                    }
                }
                $em->flush();

            });
        } catch (\Exception $e) {

            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => $e->getMessage()));
        }

        return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Liquidación(es) realizada(s) con éxito.'));
    }

    public function descartarAntAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $ent_ant_del = $em->getRepository('PortadoresBundle:AnticiposEliminados')->find($id);

        $ant_liq = $em->getRepository('PortadoresBundle:LiquidacionesEliminadas')->findby(array('anticipo' => $ent_ant_del->getAnticipo()));
        foreach ($ant_liq as $ent) {
            $em->remove($ent);
        }
        try {
            $em->remove($ent_ant_del);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Anticipo descartado con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function desmarcarAntAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $ent_ant_del = $em->getRepository('PortadoresBundle:AnticiposEliminados')->find($id);
        $ent_ant_del->setToRestore(false);
        try {
            $em->persist($ent_ant_del);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Anticipo desmarcado con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

}