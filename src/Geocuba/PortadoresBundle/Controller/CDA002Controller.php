<?php
/**
 * Created by PhpStorm.
 * User: yosley
 * Date: 09/03/2016
 * Time: 15:52
 */

namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\AdminBundle\Util\Util;
use Geocuba\PortadoresBundle\Entity\CDA002;
use Geocuba\PortadoresBundle\Entity\DescuentoBajo;
use Geocuba\PortadoresBundle\Entity\DescuentoDeterioro;
use Geocuba\PortadoresBundle\Entity\DescuentoSobreconsumo;
use Geocuba\PortadoresBundle\Entity\Actividad;
use Geocuba\PortadoresBundle\Entity\Unidad;
use Doctrine\Common\CommonException;
use Doctrine\Common\Util\Debug;
use Geocuba\PortadoresBundle\Util\MonedaEnum;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\PortadoresBundle\Util\DocumentosEnum;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;


class CDA002Controller extends Controller
{

    use ViewActionTrait;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $mes = $request->get('mes');
        $anno = $request->get('anno');

        $portadorName = $request->get('portadorName');
        $unidadid = $request->get('unidadid');
        $portador_id = $request->get('portadorid');
        $monedaStr = $request->get('moneda');


        if ($portadorName !== "ELECTRICIDAD") {
            $entities = $em->getRepository('PortadoresBundle:CDA002')->findBy(array('nunidadid' => $unidadid, 'portador' => $portador_id, 'anno' => $anno, 'mes' => $mes, 'moneda' => $monedaStr));
        } else {
            $entities = $em->getRepository('PortadoresBundle:CDA002')->findBy(array('nunidadid' => $unidadid, 'portador' => $portador_id, 'anno' => $anno, 'mes' => $mes));

        }

