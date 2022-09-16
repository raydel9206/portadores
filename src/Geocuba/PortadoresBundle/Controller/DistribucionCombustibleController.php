<?php
/**
 * Created by PhpStorm.
 * User: pfcadenas
 * Date: 02/06/2017
 * Time: 16:10
 */

namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\AdminBundle\Util\Util;
use Geocuba\PortadoresBundle\Util\FechaUtil;
use Geocuba\PortadoresBundle\Entity\DistribucionCombustible;
use Geocuba\PortadoresBundle\Entity\DistribucionCombustibleDesglose;
use Geocuba\PortadoresBundle\Entity\DistribucionDesgloseEdit;
use Geocuba\PortadoresBundle\Entity\Eventualidad;
use Geocuba\PortadoresBundle\Entity\Nchofer;
use Geocuba\PortadoresBundle\Entity\Tarjeta;
use Geocuba\PortadoresBundle\Entity\Persona;
use Geocuba\PortadoresBundle\Entity\TarjetaVehiculo;
use Geocuba\PortadoresBundle\Entity\PlanificacionCombustible;
use Geocuba\PortadoresBundle\Entity\PlanificacionCombustibleCuc;
use Geocuba\PortadoresBundle\Entity\VehiculoPersona;
use Geocuba\PortadoresBundle\Util\DocumentosEnum;
use Doctrine\Common\CommonException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;
use Geocuba\PortadoresBundle\Util\Datos;

class DistribucionCombustibleController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $anno = $request->get('anno');
        $mes = $request->get('mes');
        $nunidadid = $request->get('unidadid');

        $fechaDesde = $anno . '-' . $mes . '-' . '1';
        $fechaHasta = FechaUtil::getUltimoDiaMes($mes, $anno);

        $_tipoCombustible = trim($request->get('tipoCombustible'));

        $_unidades [0]= $nunidadid;
