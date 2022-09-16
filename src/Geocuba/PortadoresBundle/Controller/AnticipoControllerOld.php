<?php
/**
 * Created by PhpStorm.
 * User: yosley
 * Date: 05/10/2015
 * Time: 14:12
 */


namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\PortadoresBundle\Entity\ActaRespMaterial;
use Geocuba\PortadoresBundle\Entity\CuentaGasto;
use Geocuba\PortadoresBundle\Entity\HistorialTarjeta;
use Geocuba\PortadoresBundle\Entity\Liquidacion;
use Geocuba\PortadoresBundle\Entity\Anticipo;
use Geocuba\PortadoresBundle\Entity\Consecutivos;
use Doctrine\Common\CommonException;
use Doctrine\Common\Util\Debug;
use Geocuba\PortadoresBundle\Entity\Tarjeta;
use Geocuba\PortadoresBundle\Entity\Vehiculo;
use Geocuba\PortadoresBundle\Util\FechaUtil;
use Geocuba\PortadoresBundle\Util\MonedaEnum;
use Geocuba\Utils\Functions;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Datos;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Security\Acl\Exception\Exception;
use Geocuba\PortadoresBundle\Util\DocumentosEnum;
use Symfony\Component\HttpKernel\Exception\{
    HttpException, NotFoundHttpException
};
use Geocuba\PortadoresBundle\Util\Utiles;


class AnticipoController extends Controller
{
    use ViewActionTrait;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loadAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();

        $mes = $request->get('mes');
        $anno = $request->get('anno');

        if (\is_null($mes) && \is_null($anno)) {
            $fecha = new \DateTime();
            $anno = $fecha->format('Y');
            $mes = $fecha->format('n');
        }

        $fechaDesde = $anno . '-' . $mes . '-' . '1';
        $fechaHasta = FechaUtil::getUltimoDiaMes($mes, $anno);

        $_noVale = $request->get('vale');
        $_matricula = $request->get('vehiculo');
        $_nroTarjeta = $request->get('tarjeta');
        $nunidadid = $request->get('unidadid');
        $_estado = $request->get('estado');

        $start = $request->get('start');
        $limit = $request->get('limit');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $entities = $em->getRepository('PortadoresBundle:Anticipo')->buscarAnticipo($_noVale, $_matricula, $_nroTarjeta, $_estado, $_unidades, $fechaDesde, $fechaHasta, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:Anticipo')->buscarAnticipo($_noVale, $_matricula, $_nroTarjeta, $_estado, $_unidades, $fechaDesde, $fechaHasta, $start, $limit, true);

        $_data = array();
        /** @var Anticipo $entity */
        foreach ($entities as $entity) {
            $id_tarjeta = $entity->getTarjeta()->getId();
            $tarjeta = $em->getRepository('PortadoresBundle:Tarjeta')->find($id_tarjeta);
            $importe_tarjeta = $tarjeta->getImporte();
            $_data[] = array(
                'id' => $entity->getId(),
                'fecha' => date_format($entity->getFecha(), 'd/m/Y'),
                'hora' => date_format($entity->getFecha(), 'g:i A'),
                'fecha_anticipo' => date_format($entity->getFecha(), 'd/m/Y g:i A'),
                'no_vale' => $entity->getNoVale(),
//                '' => $entity->g,
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
                'excepcional' => $entity->getExcepcional(),
                'transito_plugin' => $entity->getTransito() ? 'SI' : 'NO',
                'terceros_plugin' => $entity->getTerceros() ? 'SI' : 'NO',
                'tipo_combustible_id' => $entity->getTarjeta()->getTipoCombustibleid()->getId(),
                'centrocostoid' => $entity->getTarjeta()->getCentrocosto()->getId(),
                'actividadid' => $entity->getVehiculo()->getActividad()->getId(),
                'excepcional_plugin' => $entity->getExcepcional()? 'SI' : 'NO',
                'motivo'=> $entity->getExcepcional()?$entity->getMotivo():''
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loadVehiculoAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $nunidadid = $request->get('unidadid');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $qb = $em->createQueryBuilder();
        $entities = $qb->select('nvehiculo')
            ->from('PortadoresBundle:Vehiculo', 'nvehiculo')
            ->where($qb->expr()->in('nvehiculo.nunidadid', ':unidad'))
            ->andWhere('nvehiculo.visible = true')
            ->orderBy('nvehiculo.matricula')
            ->setparameter('unidad', $_unidades)
            ->getQuery()->getResult();

        $_data = array();
        foreach ($entities as $entity) {

//            $persona = '';
//            foreach ($entity->getPersonas() as $p) {
//                if ($p->getVisible())
//                    $persona = $p->getIdpersona()->getId();
//            }

            $tarjetas = [];
            foreach ($entity->getTarjetas() as $t) {
                if ($t->getVisible()) {
                    $tarjetas[] = $t->getTarjetaid()->getId();
//                    foreach ($t->getTarjetaid()->getNtarjetanpersonaid() as $tarjetapersona)
//                        if ($tarjetapersona->getVisible())
//                            $persona = $tarjetapersona->getPersonaid()->getId();
                }
            }

            $_data[] = array(
                'id' => $entity->getId(),
                'matricula' => $entity->getMatricula(),
                'tipo_combustibleid' => $entity->getNTipoCombustibleid()->getId(),
//                'persona' => $persona,
                'tarjetas' => $tarjetas
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => count($_data)));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loadTarjetaAnticipoAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();

        $tipo_combustible_id = $request->get('tipo_combustible_id');
        $nunidadid = $request->get('unidadid');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $qb = $em->createQueryBuilder();
        $entities = $qb->select('ntarjeta')
            ->from('PortadoresBundle:Tarjeta', 'ntarjeta')
//            ->innerJoin('ntarjeta.ntipoCombustibleid', 'ntarjetanvehiculoid')
//            ->innerJoin('ntarjetanvehiculoid.nvehiculoid', 'nvehiculoid')
            ->where($qb->expr()->in('ntarjeta.nunidadid', ':unidades'))
            ->setParameter('unidades', $_unidades)
            ->andWhere($qb->expr()->eq('ntarjeta.ntipoCombustibleid', ':vehiculo'))
            ->setParameter('vehiculo', $tipo_combustible_id)
            ->andWhere('ntarjeta.visible = true')
            ->orderBy('ntarjeta.nroTarjeta')
            ->getQuery()->getResult();

//        $qb = $em->createQueryBuilder();
//        $entities = $qb->select('ntarjeta')
//            ->from('PortadoresBundle:Tarjeta', 'ntarjeta')
//            ->innerJoin('ntarjeta.ntarjetanvehiculoid', 'ntarjetanvehiculoid')
//            ->innerJoin('ntarjetanvehiculoid.nvehiculoid', 'nvehiculoid')
//            ->where($qb->expr()->in('ntarjeta.nunidadid', $_unidades))
//            ->andWhere($qb->expr()->eq('nvehiculoid.id', ':vehiculo'))
//            ->setParameter('vehiculo', $vehiculoid)
//            ->andWhere('ntarjetanvehiculoid.visible = true')
//            ->orderBy('ntarjeta.nroTarjeta')
//            ->getQue


        $_data = array();
        /** @var Tarjeta $entity */
        foreach ($entities as $entity) {
            $personasObject = $em->getRepository('PortadoresBundle:TarjetaPersona')->findBy(array('ntarjetaid'=> $entity->getId(), 'visible' => true));
            $personas = array_map(function ($item) {
                return $item->getPersonaid()->getId();
            }, $personasObject);

            $_data[] = array(
                'id' => $entity->getId(),
                'ncajaid' => $entity->getCajaid()->getId(),
                'ntipo_combustibleid' => $entity->getTipoCombustibleid()->getId(),
                'nombretipo_combustibleid' => $entity->getTipoCombustibleid()->getNombre(),
                'preciotipo_combustibleid' => $entity->getTipoCombustibleid()->getPrecio(),
                'nmonedaid' => $entity->getMonedaid()->getId(),
                'nunidadid' => $entity->getUnidadid()->getId(),
                'nombreunidadid' => $entity->getUnidadid()->getNombre(),
                'nro_tarjeta' => $entity->getNroTarjeta(),
                'importe' => $entity->getImporte(),
                'fecha_registro' => $entity->getFechaRegistro()->format('d/m/Y'),
                'fecha_vencimieno' => $entity->getFechaVencimieno()->format('d/m/Y'),
                'fecha_baja' => null === $entity->getFechaBaja() ? null : $entity->getFechaBaja()->format('d/m/Y'),
                'causa_baja' => $entity->getCausaBaja(),
                'reserva' => $entity->getReserva(),
                'exepcional' => $entity->getExepcional(),
                'personas' => $personas
            );
        }


        return new JsonResponse(array('rows' => $_data, 'total' => count($_data)));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loadTrabajoAnticipoAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $vehiculoid = $request->get('vehiculoid');
        $tarjetaid = $request->get('tarjetaid');
        /** @var Vehiculo $vehiculo */
        $vehiculo = $em->getRepository('PortadoresBundle:Vehiculo')->find($vehiculoid);
        $vehiculo_tipo_comb = $vehiculo->getNTipoCombustibleid()->getId();

        /** @var Tarjeta $tarjeta */
        $tarjeta = $em->getRepository('PortadoresBundle:Tarjeta')->find($tarjetaid);

        $entities = array();

        if ($tarjeta) {
            $tarjeta_moneda = $tarjeta->getMonedaid()->getId();

            $qb = $em->createQueryBuilder();
            $entities = $qb->select('t')
                ->from('PortadoresBundle:Trabajo', 't')
                ->from('PortadoresBundle:TrabajoAsignacionCombustible', 'a')
                ->Where('t.visible = true')
                ->andWhere('t.id = a.trabajoid')
                ->andWhere('t.id = a.trabajoid')
                ->andWhere('a.tipoCombustible = ' . "'$vehiculo_tipo_comb'")
                ->andWhere('a.moneda = ' . "'$tarjeta_moneda'")
                ->orderBy('t.nombre', 'ASC')
                ->getQuery()->getResult();

        }

        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'nombre' => $entity->getCodigo() . '-' . $entity->getNombre(),
                'fecha_ini' => date_format($entity->getFechaInicio(), 'd/m/Y'),
                'fecha_fin' => date_format($entity->getFechaFin(), 'd/m/Y')
            );
        }
        return new JsonResponse(array('rows' => $_data, 'total' => \count($_data)));
    }

//    /**
//     * @param Request $request
//     * @return JsonResponse
//     */
//    public function loadCentroCostoAnticipoAction(Request $request): JsonResponse
//    {
//        $em = $this->getDoctrine()->getManager();
//
//        $qb = $em->createQueryBuilder();
//        $entities = $qb->select('ncentrocosto')
//            ->from('PortadoresBundle:CentroCosto', 'ncentrocosto')
//            ->andWhere('ncentrocosto.visible = true')
//            ->getQuery()->getResult();
//
//        $_data = array();
//        foreach ($entities as $entity) {
//            $_data[] = array(
//                'id' => $entity->getId(),
//                'nombre' => $entity->getNombre()
//            );
//        }
//
//        return new JsonResponse(array('rows' => $_data, 'total' => \count($_data)));
//    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getCantLtsAnticipoAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $id = trim($request->get('id'));
        $entity = $em->getRepository('PortadoresBundle:Tarjeta')->find($id);

        if (!is_null($entity)) {
            $var = array(
                'importe' => $entity->getImporte(),
                'ntipo_combustibleid' => $entity->getTipoCombustibleid()->getId(),
                'ntipo_combustibleidprecio' => $entity->getTipoCombustibleid()->getPrecio(),

            );
        } else {
            $var = array(
                'importe' => 0,
                'ntipo_combustibleid' => -1,
                'ntipo_combustibleidprecio' => 0,
            );
        }

