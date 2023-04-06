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
use Geocuba\PortadoresBundle\Entity\CuentaRecarga;
use Geocuba\PortadoresBundle\Entity\PlanificacionCombustible;
use Geocuba\PortadoresBundle\Entity\PlanificacionCombustibleCuc;
use Geocuba\PortadoresBundle\Util\Datos;
use Geocuba\PortadoresBundle\Util\DocumentosEnum;
use Doctrine\Common\CommonException;
use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;
use Doctrine\Common\Util\Debug;


class AsignacionController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $mes = $request->get('mes');
        $anno = $request->get('anno');
        $_tipoCombustible = trim($request->get('tipo_combustibleid'));
        $nunidadid = $request->get('unidadid');
        $moneda = $request->get('moneda');

        $em = $this->getDoctrine()->getManager();
        $tipoCombustible = $em->getRepository('PortadoresBundle:TipoCombustible')->find($_tipoCombustible);
        $unidad = $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid);

        if ($tipoCombustible) {
            $entities = $em->getRepository('PortadoresBundle:Asignacion')->findBy(array('tipoCombustible' => $tipoCombustible, 'unidad' => $unidad, 'moneda' => $moneda, 'mes' => $mes, 'anno' => $anno, 'visible' => true), array('fecha' => 'DESC'));
        } else {
            $entities = $em->getRepository('PortadoresBundle:Asignacion')->findBy(array('unidad' => $unidad, 'moneda' => $moneda, 'mes' => (int)$mes, 'anno' => (int)$anno, 'visible' => true), array('fecha' => 'DESC'));
        }

        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'unidad' => $entity->getUnidad(),
                'tipo_combustible_id' => $entity->getTipoCombustible()->getId(),
                'tipo_combustible' => $entity->getTipoCombustible()->getNombre(),
                'moneda_id' => $entity->getMoneda()->getId(),
                'moneda' => $entity->getMoneda()->getNombre(),
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
        $moneda_id = $request->get('moneda');


        $em = $this->getDoctrine()->getManager();
        $tiposCombustible = $em->getRepository('PortadoresBundle:TipoCombustible')->findBy(array('visible' => true));
        $moneda = $em->getRepository('PortadoresBundle:Moneda')->findOneBy(array('id' => $moneda_id));


        $_data = array();
        foreach ($tiposCombustible as $tipoCombustible) {
            $asignacion = $em->getRepository('PortadoresBundle:Asignacion')->findOneBy(array('tipoCombustible' => $tipoCombustible, 'unidad' => $nunidadid, 'moneda' => $moneda_id, 'visible' => true), array('fecha' => 'DESC'));
            if ($asignacion) {
                $_data[] = array(
                    'id' => $asignacion->getId(),
                    'tipo_combustible_id' => $tipoCombustible->getId(),
                    'tipo_combustible' => $tipoCombustible->getNombre(),
                    'moneda' => $asignacion->getMoneda()->getId(),
                    'monedaNombre' => $asignacion->getMoneda()->getNombre(),
                    'disponible' => $asignacion->getDisponible(),
                    'para_mes' => $asignacion->getParaMes(),
                );
            } else {
                $asignacion = new Asignacion();
                $asignacion->setTipoCombustible($tipoCombustible);
                $asignacion->setMoneda($moneda);
                $asignacion->setDisponible(0);

                $_data[] = array(
                    'id' => $asignacion->getId(),
                    'tipo_combustible_id' => $tipoCombustible->getId(),
                    'tipo_combustible' => $tipoCombustible->getNombre(),
                    'moneda' => $asignacion->getMoneda()->getId(),
                    'monedaNombre' => $asignacion->getMoneda()->getNombre(),
                    'disponible' => $asignacion->getDisponible(),
                    'para_mes' => $asignacion->getParaMes(),
                );

            }
        }


        return new JsonResponse(array('rows' => $_data));
    }

    public function loadDisponibleTCAction(Request $request)
    {
        $_tipoCombustible = trim($request->get('tipo_combustibleid'));
        $_moneda = trim($request->get('moneda'));
        $nunidadid = $request->get('unidadid');
        $em = $this->getDoctrine()->getManager();

        $tipoCombustible = $em->getRepository('PortadoresBundle:TipoCombustible')->find($_tipoCombustible);
        $moneda = $em->getRepository('PortadoresBundle:Moneda')->find($_moneda);
        $disponible = 0;


//       Volver a cambiar
        $disponible += Datos::getPlanDisponibleFincimex($em, $nunidadid, $tipoCombustible, $moneda);
        $disponible += Datos::getSaldoDisponibleFincimex($em, $nunidadid, $tipoCombustible, $moneda);
        $disponible += Datos::getSaldoCaja($em, $nunidadid, $tipoCombustible, $moneda);

        return new JsonResponse(array('disponible' => $disponible));
    }

    public function addAction(Request $request)
    {
        $nunidadid = $request->get('unidadid');
        $_tipoCombustible = trim($request->get('tipo_combustible_id'));
        $_moneda = trim($request->get('moneda_id'));

        $hora_actual = new \DateTime();
        $hora = $hora_actual->format('g:i');
        $fecha_obj = date_create_from_format('d/m/Y g:i', trim($request->get('fecha'). ' ' . $hora));
        $cantidad = $request->get('cantidad');
        $_date = '01-' . str_replace('/', '-', $request->get('para_mes'));
        $paraMes = date_create_from_format('d-m-Y', $_date);

        $em = $this->getDoctrine()->getManager();
        $tipoCombustible = $em->getRepository('PortadoresBundle:TipoCombustible')->find($_tipoCombustible);
        $moneda = $em->getRepository('PortadoresBundle:Moneda')->find($_moneda);
        $unidad = $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid);

        $last_asignacion = $em->getRepository('PortadoresBundle:Asignacion')->findOneBy(array('tipoCombustible' => $tipoCombustible, 'unidad' => $unidad, 'moneda' => $moneda, 'visible' => true), array('fecha' => 'DESC'));

        if ($last_asignacion && $fecha_obj < $last_asignacion->getFecha()) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Las fecha no puede ser menor a ' . $last_asignacion->getFecha()->format('d/m/Y') . ' de la última asignación.'));
        }

        if ($last_asignacion) {
            $last_asignacion->setModificable(false);
        }

        $asignacion = new Asignacion();
        $asignacion->setFecha($fecha_obj);
        $asignacion->setTipoCombustible($tipoCombustible);
        $asignacion->setMoneda($moneda);
        $asignacion->setUnidad($unidad);
        $asignacion->setCantidad($cantidad);
        $asignacion->setAnno($fecha_obj->format('Y'));
        $asignacion->setMes($fecha_obj->format('m'));
        $asignacion->setParaMes($paraMes);
        $asignacion->setModificable(true);
        $asignacion->setVisible(true);

        $entity_cuenta = $em->getRepository('PortadoresBundle:CuentaRecarga')->findOneBy(array('unidad' => $nunidadid, 'moneda' => $_moneda, 'tipoCombustible' => $_tipoCombustible));

        if ($entity_cuenta) {
            $entity_cuenta->setMonto($entity_cuenta->getMonto() + ($tipoCombustible->getPrecio() * $cantidad));
        } else {
            $entity_cuenta = new CuentaRecarga();
            $entity_cuenta->setUnidad($unidad);
            $entity_cuenta->setTipoCombustible($tipoCombustible);
            $entity_cuenta->setMoneda($moneda);
            $entity_cuenta->setMonto($tipoCombustible->getPrecio() * $cantidad);
        }

        $em->persist($entity_cuenta);
        $last_asignacion = $em->getRepository('PortadoresBundle:Asignacion')->findOneBy(array('tipoCombustible' => $tipoCombustible, 'unidad' => $unidad, 'moneda' => $moneda, 'visible' => true), array('fecha' => 'DESC'));

        if (!$last_asignacion)
            $asignacion->setDisponible($cantidad);
        else
            $asignacion->setDisponible($last_asignacion->getDisponible() + $cantidad);


        try {
            $em->persist($asignacion);
            if ($last_asignacion) {
                $em->persist($last_asignacion);
            }
            $em->flush();
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
        $_moneda = trim($request->get('moneda_id'));

        $hora_actual = new \DateTime();
        $hora = $hora_actual->format('g:i');
        $fecha_obj = date_create_from_format('d/m/Y g:i', trim($request->get('fecha'). ' ' . $hora));
        $cantidad = $request->get('cantidad');
        $_date = '01-' . str_replace('/', '-', $request->get('para_mes'));
        $paraMes = date_create_from_format('d-m-Y', $_date);

        $tipoCombustible = $em->getRepository('PortadoresBundle:TipoCombustible')->find($_tipoCombustible);
        $moneda = $em->getRepository('PortadoresBundle:Moneda')->find($_moneda);
        $unidad = $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid);


        $last_asignacion = $em->getRepository('PortadoresBundle:Asignacion')->findOneBy(array('tipoCombustible' => $tipoCombustible, 'unidad' => $unidad, 'moneda' => $moneda, 'visible' => true), array('fecha' => 'ASC'), 2, 0);
        if ($last_asignacion) {
            if ($fecha_obj < $last_asignacion->getFecha())
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Las fecha no puede ser menor a ' . $last_asignacion->getFecha()->format('d/m/Y') . ' de la última asignación.'));
        }

        $asignacion = $em->getRepository('PortadoresBundle:Asignacion')->find($id);
        $asignacion->setFecha($fecha_obj);
        $new_disponible = $asignacion->getDisponible() + (abs($cantidad - $asignacion->getCantidad()));
        if ($new_disponible < 0){
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'No se puede modificar la asignación, el combustible ya fue distribuido.'));
        }

        $asignacion->setAnno($fecha_obj->format('Y'));
        $asignacion->setMes($fecha_obj->format('m'));
        $asignacion->setDisponible($new_disponible);
        $asignacion->setTipoCombustible($tipoCombustible);
        $asignacion->setMoneda($moneda);
        $asignacion->setCantidad($cantidad);
        $asignacion->setParaMes($paraMes);

        $entity_cuenta = $em->getRepository('PortadoresBundle:CuentaRecarga')->findOneBy(array('unidad' => $nunidadid, 'moneda' => $_moneda, 'tipoCombustible' => $_tipoCombustible));
        if ($entity_cuenta) {
            $entity_cuenta->setMonto($entity_cuenta->getMonto() + floatval($tipoCombustible->getPrecio()) * (abs($cantidad - $asignacion->getCantidad())));
        } else {
            $entity_cuenta = new CuentaRecarga();
            $entity_cuenta->setUnidad($unidad);
            $entity_cuenta->setTipoCombustible($tipoCombustible);
            $entity_cuenta->setMoneda($moneda);
            $entity_cuenta->setMonto(floatval($tipoCombustible->getPrecio()) * ($cantidad - $asignacion->getCantidad()));
        }

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
        $_moneda = trim($request->get('moneda_id'));

        $tipoCombustible = $em->getRepository('PortadoresBundle:TipoCombustible')->find($_tipoCombustible);

        $entity = $em->getRepository('PortadoresBundle:Asignacion')->find($id);
        if ($entity->getCantidad() > $entity->getDisponible())
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'No se puede eliminar la asignación, el combustible ya fue distribuido.'));

        $last_asignacion = $em->getRepository('PortadoresBundle:Asignacion')->findBy(array('tipoCombustible' => $_tipoCombustible, 'unidad' => $nunidadid, 'moneda' => $_moneda, 'visible' => true), array('fecha' => 'DESC'), 2, 1);

        if (count($last_asignacion) > 1) {
            $last_asignacion[0]->setModificable(true);
            $em->persist($last_asignacion[0]);
        }


        $entity_cuenta = $em->getRepository('PortadoresBundle:CuentaRecarga')->findOneBy(array('unidad' => $nunidadid, 'moneda' => $_moneda, 'tipoCombustible' => $_tipoCombustible));
        if ($entity_cuenta) {
            $entity_cuenta->setMonto($entity_cuenta->getMonto() - ($tipoCombustible->getPrecio() * $entity->getCantidad()));
        }

        $entity->setVisible(false);
        try {
            $em->persist($entity);
            if ($last_asignacion) {
                $em->persist($last_asignacion[0]);
            }
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Asignación eliminada con éxito.'));
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
                $moneda = $em->getRepository('PortadoresBundle:Moneda')->find($store[$i]->moneda);
                $asignacion = new Asignacion();
                $asignacion->setFecha(new \DateTime());
                $asignacion->setTipoCombustible($tipoCombustible);
                $asignacion->setMoneda($moneda);
                $asignacion->setUnidad($unidad);
                $asignacion->setCantidad($store[$i]->disponible);
                $asignacion->setDisponible($store[$i]->disponible);
                $asignacion->setMes($session->get('selected_month'));
                $asignacion->setAnno($session->get('selected_year'));
                $asignacion->setParaMes($store[$i]->para_mes);
//                $asignacion->setModificable(true);
//                $asignacion->setVisible(true);
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

    public function loadDemandaAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $_data = array();
        $mes = $request->get('mes');
        $anno = $request->get('anno');
        $_tipoCombustible = trim($request->get('tipo_combustibleid'));
        $nunidadid = $request->get('unidadid');
        $moneda = $request->get('moneda');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $qb = $em->createQueryBuilder();
        $qb->select('demanda')
            ->from('PortadoresBundle:DemandaCombustible', 'demanda')
            ->Where($qb->expr()->in('demanda.unidad', $_unidades))
            ->andWhere('demanda.anno = :anno')
            ->andWhere('demanda.mes = :mes')
            ->andWhere($qb->expr()->eq('demanda.visible', 'true'))
            ->setparameter(':anno', $anno)
            ->setparameter(':mes', $mes);
        if (isset($_tipoCombustible) && $_tipoCombustible != '') {
            $qb->andWhere('demanda.tipoCombustible = :tipoCombustible')
                ->setParameter('tipoCombustible', $_tipoCombustible);
        }
        if (isset($moneda) && $moneda != '') {
            $qb->andWhere('demanda.moneda = :moneda')
                ->setParameter('moneda', $moneda);
        }

        $entities = $qb->getQuery()->getResult();

        foreach ($entities as $item) {
            $_data[] = array(
                'id' => $item->getId(),
                'unidad' => $item->getUnidad()->getNombre(),
                'demandado' => $item->getCantLitros(),
                'tipo_combustible' => $item->getTipoCombustible()->getNombre()
            );
        }
        return new JsonResponse(array('rows' => $_data));
    }

}