        $data = array();
        /** @var CDA002 $entity */
        foreach ($entities as $entity) {
            $data[] = array(
                'id' => $entity->getId(),
                'codigo' => $entity->getNactividadid()->getCodigomep(),
                'actividad' => $entity->getNactividadid()->getId(),
                'actividad_nombre' => $entity->getNactividadid()->getNombre(),
                'um_actividad' => $entity->getNactividadid()->getUmActividad()->getId(),
                'um_actividad_nombre' => $entity->getNactividadid()->getUmActividad()->getNivelActividad(),
                'nivel_activ' => $entity->getNivelActividad(),
                'consumo' => $entity->getConsumo(),
                'indice' => $entity->getIndice(),
                'nivel_activ_acum' => $entity->getNivelActividadAcum(),
                'consumo_acum' => $entity->getConsumoAcum(),
                'indice_acum' => $entity->getIndiceAcum(),
                'nivel_actividad_plan' => $entity->getNivelActividadPlan(),
                'consumo_plan' => floatval($entity->getConsumoPlan()),
                'indice_plan' => $entity->getIndicePlan(),
                'indice_anual' => $entity->getIndiceAnual(),
                'mes' => $entity->getMes(),
                'codigo_gae' => $entity->getNactividadid()->getCodigogae(),
                'desc_det_id' => $entity->getDescuentoDeterioroid() ? $entity->getDescuentoDeterioroid()->getId() : null,
                'desc_det_cant' => $entity->getDescuentoDeterioroid() ? $entity->getDescuentoDeterioroid()->getCantidad() : null,
                'desc_det_mes' => $entity->getDescuentoDeterioroid() ? $entity->getDescuentoDeterioroid()->getMes() : null,
                'desc_det_acumulado' => $entity->getDescuentoDeterioroid() ? $entity->getDescuentoDeterioroid()->getAcumulado() : null,
                'desc_bajo_nivel_id' => $entity->getDescuentoDeterioroid() ? $entity->getDescuentoBajoid()->getId() : null,
                'desc_bajo_nivel_cant' => $entity->getDescuentoDeterioroid() ? $entity->getDescuentoBajoid()->getCantidad() : null,
                'desc_bajo_nivel_mes' => $entity->getDescuentoDeterioroid() ? $entity->getDescuentoBajoid()->getMes() : null,
                'desc_bajo_nivel_acumulado' => $entity->getDescuentoDeterioroid() ? $entity->getDescuentoBajoid()->getAcumulado() : null,
                'desc_sobreconsumo_id' => $entity->getDescuentoDeterioroid() ? $entity->getDescuentoSobreconsumoid()->getId() : null,
                'desc_sobreconsumo_cant' => $entity->getDescuentoDeterioroid() ? $entity->getDescuentoSobreconsumoid()->getCantidad() : null,
                'desc_sobreconsumo_mes' => $entity->getDescuentoDeterioroid() ? $entity->getDescuentoSobreconsumoid()->getMes() : null,
                'desc_sobreconsumo_acumulado' => $entity->getDescuentoDeterioroid() ? $entity->getDescuentoSobreconsumoid()->getAcumulado() : null,
                'unidad' => $entity->getNunidadid()->getId(),
                'portador' => $entity->getPortador()->getId(),
                'monedaid' => ($portadorName !== "ELECTRICIDAD") ? $entity->getMoneda()->getId() : '',
                'moneda' => ($portadorName !== "ELECTRICIDAD") ? $entity->getMoneda()->getNombre() : '',
                'relacion_real_plan' => $entity->getRelacionRealPlan(),
                'relacion_acumulado_aprobado' => $entity->getRelacionAcumAprob()
            );
        }
        return new JsonResponse(array('rows' => $data));
    }

    /**
     * @Route("/ncda002/generarCDA002",name="generarCDA002",options={"expose"=true})
     * @param Request $request
     * @return JsonResponse
     */
    public function generarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $portadorid = $request->get('portadorid');
        $monedaStr = $request->get('moneda');

        $unidadid = $request->get('unidadid');
        $portadorName = $request->get('portadorName');
        $anno = $request->get('anno');
        $mes = $request->get('mes');

        if ($portadorName !== "ELECTRICIDAD") {
            $cda002_existente = $em->getRepository('PortadoresBundle:CDA002')->findBy(array('anno' => $anno, 'mes' => $mes, 'portador' => $portadorid, 'nunidadid' => $unidadid, 'moneda' => $monedaStr));
        } else {
            $cda002_existente = $em->getRepository('PortadoresBundle:CDA002')->findBy(array('anno' => $anno, 'mes' => $mes, 'portador' => $portadorid, 'nunidadid' => $unidadid));

        }

        foreach ($cda002_existente as $cda) {
            try {
                $em->remove($cda);
                $em->flush();
            } catch (\Exception $ex) {
                if ($ex instanceof HttpException) {
                    return new JsonResponse(['success' => false, 'message' => $ex->getMessage()]);
                } else {
                    throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $ex->getMessage(), $ex);
                }
            }
        }

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($unidadid), $_unidades);//
        $unidades_string = $this->unidadesToString($_unidades);

        $portador = $em->getRepository('PortadoresBundle:Portador')->find($portadorid);

        /*  Si el portador es GLP */
        if ($portador->getNombre() == 'GLP') {
            $actividades = $em->getRepository('PortadoresBundle:Actividad')->findBy(array('portadorid' => $portador, 'visible' => true));
            if ($actividades) {
                foreach ($actividades as $actividad) {
                    $actividadid = $actividad['id'];
                    /** @var Actividad $actividad */
                    $actividad = $em->getRepository('PortadoresBundle:Actividad')->find($actividadid);

                    $indice_real = 0;
                    $indice_plan = 0;

                    $acumulado = $this->getDoctrine()->getConnection()->fetchAll("Select * from datos.cda002_acumulado WHERE anno=$anno AND nactividadid = '$actividadid'");

                    $consumo_acumulado = count($acumulado) > 0 ? $acumulado[0]['consumo_acum'] : 0;
                    $nivel_act_acumulado = count($acumulado) > 0 ? $acumulado[0]['nivel_act_acum'] : 0;
                    $indice_acumulado = $nivel_act_acumulado == 0 ? 0 : $consumo_acumulado / $nivel_act_acumulado;


                    $desc_bajo_nivel_cant = 0;
                    $desc_bajo_nivel_mes = 0;

                    $desc_bajo = new DescuentoBajo();
                    $desc_bajo->setCantidad($desc_bajo_nivel_cant);
                    $desc_bajo->setMes($desc_bajo_nivel_mes);
                    $desc_bajo->setAcumulado($desc_bajo_nivel_mes);

                    try {
                        $em->persist($desc_bajo);
                        $em->flush();

                    } catch (\Exception $ex) {
                        if ($ex instanceof HttpException) {
                            return new JsonResponse(['success' => false, 'message' => $ex->getMessage()]);
                        } else {
                            throw new HttpException(\Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR, $ex->getMessage(), $ex);
                        }
                    }

                    $relacion_real_plan = $indice_plan == 0 ? 0 : $indice_real / $indice_plan;

                    $desc_deterioro_cant = 0;
                    $desc_deterioro_mes = $desc_deterioro_cant = 0;

                    $desc_deterioro = new DescuentoDeterioro();
                    $desc_deterioro->setCantidad($desc_deterioro_cant);
                    $desc_deterioro->setMes($desc_deterioro_mes);
                    $desc_deterioro->setAcumulado($desc_deterioro_mes);

                    try {
                        $em->persist($desc_deterioro);
                        $em->flush();

                    } catch (\Exception $ex) {
                        if ($ex instanceof HttpException) {
                            return new JsonResponse(['success' => false, 'message' => $ex->getMessage()]);
                        } else {
                            throw new HttpException(\Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR, $ex->getMessage(), $ex);
                        }
                    }


                    $desc_sobreconsumo_cant = 0;
                    $desc_sobreconsumo_mes = 0;

                    $descuento_sobreconsumo = new DescuentoSobreconsumo();
                    $descuento_sobreconsumo->setCantidad($desc_sobreconsumo_cant);
                    $descuento_sobreconsumo->setMes($desc_sobreconsumo_mes);
                    $descuento_sobreconsumo->setAcumulado($desc_sobreconsumo_mes);

                    try {
                        $em->persist($descuento_sobreconsumo);
                        $em->flush();

                    } catch (\Exception $ex) {
                        if ($ex instanceof HttpException) {
                            return new JsonResponse(['success' => false, 'message' => $ex->getMessage()]);
                        } else {
                            throw new HttpException(\Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR, $ex->getMessage(), $ex);
                        }
                    }

                    $entity_cda002 = new CDA002();

                    $entity_cda002->setMes($mes);
                    $entity_cda002->setAnno($anno);

                    $entity_cda002->setUnidadid($em->getRepository('PortadoresBundle:Unidad')->find($unidadid));
                    $entity_cda002->setActividadid($actividad);
                    $entity_cda002->setPortador($em->getRepository('PortadoresBundle:Portador')->find($portadorid));


                    $entity_cda002->setIndice($indice_real);
                    $entity_cda002->setConsumo(0);
                    $entity_cda002->setNivelActividad(0);

                    $entity_cda002->setNivelActividadPlan(0);
                    $entity_cda002->setConsumoPlan(0);
                    $entity_cda002->setIndicePlan(0);

                    $entity_cda002->setIndiceAcum($indice_acumulado);
                    $entity_cda002->setConsumoAcum($consumo_acumulado);
                    $entity_cda002->setNivelActividadAcum($nivel_act_acumulado);

                    $entity_cda002->setRelacionRealPlan($relacion_real_plan);

                    $entity_cda002->setDescuentoBajoid($desc_bajo);
                    $entity_cda002->setDescuentoDeterioroid($desc_deterioro);
                    $entity_cda002->setDescuentoSobreconsumoid($descuento_sobreconsumo);


                    try {
                        $em->persist($entity_cda002);
                        $em->flush();

                    } catch (\Exception $ex) {
                        if ($ex instanceof HttpException) {
                            return new JsonResponse(['success' => false, 'message' => $ex->getMessage()]);
                        } else {
                            throw new HttpException(\Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR, $ex->getMessage(), $ex);
                        }
                    }

                }
            }

        } /*Si el portador es electricidad*/
        if ($portador->getNombre() == 'ELECTRICIDAD') {
            /*BUSCAMOS EL PLAN EN EL CDA 001*/
            $sql = "SELECT max(cda001.actividad_id) as actividad_id, sum(cda001.plan_final_nivel_act) as plan_final_nivel_act,
                           sum(cda001.plan_final_consumo) as plan_final_consumo, sum(cda001.plan_final_indice) as  plan_final_indice
                        FROM datos.cda001 WHERE nunidadid IN ($unidades_string) and cda001.portadorid = '$portadorid' GROUP BY cda001.actividad_id";

            $cda001 = $this->getDoctrine()->getConnection()->fetchAll($sql);

            /* POR CADA UNO DE LOS ELEMENTOS DEL CDA 001*/
            foreach ($cda001 as $cda) {

                $actividad = $em->find('PortadoresBundle:Actividad', $cda['actividad_id']);

                $sqlPrepago = "select 
                                    max(s.nactividadid) as actividad_id,
                                    sum(ap.consumo_total_real) as consumo,
                                    sum(tb.horas) as nivel_act,
                                    sum(ap.plan_diario) as consumo_total_plan                                 
                                from datos.autolecturaprepago ap
                                inner join nomencladores.servicio s on s.id = ap.serviciosid
                                inner join nomencladores.turnotrabajo tb on tb.id = s.turnos_trabajo
                                where s.nactividadid = '" . $cda['actividad_id'] . "' and extract(month from ap.fecha_lectura) = $mes and extract(year from ap.fecha_lectura) = $anno and s.nunidadid = '$unidadid' ";
                $prepago = $this->getDoctrine()->getConnection()->fetchAll($sqlPrepago);


                $sqlTresescalas = "select 
                                    max(s.nactividadid) as actividad_id,
                                    sum(aut.consumo_total_real) as consumo,
                                    sum(tb.horas) as nivel_act,
                                    sum(aut.consumo_total_plan) as consumo_total_plan                             
                                from datos.autolectura_tresescalas aut
                                inner join nomencladores.servicio s on s.id = aut.serviciosid
                                inner join nomencladores.turnotrabajo tb on tb.id = s.turnos_trabajo
                                where s.nactividadid = '" . $cda['actividad_id'] . "' and extract(month from aut.fecha_lectura) = $mes and extract(year from aut.fecha_lectura) = $anno and s.nunidadid = '$unidadid' ";

                $tresescalas = $this->getDoctrine()->getConnection()->fetchAll($sqlTresescalas);


                $cda002 = [
                    'consumo_real' => $prepago[0]['consumo'] + $tresescalas[0]['consumo'],
                    'nivel_act' => $prepago[0]['nivel_act'] + $tresescalas[0]['nivel_act'],
                    'consumo_total_plan' => $prepago[0]['consumo_total_plan'] + $tresescalas[0]['consumo_total_plan'],
                ];

                $indice_real = $cda002['nivel_act'] !== 0 ? $cda002['consumo_real'] / $cda002['nivel_act'] : 0;
                $consumo_plan = $cda002['consumo_total_plan'] !== 0 ? $cda002['consumo_total_plan'] : 0;

                $cda002Anterior = null;
                $i = $mes - 1;
                while ($i > 0 && !$cda002Anterior) {
                    $cda002Anterior = $em->getRepository('PortadoresBundle:CDA002')->findOneBy(['anno' => $anno, 'mes' => $mes, 'portador' => $portadorid, 'nactividadid' => $cda['actividad_id']]);
                    $i--;
                }
                $consumo_acumulado = $cda002Anterior ? $cda002Anterior->getConsumoAcum() * 1000 + $cda002['consumo_real'] : $cda002['consumo_real'];
                $nivel_act_acumulado = $cda002Anterior ? $cda002Anterior->getNivelActividadAcum() * 1000 + $cda002['nivel_act'] : $cda002['nivel_act'];
                $indice_acumulado = $nivel_act_acumulado ? $consumo_acumulado / $nivel_act_acumulado : 0;

                $relacion_real_plan = $cda['plan_final_indice'] == 0 ? 0 : $indice_real / $cda['plan_final_indice'];
                $relacion_acumulado_plan = $cda['plan_final_indice'] == 0 ? 0 : $indice_acumulado / $cda['plan_final_indice'];

                $entity_cda002 = new CDA002();
                if($cda002['consumo_real'] !== 0 ){
                    $entity_cda002->setMes($mes);
                    $entity_cda002->setAnno($anno);
                    $entity_cda002->setNunidadid($em->getRepository('PortadoresBundle:Unidad')->find($unidadid));
                    $entity_cda002->setNactividadid($actividad);
                    $entity_cda002->setPortador($em->getRepository('PortadoresBundle:Portador')->find($portadorid));
                    $entity_cda002->setNumNivelActividadid($actividad->getUmActividad());

                    $entity_cda002->setIndice($indice_real);
                    $entity_cda002->setConsumo($cda002['consumo_real'] / 1000);
                    $entity_cda002->setNivelActividad($cda002['nivel_act'] / 1000);
                    $entity_cda002->setNivelActividadPlan($cda['plan_final_nivel_act'] / 1000);
                    $entity_cda002->setConsumoPlan($consumo_plan / 1000);
//                $entity_cda002->setConsumoPlan($cda['plan_final_consumo'] / 1000);
                    $entity_cda002->setIndicePlan($cda['plan_final_indice']);
                    $entity_cda002->setIndiceAcum($indice_acumulado);
                    $entity_cda002->setConsumoAcum($consumo_acumulado / 1000);
                    $entity_cda002->setNivelActividadAcum($nivel_act_acumulado / 1000);
                    $entity_cda002->setRelacionRealPlan($relacion_real_plan);
                    $entity_cda002->setRelacionAcumAprob($relacion_acumulado_plan);
                    try {
                        $em->persist($entity_cda002);
                        $em->flush();
                    } catch (\Exception $ex) {
                        if ($ex instanceof HttpException) {
                            return new JsonResponse(['success' => false, 'message' => $ex->getMessage()]);
                        } else {
                            throw new HttpException(\Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR, $ex->getMessage(), $ex);
                        }
                    }
                }

            }


        }
        else {
            $meses = ['anual', 'ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
            //Buscar los registros de combustibles para determinar los consumos de comb y nivel de act
            $sql = "select sum(rca.combustible)as combustible,
                           sum(rca.km) as km, 
                           rc.vehiculoid, 
                           p.id as portadorid
                        from datos.registro_combustible as rc
                        inner join nomencladores.vehiculo nvehiculo on nvehiculo.id = rc.vehiculoid
                        inner join nomencladores.tipo_combustible tc on nvehiculo.ntipo_combustibleid = tc.id
                        inner join nomencladores.portador p on p.id = tc.portadorid
                        inner join datos.registro_combustible_analisis as rca on rca.registro_combustible_id = rc.id
                        where rca.conceptoid = '3' and date_part('YEAR', rc.fecha) = $anno and date_part('MONTH', rc.fecha) = $mes and p.id = '$portadorid'
                        and nvehiculo.nunidadid in ($unidades_string) and rc.visible = true 
                        group by rc.vehiculoid,p.id ";
            $sqlRCA = $this->getDoctrine()->getConnection()->fetchAll($sql);
            $arr = array();

            foreach ($sqlRCA as $reg) {
                $idVeh = $reg['vehiculoid'];
                //Buscar los consumos por actividades en las liquidaciones porque el 117 no diferencia cuanto se consume por una actividad u otra

                $sqlL = "select m.id as moneda, 
                                 a.id as actividadid, 
                                 a.nombre as actividad, 
                                 liq.nvehiculoid, 
                                 sum(liq.cant_litros) as cant_litros
                        from datos.liquidacion as liq
                        join nomencladores.tarjeta as t on liq.ntarjetaid = t.id
                        join nomencladores.moneda as m on m.id = t.nmonedaid
                        join nomencladores.actividad as a on a.id = liq.nactividadid
                        join nomencladores.portador as p on p.id = a.portadorid
                        where liq.nvehiculoid = '$idVeh' and liq.visible = true and  date_part('YEAR', liq.fecha_vale) = $anno and date_part('MONTH', liq.fecha_vale) = $mes and p.id = '$portadorid' and m.id = '$monedaStr'
                        group by m.id , a.id, liq.nvehiculoid, a.portadorid";

                $sqlLIQ = $this->getDoctrine()->getConnection()->fetchAll($sqlL);

                if ($monedaStr === MonedaEnum::cup) {
                    $sqlP = "select pc.combustible_litros_$meses[$mes],
                               pc.nivel_act_kms_$meses[$mes], 
                               pc.nivel_act_kms_total, 
                               pc.combustible_litros_total 
                        
                               from datos.planificacion_combustible as pc
                               inner join nomencladores.vehiculo nvehiculo on nvehiculo.id = pc.vehiculoid
                               inner join nomencladores.tipo_combustible tc on nvehiculo.ntipo_combustibleid = tc.id
                               inner join nomencladores.portador p on p.id = tc.portadorid
                               WHERE pc.anno = $anno AND p.id = '$portadorid' and pc.vehiculoid = '$idVeh'";
                } else {
                    $sqlP = "select pc.combustible_litros_$meses[$mes],
                               pc.nivel_act_kms_$meses[$mes], 
                               pc.nivel_act_kms_total, 
                               pc.combustible_litros_total 
                        
                               from datos.planificacion_combustible_cuc as pc
                               inner join nomencladores.vehiculo nvehiculo on nvehiculo.id = pc.vehiculoid
                               inner join nomencladores.tipo_combustible tc on nvehiculo.ntipo_combustibleid = tc.id
                               inner join nomencladores.portador p on p.id = tc.portadorid
                               WHERE pc.anno = $anno AND p.id = '$portadorid' and pc.vehiculoid = '$idVeh'";
                }


                $sqlPlan = $this->getDoctrine()->getConnection()->fetchAll($sqlP);


                if (!count($sqlLIQ)) {
                    $moneda = "";
                    $vehTar = $em->getRepository('PortadoresBundle:Vehiculo')->find($idVeh)->getTarjetas();
                    count($vehTar) > 0 ? $moneda = $vehTar[0]->getTarjetaid()->getMonedaid()->getId() : "";

                    if($monedaStr === $moneda){
                        $arr[] = array(
                            'moneda' => $monedaStr,
                            'actividadid' => $em->getRepository('PortadoresBundle:Vehiculo')->find($idVeh)->getActividad()->getId(),
                            'nombre_actividad' => $em->getRepository('PortadoresBundle:Vehiculo')->find($idVeh)->getActividad()->getNombre(),
                            'nvehiculoid' => $idVeh,
                            'cant_litros' => 0,
                            'comb_debio_consumir' => (count($sqlPlan) > 0) ? $sqlPlan[0]["combustible_litros_$meses[$mes]"] : 0,
                            'nivel_act_debio_realizar' => (count($sqlPlan) > 0) ? $sqlPlan[0]["nivel_act_kms_$meses[$mes]"] : 0,
                            'nivel_act' => $reg['km'],
                            'consumo_real' => $reg['combustible']
                        );
                    }

                } else {
                    foreach ($sqlLIQ as $liq) {
                        if ($liq['moneda'] === $monedaStr) {
                            $arr[] = array(
                                'moneda' => $liq['moneda'],
                                'actividadid' => $liq['actividadid'],
                                'nombre_actividad' => $liq['actividad'],
                                'nvehiculoid' => $liq['nvehiculoid'],
                                'cant_litros' => $liq['cant_litros'],
                                'comb_debio_consumir' => (count($sqlPlan) > 0) ? $sqlPlan[0]["combustible_litros_$meses[$mes]"] : 0,
                                'nivel_act_debio_realizar' => (count($sqlPlan) > 0) ? $sqlPlan[0]["nivel_act_kms_$meses[$mes]"] : 0,
                                'nivel_act' => $reg['km'],
                                'consumo_real' => $reg['combustible']
                            );
                        }
                    }
                }
            }


            if (count($arr) > 0) {
                $ids_actividades = [];
                foreach ($arr as $item) {
                    $em->getRepository('PortadoresBundle:Actividad')->find($item['actividadid']);
                    $id_act = $item['actividadid'];
                    if (!in_array($id_act, $ids_actividades)) {
                        $ids_actividades[] = $id_act;
                    }
                }

                $result = [];
                foreach ($ids_actividades as $unique_id) {
                    $temp = [];
                    foreach ($arr as $arr_act) {
                        $id = $arr_act["actividadid"];

                        if ($id === $unique_id) {
                            $temp[] = $arr_act;
                        }
                    }
                    $actividadid = $temp[0];
                    $actividadid["cantidad"] = 0;
                    for ($s = 1; $s < sizeof($temp); $s++) {
                        $actividadid['consumo_real'] = $actividadid['consumo_real'] + $temp[$s]['consumo_real'];
                        $actividadid['comb_debio_consumir'] = $actividadid['comb_debio_consumir'] + $temp[$s]['comb_debio_consumir'];
                        $actividadid['nivel_act'] = $actividadid['nivel_act'] + $temp[$s]['nivel_act'];
                        $actividadid['nivel_act_debio_realizar'] = $actividadid['nivel_act_debio_realizar'] + $temp[$s]['nivel_act_debio_realizar'];
                    }
                    $cda002[] = $actividadid;
                }

                $consumo_total = 0;

                foreach ($cda002 as $cda2) {
                    $consumo_total += $cda2['consumo_real'];
                }

                foreach ($cda002 as $cda) {
                    $actividadid = $cda['actividadid'];
                    /** @var Actividad $actividad */
                    $actividad = $em->getRepository('PortadoresBundle:Actividad')->find($actividadid);
                    $indice_real = $cda['nivel_act'] == 0 ? 0 : $cda['consumo_real'] / $cda['nivel_act'];
                    $indice_plan = $cda['nivel_act_debio_realizar'] == 0 ? 0 : $cda['comb_debio_consumir'] / $cda['nivel_act_debio_realizar'];
                    $acumulado = $this->getDoctrine()->getConnection()->fetchAll("Select * from datos.cda002_acumulado WHERE anno = $anno AND nactividadid = '$actividadid'");

                    $consumo_acumulado = empty($acumulado) ? $cda['consumo_real'] : $acumulado[0]['consumo_acum'] + $cda['consumo_real'];
                    $nivel_act_acumulado = empty($acumulado) ? $cda['nivel_act'] : $acumulado[0]['nivel_act_acum'] + $cda['nivel_act'];
                    $indice_acumulado = $nivel_act_acumulado == 0 ? 0 : $consumo_acumulado / $nivel_act_acumulado;

                    $desc_bajo_nivel_cant = $consumo_total > 0 && $cda['nivel_act_debio_realizar'] > 0
                    && $cda['comb_debio_consumir'] && $indice_plan > 0 && $cda['nivel_act_debio_realizar'] > $cda['nivel_act'] ? 1 : 0;
                    $desc_bajo_nivel_mes = $desc_bajo_nivel_cant == 0 ? 0 : $desc_bajo_nivel_cant == 1 && $cda['comb_debio_consumir'] > $cda['consumo_real'] ? $cda['comb_debio_consumir'] - $cda['consumo_real'] : 0;

                    $desc_bajo = new DescuentoBajo();
                    $desc_bajo->setCantidad($desc_bajo_nivel_cant);
                    $desc_bajo->setMes($desc_bajo_nivel_mes);
                    $desc_bajo->setAcumulado($desc_bajo_nivel_mes);

                    try {
                        $em->persist($desc_bajo);
                        $em->flush();
                    } catch (\Exception $ex) {
                        if ($ex instanceof HttpException) {
                            return new JsonResponse(['success' => false, 'message' => $ex->getMessage()]);
                        } else {
                            throw new HttpException(\Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR, $ex->getMessage(), $ex);
                        }
                    }

                    $relacion_real_plan = $indice_plan == 0 ? 0 : $indice_real / $indice_plan;

                    $desc_deterioro_cant = $relacion_real_plan < 1.03 ? 0 : 1;

                    $desc_deterioro_mes = $desc_deterioro_cant == 1 ? ($indice_real - $indice_plan) * $cda['nivel_act'] : 0;

                    $desc_deterioro = new DescuentoDeterioro();
                    $desc_deterioro->setCantidad($desc_deterioro_cant);
                    $desc_deterioro->setMes($desc_deterioro_mes);
                    $desc_deterioro->setAcumulado($desc_deterioro_mes);

                    try {
                        $em->persist($desc_deterioro);
                        $em->flush();

                    } catch (\Exception $ex) {
                        if ($ex instanceof HttpException) {
                            return new JsonResponse(['success' => false, 'message' => $ex->getMessage()]);
                        } else {
                            throw new HttpException(\Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR, $ex->getMessage(), $ex);
                        }
                    }

                    $desc_sobreconsumo_cant = $cda['comb_debio_consumir'] > 0 && $cda['consumo_real'] > $cda['comb_debio_consumir'] ? 1 : 0;
                    $desc_sobreconsumo_mes = $desc_sobreconsumo_cant == 1 ? $cda['consumo_real'] - $cda['comb_debio_consumir'] : 0;

                    $descuento_sobreconsumo = new DescuentoSobreconsumo();
                    $descuento_sobreconsumo->setCantidad($desc_sobreconsumo_cant);
                    $descuento_sobreconsumo->setMes($desc_sobreconsumo_mes);
                    $descuento_sobreconsumo->setAcumulado($desc_sobreconsumo_mes);

                    try {
                        $em->persist($descuento_sobreconsumo);
                        $em->flush();

                    } catch (\Exception $ex) {
                        if ($ex instanceof HttpException) {
                            return new JsonResponse(['success' => false, 'message' => $ex->getMessage()]);
                        } else {
                            throw new HttpException(\Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR, $ex->getMessage(), $ex);
                        }
                    }

                    $entity_cda002 = new CDA002();
                    $entity_cda002->setMes($mes);
                    $entity_cda002->setAnno($anno);

                    $entity_cda002->setNunidadid($em->getRepository('PortadoresBundle:Unidad')->find($unidadid));
                    $entity_cda002->setNactividadid($actividad);
                    $entity_cda002->setMoneda($em->getRepository('PortadoresBundle:Moneda')->find($monedaStr));
                    $entity_cda002->setPortador($em->getRepository('PortadoresBundle:Portador')->find($portadorid));
                    $entity_cda002->setNumNivelActividadid($actividad->getUmActividad());

                    $entity_cda002->setIndice($indice_real);
                    $entity_cda002->setConsumo($cda['consumo_real'] / 1000);
                    $entity_cda002->setNivelActividad($cda['nivel_act'] / 1000);

                    $entity_cda002->setNivelActividadPlan($cda['nivel_act_debio_realizar'] / 1000);
                    $entity_cda002->setConsumoPlan(floatval($cda['comb_debio_consumir'] / 1000));
                    $entity_cda002->setIndicePlan($indice_plan);

                    $entity_cda002->setIndiceAcum($indice_acumulado);
                    $entity_cda002->setConsumoAcum($consumo_acumulado / 1000);
                    $entity_cda002->setNivelActividadAcum($nivel_act_acumulado / 1000);

                    $entity_cda002->setRelacionRealPlan($relacion_real_plan);

                    $entity_cda002->setDescuentoBajoid($desc_bajo);
                    $entity_cda002->setDescuentoDeterioroid($desc_deterioro);
                    $entity_cda002->setDescuentoSobreconsumoid($descuento_sobreconsumo);


                    try {
                        $em->persist($entity_cda002);
                        $em->flush();

                    } catch (\Exception $ex) {
                        if ($ex instanceof HttpException) {
                            return new JsonResponse(['success' => false, 'message' => $ex->getMessage()]);
                        } else {
                            throw new HttpException(\Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR, $ex->getMessage(), $ex);
                        }
                    }
                }

            } else {
                return new JsonResponse(array('success' => false, 'cls' => 'warning', 'message' => 'No existen datos en el Análisis Equipo a Equipo para generar el CDA 002'));
            }
        }
        return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'CDA002 Generado con éxito.'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public
    function guardarCambiosAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $datos = json_decode($request->get('store'));
        for ($i = 0; $i < count($datos); $i++) {
            $id = $datos[$i]->id;

            /** @var  DescuentoDeterioro $descuento */
            $descuento = $em->getRepository('PortadoresBundle:DescuentoDeterioro')->find($datos[$i]->desc_det_id);
            $descuento->setCantidad($datos[$i]->desc_det_cant);
            $descuento->setMes($datos[$i]->desc_det_mes);
            $descuento->setAcumulado($datos[$i]->desc_det_acumulado);

            try {
                $em->persist($descuento);
                $em->flush();

            } catch (\Exception $ex) {
                if ($ex instanceof HttpException) {
                    return new JsonResponse(['success' => false, 'message' => $ex->getMessage()]);
                } else {
                    throw new HttpException(\Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR, $ex->getMessage(), $ex);
                }
            }

            /** @var  DescuentoBajo $descuento_bajo */
            $descuento_bajo = $em->getRepository('PortadoresBundle:DescuentoBajo')->find($datos[$i]->desc_bajo_nivel_id);
            $descuento_bajo->setCantidad($datos[$i]->desc_bajo_nivel_cant);
            $descuento_bajo->setMes($datos[$i]->desc_bajo_nivel_mes);
            $descuento_bajo->setAcumulado($datos[$i]->desc_bajo_nivel_acumulado);

            try {
                $em->persist($descuento_bajo);
                $em->flush();

            } catch (\Exception $ex) {
                if ($ex instanceof HttpException) {
                    return new JsonResponse(['success' => false, 'message' => $ex->getMessage()]);
                } else {
                    throw new HttpException(\Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR, $ex->getMessage(), $ex);
                }
            }

            /** @var  DescuentoSobreconsumo $descuento_sobreconsumo */
            $descuento_sobreconsumo = $em->getRepository('PortadoresBundle:DescuentoSobreconsumo')->find($datos[$i]->desc_sobreconsumo_id);
            $descuento_sobreconsumo->setCantidad($datos[$i]->desc_sobreconsumo_cant);
            $descuento_sobreconsumo->setMes($datos[$i]->desc_sobreconsumo_mes);
            $descuento_sobreconsumo->setAcumulado($datos[$i]->desc_sobreconsumo_acumulado);

            try {
                $em->persist($descuento_sobreconsumo);
                $em->flush();

            } catch (\Exception $ex) {
                if ($ex instanceof HttpException) {
                    return new JsonResponse(['success' => false, 'message' => $ex->getMessage()]);
                } else {
                    throw new HttpException(\Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR, $ex->getMessage(), $ex);
                }
            }

            /** @var CDA002 $entity */
            $entity = $em->getRepository('PortadoresBundle:CDA002')->find($id);

            $entity->setConsumo($datos[$i]->consumo);
            $entity->setNivelActividad($datos[$i]->nivel_activ);
            $entity->setIndice($datos[$i]->indice);

            $entity->setRelacionRealPlan($datos[$i]->relacion_real_plan);
            $entity->setRelacionAcumAprob($datos[$i]->relacion_acumulado_aprobado == "" ? 0 : $datos[$i]->relacion_acumulado_aprobado);

//Debug::dump()
            $entity->setIndiceAnual($datos[$i]->indice_anual);
            $em->persist($entity);
        }

        try {
            $em->flush();

            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Cambios al CDA002 Guardados Correctamente.'));
            return $response;
        } catch (\Exception $ex) {
            if ($ex instanceof HttpException) {
                return new JsonResponse(['success' => false, 'message' => $ex->getMessage()]);
            } else {
                throw new HttpException(\Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR, $ex->getMessage(), $ex);
            }
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public
    function exportAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->get('store'));
        $portador = $request->get('portador_nombre');
        $portador_um = $request->get('portador_um');
        $unidad = $request->get('unidad_nombre');
        $consumo_total = $request->get('consumo_total');
        $consumo_total_acum = $request->get('consumo_total_acum');
        $consumo_total_plan = $request->get('consumo_total_plan');


        $mes = $request->get('mes');
//        Debug::dump($mes);die;
//        $codigogae = $data[0]->codigo_gae;

//        Debug::dump($data);die;

        $_html = "
        
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\">
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
<title></title>
<style>
.bordes_tablas
{
	border-bottom:1px solid #999;
	border-left:1px solid #999;
	border-right:1px solid #999;
	border-top:1px solid #999;
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
}
</style>

</head>

<body>
<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
  <tr>
    <td colspan=\"14\" align=\"center\"><strong>MODELO CDA 002</strong></td>

  </tr>
  <tr>
    <td colspan=\"2\">MES: " . strtoupper($mes) . " </td>
    <td colspan=\"2\" align=\"center\">$portador</td>
    <td colspan=\"3\">ORGANISMO: INRH</td>
    <td colspan=\"2\">EMPRESA: " . $unidad . "</td>
    <td colspan=\"3\" align=\"center\">CDA -002</td>
    <td colspan=\"2\">&nbsp;</td>

  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan=\"3\">U.M. para el consumo: " . $portador_um . "</td>
    <td colspan=\"2\">CÓDIGO:</td>
    <td>&nbsp;</td>
    <td colspan=\"2\">CÓDIGO:</td>
    <td colspan=\"3\">PRODUCTO:" . $portador . "</td>
    <td colspan=\"2\" align=\"center\"><strong>RELACIÓN</strong></td>

  </tr>
  <tr>
    <td rowspan=\"2\" align=\"center\" class=\"bordes_tablas\">CÓDIGO MEP</td>
    <td rowspan=\"2\" align=\"center\"  class=\"bordes_arriba_derecho_abajo\">ACTIVIDAD</td>
    <td rowspan=\"2\" align=\"center\"  class=\"bordes_arriba_derecho_abajo\">U.M NIVEL ACTIVIDAD</td>
    <td colspan=\"3\" align=\"center\"  class=\"bordes_arriba_derecho_abajo\"><strong>ACUMULADO DEL AÑO</strong></td>
    <td colspan=\"3\" align=\"center\"  class=\"bordes_arriba_derecho_abajo\"><strong>PLAN DE " . strtoupper($mes) . "</strong></td>
    <td colspan=\"3\" align=\"center\"  class=\"bordes_arriba_derecho_abajo\"><strong>REAL DE " . strtoupper($mes) . "</strong></td>
    <td rowspan=\"2\" align=\"center\"  class=\"bordes_arriba_derecho_abajo\">REAL vs PLAN(MENSUAL)</td>
    <td rowspan=\"2\" align=\"center\"  class=\"bordes_arriba_derecho_abajo\">ACUM. vs APROB. (ANUAL)</td>

  </tr>
  <tr>
    <td align=\"center\" class=\"bordes_derecho_abajo\">NIVEL ACTIVIDAD</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">CONSUMO</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">ÍNDICE</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">NIVEL ACTIVIDAD</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">CONSUMO</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">ÍNDICE</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">NIVEL ACTIVIDAD</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">CONSUMO</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">ÍNDICE</td>
  </tr>
  

        ";

        foreach ($data as $dat) {
            $_html .= "
        <tr>
    <td align='center' class=\"bordes_derecho_abajo_izquierda\">" . $dat->codigo . "</td>
    <td align='center' class=\"bordes_derecho_abajo_izquierda\">" . $dat->actividad_nombre . "</td>
    <td align='center' class=\"bordes_derecho_abajo_izquierda\">" . $dat->um_actividad_nombre . "</td>
    <td align='center' class=\"bordes_derecho_abajo_izquierda\">" . $dat->nivel_activ_acum . "</td>
    <td align='center' class=\"bordes_derecho_abajo_izquierda\">" . $dat->consumo_acum . "</td>
    <td align='center' class=\"bordes_derecho_abajo_izquierda\">" . round($dat->indice_acum, 4) . "</td>
    <td align='center' class=\"bordes_derecho_abajo_izquierda\">" . $dat->nivel_actividad_plan . "</td>
    <td align='center' class=\"bordes_derecho_abajo_izquierda\">" . $dat->consumo_plan . "</td>
    <td align='center' class=\"bordes_derecho_abajo_izquierda\">" . $dat->indice_plan . "</td>
    <td align='center' class=\"bordes_derecho_abajo_izquierda\">" . $dat->nivel_activ . "</td>
    <td align='center' class=\"bordes_derecho_abajo_izquierda\">" . $dat->consumo . "</td>
    <td align='center' class=\"bordes_derecho_abajo_izquierda\">" . $dat->indice . "</td>
    <td align='center' class=\"bordes_derecho_abajo_izquierda\">" . $dat->indice . "</td>
    <td align='center' class=\"bordes_derecho_abajo_izquierda\">" . $dat->indice . "</td>
    <td align='center' class=\"bordes_derecho_abajo_izquierda\">" . $dat->relacion_real_plan . "</td>
    <td align='center' class=\"bordes_derecho_abajo_izquierda\">" . $dat->relacion_acumulado_aprobado . "</td>

  </tr>
  
  
        ";
        }


        $_html .= "<tr>
    <td bgcolor=\"#CCFFFF\" class=\"bordes_derecho_abajo_izquierda\">&nbsp;</td>
    <td align='center' class=\"bordes_derecho_abajo_izquierda\">TOTAL DE " . $portador . "</td>
    <td bgcolor=\"#CCFFFF\" class=\"bordes_derecho_abajo_izquierda\">&nbsp;</td>
    <td bgcolor=\"#CCFFFF\" class=\"bordes_derecho_abajo_izquierda\">&nbsp;</td>
    <td align='center' class=\"bordes_derecho_abajo_izquierda\">" . $consumo_total_acum . "</td>
    <td bgcolor=\"#CCFFFF\" class=\"bordes_derecho_abajo_izquierda\">&nbsp;</td>
    <td bgcolor=\"#CCFFFF\" class=\"bordes_derecho_abajo_izquierda\">&nbsp;</td>
    <td align='center' class=\"bordes_derecho_abajo_izquierda\">" . $consumo_total_plan . "</td>
    <td bgcolor=\"#CCFFFF\" class=\"bordes_derecho_abajo_izquierda\">&nbsp;</td>
    <td bgcolor=\"#CCFFFF\" class=\"bordes_derecho_abajo_izquierda\">&nbsp;</td>
    <td align='center' class=\"bordes_derecho_abajo_izquierda\">" . $consumo_total . "</td>
    <td bgcolor=\"#CCFFFF\" class=\"bordes_derecho_abajo_izquierda\">&nbsp;</td>
    <td bgcolor=\"#CCFFFF\" class=\"bordes_derecho_abajo_izquierda\">&nbsp;</td>
    <td bgcolor=\"#CCFFFF\" class=\"bordes_derecho_abajo_izquierda\">&nbsp;</td>

  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>

  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>

  </tr>";
        $pieFirma = $this->get('portadores.piefirma')->getPieFirmaDistribucion(DocumentosEnum::distribucionCombustible, $unidad);

        $_html .= "<tr>
                <td colspan='13' style='text-align: left;'>$pieFirma</td>
            </tr>";
        $_html .= "
    <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>

  </tr>
</table>
</body>
</html>";
        return new JsonResponse((array('success' => true, 'html' => $_html)));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public
    function printAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $datos = json_decode($request->get('store'));
        $summary = json_decode($request->get('summary'));
        $portador = $request->get('portador_nombre');
        $portador_um = $request->get('portador_um');
        $unidad = strtoupper($request->get('unidad_nombre'));
        $unidad_id = $request->get('unidad_id');
        $consumo_total = $request->get('consumo_total');
        $consumo_total_acum = $request->get('consumo_total_acum');
        $consumo_total_plan = $request->get('consumo_total_plan');

        $piefirma = $em->getRepository('PortadoresBundle:PieFirma')->findOneBy(array(
            'documento' => DocumentosEnum::modeloCDA002,
            'nunidadid' => $unidad_id
        ));

        $mes = strtoupper($request->get('mes'));
        $anno = $request->get('anno');
//        Debug::dump($mes);die;
//        $codigogae = $data[0]->codigo_gae;

//        Debug::dump($data);die;

        $_html = "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\"
xmlns:x=\"urn:schemas-microsoft-com:office:excel\"
xmlns=\"http://www.w3.org/TR/REC-html40\">

<head>
<meta http-equiv=Content-Type content=\"text/html; charset=utf-8\">
<meta name=ProgId content=Excel.Sheet>
<meta name=Generator content=\"Microsoft Excel 15\">
<link rel=File-List href=\"CDA002_files/filelist.xml\">
<style id=\"download_29688_Styles\">
<!--table
	{mso-displayed-decimal-separator:\"\.\";
	mso-displayed-thousand-separator:\"\,\";
	
	}
.xl1529688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:11.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl6329688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:11.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl6429688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:14.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl6529688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl6629688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl6729688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl6829688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.000\";
	text-align:general;
	vertical-align:bottom;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl6929688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.00000\";
	text-align:general;
	vertical-align:bottom;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl7029688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:right;
	vertical-align:bottom;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl7129688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:underline;
	text-underline-style:single;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl7229688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:red;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border:1.0pt solid windowtext;
	background:#CCFFFF;
	mso-pattern:black none;
	white-space:normal;}