        return new JsonResponse(array('rows' => $var, 'total' => count($var)));
    }

    public function loadLiquidacionesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $start = $request->get('start');
        $limit = $request->get('limit');

        $mes = $request->get('mes');
        $anno = $request->get('anno');

        $nunidadid = $request->get('unidadid');
        $nro_vale = $request->get('nro_vale');
        $tarjetaid = $request->get('tarjeta');
        $anticipoid = $request->get('anticipoid');

        $fechaDesde = $mes ? $anno . '-' . $mes . '-' . '1' : '';
        $fechaHasta = $mes ? FechaUtil::getUltimoDiaMes($mes, $anno) : '';

        $total = 0;
        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);
        if (!$anticipoid) {
            $entities = $em->getRepository('PortadoresBundle:Liquidacion')->buscarEntregas($_unidades, $nro_vale, $tarjetaid, $fechaDesde, $fechaHasta, $start, $limit);
            $total = $em->getRepository('PortadoresBundle:Liquidacion')->buscarEntregas($_unidades, $nro_vale, $tarjetaid, $fechaDesde, $fechaHasta, $start, $limit, true);
        } else {
            $entitiesLiquidaciones = $em->getRepository('PortadoresBundle:Liquidacion')->buscarLiquidacion($_unidades, $nro_vale, $tarjetaid, $anticipoid, $fechaDesde, $fechaHasta);
            $anticipo = $em->getRepository('PortadoresBundle:Anticipo')->find($anticipoid);
            $entities = array_merge($entitiesLiquidaciones,
                $em->getRepository('PortadoresBundle:Liquidacion')->buscarLiquidacion($_unidades, $nro_vale, $tarjetaid, null, $anticipo->getFecha()->format('Y-m-d H:i:s'), $fechaHasta));
        }

        $_data = array();
        foreach ($entities as $entity) {
            $historial = $em->getRepository('PortadoresBundle:HistorialTarjeta')->findOneBy(array('liquidacionid' => $entity->getId()));
            $_data[] = array(
                'liquidacionid' => $entity->getId(),
                'nvehiculoid' => $entity->getNvehiculoid()->getId(),
                'ntarjetaid' => $entity->getNtarjetaid()->getId(),
                'ntarjetaidnro' => $entity->getNtarjetaid()->getNroTarjeta(),
                'npersonaid' => $entity->getNpersonaid()->getId(),
                'nombrepersonaid' => $entity->getNpersonaid()->getNombre(),
//                'nactividadid' => $entity->getNactividadid() ? $entity->getNactividadid()->getId() : '',
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
                'historial' => $historial !== null,
                'abierto' => $entity->getAnticipo()->getAbierto()
            );
        }

        return new JsonResponse(array('success' => true, 'rows' => $_data, 'total' => $total));
    }

    public function loadAnticipoLiquidacionesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $anticipoid = $request->get('id');
        $entities = $em->getRepository('PortadoresBundle:Liquidacion')->findByAnticipo($anticipoid);
        $_data = array();
        foreach ($entities as $entity) {
            $historial = $em->getRepository('PortadoresBundle:HistorialTarjeta')->findOneBy(array('liquidacionid' => $entity->getId()));
            $_data[] = array(
                'liquidacionid' => $entity->getId(),
                'vehiculo' => $entity->getNvehiculoid()->getMatricula(),
                'nvehiculoid' => $entity->getNvehiculoid()->getId(),
                'ntarjetaid' => $entity->getNtarjetaid()->getId(),
                'ntarjetaidnro' => $entity->getNtarjetaid()->getNroTarjeta(),
                'tipo_combustible_id' => $entity->getNtarjetaid()->getTipoCombustibleid()->getId(),
                'tipo_combustible' => $entity->getNtarjetaid()->getTipoCombustibleid()->getNombre(),
                'npersonaid' => $entity->getNpersonaid()->getId(),
                'ncentrocostoid' => $entity->getNcentrocostoid() ? $entity->getNcentrocostoid()->getId() : '',
                'nombrepersonaid' => $entity->getNpersonaid()->getNombre(),
                'nsubactividadid' => $entity->getNactividadid() ? $entity->getNactividadid()->getId() : null,
                'nservicentroid' => $entity->getNservicentroid()->getId(),
                'nservicentroid_nombre' => $entity->getNservicentroid()->getNombre(),
                'nro_vale' => $entity->getNroVale(),
                'importe' => $entity->getImporte(),
                'importe_inicial' => $entity->getImporteInicial(),
                'importe_final' => $entity->getImporteFinal(),
                'cant_litros' => $entity->getCantLitros(),
                'fecha_servicio' => $entity->getFechaVale()->format('d/m/Y H:i:s'),
//                'fecha_servicio' => $entity->getFechaVale()->format('d/m/Y g:i A'),
                'fecha_vale' => $entity->getFechaVale()->format('d/m/Y'),
                'hora_vale' => $entity->getFechaVale()->format('g:i A'),
//                'hora_vale' => $entity->getFechaVale(),
//                'hora_vale' => $entity->getFechaVale()->format('h:m:s'),
                'fecha_registro' => $entity->getFechaRegistro()->format('d/m/Y'),
                'historial' => $historial !== null
            );
        }
        return new JsonResponse(array('success' => true, 'rows' => $_data, 'total' => count($_data)));
    }


    public function addLiquidacionesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        try {
            $em->transactional(function ($em) use ($request) {
                $anticipoid = $request->get('anticipoid');
                $datos_liquidaciones = json_decode($request->get('liquidaciones'));
                $anticipo = $em->getRepository('PortadoresBundle:Anticipo')->find($anticipoid);

                $ant_liq = $em->getRepository('PortadoresBundle:Liquidacion')->findby(array('anticipo' => $anticipoid));

                $suma_importe = 0;
                foreach ($ant_liq as $liquid) {
                    $historial = $em->getRepository('PortadoresBundle:HistorialTarjeta')->findOneBy(array('liquidacionid' => $liquid->getId()));
                    if ($historial) {
                        $em->remove($historial);
                        $suma_importe += $liquid->getImporte();
                    }
                    $em->remove($liquid);
                }

                $tarjeta = $anticipo->getTarjeta();
                $tarjeta->setImporte($tarjeta->getImporte() + $suma_importe);
                $em->persist($tarjeta);

                try {
                    $em->flush();
                } catch (\Exception $e) {
                    return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error en el servidor.'));
                }

                $vehiculo = $anticipo->getVehiculo();

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
                    $fechaDesde = $fecha->format('Y') . '-' . '01-01 00:00:00';
                    $fechaHasta = FechaUtil::getUltimoDiaMes(12, $fecha->format('Y'));

                    $suma_importe += $importe;
                    //validar la consecutividad de las acciones sobre la tarjeta
                    $entityValidar = $em->getRepository('PortadoresBundle:HistorialTarjeta')->buscarHistorialValidar($tarjeta->getId(), $fecha->format('Y-m-d h:i:s'), false);
                    $valido = true;
                    foreach ($entityValidar as $entity) {
                        if ($entity->getLiquidacionid() != null && $entity->getLiquidacionid()->getAnticipo()->getId() == $anticipoid) {
                            $historial = $entity;
                            $valido = false;
                        }
                    }
                    if (!$valido) {
                        //$historial = $entityValidar[count($entityValidar) - 1];
                        return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'No se pueden crear liquidaciones con fecha anterior a ' . $historial->getFecha()->format('d/m/Y g:i A') . ' para la tarjeta: ' . $tarjeta->getNroTarjeta() . '.'));
                    }

//           Validar que la cantidad del anticipo no sobrepase lo que queda por consumir del plan del Anno del vehiculo seleccionado
//            if ($tarjeta->getMonedaid()->getId() == \Geocuba\PortadoresBundle\Util\MonedaEnum::cup)
//                $cantPlanificadoVehiculo = $em->getRepository('PortadoresBundle:PlanificacionCombustible')->planificacionVehiculo($vehiculo->getId(), 13, $fecha->format('Y'));
//            else
//                $cantPlanificadoVehiculo = $em->getRepository('PortadoresBundle:PlanificacionCombustibleCuc')->planificacionVehiculo($vehiculo->getId(), 13, $fecha->format('Y'));
//            $cantConsumidaVehiculo = $em->getRepository('PortadoresBundle:Liquidacion')->consumoVehiculo($vehiculo->getId(), $tarjeta->getTipoCombustibleid()->getId(), $fechaDesde, $fechaHasta, $tarjeta->getMonedaid()->getId());
//            if (is_null($cantConsumidaVehiculo))
//                $cantConsumidaVehiculo = 0;
//            if ($cantPlanificadoVehiculo - $cantConsumidaVehiculo < $cant_litros)
//                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'La cantidad solicitada no puede exceder los ' . ($cantPlanificadoVehiculo - $cantConsumidaVehiculo) . ' L porque sobrepasa el combustible planificado anual del vehículo seleccionado.'));

                    $entity = $em->getRepository('PortadoresBundle:Liquidacion')->find($liquidacionid);
                    if (!$entity) {
                        $entity = new Liquidacion();
                    }

                    $entity->setAnticipo($anticipo);
                    $entity->setNroVale($nro_vale);
                    $entity->setImporte($importe);
                    $entity->setImporteFinal($importe_final);
                    $entity->setImporteInicial($importe_inicial);
                    $entity->setCantLitros($cant_litros);
                    $entity->setFechaVale($fecha);
                    $entity->setFechaRegistro($fecha_registro);
//            $entity->setNsubactividadid($em->getRepository('PortadoresBundle:SubActividad')->find($nsubactividadid));
                    $entity->setNactividadid($em->getRepository('PortadoresBundle:Actividad')->find($nsubactividadid));
                    $entity->setNpersonaid($em->getRepository('PortadoresBundle:Persona')->find($npersonaid));
                    $entity->setNservicentroid($em->getRepository('PortadoresBundle:Servicentro')->find($nservicentroid));
                    $entity->setNtarjetaid($tarjeta);
                    $entity->setNvehiculoid($vehiculo);
                    $entity->setVisible(true);
                    $entity->setUnidadid($nunidadid);

//            $entity->setNunidadid($em->getRepository('PortadoresBundle:Unidad')->find($nunidadid));
                    $entity->setNcentrocostoid($em->getRepository('PortadoresBundle:CentroCosto')->find($ncentrocostoid));
                    if (isset($nfamiliaid) && !is_null($nfamiliaid))
                        $entity->setNfamiliaid($em->getRepository('PortadoresBundle:Familia')->find($nfamiliaid));

                    $tarjeta->setImporte($importe_final);

                    try {
                        $em->persist($entity);
                        $em->persist($tarjeta);
                    } catch (CommonException $ex) {
                        return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
                    }
                    $precio_comb = $tarjeta->getTipoCombustibleid()->getPrecio();

                    $session = $request->getSession();
                    $mes = $session->get('current_month');
                    $anno = $session->get('current_year');

                    $entity1 = new HistorialTarjeta();
                    $entity1->setLiquidacionid($entity);
                    $entity1->setFecha($fecha);
                    $entity1->setSalidaImporte($importe);
                    $entity1->setSalidaCantidad($cant_litros);
                    $entity1->setExistenciaImporte($importe_final);
                    $entity1->setExistenciaCantidad(round($importe_final / $precio_comb, 2));
                    $entity1->setNroVale($nro_vale);
                    $entity1->setCancelado(false);
                    $entity1->setMes($mes);
                    $entity1->setAnno($anno);
                    $entity1->setTarjetaid($tarjeta);

                    try {
                        $em->persist($entity1);
                    } catch (CommonException $ex) {
                        return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
                    }
                }

                /* if ($suma_importe > $importe_anticipo) {
                    return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El importe de las liquidaciones no puede exceder el importe de anticipo'));
                } */

                $em->flush();

            });
        } catch (\Exception $e) {

            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => $e->getMessage()));
        }

        return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Liquidación(es) realizada(s) con éxito.'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $year = $session->get('current_year');
        $fecha = new \DateTime();

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

        $entityValidar = $em->getRepository('PortadoresBundle:Anticipo')->findOneBy(array(
            'tarjeta' => $tarjeta,
            'abierto' => true,
            'visible' => true
        ));
        if ($entityValidar) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Existe un anticipo abierto para la tarjeta: ' . $tarjeta->getNroTarjeta() . '.'));
        }

        /*$entityValidar = $em->getRepository('PortadoresBundle:Anticipo')->findOneBy(array(
            'npersonaid' => $persona,
            'abierto' => true,
            'visible' => true
        ));
        if ($entityValidar) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Existe un anticipo abierto para la persona: ' . $persona->getNombre() .'.'));
        }*/

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
//        if ($tarjeta->getMonedaid()->getId() == \Geocuba\PortadoresBundle\Util\MonedaEnum::cup) {
//        $cantPlanificadoVehiculo = $em->getRepository('PortadoresBundle:PlanificacionCombustible')->planificacionVehiculo($vehiculo->getId(), $fecha->format('m'), $fecha->format('Y'));
//        } else {
//            $cantPlanificadoVehiculo = $em->getRepository('PortadoresBundle:PlanificacionCombustibleCuc')->planificacionVehiculo($vehiculo->getId(), $fecha->format('m'), $fecha->format('Y'));
//        }
//        $cantConsumidaVehiculo = $em->getRepository('PortadoresBundle:Liquidacion')->consumoVehiculo($vehiculo->getId(), $tarjeta->getTipoCombustibleid()->getId(), $fechaDesde, $fechaHasta, $tarjeta->getMonedaid()->getId());
//        if (null === $cantConsumidaVehiculo) {
//            $cantConsumidaVehiculo = 0;
//        }
//
//        if ($cantPlanificadoVehiculo - $cantConsumidaVehiculo < $cantidad)
//            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'La cantidad solicitada no puede exceder los ' . ($cantPlanificadoVehiculo - $cantConsumidaVehiculo) . ' L porque sobrepasa el combustible planificado al vehículo seleccionado.'));

        $entity = new Anticipo();
        $entity->setFecha($fecha);
        $entity->setNpersonaid($persona);
        $entity->setVehiculo($vehiculo);
        $entity->setTarjeta($tarjeta);
        $entity->setCantidad($cantidad);
        $entity->setImporte($importe);
        $entity->setTrabajo($trabajo);
        if ($transito) {
            $entity->setTransito(true);
        } else {
            $entity->setTransito(false);
        }
        if ($terceros) {
            $entity->setTerceros(true);
        } else {
            $entity->setTerceros(false);
        }
        $entity->setExcepcional($excepcional);
        if($excepcional)
            $entity->setMotivo($request->get('motivo'));
        $entity->setVisible(true);

        $consecutivo = $em->getRepository('PortadoresBundle:Consecutivos')->findOneBy(array('anno' => $year, 'idunidad' => $vehiculo->getNunidadid()->getId()));
        $noAnticipo = 1;
        if (null !== $consecutivo) {
            $noAnticipo = $consecutivo->getNoAnticipo();
            $consecutivo->setNoAnticipo($noAnticipo + 1);
        } else {
            $consecutivo = new Consecutivos();
            $consecutivo->setAnno($year);
            $consecutivo->setIdunidad($vehiculo->getNunidadid());
            $consecutivo->setNoAnticipo($noAnticipo + 1);
        }

        $entity->setNoVale($vehiculo->getNunidadid()->getSiglas() . '-' . str_pad($noAnticipo, 4, '0', STR_PAD_LEFT));

        try {
            $em->persist($consecutivo);
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Anticipo adicionado con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function modAction(Request $request)
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

        $entity = $em->getRepository('PortadoresBundle:Anticipo')->find($id);

        if (!$entity->getAbierto())
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El anticipo ya ha sido cerrado y no se puede modificar.'));

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

        /*if ($npersonaid != $entity->getNpersonaid()->getId()) {
            $entityValidar = $em->getRepository('PortadoresBundle:Anticipo')->findOneBy(array(
                'npersonaid' => $persona,
                'abierto' => true,
                'visible' => true
            ));
            if ($entityValidar) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Existe un anticipo abierto para la persona: ' . $persona->getNombre() .'.'));
            }
        }*/

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

        //Validar que la cantidad del anticipo no sobrepase lo que queda por consumir del plan del vehiculo seleccionado
//        if ($tarjeta->getMonedaid()->getId() == \Geocuba\PortadoresBundle\Util\MonedaEnum::cup)
//        $cantPlanificadoVehiculo = $em->getRepository('PortadoresBundle:PlanificacionCombustible')->planificacionVehiculo($vehiculo->getId(), $fecha->format('m'), $fecha->format('Y'));
//        else
//            $cantPlanificadoVehiculo = $em->getRepository('PortadoresBundle:PlanificacionCombustibleCuc')->planificacionVehiculo($vehiculo->getId(), $fecha->format('m'), $fecha->format('Y'));
//        $cantConsumidaVehiculo = $em->getRepository('PortadoresBundle:Liquidacion')->consumoVehiculo($vehiculo->getId(), $tarjeta->getTipoCombustibleid()->getId(), $fechaDesde, $fechaHasta, $tarjeta->getMonedaid()->getId());
//        if (is_null($cantConsumidaVehiculo))
//            $cantConsumidaVehiculo = 0;
//        if ($cantPlanificadoVehiculo - $cantConsumidaVehiculo < $cantidad)
//            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'La cantidad solicitada no puede exceder los ' . ($cantPlanificadoVehiculo - $cantConsumidaVehiculo) . ' L porque sobrepasa el combustible planificado al vehículo seleccionado.'));

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
        if($excepcional)
            $entity->setMotivo($request->get('motivo'));
        try {
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Anticipo  modificado con éxito.'));
            return $response;
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function delAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $entity = $em->getRepository('PortadoresBundle:Anticipo')->find($id);

        if (!$entity->getAbierto())
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El anticipo ya ha sido cerrado y no se puede eliminar.'));

        $liquidacion = $em->getRepository('PortadoresBundle:Liquidacion')->findOneBy(array('anticipo'=>$entity));
        if ($liquidacion)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El anticipo ya ha sido liquidado y no se puede eliminar.'));

        $entity->setVisible(false);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Anticipo  cancelado con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function addChipAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $tarjeta_id = $request->get('ntarjetaid');
        $nro_vale = trim($request->get('nro_vale'));
        $fecha_registro = trim($request->get('fecha_registro'));
        $fecha_servicio = trim($request->get('fecha_vale'));
        $hora_servicio = trim($request->get('hora_vale'));
        $servicentro_id = trim($request->get('nservicentroid'));
        $cant_litros = trim($request->get('cant_litros'));

        $_fecha_registro = date_create_from_format('d/m/Y', $fecha_registro);
        $_fecha_servicio = date_create_from_format('d/m/Y g:i A', $fecha_servicio . ' ' . $hora_servicio);
        $tarjeta = $em->getRepository('PortadoresBundle:Tarjeta')->find($tarjeta_id);

        $repetido = $em->getRepository('PortadoresBundle:Liquidacion')->buscarLiquidacionRepetido($nro_vale, $_fecha_servicio);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Existe una liquidación con igual número de comprobante en el día ' . $fecha_servicio . '.'));

        $anticipo = $em->getRepository('PortadoresBundle:Anticipo')->findOneBy(array('tarjeta' => $tarjeta_id, 'visible' => true));
        if (!$anticipo)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'No existe ningún anticipo abierto para la tarjeta ' . $tarjeta->getNroTarjeta()));

        $liquidacion = new Liquidacion();
        $liquidacion->setTarjetaid($tarjeta);
        $liquidacion->setNroVale($nro_vale);
        $liquidacion->setCantLitros($cant_litros);
        $liquidacion->setImporte($cant_litros * Datos::getCombustibles($em, $tarjeta->getTipoCombustibleid()->getId())['precio']);
        $liquidacion->setFechaRegistro($_fecha_registro);
        $liquidacion->setFechaVale($_fecha_servicio);
        $liquidacion->setVehiculoid($anticipo->getVehiculo());
        $liquidacion->setPersonaid($anticipo->getNpersonaid());
        $liquidacion->setNcentrocostoid($tarjeta->getCentrocosto());
        $liquidacion->setNactividadid($anticipo->getVehiculo()->getActividad());
        $liquidacion->setUnidadid($tarjeta->getCentrocosto()->getNunidadid());
        $liquidacion->setNservicentroid($em->getRepository('PortadoresBundle:Servicentro')->find($servicentro_id));
        $liquidacion->setVisible(true);
        $liquidacion->setAnticipo($anticipo);

        try {
            $em->persist($liquidacion);
            $em->flush();
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
        return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Entrega adicionada con éxito.'));
    }

    public function modChipAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $liquidacion_id = $request->get('id');
        $tarjeta_id = $request->get('ntarjetaid');
        $nro_vale = trim($request->get('nro_vale'));
        $fecha_registro = trim($request->get('fecha_registro'));
        $fecha_servicio = trim($request->get('fecha_vale'));
        $hora_servicio = trim($request->get('hora_vale'));
        $servicentro_id = trim($request->get('nservicentroid'));

        $_fecha_registro = date_create_from_format('d/m/Y', $fecha_registro);
        $_fecha_servicio = date_create_from_format('d/m/Y g:i A', $fecha_servicio . ' ' . $hora_servicio);
        $tarjeta = $em->getRepository('PortadoresBundle:Tarjeta')->find($tarjeta_id);

        $repetido = $em->getRepository('PortadoresBundle:Liquidacion')->buscarLiquidacionRepetido($nro_vale, $_fecha_servicio, $liquidacion_id);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Existe una liquidación con igual número de comprobante en el día ' . $fecha_servicio . '.'));

        $anticipo = $em->getRepository('PortadoresBundle:Anticipo')->findOneBy(array('tarjeta' => $tarjeta_id, 'visible' => true));
        if (!$anticipo)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'No existe ningún anticipo abierto para la tarjeta ' . $tarjeta->getNroTarjeta()));

        $liquidacion = $em->getRepository('PortadoresBundle:Liquidacion')->find($liquidacion_id);
        $liquidacion->setTarjetaid($tarjeta);
        $liquidacion->setNroVale($nro_vale);
        $liquidacion->setFechaRegistro($_fecha_registro);
        $liquidacion->setFechaVale($_fecha_servicio);
        $liquidacion->setNservicentroid($em->getRepository('PortadoresBundle:Servicentro')->find($servicentro_id));
        $anticipo = $em->getRepository('PortadoresBundle:Anticipo')->findOneBy(array('tarjeta' => $tarjeta_id, 'visible' => true));
        $liquidacion->setAnticipo($anticipo);

        try {
            $em->persist($liquidacion);
            $em->flush();
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
        return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Entrega modificada con éxito.'));
    }

    public function delLiquidacionAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $entity = $em->getRepository('PortadoresBundle:Liquidacion')->find($id);

        $anticipo = $entity->getAnticipo();
        if (!$anticipo->getAbierto())
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El anticipo ya ha sido cerrado.'));

        $entity->setAnticipo(null);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Liquidación eliminada con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function cerrarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $_fechaCierre = trim($request->get('fecha'));
        $_horaCierre = trim($request->get('hora'));
        $fecha = date_create_from_format('d/m/Y g:i A', $_fechaCierre . ' ' . $_horaCierre);
        $entity = $em->getRepository('PortadoresBundle:Anticipo')->find($id);

        $liquidaciones = $em->getRepository('PortadoresBundle:Liquidacion')->findBy(array('anticipo' => $id));

        if (count($liquidaciones) == 0) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El anticipo no ha sido liquidado.'));
        }

        foreach ($liquidaciones as $liquidacion) {
            $historial = $em->getRepository('PortadoresBundle:HistorialTarjeta')->findOneBy(array('liquidacionid' => $liquidacion->getId()));
            if (!$historial)
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Faltan datos en alguna liquidación.'));
        }

        $entity->setAbierto(false);
        $entity->setFechaCierre($fecha);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Anticipo cerrado con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function printAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = $request->get('anticipo_id');
        $anticipo = $em->getRepository('PortadoresBundle:Anticipo')->find($id);

        $matricula = $anticipo->getVehiculo()->getMatricula();
        $tarjetaAnticipo = $anticipo->getTarjeta()->getNroTarjeta();
        $cantidadAnticipo = $anticipo->getCantidad();
        $fechaAnticipo = ($anticipo->getFecha()) ? $anticipo->getFecha()->format('Y-m-d') : '';
        $horaAnticipo = ($anticipo->getFecha()) ? $anticipo->getFecha()->format('g:i A') : '';
        $tipoCombustibleAnticipo = $anticipo->getTarjeta()->getTipoCombustibleid()->getNombre();

        $persona = $anticipo->getNPersonaid()->getNombre();

        $liquidaciones = $em->getRepository('PortadoresBundle:Liquidacion')->findBy(array('anticipo' => $anticipo->getId(), 'visible' => true));

        $tipoCombustible = count($liquidaciones) > 0 ? $liquidaciones[0]->getTarjetaid()->getTipoCombustibleid()->getNombre() : '';
        $fecha = '';
        if (count($liquidaciones) == 1)
            $numeroValeServicentro = $liquidaciones[0]->getNroVale();
        elseif (count($liquidaciones) == 0)
            $numeroValeServicentro = '';
        else
            $numeroValeServicentro = 'Inferior';
        $cantidad = 0;
        if (count($liquidaciones) == 1)
            $servicentro = $liquidaciones[0]->getServicentroid()->getNombre();
        elseif (count($liquidaciones) == 0)
            $servicentro = '';
        else
            $servicentro = 'Inferior';
        $inicio = 0;
        $abastecido = 0;
        $final = PHP_INT_MAX;
        $noTarjeta = count($liquidaciones) > 0 ? $liquidaciones[0]->getTarjetaid()->getNroTarjeta() : '';
        $fechaDevolucion = ($anticipo->getFechaCierre()) ? $anticipo->getFechaCierre()->format('Y-m-d') : '';
        $horaDevolucion = ($anticipo->getFechaCierre()) ? $anticipo->getFechaCierre()->format('g:i A') : '';

        $unidadid = count($liquidaciones) > 0 ? $liquidaciones[0]->getUnidadid()->getId() : '';
        $entidad = count($liquidaciones) > 0 ? $liquidaciones[0]->getUnidadid()->getNombre() : '';

        $cc = count($liquidaciones) > 0 ? $liquidaciones[0]->getNcentrocostoid()->getNombre() : '';

        $servicentros = "<table cellspacing='0' cellpadding='5' border='1' width='50%' style='margin-top:10px'>";
        $servicentros .= "<tr><td style='text-align: center;'><strong>No. Vale</strong></td><td style='text-align: center;'><strong>Servicentro</strong></td></tr>";

        $numeroAnticipo = $anticipo->getNoVale();

        foreach ($liquidaciones as $liquidacion) {

            $fecha = $liquidacion->getFechaVale()->format('Y-m-d');

            $cantidad += $liquidacion->getCantLitros();

            if ($inicio < $liquidacion->getImporteInicial())
                $inicio = $liquidacion->getImporteInicial();
            $abastecido += $liquidacion->getImporte();
            if ($final > $liquidacion->getImporteFinal())
                $final = $liquidacion->getImporteFinal();

            $nv = $liquidacion->getNroVale();
            $svc = $liquidacion->getServicentroid()->getNombre();
            $servicentros .= "<tr><td style='text-align: left;'>$nv</td><td style='text-align: left;'>$svc</td></tr>";
        }

        $servicentros .= "</table>";

        if (is_null($anticipo->getTrabajo()))
            $proyecto = '-';
        else
            $proyecto = $anticipo->getTrabajo()->getCodigo() . '/' . $anticipo->getTrabajo()->getNombre();

        $_html = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
        <html xmlns='http://www.w3.org/1999/xhtml'>
        <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <title>ANTICIPO Y LIQUIDACI&Oacute;N DE COMBUSTIBLE</title>
        <style>
            table {
                border:0 solid;
                border-radius:0;
                font-family: 'Arial', serif;
                font-size: 12px;
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
            <td colspan='8' style='text-align: center;'><strong>ANTICIPO Y LIQUIDACI&Oacute;N DE COMBUSTIBLE</strong></td>
         </tr>

         <tr>
            <td colspan='2' style='text-align: center;'><strong>Entidad</strong></td>
            <td colspan='6' style='text-align: center;'>$entidad</td>
         </tr>

         <tr>
            <td colspan='2' style='text-align: center;'><strong>Agencia/Unidad</strong></td>
            <td colspan='6' style='text-align: center;'>$entidad</td>
         </tr>

         <tr>
            <td colspan='3' style='text-align: center;'><strong>Nombre y Apellidos</strong></td>
            <td colspan='5' style='text-align: center;'>$persona</td>
         </tr>

         <tr>
            <td colspan='2' style='text-align: center;'><strong>C/C</strong></td>
            <td colspan='2' style='text-align: center;'>$cc</td>
            <td colspan='1' style='text-align: center;'><strong>Chapa No.</strong></td>
            <td colspan='1' style='text-align: center;'>$matricula</td>
            <td colspan='1' style='text-align: center;'><strong>Fecha</strong></td>
            <td colspan='1' style='text-align: center;'>$fechaAnticipo</td>
         </tr>

         <tr>
            <td colspan='2' style='text-align: center;'><strong>Tarjeta No.</strong></td>
            <td colspan='2' style='text-align: center;'>$tarjetaAnticipo</td>
            <td colspan='1' style='text-align: center;'><strong>Saldo Tarjeta</strong></td>
            <td colspan='1' style='text-align: center;'>$$inicio</td>
            <td colspan='1' style='text-align: center;'><strong>Cant. a Serv</strong></td>
            <td colspan='1' style='text-align: center;'>$cantidadAnticipo</td>
         </tr>

         <tr>
            <td colspan='2' style='text-align: center;'><strong>Entrega</strong></td>
            <td colspan='2' style='text-align: center;'><strong>Fecha</strong></td>
            <td colspan='2' style='text-align: center;'>$fechaAnticipo</td>
            <td colspan='1' style='text-align: center;'><strong>Hora</strong></td>
            <td colspan='1' style='text-align: center;'>$horaAnticipo</td>
         </tr>

         <tr>
            <td colspan='4' style='text-align: center;'><strong>Tipo de Combustible</strong></td>
            <td colspan='4' style='text-align: center;'>$tipoCombustibleAnticipo</td>
         </tr>

         <tr>
            <td colspan='2' style='text-align: center;'><strong>Recibido por</strong></td>
            <td colspan='2' style='text-align: center;'></td>
            <td colspan='2' style='text-align: center;'><strong>Autorizado por</strong></td>
            <td colspan='2' style='text-align: center;'></td>
         </tr>

          <tr>
            <td colspan='8' style='text-align: center;'><strong> LIQUIDACI&Oacute;N</strong></td>
         </tr>

         <tr>
            <td colspan='3' style='text-align: center;'><strong>Tipo de Combustible</strong></td>
            <td colspan='2' style='text-align: center;'>$tipoCombustible</td>
            <td colspan='1' style='text-align: center;'><strong>Fecha</strong></td>
            <td colspan='2' style='text-align: center;'>$fecha</td>
         </tr>

          <tr>
            <td colspan='2' style='text-align: center;'><strong>No. Vale Servicentro</strong></td>
            <td colspan='1' style='text-align: center;'>$numeroValeServicentro</td>
            <td colspan='1' style='text-align: center;'><strong>Cantidad</strong></td>
            <td colspan='1' style='text-align: center;'>$cantidad L</td>
            <td colspan='1' style='text-align: center;'><strong>Servicentro</strong></td>
            <td colspan='2' style='text-align: center;'>$servicentro</td>
         </tr>

         <tr>
            <td colspan='2' style='text-align: center;'><strong>Importe</strong></td>
            <td colspan='1' style='text-align: center;'>Inicio</td>
            <td colspan='1' style='text-align: center;'>$$inicio</td>
            <td colspan='1' style='text-align: center;'>Abastecido</td>
            <td colspan='1' style='text-align: center;'>$$abastecido</td>
            <td colspan='1' style='text-align: center;'>Final</td>
            <td colspan='1' style='text-align: center;'>$$final</td>
         </tr>

          <tr>
            <td colspan='2' style='text-align: center;'><strong>Tarjeta No.</strong></td>
            <td colspan='1' style='text-align: center;'>$noTarjeta</td>
            <td colspan='1' style='text-align: center;'><strong>Devoluci&oacute;n</strong></td>
            <td colspan='1' style='text-align: center;'>Fecha</td>
            <td colspan='1' style='text-align: center;'>$fechaDevolucion</td>
            <td colspan='1' style='text-align: center;'>Hora</td>
            <td colspan='1' style='text-align: center;'>$horaDevolucion</td>
         </tr>

         <tr>
            <td colspan='2' style='text-align: center;'><strong>Entregado por</strong></td>
            <td colspan='2' style='text-align: center;'></td>
            <td colspan='2' style='text-align: center;'><strong>Autorizado por</strong></td>
            <td colspan='2' style='text-align: center;'></td>
         </tr>

          <tr>
            <td colspan='3' style='text-align: left;'><strong>Custodiado por</strong></td>
            <td colspan='1' style='text-align: left;'><strong>F</strong></td>
            <td colspan='1' style='text-align: left;'><strong>M</strong></td>
            <td colspan='1' style='text-align: left;'><strong>A</strong></td>
            <td colspan='2' style='text-align: left;'><strong>No. Vale</strong> &nbsp;&nbsp;&nbsp;$numeroAnticipo</td>
         </tr>

         <tr style='border: none; height: 1px;'>
            <td colspan='1' style='text-align: center; border: none; height: 1px;'></td>
            <td colspan='1' style='text-align: center; border: none; height: 1px;'></td>
            <td colspan='1' style='text-align: center; border: none; height: 1px;'></td>
            <td colspan='1' style='text-align: center; border: none; height: 1px;'></td>
            <td colspan='1' style='text-align: center; border: none; height: 1px;'></td>
            <td colspan='1' style='text-align: center; border: none; height: 1px;'></td>
            <td colspan='1' style='text-align: center; border: none; height: 1px;'></td>
            <td colspan='1' style='text-align: center; border: none; height: 1px;'></td>
         </tr>

         </table>";

        $_html .= "<p style='margin: 0;'><strong>Proyecto: &nbsp;&nbsp;&nbsp;&nbsp;</strong>$proyecto</p> ";

        if (count($liquidaciones) > 1)
            $_html .= $servicentros;

        $_html .= "   </body>
            </html>";

        return new Response(json_encode(array('success' => true, 'html' => $_html)));
    }

    public function printOldAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = $request->get('id');
        $anticipo = $em->getRepository('PortadoresBundle:Anticipo')->find($id);

        $matricula = $anticipo->getVehiculo()->getMatricula();
        $tarjetaAnticipo = $anticipo->getTarjeta()->getNroTarjeta();
        $cantidadAnticipo = $anticipo->getCantidad();
        $fechaAnticipo = ($anticipo->getFecha()) ? $anticipo->getFecha()->format('d/m/Y') : '';
        $horaAnticipo = ($anticipo->getFecha()) ? $anticipo->getFecha()->format('g:i A') : '';
        $tipoCombustibleAnticipo = $anticipo->getTarjeta()->getTipoCombustibleid()->getNombre();

        $persona = $anticipo->getNpersonaid()->getNombre();

        $liquidaciones = $anticipo->getLiquidacion();

        $tipoCombustible = count($liquidaciones) > 0 ? $liquidaciones[0]->getNtarjetaid()->getTipoCombustibleid()->getNombre() : '';
        $fecha = '';
        if (count($liquidaciones) == 1)
            $numeroValeServicentro = $liquidaciones[0]->getNroVale();
        elseif (count($liquidaciones) == 0)
            $numeroValeServicentro = '';
        else
            $numeroValeServicentro = 'Inferior';
        $cantidad = 0;
        if (count($liquidaciones) == 1)
            $servicentro = $liquidaciones[0]->getNservicentroid()->getNombre();
        elseif (count($liquidaciones) == 0)
            $servicentro = '';
        else
            $servicentro = 'Inferior';
        $inicio = 0;
        $abastecido = 0;
        $final = PHP_INT_MAX;
        $noTarjeta = count($liquidaciones) > 0 ? $liquidaciones[0]->getNtarjetaid()->getNroTarjeta() : '';
        $fechaDevolucion = ($anticipo->getFechaCierre()) ? $anticipo->getFechaCierre()->format('d/m/Y') : '';
        $horaDevolucion = ($anticipo->getFechaCierre()) ? $anticipo->getFechaCierre()->format('g:i A') : '';

