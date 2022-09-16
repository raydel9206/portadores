<?php
/**
 * Created by PhpStorm.
 * User: yosley
 * Date: 07/10/2015
 * Time: 16:27
 */


namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\PortadoresBundle\Entity\Liquidacion;
use Geocuba\PortadoresBundle\Util\FechaUtil;
use Doctrine\Common\Util\Debug;
use Geocuba\PortadoresBundle\Entity\HistorialContableRecarga;
use Geocuba\PortadoresBundle\Entity\HistorialTarjeta;
use Geocuba\PortadoresBundle\Entity\Tarjeta;
use Geocuba\PortadoresBundle\Entity\TarjetaPersona;
use Geocuba\PortadoresBundle\Entity\TarjetaVehiculo;
use Geocuba\PortadoresBundle\Entity\SaldoTarjeta;
use Geocuba\PortadoresBundle\Entity\TarjetasCanceladas;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Geocuba\PortadoresBundle\Entity\Unidad;
use Geocuba\PortadoresBundle\Util\Datos;
use Geocuba\Utils\ViewActionTrait;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\PortadoresBundle\Util\Utiles;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TarjetaController extends Controller
{
    use ViewActionTrait;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $_numero = trim($request->get('numero'));
        $nunidadid = $request->get('nunidadid');
        $start = $request->get('start');
        $limit = $request->get('limit');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $entities = $em->getRepository('PortadoresBundle:Tarjeta')->buscarTarjera($_numero, $_unidades, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:Tarjeta')->buscarTarjera($_numero, $_unidades, $start, $limit, true);

        $_data = array();
        /** @var Tarjeta $entity */
        foreach ($entities as $entity) {
            /**@var TarjetaPersona $persona */
            $persona = $em->getRepository('PortadoresBundle:TarjetaPersona')->findOneBy(array('ntarjetaid' => $entity->getId()));
            /**@var TarjetaVehiculo $vehiculo */
            $vehiculo = $em->getRepository('PortadoresBundle:TarjetaVehiculo')->findOneBy(array('ntarjetaid' => $entity->getId()));
            $_data[] = array(
                'id' => $entity->getId(),
                'ncajaid' => $entity->getCajaid()->getId(),
                'ntipo_combustibleid' => $entity->getTipoCombustibleid()->getId(),
                'nombretipo_combustibleid' => $entity->getTipoCombustibleid()->getNombre(),
                'centrocostoid' => null === $entity->getCentrocosto() ? null : $entity->getCentrocosto()->getId(),
                'centrocostonombre' => null === $entity->getCentrocosto() ? null : $entity->getCentrocosto()->getNombre(),
                'nmonedaid' => $entity->getMonedaid()->getId(),
                'nombremonedaid' => $entity->getMonedaid()->getNombre(),
                'nunidadid' => $entity->getUnidadid()->getId(),
                'nombreunidadid' => $entity->getUnidadid()->getNombre(),
                'nro_tarjeta' => $entity->getNroTarjeta(),
                'importe' => (float)$entity->getImporte(),
                'importel' => round($entity->getImporte() / $entity->getTipoCombustibleid()->getPrecio(), 2),
                'fecha_registro' => $entity->getFechaRegistro()->format('d/m/Y'),
                'fecha_vencimieno' => $entity->getFechaVencimieno()->format('d/m/Y'),
                'fecha_baja' => null === $entity->getFechaBaja() ? null : $entity->getFechaBaja()->format('d/m/Y'),
                'causa_baja' => $entity->getCausaBaja(),
                'reserva' => $entity->getReserva(),
                'persona' => ($persona) ? $persona->getPersonaid()->getNombre() : '',
                'vehiculo' => ($vehiculo) ? $vehiculo->getVehiculoid()->getMatricula() : '',
                'exepcional' => $entity->getExepcional(),
                'estado' => intval($entity->getEstado())
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    public function loadComboAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $_numero = trim($request->get('numero'));
        $nunidadid = $request->get('unidadid');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $entities = $em->getRepository('PortadoresBundle:Tarjeta')->buscarTarjetaCombo($_numero, $_unidades);

        $_data = array();
        /** @var Tarjeta $entity */
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity['id'],
                'nro_tarjeta' => $entity['nro_tarjeta']
            );
        }

        return new JsonResponse(array('rows' => $_data));
    }

    public function loadTarjetaAnticipoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $_numero = trim($request->get('numero'));
        $nunidadid = $request->get('unidadid');
        $mes = $request->get('mes');
        $anno = $request->get('anno');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $entities = $em->getRepository('PortadoresBundle:Tarjeta')->buscarTarjetaAnticipoCombo($_unidades, $mes, $anno);

        $_data = array();
        /** @var Tarjeta $entity */
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity['id'],
                'nro_tarjeta' => $entity['nro_tarjeta'],
//                'nro_tarjeta' => strlen($entity['nro_tarjeta'])===16?substr($entity['nro_tarjeta'],9,15):$entity['nro_tarjeta'],
            );
        }

        return new JsonResponse(array('rows' => $_data));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loadbajasAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $nunidadid = $request->get('nunidadid');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $_unidadesstring = $this->unidadesToString($_unidades);


        $conn = $this->get('database_connection');

        $entities = $conn->fetchAll(" SELECT tarje.id as id,tarje.nro_tarjeta as nro_tarjeta,tarje.fecha_baja as fecha_baja,
  tarje.causa_baja as causa_baja,tarje.estado as estado,unidad.nombre as nombreunidadid,unidad.id as nunidadid
  FROM nomencladores.tarjeta as tarje
  join nomencladores.unidad as unidad on unidad.id=tarje.nunidadid
  where tarje.visible=false and  unidad.id in ($_unidadesstring) order by fecha_baja desc;");

        $_data = array();

        foreach ($entities as $entity) {

            $_data[] = array(
                'id' => $entity['id'],
                'nunidadid' => $entity['nunidadid'],
                'nombreunidadid' => $entity['nombreunidadid'],
                'nro_tarjeta' => $entity['nro_tarjeta'],
                'fecha_baja' => (is_null($entity['fecha_baja']) ? null : $entity['fecha_baja']),
                'causa_baja' => $entity['causa_baja'],
                'estado' => $entity['estado']
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => count($_data)));
    }

    /**
     * @param $id
     * @return array
     */
    public function recursivoAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $tree = array();

        $entitiess = $em->getRepository('PortadoresBundle:NestructuraUnidades')->findByPadreid($id);
        array_push($tree, $id);

        if ($entitiess) {
            foreach ($entitiess as $entity) {
                array_push($tree, $entity->getNunidadid()->getId());

                $this->recursivoAction($entity->getNunidadid()->getId());
            }
        }
        return $tree;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addAction(Request $request)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $nro_tarjeta = trim($request->get('nro_tarjeta'));
        $importe = (float)trim($request->get('importe'));

        $reserva = trim($request->get('reserva'));
        $nunidadid = trim($request->get('nunidadid'));
        $ntipo_combustibleid = trim($request->get('ntipo_combustibleid'));
        $nmonedaid = trim($request->get('nmonedaid'));
        $ncajaid = trim($request->get('ncajaid'));
        $fecha_registro = trim($request->get('fecha_registro'));
        $fecha_vencimieno = trim($request->get('fecha_vencimieno'));
        $exepcional = trim($request->get('exepcional'));
        $centrocostoid = trim($request->get('centrocostoid'));
        $es_saldo_inicial = trim($request->get('es_saldo_inicial'));

        $mes_abierto = $session->get('selected_month');
        $anno_abierto = $session->get('selected_year');


        if ($reserva) {
            $reservaA = 'true';
        } else {
            $reservaA = 'false';
        }

        if ($exepcional) {
            $exepcionalA = 'true';
        } else {
            $exepcionalA = 'false';
        }

        $entities = $em->getRepository('PortadoresBundle:Tarjeta')->findByNroTarjeta($nro_tarjeta);
        if ($entities) {
            if ($entities[0]->getVisible())
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Existe una Tarjeta con el mismo número.'));
            else {
                $entities[0]->setVisible(true);
                $em->persist($entities[0]);
                $em->flush();
                return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'La tarjeta fue registrada con éxito.'));
            }
        }

        $fecha_registroA = date_create_from_format('d/m/Y', $fecha_registro);
        $fecha_vencimienoA = date_create_from_format('d/m/Y', $fecha_vencimieno);

        $centrocosto = $em->getRepository('PortadoresBundle:CentroCosto')->find($centrocostoid);

        $entity = new Tarjeta();
        $entity->setNroTarjeta($nro_tarjeta);
        $entity->setImporte($importe);
        $entity->setReserva($reservaA);
        $entity->setFechaRegistro($fecha_registroA);
        $entity->setFechaVencimieno($fecha_vencimienoA);
        $entity->setUnidadid($em->getRepository('PortadoresBundle:Unidad')->find($nunidadid));
        $entity->setCajaid($em->getRepository('PortadoresBundle:Caja')->find($ncajaid));
        $entity->setMonedaid($em->getRepository('PortadoresBundle:Moneda')->find($nmonedaid));
        $entity->setTipoCombustibleid($em->getRepository('PortadoresBundle:TipoCombustible')->find($ntipo_combustibleid));
        $entity->setCentrocosto($centrocosto);
        $entity->setUnidadid($centrocosto->getNunidadid());
        $entity->setExepcional($exepcionalA);
        $entity->setEstado(0);
        $entity->setVisible(true);


        try {
            $em->persist($entity);
            $em->flush();
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }

        if ($es_saldo_inicial) {
            $entity_saldo = new SaldoTarjeta();
            $entity_saldo->setSaldoInicial($importe);
            $entity_saldo->setAnno($anno_abierto);
            $entity_saldo->setMes($mes_abierto);
            $entity_saldo->setIdTarjeta($em->getRepository('PortadoresBundle:Tarjeta')->find($entity));
            try {
                $em->persist($entity_saldo);
            } catch (\Exception $ex) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
            }
        }

        try {
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Tarjeta adicionada con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }


    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function modAction(Request $request)
    {
        $session = $request->getSession();
        $mes_abierto = $session->get('current_month');
        $anno_abierto = $session->get('current_year');
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $nro_tarjeta = trim($request->get('nro_tarjeta'));
        $importe = trim($request->get('importe'));
        $reserva = trim($request->get('reserva'));
        $nunidadid = trim($request->get('nunidadid'));
        $ntipo_combustibleid = trim($request->get('ntipo_combustibleid'));
        $nmonedaid = trim($request->get('nmonedaid'));
        $ncajaid = trim($request->get('ncajaid'));
        $fecha_registro = trim($request->get('fecha_registro'));
        $fecha_vencimieno = trim($request->get('fecha_vencimieno'));
        $exepcional = trim($request->get('exepcional'));
        $centrocostoid = trim($request->get('centrocostoid'));
        $es_saldo_inicial = trim($request->get('es_saldo_inicial'));


        if ($reserva) {
            $reservaA = 'true';
        } else {
            $reservaA = 'false';
        }

        if ($exepcional) {
            $exepcionalA = 'true';
        } else {
            $exepcionalA = 'false';
        }
        $entities = $em->getRepository('PortadoresBundle:Tarjeta')->findByNroTarjeta($nro_tarjeta);
        if ($entities)
            if ($entities[0]->getId() != $id) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Existe una tarjeta con ese número.'));
            }

        $centrocosto = $em->getRepository('PortadoresBundle:CentroCosto')->find($centrocostoid);

        $entity = $em->getRepository('PortadoresBundle:Tarjeta')->find($id);
        $fecha_registroA = date_create_from_format('d/m/Y', $fecha_registro);
        $fecha_vencimienoA = date_create_from_format('d/m/Y', $fecha_vencimieno);

        $entity->setNroTarjeta($nro_tarjeta);
        $entity->setImporte($importe);
        //$entity->setPin($pin);
        $entity->setReserva($reservaA);
        $entity->setFechaRegistro($fecha_registroA);
        $entity->setFechaVencimieno($fecha_vencimienoA);
        $entity->setUnidadid($em->getRepository('PortadoresBundle:Unidad')->find($nunidadid));
        $entity->setCajaid($em->getRepository('PortadoresBundle:Caja')->find($ncajaid));
        $entity->setMonedaid($em->getRepository('PortadoresBundle:Moneda')->find($nmonedaid));
        $entity->setTipoCombustibleid($em->getRepository('PortadoresBundle:TipoCombustible')->find($ntipo_combustibleid));
        $entity->setCentrocosto($centrocosto);
        $entity->setExepcional($exepcionalA);


        try {
            $em->persist($entity);
            $em->flush();
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }


        if ($es_saldo_inicial) {
            $entity_saldo = new SaldoTarjeta();
            $entity_saldo->setSaldoInicial($importe);
            $entity_saldo->setAnno($anno_abierto);
            $entity_saldo->setMes($mes_abierto);
            $entity_saldo->setIdTarjeta($em->getRepository('PortadoresBundle:Tarjeta')->find($entity));
            $em->persist($entity_saldo);
            try {
                $em->persist($entity_saldo);

            } catch (\Exception $ex) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
            }
        }


        try {

            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Tarjeta modificada con éxito.'));
            return $response;
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function darbajaAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $causa_baja = trim($request->get('causa_baja'));
        $fecha_baja = $request->get('fecha_baja');
        $fecha_bajaA = date_create_from_format('d/m/Y', $fecha_baja);
        $entity = $em->getRepository('PortadoresBundle:Tarjeta')->find($id);

        //Validar que no exista un Anticipo Abierto para esa Tarjeta
        $anticipo = $em->getRepository('PortadoresBundle:Anticipo')->findOneBy(array('tarjeta' => $entity, 'abierto' => true, 'visible' => true));

        if ($anticipo)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Existe un anticipo abierto para la tarjeta ' . $entity->getNroTarjeta()));

        $entity->setCausaBaja($causa_baja);
        $entity->setFechaBaja($fecha_bajaA);
        $entity->setVisible(false);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Tarjeta dada de baja con éxito.'));

        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Doctrine\DBAL\DBALException
     */
    public function canceltarjetaAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $nro_tarjeta = $request->get('nro_tarjeta');
        $tarjetaid = $request->get('tarjetaid');
        $nunidadid = $request->get('nunidadid');
        $fecha_cancel = $request->get('fecha_cancel');
        $motivo = $request->get('motivo');
        $accion = $request->get('accion');
        $fecha_cancel_format = date_create_from_format('d/m/Y', $fecha_cancel);

        $_user = $this->get('security.token_storage')->getToken()->getUser();

        $entity = $em->getRepository('PortadoresBundle:Tarjeta')->find($id);

        //Validar que no exista un Anticipo Abierto para esa Tarjeta
        $anticipo = $em->getRepository('PortadoresBundle:Anticipo')->findOneBy(array('tarjeta' => $entity, 'abierto' => true, 'visible' => true));

        if ($anticipo)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Existe un anticipo abierto para la tarjeta ' . $entity->getNroTarjeta()));

        if ($accion != 'quitar') {
            $entity_tarjetaV = $em->getRepository('PortadoresBundle:TarjetaVehiculo')->findOneBy(array(
                'ntarjetaid' => $entity,
                'visible' => true
            ));
            if ($entity_tarjetaV) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Antes de cancelar la tarjeta debe eliminar la asociacion con el medio técnico.'));
            }

            $entity_tarjetaP = $em->getRepository('PortadoresBundle:TarjetaPersona')->findOneBy(array(
                'ntarjetaid' => $entity,
                'visible' => true
            ));
            if ($entity_tarjetaP) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Antes de cancelar la tarjeta debe eliminar la asociacion  con persona.'));
            }

            $saldo_tarjeta = $entity->getImporte();
            $tipocombustible = $entity->getTipoCombustibleid()->getId();
            $unidad = $entity->getUnidadid()->getId();

            $entity_cuenta = $em->getRepository('PortadoresBundle:CuentaRecarga')->findOneBy(array(
                    'tipoCombustible' => $entity->getTipoCombustibleid(),
                    'unidad' => $nunidadid
                )
            );
            if ($entity_cuenta)
                $entity_cuenta->setMonto($entity_cuenta->getMonto() + $saldo_tarjeta);

            $cancel_entity = new TarjetasCanceladas();
            $cancel_entity->setNroTarjeta($nro_tarjeta);
            $cancel_entity->setMotivo($motivo);
            $cancel_entity->setFechaCancelacion($fecha_cancel_format);
            $cancel_entity->setNunidad($nunidadid);
            $cancel_entity->setUsuario($_user->getNombreCompleto());
            $cancel_entity->setSaldo($saldo_tarjeta);

            $conn = $this->get('database_connection');
            $conn->executeUpdate("UPDATE datos.historial_tarjeta
SET  existencia_importe=0, existencia_cantidad=0
WHERE id =(SELECT hist.id as id
       FROM  datos.historial_tarjeta as hist
       WHERE id in (SELECT id FROM  datos.historial_tarjeta where  tarjetaid='$tarjetaid' ORDER BY id DESC LIMIT 1 OFFSET 0)
       Group By hist.tarjetaid,hist.id);");

            $entity->setEstado(3);
            $entity->setImporte(0);
            try {
                $em->persist($entity);
                $em->persist($cancel_entity);
                $em->flush();

                return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Tarjeta Cancelada.'));

            } catch (\Exception $ex) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
            }
        } else {
            $entity_canceladas = $em->getRepository('PortadoresBundle:TarjetasCanceladas')->findByNroTarjeta($nro_tarjeta);
            foreach ($entity_canceladas as $cancelas) {
                $id_cancelada = $cancelas->getId();
            }
            $entity_cancel = $em->getRepository('PortadoresBundle:TarjetasCanceladas')->find($id_cancelada);
//            print_r($id_cancelada);die;
            $saldo_tarjeta = $entity_cancel->getSaldo();
            $unidad = $entity->getNunidadid()->getId();

            $entity_cuenta = $em->getRepository('PortadoresBundle:CuentaRecarga')->findOneByNunidad($unidad);

            // \Doctrine\Common\Util\Debug::dump(!is_bool(strpos(strtolower($entity->getTipoCombustibleid()->getNombre()), 'diesel')));die;
            /** @noinspection TypeUnsafeComparisonInspection */
            if (!is_bool(stripos($entity->getTipoCombustibleid()->getPortadorid()->getNombre(), 'diesel')) == true) {
//print_r('fdf');die;
                $entity_cuenta->setMonto($entity_cuenta->getMonto() + $saldo_tarjeta);
                $em->persist($entity_cuenta);
            } /** @noinspection TypeUnsafeComparisonInspection */ elseif (!is_bool(stripos($entity->getTipoCombustibleid()->getPortadorid()->getNombre(), 'gasolina')) == true) {
                //print_r('gasolina');die;
                $entity_cuenta->setMonto($entity_cuenta->getMonto() - $saldo_tarjeta);
                $em->persist($entity_cuenta);
            }

            $conn = $this->get('database_connection');
            $conn->executeUpdate("UPDATE datos.historial_tarjeta
SET  existencia_importe=$saldo_tarjeta, existencia_cantidad=0
WHERE id =(SELECT hist.id as id
       FROM  datos.historial_tarjeta as hist
       WHERE id in (SELECT id FROM  datos.historial_tarjeta where  tarjetaid='$tarjetaid' ORDER BY id DESC LIMIT 1 OFFSET 0)
       Group By hist.tarjetaid,hist.id);");


            $entity->setEstado(1);
            $entity->setImporte($saldo_tarjeta);
            try {
                $em->persist($entity);
//                $em->persist($cancel_entity);
                $em->flush();

                return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Tarjeta Cancelada.'));

            } catch (\Exception $ex) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
            }
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loadcanceladasAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $nunidadid = $request->get('nunidadid');

        $_data = array();

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $_unidadesstring = $this->unidadesToString($_unidades);

        $conn = $this->get('database_connection');

        $entities = $conn->fetchAll(" SELECT tar_cancel.id, fecha_cancelacion, nro_tarjeta, motivo, usuario, nunidad,unidad.nombre as nombre_unidad
  FROM datos.tarjetas_canceladas as tar_cancel
  join nomencladores.unidad as unidad on unidad.id=tar_cancel.nunidad
  where  unidad.id in ($_unidadesstring) order by fecha_cancelacion desc;");

        foreach ($entities as $entity) {

            $_data[] = array(

                'id' => $entity['id'],
                'fecha_cancelacion' => $entity['fecha_cancelacion'],
                'nro_tarjeta' => $entity['nro_tarjeta'],
                'motivo' => $entity['motivo'],
                'nunidadid' => $entity['nunidad'],
                'nombreunidadid' => $entity['nombre_unidad'],
                'usuario' => $entity['usuario'],

            );

        }


        return new JsonResponse(array('rows' => $_data, 'total' => count($_data)));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function recargaTarjetasAction(Request $request)
    {
        $message = '';
        $unidad = $request->get('nunidadid');
        $em = $this->getDoctrine()->getManager();
        $entrada = $request->get('entrada');
        $recarga = $request->get('recarga');
        $salida = $request->get('salida');


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

            $entity_cuenta = $em->getRepository('PortadoresBundle:CuentaRecarga')->findOneBy(array('unidad' => $unidad, 'moneda' => $entity->getMonedaid()->getId(), 'tipoCombustible' => $entity->getTipoCombustibleid()->getId()));
            $last_asignacion = $em->getRepository('PortadoresBundle:Asignacion')->findOneBy(array('tipoCombustible' => $entity->getTipoCombustibleid()->getId(), 'unidad' => $unidad, 'moneda' => $entity->getMonedaid()->getId(), 'visible' => true), array('fecha' => 'DESC'));

            if ($entity_cuenta){
                $entity_cuenta->setMonto($entity_cuenta->getMonto() - $importe_recarga);
                $em->persist($entity_cuenta);
            }else{
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'No tiene cuenta creada para esta moneda. Por favor revise'));
            }

            if ($last_asignacion) {
                if ((double)$last_asignacion->getDisponible() < (double)$importe_recarga / (double)$entity->getTipoCombustibleid()->getPrecio()) {
                    return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El importe de la recarga excede los '. round($last_asignacion->getDisponible()).'$ '.'equivalentes a '.round($last_asignacion->getDisponible() *(double)$entity->getTipoCombustibleid()->getPrecio(), 2).' de la disponibilidad.'));
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
            $entity->setEstado(1);
            $em->persist($entity);
            try {
                $em->flush();
            } catch (\Exception $e) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
            }
            $message = 'Tarjeta recargada con éxito.';


        }

        //entrada a caja
        if (isset($entrada, $recarga, $salida) && $entrada == 'true' && $recarga == 'false' && $salida == 'false') {
            $entity->setEstado(0);
            $em->persist($entity);
            try {
                $em->flush();
            } catch (\Exception $e) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
            }
            $message = 'Tarjeta entrada en caja con éxito.';
        }

        //entra y recargar
        if (isset($entrada, $recarga, $salida) && $entrada == 'true' && $recarga == 'true' && $salida == 'false') {
            $_fechaEmision = trim($request->get('fecha_recarga'));
            $_horaEmision = trim($request->get('hora_recarga'));
            $fecha_recarga = date_create_from_format('d/m/Y g:i A', $_fechaEmision . ' ' . $_horaEmision);

            $importe_recarga = $request->get('importe_recarga');
            $no_vale = $request->get('no_vale');
            $no_factura = $request->get('no_factura');

            $mes = $fecha_recarga->format('n');
            $anno = $fecha_recarga->format('Y');


            $entityValidar = $em->getRepository('PortadoresBundle:HistorialTarjeta')->buscarHistorialValidar($tarjetaid, $fecha_recarga, false);
            if (\count($entityValidar) > 0) {
                $historial = $entityValidar[\count($entityValidar) - 1];
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

            $entity_cuenta = $em->getRepository('PortadoresBundle:CuentaRecarga')->findOneBy(array('unidad' => $unidad, 'moneda' => $entity->getMonedaid()->getId(), 'tipoCombustible' => $entity->getTipoCombustibleid()->getId()));
            if ($entity_cuenta){
                $entity_cuenta->setMonto($entity_cuenta->getMonto() - $importe_recarga);
                $em->persist($entity_cuenta);
            }else{
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'No tiene cuenta creada para esta moneda. Por favor revise'));
            }

            $last_asignacion = $em->getRepository('PortadoresBundle:Asignacion')->findOneBy(array('tipoCombustible' => $entity->getTipoCombustibleid()->getId(), 'unidad' => $unidad, 'moneda' => $entity->getMonedaid()->getId(), 'visible' => true), array('fecha' => 'DESC'));

            if ($last_asignacion) {
                if ((double)$last_asignacion->getDisponible() < (double)$importe_recarga / (double)$entity->getTipoCombustibleid()->getPrecio()) {
                    return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El importe de la recarga excede los '. round($last_asignacion->getDisponible()).'$ '.'equivalentes a '.round($last_asignacion->getDisponible() *(double)$entity->getTipoCombustibleid()->getPrecio(),2).' de la disponibilidad.'));
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
            $entity->setEstado(1);
            $em->persist($entity);
            try {
                $em->flush();
            } catch (\Exception $e) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
            }
            $message = 'Tarjeta recargada con éxito.';


        }

        //entrada , recargar y salida
        if (isset($entrada, $recarga, $salida) && $entrada == 'true' && $recarga == 'true' && $salida == 'true') {
            $_fechaEmision = trim($request->get('fecha_recarga'));
            $_horaEmision = trim($request->get('hora_recarga'));
            $fecha_recarga = date_create_from_format('d/m/Y g:i A', $_fechaEmision . ' ' . $_horaEmision);

            $importe_recarga = $request->get('importe_recarga');
            $no_vale = $request->get('no_vale');
            $no_factura = $request->get('no_factura');

            $mes = $fecha_recarga->format('n');
            $anno = $fecha_recarga->format('Y');

            $entityValidar = $em->getRepository('PortadoresBundle:HistorialTarjeta')->buscarHistorialValidar($tarjetaid, $fecha_recarga, false);
            if (\count($entityValidar) > 0) {
                $historial = $entityValidar[\count($entityValidar) - 1];
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'No se pueden hacer recargas con fecha anterior a ' . $historial->getFecha()->format('d/m/Y g:i A') . ' para la tarjeta: ' . $entity->getNroTarjeta() . '.'));
            }

            $entityValidar = $em->getRepository('PortadoresBundle:HistorialTarjeta')->findBy(array(
                'tarjetaid' => $tarjetaid,
                'fecha' => $fecha_recarga,
                'nroVale' => $no_vale,
                'cancelado' => false));
            if (\count($entityValidar) > 0) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Existe una recarga con el mismo numero de vale e igual fecha en el sistema.'));
            }

            $entity_cuenta = $em->getRepository('PortadoresBundle:CuentaRecarga')->findOneBy(array('unidad' => $unidad, 'moneda' => $entity->getMonedaid()->getId(), 'tipoCombustible' => $entity->getTipoCombustibleid()->getId()));
            if ($entity_cuenta){
                $entity_cuenta->setMonto($entity_cuenta->getMonto() - $importe_recarga);
                $em->persist($entity_cuenta);
            }else{
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'No tiene cuenta creada para esta moneda. Por favor revise'));
            }
            $last_asignacion = $em->getRepository('PortadoresBundle:Asignacion')->findOneBy(array('tipoCombustible' => $entity->getTipoCombustibleid()->getId(), 'unidad' => $unidad, 'moneda' => $entity->getMonedaid()->getId(), 'visible' => true), array('fecha' => 'DESC'));

            if ($last_asignacion) {
                if ((double)$last_asignacion->getDisponible() < (double)$importe_recarga / (double)$entity->getTipoCombustibleid()->getPrecio()) {
                    return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El importe de la recarga excede los '. round($last_asignacion->getDisponible()).'$ '.'equivalentes a '.round($last_asignacion->getDisponible() *(double)$entity->getTipoCombustibleid()->getPrecio(),2).' de la disponibilidad.'));
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
            //cambio estado a Transito
            $entity->setEstado(2);
            $em->persist($entity);
            try {
                $em->flush();
            } catch (\Exception $e) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
            }
            $message = 'Tarjeta recargada con éxito.';


        }

        //salida
        if (isset($salida, $recarga, $entrada) && $salida == 'true' && $recarga == 'false' && $entrada == 'false') {
            $entity->setEstado(2);
            $em->persist($entity);
            try {
                $em->flush();
            } catch (\Exception $e) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
            }
            if ($recarga == 'false') {
                $message = 'Tarjeta sacada de caja con éxito.';
            }
        }

        return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => $message));
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function exportRecargaAction(Request $request)
    {

        $entrada = $request->get('entrada');
        $recarga = $request->get('recarga');
        $salida = $request->get('salida');
        $fechaActual = new \DateTime();
        $unidad = $request->get('nunidadid');
        $em = $this->getDoctrine()->getManager();
        $tarjetaid = $request->get('tarjetaid');
        $entity = $em->getRepository('PortadoresBundle:Tarjeta')->find($tarjetaid);
        $importe_recarga = $request->get('importe_recarga');

        $_html = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
        <html xmlns='http://www.w3.org/1999/xhtml'>
        <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <title>Comprobante</title>
        <style>
           table {
                border:0 solid;
                border-radius:0;
                font-family: 'Arial', serif;
                font-size: 12px;
                border-collapse: collapse;
            }
            td{
            }
        </style>
        </head>

        <body>
        <header>
            <img  src='../../assets/img/PNG/logo.png' height='60px' width='160px'>
        </header>
        <table cellspacing='0' cellpadding='5' border='1' width='100%'>
        <tr>
            <td style='text-align: center;'><strong>No.</strong></td>
            <td style='text-align: center;'><strong>Matrícula</strong></td>
            <td style='text-align: center;'><strong>Marca/Modelo</strong></td>
             <td style='text-align: center;'><strong>ASIGN(LT)</strong></td>
            <td style='text-align: center;'><strong>TOTAL($)</strong></td>
            <td style='text-align: center;'><strong>Norma</strong></td>
            <td style='text-align: center;'><strong>Kms</strong></td>
            <td style='text-align: center;'><strong>Indice</strong></td>
            <td style='text-align: center;'><strong>Lubricante</strong></td>
            <td style='text-align: center;'><strong>Líq. Freno</strong></td>
            </tr>
        <tr>
            <td style='text-align: center;'><strong>No.</strong></td>
            <td style='text-align: center;'><strong>Matrícula</strong></td>
            <td style='text-align: center;'><strong>Marca/Modelo</strong></td>
             <td style='text-align: center;'><strong>ASIGN(LT)</strong></td>
            <td style='text-align: center;'><strong>TOTAL($)</strong></td>
            <td style='text-align: center;'><strong>Norma</strong></td>
            <td style='text-align: center;'><strong>Kms</strong></td>
            <td style='text-align: center;'><strong>Indice</strong></td>
            <td style='text-align: center;'><strong>Lubricante</strong></td>
            <td style='text-align: center;'><strong>Líq. Freno</strong></td>
            </tr>";
        $_html .= "</table>
        </body>
        </html>";

        $spreadsheet = new Spreadsheet();

        $active_sheet = $spreadsheet->getActiveSheet();
        //Header
        $active_sheet->setCellValue('A1', 'Consecutivo');
        $active_sheet->setCellValue('B1', 'Código Moneda');
        $active_sheet->setCellValue('C1', 'Código Cuenta');
        $active_sheet->setCellValue('D1', 'Código Centro de costo');
        $active_sheet->setCellValue('E1', 'Código Acreedor\Deudor');
        $active_sheet->setCellValue('F1', 'Documento');
        $active_sheet->setCellValue('G1', 'Débito');
        $active_sheet->setCellValue('H1', 'Crédito');
        $active_sheet->setCellValue('I1', 'Fecha Valor');
        $active_sheet->setCellValue('J1', 'Fecha');
        $active_sheet->setCellValue('K1', 'Código Diario Contable');
        $active_sheet->setCellValue('L1', 'Descripción del Apunte');
        $active_sheet->setCellValue('M1', 'Título del Comprobante');
        $active_sheet->setCellValue('N1', 'Descripción general del comprobante');
        $pos = 2;

        $nombre = '';

        //entrada
        if (isset($entrada) && $entrada == 'true' && isset($recarga) && $recarga == 'false' && isset($salida) && $salida == 'false') {
            $importe_tarjeta = $entity->getImporte();
            if (isset($importe_recarga))
                $importe_tarjeta -= $importe_recarga;
            $active_sheet->setCellValue('A' . $pos->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT), '1');
            $active_sheet->setCellValue('A' . $pos, '1');
            $active_sheet->setCellValue('B' . $pos, $entity->getMonedaid()->getNombre());
            $active_sheet->setCellValue('C' . $pos, $entity->getMonedaid()->getNombre() == 'CUP' ? '102700' : '103700');
            $active_sheet->setCellValue('D' . $pos, '');
            $active_sheet->setCellValue('E' . $pos, '');
            $active_sheet->setCellValue('F' . $pos, $entity->getNroTarjeta());
            $active_sheet->setCellValue('G' . $pos, number_format($importe_tarjeta, 2));
            $active_sheet->setCellValue('H' . $pos, '0.00');
            $active_sheet->setCellValue('I' . $pos, '');
            $active_sheet->setCellValue('J' . $pos, '*' . $fechaActual->format('d/m/Y'));
            $active_sheet->setCellValue('K' . $pos, 'LG');
            $active_sheet->setCellValue('L' . $pos, '');
            $active_sheet->setCellValue('M' . $pos, 'RECARGA COMBUSTIBLE');
            $active_sheet->setCellValue('N' . $pos, 'CONTABILIZANDO VALE DE RECARGA');
            $pos++;

            $active_sheet->setCellValue('A' . $pos, '1');
            $active_sheet->setCellValue('B' . $pos, $entity->getMonedaid()->getNombre());
            $active_sheet->setCellValue('C' . $pos, $entity->getMonedaid()->getNombre() == 'CUP' ? '14602039005' : '14702039005');
            $active_sheet->setCellValue('D' . $pos, '');
            $active_sheet->setCellValue('E' . $pos, '');
            $active_sheet->setCellValue('F' . $pos, 'EN/CAJA');
            $active_sheet->setCellValue('G' . $pos, '0.00');
            $active_sheet->setCellValue('H' . $pos, number_format($importe_tarjeta, 2));
            $active_sheet->setCellValue('I' . $pos, '');
            $active_sheet->setCellValue('J' . $pos, '*' . $fechaActual->format('d/m/Y'));
            $active_sheet->setCellValue('K' . $pos, 'LG');
            $active_sheet->setCellValue('L' . $pos, '');
            $active_sheet->setCellValue('M' . $pos, 'RECARGA COMBUSTIBLE');
            $active_sheet->setCellValue('N' . $pos, 'CONTABILIZANDO VALE DE RECARGA');
            $pos++;

            $nombre .= ' entrada';
        }