.xl7329688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.000\";
	text-align:general;
	vertical-align:bottom;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl7429688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl7529688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.000\";
	text-align:center;
	vertical-align:bottom;
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl7629688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:0;
	text-align:general;
	vertical-align:bottom;
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl7729688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:blue;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl7829688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl7929688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.00000\";
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:none;
	background:#CCFFCC;
	mso-pattern:black none;
	white-space:nowrap;}
.xl8029688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.000\";
	text-align:center;
	vertical-align:middle;
	background:#CCFFCC;
	mso-pattern:black none;
	white-space:normal;}
.xl8129688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:blue;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl8229688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.000\";
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl82296881
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.00\";
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl8329688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:white;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.000\";
	text-align:center;
	vertical-align:bottom;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl8429688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:red;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Cambria, serif;
	mso-font-charset:0;
	mso-number-format:\"0\.000\";
	text-align:center;
	vertical-align:bottom;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl8529688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:blue;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:bottom;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl8629688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl8729688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:0;
	text-align:center;
	vertical-align:bottom;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl8829688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.000\";
	text-align:center;
	vertical-align:bottom;
	border-top:none;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	background:yellow;
	mso-pattern:black none;
	white-space:nowrap;}
.xl8929688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.000\";
	text-align:center;
	vertical-align:bottom;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl9029688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.000\";
	text-align:center;
	vertical-align:bottom;
	border-top:none;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl9129688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:14.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl9229688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:middle;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl9329688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.000\";
	text-align:general;
	vertical-align:middle;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl9429688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:underline;
	text-underline-style:single;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl9529688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:red;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:normal;}
