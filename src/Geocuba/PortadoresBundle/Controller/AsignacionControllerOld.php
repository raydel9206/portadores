<?php
/**
 * Created by PhpStorm.
 * User: pfcadenas
 * Date: 11/11/2016
 * Time: 15:43
 */

namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\AdminBundle\Util\Util;
use Geocuba\AdminBundle\Util\FechaUtil;
use Geocuba\PortadoresBundle\Entity\Asignacion;
use Geocuba\PortadoresBundle\Entity\PlanificacionCombustible;
use Geocuba\PortadoresBundle\Entity\PlanificacionCombustibleCuc;
use Geocuba\PortadoresBundle\Util\Datos;
use Geocuba\PortadoresBundle\Util\DocumentosEnum;
use Doctrine\Common\CommonException;
use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;


class AsignacionController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $mes = $request->get('mes');
        $anno = $request->get('anno');
        $_tipoCombustible = trim($request->get('tipo_combustibleid'));
        $nunidadid = $request->get('unidadid');

        $em = $this->getDoctrine()->getManager();
        $tipoCombustible = $em->getRepository('PortadoresBundle:TipoCombustible')->find($_tipoCombustible);
        $unidad = $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid);

        if ($tipoCombustible) {
            $entities = $em->getRepository('PortadoresBundle:Asignacion')->findBy(array('tipoCombustible' => $tipoCombustible, 'unidad' => $unidad, 'mes' => $mes, 'anno' => $anno, 'visible' => true), array('fecha' => 'DESC'));
        } else {
            $entities = $em->getRepository('PortadoresBundle:Asignacion')->findBy(array('unidad' => $unidad, 'mes' => (int)$mes, 'anno' => (int)$anno, 'visible' => true), array('fecha' => 'DESC'));
        }

        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'unidad' => $entity->getUnidad(),
                'tipo_combustible_id' => $entity->getTipoCombustible()->getId(),
                'tipo_combustible' => $entity->getTipoCombustible()->getNombre(),
                'denominacion' => $entity->getDenominacion(),
                'fecha' => $entity->getFecha()->format('d/m/Y'),
                'cantidad' => $entity->getCantidad(),
                'disponible' => $entity->getDisponible(),
                'modificable' => $entity->getModificable(),
                'para_mes' => $entity->getParaMes()->format('d-m-Y'),
                'paraMes' => $entity->getParaMes()->format('m/Y')
            );
        }

        return new JsonResponse(array('rows' => $_data));
    }

    public function loadDisponibleAction(Request $request)
    {
        $nunidadid = $request->get('unidadid');

        $em = $this->getDoctrine()->getManager();
        $tiposCombustible = $em->getRepository('PortadoresBundle:TipoCombustible')->findBy(array('visible' => true));

        $_data = array();
        foreach ($tiposCombustible as $tipoCombustible) {
            $asignacion = $em->getRepository('PortadoresBundle:Asignacion')->findOneBy(array('tipoCombustible' => $tipoCombustible, 'unidad' => $nunidadid, 'visible' => true), array('fecha' => 'DESC'));
            if ($asignacion) {
                $_data[] = array(
                    'id' => $asignacion->getId(),
                    'tipo_combustible_id' => $tipoCombustible->getId(),
                    'tipo_combustible' => $tipoCombustible->getNombre(),
                    'disponible' => $asignacion->getDisponible(),
                );
            } else {
                $asignacion = new Asignacion();
                $asignacion->setTipoCombustible($tipoCombustible);
                $asignacion->setDisponible(0);

                $_data[] = array(
                    'id' => $asignacion->getId(),
                    'tipo_combustible_id' => $tipoCombustible->getId(),
                    'tipo_combustible' => $tipoCombustible->getNombre(),
                    'disponible' => $asignacion->getDisponible(),
                );

            }
        }


        return new JsonResponse(array('rows' => $_data));
    }

    public function loadDisponibleTCAction(Request $request)
    {
        $_tipoCombustible = trim($request->get('tipo_combustibleid'));
        $nunidadid = $request->get('unidadid');

        $em = $this->getDoctrine()->getManager();
        $tipoCombustible = $em->getRepository('PortadoresBundle:TipoCombustible')->find($_tipoCombustible);

        $disponible = 0;

        $disponible += Datos::getPlanDisponibleFincimex($em, $nunidadid, $tipoCombustible);
        $disponible += Datos::getSaldoDisponibleFincimex($em, $nunidadid, $tipoCombustible);
        $disponible += Datos::getSaldoCaja($em, $nunidadid, $tipoCombustible);

        return new JsonResponse(array('disponible' => $disponible));
    }

    public function addAction(Request $request)
    {
        $nunidadid = $request->get('unidadid');
        $_tipoCombustible = trim($request->get('tipo_combustible_id'));
        $fecha_obj = date_create_from_format('d/m/Y', trim($request->get('fecha')));
        $cantidad = $request->get('cantidad');
        $_date = '01-' . str_replace('/', '-', $request->get('para_mes'));
        $paraMes = date_create_from_format('d-m-Y', $_date);

        $em = $this->getDoctrine()->getManager();
        $tipoCombustible = $em->getRepository('PortadoresBundle:TipoCombustible')->find($_tipoCombustible);
        $unidad = $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid);

        $last_asignacion = $em->getRepository('PortadoresBundle:Asignacion')->findOneBy(array('tipoCombustible' => $tipoCombustible, 'unidad' => $unidad, 'visible' => true), array('fecha' => 'DESC'));

        if ($last_asignacion && $fecha_obj < $last_asignacion->getFecha()) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Las fecha no puede ser menor a ' . $last_asignacion->getFecha()->format('d/m/Y') . ' de la última asignación.'));
        }


        if ($last_asignacion)
            $last_asignacion->setModificable(false);

        $asignacion = new Asignacion();
        $asignacion->setFecha($fecha_obj);
        $asignacion->setTipoCombustible($tipoCombustible);
        $asignacion->setUnidad($unidad);
        $asignacion->setCantidad($cantidad);
        $asignacion->setAnno($fecha_obj->format('Y'));
        $asignacion->setMes($fecha_obj->format('m'));
        $asignacion->setParaMes($paraMes);
        $asignacion->setModificable(true);
        $asignacion->setVisible(true);

        $last_asignacion = $em->getRepository('PortadoresBundle:Asignacion')->findOneBy(array('tipoCombustible' => $tipoCombustible, 'unidad' => $unidad, 'visible' => true), array('fecha' => 'DESC'));
        if (!$last_asignacion)
            $asignacion->setDisponible($cantidad);
        else
            $asignacion->setDisponible($last_asignacion->getDisponible() + $cantidad);


        try {
            $em->persist($asignacion);
            $em->flush();
        } catch
        (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }

        try {
            if ($last_asignacion) {
                $em->persist($last_asignacion);
                $em->flush();
            }
        } catch
        (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }

        return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Asignación adicionada con éxito.'));
    }

    public function modAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $nunidadid = $request->get('unidadid');
        $_tipoCombustible = trim($request->get('tipo_combustible_id'));
        $fecha = trim($request->get('fecha'));
        $fecha_obj = date_create_from_format('d/m/Y', trim($request->get('fecha')));
        $cantidad = $request->get('cantidad');
        $_date = '01-' . str_replace('/', '-', $request->get('para_mes'));
        $paraMes = date_create_from_format('d-m-Y', $_date);

        $tipoCombustible = $em->getRepository('PortadoresBundle:TipoCombustible')->find($_tipoCombustible);
        $unidad = $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid);

        $last_asignacion = $em->getRepository('PortadoresBundle:Asignacion')->findBy(array('tipoCombustible' => $tipoCombustible, 'unidad' => $unidad, 'visible' => true), array('fecha' => 'ASC'), 2, 0)[0];
        if ($fecha < $last_asignacion->getFecha()->format('d/m/Y'))
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Las fecha no puede ser menor a ' . $last_asignacion->getFecha()->format('d/m/Y') . ' de la última asignación.'));

        $asignacion = $em->getRepository('PortadoresBundle:Asignacion')->find($id);
