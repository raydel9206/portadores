<?php

namespace Geocuba\PortadoresBundle\Controller;

use Doctrine\Common\Util\Debug;
use Geocuba\AdminBundle\EventListener\DoctrineEventsSuscriber;
use Geocuba\AdminBundle\Util\FechaUtil;
use Geocuba\PortadoresBundle\Entity\Norma;
use Geocuba\PortadoresBundle\Entity\Traslado;
use Geocuba\PortadoresBundle\Entity\Vehiculo;
use Geocuba\PortadoresBundle\Entity\Chofer;
use Geocuba\PortadoresBundle\Entity\VehiculoPersona;
use Geocuba\PortadoresBundle\Entity\VehiculoTipoMantenimiento;
use Doctrine\ORM\Query\Expr\Join;
use Geocuba\PortadoresBundle\Util\RootEnum;
use PhpOffice\PhpSpreadsheet\Shared\OLE\PPS\Root;
use PhpParser\Node\Expr\Array_;
use Symfony\Component\HttpFoundation\Response;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\PortadoresBundle\Util\Utiles;

class VehiculoController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {

        $_nombre = trim($request->get('nombre'));
        $_tipoCombustible = trim($request->get('tipoCombustible'));
        $_tipoMedio = trim($request->get('tipoMedio'));
        $nunidadid = $request->get('unidadid');
        $start = $request->get('start');
        $limit = $request->get('limit');

