<?php
/**
 * Created by PhpStorm.
 * User: pfcadenas
 * Date: 19/05/2017
 * Time: 10:13
 */

namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\PortadoresBundle\Util\FechaUtil;
use Geocuba\PortadoresBundle\Entity\HojaRuta;
use Geocuba\PortadoresBundle\Entity\HojaRutaConductor;
use Geocuba\PortadoresBundle\Entity\HojaRutaDesglose;
use Geocuba\PortadoresBundle\Entity\Nactividad;
use Doctrine\Common\CommonException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Geocuba\AdminBundle\Util\Util;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;


class HojaRutaController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $_chapa = trim($request->get('chapa'));
        $nunidadid = trim($request->get('unidadid'));

        $session = $request->getSession();
        $anno = $session->get('selected_year');
        $mes = $session->get('selected_month');

        $fechaDesde = $anno . '-' . $mes . '-' . '1';
        $fechaHasta = FechaUtil::getUltimoDiaMes($mes, $anno);

        $start = $request->get('start');
        $limit = $request->get('limit');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $entities = $em->getRepository('PortadoresBundle:HojaRuta')->buscarHojaRuta($_chapa, $_unidades, $fechaDesde, $fechaHasta, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:HojaRuta')->buscarHojaRuta($_chapa, $_unidades, $fechaDesde, $fechaHasta, $start, $limit, true);

        $_data = array();

        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'fecha' => date_format($entity->getFecha(), 'd/m/Y'),
                'numerohoja' => $entity->getNumerohoja(),
                'capacidad' => $entity->getCapacidad(),
                'numero' => $entity->getNumero(),

                'entidad' => $entity->getEntidad(),
                'organismo' => $entity->getOrganismo(),
                'lugarparqueo' => $entity->getLugarparqueo(),

                'servicioautorizado' => $entity->getServicioautorizado(),
                'kmsdisponible' => $entity->getKmsdisponible(),
                'observaciones' => $entity->getObservaciones(),

                'vehiculoid' => $entity->getVehiculo()->getId(),
                'vehiculo' => $entity->getVehiculo()->getMatricula(),

                'habilitadaporid' => $entity->getHabilitadapor()->getId(),
                'habilitadapor' => $entity->getHabilitadapor()->getNombre(),
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $_fecha = trim($request->get('fecha'));
        $fecha = date_create_from_format('d/m/Y', $_fecha);

        $numerohoja = trim($request->get('numerohoja'));
        $capacidad = trim($request->get('capacidad'));
        $numero = trim($request->get('numero'));

        $entidad = trim($request->get('entidad'));
        $organismo = trim($request->get('organismo'));
        $lugarparqueo = trim($request->get('lugarparqueo'));

        $servicioautorizado = trim($request->get('servicioautorizado'));
        $kmsdisponible = trim($request->get('kmsdisponible'));
        $observaciones = trim($request->get('observaciones'));

        $vehiculoid = trim($request->get('vehiculoid'));
        $habilitadoporid = trim($request->get('habilitadaporid'));

        $vehiculo = $em->getRepository('PortadoresBundle:Vehiculo')->find($vehiculoid);
        $habilitadopor = $em->getRepository('PortadoresBundle:Persona')->find($habilitadoporid);

        $entityValidar = $em->getRepository('PortadoresBundle:HojaRuta')->findOneBy(array(
            'fecha' => $fecha,
            'vehiculo' => $vehiculo->getId()
        ));
        if (!is_null($entityValidar))
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Existe una hoja de ruta para el vehiculo: ' . $vehiculo->getMatricula() . ' en la fecha:' . $_fecha . '.'));

        $entity = new HojaRuta();
        $entity->setVisible(true);

        $entity->setFecha($fecha);

        $entity->setNumerohoja($numerohoja);
        $entity->setCapacidad($capacidad);
        $entity->setNumero($numero);

        $entity->setEntidad($entidad);
        $entity->setOrganismo($organismo);
        $entity->setLugarparqueo($lugarparqueo);

        $entity->setServicioautorizado($servicioautorizado);
        $entity->setKmsdisponible($kmsdisponible);
        $entity->setObservaciones($observaciones);

        $entity->setVehiculo($vehiculo);
        $entity->setHabilitadapor($habilitadopor);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Hoja de ruta adicionada con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function modAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');

        $_fecha = trim($request->get('fecha'));
        $fecha = date_create_from_format('d/m/Y', $_fecha);

        $numerohoja = trim($request->get('numerohoja'));
        $capacidad = trim($request->get('capacidad'));
        $numero = trim($request->get('numero'));

        $entidad = trim($request->get('entidad'));
        $organismo = trim($request->get('organismo'));
        $lugarparqueo = trim($request->get('lugarparqueo'));

        $servicioautorizado = trim($request->get('servicioautorizado'));
        $kmsdisponible = trim($request->get('kmsdisponible'));
        $observaciones = trim($request->get('observaciones'));

        $vehiculoid = trim($request->get('vehiculoid'));
        $habilitadoporid = trim($request->get('habilitadaporid'));

        $vehiculo = $em->getRepository('PortadoresBundle:Vehiculo')->find($vehiculoid);
        $habilitadopor = $em->getRepository('PortadoresBundle:Persona')->find($habilitadoporid);

        $entity = $em->getRepository('PortadoresBundle:HojaRuta')->find($id);
        $entity->setVisible(true);

        $entity->setFecha($fecha);

        $entity->setNumerohoja($numerohoja);
        $entity->setCapacidad($capacidad);
        $entity->setNumero($numero);

        $entity->setEntidad($entidad);
        $entity->setOrganismo($organismo);
        $entity->setLugarparqueo($lugarparqueo);

        $entity->setServicioautorizado($servicioautorizado);
        $entity->setKmsdisponible($kmsdisponible);
        $entity->setObservaciones($observaciones);

        $entity->setVehiculo($vehiculo);
        $entity->setHabilitadapor($habilitadopor);

        try {
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Hoja de ruta modificada con éxito.'));
            return $response;
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function delAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = $request->get('id');
        $entity = $em->getRepository('PortadoresBundle:HojaRuta')->find($id);
        $entity->setVisible(false);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Hoja de ruta eliminada con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }


    public function loadDesgloseAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $_hojarutaid = trim($request->get('hojarutaid'));

        $start = $request->get('start');
        $limit = $request->get('limit');

        $entities = $em->getRepository('PortadoresBundle:HojaRutaDesglose')->buscarHojaRutaDesglose($_hojarutaid, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:HojaRutaDesglose')->buscarHojaRutaDesglose($_hojarutaid, $start, $limit, true);

        $_data = array();

        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'fecha' => $entity->getFecha()->format('d/m/Y'),
                'horasalida' => $entity->getHorasalida()->format('g:i A'),
                'horallegada' => $entity->getHorallegada()->format('g:i A'),

                'kmssalida' => $entity->getKmssalida(),
                'kmsllegada' => $entity->getKmsllegada()
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    public function addDesgloseAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $idhojaruta = $request->get('idhojaruta');
        $_fecha = trim($request->get('fecha'));
        $fecha = date_create_from_format('d/m/Y', $_fecha);
        $_horasalida = trim($request->get('horasalida'));
        $horasalida = date_create_from_format('g:i A', $_horasalida);
        $_horallegada = trim($request->get('horallegada'));
        $horallegada = date_create_from_format('g:i A', $_horallegada);
        $kmssalida = trim($request->get('kmssalida'));
        $kmsllegada = trim($request->get('kmsllegada'));

        $entity = new HojaRutaDesglose();
        $entity->setHojarutaid($em->getRepository('PortadoresBundle:HojaRuta')->find($idhojaruta));
        $entity->setFecha($fecha);
        $entity->setHorasalida($horasalida);
        $entity->setHorallegada($horallegada);
        $entity->setKmssalida($kmssalida);
        $entity->setKmsllegada($kmsllegada);
        $entity->setKmstotales($kmsllegada-$kmssalida);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Ruta  adicionada con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function delDesgloseAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = $request->get('id');
        $entity = $em->getRepository('PortadoresBundle:HojaRutaDesglose')->find($id);

        try {
            $em->remove($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Ruta eliminada con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }


    public function loadConductorAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $_hojarutaid = trim($request->get('hojarutaid'));
        $hoja_ruta = $em->getRepository('PortadoresBundle:HojaRuta')->find($_hojarutaid);

        $entities = $em->getRepository('PortadoresBundle:HojaRutaConductor')->findBy(array('hojarutaid' => $hoja_ruta));
        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'personaid' => $entity->getConductor()->getId(),
                'persona' => $entity->getConductor()->getNombre(),
                'nro_licencia' => $entity->getLicencia(),
            );
        }

        return new JsonResponse(array('rows' => $_data));
    }

    public function addConductorAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $idhojaruta = $request->get('idhojaruta');
        $personaid = trim($request->get('personaid'));
        $nro_licencia = trim($request->get('nro_licencia'));

        $entity = new HojaRutaConductor();
        $entity->setHojarutaid($em->getRepository('PortadoresBundle:HojaRuta')->find($idhojaruta));
        $entity->setConductor($em->getRepository('PortadoresBundle:Persona')->find($personaid));
        $entity->setLicencia($nro_licencia);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Conductor adicionado con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function delConductorAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = $request->get('id');
        $entity = $em->getRepository('PortadoresBundle:HojaRutaConductor')->find($id);

        try {
            $em->remove($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Conductor eliminado con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }


}