//        $unidadid = count($liquidaciones) > 0 ? $liquidaciones[0]->getNunidadid()->getId() : '';
        $unidad = count($liquidaciones) > 0 ? $liquidaciones[0]->getNunidadid()->getNombre() : '';
        $cc = count($liquidaciones) > 0 ? $liquidaciones[0]->getNcentrocostoid()->getNombre() : '';

        $servicentros = "<table cellspacing='0' cellpadding='5' border='1' width='50%' style='margin-top:10px'>";
        $servicentros .= "<tr><td style='text-align: center;'><strong>No. Vale</strong></td><td style='text-align: center;'><strong>Servicentro</strong></td></tr>";

        $numeroAnticipo = $anticipo->getNoVale();

        foreach ($liquidaciones as $liquidacion) {

            $fecha = $liquidacion->getFechaVale()->format('d/m/Y');

            $cantidad += $liquidacion->getCantLitros();

            if ($inicio < $liquidacion->getImporteInicial())
                $inicio = $liquidacion->getImporteInicial();
            $abastecido += $liquidacion->getImporte();
            //if ($final > $liquidacion->getImporteFinal())
            $final = $liquidacion->getImporteFinal();

            $nv = $liquidacion->getNroVale();
            $svc = $liquidacion->getNservicentroid()->getNombre();
            $servicentros .= "<tr><td style='text-align: left;'>$nv</td><td style='text-align: left;'>$svc</td></tr>";
        }

        $servicentros .= "</table>";

        if (is_null($anticipo->getTrabajo()))
            $proyecto = '-';
        else
            $proyecto = $anticipo->getTrabajo()->getCodigo() . '/' . $anticipo->getTrabajo()->getNombre();

        $_html = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
        <html xmlns='http://www.w3.org/1999/xhtml'>
        <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <title>ANTICIPO Y LIQUIDACI&Oacute;N DE COMBUSTIBLE</title>
        <style>
            table {
                border:0 solid;
                border-radius:0;
                font-family: 'Arial', serif;
                font-size: 12px;
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
            <td colspan='8' style='text-align: center;'><strong>ANTICIPO Y LIQUIDACI&Oacute;N DE COMBUSTIBLE</strong></td>
         </tr>

         <tr>
            <td colspan='2' style='text-align: center;'><strong>Entidad</strong></td>
            <td colspan='6' style='text-align: center;'>$unidad</td>
         </tr>

         <tr>
            <td colspan='2' style='text-align: center;'><strong>Agencia/Unidad</strong></td>
            <td colspan='6' style='text-align: center;'>$unidad</td>
         </tr>

         <tr>
            <td colspan='3' style='text-align: center;'><strong>Nombre y Apellidos</strong></td>
            <td colspan='5' style='text-align: center;'>$persona</td>
         </tr>

         <tr>
            <td colspan='2' style='text-align: center;'><strong>C/C</strong></td>
            <td colspan='2' style='text-align: center;'>$cc</td>
            <td colspan='1' style='text-align: center;'><strong>Chapa No.</strong></td>
            <td colspan='1' style='text-align: center;'>$matricula</td>
            <td colspan='1' style='text-align: center;'><strong>Fecha</strong></td>
            <td colspan='1' style='text-align: center;'>$fechaAnticipo</td>
         </tr>

         <tr>
            <td colspan='2' style='text-align: center;'><strong>Tarjeta No.</strong></td>
            <td colspan='2' style='text-align: center;'>$tarjetaAnticipo</td>
            <td colspan='1' style='text-align: center;'><strong>Saldo Tarjeta</strong></td>
            <td colspan='1' style='text-align: center;'>$$inicio</td>
            <td colspan='1' style='text-align: center;'><strong>Cant. a Serv</strong></td>
            <td colspan='1' style='text-align: center;'>$cantidadAnticipo</td>
         </tr>

         <tr>
            <td colspan='2' style='text-align: center;'><strong>Entrega</strong></td>
            <td colspan='2' style='text-align: center;'><strong>Fecha</strong></td>
            <td colspan='2' style='text-align: center;'>$fechaAnticipo</td>
            <td colspan='1' style='text-align: center;'><strong>Hora</strong></td>
            <td colspan='1' style='text-align: center;'>$horaAnticipo</td>
         </tr>

         <tr>
            <td colspan='4' style='text-align: center;'><strong>Tipo de Combustible</strong></td>
            <td colspan='4' style='text-align: center;'>$tipoCombustibleAnticipo</td>
         </tr>

         <tr>
            <td colspan='2' style='text-align: center;'><strong>Recibido por</strong></td>
            <td colspan='2' style='text-align: center;'></td>
            <td colspan='2' style='text-align: center;'><strong>Autorizado por</strong></td>
            <td colspan='2' style='text-align: center;'></td>
         </tr>

          <tr>
            <td colspan='8' style='text-align: center;'><strong> LIQUIDACI&Oacute;N</strong></td>
         </tr>

         <tr>
            <td colspan='3' style='text-align: center;'><strong>Tipo de Combustible</strong></td>
            <td colspan='2' style='text-align: center;'>$tipoCombustible</td>
            <td colspan='1' style='text-align: center;'><strong>Fecha</strong></td>
            <td colspan='2' style='text-align: center;'>$fecha</td>
         </tr>

          <tr>
            <td colspan='2' style='text-align: center;'><strong>No. Vale Servicentro</strong></td>
            <td colspan='1' style='text-align: center;'>$numeroValeServicentro</td>
            <td colspan='1' style='text-align: center;'><strong>Cantidad</strong></td>
            <td colspan='1' style='text-align: center;'>$cantidad L</td>
            <td colspan='1' style='text-align: center;'><strong>Servicentro</strong></td>
            <td colspan='2' style='text-align: center;'>$servicentro</td>
         </tr>

         <tr>
            <td colspan='2' style='text-align: center;'><strong>Importe</strong></td>
            <td colspan='1' style='text-align: center;'>Inicio</td>
            <td colspan='1' style='text-align: center;'>$$inicio</td>
            <td colspan='1' style='text-align: center;'>Abastecido</td>
            <td colspan='1' style='text-align: center;'>$$abastecido</td>
            <td colspan='1' style='text-align: center;'>Final</td>
            <td colspan='1' style='text-align: center;'>$$final</td>
         </tr>

          <tr>
            <td colspan='2' style='text-align: center;'><strong>Tarjeta No.</strong></td>
            <td colspan='1' style='text-align: center;'>$noTarjeta</td>
            <td colspan='1' style='text-align: center;'><strong>Devoluci&oacute;n</strong></td>
            <td colspan='1' style='text-align: center;'>Fecha</td>
            <td colspan='1' style='text-align: center;'>$fechaDevolucion</td>
            <td colspan='1' style='text-align: center;'>Hora</td>
            <td colspan='1' style='text-align: center;'>$horaDevolucion</td>
         </tr>

         <tr>
            <td colspan='2' style='text-align: center;'><strong>Entregado por</strong></td>
            <td colspan='2' style='text-align: center;'></td>
            <td colspan='2' style='text-align: center;'><strong>Autorizado por</strong></td>
            <td colspan='2' style='text-align: center;'></td>
         </tr>

          <tr>
            <td colspan='3' style='text-align: left;'><strong>Custodiado por</strong></td>
            <td colspan='1' style='text-align: left;'><strong>F</strong></td>
            <td colspan='1' style='text-align: left;'><strong>M</strong></td>
            <td colspan='1' style='text-align: left;'><strong>A</strong></td>
            <td colspan='2' style='text-align: left;'><strong>No. Vale</strong> &nbsp;&nbsp;&nbsp;$numeroAnticipo</td>
         </tr>

         <tr style='border: none; height: 1px;'>
            <td colspan='1' style='text-align: center; border: none; height: 1px;'></td>
            <td colspan='1' style='text-align: center; border: none; height: 1px;'></td>
            <td colspan='1' style='text-align: center; border: none; height: 1px;'></td>
            <td colspan='1' style='text-align: center; border: none; height: 1px;'></td>
            <td colspan='1' style='text-align: center; border: none; height: 1px;'></td>
            <td colspan='1' style='text-align: center; border: none; height: 1px;'></td>
            <td colspan='1' style='text-align: center; border: none; height: 1px;'></td>
            <td colspan='1' style='text-align: center; border: none; height: 1px;'></td>
         </tr>

         </table>";

        $_html .= "<p style='margin: 0;'><strong>Proyecto: &nbsp;&nbsp;&nbsp;&nbsp;</strong>$proyecto</p> ";

        if (count($liquidaciones) > 1)
            $_html .= $servicentros;

        $_html .= "   </body>
            </html>";

        return new Response(json_encode(array('success' => true, 'html' => $_html)));
    }

    public function printSubmayorAction(Request $request)
    {
        $anticipoid = $request->query->get('id');

        $anticipo = $this->getDoctrine()->getRepository('PortadoresBundle:Anticipo')->find($anticipoid);

        if (!$anticipo) {
            throw new NotFoundHttpException('El Anticipo no existe.');
        }

        $title = 'Modelo Entrega-Liquidación';
        $file_generator_service = $this->get('app.service.file.helper');

        try {
            $word = $file_generator_service->createPhpWord($title);
            $word = $this->_writePhpWordDataSubmayor($title, $word, $anticipo);

            return $file_generator_service->stream($word, $title, $request->query->getInt('formato'));
        } catch (\Exception $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), $e);
        }
    }

    public function printEntregaAction(Request $request)
    {
        $id = $request->query->get('id');
        $mes = $request->get('mes');
        $anno = $request->get('anno');

        $fechaDesde = $anno . '-' . $mes . '-' . '1 00:00:00';
        $fechaHasta = FechaUtil::getUltimoDiaMes($mes, $anno);

        $tarjeta = $this->getDoctrine()->getRepository('PortadoresBundle:Tarjeta')->find($id);

        if (!$tarjeta) {
            throw new NotFoundHttpException('La Tarjeta no existe.');
        }

        $title = 'Entrega Chips Tarj. ' . $tarjeta->getNroTarjeta();
        $file_generator_service = $this->get('app.service.file.helper');

        try {
            $word = $file_generator_service->createPhpWord($title);
            $word = $this->_writePhpWordData($title, $word, $id, $tarjeta->getUnidadid(), $fechaDesde, $fechaHasta);

            return $file_generator_service->stream($word, $title, $request->query->getInt('formato'));
        } catch (\Exception $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), $e);
        }
    }

    private function _writePhpWordData($title, $word, $tarjetaid, $unidad, $fechaDesde, $fechaHasta)
    {

        $em = $this->getDoctrine()->getManager();
        $section = $word->addSection();
        $section->getStyle()->setPaperSize('Letter');
        $section->getStyle()->setOrientation('landscape');
        $section->getStyle()->setMarginLeft(750);
        $section->getStyle()->setMarginRight(750);

        //Header
//        $section->addHeader()->addImage($this->container->getParameter('kernel.project_dir') . '/web/assets/img/PNG/radiocuba.png', [
//            'width' => \PhpOffice\PhpWord\Shared\Converter::inchToPoint(1.25),
//            'height' => \PhpOffice\PhpWord\Shared\Converter::inchToPoint(0.48),
//            'scale' => 71,
//            'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::START
//        ]);

        //Datos
//        $_unidades = [];
//        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $entities = $em->getRepository('PortadoresBundle:Liquidacion')->buscarLiquidacion(null, null, $tarjetaid, null, $fechaDesde, $fechaHasta);
        if (count($entities) == 0)
            throw new NotFoundHttpException('La Tarjeta no tiene chips entregados este mes.');


        //Stilo de Tabla
        $tableStyleName = 'Base Style';
        $tableStyle = array(
            'cellMargin' => 50,
            'borderSize' => 2,
//            'borderColor' => 'eeeeee',
            'spacing' => 0);

        $tableCellStyle = array('valign' => 'center');
        $titleFontStyle = array('bold' => true);
        $columnsFontStyle = array('size' => 7);
        $cellHCentered = array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceBefore' => 2, 'spaceAfter' => 2);
        $cellHCenteredTitle = array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceBefore' => 100, 'spaceAfter' => 100);
        $cellHCenteredColumns = array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceBefore' => 50, 'spaceAfter' => 50);
        $word->addTableStyle($tableStyleName, $tableStyle);

        //Titulo
//        $header = array('size' => 12, 'bold' => true,'underline' => \PhpOffice\PhpWord\Style\Font::UNDERLINE_SINGLE);
//        $section->addText('MODELO PARA SOLICITUD DE TRABAJOS A REALIZAR EN LA RED', $header, $cellHCentered);

        //Columns Title
        $table = $section->addTable($tableStyleName);
        $row = $table->addRow(350, array('exactHeight' => true));
        $row->addCell(11000, array('gridSpan' => 6))->addText('ENTREGA DE CHIP DE COMBUSTIBLES', $titleFontStyle, $cellHCenteredTitle);
        $row->addCell(2000, array())->addText('TARJETA No', $titleFontStyle, $cellHCenteredTitle);
        $row->addCell(2000, array())->addText($entities[0]->getNtarjetaid()->getNroTarjeta(), array('bold' => true, 'size' => 8), ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceBefore' => 150, 'spaceAfter' => 150]);

        $row = $table->addRow(420, array('exactHeight' => true));
        $row->addCell(2000, $tableCellStyle)->addText('FECHA DE SERVICIO', $columnsFontStyle, $cellHCenteredColumns);
        $row->addCell(2000, $tableCellStyle)->addText('FECHA DE ENTREGA DEL CHIP', $columnsFontStyle, $cellHCenteredColumns);
        $row->addCell(1000, $tableCellStyle)->addText('TIPO DE COMBUSTIBLE', $columnsFontStyle, $cellHCenteredColumns);
        $row->addCell(1250, $tableCellStyle)->addText('No DEL CHIP', $columnsFontStyle, $cellHCenteredColumns);
        $row->addCell(1250, $tableCellStyle)->addText('MATRICULA', $columnsFontStyle, $cellHCenteredColumns);
        $row->addCell(3500, $tableCellStyle)->addText('NOMBRE DEL CHOFER', $columnsFontStyle, $cellHCenteredColumns);
        $row->addCell(2000, $tableCellStyle)->addText('FIRMA DEL CHOFER', $columnsFontStyle, $cellHCenteredColumns);
        $row->addCell(2000, $tableCellStyle)->addText('FIRMA DEL QUE RECIBE', $columnsFontStyle, $cellHCenteredColumns);

        //Rows
        foreach ($entities as $entity) {
            $row = $table->addRow(300, array('exactHeight' => true));
            $row->addCell(2000, $tableCellStyle)->addText($entity->getFechaVale()->format('d/m/Y'), $columnsFontStyle, $cellHCentered);
            $row->addCell(2000, $tableCellStyle)->addText($entity->getFechaRegistro()->format('d/m/Y'), $columnsFontStyle, $cellHCentered);
            $row->addCell(2000, $tableCellStyle)->addText($entity->getTarjetaid()->getTipoCombustibleid()->getNombre(), $columnsFontStyle, $cellHCentered);
            $row->addCell(2000, $tableCellStyle)->addText($entity->getNroVale(), $columnsFontStyle, $cellHCentered);
            $row->addCell(2000, $tableCellStyle)->addText($entity->getVehiculoid()->getMatricula(), $columnsFontStyle, $cellHCentered);
            $row->addCell(2000, $tableCellStyle)->addText($entity->getPersonaid()->getNombre(), $columnsFontStyle, $cellHCentered);
            $row->addCell(2000, $tableCellStyle)->addText('', $columnsFontStyle, $cellHCentered);
            $row->addCell(2000, $tableCellStyle)->addText('', $columnsFontStyle, $cellHCentered);
        }
        $section->addTextBreak();

        //Pie de Firma
        $pieFirma = $this->get('portadores.piefirma')->getPieFirmaDistribucionPhpWord(DocumentosEnum::entregachips, $unidad, $word, $section);

        return $word;
    }

    private function _writePhpWordDataSubmayor($title, $word, $anticipo)
    {

        $em = $this->getDoctrine()->getManager();
        $section = $word->addSection();
        $section->getStyle()->setPaperSize('Letter');
        $section->getStyle()->setOrientation('portrait');
        $section->getStyle()->setMarginLeft(800);
        $section->getStyle()->setMarginRight(800);
//        $section->getStyle()->setMarginTop(0);
//        $section->getStyle()->setMarginBottom(0);
        $section->setHeaderHeight(250);

        //Header
        $section->addHeader()->addImage($this->container->getParameter('kernel.project_dir') . '/web/assets/img/PNG/logo.png', [
            'width' => \PhpOffice\PhpWord\Shared\Converter::inchToPoint(1.90),
            'height' => \PhpOffice\PhpWord\Shared\Converter::inchToPoint(0.50),
            'scale' => 71,
            'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::START
        ]);

        //Datos
        $liquidaciones = $em->getRepository('PortadoresBundle:Liquidacion')->findByAnticipo($anticipo);

//        if(count($liquidaciones)==0)
//            throw new NotFoundHttpException('La Tarjeta no tiene chips entregados este mes.');


        //Stilo de Tabla
        $tableStyleName = 'Base Style';
        $tableStyle = array(
            'cellMarginTop' => 50,
            'borderSize' => 2,
//            'borderColor' => 'eeeeee',
            'spacing' => 0);

        $tableCellStyle = array('valign' => 'center');
        $titleFontStyle = array('bold' => true);
        $columnsFontStyle = array('size' => 7);
        $cellHCentered = array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceBefore' => 2, 'spaceAfter' => 2);
        $cellHCenteredTitle = array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceBefore' => 100, 'spaceAfter' => 100);
        $cellHCenteredColumns = array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceBefore' => 50, 'spaceAfter' => 50);
        $cellHCentered200 = array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceBefore' => 0, 'spaceAfter' => 0);
        $word->addTableStyle($tableStyleName, $tableStyle);

        //Titulo
//        $header = array('size' => 12, 'bold' => true,'underline' => \PhpOffice\PhpWord\Style\Font::UNDERLINE_SINGLE);
//        $section->addText('MODELO PARA SOLICITUD DE TRABAJOS A REALIZAR EN LA RED', $header, $cellHCentered);

        //Columns Title
        $table = $section->addTable($tableStyleName);
//        $table1 = $section->addTable($tableStyleName);

        $row = $table->addRow(450, array('exactHeight' => true));
        $row->addCell(10000, array('gridSpan' => 8));

        $row = $table->addRow(250, array('exactHeight' => true));
        $row->addCell(7000, array('gridSpan' => 5.1));
        $row->addCell(1250, array('gridSpan' => 1.2))->addText('No. DE FOLIO:', $columnsFontStyle, $cellHCentered200);
        $row->addCell(2500, array('gridSpan' => 2));

        $row = $table->addRow(260, array('exactHeight' => true));
        $row->addCell(10000, array('gridSpan' => 8))->addText('MODELO ENTREGA-LIQUIDACION y RESPONSABILIDAD MATERIAL DE TARJETA MAGNÉTICA (TM-1)', array('size' => 8, 'bold' => true, 'underline' => 'single'), $cellHCentered200);
        $row = $table->addRow(50, array('exactHeight' => true));
        $row->addCell(10000, array('gridSpan' => 8));

        $row = $table->addRow(250, array('exactHeight' => true));
        $row->addCell(6000, array('gridSpan' => 4))->addText('NUMERO DE TARJETA MAGNETICA:', $columnsFontStyle, $cellHCentered200);
        $row->addCell(4000, array('gridSpan' => 4))->addText($anticipo->getTarjeta()->getNroTarjeta(), $columnsFontStyle, $cellHCentered200);

        $row = $table->addRow(250, array('exactHeight' => true));
        $row->addCell(3750, array('gridSpan' => 3.25))->addText('TIPO DE COMBUSTIBLE:', $columnsFontStyle, $cellHCentered200);
        $row->addCell(3750, array('gridSpan' => 3.25))->addText(strtoupper($anticipo->getTarjeta()->getTipoCombustibleid()->getNombre()), array('size' => 9, 'bold' => true, ''), $cellHCentered200);
        $row->addCell(1250, array('gridSpan' => 0.75))->addText('PRECIO', $columnsFontStyle, $cellHCentered200);
        $row->addCell(1250, array('gridSpan' => 0.75))->addText($anticipo->getTarjeta()->getTipoCombustibleid()->getPrecio(), $columnsFontStyle, $cellHCentered200);

        $row = $table->addRow(250, array('exactHeight' => true));
        $row->addCell(3750, array('gridSpan' => 7))->addText('CODIGO DE LA ACTIVIDAD/SUBACTIVIDAD EN QUE SE CONSUME:', $columnsFontStyle, $cellHCentered200);
        $row->addCell(3750, array('gridSpan' => 1))->addText($anticipo->getVehiculo()->getActividad()->getCodigomep(), $columnsFontStyle, $cellHCentered200);

//        $row->addCell(2000, array())->addText('TARJETA No', $titleFontStyle, $cellHCenteredTitle);
//        $row->addCell(2000, array())->addText($entities[0]->getNtarjetaid()->getNroTarjeta(), array('bold' => true, 'size' => 8), ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER,'spaceBefore' => 150,'spaceAfter' => 150]);
//
//        $row = $table->addRow(400, array('exactHeight' => true));
//        $row->addCell(2000, $tableCellStyle)->addText('FECHA DE SERVICIO', $columnsFontStyle, $cellHCenteredColumns);
//        $row->addCell(2000, $tableCellStyle)->addText('FECHA DE ENTREGA DEL CHIP', $columnsFontStyle, $cellHCenteredColumns);
//        $row->addCell(1000, $tableCellStyle)->addText('TIPO DE COMBUSTIBLE', $columnsFontStyle, $cellHCenteredColumns);
//        $row->addCell(1250, $tableCellStyle)->addText('No DEL CHIP', $columnsFontStyle, $cellHCenteredColumns);
//        $row->addCell(1250, $tableCellStyle)->addText('MATRICULA', $columnsFontStyle, $cellHCenteredColumns);
//        $row->addCell(3500, $tableCellStyle)->addText('NOMBRE DEL CHOFER', $columnsFontStyle, $cellHCenteredColumns);
//        $row->addCell(2000, $tableCellStyle)->addText('FIRMA DEL CHOFER', $columnsFontStyle, $cellHCenteredColumns);
//        $row->addCell(2000, $tableCellStyle)->addText('FIRMA DEL QUE RECIBE', $columnsFontStyle, $cellHCenteredColumns);
//
//        //Rows
//        foreach ($entities as $entity) {
//            $row = $table->addRow(300, array('exactHeight' => true));
//            $row->addCell(2000, $tableCellStyle)->addText($entity->getFechaVale()->format('d/m/Y'), $columnsFontStyle, $cellHCentered);
//            $row->addCell(2000, $tableCellStyle)->addText($entity->getFechaRegistro()->format('d/m/Y'), $columnsFontStyle, $cellHCentered);
//            $row->addCell(2000, $tableCellStyle)->addText($entity->getTarjetaid()->getTipoCombustibleid()->getNombre(), $columnsFontStyle, $cellHCentered);
//            $row->addCell(2000, $tableCellStyle)->addText($entity->getNroVale(), $columnsFontStyle, $cellHCentered);
//            $row->addCell(2000, $tableCellStyle)->addText($entity->getVehiculoid()->getMatricula(), $columnsFontStyle, $cellHCentered);
//            $row->addCell(2000, $tableCellStyle)->addText($entity->getPersonaid()->getNombre(), $columnsFontStyle, $cellHCentered);
//            $row->addCell(2000, $tableCellStyle)->addText('', $columnsFontStyle, $cellHCentered);
//            $row->addCell(2000, $tableCellStyle)->addText('', $columnsFontStyle, $cellHCentered);
//        }
//        $section->addTextBreak();

//        Pie de Firma
//        $pieFirma = $this->get('portadores.piefirma')->getPieFirmaDistribucionPhpWord(DocumentosEnum::entregachips, $word, $section);

        return $word;
    }

    public function loadTarjetaComboAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $nunidadid = $request->get('unidadid');
        $mes = $request->get('mes');
        $anno = $request->get('anno');

        if (\is_null($mes) && \is_null($anno)) {
            $fecha = new \DateTime();
            $anno = $fecha->format('Y');
            $mes = $fecha->format('n');
        }

        $fechaDesde = $anno . '-' . $mes . '-' . '1';
        $fechaHasta = FechaUtil::getUltimoDiaMes($mes, $anno);

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $entities = $em->getRepository('PortadoresBundle:Anticipo')->buscarTarjetaCombo($_unidades, $fechaDesde, $fechaHasta);

        $_data = array();
        /** @var Tarjeta $entity */
        foreach ($entities as $entity) {
            $_data[] = array(
                'ntarjetaid' => $entity['tarjeta_id'],
                'ntarjetaidnro' => $entity['nro_tarjeta'],
                'tipo_combustible_id' => $entity['tipo_combustible_id'],
            );
        }

        return new JsonResponse(array('rows' => $_data));
    }

    public function loadTarjetaAnticipoComboAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $nunidadid = $request->get('unidadid');
        $mes = $request->get('mes');
        $anno = $request->get('anno');

        $fechaDesde = $anno . '-' . $mes . '-01 00:00:00';
        $fechaHasta = FechaUtil::getUltimoDiaMes($mes, $anno) . ' 23:59:59';

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $entities = $em->getRepository('PortadoresBundle:Anticipo')->buscarTarjetaAnticipoCombo($_unidades, $fechaDesde, $fechaHasta);

        $_data = array();
        /** @var Tarjeta $entity */
        foreach ($entities as $entity) {
            $_data[] = array(
                'ntarjetaid' => $entity['tarjeta_id'],
                'ntarjetaidnro' => $entity['nro_tarjeta'],
                'tipo_combustible_id' => $entity['tipo_combustible_id'],
            );
        }

        return new JsonResponse(array('rows' => $_data));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Mpdf\MpdfException
     */
    public function toPDFAnticipoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $anticipo_id = $request->get('anticipo_id');

        /** @var Anticipo $anticipo */
        $anticipo = $em->getRepository('PortadoresBundle:Anticipo')->find($anticipo_id);
        $tarjeta_id = $anticipo->getTarjeta()->getId();
        $persona_id = $anticipo->getNpersonaid()->getId();

        $liquidaciones = $em->getRepository('PortadoresBundle:Liquidacion')->findBy(array('anticipo' => $anticipo->getId(), 'visible' => true));

        $saldo_incial = $liquidaciones[0]->getImporteInicial();
        $saldo_litros = $liquidaciones[0]->getImporteInicial() * ($liquidaciones[0]->getCantLitros() / $liquidaciones[0]->getImporte());

        $total_liquidado_importe = 0;
        $total_liquidado_litros = 0;
        foreach ($liquidaciones as $liquidacion) {
            $total_liquidado_importe += $liquidacion->getImporte();
            $total_liquidado_litros += $liquidacion->getCantLitros();
        }

        /** @var ActaRespMaterial $acta_resposnabilidad_recibe */
        $conn = $this->get('database_connection');

        $fecha_abierto = $anticipo->getFecha()->format('Y-m-d');
        $fecha_cierre = $anticipo->getFechaCierre()->format('Y-m-d');

