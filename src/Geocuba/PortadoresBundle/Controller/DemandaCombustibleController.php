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
use Geocuba\PortadoresBundle\Util\MonedaEnum;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;

class DemandaCombustibleController extends Controller
{
    use ViewActionTrait;

    ///Modificar los tipos de datos en BD
    public function loadAction(Request $request)
    {
        $nunidadid = $request->get('unidadid');
        $mes = $request->get('mes');
        $anno = $request->get('anno');
        $moneda_id = $request->get('moneda');

        $_data = array();
        $em = $this->getDoctrine()->getManager();

        $_unidades[0] = $nunidadid;
        $entities = $em->getRepository('PortadoresBundle:TipoCombustible')->findBy(array('visible' => true));
        $moneda = $em->getRepository('PortadoresBundle:Moneda')->findOneBy(array('id' => $moneda_id));

        foreach ($entities as $entity) {
            $demanda = $em->getRepository('PortadoresBundle:DemandaCombustible')->findOneBy(array('unidad' => $nunidadid,'moneda' => $moneda, 'tipoCombustible' => $entity, 'mes' => $mes, 'anno' => $anno, 'visible' => true));
            if ($demanda) {
                $_data[] = array(
                    'demanda_id' => $demanda->getId(),
                    'tipo_combustible' => $demanda->getTipoCombustible()->getNombre(),
                    'tipo_combustible_id' => $demanda->getTipoCombustible()->getId(),
                    'moneda_id' => $demanda->getMoneda()->getId(),
                    'moneda' => $demanda->getMoneda()->getNombre(),
                    'cant_litros' => $demanda->getCantLitros(),
                    'propuesta' => $demanda->getCombPlanificado() - $demanda->getDisponibleFincimex( )- $demanda->getSaldoFincimex() - $demanda->getSaldoCaja(),
                    'comb_planificado' => $demanda->getCombPlanificado(),
                    'disponible_fincimex' => $demanda->getDisponibleFincimex(),
                    'saldo_fincimex' => $demanda->getSaldoFincimex(),
                    'saldo_caja' => $demanda->getSaldoCaja(),
                    'unidad_id' => $demanda->getUnidad()->getId(),
                );
            } else {
                //Obtener Combustible Planificado en el mes
                $comb_planificado = $this->cantPlanificada($entity->getId(), $nunidadid, $mes, $anno, $moneda);

                //Obtener Disponible Fincimex
                $disponible_fincimex = Datos::getPlanDisponibleFincimex($em,$nunidadid,$entity, $moneda);

                //Obtener Saldo Fincimex
                $saldo_fincimex = Datos::getSaldoDisponibleFincimex($em,$nunidadid,$entity, $moneda);

                //Obtener Saldo caja
                $saldo_caja = Datos::getSaldoCaja($em,$nunidadid,$entity, $moneda);

                $_data[] = array(
                    'demanda_id' => '',
                    'tipo_combustible' => $entity->getNombre(),
                    'tipo_combustible_id' => $entity->getId(),
                    'moneda_id' => $moneda->getId(),
                    'moneda' => $moneda->getNombre(),
                    'cant_litros' => 0.00,
                    'propuesta' => floatval($comb_planificado - $disponible_fincimex - $saldo_fincimex - $saldo_caja),
                    'comb_planificado' => floatval($comb_planificado),
                    'disponible_fincimex' => floatval($disponible_fincimex),
                    'saldo_fincimex' => floatval($saldo_fincimex),
                    'saldo_caja' => floatval($saldo_caja),
                    'unidad_id' => $nunidadid
                );
            }
        }

        return new JsonResponse(array('rows' => $_data));
    }

    public function guardarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $nunidadid = $request->get('unidadid');
        $moneda_id = $request->get('moneda_id');
        $mes = $request->get('mes');
        $anno = $request->get('anno');
        $datos_demandas = json_decode($request->get('demandas'));
        $moneda = $em->getRepository('PortadoresBundle:Moneda')->findOneBy(array('id' => $moneda_id));


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
                $demanda->setMoneda($moneda);
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
        } catch (\Exception $exceptione) {
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
        } catch (\Exception $ex) {
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

    private function cantPlanificada($tipoCombustible, $unidad, $mes, $anno, $moneda)
    {
        $em = $this->getDoctrine()->getManager();

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($unidad), $_unidades);

        $unidades_string = $this->unidadesToString($_unidades);

        $arr_mes = array(1 => 'ene', 2 => 'feb', 3 => 'mar', 4 => 'abr', 5 => 'may', 6 => 'jun', 7 => 'jul', 8 => 'ago', 9 => 'sep', 10 => 'oct', 11 => 'nov', 12 => 'dic');
        $planificado = array();
        if($moneda->getId() == MonedaEnum::cup){
            $sql = "SELECT 
                  sum(p.combustible_litros_$arr_mes[$mes]) as comb_planificado
                  FROM datos.planificacion_combustible as p 
                  join nomencladores.vehiculo as v on p.vehiculoid = v.id
                  where p.anno = $anno and v.nunidadid in ($unidades_string) and v.ntipo_combustibleid ='$tipoCombustible' and p.aprobada = true";
            $planificado = $this->getDoctrine()->getConnection()->fetchAll($sql);
        }
        if($moneda->getId() == MonedaEnum::cuc){
            $sql = "SELECT 
                  sum(p.combustible_litros_$arr_mes[$mes]) as comb_planificado
                  FROM datos.planificacion_combustible_cuc as p 
                  join nomencladores.vehiculo as v on p.vehiculoid = v.id
                  where p.anno = $anno and v.nunidadid in ($unidades_string) and v.ntipo_combustibleid ='$tipoCombustible' and p.aprobada = true";
            $planificado = $this->getDoctrine()->getConnection()->fetchAll($sql);
        }

        return (int)$planificado[0]['comb_planificado'];
    }

    private function unidadesToString($_unidades){
        $_string_unidades = "'" . $_unidades[0]. "'";
        for ($i = 1; $i < count($_unidades); $i++) {
            $_string_unidades .= ",'" . $_unidades[$i]. "'";
        }
        return $_string_unidades;
    }


}