//        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $distribuciones = $em->getRepository('PortadoresBundle:DistribucionCombustible')->buscarDistribucionCombustible($_tipoCombustible, $_unidades, $mes, $anno);

        $_data = array();
        foreach ($distribuciones as $distribucion) {
            $_data[] = array(
                'id' => $distribucion->getId(),
                'denominacion' => $distribucion->getDenominacion(),
                'fecha' => date_format($distribucion->getFecha(), 'd/m/Y'),
                'tipo_combustible_id' => $distribucion->getTipoCombustible()->getId(),
                'tipo_combustible' => $distribucion->getTipoCombustible()->getNombre(),
                'portador' => $distribucion->getTipoCombustible()->getPortadorid()->getNombre(),
                'precio' => $distribucion->getTipoCombustible()->getPrecio(),
                'cantidad' => $distribucion->getCantidad(),
                'aprobada' => $distribucion->getAprobada(),
                'unidadid' => $distribucion->getNunidadid()->getId()
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => count($_data)));
    }

    public function loadDesgloseAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $_tipoCombustible = trim($request->get('tipoCombustible'));
        $tipoCombustible = $em->getRepository('PortadoresBundle:TipoCombustible')->find($_tipoCombustible);

        $nunidadid = $request->get('unidadid');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $_distribucion = trim($request->get('distribucion'));
        $distribucion = $em->getRepository('PortadoresBundle:DistribucionCombustible')->find($_distribucion);

        $mes = $distribucion->getMes();
        $anno = $distribucion->getAnno();

        $_data = array();
        $distribucionCombustibleEdit = $em->getRepository('PortadoresBundle:DistribucionDesgloseEdit')->buscarDistribucionCombustibleEdit($_distribucion, null);
        if ($distribucionCombustibleEdit) {
            foreach ($distribucionCombustibleEdit as $edit_dist) {
                $_data[] = array(
                    'id_edit' => $edit_dist->getId(),
                    'id' => $edit_dist->getDistCombDesg(),
                    'dist_comb_desg' => $edit_dist->getDistCombDesg(),
                    'vehiculoid' => $edit_dist->getVehiculoid()->getId(),
                    'paralizado' => $edit_dist->getVehiculoid()->getNestadoTecnicoid(),
                    'vehiculo' => $edit_dist->getVehiculoid()->getMatricula(),
                    'vehiculo_denominacion' => $edit_dist->getVehiculoid()->getNdenominacionVehiculoid()->getNombre(),
                    'vehiculo_marca' => $edit_dist->getVehiculoid()->getNmodeloid()->getMarcaVehiculoid()->getNombre(),
                    'vehiculo_norma' => $edit_dist->getVehiculoid()->getNorma(),
                    'personaid' => (!is_null($edit_dist->getPersonaid())) ? $edit_dist->getPersonaid()->getId() : '',
                    'persona' => (!is_null($edit_dist->getNombrePersona())) ? $edit_dist->getNombrePersona() : '',
                    'tarjetaid' => (!is_null($edit_dist->getTarjetaid())) ? $edit_dist->getTarjetaid()->getId() : '',
                    'tarjeta' => (!is_null($edit_dist->getNroTarjeta())) ? $edit_dist->getNroTarjeta() : '',
                    'cambustible_asignado' => $edit_dist->getCombAsig(),
                    'inicial' => $edit_dist->getCombAsig(),
                    'preciocombustible' => $edit_dist->getPrecioCombustible(),
                    'monto_asignado' => $edit_dist->getMontoAsig(),
                    'cantidad' => $edit_dist->getCargaLts(),
                    'monto' => $edit_dist->getCargaMonto(),
                    'kms' => $edit_dist->getKms(),
                    'incremento' => (!is_null($edit_dist->getIncremento())) ? $edit_dist->getIncremento() : 0,
                    'reduccion' => (!is_null($edit_dist->getReduccion())) ? $edit_dist->getReduccion() : 0,
                    'nota' => $edit_dist->getNota()
                );
            }
        } elseif (!$distribucionCombustibleEdit) {
            $vehiculos = $em->getRepository('PortadoresBundle:Vehiculo')->buscarVehiculo('', $_tipoCombustible, $_unidades);
            $total_km = 0;
            foreach ($vehiculos as $vehiculo) {

                $planificacion = $em->getRepository('PortadoresBundle:PlanificacionCombustible')->planificacionVehiculo($vehiculo->getId(), $mes, $anno);
                $planificacionCUC = $em->getRepository('PortadoresBundle:PlanificacionCombustibleCuc')->planificacionVehiculo($vehiculo->getId(), $mes, $anno);

                $persona = null;
                $nombre_persona = null;
                if ($vehiculo->getPersonas()->count() > 0)
                    $persona = $vehiculo->getPersonas()->first()->getIdpersona();

                $tarjeta = null;
                if ($vehiculo->getTarjetas()->count() > 0)
                    $tarjeta = $vehiculo->getTarjetas()->first()->getTarjetaid();

                $distribucionDesglose = $em->getRepository('PortadoresBundle:DistribucionCombustibleDesglose')->findOneBy(array(
                    'vehiculo' => $vehiculo->getId(),
                    'distribucionCombustible' => $_distribucion,
                ));

                if (!$distribucionDesglose) {
                    $distribucionDesglose = new DistribucionCombustibleDesglose();
                    $distribucionDesglose->setDistribucionCombustible($distribucion);
                    $distribucionDesglose->setVehiculo($vehiculo);
                    $distribucionDesglose->setPersona($persona);
                    $distribucionDesglose->setTarjeta($tarjeta);
                    $distribucionDesglose->setPrecioCombustible($tipoCombustible->getPrecio());

                    try {
                        $em->persist($distribucionDesglose);
                        $em->flush();
                    } catch (\Exception $ex) {
                    }
                }

                $distribucionCombustibleEdit = new DistribucionDesgloseEdit();
                $distribucionCombustibleEdit->setDistCombDesg($distribucionDesglose->getId());
                $distribucionCombustibleEdit->setCargaLts($distribucionDesglose->getCantidad());
                $distribucionCombustibleEdit->setVehiculoid($vehiculo);
                $distribucionCombustibleEdit->setVehiculoMarca($vehiculo->getMatricula());
                $distribucionCombustibleEdit->setVehiculoDenominacion($vehiculo->getNdenominacionVehiculoid()->getId());
                $distribucionCombustibleEdit->setVehiculoModelo($vehiculo->getNmodeloid()->getNombre());
                $distribucionCombustibleEdit->setMatricula($vehiculo->getMatricula());


                if ($distribucionDesglose->getTarjeta()) {
                    $distribucionCombustibleEdit->setTarjetaid((!is_null($distribucionDesglose->getTarjeta())) ? $distribucionDesglose->getTarjeta() : null);
                    $distribucionCombustibleEdit->setNroTarjeta($distribucionDesglose->getTarjeta()->getNroTarjeta());
                } else {
                    $distribucionCombustibleEdit->setNroTarjeta(null);
                }

                $total_km += $planificacion + $planificacionCUC;

                $distribucionCombustibleEdit->setCargaMonto($distribucionDesglose->getCantidad() * $distribucionDesglose->getPrecioCombustible());
                $distribucionCombustibleEdit->setCombAsig($planificacion + $planificacionCUC);
                $distribucionCombustibleEdit->setKms(($planificacion + $planificacionCUC) * $distribucionDesglose->getVehiculo()->getNorma());
                $distribucionCombustibleEdit->setDistCombustible($distribucion);
                $distribucionCombustibleEdit->setPrecioCombustible($distribucionDesglose->getPrecioCombustible());
                $distribucionCombustibleEdit->setNmes($mes);
                $distribucionCombustibleEdit->setCantidad($distribucionDesglose->getCantidad());
                $distribucionCombustibleEdit->setMontoAsig(($planificacion + $planificacionCUC) * $distribucionDesglose->getPrecioCombustible());


                if ($name_per = $distribucionDesglose->getPersona()) {
                    $distribucionCombustibleEdit->setPersonaid((!is_null($distribucionDesglose->getPersona())) ? $distribucionDesglose->getPersona() : null);
                    $distribucionCombustibleEdit->setNombrePersona($distribucionDesglose->getPersona()->getNombre());
                } else {
                    $distribucionCombustibleEdit->setNombrePersona(null);
                }
                $distribucionCombustibleEdit->setMonto($distribucionDesglose->getCantidad() * $distribucionDesglose->getPrecioCombustible());
                $distribucionCombustibleEdit->setNota($distribucionDesglose->getNota());

                $em->persist($distribucionCombustibleEdit);
                $em->flush();

                $_data[] = array(
                    'id' => $distribucionDesglose->getId(),
                    'vehiculoid' => $distribucionDesglose->getVehiculo()->getId(),
                    'paralizado' => $distribucionDesglose->getVehiculo()->getNestadoTecnicoid(),
                    'vehiculo' => $distribucionDesglose->getVehiculo()->getMatricula(),
                    'vehiculo_denominacion' => $distribucionDesglose->getVehiculo()->getNdenominacionVehiculoid()->getNombre(),
                    'vehiculo_marca' => $distribucionDesglose->getVehiculo()->getNmodeloid()->getMarcaVehiculoid()->getNombre(),
                    'vehiculo_norma' => $distribucionDesglose->getVehiculo()->getNorma(),
                    'personaid' => (!is_null($distribucionDesglose->getPersona())) ? $distribucionDesglose->getPersona()->getId() : '',
                    'persona' => (!is_null($distribucionDesglose->getPersona())) ? $distribucionDesglose->getPersona()->getNombre() : '',
                    'tarjetaid' => (!is_null($distribucionDesglose->getTarjeta())) ? $distribucionDesglose->getTarjeta()->getId() : '',
                    'tarjeta' => (!is_null($distribucionDesglose->getTarjeta())) ? $distribucionDesglose->getTarjeta()->getNroTarjeta() : '',
                    'cambustible_asignado' => $planificacion + $planificacionCUC,
                    'inicial' => $planificacion + $planificacionCUC,
                    'preciocombustible' => $distribucionDesglose->getPrecioCombustible(),
                    'monto_asignado' => ($planificacion + $planificacionCUC) * $distribucionDesglose->getPrecioCombustible(),
                    'cantidad' => $distribucionDesglose->getCantidad(),
                    'monto' => $distribucionDesglose->getCantidad() * $distribucionDesglose->getPrecioCombustible(),
                    'kms' => ($planificacion + $planificacionCUC) * $distribucionDesglose->getVehiculo()->getNorma(),
                    'nota' => $distribucionDesglose->getNota(),
                    'incremento' => 0,
                    'reduccion' => 0,
                    'dist_comb_desg' => $distribucionDesglose->getId());
            }

            $distribucion->setCantidad($total_km);

            try {
                $em->persist($distribucion);
                $em->flush();
            } catch (\Exception $ex) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
            }
        }
        return new JsonResponse(array('rows' => $_data, 'total' => count($_data)));
    }

    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $nunidadid = $request->get('unidadid');

        $_date = '01-' . str_replace('/', '-', $request->get('periodo'));
        $mes_anno = new \DateTime($_date);

        $_denominacion = $request->get('denominacion');
        $_fecha = date_create_from_format('d/m/Y', $request->get('fecha'));
        $_tipoCombustible = $request->get('tipo_combustible_id');
        $mes = $request->getSession()->get('selected_month');
        $anno = $request->getSession()->get('selected_year');