//        $asignacion->setDenominacion($denominacion);
        $asignacion->setFecha($fecha_obj);
        $new_disponible = $asignacion->getDisponible() + ($cantidad - $asignacion->getCantidad());
        if ($new_disponible < 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'No se puede modificar la asignación, el combustible ya fue distribuido.'));

        $asignacion->setAnno($fecha_obj->format('Y'));
        $asignacion->setMes($fecha_obj->format('m'));
        $asignacion->setDisponible($new_disponible);
        $asignacion->setCantidad($cantidad);
        $asignacion->setParaMes($paraMes);

        try {
            $em->persist($asignacion);
            $em->flush();
        } catch
        (\Exception $ex) {

            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }

        return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Asignación modificada con éxito.'));
    }

    public function delAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = $request->get('id');
        $nunidadid = $request->get('unidadid');
        $_tipoCombustible = trim($request->get('tipo_combustible_id'));

        $tipoCombustible = $em->getRepository('PortadoresBundle:TipoCombustible')->find($_tipoCombustible);
        $unidad = $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid);

        $entity = $em->getRepository('PortadoresBundle:Asignacion')->find($id);
        if ($entity->getCantidad() > $entity->getDisponible())
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'No se puede eliminar la asignación, el combustible ya fue distribuido.'));

        $last_asignacion = $em->getRepository('PortadoresBundle:Asignacion')->findBy(array('tipoCombustible' => $tipoCombustible, 'unidad' => $unidad, 'visible' => true), array('fecha' => 'ASC'), 2, 0)[0];
        if ($last_asignacion)
            $last_asignacion->setModificable(true);

        $entity->setVisible(false);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Asignación eliminada con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }

        try {
            if ($last_asignacion) {
                $em->persist($last_asignacion);
                $em->flush();
            }
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function modDisponibleAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $unidadid = $request->get('unidadid');
        $store = json_decode($request->get('store'));

        $unidad = $em->getRepository('PortadoresBundle:Unidad')->find($unidadid);

        for ($i = 0; $i < sizeof($store); $i++) {
            $last_asignacion = $em->getRepository('PortadoresBundle:Asignacion')->find($store[$i]->id);

            if ($last_asignacion) {
                $last_asignacion->setDisponible($store[$i]->disponible);
                $em->persist($last_asignacion);
            } elseif ($store[$i]->disponible > 0) {
                $tipoCombustible = $em->getRepository('PortadoresBundle:TipoCombustible')->find($store[$i]->tipo_combustible_id);

                $asignacion = new Asignacion();
                $asignacion->setFecha(new \DateTime());
                $asignacion->setTipoCombustible($tipoCombustible);
                $asignacion->setUnidad($unidad);
                $asignacion->setCantidad($store[$i]->disponible);
                $asignacion->setDisponible($store[$i]->disponible);
                $asignacion->setMes($session->get('selected_month'));
                $asignacion->setAnno($session->get('selected_year'));
                $asignacion->setModificable(true);
                $asignacion->setVisible(true);
                $em->persist($asignacion);
            }
        }

        try {
            $em->flush();
        } catch
        (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }

        return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Disponibilidad modificada con éxito.'));

    }

    public function subCantidadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $cantidad = $request->get('cantidad');
        $nunidadid = $request->get('unidadid');
        $_tipoCombustible = trim($request->get('tipo_combustibleid'));

        $tipoCombustible = $em->getRepository('PortadoresBundle:TipoCombustible')->find($_tipoCombustible);
        $unidad = $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid);

        $asignacion = $em->getRepository('PortadoresBundle:PlanDisponible')->findOneBy(array('tipoCombustible' => $tipoCombustible, 'unidad' => $unidad, 'visible' => true), array('fecha' => 'DESC'));

        if (!$asignacion)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'No existe plan disponible para el tipo de combustible en la unidad'));

        if ($asignacion->getDisponible() - $cantidad < 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'No hay disponibilidad de ' . $tipoCombustible->getCodigo() . ' para la cantidad solicitada'));
        $asignacion->setDisponible($asignacion->getDisponible() - $cantidad);

        try {
            $em->persist($asignacion);
            $em->flush();
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

}