.xl9629688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:red;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid black;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:normal;}
.xl9729688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl9829688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:none;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid black;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl9929688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:white;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl10029688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:white;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl10129688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:white;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid black;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl10229688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:none;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl10329688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:right;
	vertical-align:bottom;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl10429688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl10529688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:none;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl10629688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	background:#CCFFCC;
	mso-pattern:black none;
	white-space:normal;}
.xl10729688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	background:#CCFFCC;
	mso-pattern:black none;
	white-space:nowrap;}
.xl10829688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:red;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	background:#CCFFFF;
	mso-pattern:black none;
	white-space:normal;}
.xl10929688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:red;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	background:#CCFFFF;
	mso-pattern:black none;
	white-space:normal;}
.xl11029688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:red;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid black;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	background:#CCFFFF;
	mso-pattern:black none;
	white-space:normal;}
.xl11129688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:red;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid black;
	background:#CCFFFF;
	mso-pattern:black none;
	white-space:normal;}
.xl11229688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:red;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	background:#CCFFFF;
	mso-pattern:black none;
	white-space:normal;}
.xl11329688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl11429688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:1.0pt solid black;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl11529688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:normal;}
.xl11629688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:normal;}
.xl11729688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:normal;}
.xl11829688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:middle;
	border-top:none;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:normal;}