//        var_dump($tarjeta_id);
//        var_dump($fecha_abierto);
//        var_dump($fecha_cierre);
//        var_dump($persona_id);

        $acta_1 = $conn->fetchAll("SELECT r.fecha, r.tarjeta, r.entregaid, r.recibeid
        FROM datos.acta_resp_material as r 
        where r.tarjeta like '%$tarjeta_id%' 
        and r.fecha >= '$fecha_abierto'
        and r.recibeid = '$persona_id' ORDER BY r.fecha limit 1;");

        $acta_2 = $conn->fetchAll("SELECT r.fecha, r.tarjeta, r.entregaid, r.recibeid
        FROM datos.acta_resp_material as r 
        where r.tarjeta like '%$tarjeta_id%' 
        and r.fecha >= '$fecha_abierto'
        and r.entregaid = '$persona_id' ORDER BY r.fecha limit 1;");


        $entrega_acta1 = count($acta_1) > 0 ? $em->getRepository('PortadoresBundle:Persona')->find($acta_1[0]['entregaid'])->getNombre() : '';
        $recibe_acta1 = count($acta_1) > 0 ? $em->getRepository('PortadoresBundle:Persona')->find($acta_1[0]['recibeid'])->getNombre() : '';
        $fecha_acta1 = count($acta_1) > 0 ? $acta_1[0]['fecha'] : '';

        $entrega_acta2 = count($acta_2) > 0 ? $em->getRepository('PortadoresBundle:Persona')->find($acta_2[0]['entregaid'])->getNombre() : '';
        $recibe_acta2 = count($acta_2) > 0 ? $em->getRepository('PortadoresBundle:Persona')->find($acta_2[0]['recibeid'])->getNombre() : '';
        $fecha_acta2 = count($acta_2) > 0 ? $acta_2[0]['fecha'] : '';

//        $fecha_recibe = null;
//        $nombre_recibe = null;
//        $nombre_entrega = null;
//        if (!empty($acta_resposnabilidad_recibe[0])) {
//            $fecha_recibe = $acta_resposnabilidad_recibe[0]->getFecha()->format('d/m/Y');
//            $nombre_recibe = $acta_resposnabilidad_recibe[0]->getRecibeid()->getNombre();
//            $nombre_entrega = $acta_resposnabilidad_recibe[0]->getEntregaid()->getNombre();
//        }
//
//        $fecha_recibe_entrega = null;
//        $nombre_recibe_entrega = null;
//        $nombre_entrega_entrega = null;
//        if (!empty($acta_resposnabilidad_recibe[0])) {
//            $fecha_recibe_entrega = $acta_resposnabilidad_entrega[0]->getFecha()->format('d/m/Y');
//            $nombre_entrega_entrega = $acta_resposnabilidad_entrega[0]->getRecibeid()->getNombre();
//            $nombre_recibe_entrega = $acta_resposnabilidad_entrega[0]->getEntregaid()->getNombre();
//        }

//        $historial = $em->getRepository('PortadoresBundle:HistorialTarjeta')->findBy(array('anticipo'=>$anticipo_id),array('fecha'=>'ASC'));
        /** @var Liquidacion $liquidacion */
        $liquidacion = $em->getRepository('PortadoresBundle:Liquidacion')->findBy(array('anticipo' => $anticipo_id), array('fechaVale' => 'ASC'));

        $saldoTarjetaEntregada = $liquidacion[0]->getImporteInicial();
        $cantidadTarjetaEntregada = $liquidacion[0]->getImporteInicial() * ($liquidacion[0]->getCantLitros() / $liquidacion[0]->getImporte());

        $detalle_gasto = $em->getRepository('PortadoresBundle:DetalleGasto')->findOneBy(array('unidad' => $anticipo->getTarjeta()->getUnidadid(), 'ntipoCombustibleid' => $anticipo->getTarjeta()->getTipoCombustibleid(), 'moneda' => $anticipo->getTarjeta()->getMonedaid(), 'visible' => true));
        $elemento_gasto = $em->getRepository('PortadoresBundle:ElementoGasto')->findOneBy(array('unidad' => $anticipo->getTarjeta()->getUnidadid(), 'moneda' => $anticipo->getTarjeta()->getMonedaid(), 'visible' => true));
        if ($detalle_gasto && $elemento_gasto) {
            /** @var CuentaGasto $cuenta_gasto */
            $cuenta_gasto = $em->getRepository('PortadoresBundle:CuentaGasto')->findOneBy(array('detalleGasto' => $detalle_gasto, 'elementoGasto' => $elemento_gasto, 'centroCosto' => $anticipo->getTarjeta()->getCentrocosto(), 'visible' => true));
        }


        $total_cantidad = 0;
        $total_saldo = 0;
        /** @var Liquidacion $liq */
        foreach ($liquidacion as $liq) {
            $total_cantidad += $liq->getCantLitros();
            $total_saldo += $liq->getImporte();
        }

        $cuenta_anticipo = $anticipo->getTarjeta()->getMonedaid()->getId() == MonedaEnum::cup ? '162101' : '162201';
        $cuenta_anticipo_efectivo = $anticipo->getTarjeta()->getMonedaid()->getId() == MonedaEnum::cup ? '102700' : '103700';
        $nro_cuenta_gasto = empty($cuenta_gasto) == true ? '' : $cuenta_gasto->getNoCuenta();
        $_html = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\">
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
<title>Documento sin título</title>
<style type=\"text/css\">
<!--
.center_text {
	text-align: center;
	font-weight: bold;
}
.left_text {
	text-align: left;
	font-weight: bold;
}
.rigth_text {
	text-align: right;
	font-weight: bold;
}
.word_bold {
	font-weight: bold;
}

.bordes_tablas
{
	border-bottom:1px solid #999;
	border-left:1px solid #999;
	border-right:1px solid #999;
	border-top:1px solid #999;
}

.bordes_izquierdo_arriba_derecho
{
	border-right:1px solid #999;
	border-top:1px solid #999;
	border-left:1px solid #999
}
.bordes_derecho_abajo
{
	border-right:1px solid #999;
	border-bottom:1px solid #999;
}
.bordes_derecho
{
	border-right:1px solid #999;
}

.bordes_abajo
{
	border-bottom:1px solid #999;
}
.bordes_arriba_derecho_abajo
{
	border-top:1px solid #999;
	border-right:1px solid #999;
	border-bottom:1px solid #999;
}
.bordes_derecho_abajo_izquierda
{
	border-right:1px solid #999;
	border-bottom:1px solid #999;
	border-left:1px solid #999;
	font-weight: bold;
}
.ancho{
	height: 10px;
	}
	.saltoDePagina{
				display:block;
				page-break-before:always;
			}
			
			td{
	font-size:10px;
	text-align: center;
	padding:2px
				}
				p{
	font-size:12px;
	text-align: left;
}

header {
    width: 100%;
    margin-bottom: 10px!important;
}

header > img {
    margin: 0 35px;
}
					
-->
</style>
</head>
<body>
<header>
    <img  src='../../assets/img/PNG/logo.png' height='60px' width='160px'>
</header>
<table width=\"90%\" style=\"margin-right:15px; margin-left:35px\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
  
 
  
  <tr>
    <th colspan=\"8\" scope=\"col\" class=\"bordes_izquierdo_arriba_derecho ancho\">&nbsp;</th>
  </tr>
  <tr>
    <td colspan=\"6\" class=\"bordes_derecho_abajo_izquierda\">&nbsp;</td>
    <td class=\"bordes_tablas\"><strong>No. DE FOLIO:</strong></td>
    <td class=\"bordes_arriba_derecho_abajo\">&nbsp;</td>
  </tr>
  <tr> 
    <td colspan=\"8\"  class=\"center_text bordes_derecho_abajo_izquierda\">MODELO ENTREGA-LIQUIDACION y RESPONSABILIDAD MATERIAL DE TARJETA MAGNETICA (TM-1)</td>
  </tr>
  <tr>
    <td colspan=\"4\" class=\"center_text bordes_derecho_abajo_izquierda\">NÚMERO DE TARJETA MAGNETICA</td>
    <td  class=\"bordes_derecho_abajo_izquierda\" colspan=\"4\">" . $anticipo->getTarjeta()->getNroTarjeta() . "</td>
  </tr>
  <tr>
    <td colspan=\"3\" class=\"center_text bordes_derecho_abajo_izquierda\">TIPO COMBUSTIBLE</td>
    <td class=\"bordes_derecho_abajo\" colspan=\"3\">" . $anticipo->getTarjeta()->getTipoCombustibleid()->getNombre() . "</td>
    <td class=\"center_text bordes_derecho_abajo\">PRECIO</td>
    <td class=\"center_text bordes_derecho_abajo\">" . $anticipo->getTarjeta()->getTipoCombustibleid()->getPrecio() . "</td>
  </tr>
  <tr>
    <td colspan=\"6\" class=\"center_text bordes_derecho_abajo_izquierda\">CÓDIGO DE LA ACTIVIDAD/SUBACTIVIDAD E QUE SE CONSUME</td>
    <td colspan=\"2\"  class=\"center_text bordes_derecho_abajo\">" . $anticipo->getVehiculo()->getActividad()->getCodigomep() . "</td>
  </tr>
  <tr>
    <td class=\"bordes_derecho_abajo_izquierda\" colspan=\"8\">&nbsp;</td>
  </tr>
  <tr>
    <td colspan=\"4\" class=\"center_text bordes_derecho_abajo_izquierda\">DATOS DEL USUARIO DE LA TARJETA MAGNÉTICA</td>
    <td colspan=\"4\" class=\"center_text bordes_derecho_abajo\">DATOS DEL VEHÍCULO A QUE SE DESTINA LA TARJETA <br />MAGNÉTICA</td>
  </tr>
  <tr>
    <td class=\"center_text bordes_derecho_abajo_izquierda\">ANALISIS EN<br /> SUBMAYOR</td>
    <td colspan=\"3\" class=\"center_text bordes_derecho_abajo\">NOMBRE</td>
    <td class=\"center_text bordes_derecho_abajo\">MARCA</td>
    <td class=\"center_text bordes_derecho_abajo\">TIPO DE <br />VEHÍCULO</td>
    <td class=\"center_text bordes_derecho_abajo\">CHAPA</td>
    <td class=\"center_text bordes_derecho_abajo\">ÍNDICE <br />CONSUMO</td>
  </tr>
  <tr>
    <td class=\"center_text bordes_derecho_abajo_izquierda\">" . substr($anticipo->getNpersonaid()->getCi(), 6, 10) . "</td>
    <td colspan=\"3\" class=\"center_text bordes_derecho_abajo\">" . $anticipo->getNpersonaid()->getNombre() . "</td>
    <td class=\"center_text bordes_derecho_abajo\">" . $anticipo->getVehiculo()->getNmodeloid()->getMarcaVehiculoid()->getNombre() . "</td>
    <td class=\"center_text bordes_derecho_abajo\">" . $anticipo->getVehiculo()->getNdenominacionVehiculoid()->getNombre() . "</td>
    <td class=\"center_text bordes_derecho_abajo\">" . $anticipo->getVehiculo()->getMatricula() . "</td>
    <td class=\"center_text bordes_derecho_abajo\">" . number_format($anticipo->getVehiculo()->getNorma(), 2) . "</td>
  </tr>
  <tr>
    <td  class=\"center_text bordes_derecho_abajo_izquierda\">ÁREA DE TRABAJO</td>
    <td  class=\"center_text bordes_derecho_abajo\" colspan=\"3\">" . $anticipo->getVehiculo()->getArea()->getNombre() . "</td>
    <td colspan=\"2\" class=\"center_text bordes_derecho_abajo\">CENTRO DE COSTO</td>
    <td colspan=\"2\" class=\"center_text bordes_derecho_abajo\">" . $anticipo->getTarjeta()->getCentrocosto()->getCodigo() . "</td>
  </tr>
  <tr>
    <td class=\"center_text bordes_derecho_abajo_izquierda\" colspan=\"4\">&nbsp;</td>
    <td colspan=\"2\" class=\"center_text bordes_derecho_abajo\" >LITROS</td>
    <td colspan=\"2\" class=\"center_text bordes_derecho_abajo\" >IMPORTES</td>
  </tr>
  <tr>
    <td colspan=\"4\" class=\"left_text bordes_derecho_abajo_izquierda\">SALDO EN TARJETA RECARGADA</td>
    <td colspan=\"2\" class=\"center_text bordes_derecho_abajo\" >" . number_format($cantidadTarjetaEntregada, 2) . "</td>
    <td colspan=\"2\" class=\"center_text bordes_derecho_abajo\" >" . number_format($saldoTarjetaEntregada, 2) . "</td>
  </tr>
  <tr>
    <td colspan=\"4\" class=\"bordes_derecho_abajo_izquierda\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; DE ELLO AUTORIZADO A CONSUMIR</td>
    <td colspan=\"2\" class=\"center_text bordes_derecho_abajo\">" . number_format($anticipo->getCantidad(), 2) . "</td>
    <td colspan=\"2\" class=\"center_text bordes_derecho_abajo\">" . number_format($anticipo->getImporte(), 2) . "</td>
  </tr>
  <tr>
    <td colspan=\"4\" class=\"bordes_derecho_abajo_izquierda\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; CONSUMO EJECUTADO DE COMBUSTIBLE (&quot;--1---&quot;)   </td>
    <td colspan=\"2\" class=\"center_text bordes_derecho_abajo\">" . number_format($total_cantidad, 2) . "</td>
    <td colspan=\"2\" class=\"center_text bordes_derecho_abajo\">" . number_format($total_saldo, 2) . "</td>
  </tr>
  <tr>
    <td colspan=\"4\" class=\"bordes_derecho_abajo_izquierda\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; SALDO FINAL ENTREGADO A LA CAJA (&quot;--2---&quot;)</td>
    <td colspan=\"2\"  class=\"center_text bordes_derecho_abajo\">" . number_format($cantidadTarjetaEntregada - $total_cantidad, 2) . "</td>
    <td colspan=\"2\"  class=\"center_text bordes_derecho_abajo\">" . number_format($saldoTarjetaEntregada - $total_saldo, 2) . "</td>
  </tr>
  <tr>
    <td colspan=\"8\" class=\"bordes_derecho_abajo_izquierda\">&nbsp;</td>
  </tr>
  <tr>
    <td colspan=\"4\" class=\"left_text bordes_derecho_abajo_izquierda\">ANTICIPO RECIBIDO POR:</td>
    <td colspan=\"4\" class=\"left_text bordes_derecho_abajo\">ANTICIPO ENTREGADO DE LA CAJA POR:</td>
  </tr>
  <tr>
    <td class=\"center_text bordes_derecho_abajo_izquierda\">" . $fecha_acta1 . "</td>
    <td colspan=\"2\" class=\"center_text bordes_derecho_abajo\">" . $recibe_acta1 . "</td>
    <td class=\"center_text bordes_derecho_abajo\">&nbsp;</td>
    <td colspan=\"3\" class=\"center_text bordes_derecho_abajo\">" . $entrega_acta1 . "</td>
    <td class=\"center_text bordes_derecho_abajo\">&nbsp;</td>
  </tr>
  <tr>
    <td class=\"center_text bordes_derecho_abajo_izquierda\">Fecha</td>
    <td colspan=\"2\" class=\"center_text bordes_derecho_abajo\">Nombre y Apellidos</td>
    <td class=\"center_text bordes_derecho_abajo\">Firma</td>
    
    <td colspan=\"3\" class=\"center_text bordes_derecho_abajo\">Nombre y Apellidos</td>
    <td class=\"center_text bordes_derecho_abajo\">Firma</td>
  </tr>
  <tr>
    <td colspan=\"8\" class=\"bordes_derecho_abajo_izquierda\">&nbsp;</td>
  </tr>
  <tr>
    <td colspan=\"4\" class=\"left_text bordes_derecho_abajo_izquierda\">ANTICIPO LIQUIDADO POR:</td>
    <td colspan=\"4\" class=\"left_text bordes_derecho_abajo\">LIQUIDACION RECIBIDA EN LA CAJA POR:</td>
  </tr>
  <tr>
    <td class=\"bordes_derecho_abajo_izquierda\">" . $fecha_acta2 . "</td>
    <td colspan=\"2\" class=\"bordes_derecho_abajo\">" . $entrega_acta2 . "</td>
    <td class=\"bordes_derecho_abajo\">&nbsp;</td>
    <td colspan=\"3\" class=\"bordes_derecho_abajo\">" . $recibe_acta2 . "</td>
    <td class=\"bordes_derecho_abajo\">&nbsp;</td>
  </tr>
  <tr>
    <td class=\"center_text bordes_derecho_abajo_izquierda\">Fecha</td>
    <td colspan=\"2\" class=\"center_text bordes_derecho_abajo\">Nombre y Apellidos</td>
    <td class=\"center_text bordes_derecho_abajo\">Firma</td>
    
    <td colspan=\"3\" class=\"center_text bordes_derecho_abajo\">Nombre y Apellidos</td>
    <td class=\"center_text bordes_derecho_abajo\">Firma</td>
  </tr>
  <tr>
    <td class=\"bordes_derecho_abajo_izquierda\" colspan=\"8\">
    <p class=\"center_text\"><strong>ACTA DE RESPONSABILIDAD MATERIAL (TM-3)</strong></p>
    <p>El que suscribe y firma como Receptor del Anticipo</p>
    <p>Para dar cumplimiento al procedimiento a seguir en cuanto a las indemnizaciones por la aplicacion del Decreto Ley No 249 sobre responsabilidad material a los dirigentes, funcionarios y demas trabajadores, en el momento de recibir el anticipo lo asume como <span class=\"word_bold\">Acta de Responsabilidad Material</span>, lo cual lleva implicito la responsabilidad que asume por la custodia y cuidado de la tarjeta magnética de combustible que se entrega a traves de MODELO ENTREGA-LIQUIDACION TARJETA MAGNETICA y mediante los documentos oficiales establecidos que se encuentran depositados en la caja fuerte de la empresa. Conociendo que de producirse la perdida o extravío me sera aplicado lo establecido en el Decreto Ley No 249. De igual forma acepto que soy responsable de todas las transacciones que se realicen con la misma en el periodo que esta bajo su custodia la Tarjeta Magnética.</p>
    <p>Conociendo que la tarjeta no debe ser expuesta a la humedad, altas temperaturas, campos magneticos, sufrir dobleces y sudor y velaré porque quien la use en el ServiCentro no la retire del lector de tarjeta hasta que concluya la operacion.</p>
<p>    Para su liquidacion mensual, el penultimo dia del mes, se presentara el original moelo de la entrega y liquidacion de tarjetas magneticas de combustible, debidamente firmado, acompañado de los justificantes (Chips), que en su parte posterior se dejadra constancia con la firma del chofer, el numero de la chapa y marca del veh;iculo, y en el cual tambien debe ser visible la cantidad abastecida y fecha en que s erealizaró.</p>
<p>    Y para que asi conste, funge como valida para esta Acta, la firma arriba plasmada y como periodo de responsabilidad la fecha obicada entre la firma de Recibido por el Usuario del Anticipo y su posterior Liquidacion del Anticipo a la Caja.</p>
    </td>
  </tr>
  <tr>
    <td colspan=\"4\" class=\"center_text bordes_derecho_abajo_izquierda\">Registro Contable en el momento de Entrega</td>
    <td colspan=\"4\" class=\"center_text bordes_derecho_abajo\">Registro Contable en el momento de Liquidación  </td>
  </tr>
  <tr>
    <td colspan=\"2\" class=\"center_text bordes_derecho_abajo_izquierda\">Cuentas</td>
    <td class=\"center_text bordes_derecho_abajo\">Debito</td>
    <td class=\"center_text bordes_derecho_abajo\">Credito</td>
    <td colspan=\"2\" class=\"center_text bordes_derecho_abajo\">Cuentas</td>
    <td class=\"center_text bordes_derecho_abajo\">Debito</td>
    <td class=\"center_text bordes_derecho_abajo\">Crédito</td>
  </tr>
  <tr>
    <td class=\"center_text bordes_derecho_abajo_izquierda\">" . $cuenta_anticipo . "</td>
    <td class=\"center_text bordes_derecho_abajo\">" . substr($anticipo->getNpersonaid()->getCi(), 6, 10) . "</td>
    <td class=\"center_text bordes_derecho_abajo\">" . number_format($total_saldo, 2) . "</td>
    <td class=\"center_text bordes_derecho_abajo\">&nbsp;</td>
    <td class=\"center_text bordes_derecho_abajo\">" . $cuenta_anticipo_efectivo . "</td>
    <td class=\"center_text bordes_derecho_abajo\">&nbsp;</td>
    <td  class=\"center_text bordes_derecho_abajo\">0.00</td>
    <td  class=\"center_text bordes_derecho_abajo\">&nbsp;</td>
  </tr>
  <tr>
    <td class=\"center_text bordes_derecho_abajo_izquierda\">&nbsp;" . $cuenta_anticipo_efectivo . "</td>
    <td class=\"center_text bordes_derecho_abajo\">&nbsp;</td>
    <td class=\"center_text bordes_derecho_abajo\">&nbsp;</td>
    <td class=\"center_text bordes_derecho_abajo\">" . number_format($total_saldo, 2) . "</td>
    <td colspan=\"2\" class=\"center_text bordes_derecho_abajo\">" . $nro_cuenta_gasto . "</td>
    <td class=\"center_text bordes_derecho_abajo\">" . number_format($total_saldo, 2) . "</td>
    <td class=\"center_text bordes_derecho_abajo\">&nbsp;</td>
  </tr>
  <tr>
    <td class=\"center_text bordes_derecho_abajo_izquierda\">Registro</td>
    <td class=\"center_text bordes_derecho_abajo\"></td>
    <td class=\"center_text bordes_derecho_abajo\">Comp:</td>
    <td class=\"center_text bordes_derecho_abajo\">&nbsp;</td>
    <td class=\"center_text bordes_derecho_abajo\">" . $cuenta_anticipo . "</td>
    <td class=\"center_text bordes_derecho_abajo\">" . substr($anticipo->getNpersonaid()->getCi(), 6, 10) . "</td>
     <td class=\"center_text bordes_derecho_abajo\">&nbsp;</td>
     <td class=\"center_text bordes_derecho_abajo\">" . number_format($total_saldo, 2) . "</td>
  </tr>
  <tr>
    <td colspan=\"8\">Carretera Vieja de Guanabacoa y Línea de Ferrocarril, Guanabacoa, La Habana, CUBA</td>
  </tr>
  <tr>
    <td colspan=\"8\">Tf:(53)-(7)-797 9255 y 797-0821/22 ext 102</td>
  </tr>
  <tr>
    <td colspan=\"8\">Email: direccion@cubahidraulica.cu &nbsp;&nbsp;&nbsp;  www.cubahidraulica.cu</td>
  </tr>
  
</table>




<div style='page-break-before:always; margin: 5px 0 10px 35px;'>
    <img  src='../../assets/img/PNG/logo.png' height='60px' width='160px'>
</div>

<table width=\"90%\" style=\"margin-right:15px; margin-left:35px;\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
<tr>
    <th colspan=\"8\" scope=\"col\" class=\"center_text\"></th>
  </tr><tr>
    <th colspan=\"8\" scope=\"col\" class=\"center_text\"></th>
  </tr><tr>
    <th colspan=\"8\" scope=\"col\" class=\"center_text\"></th>
  </tr><tr>
    <th colspan=\"8\" scope=\"col\" class=\"center_text\"></th>
  </tr><tr>
    <th colspan=\"8\" scope=\"col\" class=\"center_text\"></th>
  </tr><tr>
    <th colspan=\"8\" scope=\"col\" class=\"center_text\"></th>
  </tr>
  <tr>
    <th colspan=\"8\" scope=\"col\" class=\"center_text bordes_izquierdo_arriba_derecho\">SUBMAYOR CONTROL TARJETA MAGNÉTICA(TM-2)</th>
  </tr>
  <tr>
    <td colspan=\"4\" class=\"center_text bordes_tablas\">NÚMERO DE TARJETA MAGNÉTICA</td>
    <td colspan=\"4\" class=\"bordes_arriba_derecho_abajo\">" . $anticipo->getTarjeta()->getNroTarjeta() . "</td>
  </tr>
  <tr>
    <td colspan=\"3\" class=\"bordes_derecho_abajo_izquierda\">TIPO DE COMBUSTIBLE</td>
    <td colspan=\"3\" class=\"bordes_derecho_abajo\">" . $anticipo->getTarjeta()->getTipoCombustibleid()->getNombre() . "</td>
    <td class=\"bordes_derecho_abajo\">PRECIO</td>
    <td class=\"bordes_derecho_abajo\">" . $anticipo->getTarjeta()->getTipoCombustibleid()->getPrecio() . "</td>
  </tr>
  <tr>
    <td colspan=\"6\" class=\"bordes_derecho_abajo_izquierda\">CÓDIGO DE LA ACTIVIDAD/SUBACTIVIDAD EN QUE SE CONSUME</td>
    <td colspan=\"2\" class=\"bordes_derecho_abajo\">" . $anticipo->getVehiculo()->getActividad()->getCodigomep() . "</td>
  </tr>
  <tr>
    <td colspan=\"4\" class=\"bordes_derecho_abajo_izquierda\">DATOS DEL USUARIO DEE LA TARJETA MAGNÉTICA</td>
    <td colspan=\"4\" class=\"center_text bordes_derecho_abajo\">DATOS DEL VEHÍCULO A QUE SE DESTINA LA TARJETA <br />MAGNETICA</td>
  </tr>
  <tr>
    <td class=\"bordes_derecho_abajo_izquierda\">ANÁLISIS EN <br />SUBMAYOR</td>
    <td colspan=\"3\" class=\"center_text bordes_derecho_abajo\">NOMBRE</td>
    <td class=\"center_text bordes_derecho_abajo\">MARCA</td>
    <td class=\"center_text bordes_derecho_abajo\">TIPO DE <br />VEHÍCULO</td>
    <td class=\"center_text bordes_derecho_abajo\">CHAPA</td>
    <td class=\"center_text bordes_derecho_abajo\">ÍNDICE DE <br />CONSUMO</td>
  </tr>
  <tr>
    <td class=\"bordes_derecho_abajo_izquierda\">" . substr($anticipo->getNpersonaid()->getCi(), 6, 10) . "</td>
    <td colspan=\"3\" class=\"bordes_derecho_abajo\">" . $anticipo->getNpersonaid()->getNombre() . "</td>
    <td class=\"bordes_derecho_abajo\">" . $anticipo->getVehiculo()->getNmodeloid()->getMarcaVehiculoid()->getNombre() . "</td>
    <td class=\"bordes_derecho_abajo\">" . $anticipo->getVehiculo()->getNdenominacionVehiculoid()->getNombre() . "</td>
    <td class=\"bordes_derecho_abajo\">" . $anticipo->getVehiculo()->getMatricula() . "</td>
    <td class=\"bordes_derecho_abajo\">" . number_format($anticipo->getVehiculo()->getNorma(), 2) . "</td>
  </tr>
  <tr>
    <td class=\"bordes_derecho_abajo_izquierda\">ÁREA DE <br />TRABAJO</td>
    <td colspan=\"3\" class=\"bordes_derecho_abajo\">" . $anticipo->getVehiculo()->getArea()->getNombre() . "</td>
    <td colspan=\"2\" class=\"center_text bordes_derecho_abajo\">CENTRO DE COSTO</td>
    <td colspan=\"2\" class=\"bordes_derecho_abajo\">" . $anticipo->getTarjeta()->getCentrocosto()->getCodigo() . "</td>
  </tr>
  <tr>
    <td colspan=\"8\" class=\"bordes_tablas\">&nbsp;</td>
  </tr>
  <tr>
    <td rowspan=\"2\" class=\"bordes_derecho_abajo_izquierda\">Fecha</td>
    <td rowspan=\"2\" class=\"center_text bordes_derecho_abajo\">Documento</td>
    <td colspan=\"2\" class=\"center_text bordes_derecho_abajo\">Entradas</td>
    <td colspan=\"2\" class=\"center_text bordes_derecho_abajo\">Salidas</td>
    <td colspan=\"2\" class=\"center_text bordes_derecho_abajo\">Saldo</td>
  </tr>
  <tr>
    <td class=\"center_text bordes_derecho_abajo\">Fisico</td>
    <td class=\"center_text bordes_derecho_abajo\">Valor</td>
    <td class=\"center_text bordes_derecho_abajo\">Fisico</td>
    <td class=\"center_text bordes_derecho_abajo\">Valor</td>
    <td class=\"center_text bordes_derecho_abajo\">Fisico</td>
    <td class=\"center_text bordes_derecho_abajo\">Valor</td>
  </tr>
 
";

        $_html .= "
         <tr>
    <td class=\"bordes_derecho_abajo_izquierda\">" . $liquidacion[0]->getFechaVale()->format('d/m/Y') . "</td>
    <td class=\"bordes_derecho_abajo\"></td>
    <td class=\"bordes_derecho_abajo\">&nbsp;</td>
    <td class=\"bordes_derecho_abajo\" style='text-align: right;'>0.00</td>
    <td class=\"bordes_derecho_abajo\" style='text-align: right;'></td>
    <td class=\"bordes_derecho_abajo\" style='text-align: right;'>0.00</td>
    <td class=\"bordes_derecho_abajo\" style='text-align: right;'>" . number_format($liquidacion[0]->getImporteInicial() / ($liquidacion[0]->getImporte() / $liquidacion[0]->getCantLitros()), 2) . "</td>
    <td class=\"bordes_derecho_abajo\" style='text-align: right;'>" . number_format($liquidacion[0]->getImporteInicial(), 2) . "</td>
  </tr>

        ";

        $suma_salida_fisico = 0;
        $suma_salida_valor = 0;

        $cantidad = 37 - count($liquidacion);

        foreach ($liquidacion as $liq) {
            $suma_salida_fisico += $liq->getCantLitros();
            $suma_salida_valor += $liq->getImporte();
            $_html .= "
         <tr>
    <td class=\"bordes_derecho_abajo_izquierda\">" . $liq->getFechaVale()->format('d/m/Y') . "</td>
    <td class=\"bordes_derecho_abajo\">" . $liq->getNroVale() . "</td>
    <td class=\"bordes_derecho_abajo\">&nbsp;</td>
    <td class=\"bordes_derecho_abajo\" style='text-align: right;'>0.00</td>
    <td class=\"bordes_derecho_abajo\" style='text-align: right;'>" . number_format($liq->getCantLitros(), 2) . "</td>
    <td class=\"bordes_derecho_abajo\" style='text-align: right;'>" . number_format($liq->getImporte(), 2) . "</td>
    <td class=\"bordes_derecho_abajo\" style='text-align: right;'>" . number_format($liq->getImporteFinal() / ($liq->getImporte() / $liq->getCantLitros()), 2) . "</td>
    <td class=\"bordes_derecho_abajo\" style='text-align: right;'>" . number_format($liq->getImporteFinal(), 2) . "</td>
  </tr>

        ";

        }

        for ($i = 0; $i < $cantidad; $i++) {
            $_html .= "
          <tr>
    <td class=\"bordes_derecho_abajo_izquierda\">&nbsp;</td>
    <td class=\"bordes_derecho_abajo\">&nbsp;</td>
    <td class=\"bordes_derecho_abajo\">&nbsp;</td>
    <td class=\"bordes_derecho_abajo\" style='text-align: right;'> 0.00&nbsp;</td>
    <td class=\"bordes_derecho_abajo\" style='text-align: right;'>&nbsp;</td>
    <td class=\"bordes_derecho_abajo\" style='text-align: right;'>&nbsp;0.00</td>
    <td class=\"bordes_derecho_abajo\" style='text-align: right;'>&nbsp;</td>
    <td class=\"bordes_derecho_abajo\" style='text-align: right;'>&nbsp;</td>
  </tr> 
     ";
        }

        $_html .= "   
 
  <tr>
    <td class=\"bordes_derecho_abajo_izquierda\">&nbsp;</td>
    <td class=\"center_text bordes_derecho_abajo\">Saldo Final <br />Submayor</td>
    <td class=\"bordes_derecho_abajo\" style='text-align: right;'>0.00</td>
    <td class=\"bordes_derecho_abajo\" style='text-align: right;'>0.00</td>
    <td class=\"bordes_derecho_abajo\" style='text-align: right;'>" . number_format($suma_salida_fisico, 2) . "</td>
    <td class=\"bordes_derecho_abajo\" style='text-align: right;'>" . number_format($suma_salida_valor, 2) . "</td>
    <td class=\"bordes_derecho_abajo\" style='text-align: right;'>" . number_format($liquidacion[count($liquidacion) - 1]->getImporteFinal() / ($liquidacion[count($liquidacion) - 1]->getImporte() / $liquidacion[count($liquidacion) - 1]->getCantLitros()), 2) . "</td>
    <td class=\"bordes_derecho_abajo\" style='text-align: right;'>" . $liquidacion[count($liquidacion) - 1]->getImporteFinal() . "</td>
  </tr>
  <tr>
    <td colspan=\"8\">Carretera Vieja de Guanabacoa y Línea de Ferrocarril, Guanabacoa, La Habana, CUBA</td>
  </tr>
  <tr>
    <td colspan=\"8\">Tf:(53)-(7)-797 9255 y 797-0821/22 ext 102</td>
  </tr>
  <tr>
    <td colspan=\"8\">Email: direccion@cubahidraulica.cu &nbsp;&nbsp;&nbsp;  www.cubahidraulica.cu</td>
  </tr>
</table>


</body>
</html>
        ";

        return new JsonResponse(['success' => true, 'html' => $_html]);
    }
}