//recarga
        if (isset($recarga) && $recarga == 'true' && isset($entrada) && $entrada == 'false' && isset($salida) && $salida == 'false') {
//            $cheque = $em->getRepository('PortadoresBundle:ChequeFincimex')->findBy(array('visible' => true, 'nunidadid' => $unidad), array('fechaRegistro' => 'DESC'));

            $conn = $this->get('database_connection');
//print_r($unidad);die;
//            $cheque = $conn->fetchAll("SELECT no_cheque,max(fecha_deposito)
//  FROM nomencladores.cheque_fincimex
//  where id in (SELECT id FROM nomencladores.cheque_fincimex  where  nunidadid= '$unidad' ORDER BY id DESC LIMIT 1 OFFSET 0)
//  group by nunidadid,no_cheque;");

//print_r($cheque);die;
            $active_sheet->setCellValue('A' . $pos, '1');
            $active_sheet->setCellValue('B' . $pos, $entity->getMonedaid()->getNombre());
            $active_sheet->setCellValue('C' . $pos, $entity->getMonedaid()->getNombre() == 'CUP' ? '102700' : '103700');
            $active_sheet->setCellValue('D' . $pos, '');
            $active_sheet->setCellValue('E' . $pos, '');
            $active_sheet->setCellValue('F' . $pos, $entity->getNroTarjeta());
            $active_sheet->setCellValue('G' . $pos, number_format($importe_recarga, 2));
            $active_sheet->setCellValue('H' . $pos, '0.00');
            $active_sheet->setCellValue('I' . $pos, '');
            $active_sheet->setCellValue('J' . $pos, '*' . $fechaActual->format('d/m/Y'));
            $active_sheet->setCellValue('K' . $pos, 'LG');
            $active_sheet->setCellValue('L' . $pos, '');
            $active_sheet->setCellValue('M' . $pos, 'RECARGA COMBUSTIBLE');
            $active_sheet->setCellValue('N' . $pos, 'CONTABILIZANDO VALE DE RECARGA');
            $pos++;

            $active_sheet->setCellValue('A' . $pos, '1');
            $active_sheet->setCellValue('B' . $pos, $entity->getMonedaid()->getNombre());
            $active_sheet->setCellValue('C' . $pos, $entity->getMonedaid()->getNombre() == 'CUP' ? '14602039005' : '14702039005');
            $active_sheet->setCellValue('D' . $pos, '');
            $active_sheet->setCellValue('E' . $pos, '');
//            $active_sheet->setCellValue('F' . $pos, $cheque[0]['no_cheque']);
            $active_sheet->setCellValue('G' . $pos, '0.00');
            $active_sheet->setCellValue('H' . $pos, number_format($importe_recarga, 2));
            $active_sheet->setCellValue('I' . $pos, '');
            $active_sheet->setCellValue('J' . $pos, '*' . $fechaActual->format('d/m/Y'));
            $active_sheet->setCellValue('K' . $pos, 'LG');
            $active_sheet->setCellValue('L' . $pos, '');
            $active_sheet->setCellValue('M' . $pos, 'RECARGA COMBUSTIBLE');
            $active_sheet->setCellValue('N' . $pos, 'CONTABILIZANDO VALE DE RECARGA');
            $pos++;

            $nombre .= ' recarga';
        }