.xl11929688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:middle;
	border-top:none;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:normal;}
.xl12029688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:middle;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:normal;}
.xl12129688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl12229688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl12329688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid black;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl12429688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:middle;
	border-top:none;
	border-right:none;
	border-bottom:1.0pt solid black;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl12529688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:middle;
	border-top:none;
	border-right:none;
	border-bottom:1.0pt solid black;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl12629688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:middle;
	border-top:none;
	border-right:1.0pt solid black;
	border-bottom:1.0pt solid black;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl12729688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl12829688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:middle;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid black;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl12929688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:middle;
	border-top:1.0pt solid black;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl13029688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:middle;
	border-top:1.0pt solid black;
	border-right:none;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl13129688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:middle;
	border-top:1.0pt solid black;
	border-right:1.0pt solid black;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl13229688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	background:#CCFFCC;
	mso-pattern:black none;
	white-space:normal;}
.xl13329688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.000\";
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:none;
	background:#CCFFCC;
	mso-pattern:black none;
	white-space:normal;}
.xl13429688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	mso-number-format:\"0\.000\";
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl13529688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	background:#CCFFCC;
	mso-pattern:black none;
	white-space:nowrap;}
.xl13629688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl13729688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.00000\";
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:.5pt solid windowtext;
	background:#CCFFCC;
	mso-pattern:black none;
	white-space:nowrap;}