        $_data = array();
        $em = $this->getDoctrine()->getManager();

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);


        $entities = $em->getRepository('PortadoresBundle:Vehiculo')->buscarVehiculo($_nombre, $_tipoCombustible, $_tipoMedio, $_unidades, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:Vehiculo')->buscarVehiculo($_nombre, $_tipoCombustible, $_tipoMedio, $_unidades, $start, $limit, true);

        foreach ($entities as $entity) {

            /**@var Vehiculo $entity */
            $firstPadre = $entity->getNunidadid()->getPadreid();
            if ($firstPadre && $firstPadre->getId() !== RootEnum::root) {
                $empresa = $firstPadre->getSiglas();
            } else {
                $empresa = $entity->getNunidadid()->getSiglas();
            }

            $actividadid = '-';
            $actividad = '-';
            if ($entity->getActividad()) {
                $actividadid = $entity->getActividad()->getId();
                $actividad = $entity->getActividad()->getNombre();
            }
            $_data[] = array(
                'id' => $entity->getId(),
                'matricula' => $entity->getMatricula(),
                'empresa' => $empresa,
                'norma' => round($entity->getNorma(), 2),
                'nro_orden' => $entity->getNroOrden(),
                'odometro' => $entity->getOdometro(),
                'norma_fabricante' => round($entity->getNormaFabricante(), 2),
                'factor' => round($entity->getFactor(), 2),
                'norma_far' => round($entity->getNormaFar(), 2),
                'norma_lubricante' => $entity->getNormaLubricante(),
                'nro_inventario' => $entity->getNroInventario(),
                'nro_serie_carreceria' => $entity->getNroSerieCarreceria(),
                'nro_serie_motor' => $entity->getNroSerieMotor(),
                'color' => $entity->getColor(),
                'nro_circulacion' => $entity->getNroCirculacion(),
                'fecha_expiracion_circulacion' => (is_null($entity->getFechaExpiracionCirculacion())) ? '' : $entity->getFechaExpiracionCirculacion()->format('d/m/Y'),
                'fecha_expiracion_circulacion_compare' => (is_null($entity->getFechaExpiracionCirculacion())) ? '' : $entity->getFechaExpiracionCirculacion()->format('m/d/Y'),
                'anno_fabricacion' => $entity->getAnnoFabricacion(),
                'nmarca_vehiculoid' => $entity->getNmodeloid()->getMarcaVehiculoid()->getId(),
                'nmarca_vehiculo' => $entity->getNmodeloid()->getMarcaVehiculoid()->getNombre(),
                'nmodelo_vehiculoid' => $entity->getNmodeloid()->getId(),
                'nestado_tecnicoid' => $entity->getNestadoTecnicoid()->getId(),
                'ndenominacion_vehiculoid' => $entity->getNdenominacionVehiculoid()->getId(),
                'ndenominacion_vehiculo' => $entity->getNdenominacionVehiculoid()->getNombre(),
                'nunidadid' => $entity->getNunidadid()->getId(),
                'nunidad' => $entity->getNunidadid()->getSiglas(),
                'area_id' => $entity->getArea() ? $entity->getArea()->getId() : '',
                'area' => $entity->getArea() ? $entity->getArea()->getNombre() : '',
                'ntipo_combustibleid' => $entity->getNtipoCombustibleid()->getId(),
                'vehiculo' => ($entity->getEmbarcacion() || $entity->getEquipotecn()) ? false : true,
                'equipoTecn' => ($entity->getEquipotecn()) ? true : false,
                'embarcacion' => ($entity->getEmbarcacion()) ? true : false,
                'actividad' => $actividadid,
                'actividad_nombre' => $actividad,
                'ntipo_combustible' => $entity->getNtipoCombustibleid()->getNombre(),
                'fecha_expiracion_somaton' => (is_null($entity->getFechaExpiracionSomaton())) ? '' : $entity->getFechaExpiracionSomaton()->format('d/m/Y'),
                'fecha_expiracion_licencia_operativa' => (is_null($entity->getFechaExpiracionLicenciaOperativa())) ? '' : $entity->getFechaExpiracionLicenciaOperativa()->format('d/m/Y'),
            );
        }

        $datos = array();
        for ($i = 0; $i < count($_data); $i++) {
            $entities = $em->getRepository('PortadoresBundle:VehiculoPersona')->findBy(array('idvehiculo' => $_data[$i]['id'], 'visible' => true));
            if ($entities) {
                foreach ($entities as $entiy) {
                    if ($entiy->getVisible()) {
                        $persona_id = $entiy->getIdpersona()->getId();
                        $persona_nombre = $entiy->getIdpersona()->getNombre();
                    }
                }
                $datos[] = array(
                    'persona_id' => $persona_id,
                    'persona_nombre' => $persona_nombre,
                    'id' => $_data[$i]['id'],
                    'id' => $_data[$i]['id'],
                    'nro_orden' => $_data[$i]['nro_orden'],
                    'odometro' => $_data[$i]['odometro'],
                    'matricula' => $_data[$i]['matricula'],
                    'empresa' => $_data[$i]['empresa'],
                    'norma_fabricante' => round($_data[$i]['norma_fabricante'], 2),
                    'factor' => round($_data[$i]['factor'], 2),
                    'norma' => round($_data[$i]['norma'], 2),
                    'norma_far' => round($_data[$i]['norma_far'], 2),
                    'norma_lubricante' => $_data[$i]['norma_lubricante'],
                    'nro_inventario' => $_data[$i]['nro_inventario'],
                    'nro_serie_carreceria' => $_data[$i]['nro_serie_carreceria'],
                    'nro_serie_motor' => $_data[$i]['nro_serie_motor'],
                    'color' => $_data[$i]['color'],
                    'nro_circulacion' => $_data[$i]['nro_circulacion'],
                    'fecha_expiracion_circulacion' => $_data[$i]['fecha_expiracion_circulacion'],
                    'fecha_expiracion_circulacion_compare' => $_data[$i]['fecha_expiracion_circulacion_compare'],
                    'anno_fabricacion' => $_data[$i]['anno_fabricacion'],
                    'nmarca_vehiculoid' => $_data[$i]['nmarca_vehiculoid'],
                    'nmarca_vehiculo' => $_data[$i]['nmarca_vehiculo'],
                    'nmodelo_vehiculoid' => $_data[$i]['nmodelo_vehiculoid'],
                    'nestado_tecnicoid' => $_data[$i]['nestado_tecnicoid'],
                    'ndenominacion_vehiculoid' => $_data[$i]['ndenominacion_vehiculoid'],
                    'ndenominacion_vehiculo' => $_data[$i]['ndenominacion_vehiculo'],
                    'vehiculo' => $_data[$i]['vehiculo'],
                    'equipoTecn' => $_data[$i]['equipoTecn'],
                    'embarcacion' => $_data[$i]['embarcacion'],
                    'nunidadid' => $_data[$i]['nunidadid'],
                    'nunidad' => $_data[$i]['nunidad'],
                    'area_id' => $_data[$i]['area_id'],
                    'area' => $_data[$i]['area'],
                    'ntipo_combustibleid' => $_data[$i]['ntipo_combustibleid'],
                    'actividad' => $_data[$i]['actividad'],
                    'actividad_nombre' => $_data[$i]['actividad_nombre'],
                    'ntipo_combustible' => $_data[$i]['ntipo_combustible'],
                    'fecha_expiracion_somaton' => $_data[$i]['fecha_expiracion_somaton'],
                    'fecha_expiracion_licencia_operativa' => $_data[$i]['fecha_expiracion_licencia_operativa']
                );
            } else
                $datos[] = array(
                    'persona_id' => '',
                    'persona_nombre' => '',
                    'id' => $_data[$i]['id'],
                    'matricula' => $_data[$i]['matricula'],
                    'empresa' => $_data[$i]['empresa'],
                    'nro_orden' => $_data[$i]['nro_orden'],
                    'odometro' => $_data[$i]['odometro'],
                    'norma_fabricante' => round($_data[$i]['norma_fabricante'], 2),
                    'factor' => round($_data[$i]['factor'], 2),
                    'norma' => round($_data[$i]['norma'], 2),
                    'norma_far' => round($_data[$i]['norma_far'], 2),
                    'norma_lubricante' => $_data[$i]['norma_lubricante'],
                    'nro_inventario' => $_data[$i]['nro_inventario'],
                    'nro_serie_carreceria' => $_data[$i]['nro_serie_carreceria'],
                    'nro_serie_motor' => $_data[$i]['nro_serie_motor'],
                    'color' => $_data[$i]['color'],
                    'nro_circulacion' => $_data[$i]['nro_circulacion'],
                    'fecha_expiracion_circulacion' => $_data[$i]['fecha_expiracion_circulacion'],
                    'fecha_expiracion_circulacion_compare' => $_data[$i]['fecha_expiracion_circulacion_compare'],
                    'anno_fabricacion' => $_data[$i]['anno_fabricacion'],
                    'nmarca_vehiculoid' => $_data[$i]['nmarca_vehiculoid'],
                    'nmarca_vehiculo' => $_data[$i]['nmarca_vehiculo'],
                    'nmodelo_vehiculoid' => $_data[$i]['nmodelo_vehiculoid'],
                    'nestado_tecnicoid' => $_data[$i]['nestado_tecnicoid'],
                    'ndenominacion_vehiculoid' => $_data[$i]['ndenominacion_vehiculoid'],
                    'ndenominacion_vehiculo' => $_data[$i]['ndenominacion_vehiculo'],
                    'vehiculo' => $_data[$i]['vehiculo'],
                    'equipoTecn' => $_data[$i]['equipoTecn'],
                    'embarcacion' => $_data[$i]['embarcacion'],
                    'nunidadid' => $_data[$i]['nunidadid'],
                    'nunidad' => $_data[$i]['nunidad'],
                    'area_id' => $_data[$i]['area_id'],
                    'area' => $_data[$i]['area'],
                    'ntipo_combustibleid' => $_data[$i]['ntipo_combustibleid'],
                    'actividad' => $_data[$i]['actividad'],
                    'actividad_nombre' => $_data[$i]['actividad_nombre'],
                    'ntipo_combustible' => $_data[$i]['ntipo_combustible'],
                    'fecha_expiracion_somaton' => $_data[$i]['fecha_expiracion_somaton'],
                    'fecha_expiracion_licencia_operativa' => $_data[$i]['fecha_expiracion_licencia_operativa']
                );

        }
        return new JsonResponse(array('rows' => $datos, 'total' => $total));
    }

    public function loadComboAction(Request $request)
    {
        $_data = array();
        $em = $this->getDoctrine()->getManager();

        $_tipoCombustible = trim($request->get('tipoCombustible'));
        $nunidadid = $request->get('unidadid');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $entities = $em->getRepository('PortadoresBundle:Vehiculo')->buscarVehiculoCombo($_unidades, $_tipoCombustible);

        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity['id'],
                'matricula' => $entity['matricula'],
                'nmarca_vehiculo' => $entity['nombre'],
                'norma' => $entity['norma'],
                'nunidadid' => $entity['nunidadid'],
                'tipo_combustibleid' => $entity['tipo_combustibleid'],
                'odometro' => $entity['odometro'],
            );
        }

        return new JsonResponse(array('rows' => $_data));
    }

    public function listadovehiculosAction(Request $request)
    {
        $_data = array();
        $em = $this->getDoctrine()->getManager();

        $_user = $this->get('security.token_storage')->getToken()->getUser();
        $unidades = $em->getRepository('AdminBundle:UsuarioUnidad')->findBy(array('usuario' => $_user->getId()));

        /** @var UsuarioUnidad $unidad */
        foreach ($unidades as $unidad) {
            $_unidades[] = $unidad->getUnidad()->getId();
        }

        $qb = $em->createQueryBuilder();
        $qb->select('vehiculo')
            ->from('PortadoresBundle:VehiculoPersona', 'vehiculo')
            ->innerJoin('vehiculo.idvehiculo', 'idV')
            ->Where($qb->expr()->in('idV.nunidadid', $_unidades))
            ->andWhere($qb->expr()->eq('idV.visible', 'true'));

        $entities = $qb->orderBy('idV.matricula', 'ASC')
            ->getQuery()
            ->getResult();


        $datos = array();
        foreach ($entities as $entity) {
            $datos[] = array(
                'idvehiculo' => $entity->getIdvehiculo()->getId(),
                'matricula' => $entity->getIdvehiculo()->getMatricula(),
                'idpersona' => $entity->getIdpersona()->getId(),
                'nombre_persona' => $entity->getIdpersona()->getNombre(),
                'id' => $entity->getId(),
            );

        }
        return new JsonResponse(array('rows' => $datos, 'total' => count($datos)));
    }

    public function loadAsignacionVehiculoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $_vehiculoid = trim($request->get('vehiculoid'));

        $entities = $em->getRepository('PortadoresBundle:VehiculoPersona')->findBy(array('idvehiculo' => $_vehiculoid, 'visible' => true));

        $_data = array();
        foreach ($entities as $entity) {

            $chofer = $em->getRepository('PortadoresBundle:Chofer')->findOneBy(array(
                'npersonaid' => $entity->getIdpersona()->getId(),
            ));

            $_data [] = array(
                'id' => $entity->getId(),
                'personaid' => $entity->getIdpersona()->getId(),
                'persona' => $entity->getIdpersona()->getNombre(),
                'choferid' => $chofer->getId(),
                'nro_licencia' => $chofer->getNroLicencia(),
                'fecha_expiracion_licencia' => $chofer->getFechaExpiracionLicencia()->format('d/m/Y')
            );

        }

        return new JsonResponse(array('rows' => $_data));
    }

    public function addAsignacionVehiculoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $nro_licencia = trim($request->get('nro_licencia'));
        $npersonaid = trim($request->get('personaid'));
        $fechaExpiracionLicenciaStr = trim($request->get('fecha_expiracion_licencia'));
        $fecha = date_create_from_format('d/m/Y', $fechaExpiracionLicenciaStr);
        $id_vehiculo = trim($request->get('vehiculoid'));

        $repetido = $em->getRepository('PortadoresBundle:VehiculoPersona')->findBy(array(
            'idvehiculo' => $id_vehiculo,
            'idpersona' => $npersonaid,
            'visible' => true
        ));
        if ($repetido)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El vehículo ya fue asignado a esta persona.'));

        $chofer = $em->getRepository('PortadoresBundle:Chofer')->findOneBy(array(
            'npersonaid' => $npersonaid,
        ));

        if ($chofer) {
            if (!$chofer->getVisible())
                $chofer->setVisible(true);
        } else {
            $chofer = new Chofer();
            $chofer->setNpersonaid($em->getRepository('PortadoresBundle:Persona')->find($npersonaid));
        }
        $chofer->setNroLicencia($nro_licencia);
        $chofer->setFechaExpiracionLicencia($fecha);

        $vehiculoPersona = new VehiculoPersona();
        $vehiculoPersona->setIdpersona($em->getRepository('PortadoresBundle:Persona')->find($npersonaid));
        $vehiculoPersona->setIdvehiculo($em->getRepository('PortadoresBundle:Vehiculo')->find($id_vehiculo));
        $vehiculoPersona->setVisible(true);

        try {
            $em->persist($chofer);
            $em->persist($vehiculoPersona);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Chofer asignado con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function modAsignacionVehiculoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $npersonaid = trim($request->get('personaid'));
        $id_vehiculo = trim($request->get('vehiculoid'));
        $nro_licencia = trim($request->get('nro_licencia'));
        $fechaExpiracionLicencia = date_create_from_format('d/m/Y', trim($request->get('fecha_expiracion_licencia')));

        $chofer = $em->getRepository('PortadoresBundle:Chofer')->findOneBy(array(
            'npersonaid' => $npersonaid,
        ));

        if ($chofer) {
            if (!$chofer->getVisible())
                $chofer->setVisible(true);
        } else {
            $chofer = new Chofer();
            $chofer->setNpersonaid($em->getRepository('PortadoresBundle:Persona')->find($npersonaid));
        }
        $chofer->setNroLicencia($nro_licencia);
        $chofer->setFechaExpiracionLicencia($fechaExpiracionLicencia);

        $vehiculoPersona = $em->getRepository('PortadoresBundle:VehiculoPersona')->find($id);
        $vehiculoPersona->setIdpersona($em->getRepository('PortadoresBundle:Persona')->find($npersonaid));
        $vehiculoPersona->setIdvehiculo($em->getRepository('PortadoresBundle:Vehiculo')->find($id_vehiculo));
        $vehiculoPersona->setVisible(true);

        try {
            $em->persist($chofer);
            $em->persist($vehiculoPersona);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Chofer modificado con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function delAsignacionVehiculoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = $request->get('id');
        $entity = $em->getRepository('PortadoresBundle:VehiculoPersona')->find($id);

        //Se analiza si se puede eliminar el chofer
        $repetido = $em->getRepository('PortadoresBundle:VehiculoPersona')->findBy(array(
            'idpersona' => $entity->getIdpersona()->getId(),
            'visible' => true
        ));

        if (count($repetido) == 1) {
            $chofer = $em->getRepository('PortadoresBundle:Chofer')->findOneBy(array(
                'npersonaid' => $entity->getIdpersona()->getId(),
            ));
            if ($chofer) {
                $chofer->setVisible(false);
                $em->persist($chofer);
            }
        }

        $entity->setVisible(false);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Chofer eliminado con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function addAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $matricula = trim($request->get('matricula'));
        $norma = trim($request->get('norma'));
        $nro_orden = intval($request->get('nro_orden'));
        $odometro = trim($request->get('odometro'));
        $norma_far = trim($request->get('norma_far'));
        $norma_fabricante = trim($request->get('norma_fabricante'));
        $factor = trim($request->get('factor'));
        $normaLubricante = trim($request->get('norma_lubricante'));
        $nro_inventario = trim($request->get('nro_inventario'));
        $nro_serie_carreceria = trim($request->get('nro_serie_carreceria'));
        $nro_serie_motor = trim($request->get('nro_serie_motor'));
        $color = trim($request->get('color'));
        $actividad = trim($request->get('actividad'));
        $nro_circulacion = trim($request->get('nro_circulacion'));
        $anno_fabricacion = trim($request->get('anno_fabricacion'));
        $nunidadid = trim($request->get('nunidadid'));
        $area_id = trim($request->get('area_id'));
        $nmodeloid = trim($request->get('nmodelo_vehiculoid'));
        $ntipo_combustibleid = trim($request->get('ntipo_combustibleid'));
        $nestado_tecnicoid = trim($request->get('nestado_tecnicoid'));
        $ndenominacion_vehiculoid = trim($request->get('ndenominacion_vehiculoid'));

        $favmedio = $request->get('fav-medio');

        $equipoTecn = ($favmedio === 'embarcacion') ? true : false;
        $embarcacion = ($favmedio === 'equipoTecn') ? true : false;

        $traslado = $request->get('traslado');
        $idtraslado = $request->get('idtraslado');

        if (!$traslado) {
            $entities = $em->getRepository('PortadoresBundle:Vehiculo')->findByMatricula($matricula);
            if ($entities) {
                if ($entities[0]->getVisible())
                    return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El Vehículo ya existe.'));
            }
        }

        $inv = $em->getRepository('PortadoresBundle:Vehiculo')->findOneBy(array('nroInventario' => $nro_inventario, 'nunidadid' => $nunidadid, 'visible' => true));
        if ($inv) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El número de inventario ya existe.'));
        }

        $orden = $em->getRepository('PortadoresBundle:Vehiculo')->findOneBy(array('nroOrden' => $nro_orden, 'nunidadid' => $nunidadid, 'visible' => true));
        if ($orden !== null) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El número de orden ya existe.'));
        }

        $entity = new Vehiculo();
        $entity->setMatricula($matricula);
        $entity->setNorma($norma_far);
        $entity->setNroOrden($nro_orden);
        $entity->setNormaFabricante($norma_fabricante);
        $entity->setFactor($factor ? $factor : null);
        $entity->setNormaFar($norma_far);
        $entity->setNormaLubricante($normaLubricante);
        $entity->setNroInventario($nro_inventario);
        $entity->setNroSerieCarreceria($nro_serie_carreceria);
        $entity->setNroSerieMotor($nro_serie_motor);
        $entity->setEmbarcacion($embarcacion);
        $entity->setEquipoTecn($equipoTecn);
        $entity->setColor($color);
        $entity->setNroCirculacion($nro_circulacion);
        $entity->setAnnoFabricacion($anno_fabricacion);
        $entity->setNunidadid($em->getRepository('PortadoresBundle:Unidad')->find($nunidadid));

        $entity->setArea($em->getRepository('PortadoresBundle:Area')->find($area_id));

        $entity->setNmodeloid($em->getRepository('PortadoresBundle:ModeloVehiculo')->find($nmodeloid));
        $entity->setNtipoCombustibleid($em->getRepository('PortadoresBundle:TipoCombustible')->find($ntipo_combustibleid));
        $entity->setNestadoTecnicoid($em->getRepository('PortadoresBundle:EstadoTecnico')->find($nestado_tecnicoid));
        $entity->setNdenominacionVehiculoid($em->getRepository('PortadoresBundle:DenominacionVehiculo')->find($ndenominacion_vehiculoid));
        $entity->setActividad($em->getRepository('PortadoresBundle:Actividad')->find($actividad));
        $entity->setOdometro($odometro);
        $entity->setVisible(true);

        if ($traslado) {
            $ent_traslado = $em->getRepository('PortadoresBundle:Traslado')->find(array('id' => $idtraslado));
            $ent_traslado->setAceptado(true);
            $ent_traslado->setHacia($em->getRepository('PortadoresBundle:Unidad')->find($nunidadid));
            try {
                $em->persist($ent_traslado);
            } catch (\Exception $ex) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error confirmando el traslado, si el error persiste contacte a su administrador.'));
            }
        }

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Vehículo adicionado con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function modAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $matricula = trim($request->get('matricula'));
        $nro_orden = trim($request->get('nro_orden'));
        $odometro = trim($request->get('odometro'));
        $norma = trim($request->get('norma'));
        $norma_far = trim($request->get('norma_far'));
        $norma_fabricante = trim($request->get('norma_fabricante'));
        $factor = trim($request->get('factor'));
        $normaLubricante = trim($request->get('norma_lubricante'));
        $nro_inventario = trim($request->get('nro_inventario'));
        $nro_serie_carreceria = trim($request->get('nro_serie_carreceria'));
        $nro_serie_motor = trim($request->get('nro_serie_motor'));
        $color = trim($request->get('color'));
        $nro_circulacion = trim($request->get('nro_circulacion'));
        $actividad = trim($request->get('actividad'));
        $anno_fabricacion = trim($request->get('anno_fabricacion'));
        $nunidadid = trim($request->get('nunidadid'));
        $area_id = trim($request->get('area_id'));

        $nmodeloid = trim($request->get('nmodelo_vehiculoid'));
        $ntipo_combustibleid = trim($request->get('ntipo_combustibleid'));
        $nestado_tecnicoid = trim($request->get('nestado_tecnicoid'));
        $ndenominacion_vehiculoid = trim($request->get('ndenominacion_vehiculoid'));

        $favmedio = $request->get('fav-medio');

        $equipoTecn = ($favmedio === 'equipoTecn') ? true : false;
        $embarcacion = ($favmedio === 'embarcacion') ? true : false;