//salida
        if (isset($salida) && $salida == 'true' && isset($entrada) && $entrada == 'false' && isset($recarga) && $recarga == 'true') {

            $active_sheet->setCellValue('A' . $pos, '2');
            $active_sheet->setCellValue('B' . $pos, $entity->getMonedaid()->getNombre());
            $active_sheet->setCellValue('C' . $pos, $entity->getMonedaid()->getNombre() == 'CUP' ? '14602039005' : '14702039005');
            $active_sheet->setCellValue('D' . $pos, '');
            $active_sheet->setCellValue('E' . $pos, '');
            $active_sheet->setCellValue('F' . $pos, 'SAL/CAJA');
            $active_sheet->setCellValue('G' . $pos, number_format($entity->getImporte(), 2));
            $active_sheet->setCellValue('H' . $pos, '0.00');
            $active_sheet->setCellValue('I' . $pos, '');
            $active_sheet->setCellValue('J' . $pos, '*' . $fechaActual->format('d/m/Y'));
            $active_sheet->setCellValue('K' . $pos, 'LG');
            $active_sheet->setCellValue('L' . $pos, '');
            $active_sheet->setCellValue('M' . $pos, 'SALIDA CAJA');
            $active_sheet->setCellValue('N' . $pos, 'CONTABILIZANDO SALIDA DE CAJA');
            $pos++;

            $active_sheet->setCellValue('A' . $pos, '2');
            $active_sheet->setCellValue('B' . $pos, $entity->getMonedaid()->getNombre());
            $active_sheet->setCellValue('C' . $pos, $entity->getMonedaid()->getNombre() == 'CUP' ? '102700' : '103700');
            $active_sheet->setCellValue('D' . $pos, '');
            $active_sheet->setCellValue('E' . $pos, '');
            $active_sheet->setCellValue('F' . $pos, $entity->getNroTarjeta());
            $active_sheet->setCellValue('G' . $pos, '0.00');
            $active_sheet->setCellValue('H' . $pos, number_format($entity->getImporte(), 2));
            $active_sheet->setCellValue('I' . $pos, '');
            $active_sheet->setCellValue('J' . $pos, '*' . $fechaActual->format('d/m/Y'));
            $active_sheet->setCellValue('K' . $pos, 'LG');
            $active_sheet->setCellValue('L' . $pos, '');
            $active_sheet->setCellValue('M' . $pos, 'SALIDA CAJA');
            $active_sheet->setCellValue('N' . $pos, 'CONTABILIZANDO SALIDA DE CAJA');
            $pos++;

            $nombre .= ' salida';
        }

        $writer = new Xls($spreadsheet);

        $response = new StreamedResponse(
            function () use ($writer) {

                $writer->save('php://output');
            }, \Symfony\Component\HttpFoundation\Response::HTTP_OK, ['Content-Type' => 'application/vnd.ms-excel', 'Pragma' => 'public', 'Cache-Control' => 'maxage=0']);


        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'Comprobante.xls'));
        return $response;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function corregirAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $_id = $request->get('id');
        $nunidadid = trim($request->get('nunidadid'));
        $date = $request->get('fecha');
        $hour = $request->get('hora');
        $conn = $this->get('database_connection');
        $fechaObj = date_create_from_format('d/m/Y g:i A', $date . ' ' . $hour);
        $fecha = $fechaObj->format('Y-m-d g:i A');

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
        $tarjeta->setEstado(0);
        $em->persist($tarjeta);

        if ($entities_historiales) {
            foreach ($entities_historiales as $historial) {
                if (!is_null($historial['liquidacionid'])) {
                    //Eliminando Liquidaciones
                    /** @var Liquidacion $entiti_liquida */
                    $entiti_liquida = $em->getRepository('PortadoresBundle:Liquidacion')->find($historial['liquidacionid']);
                    $em->remove($entiti_liquida);

                } else {
                    //Eliminando Historial de Recarga
                    $entities_Historial_recarga = $em->getRepository('PortadoresBundle:HistorialContableRecarga')->find($historial['racargaid']);
                    $em->remove($entities_Historial_recarga);
                }
                //Eliminando Historial de Tarjeta
                $enti_his = $em->getRepository('PortadoresBundle:HistorialTarjeta')->find($historial['id']);
                $em->remove($enti_his);
            }
        }
        try {
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Tarjeta corregida con éxito'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loadTarjetaVehiculoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $_tarjeta = trim($request->get('tarjeta'));

        $start = $request->get('start');
        $limit = $request->get('limit');

        $entities = $em->getRepository('PortadoresBundle:TarjetaVehiculo')->buscarXTarjeta($_tarjeta, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:TarjetaVehiculo')->buscarXTarjeta($_tarjeta, $start, $limit, true);

        $_data = array();
        /** @var TarjetaVehiculo $entity */
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'vehiculoid' => $entity->getVehiculoid()->getId(),
                'vehiculo' => $entity->getVehiculoid()->getMatricula(),
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addTarjetaVehiculoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $tarjetaid = $request->get('tarjetaid');
        $vehiculoid = $request->get('vehiculoid');

        $entityValidar = $em->getRepository('PortadoresBundle:TarjetaVehiculo')->findBy(array(
            'ntarjetaid' => $tarjetaid,
            'nvehiculoid' => $vehiculoid,
            'visible' => true
        ));

        //Civil
        //  if (\count($entityValidar) > 0) {
        //     return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El vehículo ya tiene la tarjeta ' . $entityValidar[0]->getTarjetaid()->getNroTarjeta() . ' asignada.'));
        //}

        //FAR
//        if (\count($entityValidar) > 1) {
//            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El vehículo ya tiene las tarjetas '. $entityValidar[0]->getTarjetaid()->getNroTarjeta().' y '.$entityValidar[1]->getTarjetaid()->getNroTarjeta().'   asignadas.'));
//        }

        $tarjeta = $em->getRepository('PortadoresBundle:Tarjeta')->find($tarjetaid);
        $vehiculo = $em->getRepository('PortadoresBundle:Vehiculo')->find($vehiculoid);

        $entity = new TarjetaVehiculo();
        $entity->setTarjetaid($tarjeta);
        $entity->setVehiculoid($vehiculo);
        $entity->setVisible(true);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Tarjeta asignada con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function delTarjetaVehiculoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $entity = $em->getRepository('PortadoresBundle:TarjetaVehiculo')->find($id);
        $entity->setVisible(false);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Asignación eliminada con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loadTarjetaPersonaAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $_tarjeta = trim($request->get('tarjeta'));

        $start = $request->get('start');
        $limit = $request->get('limit');

        $entities = $em->getRepository('PortadoresBundle:TarjetaPersona')->buscarXTarjeta($_tarjeta, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:TarjetaPersona')->buscarXTarjeta($_tarjeta, $start, $limit, true);

        $_data = array();
        /** @var TarjetaPersona $entity */
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'personaid' => $entity->getPersonaid()->getId(),
                'persona' => $entity->getPersonaid()->getNombre(),
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addTarjetaPersonaAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $tarjetaid = $request->get('tarjetaid');
        $personaid = $request->get('personaid');

        $entityValidar = $em->getRepository('PortadoresBundle:TarjetaPersona')->findBy(array(
//            'ntarjetaid' => $tarjetaid,
            'npersonaid' => $personaid,
            'visible' => true
        ));

//        //Civil
//        if (count($entityValidar) > 0)
//            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'La persona seleccionada tiene la tarjeta ' . $entityValidar[0]->getTarjetaid()->getNroTarjeta() . ' asignada.'));

//        FAR
        //if (count($entityValidar) > 1)
        //    return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'La persona seleccionada tiene las tarjetas '. $entityValidar[0]->getTarjetaid()->getNroTarjeta().' y '.$entityValidar[1]->getTarjetaid()->getNroTarjeta().' asignada.'));

        $tarjeta = $em->getRepository('PortadoresBundle:Tarjeta')->find($tarjetaid);
        $persona = $em->getRepository('PortadoresBundle:Persona')->find($personaid);

        $entity = new TarjetaPersona();
        $entity->setTarjetaid($tarjeta);
        $entity->setPersonaid($persona);
        $entity->setVisible(true);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Tarjeta asignada con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function delTarjetaPersonaAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');

        $entity = $em->getRepository('PortadoresBundle:TarjetaPersona')->find($id);

        $entity->setVisible(false);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Asignación eliminada con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function historialtarjetaAction(Request $request)
    {
        $tarjetaid = $request->get('tarjetaid');
        $desde_date = $request->get('desde');
        $fecha_desde = date_create_from_format('Y-m-d H:i:s', $desde_date . ' 00:00:00');
        $inicio = "'" . $fecha_desde->format('Y-m-d H:i:s') . "'";
        $hasta_date = $request->get('hasta');
        $fecha_hasta = date_create_from_format('Y-m-d H:i:s', $hasta_date . ' 23:59:59');
        $fin = "'" . $fecha_hasta->format('Y-m-d H:i:s') . "'";

        $historial = array();

        $conn = $this->get('database_connection');
        $entities_exis = $conn->fetchAll("SELECT  id_tarjeta, nro_vale, nro_factura, hr.fecha , monto_recarga, monto_restante, nombre_usuario,tarjeta.nro_tarjeta
            FROM datos.historial_contable_recarga hr
            join nomencladores.tarjeta as tarjeta on tarjeta.id=id_tarjeta
            join datos.historial_tarjeta as ht on ht.racargaid=hr.id
            where hr.fecha BETWEEN $inicio AND $fin
            AND id_tarjeta='$tarjetaid' ORDER BY hr.fecha DESC;");

        if ($entities_exis) {
            foreach ($entities_exis as $entity) {
                $fecha = new \DateTime($entity['fecha']);
                $historial[] = array(
                    'nro_factura' => $entity['nro_factura'],
                    'nro_vale' => $entity['nro_vale'],
                    'monto_recarga' => $entity['monto_recarga'],
                    'fecha' => $fecha->format('d/m/Y g:i A'),
                    'Nro_tarjeta' => $entity['nro_tarjeta'],
                    'tarjetaid' => $entity['id_tarjeta']
                );
            }
        }

        return new JsonResponse(array('rows' => $historial, 'total' => \count($historial)));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function eliminarRecargaAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $monto = $request->get('monto_recarga');
        $monto_new = $request->get('monto_new');
        $tarjetaid = $request->get('tarjetaid');
        $accion = $request->get('accion');
        $fecha = $request->get('fecha_recarga');
        $fecha_recarga = date_create_from_format('Y-m-d', $fecha);
        $saldo_a_rebajar = $monto - $monto_new;
        $entity_tarjeta = $em->getRepository('PortadoresBundle:Tarjeta')->find($tarjetaid);

        $unidad = $entity_tarjeta->getNunidadid()->getId();

        if ($accion == 'del') {

            $saldo_nuevo = $entity_tarjeta->getImporte() - $monto;

            if (\is_int(strpos($saldo_nuevo, "-"))) {
                return new JsonResponse(array('success' => true, 'cls' => 'warning', 'message' => 'La Recarga seleccionda no se Puede Eliminar porque queda saldo Negativo.Revice liquidaciones de la misma'));

            } else {
                $entity_tarjeta->setImporte($saldo_nuevo);

                $entity = $em->getRepository('PortadoresBundle:HistorialContableRecarga')->find($id);
                $em->remove($entity);


                $entity_cuenta = $em->getRepository('PortadoresBundle:CuentaRecarga')->findOneByNunidad($unidad);

                if (!\is_bool(stripos($entity_tarjeta->getTipoCombustibleid()->getPortadorid()->getNombre(), 'diesel'))) {
                    $entity_cuenta->setMonto($entity_cuenta->getMonto() + $monto);
                    $em->persist($entity_cuenta);
                }

                if (!\is_bool(stripos($entity_tarjeta->getTipoCombustibleid()->getPortadorid()->getNombre(), 'gasolina'))) {
                    $entity_cuenta->setMonto($entity_cuenta->getMonto() + $monto);
                    $em->persist($entity_cuenta);
                }
                try {
                    $em->persist($entity_tarjeta);
                    $em->flush();
                    return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Recarga Eliminada.'));
                } catch (\Exception $ex) {
                    return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
                }

            }
        } elseif ($accion == 'mod') {

            $saldo_nuevo_Mod = $entity_tarjeta->getImporte() - $saldo_a_rebajar;

            if (\is_int(strpos($saldo_nuevo_Mod, "-"))) {
                return new JsonResponse(array('success' => true, 'cls' => 'warning', 'message' => 'La Recarga seleccionda no se Puede Eliminar porque queda saldo Negativo.Revice liquidaciones de la misma'));


            } else {
                $entity_tarjeta->setImporte($saldo_nuevo_Mod);

                $entity = $em->getRepository('PortadoresBundle:HistorialContableRecarga')->find($id);
                $entity->setMontoRecarga($monto_new);
                $entity->setFecha($fecha_recarga);

                $entity_cuenta = $em->getRepository('PortadoresBundle:CuentaRecarga')->findOneByNunidad($unidad);

                if (!\is_bool(stripos($entity_tarjeta->getTipoCombustibleid()->getPortadorid()->getNombre(), 'diesel'))) {

                    $entity_cuenta->setMonto($entity_cuenta->getMonto() + $saldo_a_rebajar);
                    $em->persist($entity_cuenta);
                }

                if (!\is_bool(stripos($entity_tarjeta->getTipoCombustibleid()->getPortadorid()->getNombre(), 'gasolina'))) {

                    $entity_cuenta->setMonto($entity_cuenta->getMonto() + $saldo_a_rebajar);
                    $em->persist($entity_cuenta);
                }

                try {
                    $em->persist($entity_tarjeta);
                    $em->persist($entity);
                    $em->flush();

                    return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Recarga Eliminada.'));

                } catch (\Exception $ex) {
                    return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
                }
            }
        }
    }

    private function unidadesToString($_unidades)
    {
        $_string_unidades = "'" . $_unidades[0] . "'";
        for ($i = 1; $i < count($_unidades); $i++) {
            $_string_unidades .= ",'" . $_unidades[$i] . "'";
        }
        return $_string_unidades;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loadDispFincimexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $_data = array();
        $nunidadid = $request->get('unidadid');
        $tipoCombustible = $em->getRepository('PortadoresBundle:TipoCombustible')->findByVisible(true);
        foreach ($tipoCombustible as $item) {
            $_data[] = array(
                'id' => $item->getId(),
                'combustible' => $item->getNombre(),
                'disponible' => Datos::getPlanDisponibleFincimex($em, $nunidadid, $item->getId(), '')
            );
        }
        return new JsonResponse(array('rows' => $_data));
    }
}