.xl13829688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.000\";
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	background:#CCFFCC;
	mso-pattern:black none;
	white-space:normal;}
.xl13929688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:blue;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.00000\";
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl14029688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.000\";
	text-align:center;
	vertical-align:bottom;
	border:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl14129688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.00000\";
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:#CCFFCC;
	mso-pattern:black none;
	white-space:nowrap;}
.xl14229688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:blue;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.00000\";
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:nowrap;}
.xl14329688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:blue;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.00000\";
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl143296881
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:blue;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.00\";
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}		
.xl14429688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:white;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.00000\";
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl14529688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:white;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.00000\";
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl14629688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.000\";
	text-align:center;
	vertical-align:bottom;
	border:.5pt solid windowtext;
	background:yellow;
	mso-pattern:black none;
	white-space:nowrap;}
.xl14729688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:0;
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl14829688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.000\";
	text-align:center;
	vertical-align:bottom;
	border-top:none;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	background:#CCFFCC;
	mso-pattern:black none;
	white-space:nowrap;}
.xl14929688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:blue;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.000\";
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl15029688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:blue;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.00000\";
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:nowrap;}
.xl15129688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:0;
	text-align:center;
	vertical-align:bottom;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl15229688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:blue;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.000\";
	text-align:center;
	vertical-align:bottom;
	border-top:none;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl15329688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.000\";
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:yellow;
	mso-pattern:black none;
	white-space:nowrap;}
.xl15429688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:blue;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.00000\";
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	background:white;
	mso-pattern:black none;
	white-space:nowrap;}
.xl15529688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.000\";
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl15629688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:blue;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.00000\";
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:#CCFFCC;
	mso-pattern:black none;
	white-space:nowrap;}
.xl15729688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.000\";
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	background:#CCFFCC;
	mso-pattern:black none;
	white-space:nowrap;}
.xl15829688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:blue;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.00000\";
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	background:#CCFFCC;
	mso-pattern:black none;
	white-space:nowrap;}
.xl15929688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:blue;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.00000\";
	text-align:center;
	vertical-align:bottom;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:none;
	background:white;
	mso-pattern:black none;
	white-space:nowrap;}
.xl16029688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:blue;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.000\";
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl16129688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:red;
	font-size:14.0pt;
	font-weight:700;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid black;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl16229688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:blue;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.00000\";
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:#CCFFCC;
	mso-pattern:black none;
	white-space:nowrap;}
