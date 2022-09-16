<?php
/**
 * Created by PhpStorm.
 * User: yosley
 * Date: 06/10/2015
 * Time: 16:36
 */


namespace Geocuba\PortadoresBundle\Controller;

use Doctrine\Common\Util\Debug;
use Geocuba\AdminBundle\Util\Util;
use Geocuba\PortadoresBundle\Entity\DemandaCombustible;
use Geocuba\PortadoresBundle\Entity\Persona;
use Doctrine\Common\CommonException;
use Geocuba\PortadoresBundle\Util\Datos;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;

class DemandaCombustibleController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $nunidadid = $request->get('unidadid');
        $mes = $request->get('mes');
        $anno = $request->get('anno');

        $_data = array();
        $em = $this->getDoctrine()->getManager();

        $_unidades[0] = $nunidadid;
//        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        //Cargar Realizadas
//        $entities = $em->getRepository('PortadoresBundle:DemandaCombustible')->findBy(array('unidad' => $nunidadid, 'mes' => $mes, 'anno' => $anno, 'visible' => true));
//
//        foreach ($entities as $entity) {
//            $_data[] = array(
//                'id' => $entity->getId(),
//                'tipo_combustible' => $entity->getTipoCombustible()->getNombre(),
//                'tipo_combustible_id' => $entity->getTipoCombustible()->getId(),
//                'cant_litros' => $entity->getCantLitros(),
//                'comb_planificado' => $entity->getDisponibleFincimex(),
//                'disponible_fincimex' => $entity->getDisponibleFincimex(),
//                'saldo_fincimex' => $entity->getSaldoFincimex(),
//                'saldo_caja' => $entity->getSaldoCaja(),
//                'unidad_id' => $entity->getUnidad()->getId(),
//            );
//        }

        //Cargar Todas
        $entities = $em->getRepository('PortadoresBundle:TipoCombustible')->findBy(array('visible' => true));

        foreach ($entities as $entity) {
            $demanda = $em->getRepository('PortadoresBundle:DemandaCombustible')->findOneBy(array('unidad' => $nunidadid, 'tipoCombustible' => $entity, 'mes' => $mes, 'anno' => $anno, 'visible' => true));
            if ($demanda) {
                $_data[] = array(
                    'demanda_id' => $demanda->getId(),
                    'tipo_combustible' => $demanda->getTipoCombustible()->getNombre(),
                    'tipo_combustible_id' => $demanda->getTipoCombustible()->getId(),
                    'cant_litros' => $demanda->getCantLitros(),
                    'propuesta' => $demanda->getCombPlanificado()-$demanda->getDisponibleFincimex()-$demanda->getSaldoFincimex()-$demanda->getSaldoCaja(),
                    'comb_planificado' => $demanda->getCombPlanificado(),
                    'disponible_fincimex' => $demanda->getDisponibleFincimex(),
                    'saldo_fincimex' => $demanda->getSaldoFincimex(),
                    'saldo_caja' => $demanda->getSaldoCaja(),
                    'unidad_id' => $demanda->getUnidad()->getId(),
                );
            } else {
                //Obtener Combustible Planificado en el mes
                $comb_planificado = $this->cantPlanificada($entity->getId(), $nunidadid, $mes, $anno);

                //Obtener Disponible Fincimex
                $disponible_fincimex = Datos::getPlanDisponibleFincimex($em,$nunidadid,$entity);

                //Obtener Saldo Fincimex
                $saldo_fincimex = Datos::getSaldoDisponibleFincimex($em,$nunidadid,$entity);

                //Obtener Saldo caja
                $saldo_caja = Datos::getSaldoCaja($em,$nunidadid,$entity);

                $_data[] = array(
                    'demanda_id' => '',
                    'tipo_combustible' => $entity->getNombre(),
                    'tipo_combustible_id' => $entity->getId(),
                    'cant_litros' => 0,
                    'propuesta' => $comb_planificado - $disponible_fincimex - $saldo_fincimex - $saldo_caja,
                    'comb_planificado' => $comb_planificado,
                    'disponible_fincimex' => $disponible_fincimex,
                    'saldo_fincimex' => $saldo_fincimex,
                    'saldo_caja' => $saldo_caja,
                    'unidad_id' => $nunidadid,
                );
            }
        }

        return new JsonResponse(array('rows' => $_data));
    }

    public function guardarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $nunidadid = $request->get('unidadid');
        $mes = $request->get('mes');
        $anno = $request->get('anno');
        $datos_demandas = json_decode($request->get('demandas'));


        for ($i = 0, $iMax = \count($datos_demandas); $i < $iMax; $i++) {
            $id = $datos_demandas[$i]->demanda_id;
            $tipo_combustible = $em->getRepository('PortadoresBundle:TipoCombustible')->find($datos_demandas[$i]->tipo_combustible_id);
            $cant_litros = $datos_demandas[$i]->cant_litros;
            $comb_planificado = $datos_demandas[$i]->comb_planificado;
            $disponible_fincimex = $datos_demandas[$i]->disponible_fincimex;
            $saldo_fincimex = $datos_demandas[$i]->saldo_fincimex;
            $saldo_caja = $datos_demandas[$i]->saldo_caja;
            if ($id != '') {
                $demanda = $em->getRepository('PortadoresBundle:DemandaCombustible')->find($id);
                $demanda->setCantLitros($cant_litros);
                $em->persist($demanda);
            } else {
                $demanda = new DemandaCombustible();
                $demanda->setUnidad($em->getRepository('PortadoresBundle:Unidad')->find($nunidadid));
                $demanda->setMes($mes);
                $demanda->setAnno($anno);
                $demanda->setTipoCombustible($tipo_combustible);
                $demanda->setCantLitros($cant_litros);
                $demanda->setCombPlanificado($comb_planificado);
                $demanda->setDisponibleFincimex($disponible_fincimex);
                $demanda->setSaldoFincimex($saldo_fincimex);
                $demanda->setSaldoCaja($saldo_caja);
                $demanda->setVisible(true);
            }
            $em->persist($demanda);
        }

        try {
            $em->flush();
        } catch (Exception $exceptione) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
        return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Demanda realizada con éxito.'));
    }

    public function delAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = $request->get('id');
        $entity = $em->getRepository('PortadoresBundle:DemandaCombustible')->find($id);
        $entity->setVisible(false);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Acción realizada con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    private function saldoCaja($tipoCombustible, $unidad)
    {
        $em = $this->getDoctrine()->getManager();
        $tarjetas = $em->getRepository('PortadoresBundle:Tarjeta')->findBy(array('nunidadid' => $unidad, 'ntipoCombustibleid' => $tipoCombustible, 'visible' => true));
        $suma = 0;
        foreach ($tarjetas as $tarjeta) {
            $suma += $tarjeta->getImporte() / $tipoCombustible->getPrecio();
        }

        return $suma;
    }

    private function cantPlanificada($tipoCombustible, $unidad, $mes, $anno)
    {
        $em = $this->getDoctrine()->getManager();

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($unidad), $_unidades);

        $unidades_string = $this->unidadesToString($_unidades);

        $arr_mes = array(1 => 'ene', 2 => 'feb', 3 => 'mar', 4 => 'abr', 5 => 'may', 6 => 'jun', 7 => 'jul', 8 => 'ago', 9 => 'sep', 10 => 'oct', 11 => 'nov', 12 => 'dic');

        $sql = "SELECT 
                  sum(p.combustible_litros_$arr_mes[$mes]) as comb_planificado
                  FROM datos.planificacion_combustible as p 
                  join nomencladores.vehiculo as v on p.vehiculoid = v.id
                  where p.anno = $anno and v.nunidadid in ($unidades_string) and v.ntipo_combustibleid ='$tipoCombustible' and p.aprobada = true";
        $_data_cup = $this->getDoctrine()->getConnection()->fetchAll($sql);

        $sql = "SELECT 
                  sum(p.combustible_litros_$arr_mes[$mes]) as comb_planificado
                  FROM datos.planificacion_combustible_cuc as p 
                  join nomencladores.vehiculo as v on p.vehiculoid = v.id
                  where p.anno = $anno and v.nunidadid in ($unidades_string) and v.ntipo_combustibleid ='$tipoCombustible' and p.aprobada = true";
        $_data_cuc = $this->getDoctrine()->getConnection()->fetchAll($sql);

        return (int)$_data_cup[0]['comb_planificado'] + (int)$_data_cuc[0]['comb_planificado'];
    }

    private function unidadesToString($_unidades){
        $_string_unidades = "'" . $_unidades[0]. "'";
        for ($i = 1; $i < count($_unidades); $i++) {
            $_string_unidades .= ",'" . $_unidades[$i]. "'";
        }
        return $_string_unidades;
    }


}