//        $repetido = $em->getRepository('PortadoresBundle:Vehiculo')->findOneBy(array('matricula' => $matricula, 'visible' => true));
//        if ($repetido) {
//            if ($repetido->getId() != $id)
//                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El Vehículo ya existe.'));
//        }
//
//        $repetido = $em->getRepository('PortadoresBundle:Vehiculo')->findOneBy(array('nroInventario' => $nro_inventario, 'nunidadid' => $nunidadid, 'visible' => true));
//        if ($repetido) {
//            if ($repetido->getId() != $id)
//                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El número de inventario ya existe.'));
//        }
//
//        $repetido = $em->getRepository('PortadoresBundle:Vehiculo')->findOneBy(array('nroOrden' => $nro_orden, 'nunidadid' => $nunidadid, 'visible' => true));
//        if ($repetido) {
//            if ($repetido->getId() != $id)
//                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El número de orden ya existe.'));
//        }


        /**@var Vehiculo $entity * */
        $entity = $em->getRepository('PortadoresBundle:Vehiculo')->find($id);
        $entity->setMatricula($matricula);
        $entity->setNorma($norma_far);
        $entity->setNroOrden($nro_orden);
        $entity->setNormaFabricante($norma_fabricante);
        $entity->setFactor($factor ? $factor : null);
        $entity->setNormaFar($norma_far);
        $entity->setNormaLubricante($normaLubricante);
        $entity->setNroInventario($nro_inventario);
        $entity->setNroSerieCarreceria($nro_serie_carreceria);
        $entity->setNroSerieMotor($nro_serie_motor);
        $entity->setEmbarcacion($embarcacion);
        $entity->setEquipoTecn($equipoTecn);
        $entity->setColor($color);
        $entity->setNroCirculacion($nro_circulacion);
        $entity->setAnnoFabricacion($anno_fabricacion);
        $entity->setNunidadid($em->getRepository('PortadoresBundle:Unidad')->find($nunidadid));
        $entity->setArea($em->getRepository('PortadoresBundle:Area')->find($area_id));
        $entity->setNmodeloid($em->getRepository('PortadoresBundle:ModeloVehiculo')->find($nmodeloid));
        $entity->setNtipoCombustibleid($em->getRepository('PortadoresBundle:TipoCombustible')->find($ntipo_combustibleid));
        $entity->setNestadoTecnicoid($em->getRepository('PortadoresBundle:EstadoTecnico')->find($nestado_tecnicoid));
        $entity->setNdenominacionVehiculoid($em->getRepository('PortadoresBundle:DenominacionVehiculo')->find($ndenominacion_vehiculoid));
        $entity->setActividad($em->getRepository('PortadoresBundle:Actividad')->find($actividad));
        $entity->setOdometro($odometro);
        $entity->setVisible(true);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Vehículo modificado con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function delAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');

        $entities = $em->getRepository('PortadoresBundle:VehiculoPersona')->findByIdvehiculo($id);
        foreach ($entities as $entity) {
            $entity->setVisible(false);
            $em->persist($entity);
        }

        $entity = $em->getRepository('PortadoresBundle:Vehiculo')->find($id);
        $entity->setVisible(false);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Vehículo eliminado con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function getTarjetasByVehiculosAction($matricula)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('PortadoresBundle:Vehiculo')->findBy(array('matricula' => $matricula, 'visible' => true));
        $_data = array();
        if (count($entities) > 0) {
            foreach ($entities[0]->getNtarjetaid() as $tarjeta) {
                $_data[] = array('nro_tarjeta' => $tarjeta->getNroTarjeta());
            }
        }
        return new JsonResponse(array('rows' => $_data, 'total' => count($_data)));
    }

    public function loadMantenimientoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $marcaid = $request->get('marcaid');

        $entities = $em->getRepository('PortadoresBundle:Norma')->findBy(array(
            'marca' => $marcaid
        ));

        $_data = array();
        foreach ($entities as $item) {
            /** @var Norma $item */
            if ($item->getCantHoras() > 0) {
                $_data[] = array(
                    'id' => $item->getId(),
                    'tipo_mantenimiento_id' => $item->getTipoMantenimiento()->getId(),
                    'tipo_mantenimiento' => $item->getTipoMantenimiento()->getNombre(),
                    'km' => $item->getCantHoras(),
                );
            }
        }
        return new JsonResponse(array('rows' => $_data));
    }

    public function addMantenimientoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $tipomantenimientoid = trim($request->get('tipo_mantenimiento_id'));
        $km = trim($request->get('km'));
        $vehiculoid = trim($request->get('vehiculoid'));

        $entities = $em->getRepository('PortadoresBundle:VehiculoTipoMantenimiento')->findOneBy(array(
            'nvehiculoid' => $vehiculoid,
            'tipoMantenimientoid' => $tipomantenimientoid,
            'visible' => true
        ));
        if (!is_null($entities)) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El Mantenimiento ya existe.'));
        }

        $vehiculo = $em->getRepository('PortadoresBundle:Vehiculo')->find($vehiculoid);
        $tipoMantenimiento = $em->getRepository('PortadoresBundle:TipoMantenimiento')->find($tipomantenimientoid);
        $entity = new VehiculoTipoMantenimiento();
        $entity->setKilometros($km);
        $entity->setNvehiculoid($vehiculo);
        $entity->setTipoMantenimientoid($tipoMantenimiento);
        $entity->setVisible(true);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Mantenimiento adicionado con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function modMantenimientoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = $request->get('id');
        $tipomantenimientoid = trim($request->get('tipo_mantenimiento_id'));
        $km = trim($request->get('km'));
        $vehiculoid = trim($request->get('vehiculoid'));

        $entities = $em->getRepository('PortadoresBundle:VehiculoTipoMantenimiento')->findBy(array(
            'nvehiculoid' => $vehiculoid,
            'tipoMantenimientoid' => $tipomantenimientoid,
            'visible' => true
        ));
        foreach ($entities as $e) {
            if ($e->getId() != $id) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El Mantenimiento ya existe.'));
            }
        }

        $tipoMantenimiento = $em->getRepository('PortadoresBundle:TipoMantenimiento')->find($tipomantenimientoid);
        $entity = $em->getRepository('PortadoresBundle:VehiculoTipoMantenimiento')->find($id);
        $entity->setKilometros($km);
        $entity->setTipoMantenimientoid($tipoMantenimiento);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Mantenimiento modificado con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function delMantenimientoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $entity = $em->getRepository('PortadoresBundle:VehiculoTipoMantenimiento')->find($id);
        $entity->setVisible(false);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Mantenimiento eliminado con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function loadActividadVehiculoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $tipo_combustibleid = trim($request->get('tipo_combustibleid'));

        $start = $request->get('start');
        $limit = $request->get('limit');

        $portador_tipocomb = $em->getRepository('PortadoresBundle:TipoCombustible')->find($tipo_combustibleid);

        $entities = $em->getRepository('PortadoresBundle:Actividad')->buscarActividadxTipoCombustible($portador_tipocomb->getPortadorid()->getId(), $start, $limit);
        $total = $em->getRepository('PortadoresBundle:Actividad')->buscarActividadxTipoCombustible($portador_tipocomb->getPortadorid()->getId(), $start, $limit, true);

        $_data = array();
        $group = $request->get('group');
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre(),
                'codigogae' => $entity->getCodigogae(),
                'codigomep' => $entity->getCodigomep(),
                'administrativa' => $entity->getAdministrativa(),
                'inversion' => $entity->getInversiones(),
                'trafico' => $entity->getTrafico(),
                'um_actividad' => $entity->getUmActividad()->getId(),
                'um_actividad_nombre' => $entity->getUmActividad()->getNivelActividad(),
                'id_portador' => $entity->getPortadorid()->getId(),
                'portadornombre' => $entity->getPortadorid()->getNombre()
            );
        }
        return new JsonResponse(array('rows' => $_data));
    }

    public function printPageAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->get('store'));
        $anno = $request->get('anno');
        $mes = FechaUtil::getNombreMes($request->get('mes'));
        $group = $request->get('group');
        $_html = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
        <html xmlns='http://www.w3.org/1999/xhtml'>
         <head>
          <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
           <title>Listado de Medios Técnicos</title>
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
          <header>
            </header>
            </br>
           <table cellspacing='0' cellpadding='5' border='1' width='100%'>             
             <tr>
              <td colspan='8' style='text-align: center; border: none; font-size: 14px;'><strong>Listado de Medios Técnicos</strong></td>
             </tr>
             <tr>
              <td colspan='2' style='text-align: center; border: none'></td>
              <td colspan='2' style='text-align: left; border: none;font-size: 12px;'><strong>Año:</strong><strong>$anno</strong></td>
              <td colspan='2' style='text-align: right; border: none;font-size: 12px;'><strong>Mes:</strong><strong>$mes</strong></td>
              <td colspan='2' style='text-align: center; border: none'></td>
             </tr>
            <tr>
              <td style='text-align: center'><strong>Mátricula</strong></td>
              <td style='text-align: center'><strong>Empresa</strong></td>
              <td style='text-align: center'><strong>Unidad</strong></td>
              <td style='text-align: center'><strong>Tipo de Combustible</strong></td>
              <td style='text-align: center'><strong>Norma</strong></td>
              <td style='text-align: center' ><strong>Norma FAR</strong></td>
              <td style='text-align: center' ><strong>No. Inventario</strong></td>
              <td style='text-align: center' ><strong>No. Motor</strong></td>
              <td style='text-align: center' ><strong>No. Chasis</strong></td>
              <td style='text-align: center' ><strong>No. Circulación</strong></td>
              <td style='text-align: center'><strong>Actividad</strong></td>
            
            </tr>";
        $noT = '';
        usort($data, function ($a, $b) use ($group) {
            return $a->$group < $b->$group ? -1 : 1;
        });

        $newData = [];
        for ($a = 0; $a < count($data); $a++) {
            $qb = $em->createQueryBuilder();
            $qb->select('traslado')
                ->from('PortadoresBundle:Traslado', 'traslado')
                ->innerJoin('traslado.vehiculo', 'vehiculo')
                ->Where($qb->expr()->eq('vehiculo.id', ':idvehiculo'))
                ->setParameter('idvehiculo', $data[$a]->id);

            $trasladado = $qb->getQuery()->getResult();

            if (!$trasladado) {
                array_unshift($newData, $data[$a]);
            }
        }


        for ($i = 0; $i < count($newData); $i++) {
            if ($noT != $newData[$i]->$group) {
                $noT = $newData[$i]->$group;
                $total = 0;
                foreach ($newData as $arr) {
                    if ($arr->$group == $noT) {
                        $total += 1;
                    }
                }
                $veh = 'Medio Técnico';
                if ($total > 1) $veh = 'Medios Técnicos';
                $_html .= "<tr>
                    <td colspan='7' style='text-align: left;'><strong>" . $newData[$i]->$group . "</strong>(" . $total . " " . $veh . ")</td>
                </tr>";
            }
            $_html .= "<tr>
              <td style='text-align: center'> " . $newData[$i]->matricula . "</td>
              <td style='text-align: center'> " . $newData[$i]->empresa . "</td>
              <td style='text-align: center'> " . $newData[$i]->nunidad . "</td>
              <td style='text-align: center'> " . $newData[$i]->ntipo_combustible . "</td>
              <td style='text-align: left'>" . $newData[$i]->norma . "</td>
              <td style='text-align: center'>" . $newData[$i]->norma_far . "</td>
              <td style='text-align: left'>" . $newData[$i]->nro_inventario . "</td>
              <td style='text-align: left'>" . $newData[$i]->nro_serie_motor . "</td>
              <td style='text-align: left'>" . $newData[$i]->nro_serie_carreceria . "</td>
              <td style='text-align: left'>" . $newData[$i]->nro_circulacion . "</td>
              <td style='text-align: center'> " . $newData[$i]->actividad_nombre . "</td>
            </tr>
            ";
        }

        $_html .= "
               </table>
               </body>
             </html>";
        return new Response(json_encode(array('success' => true, 'html' => $_html)));
    }

    public function printAllAction(Request $request)
    {
        $anno = $request->get('anno');
        $mes = FechaUtil::getNombreMes($request->get('mes'));
        $data = [];

        $nunidadid = $request->get('unidadid');
        $em = $this->getDoctrine()->getManager();

        $group = $request->get('group');


        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);
        $qb = $em->createQueryBuilder();
        $qb->select('vehiculo')
            ->from('PortadoresBundle:Vehiculo', 'vehiculo')
            ->Where($qb->expr()->in('vehiculo.nunidadid', $_unidades))
            ->andWhere($qb->expr()->eq('vehiculo.visible', 'true'));

        $entities = $qb->getQuery()->getResult();


        foreach ($entities as $entity) {
            /**@var Vehiculo $entity */

            $firstPadre = $entity->getNunidadid()->getPadreid();
            if ($firstPadre && $firstPadre->getId() !== RootEnum::root) {
                $empresa = $firstPadre->getSiglas();
            } else {
                $empresa = $entity->getNunidadid()->getSiglas();
            }

            $data[] = array(
                'id' => $entity->getId(),
                'ndenominacion_vehiculo' => $entity->getNdenominacionVehiculoid()->getNombre(),
                'matricula' => $entity->getMatricula(),
                'ntipo_combustible' => $entity->getNtipoCombustibleid()->getNombre(),
                'empresa' => $empresa,
                'unidad' => $entity->getNunidadid()->getSiglas(),
                'norma' => $entity->getNorma(),
                'norma_far' => $entity->getNormaFar(),
                'nro_inventario' => $entity->getNroInventario(),
                'nro_serie_carreceria' => $entity->getNroSerieCarreceria(),
                'nro_serie_motor' => $entity->getNroSerieMotor(),
                'nro_circulacion' => $entity->getNroCirculacion(),
                'actividad' => $entity->getActividad()->getNombre(),
            );
        }

        $newData = [];
        for ($a = 0; $a < count($data); $a++) {
            $qb = $em->createQueryBuilder();
            $qb->select('traslado')
                ->from('PortadoresBundle:Traslado', 'traslado')
                ->innerJoin('traslado.vehiculo', 'vehiculo')
                ->Where($qb->expr()->eq('vehiculo.id', ':idvehiculo'))
                ->setParameter('idvehiculo', $data[$a]['id']);

            $trasladado = $qb->getQuery()->getResult();

            if (!$trasladado) {
                array_unshift($newData, $data[$a]);
            }
        }


        $_html = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
        <html xmlns='http://www.w3.org/1999/xhtml'>
         <head>
          <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
           <title>Listado de Medios Técnicos</title>
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
          <header>
            </header>
            </br>
           <table cellspacing='0' cellpadding='5' border='1' width='100%'>             
             <tr>
              <td colspan='8' style='text-align: center; border: none; font-size: 14px;'><strong>Listado de Medios Técnicos</strong></td>
             </tr>
             <tr>
              <td colspan='2' style='text-align: center; border: none'></td>
              <td colspan='2' style='text-align: left; border: none;font-size: 12px;'><strong>Año:</strong><strong>$anno</strong></td>
              <td colspan='2' style='text-align: right; border: none;font-size: 12px;'><strong>Mes:</strong><strong>$mes</strong></td>
              <td colspan='2' style='text-align: center; border: none'></td>
             </tr>
            <tr>
              <td style='text-align: center'><strong>Mátricula</strong></td>
              <td style='text-align: center'><strong>Empresa</strong></td>
              <td style='text-align: center'><strong>Unidad</strong></td>
              <td style='text-align: center'><strong>Tipo de Combustible</strong></td>
              <td style='text-align: center'><strong>Norma</strong></td>
              <td style='text-align: center' ><strong>Norma FAR</strong></td>
              <td style='text-align: center' ><strong>No. Inventario</strong></td>
              <td style='text-align: center' ><strong>No. Motor</strong></td>
              <td style='text-align: center' ><strong>No. Chasis</strong></td>
              <td style='text-align: center' ><strong>No. Circulación</strong></td>
              <td style='text-align: center'><strong>Actividad</strong></td>
            
            </tr>";
        $noT = '';
        usort($newData, function ($a, $b) use ($group) {
            return $a[$group] < $b[$group] ? -1 : 1;
        });

        for ($i = 0; $i < count($newData); $i++) {

            if ($noT != $newData[$i][$group]) {
                $noT = $newData[$i][$group];
                $total = 0;
                foreach ($newData as $arr) {
                    if ($arr[$group] == $noT) {
                        $total += 1;
                    }
                }
                $veh = 'Medio Técnico';
                if ($total > 1) $veh = 'Medios Técnicos';
                $_html .= "<tr>
                    <td colspan='7' style='text-align: left;'><strong>" . $newData[$i][$group] . "</strong>(" . $total . " " . $veh . ")</td>
                </tr>";
            }
            $_html .= "<tr>
              <td style='text-align: center'> " . $newData[$i]['matricula'] . "</td>
              <td style='text-align: center'> " . $newData[$i]['empresa'] . "</td>
              <td style='text-align: center'> " . $newData[$i]['unidad'] . "</td>
              <td style='text-align: center'> " . $newData[$i]['ntipo_combustible'] . "</td>
              <td style='text-align: left'>" . $newData[$i]['norma'] . "</td>
              <td style='text-align: center'>" . $newData[$i]['norma_far'] . "</td>
              <td style='text-align: left'>" . $newData[$i]['nro_inventario'] . "</td>
              <td style='text-align: left'>" . $newData[$i]['nro_serie_motor'] . "</td>
              <td style='text-align: left'>" . $newData[$i]['nro_serie_carreceria'] . "</td>
              <td style='text-align: left'>" . $newData[$i]['nro_circulacion'] . "</td>
              <td style='text-align: center'> " . $newData[$i]['actividad'] . "</td>
            </tr>
            ";
        }

        $_html .= "
               </table>
               </body>
             </html>";
        return new Response(json_encode(array('success' => true, 'html' => $_html)));
    }

    public function trasladarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $unidad_desde = trim($request->get('unidad_desde'));
        $unidad_hacia = trim($request->get('unidad_hacia'));
        $motivos = trim($request->get('motivos'));
        $vehiculo = trim($request->get('vehiculo'));

        $fecha_trasladoStr = trim($request->get('fecha_traslado'));
        $fecha = date_create_from_format('d/m/Y', $fecha_trasladoStr);

        $ready_yet = $em->getRepository('PortadoresBundle:Traslado')->findBy(array(
            'vehiculo' => $vehiculo,
            'desde' => $unidad_desde,
            'hacia' => $unidad_hacia,
        ));

        if ($ready_yet) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El vehículo ya fue trasladado o está listo para traslado.'));
        }

        $traslado = new Traslado();
        $traslado->setDesde($em->getRepository('PortadoresBundle:Unidad')->find($unidad_desde));
        $traslado->setHacia($em->getRepository('PortadoresBundle:Unidad')->find($unidad_hacia));
        $traslado->setVehiculo($em->getRepository('PortadoresBundle:Vehiculo')->find($vehiculo));
        $traslado->setFecha($fecha);
        $traslado->setMotivos($motivos);

        try {
            $em->persist($traslado);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Vehículo listo para traslado. Espere confirmación'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }

    }

    public function readyTraslateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $unidadid = trim($request->get('unidadid'));

        $recieve = $em->getRepository('PortadoresBundle:Traslado')->findBy(array('hacia' => $unidadid, 'aceptado' => false));
        $send = $em->getRepository('PortadoresBundle:Traslado')->findBy(array('desde' => $unidadid, 'aceptado' => false));

        $data = array();
        if (\count($recieve) > 0) {
            foreach ($recieve as $rec) {
                /**@var Traslado $rec */
                $data[] = array(
                    'id' => $rec->getId(),
                    'matricula' => $rec->getVehiculo()->getMatricula(),
                    'fecha' => $rec->getFecha()->format('Y-m-d'),
                    'motivos' => $rec->getMotivos(),
                    'desde' => $rec->getDesde()->getSiglas(),
                    'desdeid' => $rec->getDesde()->getId(),
                    'hacia' => $rec->getHacia()->getSiglas(),
                    'haciaid' => $rec->getHacia()->getId(),
                    'aceptado' => $rec->getAceptado(),

                    'norma' => round($rec->getVehiculo()->getNorma(), 2),
                    'nro_orden' => $rec->getVehiculo()->getNroOrden(),
                    'odometro' => $rec->getVehiculo()->getOdometro(),
                    'norma_fabricante' => round($rec->getVehiculo()->getNormaFabricante(), 2),
                    'norma_far' => round($rec->getVehiculo()->getNormaFar(), 2),
                    'norma_lubricante' => $rec->getVehiculo()->getNormaLubricante(),
                    'nro_inventario' => $rec->getVehiculo()->getNroInventario(),
                    'nro_serie_carreceria' => $rec->getVehiculo()->getNroSerieCarreceria(),
                    'nro_serie_motor' => $rec->getVehiculo()->getNroSerieMotor(),
                    'color' => $rec->getVehiculo()->getColor(),
                    'nro_circulacion' => $rec->getVehiculo()->getNroCirculacion(),
                    'fecha_expiracion_circulacion' => (is_null($rec->getVehiculo()->getFechaExpiracionCirculacion())) ? '' : $rec->getVehiculo()->getFechaExpiracionCirculacion()->format('d/m/Y'),
                    'fecha_expiracion_circulacion_compare' => (is_null($rec->getVehiculo()->getFechaExpiracionCirculacion())) ? '' : $rec->getVehiculo()->getFechaExpiracionCirculacion()->format('m/d/Y'),
                    'anno_fabricacion' => $rec->getVehiculo()->getAnnoFabricacion(),
                    'nmarca_vehiculoid' => $rec->getVehiculo()->getNmodeloid()->getMarcaVehiculoid()->getId(),
                    'nmarca_vehiculo' => $rec->getVehiculo()->getNmodeloid()->getMarcaVehiculoid()->getNombre(),
                    'nmodelo_vehiculoid' => $rec->getVehiculo()->getNmodeloid()->getId(),
                    'nestado_tecnicoid' => $rec->getVehiculo()->getNestadoTecnicoid()->getId(),
                    'ndenominacion_vehiculoid' => $rec->getVehiculo()->getNdenominacionVehiculoid()->getId(),
                    'ndenominacion_vehiculo' => $rec->getVehiculo()->getNdenominacionVehiculoid()->getNombre(),

                    'nunidadid' => $rec->getHacia()->getId(),
                    'nunidad' => $rec->getHacia()->getSiglas(),

                    'area_id' => '',
                    'area' => '',
                    'ntipo_combustibleid' => $rec->getVehiculo()->getNtipoCombustibleid()->getId(),
                    'vehiculo' => ($rec->getVehiculo()->getEmbarcacion() || $rec->getVehiculo()->getEquipotecn()) ? false : true,
                    'equipoTecn' => ($rec->getVehiculo()->getEquipotecn()) ? true : false,
                    'embarcacion' => ($rec->getVehiculo()->getEmbarcacion()) ? true : false,
                    'actividad' => $rec->getVehiculo()->getActividad()->getId(),
                    'actividad_nombre' => $rec->getVehiculo()->getActividad()->getNombre(),
                    'ntipo_combustible' => $rec->getVehiculo()->getNtipoCombustibleid()->getNombre(),
                    'fecha_expiracion_somaton' => (is_null($rec->getVehiculo()->getFechaExpiracionSomaton())) ? '' : $rec->getVehiculo()->getFechaExpiracionSomaton()->format('d/m/Y'),
                    'fecha_expiracion_licencia_operativa' => (is_null($rec->getVehiculo()->getFechaExpiracionLicenciaOperativa())) ? '' : $rec->getVehiculo()->getFechaExpiracionLicenciaOperativa()->format('d/m/Y'),
                );
            }
        }

        if (\count($send) > 0) {
            foreach ($send as $s) {
                /**@var Traslado $s */
                $data[] = array(
                    'id' => $s->getId(),
                    'matricula' => $s->getVehiculo()->getMatricula(),
                    'fecha' => $s->getFecha()->format('Y-m-d'),
                    'motivos' => $s->getMotivos(),
                    'desde' => $s->getDesde()->getSiglas(),
                    'hacia' => $s->getHacia()->getSiglas(),
                    'desdeid' => $s->getDesde()->getId(),
                    'haciaid' => $s->getHacia()->getId(),
                    'aceptado' => $s->getAceptado(),

                    'norma' => round($s->getVehiculo()->getNorma(), 2),
                    'nro_orden' => $s->getVehiculo()->getNroOrden(),
                    'odometro' => $s->getVehiculo()->getOdometro(),
                    'norma_fabricante' => round($s->getVehiculo()->getNormaFabricante(), 2),
                    'norma_far' => round($s->getVehiculo()->getNormaFar(), 2),
                    'norma_lubricante' => $s->getVehiculo()->getNormaLubricante(),
                    'nro_inventario' => $s->getVehiculo()->getNroInventario(),
                    'nro_serie_carreceria' => $s->getVehiculo()->getNroSerieCarreceria(),
                    'nro_serie_motor' => $s->getVehiculo()->getNroSerieMotor(),
                    'color' => $s->getVehiculo()->getColor(),
                    'nro_circulacion' => $s->getVehiculo()->getNroCirculacion(),
                    'fecha_expiracion_circulacion' => (is_null($s->getVehiculo()->getFechaExpiracionCirculacion())) ? '' : $s->getVehiculo()->getFechaExpiracionCirculacion()->format('d/m/Y'),
                    'fecha_expiracion_circulacion_compare' => (is_null($s->getVehiculo()->getFechaExpiracionCirculacion())) ? '' : $s->getVehiculo()->getFechaExpiracionCirculacion()->format('m/d/Y'),
                    'anno_fabricacion' => $s->getVehiculo()->getAnnoFabricacion(),
                    'nmarca_vehiculoid' => $s->getVehiculo()->getNmodeloid()->getMarcaVehiculoid()->getId(),
                    'nmarca_vehiculo' => $s->getVehiculo()->getNmodeloid()->getMarcaVehiculoid()->getNombre(),
                    'nmodelo_vehiculoid' => $s->getVehiculo()->getNmodeloid()->getId(),
                    'nestado_tecnicoid' => $s->getVehiculo()->getNestadoTecnicoid()->getId(),
                    'ndenominacion_vehiculoid' => $s->getVehiculo()->getNdenominacionVehiculoid()->getId(),
                    'ndenominacion_vehiculo' => $s->getVehiculo()->getNdenominacionVehiculoid()->getNombre(),

                    'nunidadid' => $s->getHacia()->getId(),
                    'nunidad' => $s->getHacia()->getSiglas(),

                    'area_id' => $s->getVehiculo()->getArea() ? $s->getVehiculo()->getArea()->getId() : '',
                    'area' => $s->getVehiculo()->getArea() ? $s->getVehiculo()->getArea()->getNombre() : '',
                    'ntipo_combustibleid' => $s->getVehiculo()->getNtipoCombustibleid()->getId(),
                    'vehiculo' => ($s->getVehiculo()->getEmbarcacion() || $s->getVehiculo()->getEquipotecn()) ? false : true,
                    'equipoTecn' => ($s->getVehiculo()->getEquipotecn()) ? true : false,
                    'embarcacion' => ($s->getVehiculo()->getEmbarcacion()) ? true : false,
                    'actividad' => $s->getVehiculo()->getActividad()->getId(),
                    'actividad_nombre' => $s->getVehiculo()->getActividad()->getNombre(),
                    'ntipo_combustible' => $s->getVehiculo()->getNtipoCombustibleid()->getNombre(),
                    'fecha_expiracion_somaton' => (is_null($s->getVehiculo()->getFechaExpiracionSomaton())) ? '' : $s->getVehiculo()->getFechaExpiracionSomaton()->format('d/m/Y'),
                    'fecha_expiracion_licencia_operativa' => (is_null($s->getVehiculo()->getFechaExpiracionLicenciaOperativa())) ? '' : $s->getVehiculo()->getFechaExpiracionLicenciaOperativa()->format('d/m/Y'),
                );
            }
        }
        return new JsonResponse(array('rows' => $data));
    }

    public function listTraslateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $unidadid = trim($request->get('unidadid'));

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($unidadid), $_unidades);

        $recieve = $em->getRepository('PortadoresBundle:Traslado')->buscarTrasladoHacia($_unidades);
        $send = $em->getRepository('PortadoresBundle:Traslado')->buscarTrasladoDesde($_unidades);

        $data = array();
        if (\count($recieve) > 0) {
            foreach ($recieve as $rec) {
                /**@var Traslado $rec */
                $data[] = array(
                    'id' => $rec->getId(),
                    'matricula' => $rec->getVehiculo()->getMatricula(),
                    'fecha' => $rec->getFecha()->format('Y-m-d'),
                    'motivos' => $rec->getMotivos(),
                    'desde' => $rec->getDesde()->getSiglas(),
                    'hacia' => $rec->getHacia()->getSiglas(),
                    'aceptado' => $rec->getAceptado(),

                    'norma' => round($rec->getVehiculo()->getNorma(), 2),
                    'nro_orden' => $rec->getVehiculo()->getNroOrden(),
                    'odometro' => $rec->getVehiculo()->getOdometro(),
                    'norma_fabricante' => round($rec->getVehiculo()->getNormaFabricante(), 2),
                    'norma_far' => round($rec->getVehiculo()->getNormaFar(), 2),
                    'norma_lubricante' => $rec->getVehiculo()->getNormaLubricante(),
                    'nro_inventario' => $rec->getVehiculo()->getNroInventario(),
                    'nro_serie_carreceria' => $rec->getVehiculo()->getNroSerieCarreceria(),
                    'nro_serie_motor' => $rec->getVehiculo()->getNroSerieMotor(),
                    'color' => $rec->getVehiculo()->getColor(),
                    'nro_circulacion' => $rec->getVehiculo()->getNroCirculacion(),
                    'fecha_expiracion_circulacion' => (is_null($rec->getVehiculo()->getFechaExpiracionCirculacion())) ? '' : $rec->getVehiculo()->getFechaExpiracionCirculacion()->format('d/m/Y'),
                    'fecha_expiracion_circulacion_compare' => (is_null($rec->getVehiculo()->getFechaExpiracionCirculacion())) ? '' : $rec->getVehiculo()->getFechaExpiracionCirculacion()->format('m/d/Y'),
                    'anno_fabricacion' => $rec->getVehiculo()->getAnnoFabricacion(),
                    'nmarca_vehiculoid' => $rec->getVehiculo()->getNmodeloid()->getMarcaVehiculoid()->getId(),
                    'nmarca_vehiculo' => $rec->getVehiculo()->getNmodeloid()->getMarcaVehiculoid()->getNombre(),
                    'nmodelo_vehiculoid' => $rec->getVehiculo()->getNmodeloid()->getId(),
                    'nestado_tecnicoid' => $rec->getVehiculo()->getNestadoTecnicoid()->getId(),
                    'ndenominacion_vehiculoid' => $rec->getVehiculo()->getNdenominacionVehiculoid()->getId(),
                    'ndenominacion_vehiculo' => $rec->getVehiculo()->getNdenominacionVehiculoid()->getNombre(),

                    'nunidadid' => $rec->getHacia()->getId(),
                    'nunidad' => $rec->getHacia()->getSiglas(),

                    'area_id' => '',
                    'area' => '',
                    'ntipo_combustibleid' => $rec->getVehiculo()->getNtipoCombustibleid()->getId(),
                    'vehiculo' => ($rec->getVehiculo()->getEmbarcacion() || $rec->getVehiculo()->getEquipotecn()) ? false : true,
                    'equipoTecn' => ($rec->getVehiculo()->getEquipotecn()) ? true : false,
                    'embarcacion' => ($rec->getVehiculo()->getEmbarcacion()) ? true : false,
                    'actividad' => $rec->getVehiculo()->getActividad()->getId(),
                    'actividad_nombre' => $rec->getVehiculo()->getActividad()->getNombre(),
                    'ntipo_combustible' => $rec->getVehiculo()->getNtipoCombustibleid()->getNombre(),
                    'fecha_expiracion_somaton' => (is_null($rec->getVehiculo()->getFechaExpiracionSomaton())) ? '' : $rec->getVehiculo()->getFechaExpiracionSomaton()->format('d/m/Y'),
                    'fecha_expiracion_licencia_operativa' => (is_null($rec->getVehiculo()->getFechaExpiracionLicenciaOperativa())) ? '' : $rec->getVehiculo()->getFechaExpiracionLicenciaOperativa()->format('d/m/Y'),
                );
            }
        }

        if (\count($send) > 0) {
            foreach ($send as $s) {
                /**@var Traslado $s */
                $data[] = array(
                    'id' => $s->getId(),
                    'matricula' => $s->getVehiculo()->getMatricula(),
                    'fecha' => $s->getFecha()->format('Y-m-d'),
                    'motivos' => $s->getMotivos(),
                    'desde' => $s->getDesde()->getNombre(),
                    'hacia' => $s->getHacia()->getNombre(),
                    'aceptado' => $s->getAceptado(),

                    'norma' => round($s->getVehiculo()->getNorma(), 2),
                    'nro_orden' => $s->getVehiculo()->getNroOrden(),
                    'odometro' => $s->getVehiculo()->getOdometro(),
                    'norma_fabricante' => round($s->getVehiculo()->getNormaFabricante(), 2),
                    'norma_far' => round($s->getVehiculo()->getNormaFar(), 2),
                    'norma_lubricante' => $s->getVehiculo()->getNormaLubricante(),
                    'nro_inventario' => $s->getVehiculo()->getNroInventario(),
                    'nro_serie_carreceria' => $s->getVehiculo()->getNroSerieCarreceria(),
                    'nro_serie_motor' => $s->getVehiculo()->getNroSerieMotor(),
                    'color' => $s->getVehiculo()->getColor(),
                    'nro_circulacion' => $s->getVehiculo()->getNroCirculacion(),
                    'fecha_expiracion_circulacion' => (is_null($s->getVehiculo()->getFechaExpiracionCirculacion())) ? '' : $s->getVehiculo()->getFechaExpiracionCirculacion()->format('d/m/Y'),
                    'fecha_expiracion_circulacion_compare' => (is_null($s->getVehiculo()->getFechaExpiracionCirculacion())) ? '' : $s->getVehiculo()->getFechaExpiracionCirculacion()->format('m/d/Y'),
                    'anno_fabricacion' => $s->getVehiculo()->getAnnoFabricacion(),
                    'nmarca_vehiculoid' => $s->getVehiculo()->getNmodeloid()->getMarcaVehiculoid()->getId(),
                    'nmarca_vehiculo' => $s->getVehiculo()->getNmodeloid()->getMarcaVehiculoid()->getNombre(),
                    'nmodelo_vehiculoid' => $s->getVehiculo()->getNmodeloid()->getId(),
                    'nestado_tecnicoid' => $s->getVehiculo()->getNestadoTecnicoid()->getId(),
                    'ndenominacion_vehiculoid' => $s->getVehiculo()->getNdenominacionVehiculoid()->getId(),
                    'ndenominacion_vehiculo' => $s->getVehiculo()->getNdenominacionVehiculoid()->getNombre(),

                    'nunidadid' => $s->getHacia()->getId(),
                    'nunidad' => $s->getHacia()->getSiglas(),

                    'area_id' => $s->getVehiculo()->getArea() ? $s->getVehiculo()->getArea()->getId() : '',
                    'area' => $s->getVehiculo()->getArea() ? $s->getVehiculo()->getArea()->getNombre() : '',
                    'ntipo_combustibleid' => $s->getVehiculo()->getNtipoCombustibleid()->getId(),
                    'vehiculo' => ($s->getVehiculo()->getEmbarcacion() || $s->getVehiculo()->getEquipotecn()) ? false : true,
                    'equipoTecn' => ($s->getVehiculo()->getEquipotecn()) ? true : false,
                    'embarcacion' => ($s->getVehiculo()->getEmbarcacion()) ? true : false,
                    'actividad' => $s->getVehiculo()->getActividad()->getId(),
                    'actividad_nombre' => $s->getVehiculo()->getActividad()->getNombre(),
                    'ntipo_combustible' => $s->getVehiculo()->getNtipoCombustibleid()->getNombre(),
                    'fecha_expiracion_somaton' => (is_null($s->getVehiculo()->getFechaExpiracionSomaton())) ? '' : $s->getVehiculo()->getFechaExpiracionSomaton()->format('d/m/Y'),
                    'fecha_expiracion_licencia_operativa' => (is_null($s->getVehiculo()->getFechaExpiracionLicenciaOperativa())) ? '' : $s->getVehiculo()->getFechaExpiracionLicenciaOperativa()->format('d/m/Y'),
                );
            }
        }
        return new JsonResponse(array('rows' => $data));
    }

}