.xl16329688
	{padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:windowtext;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.000\";
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	background:#CCFFCC;
	mso-pattern:black none;
	white-space:nowrap;}
-->
</style>
</head>

<body>
<!--[if !excel]>&nbsp;&nbsp;<![endif]-->
<!--The following information was generated by Microsoft Excel's Publish as Web
Page wizard.-->
<!--If the same item is republished from Excel, all information between the DIV
tags will be replaced.-->
<!----------------------------->
<!--START OF OUTPUT FROM EXCEL PUBLISH AS WEB PAGE WIZARD -->
<!----------------------------->

<div id=\"download_29688\" align=center x:publishsource=\"Excel\"><!--The following information was generated by Microsoft Excel's Publish as Web
Page wizard.--><!--If the same item is republished from Excel, all information between the DIV
tags will be replaced.--><!-----------------------------><!--START OF OUTPUT FROM EXCEL PUBLISH AS WEB PAGE WIZARD --><!----------------------------->

<table border=0 cellpadding=0 cellspacing=0 width=1633 style='border-collapse:
 collapse;table-layout:fixed;width:1228pt'>
 <col width=325 style='mso-width-source:userset;mso-width-alt:11885;width:244pt'>
 <col width=100 style='mso-width-source:userset;mso-width-alt:3657;width:75pt'>
 <col width=118 style='mso-width-source:userset;mso-width-alt:4315;width:89pt'>
 <col width=109 style='mso-width-source:userset;mso-width-alt:3986;width:82pt'>
 <col width=100 style='mso-width-source:userset;mso-width-alt:3657;width:75pt'>
 <col width=118 style='mso-width-source:userset;mso-width-alt:4315;width:89pt'>
 <col width=109 style='mso-width-source:userset;mso-width-alt:3986;width:82pt'>
 <col width=100 style='mso-width-source:userset;mso-width-alt:3657;width:75pt'>
 <col width=118 style='mso-width-source:userset;mso-width-alt:4315;width:89pt'>
 <col width=109 style='mso-width-source:userset;mso-width-alt:3986;width:82pt'>
 <col width=100 style='mso-width-source:userset;mso-width-alt:3657;width:75pt'>
 <col width=118 style='mso-width-source:userset;mso-width-alt:4315;width:89pt'>
 <col width=109 style='mso-width-source:userset;mso-width-alt:3986;width:82pt'>
 <tr height=20 style='mso-height-source:userset;height:15.0pt'>
  <td height=20 class=xl6329688 width=329 style='height:15.0pt;width:247pt'></td>
  <td class=xl6329688 width=138 style='width:104pt'></td>
  <td class=xl6329688 width=120 style='width:90pt'></td>
  <td class=xl6329688 width=114 style='width:86pt'></td>
  <td class=xl6329688 width=120 style='width:90pt'></td>
  <td class=xl6329688 width=128 style='width:96pt'></td>
  <td class=xl6329688 width=119 style='width:89pt'></td>
  <td class=xl6329688 width=93 style='width:70pt'></td>
  <td class=xl6329688 width=137 style='width:103pt'></td>
  <td class=xl6329688 width=124 style='width:93pt'></td>
  <td class=xl6329688 width=98 style='width:74pt'></td>
  <td class=xl6329688 width=72 style='width:54pt'></td>
  <td class=xl6329688 width=90 style='width:68pt'></td>
 </tr>
 <tr height=24 style='mso-height-source:userset;height:18.0pt'>
  <td colspan=11 height=24 class=xl9429688 style='height:18.0pt'>MODELO DE
  CONTROL DEL CONSUMO ( ANALISIS EFICIENCIA)</td>
  <td class=xl6429688></td>
  <td class=xl6429688></td>
 </tr>
 <tr height=26 style='mso-height-source:userset;height:19.5pt'>
  <td height=26 class=xl6529688 style='height:19.5pt'></td>
  <td class=xl6729688></td>
  <td class=xl6829688></td>
  <td class=xl6829688></td>
  <td class=xl6929688></td>
  <td class=xl6829688></td>
  <td class=xl6829688></td>
  <td class=xl6929688></td>
  <td class=xl6829688></td>
  <td class=xl6829688></td>
  <td class=xl6929688></td>
  <td class=xl6429688></td>
  <td class=xl6429688></td>
 </tr>
 <tr height=26 style='mso-height-source:userset;height:19.5pt'>
  <td height=26 class=xl7029688 style='height:19.5pt'>CDA-002</td>
  <td class=xl7129688></td>
  <td class=xl7229688 width=120 style='width:90pt'>MES:</td>
  <td colspan=2 class=xl9529688 width=234 style='border-right:1.0pt solid black;
  border-left:none;width:176pt'>" . $mes . "</td>
  <td colspan=2 class=xl9829688 style='border-left:none'>&nbsp;</td>
  <td class=xl6929688></td>
  <td class=xl7329688></td>
  <td class=xl6829688></td>
  <td class=xl6929688></td>
  <td class=xl6429688></td>
  <td class=xl6429688></td>
 </tr>
 <tr height=26 style='mso-height-source:userset;height:19.5pt'>
  <td height=26 class=xl6529688 style='height:19.5pt'></td>
  <td class=xl6729688></td>
  <td class=xl6829688></td>
  <td class=xl6829688></td>
  <td class=xl6929688></td>
  <td class=xl6829688></td>
  <td class=xl6829688></td>
  <td class=xl6929688></td>
  <td class=xl6829688></td>
  <td class=xl6829688></td>
  <td class=xl6929688></td>
  <td class=xl6429688></td>
  <td class=xl6429688></td>
 </tr>
 <tr height=26 style='mso-height-source:userset;height:19.5pt'>
  <td height=26 class=xl7429688 style='height:19.5pt'>ENTIDAD:</td>
  <td colspan=4 class=xl7429688 style='border-right:1.0pt solid black;
  border-left:none'>" . $unidad . "</td>
  <td class=xl7529688>AÑO</td>
  <td class=xl7629688 align=right>" . $anno . "</td>
  <td colspan=2 class=xl10229688 style='border-left:none'>&nbsp;</td>
  <td colspan=2 class=xl7029688></td>
  <td class=xl6429688></td>
  <td class=xl6429688></td>
 </tr>
 <tr height=26 style='mso-height-source:userset;height:19.5pt'>
  <td height=26 class=xl7729688 style='height:19.5pt'>Unidad de Medida: " . $portador_um . "</td>
  <td colspan=2 class=xl10329688>PRODUCTO:</td>
  <td colspan=2 class=xl10429688>" . $portador . "</td>
  <td class=xl7829688></td>
  <td class=xl7829688></td>
  <td class=xl6629688></td>
  <td class=xl6629688></td>
  <td colspan=2 class=xl10529688>" . $portador . "</td>
  <td class=xl6429688></td>
  <td class=xl6429688></td>
 </tr>
 <tr height=57 style='mso-height-source:userset;height:42.75pt'>
  <td rowspan=2 height=103 class=xl10629688 width=329 style='height:77.25pt;
  width:247pt'>ACTIVIDAD:</td>
  <td rowspan=2 class=xl10729688 style='border-top:none'>UM</td>
  <td colspan=3 class=xl10929688 width=354 style='border-right:1.0pt solid black;
  border-left:none;width:266pt'>ACUMULADO</td>
  <td colspan=3 class=xl11129688 width=340 style='border-right:1.0pt solid black;
  border-left:none;width:255pt'>PLAN</td>
  <td colspan=3 class=xl10929688 width=359 style='border-right:1.0pt solid black;
  border-left:none;width:270pt'>REAL</td>
  <td colspan=2 class=xl10929688 width=359 style='border-right:1.0pt solid black;
  border-left:none;width:270pt'>REL. INDICES</td>
  <td class=xl6429688></td>
  <td class=xl6429688></td>
 </tr>
 <tr height=46 style='mso-height-source:userset;height:34.5pt'>
  <td height=46 class=xl13329688 width=120 style='height:34.5pt;width:90pt'>NIVEL
  DE ACTIVIDAD</td>
  <td class=xl13329688 width=114 style='width:86pt'>CONSUMO</td>
  <td class=xl13729688 style='border-top:none;border-left:none'>INDICE</td>
  <td class=xl13829688 width=128 style='border-top:none;width:96pt'>NIVEL DE
  ACTIVIDAD</td>
  <td class=xl13329688 width=119 style='width:89pt'>CONSUMO</td>
  <td class=xl7929688>INDICE</td>
  <td class=xl13329688 width=137 style='width:103pt'>NIVEL DE ACTIVIDAD</td>
  <td class=xl13329688 width=124 style='width:93pt'>CONSUMO</td>
  <td class=xl14129688 style='border-top:none;border-left:none'>INDICE</td>
  <td class=xl13329688 width=72 style='width:54pt'>PLAN vs ACUM</td>
  <td class=xl14129688 width=90 style='width:68pt'>PLAN vs<br> REAL</td>
  <td class=xl8029688 width=72 style='width:100pt'>DET</td>
  <td class=xl8029688 width=90 style='width:100pt'>BAJO NIVEL</td>
 </tr>";
        foreach ($datos as $dato) {
            $deterioro = $dato->indice > $dato->indice_plan ? number_format($dato->nivel_activ * ($dato->indice - $dato->indice_plan) * 1000, 3) : '';
            $bajo_nivel = ($dato->nivel_activ - $dato->nivel_actividad_plan) * 1000;
            $planvsacumulado = $dato->indice_acum == 0 ? 0 : $dato->indice_plan / $dato->indice_acum;
            $planvsreal = $dato->indice == 0 ? 0 : $dato->indice_plan / $dato->indice;
            $_html .= "<tr height=26 style='mso-height-source:userset;height:20.1pt'>
  <td height=26 class=xl8129688 style='height:20.1pt'>" . $dato->actividad_nombre . "</td>
  <td class=xl13629688 style='border-left:none'>" . $dato->um_actividad_nombre . "</td>
  <td class=xl13429688 style='border-left:none'>" . number_format($dato->nivel_activ_acum, 3, '.', ',') . "</td>
  <td class=xl14029688 style='border-left:none'>" . number_format($dato->consumo_acum, 3, '.', ',') . "</td>
  <td class=xl13929688>" . number_format($dato->indice_acum, 4, '.', ',') . "</td>
  <td class=xl13429688 style='border-top:none;border-left:none'>" . number_format($dato->nivel_actividad_plan, 3, '.', ',') . "</td>
  <td class=xl15529688 style='border-left:none'>" . number_format($dato->consumo_plan, 3, '.', ',') . "</td>
  <td class=xl14329688>" . number_format($dato->indice_plan, 4, '.', ',') . "</td>
  <td class=xl13429688 style='border-left:none'>" . number_format($dato->nivel_activ, 3, '.', ',') . "</td>
  <td class=xl8229688>" . number_format($dato->consumo, 3, '.', ',') . "</td>
  <td class=xl14329688 style='border-top:none'>" . number_format($dato->indice, 4, '.', ',') . "</td>
  <td class=xl82296881>" . number_format($planvsacumulado, 4, '.', ',') . "</td>
  <td class=xl143296881 style='color:black'>" . number_format($planvsreal, 4, '.', ',') . "</td>
  <td class=xl8429688>" . $deterioro . "</td>
  <td class=xl8429688>" . $bajo_nivel . "</td>
 </tr>";
        }

        $_html .= "
 <tr height=26 style='mso-height-source:userset;height:20.1pt'>
  <td colspan=2 height=26 class=xl11329688 style='border-right:1.0pt solid black;
  height:20.1pt'>TOTAL</td>
  <td class=xl14829688>&nbsp;</td>
  <td class=xl14929688 style='border-top:none'>" . number_format($summary[0]->consumo_acum, 3) . "</td>
  <td class=xl16229688 style='border-left:none'>&nbsp;</td>
  <td class=xl16329688>&nbsp;</td>
  <td class=xl15229688 style='border-left:none'>" . number_format($summary[0]->consumo_plan, 3) . "</td>
  <td class=xl15629688>&nbsp;</td>
  <td class=xl15729688 style='border-left:none'>&nbsp;</td>
  <td class=xl16029688 style='border-top:none'>" . number_format($summary[0]->consumo, 3) . "</td>
  <td class=xl15829688>&nbsp;</td>
  <td class=xl6429688></td>
  <td class=xl6429688></td>
 </tr>
 <tr height=26 style='mso-height-source:userset;height:19.5pt'>
  <td height=26 class=xl9129688 style='height:19.5pt'></td>
  <td colspan=4 rowspan=2 class=xl11529688 width=492 style='border-right:1.0pt solid black;
  border-bottom:1.0pt solid black;width:370pt'>Elaborado por: " . ($piefirma ? $piefirma->getConfecciona()->getNombre() : '_____________________________') . "</td>
  <td colspan=4 rowspan=2 class=xl12129688 style='border-right:1.0pt solid black;
  border-bottom:1.0pt solid black'>Aprob: " . ($piefirma ? $piefirma->getAutoriza()->getNombre() : '______________________________________') . "</td>
  <td class=xl16129688 style='border-left:none'>&nbsp;</td>
  <td class=xl9229688></td>
  <td class=xl6429688></td>
  <td class=xl6429688></td>
 </tr>
 <tr height=22 style='mso-height-source:userset;height:16.5pt'>
  <td height=22 class=xl9129688 style='height:16.5pt'></td>
  <td class=xl9229688></td>
  <td class=xl9229688></td>
  <td class=xl6429688></td>
  <td class=xl6429688></td>
 </tr>
 <tr height=24 style='mso-height-source:userset;height:18.0pt'>
  <td height=24 class=xl9129688 style='height:18.0pt'></td>
  <td colspan=4 rowspan=2 class=xl12129688 style='border-right:1.0pt solid black;
  border-bottom:1.0pt solid black'>Cargo: " . ($piefirma ? $piefirma->getConfecciona()->getCargoid()->getNombre() : '____________________________________') . "</td>
  <td colspan=4 rowspan=2 class=xl12929688 style='border-right:1.0pt solid black;
  border-bottom:1.0pt solid black'>Cargo: " . ($piefirma ? $piefirma->getAutoriza()->getCargoid()->getNombre() : '______________________________________') . "</td>
  <td class=xl9329688></td>
  <td class=xl9229688></td>
  <td class=xl6429688></td>
  <td class=xl6429688></td>
 </tr>
 <tr height=27 style='mso-height-source:userset;height:20.25pt'>
  <td height=27 class=xl9129688 style='height:20.25pt'></td>
  <td class=xl9329688></td>
  <td class=xl9229688></td>
  <td class=xl6429688></td>
  <td class=xl6429688><!-----------------------------><!--END OF OUTPUT FROM EXCEL PUBLISH AS WEB PAGE WIZARD--><!-----------------------------></td>
 </tr>
 <![if supportMisalignedColumns]>
 <tr height=0 style='display:none'>
  <td width=329 style='width:247pt'></td>
  <td width=138 style='width:104pt'></td>
  <td width=120 style='width:90pt'></td>
  <td width=114 style='width:86pt'></td>
  <td width=120 style='width:90pt'></td>
  <td width=128 style='width:96pt'></td>
  <td width=119 style='width:89pt'></td>
  <td width=93 style='width:70pt'></td>
  <td width=137 style='width:103pt'></td>
  <td width=124 style='width:93pt'></td>
  <td width=98 style='width:74pt'></td>
  <td width=72 style='width:54pt'></td>
  <td width=90 style='width:68pt'></td>
 </tr>
 <![endif]>