//        $_cantidad = $request->get('cantidad');

        $entity = $em->getRepository('PortadoresBundle:DistribucionCombustible')->findOneBy(array(
            'denominacion' => $_denominacion,
            'mes' => $mes,
            'anno' => $anno,
            'nunidadid' => $nunidadid,
            'visible' => true
        ));
        if ($entity) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe una distribución con la misma denominación en '.FechaUtil::getNombreMes($mes)));
        }

        $distribucionCombustible = new DistribucionCombustible();
        $distribucionCombustible->setVisible(true);
        $distribucionCombustible->setNunidadid($em->getRepository('PortadoresBundle:Unidad')->find($nunidadid));
        $distribucionCombustible->setDenominacion($_denominacion);
        $distribucionCombustible->setFecha($_fecha);
        $distribucionCombustible->setTipoCombustible($em->getRepository('PortadoresBundle:TipoCombustible')->find($_tipoCombustible));
        $distribucionCombustible->setCantidad(0);
        $distribucionCombustible->setMes($mes_anno->format('m'));
        $distribucionCombustible->setAnno($mes_anno->format('Y'));
        $distribucionCombustible->setAprobada(false);

        try {
            $em->persist($distribucionCombustible);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Distribución de combustible adicionada con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function modAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $_id = $request->get('id');
        $_denoinacion = $request->get('denominacion');
        $_fecha = date_create_from_format('d/m/Y', $request->get('fecha'));
        $_tipoCombustible = $request->get('tipo_combustible_id');
//        $_cantidad = $request->get('cantidad');

        $entitiesValidar = $em->getRepository('PortadoresBundle:DistribucionCombustible')->findBy(array(
            'denominacion' => $_denoinacion,
            'fecha' => $_fecha,
            'visible' => true
        ));
        foreach ($entitiesValidar as $ev) {
            if ($ev->getId() != $_id)
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe una distribución con la misma denominación.'));
        }

        $distribucionCombustible = $em->getRepository('PortadoresBundle:DistribucionCombustible')->find($_id);
        $distribucionCombustible->setDenominacion($_denoinacion);
        $distribucionCombustible->setFecha($_fecha);
        $distribucionCombustible->setTipoCombustible($em->getRepository('PortadoresBundle:TipoCombustible')->find($_tipoCombustible));
//        $distribucionCombustible->setCantidad($_cantidad);
        $distribucionCombustible->setMes($_fecha->format('n'));
        $distribucionCombustible->setAnno($_fecha->format('Y'));

        try {
            $em->persist($distribucionCombustible);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Distribución de combustible modificada con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function delAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $_id = $request->get('id');

        $distribucionCombustible = $em->getRepository('PortadoresBundle:DistribucionCombustible')->find($_id);
        $distribucionCombustible->setVisible(false);

        try {
            $em->persist($distribucionCombustible);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Distribución eliminada con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function modAsignacionAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $_id = $request->get('id');
        $_persona = $request->get('personaid');
        $_tarjeta = $request->get('tarjetaid');
        $_vehiculo = $request->get('vehiculoid');
        $_cantidad = $request->get('cantidad');
        $_precio = $request->get('preciocombustible');
        $_nota = $request->get('nota');

        $persona = $em->getRepository('PortadoresBundle:Persona')->find($_persona);
        $tarjeta = $em->getRepository('PortadoresBundle:Tarjeta')->find($_tarjeta);
        $vehiculo = $em->getRepository('PortadoresBundle:Vehiculo')->find($_vehiculo);


        if ($_id != null) {
            $distribucionCombustibleDesglose = $em->getRepository('PortadoresBundle:DistribucionCombustibleDesglose')->find($_id);
            if ($distribucionCombustibleDesglose) {
                $distribucionCombustibleDesglose->setPrecioCombustible($_precio);
                $distribucionCombustibleDesglose->setCantidad($_cantidad);
                $distribucionCombustibleDesglose->setPersona($persona);
                $distribucionCombustibleDesglose->setTarjeta($tarjeta);
                $distribucionCombustibleDesglose->setVehiculo($vehiculo);
                $distribucionCombustibleDesglose->setNota($_nota);

                $em->persist($distribucionCombustibleDesglose);
            }
            $distribucionCombustibleDesgloseEdit = $em->getRepository('PortadoresBundle:DistribucionDesgloseEdit')->buscarDistribucionCombustibleEdit(null, $_id);

            if ($distribucionCombustibleDesgloseEdit[0]->getId()) {
                $distribucionCombustibleDesgloseEdit[0]->setPrecioCombustible($_precio);
                $distribucionCombustibleDesgloseEdit[0]->setCargaLts($_cantidad);
                $distribucionCombustibleDesgloseEdit[0]->setCargaMonto($_cantidad * $_precio);

                if ($persona) {
                    $distribucionCombustibleDesgloseEdit[0]->setNombrePersona($persona->getNombre());
                    $distribucionCombustibleDesgloseEdit[0]->setPersonaid($persona);
                } else {
                    $distribucionCombustibleDesgloseEdit[0]->setNombrePersona(null);
                    $distribucionCombustibleDesgloseEdit[0]->setPersonaid(null);
                }
                if ($tarjeta) {
                    $distribucionCombustibleDesgloseEdit[0]->setNroTarjeta($tarjeta->getNroTarjeta());
                    $distribucionCombustibleDesgloseEdit[0]->setTarjetaid($tarjeta);
                } else {
                    $distribucionCombustibleDesgloseEdit[0]->setNroTarjeta(null);
                    $distribucionCombustibleDesgloseEdit[0]->setTarjetaid(null);
                }

                $distribucionCombustibleDesgloseEdit[0]->setVehiculoid($vehiculo);
                $distribucionCombustibleDesgloseEdit[0]->setMatricula($vehiculo->getMatricula());
                $distribucionCombustibleDesgloseEdit[0]->setNota($_nota);

                $em->persist($distribucionCombustibleDesgloseEdit[0]);
            }

        }

        if ($_tarjeta != '' && $_vehiculo != '') {
            $tarjetaVehiculo = $em->getRepository('PortadoresBundle:TarjetaVehiculo')->findOneBy(array(
                'ntarjetaid' => $_tarjeta,
                'nvehiculoid' => $_vehiculo,
                'visible' => true
            ));
            if (is_null($tarjetaVehiculo)) {
                $tarjetaVehiculo = new TarjetaVehiculo();
                $tarjetaVehiculo->setTarjetaid($tarjeta);
                $tarjetaVehiculo->setNvehiculoid($vehiculo);
                $tarjetaVehiculo->setVisible(true);
                $em->persist($tarjetaVehiculo);
            }
        }

        if ($_persona != '' && $_vehiculo != '') {
            $chofer = $em->getRepository('PortadoresBundle:Nchofer')->findOneBy(array(
                'npersonaid' => $_persona,
            ));

            if ($chofer) {
                if (!$chofer->getVisible())
                    $chofer->setVisible(true);
            } else {
                $chofer = new Nchofer();
                $chofer->setNpersonaid($persona);
                $chofer->setNroLicencia('000');
                $chofer->setFechaExpiracionLicencia(new \DateTime());
            }

            $em->persist($chofer);

            $vehiculoPersona = $em->getRepository('PortadoresBundle:VehiculoPersona')->findOneBy(array(
                'idpersona' => $_persona,
                'idvehiculo' => $_vehiculo,
                'visible' => true
            ));
            if (is_null($vehiculoPersona)) {
                $vehiculoPersona = new VehiculoPersona();
                $vehiculoPersona->setIdpersona($persona);
                $vehiculoPersona->setIdvehiculo($vehiculo);
                $vehiculoPersona->setVisible(true);
                $em->persist($vehiculoPersona);
            }
        }

        try {
//            $em->persist($distribucionCombustibleDesglose);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Asignación de combustible modificada con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function actualizarDesgloseAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $store = json_decode($request->get('store'));
        $distribucion_combustible_id = $request->get('distribucion_combustible_id');
        $suma = 0;

        for ($i = 0; $i < sizeof($store); $i++) {
            $suma += $store[$i]->cambustible_asignado;
            $id_edit = $store[$i]->id;
            $distDesgEdit = $em->getRepository('PortadoresBundle:DistribucionDesgloseEdit')->buscarDistribucionCombustibleEdit(null, $id_edit);
            $distDesgEdit[0]->setCombAsig($store[$i]->cambustible_asignado);
            $distDesgEdit[0]->setKms($store[$i]->kms);
            $distDesgEdit[0]->setMontoAsig($store[$i]->monto_asignado);
            $distDesgEdit[0]->setIncremento($store[$i]->incremento);
            $distDesgEdit[0]->setReduccion($store[$i]->reduccion);
            try {
                $em->flush();
            } catch (\Exception $ex) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
            }
        }
        $distribucion_combustible = $em->getRepository('PortadoresBundle:DistribucionCombustible')->find($distribucion_combustible_id);
        $distribucion_combustible->setCantidad($suma);

        try {
            $em->persist($distribucion_combustible);
            $em->flush();
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }


        $response = new JsonResponse();
        $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Desglose actualizado con éxito.'));
        return $response;
    }

    public function printAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();

        $current_mes = $session->get('current_month');
        $mes_anterior = $current_mes - 1;

        $_distribucion = trim($request->get('distribucion'));
        $disponible = $request->get('disponible');

        $distribucion = $em->getRepository('PortadoresBundle:DistribucionCombustible')->find($_distribucion);

        $tipoCombustible = $distribucion->getTipoCombustible()->getNombre();
        $fecha = $distribucion->getFecha()->format('d/m/Y');
        $mes = $distribucion->getMes();
        $mesStr = FechaUtil::getNombreMes($mes);
        $_anno = $distribucion->getAnno();
        $cantidad = $distribucion->getCantidad();
        $precio = $distribucion->getCantidad() * $distribucion->getTipoCombustible()->getPrecio();

        $distribucionesDesgloses = $em->getRepository('PortadoresBundle:DistribucionDesgloseEdit')->findBy(array('distCombustible' => $_distribucion));

        $tcambustible_asignado = 0;
        $tmonto_asignado = 0;
        $tvehiculo_norma = 0;
        $tkms = 0;

        $tcargaLitros = 0;
        $tcargaDinero = 0;

        $_html = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
        <html xmlns='http://www.w3.org/1999/xhtml'>
        <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <title>DISTRIBUCIÓN DE COMBUSTIBLE</title>
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
            <img  src='../../assets/img/PNG/logo.png' height='60px' width='160px'>
        </header>
        <table cellspacing='0' cellpadding='5' border='1' width='100%'>
           
            <tr>
            <td colspan='3' style='border: none;'></td>
            <td colspan='8' style='border: none;'><div style='text-align:center;font-size: 16px;'><strong>DISTRIBUCIÓN DE COMBUSTIBLES</strong></div></td>
            <td colspan='3' style='border: none;'></td>
            </tr>
            <tr>
            <td colspan='1' style='border: none;'></td>
            <td colspan='3' style='border: none;'><div style='text-align:center;font-size: 14px;'><strong>Combustible: </strong>" . $tipoCombustible . "</div></td>
            <td colspan='1' style='border: none;'><div style='text-align:center;font-size: 14px;'><strong>Fecha: </strong>" . $fecha . "</div></td>
            <td colspan='2' style='border: none;'><div style='text-align:center;font-size: 14px;'><strong>Mes: </strong>$mesStr</div></td>
            <td colspan='3' style='border: none;'><div style='text-align:center;font-size: 14px;'><strong>Cantidad: </strong>" . $cantidad . " Lts</div></td>
            <td colspan='2' style='border: none;'><div style='text-align:center;font-size: 14px;'><strong>Monto: </strong>$" . $precio . "</div></td>
            <td colspan='1' style='border: none;'></td>
            </tr>
            <tr>
            <td style='text-align: center;'><strong>No.</strong></td>
            <td style='text-align: center;'><strong>Matrícula</strong></td>
            <td style='text-align: center;'><strong>Tipo</strong></td>
            <td style='text-align: center;'><strong>Marca/Modelo</strong></td>
            <td style='text-align: center;'><strong>Personas autorizada a extr&nbsp;er la tarjeta</strong></td>
            <td style='text-align: center;'><strong>Asign(L)</strong></td>
            <td style='text-align: center;'><strong>Asign($)</strong></td>
            <td style='text-align: center;'><strong>Kms/lts</strong></td>
            <td style='text-align: center;'><strong>Kms</strong></td>
            <td style='text-align: center;'><strong>Carga(L)</strong></td>
            <td style='text-align: center;'><strong>Carga($)</strong></td>
            <td style='text-align: center;'><strong>No Tarjeta</strong></td>
            <td style='text-align: center;'><strong>Firma Recibido</strong></td>
            </tr>";

        $i = 0;
        foreach ($distribucionesDesgloses as $distribucionDesglose) {
            $i++;
            $id_dis = $distribucionDesglose->getDistCombustible()->getId();

            $vehiculo = $distribucionDesglose->getVehiculoId()->getMatricula();
            $vehiculoDenominacion = $distribucionDesglose->getVehiculoId()->getNdenominacionVehiculoid()->getNombre();
            $vehiculoMarca = $distribucionDesglose->getVehiculoId()->getNmodeloid()->getMarcaVehiculoid()->getNombre();

            $persona = '';
            if ($distribucionDesglose->getVehiculoId()->getPersonas()->count() > 0)
                $persona = $distribucionDesglose->getVehiculoId()->getPersonas()->first()->getIdpersona()->getNombre();

            $cambustible_asignado = $distribucionDesglose->getCombAsig();
            $monto_asignado = $distribucionDesglose->getMontoAsig();
            $vehiculo_norma = $distribucionDesglose->getVehiculoId()->getNorma();
            $kms = $distribucionDesglose->getKms();

            $cargaLitros = $distribucionDesglose->getCargaLts();
            $cargaDinero = $distribucionDesglose->getCargaMonto();
            $tarjeta = $distribucionDesglose->getNota();
            if ($distribucionDesglose->getVehiculoId()->getTarjetas()->count() > 0)
                $tarjeta = $distribucionDesglose->getVehiculoId()->getTarjetas()->first()->getTarjetaid()->getNroTarjeta();


            $_html .= "<tr>
                        <td style='text-align: center;'>" . $i . "</td>
                        <td style='text-align: center;'>" . $vehiculo . "</td>
                        <td style='text-align: center;'>" . $vehiculoDenominacion . "</td>
                        <td style='text-align: center;'>" . $vehiculoMarca . "</td>
                        <td style='text-align: center;'>" . $persona . "</td>
                        <td style='text-align: right;'>" . number_format($cambustible_asignado, 2) . "</td>
                        <td style='text-align: right;'>" . number_format($monto_asignado, 2) . "</td>
                        <td style='text-align: right;'>" . number_format($vehiculo_norma, 2) . "</td>
                        <td style='text-align: right;'>" . number_format($kms, 2) . "</td>
                        <td style='text-align: right;'>" . number_format($cargaLitros, 2) . "</td>
                        <td style='text-align: right;'>" . number_format($cargaDinero, 2) . "</td>
                        <td style='text-align: center;'>" . $tarjeta . "</td>
                        <td style='text-align: center;'></td>
                        </tr>
                        ";

            $tcambustible_asignado += $cambustible_asignado;
            $tmonto_asignado += $monto_asignado;
            $tvehiculo_norma += $vehiculo_norma;
            $tkms += $kms;

            $tcargaLitros += $cargaLitros;
            $tcargaDinero += $cargaDinero;
        }

        $tnorma = ($tcambustible_asignado == 0) ? 0 : $tkms / $tcambustible_asignado;
        $_html .= "<tr>
                    <td colspan='4' style='text-align: center;'><strong>Total</strong></td>
                    <td style='text-align: center;'><strong></strong></td>
                    <td style='text-align: center;'><strong>" . number_format($tcambustible_asignado, 2) . "</strong></td>
                    <td style='text-align: center;'><strong>" . number_format($tmonto_asignado, 2) . "</strong></td>
                    <td style='text-align: center;'><strong>" . number_format($tnorma, 2) . "</strong></td>
                    <td style='text-align: center;'><strong>" . number_format($tkms, 2) . "</strong></td>
                    <td style='text-align: center;'><strong>" . number_format($tcargaLitros, 2) . "</strong></td>
                    <td style='text-align: center;'><strong>" . number_format($tcargaDinero, 2) . "</strong></td>
                    <td style='text-align: center;'><strong></strong></td>
                    <td style='text-align: center;'><strong></strong></td>
                    </tr>
                    ";

            $_html .= "<tr>
            <td colspan='13' style='text-align: left;'>Nota: Combustible disponible para este mes " . floatval($distribucion->getCantidad()+ floatval($disponible)) . "L ,de ello se distribuyen por ahora " . floatval($distribucion->getCantidad()) . "L ,quedando pendiente " . floatval($disponible) . "L</td>
            </tr>";

        $_html .= "</table>";

        $pieFirma = $this->get('portadores.piefirma')->getPieFirmaDistribucion(DocumentosEnum::distribucionCombustible, $distribucion->getNunidadid());
        $_html .= "
        <br>
        <br>
        
        $pieFirma";

        $_html .= "
        </body>
        </html>";


        return new Response(json_encode(array('success' => true, 'html' => $_html)));
    }

    public function loadEventualidadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $session = $request->getSession();
        $anno = $session->get('selected_year');
        $mes = $session->get('selected_month');

        $fechaDesde = $anno . '-' . $mes . '-' . '1';
        $fechaHasta = FechaUtil::getUltimoDiaMes($mes, $anno);

        $_user = $this->get('security.context')->getToken()->getUser();
        $dominio = $em->getRepository('AdminBundle:Dominio')->findByUsersid($_user->getId());
        $_unidades[] = $dominio[0]->getUserUnidadid()->getId();
        $dominio_unidades = $em->getRepository('AdminBundle:DominioUnidades')->findByDominioid($dominio[0]->getId());
        foreach ($dominio_unidades as $unidad) {
            $_unidades[] = $unidad->getUnidadid()->getId();
        }

        $eventualidades = $em->getRepository('PortadoresBundle:Eventualidad')->buscarEventualidad($_unidades, $fechaDesde, $fechaHasta);
        $total = $em->getRepository('PortadoresBundle:Eventualidad')->buscarEventualidad($_unidades, $fechaDesde, $fechaHasta);

        $_data = array();
        foreach ($eventualidades as $eventualidad) {
            $_data[] = array(
                'id' => $eventualidad->getId(),
                'fecha' => date_format($eventualidad->getFecha(), 'd/m/Y'),
                'personaid' => $eventualidad->getPersona()->getId(),
                'persona' => $eventualidad->getPersona()->getNombre(),
                'vehiculoid' => $eventualidad->getVehiculo()->getId(),
                'vehiculo' => $eventualidad->getVehiculo()->getMatricula(),
                'tarjetaid' => $eventualidad->getTarjeta()->getId(),
                'tarjeta' => $eventualidad->getTarjeta()->getNroTarjeta(),
                'cantidad' => $eventualidad->getCantidad(),
                'motivo' => $eventualidad->getMotivo(),
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    public function addEventualidadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $unidad = $session->get('USER_UNIDAD');

        $_fecha = date_create_from_format('d/m/Y', $request->get('fecha'));
        $_persona = $request->get('personaid');
        $_tarjeta = $request->get('tarjetaid');
        $_vehiculo = $request->get('vehiculoid');
        $_cantidad = $request->get('cantidad');
        $_motivo = $request->get('motivo');

        $persona = $em->getRepository('PortadoresBundle:Persona')->find($_persona);
        $tarjeta = $em->getRepository('PortadoresBundle:Tarjeta')->find($_tarjeta);
        $vehiculo = $em->getRepository('PortadoresBundle:Vehiculo')->find($_vehiculo);

        $eventualidad = new Eventualidad();
        $eventualidad->setVisible(true);
        $eventualidad->setNunidadid($em->getRepository('PortadoresBundle:Unidad')->find($unidad));
        $eventualidad->setFecha($_fecha);
        $eventualidad->setPersona($persona);
        $eventualidad->setTarjeta($tarjeta);
        $eventualidad->setVehiculo($vehiculo);
        $eventualidad->setCantidad($_cantidad);
        $eventualidad->setMotivo($_motivo);

        try {
            $em->persist($eventualidad);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Autorizo adicionado con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function delEventualidadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $_id = $request->get('id');

        $eventualidad = $em->getRepository('PortadoresBundle:Eventualidad')->find($_id);
        $eventualidad->setVisible(false);

        try {
            $em->persist($eventualidad);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Autorizo eliminado con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function printEventualidadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $_id = trim($request->get('id'));
        $eventualidad = $em->getRepository('PortadoresBundle:Eventualidad')->find($_id);

        $dia = $eventualidad->getFecha()->format('d');
        $mes = FechaUtil::getNombreMes($eventualidad->getFecha()->format('n'));
        $anno = $eventualidad->getFecha()->format('Y');
        $noAnno = $anno - 1958;
        $persona = $eventualidad->getPersona()->getNombre();
        $cargo = $eventualidad->getPersona()->getCargoid()->getNombre();
        $tarjeta = $eventualidad->getTarjeta()->getNroTarjeta();
        $cantidad = $eventualidad->getCantidad() > 0 ? 'con ' . $eventualidad->getCantidad() . ' lts' : '';
        $vehiculo = $eventualidad->getVehiculo()->getNdenominacionVehiculoid()->getNombre() . ' ' . $eventualidad->getVehiculo()->getNmodeloid()->getMarcaVehiculoid()->getNombre() . ' ' . $eventualidad->getVehiculo()->getNmodeloid()->getNombre() . ' de matrícula ' . $eventualidad->getVehiculo()->getMatricula();
        $motivo = $eventualidad->getmotivo();
        $pieFirma = $this->get('portadores.piefirma')->getPieFirma(DocumentosEnum::autorizoTarjeta);


        $_html = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
        <html xmlns='http://www.w3.org/1999/xhtml'>
        <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <title>AUTORIZO DE CAMBIO O ENTREGA DE TARJETA</title>
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
        <p style='text-align: left'> La Habana, $dia de $mes del $anno. <br> “Año $noAnno de la Revolución.”</p>
        <p style='text-align: center'><b>AUTORIZO DE CAMBIO DE TARJETA O ENTREGA DE TARJETA</b></p>
        <p style='text-align: justify'>Autorizo a que al compañero(a) $persona quién ocupa el cargo de $cargo se le entregue la tarjeta  prepagada No. $tarjeta para habilitar $cantidad el carro $vehiculo con motivo de:</p>
        <p style='text-align: justify;margin-left: 15px;margin-right: 15px'>$motivo</p>
        $pieFirma
        <br>
        <hr>
        <br>
        <p style='text-align: left'> La Habana, $dia de $mes del $anno. <br> “Año $noAnno de la Revolución.”</p>
        <p style='text-align: center'><b>AUTORIZO DE CAMBIO DE TARJETA O ENTREGA DE TARJETA</b></p>
        <p style='text-align: justify'>Autorizo a que al compañero(a) $persona quién ocupa el cargo de $cargo se le entregue la tarjeta  prepagada No. $tarjeta para habilitar $cantidad el carro $vehiculo con motivo de:</p>
        <p style='text-align: justify;margin-left: 15px;margin-right: 15px'>$motivo</p>
        $pieFirma

        </body>
            </html>";

        return new Response(json_encode(array('success' => true, 'html' => $_html)));
    }

    public function loadAllVehiculosAction(Request $request)
    {
        $_nombre = trim($request->get('nombre'));
        $_tipoCombustible = trim($request->get('tipoCombustible'));
        $_unidad = trim($request->get('unidad'));
        $start = $request->get('start');
        $limit = $request->get('limit');
        $nunidadid = $request->get('unidadid');

        $_data = array();
        $em = $this->getDoctrine()->getManager();

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $entities = $em->getRepository('PortadoresBundle:Vehiculo')->FindAllVehiculos($_nombre, $_tipoCombustible, $_unidad, $_unidades, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:Vehiculo')->FindAllVehiculos($_nombre, $_tipoCombustible, $_unidad, $_unidades, $start, $limit, true);

        foreach ($entities as $entity) {
            $actividadid = '-';
            $actividad = '-';
            if ($entity->getActividad()) {
                $actividadid = $entity->getActividad()->getId();
                $actividad = $entity->getActividad()->getNombre();
            }
            $_data[] = array(
                'id' => $entity->getId(),
                'matricula' => $entity->getMatricula(),
                'norma' => $entity->getNorma(),
                'norma_far' => $entity->getNormaFar(),
                'norma_lubricante' => $entity->getNormaLubricante(),
                'odometro' => $entity->getOdometro(),
                'nmarca_vehiculoid' => $entity->getNmodeloid()->getMarcaVehiculoid()->getId(),
                'nmarca_vehiculo' => $entity->getNmodeloid()->getMarcaVehiculoid()->getNombre(),
                'nmodelo_vehiculoid' => $entity->getNmodeloid()->getId(),
                'marca_modelo' => $entity->getNmodeloid()->getMarcaVehiculoid()->getNombre() . '/' . $entity->getNmodeloid()->getNombre(),
                'ndenominacion_vehiculoid' => $entity->getNdenominacionVehiculoid()->getId(),
                'ndenominacion_vehiculo' => $entity->getNdenominacionVehiculoid()->getNombre(),
                'ntipo_combustibleid' => $entity->getNtipoCombustibleid()->getId(),
                'ntipo_combustible' => $entity->getNtipoCombustibleid()->getNombre(),
            );
        }
        $datos = array();
        for ($i = 0; $i < count($_data); $i++) {
            $entities = $em->getRepository('PortadoresBundle:VehiculoPersona')->findByIdvehiculo($_data[$i]['id']);
            if ($entities) {
                foreach ($entities as $entiy) {
                    $persona_id = $entiy->getIdpersona()->getId();
                    $persona_nombre = $entiy->getIdpersona()->getNombre();
                }
                $datos[] = array(

                    'persona_id' => $persona_id,
                    'persona_nombre' => $persona_nombre,
                    'id' => $_data[$i]['id'],
                    'matricula' => $_data[$i]['matricula'],
                    'norma' => $_data[$i]['norma'],
                    'norma_far' => $_data[$i]['norma_far'],
                    'odometro' => $_data[$i]['odometro'],
                    'nmarca_vehiculoid' => $_data[$i]['nmarca_vehiculoid'],
                    'nmarca_vehiculo' => $_data[$i]['nmarca_vehiculo'],
                    'nmodelo_vehiculoid' => $_data[$i]['nmodelo_vehiculoid'],
                    'marca_modelo' => $_data[$i]['marca_modelo'],
                    'ndenominacion_vehiculoid' => $_data[$i]['ndenominacion_vehiculoid'],
                    'ndenominacion_vehiculo' => $_data[$i]['ndenominacion_vehiculo'],
                    'ntipo_combustibleid' => $_data[$i]['ntipo_combustibleid'],
                    'ntipo_combustible' => $_data[$i]['ntipo_combustible'],

                );
            } else
                $datos[] = array(

                    'persona_id' => '',
                    'persona_nombre' => '',
                    'id' => $_data[$i]['id'],
                    'matricula' => $_data[$i]['matricula'],
                    'norma' => $_data[$i]['norma'],
                    'norma_far' => $_data[$i]['norma_far'],
                    'odometro' => $_data[$i]['odometro'],
                    'nmarca_vehiculoid' => $_data[$i]['nmarca_vehiculoid'],
                    'nmarca_vehiculo' => $_data[$i]['nmarca_vehiculo'],
                    'nmodelo_vehiculoid' => $_data[$i]['nmodelo_vehiculoid'],
                    'marca_modelo' => $_data[$i]['marca_modelo'],
                    'ndenominacion_vehiculoid' => $_data[$i]['ndenominacion_vehiculoid'],
                    'ndenominacion_vehiculo' => $_data[$i]['ndenominacion_vehiculo'],
                    'ntipo_combustibleid' => $_data[$i]['ntipo_combustibleid'],
                    'ntipo_combustible' => $_data[$i]['ntipo_combustible'],

                );

        }


        return new JsonResponse(array('rows' => $datos, 'total' => $total));

    }

    public function delDesgloseAction(Request $request)
    {
        $id = trim($request->get('id'));
        $em = $this->getDoctrine()->getManager();
        $distribucion = $em->getRepository('PortadoresBundle:DistribucionCombustible')->find($id);
        $desglose = $em->getRepository('PortadoresBundle:DistribucionCombustibleDesglose')->findByDistribucionCombustible($id);
        $desglose_edit = $em->getRepository('PortadoresBundle:DistribucionDesgloseEdit')->findByDistCombustible($id);

        foreach ($desglose as $des) {
            $em->remove($des);
        }
        foreach ($desglose_edit as $des_edit) {
            $em->remove($des_edit);
        }

        $distribucion->setCantidad(0);
        try {
            $em->persist($distribucion);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Desglose eliminado con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error eliminando los datos, si el error persiste contacte a su administrador.'));
        }

    }

    public function aprobarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $_id = $request->get('id');

        $distribucionCombustible = $em->getRepository('PortadoresBundle:DistribucionCombustible')->find($_id);

        //Obtener Disponible Fincimex
        $disponible_fincimex = Datos::getPlanDisponibleFincimex($em,$distribucionCombustible->getNunidadid(),$distribucionCombustible->getTipoCombustible(), null);

        //Obtener Saldo Fincimex
        $saldo_fincimex = Datos::getSaldoDisponibleFincimex($em,$distribucionCombustible->getNunidadid(),$distribucionCombustible->getTipoCombustible(),null);

        //Obtener Saldo caja
        $saldo_caja = Datos::getSaldoCaja($em,$distribucionCombustible->getNunidadid(),$distribucionCombustible->getTipoCombustible(),null);


        if ($disponible_fincimex+$saldo_fincimex+$saldo_caja < 0) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'La distribución excede la cantidad en inventario más la disponibilidad de'. $distribucionCombustible->getTipoCombustible()->getNombre()));
        }

        $distribucionCombustible->setAprobada(true);

        try {
            $em->persist($distribucionCombustible);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Distribución aprobada con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function desaprobarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $_id = $request->get('id');

        $distribucionCombustible = $em->getRepository('PortadoresBundle:DistribucionCombustible')->find($_id);

        $distribucionCombustible->setAprobada(false);

        try {
            $em->persist($distribucionCombustible);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Distribución desaprobada con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }
}