</table>

</div>


<!----------------------------->
<!--END OF OUTPUT FROM EXCEL PUBLISH AS WEB PAGE WIZARD-->
<!----------------------------->
</body>

</html>


";
        return new JsonResponse((array('success' => true, 'html' => $_html)));
    }

    /**
     * @param $Nromes
     * @return string
     */
    public
    function damemesAction($Nromes)
    {
        if ($Nromes == 1) {
            $nombremes = 'Enero';

        } elseif ($Nromes == 2) {
            $nombremes = 'Febrero';

        } elseif ($Nromes == 3) {
            $nombremes = 'Marzo';


        } elseif ($Nromes == 4) {
            $nombremes = 'Abril';


        } elseif ($Nromes == 5) {
            $nombremes = 'Mayo';

        } elseif ($Nromes == 6) {
            $nombremes = 'Junio';


        } elseif ($Nromes == 7) {
            $nombremes = 'Julio';


        } elseif ($Nromes == 8) {
            $nombremes = 'Agosto';


        } elseif ($Nromes == 9) {
            $nombremes = 'Septiembre';


        } elseif ($Nromes == 10) {
            $nombremes = 'Octubre';


        } elseif ($Nromes == 11) {

            $nombremes = 'Noviembre';


        } elseif ($Nromes == 12) {
            $nombremes = 'Diciembre';


        }

        return $nombremes;
    }

    private
    function unidadesToString($_unidades)
    {
        $_string_unidades = "'" . $_unidades[0] . "'";
        for ($i = 1; $i < count($_unidades); $i++) {
            $_string_unidades .= ",'" . $_unidades[$i] . "'";
        }
        return $_string_unidades;
    }
}