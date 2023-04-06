<?php
/**
 * Created by PhpStorm.
 * User: asisoftware11
 * Date: 10/3/2018
 * Time: 9:07 AM
 */

namespace Geocuba\PortadoresBundle\Controller;


use ClassesWithParents\D;
use Geocuba\AdminBundle\Util\Util;
use Geocuba\PortadoresBundle\Entity\CDA001;
use Geocuba\PortadoresBundle\Entity\Actividad;
use Doctrine\Common\Util\Debug;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;
use Geocuba\PortadoresBundle\Util\DocumentosEnum;


class CDA001Controller extends Controller
{
    use ViewActionTrait;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $portadorid = $request->get('portadorid');
        $portadorName = $request->get('portadorName');
        $unidadid = $request->get('unidadid');
        $anno = $request->get('anno');
        $monedaStr = $request->get('moneda');

        $_data = array();
        if ($portadorName !== "ELECTRICIDAD") {
            $cda001 = $em->getRepository('PortadoresBundle:CDA001')->buscarCda001($portadorid, $unidadid, $anno, $monedaStr);
        } else {
            $cda001 = $em->getRepository('PortadoresBundle:CDA001')->buscarCda001($portadorid, $unidadid, $anno, '');
        }


        if ($cda001) {
            /** @var CDA001 $cda */
            foreach ($cda001 as $cda) {
                $_data [] = array(
                    'id' => $cda->getId(),
                    'anno_real' => $cda->getAnno() - 2,
                    'anno_acumulado' => $cda->getAnno() - 1,
                    'anno_propuesta' => $cda->getAnno(),
                    'actividadid' => $cda->getActividad()->getId(),
                    'actividad_nombre' => $cda->getActividad()->getNombre(),
                    'actividad_um' => $cda->getActividad()->getUmActividad()->getNivelActividad(),
                    'codigo_mep_act' => $cda->getActividad()->getCodigomep(),
                    'codigo_gae_act' => $cda->getActividad()->getCodigogae(),
                    'unidadid' => $cda->getNunidadid()->getId(),
                    'unidad_nombre' => $cda->getNunidadid()->getNombre(),
                    'portadorid' => $cda->getPortadorid()->getId(),
                    'monedaid' => ($portadorName !== "ELECTRICIDAD") ? $cda->getMoneda()->getId() : '',
                    'moneda' => ($portadorName !== "ELECTRICIDAD") ? $cda->getMoneda()->getNombre() : '',
                    'portador_nombre' => $cda->getPortadorid()->getNombre(),
                    'real_nivel_act' => $cda->getRealNivelAct(),
                    'real_consumo' => $cda->getRealConsumo(),
                    'real_indice' => $cda->getRealIndice(),
                    'acumulado_nivel_act' => $cda->getAcumuladoNivelAct(),
                    'acumulado_consumo' => $cda->getAcumuladoConsumo(),
                    'acumulado_indice' => $cda->getAcumuladoIndice(),
                    'estimado_nivel_act' => $cda->getEstimadoNivelAct(),
                    'estimado_consumo' => $cda->getEstimadoConsumo(),
                    'estimado_indice' => $cda->getEstimadoIndice(),
                    'propuesta_plan_nivel_act' => $cda->getPropuestaNivelAct(),
                    'propuesta_plan_consumo' => $cda->getPropuestaConsumo(),
                    'propuesta_plan_indice' => $cda->getPropuestaIndice(),
                    'plan_final_nivel_act' => $cda->getPlanFinalNivelAct(),
                    'plan_final_consumo' => $cda->getPlanFinalConsumo(),
                    'plan_final_indice' => $cda->getPlanFinalIndice(),
                    'total_desglose_nivel_act' => $cda->getTotalDesgloseNivelAct(),
                    'total_desglose_consumo' => $cda->getTotalDesgloseConsumo(),
                    'total_desglose_indice' => $cda->getTotalDesgloseIndice(),
                    'enero_nivel_act' => $cda->getEneroNivelAct(),
                    'enero_consumo' => $cda->getEneroConsumo(),
                    'enero_indice' => $cda->getEneroIndice(),
                    'febrero_nivel_act' => $cda->getFebreroNivelAct(),
                    'febrero_consumo' => $cda->getFebreroConsumo(),
                    'febrero_indice' => $cda->getFebreroIndice(),
                    'marzo_nivel_act' => $cda->getMarzoNivelAct(),
                    'marzo_consumo' => $cda->getMarzoConsumo(),
                    'marzo_indice' => $cda->getMarzoIndice(),
                    'abril_nivel_act' => $cda->getAbrilNivelAct(),
                    'abril_consumo' => $cda->getAbrilConsumo(),
                    'abril_indice' => $cda->getAbrilIndice(),
                    'mayo_nivel_act' => $cda->getMayoNivelAct(),
                    'mayo_consumo' => $cda->getMayoConsumo(),
                    'mayo_indice' => $cda->getMayoIndice(),
                    'junio_nivel_act' => $cda->getJunioNivelAct(),
                    'junio_consumo' => $cda->getJunioConsumo(),
                    'junio_indice' => $cda->getJunioIndice(),
                    'julio_nivel_act' => $cda->getJulioNivelAct(),
                    'julio_consumo' => $cda->getJulioConsumo(),
                    'julio_indice' => $cda->getJulioIndice(),
                    'agosto_nivel_act' => $cda->getAgostoNivelAct(),
                    'agosto_consumo' => $cda->getAgostoConsumo(),
                    'agosto_indice' => $cda->getAgostoIndice(),
                    'septiembre_nivel_act' => $cda->getSeptiembreNivelAct(),
                    'septiembre_consumo' => $cda->getSeptiembreConsumo(),
                    'septiembre_indice' => $cda->getSeptiembreIndice(),
                    'octubre_nivel_act' => $cda->getOctubreNivelAct(),
                    'octubre_consumo' => $cda->getOctubreConsumo(),
                    'octubre_indice' => $cda->getOctubreIndice(),
                    'noviembre_nivel_act' => $cda->getNoviembreNivelAct(),
                    'noviembre_consumo' => $cda->getNoviembreConsumo(),
                    'noviembre_indice' => $cda->getNoviembreIndice(),
                    'diciembre_nivel_act' => $cda->getDiciembreNivelAct(),
                    'diciembre_consumo' => $cda->getDiciembreConsumo(),
                    'diciembre_indice' => $cda->getDiciembreIndice(),
                );
            }
        }

        return new JsonResponse(array('rows' => $_data));
    }

    /**
     * @Route("/ncda001/generarCDA001", name="generarCDA001",options={"expose"=true} )
     * @param Request $request
     * @return JsonResponse
     */
    public function generarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $portadorid = $request->get('portadorid');
        $portadorName = $request->get('portadorName');
        $unidadid = $request->get('unidadid');
        $anno = $request->get('anno');
        $monedaStr = $request->get('moneda');

        /*BUSCO LOS CDA EXISTENTES, DADO EL PORTADOR,LA MONEDA, EL AÑO Y LA UNIDAD*/
        if ($portadorName !== "ELECTRICIDAD") {
            $cda001_existente = $em->getRepository('PortadoresBundle:CDA001')->findBy(array('anno' => $anno, 'portadorid' => $portadorid, 'nunidadid' => $unidadid, 'moneda' => $monedaStr));
        } else {
            $cda001_existente = $em->getRepository('PortadoresBundle:CDA001')->findBy(array('anno' => $anno, 'portadorid' => $portadorid, 'nunidadid' => $unidadid));
        }
        $moneda = $em->getRepository('PortadoresBundle:Moneda')->findOneBy(array('id' => $monedaStr));


        /*ELIMINO CADA UNO DE LOS CDA EXISTENTE PARA GENERALOS DE NUEVO*/
        foreach ($cda001_existente as $cda) {
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
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($unidadid), $_unidades);
        $unidades_string = $this->unidadesToString($_unidades);

        $portador = $em->getRepository('PortadoresBundle:Portador')->find($portadorid);
        /* VERIFICO QUE EL PORTADOR SEA DE GLP*/
        if ($portador->getNombre() == 'GLP') {
            $actividades = $em->getRepository('PortadoresBundle:Actividad')->findBy(array('portadorid' => $portador, 'visible' => true));
            /*COMPRUEBO QUE LA UNIDAD TENGAS ACTIVIDADES FILTRADAS*/
            if ($actividades) {
                /*POR CADA UNIDAD SE GUARDA EN LA BASE DE DATOS LA ACTIVIDAD CON VALORES 0, PUESTO QUE NO se planifica GLP*/
                foreach ($actividades as $actividad) {
                    $entity_cda001 = new CDA001();

                    $entity_cda001->setActividad($em->getRepository('PortadoresBundle:Actividad')->find($actividad['idactividad']));
                    $entity_cda001->setNunidadid($em->getRepository('PortadoresBundle:Unidad')->find($unidadid));
                    $entity_cda001->setPortadorid($em->getRepository('PortadoresBundle:Portador')->find($portadorid));
                    $entity_cda001->setAnno($anno);

                    $entity_cda001->setRealNivelAct(0.00);
                    $entity_cda001->setRealConsumo(0.00);
                    $entity_cda001->setAcumuladoNivelAct(0.00);
                    $entity_cda001->setAcumuladoConsumo(0.00);
                    $entity_cda001->setEstimadoNivelAct(0.00);
                    $entity_cda001->setEstimadoConsumo(0.00);
                    $entity_cda001->setPropuestaNivelAct(0.00);
                    $entity_cda001->setPropuestaConsumo(0.00);
                    $entity_cda001->setPlanFinalNivelAct(0.00);
                    $entity_cda001->setPlanFinalConsumo(0.00);
                    $entity_cda001->setPlanFinalNivelAct(0.00);
                    $entity_cda001->setPlanFinalConsumo(0.00);

                    $entity_cda001->setEneroNivelAct(0.00);
                    $entity_cda001->setEneroConsumo(0.00);

                    $entity_cda001->setFebreroNivelAct(0.00);
                    $entity_cda001->setFebreroConsumo(0.00);

                    $entity_cda001->setMarzoNivelAct(0.00);
                    $entity_cda001->setMarzoConsumo(0.00);

                    $entity_cda001->setAbrilNivelAct(0.00);
                    $entity_cda001->setAbrilConsumo(0.00);

                    $entity_cda001->setMayoNivelAct(0.00);
                    $entity_cda001->setMayoConsumo(0.00);

                    $entity_cda001->setJunioNivelAct(0.00);
                    $entity_cda001->setJunioConsumo(0.00);

                    $entity_cda001->setJulioNivelAct(0.00);
                    $entity_cda001->setJulioConsumo(0.00);

                    $entity_cda001->setAgostoNivelAct(0.00);
                    $entity_cda001->setAgostoConsumo(0.00);

                    $entity_cda001->setSeptiembreNivelAct(0.00);
                    $entity_cda001->setSeptiembreConsumo(0.00);

                    $entity_cda001->setOctubreNivelAct(0.00);
                    $entity_cda001->setOctubreConsumo(0.00);

                    $entity_cda001->setNoviembreNivelAct(0.00);
                    $entity_cda001->setNoviembreConsumo(0.00);

                    $entity_cda001->setDiciembreNivelAct(0.00);
                    $entity_cda001->setDiciembreConsumo(0.00);

                    try {
                        $em->persist($entity_cda001);
                        $em->flush();

                    } catch (\Exception $ex) {
                        if ($ex instanceof HttpException) {
                            return new JsonResponse(['success' => false, 'message' => $ex->getMessage()]);
                        } else {
                            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $ex->getMessage(), $ex);
                        }
                    }
                }
            }

        }
        if ($portador->getNombre() == 'ELECTRICIDAD') {
            $sql = "select
                        a.id as actividad_id,
                        max(a.nombre) as actividad_nombre,
                        sum(ds.plan_total) as plan_total,
                        sum(tb.horas) as nivel_act
                    
                    from datos.desglose_servicios as ds
                    inner join nomencladores.servicio as s on s.id = ds.idservicio
                    inner join nomencladores.actividad as a on a.id = s.nactividadid
                    inner join nomencladores.turnotrabajo as tb on tb.id = s.turnos_trabajo
                    where ds.anno = '$anno' and s.nunidadid = '$unidadid' group by a.id ";
            $cda001 = $this->getDoctrine()->getConnection()->fetchAll($sql);

            if (\count($cda001) >= 1) {
                for ($i = 0, $iMax = \count($cda001); $i < $iMax; $i++) {
                    $actividad = $em->getRepository('PortadoresBundle:Actividad')->find($cda001[$i]['actividad_id']);
                    $entity_cda001 = new CDA001();

                    $entity_cda001->setActividad($actividad);
                    $entity_cda001->setNunidadid($em->getRepository('PortadoresBundle:Unidad')->find($unidadid));
                    $entity_cda001->setPortadorid($em->getRepository('PortadoresBundle:Portador')->find($portadorid));
                    $entity_cda001->setAnno((int)$anno);

                    $entity_cda001->setRealNivelAct(0.00);
                    $entity_cda001->setRealConsumo(0.00);
                    $entity_cda001->setAcumuladoNivelAct(0.00);
                    $entity_cda001->setAcumuladoConsumo(0.00);
                    $entity_cda001->setEstimadoNivelAct(0.00);
                    $entity_cda001->setEstimadoConsumo(0.00);

                    $entity_cda001->setPropuestaNivelAct(0.00);
                    $entity_cda001->setPropuestaConsumo(0.00);
                    $entity_cda001->setPropuestaIndice(0.00);

                    $entity_cda001->setPlanFinalNivelAct((float)$cda001[$i]['nivel_act']);
                    $entity_cda001->setPlanFinalConsumo((float)$cda001[$i]['plan_total']);
                    $entity_cda001->setPlanFinalIndice($cda001[$i]['nivel_act'] != 0 ? $cda001[$i]['plan_total'] / $cda001[$i]['nivel_act'] : 0.00);

                    $sqlMeses = "select
                                    max(a.nombre) as actividad_nombre,
                                    sum(ds.plan_total) as plan_total,
                                    sum(tb.horas) as nivel_act,
                                    ds.mes
                                
                                from datos.desglose_servicios as ds
                                join nomencladores.servicio as s on s.id = ds.idservicio
                                join nomencladores.actividad as a on a.id = s.nactividadid
                                join nomencladores.turnotrabajo as tb on tb.id = s.turnos_trabajo
                                where ds.anno = '$anno' and a.id = '" . $cda001[$i]['actividad_id'] . "' group by ds.mes order by ds.mes";
                    $cda001Meses1 = $this->getDoctrine()->getConnection()->fetchAll($sqlMeses);

                    $cda001Meses = [];
                    if (count($cda001Meses1) === 12) $cda001Meses = $cda001Meses1;
                    else {
                        for ($j = 1; $j <= 12; $j++) {
                            $found = array_filter($cda001Meses1, function ($c) use ($j) {
                                return $c['mes'] == $j;
                            });
                            if (!$found) $cda001Meses[] = [
                                'nivel_act' => 0,
                                'plan_total' => 0
                            ];
                            else $cda001Meses[] = $found[array_keys($found)[0]];
                        }
                    }

                    $entity_cda001->setEneroNivelAct(isset($cda001Meses[0]) ? (float)$cda001Meses[0]['nivel_act'] : 0.00);
                    $entity_cda001->setEneroConsumo(isset($cda001Meses[0]) ? (float)$cda001Meses[0]['plan_total'] : 0.00);
                    $entity_cda001->setEneroIndice((isset($cda001Meses[0]) && $cda001Meses[0]['nivel_act'] == 0) ? 0.00 : $cda001Meses[0]['plan_total'] / $cda001Meses[0]['nivel_act']);

                    $entity_cda001->setFebreroNivelAct(isset($cda001Meses[1]) ? (float)$cda001Meses[1]['nivel_act'] : 0.00);
                    $entity_cda001->setFebreroConsumo(isset($cda001Meses[1]) ? (float)$cda001Meses[1]['plan_total'] : 0.00);
                    $entity_cda001->setFebreroIndice((!isset($cda001Meses[1]) || $cda001Meses[1]['nivel_act'] == 0) ? 0.00 : $cda001Meses[1]['plan_total'] / $cda001Meses[1]['nivel_act']);

                    $entity_cda001->setMarzoNivelAct(isset($cda001Meses[2]) ? (float)$cda001Meses[2]['nivel_act'] : 0.00);
                    $entity_cda001->setMarzoConsumo(isset($cda001Meses[2]) ? (float)$cda001Meses[2]['plan_total'] : 0.00);
                    $entity_cda001->setMarzoIndice((!isset($cda001Meses[2]) || $cda001Meses[2]['nivel_act'] == 0) ? 0.00 : $cda001Meses[2]['plan_total'] / $cda001Meses[2]['nivel_act']);

                    $entity_cda001->setAbrilNivelAct(isset($cda001Meses[3]) ? (float)$cda001Meses[3]['nivel_act'] : 0.00);
                    $entity_cda001->setAbrilConsumo(isset($cda001Meses[3]) ? (float)$cda001Meses[3]['plan_total'] : 0.00);
                    $entity_cda001->setAbrilIndice((!isset($cda001Meses[3]) || $cda001Meses[3]['nivel_act'] == 0) ? 0.00 : $cda001Meses[3]['plan_total'] / $cda001Meses[3]['nivel_act']);

                    $entity_cda001->setMayoNivelAct(isset($cda001Meses[4]) ? (float)$cda001Meses[4]['nivel_act'] : 0.00);
                    $entity_cda001->setMayoConsumo(isset($cda001Meses[4]) ? (float)$cda001Meses[4]['plan_total'] : 0.00);
                    $entity_cda001->setMayoIndice((!isset($cda001Meses[4]) || $cda001Meses[4]['nivel_act'] == 0) ? 0.00 : $cda001Meses[4]['plan_total'] / $cda001Meses[4]['nivel_act']);

                    $entity_cda001->setJunioNivelAct(isset($cda001Meses[5]) ? (float)$cda001Meses[5]['nivel_act'] : 0.00);
                    $entity_cda001->setJunioConsumo(isset($cda001Meses[5]) ? (float)$cda001Meses[5]['plan_total'] : 0.00);
                    $entity_cda001->setJunioIndice((!isset($cda001Meses[5]) || $cda001Meses[5]['nivel_act'] == 0) ? 0.00 : $cda001Meses[5]['plan_total'] / $cda001Meses[5]['nivel_act']);

                    $entity_cda001->setJulioNivelAct(isset($cda001Meses[6]) ? (float)$cda001Meses[6]['nivel_act'] : 0.00);
                    $entity_cda001->setJulioConsumo(isset($cda001Meses[6]) ? (float)$cda001Meses[6]['plan_total'] : 0.00);
                    $entity_cda001->setJulioIndice((!isset($cda001Meses[6]) || $cda001Meses[6]['nivel_act'] == 0) ? 0.00 : $cda001Meses[6]['plan_total'] / $cda001Meses[6]['nivel_act']);

                    $entity_cda001->setAgostoNivelAct(isset($cda001Meses[7]) ? (float)$cda001Meses[7]['nivel_act'] : 0.00);
                    $entity_cda001->setAgostoConsumo(isset($cda001Meses[7]) ? (float)$cda001Meses[7]['plan_total'] : 0.00);
                    $entity_cda001->setAgostoIndice((!isset($cda001Meses[7]) || $cda001Meses[7]['nivel_act'] == 0) ? 0.00 : $cda001Meses[7]['plan_total'] / $cda001Meses[7]['nivel_act']);

                    $entity_cda001->setSeptiembreNivelAct(isset($cda001Meses[8]) ? (float)$cda001Meses[8]['nivel_act'] : 0.00);
                    $entity_cda001->setSeptiembreConsumo(isset($cda001Meses[8]) ? (float)$cda001Meses[8]['plan_total'] : 0.00);
                    $entity_cda001->setSeptiembreIndice((!isset($cda001Meses[8]) || $cda001Meses[8]['nivel_act'] == 0) ? 0.00 : $cda001Meses[8]['plan_total'] / $cda001Meses[8]['nivel_act']);

                    $entity_cda001->setOctubreNivelAct(isset($cda001Meses[9]) ? (float)$cda001Meses[9]['nivel_act'] : 0.00);
                    $entity_cda001->setOctubreConsumo(isset($cda001Meses[9]) ? (float)$cda001Meses[9]['plan_total'] : 0.00);
                    $entity_cda001->setOctubreIndice((!isset($cda001Meses[9]) || $cda001Meses[9]['nivel_act'] == 0) ? 0.00 : $cda001Meses[9]['plan_total'] / $cda001Meses[9]['nivel_act']);

                    $entity_cda001->setNoviembreNivelAct(isset($cda001Meses[10]) ? (float)$cda001Meses[10]['nivel_act'] : 0.00);
                    $entity_cda001->setNoviembreConsumo(isset($cda001Meses[10]) ? (float)$cda001Meses[10]['plan_total'] : 0.00);
                    $entity_cda001->setNoviembreIndice((!isset($cda001Meses[10]) || $cda001Meses[10]['nivel_act'] == 0) ? 0.00 : $cda001Meses[10]['plan_total'] / $cda001Meses[10]['nivel_act']);

                    $entity_cda001->setDiciembreNivelAct(isset($cda001Meses[11]) ? (float)$cda001Meses[11]['nivel_act'] : 0.00);
                    $entity_cda001->setDiciembreConsumo(isset($cda001Meses[11]) ? (float)$cda001Meses[11]['plan_total'] : 0.00);
                    $entity_cda001->setDiciembreIndice((!isset($cda001Meses[11]) || $cda001Meses[11]['nivel_act'] == 0) ? 0.00 : $cda001Meses[11]['plan_total'] / $cda001Meses[11]['nivel_act']);

                    try {
                        $em->persist($entity_cda001);
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
        } else {

            /*SI EL PORTADOR NO ES GLP BUSCO LOS DATOS DESDE LA VISTA DE LA BD suma_planpe*/
            if ($moneda->getNombre() === 'MEP') {
                $sql = "SELECT
                        sum(suma_planpe.combustible_litros_total) AS combustible_litros_total,
                        sum(suma_planpe.combustible_litros_ene) AS combustible_litros_ene,
                        sum(suma_planpe.combustible_litros_feb) AS combustible_litros_feb,
                        sum(suma_planpe.combustible_litros_mar) AS combustible_litros_mar,
                        sum(suma_planpe.combustible_litros_abr) AS combustible_litros_abr,
                        sum(suma_planpe.combustible_litros_may) AS combustible_litros_may,
                        sum(suma_planpe.combustible_litros_jun) AS combustible_litros_jun,
                        sum(suma_planpe.combustible_litros_jul) AS combustible_litros_jul,
                        sum(suma_planpe.combustible_litros_ago) AS combustible_litros_ago,
                        sum(suma_planpe.combustible_litros_sep) AS combustible_litros_sep,
                        sum(suma_planpe.combustible_litros_oct) AS combustible_litros_oct,
                        sum(suma_planpe.combustible_litros_nov) AS combustible_litros_nov,
                        sum(suma_planpe.combustible_litros_dic) AS combustible_litros_dic,
                        sum(suma_planpe.nivel_act_kms_total) AS nivel_act_kms_total,
                        sum(suma_planpe.nivel_act_kms_ene) AS nivel_act_kms_ene,
                        sum(suma_planpe.nivel_act_kms_feb) AS nivel_act_kms_feb,
                        sum(suma_planpe.nivel_act_kms_mar) AS nivel_act_kms_mar,
                        sum(suma_planpe.nivel_act_kms_abr) AS nivel_act_kms_abr,
                        sum(suma_planpe.nivel_act_kms_may) AS nivel_act_kms_may,
                        sum(suma_planpe.nivel_act_kms_jun) AS nivel_act_kms_jun,
                        sum(suma_planpe.nivel_act_kms_jul) AS nivel_act_kms_jul,
                        sum(suma_planpe.nivel_act_kms_ago) AS nivel_act_kms_ago,
                        sum(suma_planpe.nivel_act_kms_sep) AS nivel_act_kms_sep,
                        sum(suma_planpe.nivel_act_kms_oct) AS nivel_act_kms_oct,
                        sum(suma_planpe.nivel_act_kms_nov) AS nivel_act_kms_nov,
                        sum(suma_planpe.nivel_act_kms_dic) AS nivel_act_kms_dic,
                        suma_planpe.actividad,
                        max(suma_planpe.portador) AS portador,
                        max(suma_planpe.nombre_actividad::text) AS nombre_actividad
                        
                    FROM datos.suma_planpe 
                    WHERE anno = $anno AND nunidadid in($unidades_string) AND portador='$portadorid'
                    GROUP BY suma_planpe.actividad
                    ORDER BY  suma_planpe.actividad";
            } else {
                $sql = "SELECT
                        sum(suma_planpe_cuc.combustible_litros_total) AS combustible_litros_total,
                        sum(suma_planpe_cuc.combustible_litros_ene) AS combustible_litros_ene,
                        sum(suma_planpe_cuc.combustible_litros_feb) AS combustible_litros_feb,
                        sum(suma_planpe_cuc.combustible_litros_mar) AS combustible_litros_mar,
                        sum(suma_planpe_cuc.combustible_litros_abr) AS combustible_litros_abr,
                        sum(suma_planpe_cuc.combustible_litros_may) AS combustible_litros_may,
                        sum(suma_planpe_cuc.combustible_litros_jun) AS combustible_litros_jun,
                        sum(suma_planpe_cuc.combustible_litros_jul) AS combustible_litros_jul,
                        sum(suma_planpe_cuc.combustible_litros_ago) AS combustible_litros_ago,
                        sum(suma_planpe_cuc.combustible_litros_sep) AS combustible_litros_sep,
                        sum(suma_planpe_cuc.combustible_litros_oct) AS combustible_litros_oct,
                        sum(suma_planpe_cuc.combustible_litros_nov) AS combustible_litros_nov,
                        sum(suma_planpe_cuc.combustible_litros_dic) AS combustible_litros_dic,
                        sum(suma_planpe_cuc.nivel_act_kms_total) AS nivel_act_kms_total,
                        sum(suma_planpe_cuc.nivel_act_kms_ene) AS nivel_act_kms_ene,
                        sum(suma_planpe_cuc.nivel_act_kms_feb) AS nivel_act_kms_feb,
                        sum(suma_planpe_cuc.nivel_act_kms_mar) AS nivel_act_kms_mar,
                        sum(suma_planpe_cuc.nivel_act_kms_abr) AS nivel_act_kms_abr,
                        sum(suma_planpe_cuc.nivel_act_kms_may) AS nivel_act_kms_may,
                        sum(suma_planpe_cuc.nivel_act_kms_jun) AS nivel_act_kms_jun,
                        sum(suma_planpe_cuc.nivel_act_kms_jul) AS nivel_act_kms_jul,
                        sum(suma_planpe_cuc.nivel_act_kms_ago) AS nivel_act_kms_ago,
                        sum(suma_planpe_cuc.nivel_act_kms_sep) AS nivel_act_kms_sep,
                        sum(suma_planpe_cuc.nivel_act_kms_oct) AS nivel_act_kms_oct,
                        sum(suma_planpe_cuc.nivel_act_kms_nov) AS nivel_act_kms_nov,
                        sum(suma_planpe_cuc.nivel_act_kms_dic) AS nivel_act_kms_dic,
                        suma_planpe_cuc.actividad,
                        max(suma_planpe_cuc.portador) AS portador,
                        max(suma_planpe_cuc.nombre_actividad::text) AS nombre_actividad
                        
                    FROM datos.suma_planpe_cuc 
                    WHERE anno = $anno AND nunidadid in($unidades_string) AND portador='$portadorid'
                    GROUP BY suma_planpe_cuc.actividad
                    ORDER BY  suma_planpe_cuc.actividad";
            }


            $cda001 = $this->getDoctrine()->getConnection()->fetchAll($sql);


            /*COMPROBAMOS QUE EXISTAN DATOS EN LA CONSULTA*/
            if (\count($cda001) >= 1) {
                for ($i = 0, $iMax = \count($cda001); $i < $iMax; $i++) {

                    $cda002Anterior = $em->getRepository('PortadoresBundle:CDA002')->findBy(['nactividadid' => $cda001[$i]['actividad'], 'anno' => $anno - 1, 'nunidadid' => $unidadid]);
                    $nivel_act_real_anterior = 0;
                    $consumo_comb_real_anterior = 0;
                    foreach ($cda002Anterior as $item) {
                        $nivel_act_real_anterior += $item->getNivelActividad();
                        $consumo_comb_real_anterior += $item->getConsumo();
                    }

                    /*BUSCAMOS LOS DATOS DE LA ACTIVIDAD DEL CDA*/
                    /** @var Actividad $actividad */
                    $actividad = $em->getRepository('PortadoresBundle:Actividad')->find($cda001[$i]['actividad']);

                    /*MULTIPLICAMOS LOS DATOS DE LAS VARIABLES POR 12 PUESTO QUE SON LA CANTIDAD DE MESES DEL AÑO*/
                    $nivel_act_acum = $cda001[$i]['nivel_act_kms_total'];
                    $consumo_acum = $cda001[$i]['combustible_litros_total'];
                    $indice_acum = $nivel_act_acum == 0 ? 0 : $consumo_acum / $nivel_act_acum;

                    $entity_cda001 = new CDA001();

                    if ($nivel_act_acum !== '0') {
                        $entity_cda001->setActividad($actividad);
                        $entity_cda001->setNunidadid($em->getRepository('PortadoresBundle:Unidad')->find($unidadid));
                        $entity_cda001->setPortadorid($em->getRepository('PortadoresBundle:Portador')->find($portadorid));
                        $entity_cda001->setMoneda($moneda);
                        $entity_cda001->setAnno($anno);

                        $entity_cda001->setRealNivelAct($nivel_act_real_anterior);
                        $entity_cda001->setRealConsumo($consumo_comb_real_anterior);
                        $entity_cda001->setAcumuladoNivelAct(0.00);
                        $entity_cda001->setAcumuladoConsumo(0.00);
                        $entity_cda001->setEstimadoNivelAct(0.00);
                        $entity_cda001->setEstimadoConsumo(0.00);

                        $entity_cda001->setPropuestaNivelAct($nivel_act_acum);
                        $entity_cda001->setPropuestaConsumo($consumo_acum);
                        $entity_cda001->setPropuestaIndice($indice_acum);

                        $entity_cda001->setPlanFinalNivelAct($nivel_act_acum);
                        $entity_cda001->setPlanFinalConsumo($consumo_acum);
                        $entity_cda001->setPlanFinalIndice($indice_acum);

                        $entity_cda001->setEneroNivelAct($cda001[$i]['nivel_act_kms_ene']);
                        $entity_cda001->setEneroConsumo($cda001[$i]['combustible_litros_ene']);
                        $entity_cda001->setEneroIndice(($cda001[$i]['nivel_act_kms_ene'] == 0 ? 0 : $cda001[$i]['combustible_litros_ene'] / $cda001[$i]['nivel_act_kms_ene']));

                        $entity_cda001->setFebreroNivelAct($cda001[$i]['nivel_act_kms_feb']);
                        $entity_cda001->setFebreroConsumo($cda001[$i]['combustible_litros_feb']);
                        $entity_cda001->setFebreroIndice(($cda001[$i]['nivel_act_kms_feb'] == 0 ? 0 : $cda001[$i]['combustible_litros_feb'] / $cda001[$i]['nivel_act_kms_feb']));

                        $entity_cda001->setMarzoNivelAct($cda001[$i]['nivel_act_kms_mar']);
                        $entity_cda001->setMarzoConsumo($cda001[$i]['combustible_litros_mar']);
                        $entity_cda001->setMarzoIndice(($cda001[$i]['nivel_act_kms_mar'] == 0 ? 0 : $cda001[$i]['combustible_litros_mar'] / $cda001[$i]['nivel_act_kms_mar']));

                        $entity_cda001->setAbrilNivelAct($cda001[$i]['nivel_act_kms_abr']);
                        $entity_cda001->setAbrilConsumo($cda001[$i]['combustible_litros_abr']);
                        $entity_cda001->setAbrilIndice(($cda001[$i]['nivel_act_kms_abr'] == 0 ? 0 : $cda001[$i]['combustible_litros_abr'] / $cda001[$i]['nivel_act_kms_abr']));

                        $entity_cda001->setMayoNivelAct($cda001[$i]['nivel_act_kms_may']);
                        $entity_cda001->setMayoConsumo($cda001[$i]['combustible_litros_may']);
                        $entity_cda001->setMayoIndice(($cda001[$i]['nivel_act_kms_may'] == 0 ? 0 : $cda001[$i]['combustible_litros_may'] / $cda001[$i]['nivel_act_kms_may']));

                        $entity_cda001->setJunioNivelAct($cda001[$i]['nivel_act_kms_jun']);
                        $entity_cda001->setJunioConsumo($cda001[$i]['combustible_litros_jun']);
                        $entity_cda001->setJunioIndice(($cda001[$i]['nivel_act_kms_jun'] == 0 ? 0 : $cda001[$i]['combustible_litros_jun'] / $cda001[$i]['nivel_act_kms_jun']));

                        $entity_cda001->setJulioNivelAct($cda001[$i]['nivel_act_kms_jul']);
                        $entity_cda001->setJulioConsumo($cda001[$i]['combustible_litros_jul']);
                        $entity_cda001->setJulioIndice(($cda001[$i]['nivel_act_kms_jul'] == 0 ? 0 : $cda001[$i]['combustible_litros_jul'] / $cda001[$i]['nivel_act_kms_jul']));

                        $entity_cda001->setAgostoNivelAct($cda001[$i]['nivel_act_kms_ago']);
                        $entity_cda001->setAgostoConsumo($cda001[$i]['combustible_litros_ago']);
                        $entity_cda001->setAgostoIndice(($cda001[$i]['nivel_act_kms_ago'] == 0 ? 0 : $cda001[$i]['combustible_litros_ago'] / $cda001[$i]['nivel_act_kms_ago']));

                        $entity_cda001->setSeptiembreNivelAct($cda001[$i]['nivel_act_kms_sep']);
                        $entity_cda001->setSeptiembreConsumo($cda001[$i]['combustible_litros_sep']);
                        $entity_cda001->setSeptiembreIndice(($cda001[$i]['nivel_act_kms_sep'] == 0 ? 0 : $cda001[$i]['combustible_litros_sep'] / $cda001[$i]['nivel_act_kms_sep']));

                        $entity_cda001->setOctubreNivelAct($cda001[$i]['nivel_act_kms_oct']);
                        $entity_cda001->setOctubreConsumo($cda001[$i]['combustible_litros_oct']);
                        $entity_cda001->setOctubreIndice(($cda001[$i]['nivel_act_kms_oct'] == 0 ? 0 : $cda001[$i]['combustible_litros_oct'] / $cda001[$i]['nivel_act_kms_oct']));

                        $entity_cda001->setNoviembreNivelAct($cda001[$i]['nivel_act_kms_nov']);
                        $entity_cda001->setNoviembreConsumo($cda001[$i]['combustible_litros_nov']);
                        $entity_cda001->setNoviembreIndice(($cda001[$i]['nivel_act_kms_nov'] == 0 ? 0 : $cda001[$i]['combustible_litros_nov'] / $cda001[$i]['nivel_act_kms_nov']));

                        $entity_cda001->setDiciembreNivelAct($cda001[$i]['nivel_act_kms_dic']);
                        $entity_cda001->setDiciembreConsumo($cda001[$i]['combustible_litros_dic']);
                        $entity_cda001->setDiciembreIndice(($cda001[$i]['nivel_act_kms_dic'] == 0 ? 0 : $cda001[$i]['combustible_litros_dic'] / $cda001[$i]['nivel_act_kms_dic']));


                        try {
                            $em->persist($entity_cda001);
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

            } else {

                /*SI NO EXISTEN DATOS PARA EL CDA, BUSCAMOS LAS ACTIVIDADES DE LA UNIDAD Y EL PORTADOR Y CREAMOS EL CDA
                CON VALORES EN 0 A PARTIR DE LA ACTIVIDADES*/

                $actividades = $em->getRepository('PortadoresBundle:Actividad')->findBy(array('portadorid' => $portador, 'visible' => true));

                /*COMPROBAMOS QUE EXISTAN ACTIVIDADES FILTRADAS*/
                if ($actividades) {
                    foreach ($actividades as $actividad) {
                        $entity_cda001 = new CDA001();

                        $entity_cda001->setActividad($actividad);
                        $entity_cda001->setNunidadid($em->getRepository('PortadoresBundle:Unidad')->find($unidadid));
                        $entity_cda001->setPortadorid($em->getRepository('PortadoresBundle:Portador')->find($portadorid));
                        $entity_cda001->setMoneda($moneda);
                        $entity_cda001->setAnno($anno);

                        $entity_cda001->setRealNivelAct(0.00);
                        $entity_cda001->setRealConsumo(0.00);
                        $entity_cda001->setAcumuladoNivelAct(0.00);
                        $entity_cda001->setAcumuladoConsumo(0.00);
                        $entity_cda001->setEstimadoNivelAct(0.00);
                        $entity_cda001->setEstimadoConsumo(0.00);
                        $entity_cda001->setPropuestaNivelAct(0.00);
                        $entity_cda001->setPropuestaConsumo(0.00);
                        $entity_cda001->setPlanFinalNivelAct(0.00);
                        $entity_cda001->setPlanFinalConsumo(0.00);
                        $entity_cda001->setPlanFinalNivelAct(0.00);
                        $entity_cda001->setPlanFinalConsumo(0.00);

                        $entity_cda001->setEneroNivelAct(0.00);
                        $entity_cda001->setEneroConsumo(0.00);

                        $entity_cda001->setFebreroNivelAct(0.00);
                        $entity_cda001->setFebreroConsumo(0.00);

                        $entity_cda001->setMarzoNivelAct(0.00);
                        $entity_cda001->setMarzoConsumo(0.00);

                        $entity_cda001->setAbrilNivelAct(0.00);
                        $entity_cda001->setAbrilConsumo(0.00);

                        $entity_cda001->setMayoNivelAct(0.00);
                        $entity_cda001->setMayoConsumo(0.00);

                        $entity_cda001->setJunioNivelAct(0.00);
                        $entity_cda001->setJunioConsumo(0.00);

                        $entity_cda001->setJulioNivelAct(0.00);
                        $entity_cda001->setJulioConsumo(0.00);

                        $entity_cda001->setAgostoNivelAct(0.00);
                        $entity_cda001->setAgostoConsumo(0.00);

                        $entity_cda001->setSeptiembreNivelAct(0.00);
                        $entity_cda001->setSeptiembreConsumo(0.00);

                        $entity_cda001->setOctubreNivelAct(0.00);
                        $entity_cda001->setOctubreConsumo(0.00);

                        $entity_cda001->setNoviembreNivelAct(0.00);
                        $entity_cda001->setNoviembreConsumo(0.00);

                        $entity_cda001->setDiciembreNivelAct(0.00);
                        $entity_cda001->setDiciembreConsumo(0.00);

                        try {
                            $em->persist($entity_cda001);
                            $em->flush();

                        } catch (\Exception $ex) {
                            if ($ex instanceof HttpException) {
                                return new JsonResponse(['success' => false, 'message' => $ex->getMessage()]);
                            } else {
                                throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $ex->getMessage(), $ex);
                            }
                        }
                    }
                } else {
                    /*SI  NO EXISTEN ACTIVIDADES FILTRADAS, ENTONCES MANDAMOS UN AVISO PARA QUE FILTREN LAS ACTIVIDADES*/
                    return new JsonResponse(array('success' => false, 'cls' => 'warning', 'message' => 'No existen actividades con el portador ' . $portador->getNombre()));
                }

            }
        }

        return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Datos del CDA001 generado con éxito.'));
    }

    /**
     * @Route("/ncda001/guardarCambios", name="guardarCambios",options={"expose"=true} )
     * @param Request $request
     * @return JsonResponse
     */
    public function guardarCambiosAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $data = json_decode($request->get('store'));
        $portadorid = $request->get('portadorid');
        $unidadid = $request->get('unidadid');
        $anno = $request->get('anno');
        $monedaStr = $request->get('moneda');

        $entities = $em->getRepository('PortadoresBundle:CDA001')->findBy(array('anno' => $anno, 'portadorid' => $portadorid));
        $moneda = $em->getRepository('PortadoresBundle:Moneda')->findoneBy(array('id' => $monedaStr));
        foreach ($entities as $entity)
            $em->remove($entity);

        try {
            $em->flush();
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }

        foreach ($data as $dat) {

            /** @var CDA001 $entity */
            $entity = new CDA001();

            $entity->setAnno($anno);
            $entity->setActividad($em->getRepository('PortadoresBundle:Actividad')->find($dat->actividadid));
            $entity->setPortadorid($em->getRepository('PortadoresBundle:Portador')->find($dat->portadorid));
            $entity->setMoneda($em->getRepository('PortadoresBundle:Moneda')->find($moneda));
            $entity->setNunidadid($em->getRepository('PortadoresBundle:Unidad')->find($unidadid));

            $entity->setRealNivelAct($dat->real_nivel_act);
            $entity->setRealConsumo($dat->real_consumo);
            $entity->setAcumuladoNivelAct($dat->acumulado_nivel_act);
            $entity->setAcumuladoConsumo($dat->acumulado_consumo);
            $entity->setEstimadoNivelAct($dat->estimado_nivel_act);
            $entity->setEstimadoConsumo($dat->estimado_consumo);
            $entity->setPropuestaNivelAct($dat->propuesta_plan_nivel_act);
            $entity->setPropuestaConsumo($dat->propuesta_plan_consumo);
            $entity->setPlanFinalNivelAct($dat->plan_final_nivel_act);
            $entity->setPlanFinalConsumo($dat->plan_final_consumo);
            $entity->setPlanFinalNivelAct($dat->total_desglose_nivel_act);
            $entity->setPlanFinalConsumo($dat->total_desglose_consumo);

            $entity->setEneroNivelAct($dat->enero_nivel_act);
            $entity->setEneroConsumo($dat->enero_consumo);

            $entity->setFebreroNivelAct($dat->febrero_nivel_act);
            $entity->setFebreroConsumo($dat->febrero_consumo);

            $entity->setMarzoNivelAct($dat->marzo_nivel_act);
            $entity->setMarzoConsumo($dat->marzo_consumo);

            $entity->setAbrilNivelAct($dat->abril_nivel_act);
            $entity->setAbrilConsumo($dat->abril_consumo);

            $entity->setMayoNivelAct($dat->mayo_nivel_act);
            $entity->setMayoConsumo($dat->mayo_consumo);

            $entity->setJunioNivelAct($dat->junio_nivel_act);
            $entity->setJunioConsumo($dat->junio_consumo);

            $entity->setJulioNivelAct($dat->julio_nivel_act);
            $entity->setJulioConsumo($dat->julio_consumo);

            $entity->setAgostoNivelAct($dat->agosto_nivel_act);
            $entity->setAgostoConsumo($dat->agosto_consumo);

            $entity->setSeptiembreNivelAct($dat->septiembre_nivel_act);
            $entity->setSeptiembreConsumo($dat->septiembre_consumo);

            $entity->setOctubreNivelAct($dat->octubre_nivel_act);
            $entity->setOctubreConsumo($dat->octubre_consumo);

            $entity->setNoviembreNivelAct($dat->noviembre_nivel_act);
            $entity->setNoviembreConsumo($dat->noviembre_consumo);

            $entity->setDiciembreNivelAct($dat->diciembre_nivel_act);
            $entity->setDiciembreConsumo($dat->diciembre_consumo);

            $em->persist($entity);
        }

        try {
            $em->flush();
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }

        return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Datos del CDA001 guardado con éxito.'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function exportAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->get('store'));

        $portador = $data[0]->portador_nombre;
        $unidad = $data[0]->unidad_nombre;
        $portador_unidad_M = $data[0]->actividad_um;
        $anno_real = $data[0]->anno_real;
        $anno_acum = $data[0]->anno_acumulado;
        $anno_est = $data[0]->anno_acumulado;
        $anno_prop = $data[0]->anno_propuesta;
        $anno_pf = $data[0]->anno_propuesta;

        $html = "
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
    <td colspan=\"59\">MODELO CDA 001 " . strtoupper($portador) . "</td>
  </tr>
  <tr>
    <td colspan=\"59\">U.M para el consumo: " . strtoupper($portador_unidad_M) . "</td>
  </tr>
  <tr>
    <td colspan=\"59\">Entidad: " . strtoupper($unidad) . "</td>
  </tr>
   <tr>
    <td rowspan=\"2\" align=\"center\" class=\"bordes_tablas\">CÓDIGO</td>
    <td rowspan=\"2\" align=\"center\" class=\"bordes_arriba_derecho_abajo\">ACTIVIDAD</td>
    <td rowspan=\"2\" align=\"center\" class=\"bordes_arriba_derecho_abajo\">U.M N ACT.</td>
    <td colspan=\"3\" align=\"center\" class=\"bordes_arriba_derecho_abajo\">REAL - " . $anno_real . "</td>
    <td colspan=\"3\" align=\"center\" class=\"bordes_arriba_derecho_abajo\">ACUMULADO - " . $anno_acum . "</td>
    <td colspan=\"3\" class=\"bordes_arriba_derecho_abajo\">ESTIMADO - " . $anno_est . "</td>
    <td colspan=\"3\" align=\"center\" class=\"bordes_arriba_derecho_abajo\">PROPUESTA - " . $anno_prop . "</td>
    <td rowspan=\"2\" align=\"center\" class=\"bordes_arriba_derecho_abajo\">DESCUENTOS POR DETERIORO</td>
    <td colspan=\"3\" align=\"center\" class=\"bordes_arriba_derecho_abajo\">PLAN FINAL - " . $anno_pf . "</td>
    <td colspan=\"3\" align=\"center\" class=\"bordes_arriba_derecho_abajo\">TOTAL SEGÚN DESGLOSE</td>
    <td colspan=\"3\" align=\"center\" class=\"bordes_arriba_derecho_abajo\">ENERO</td>
    <td colspan=\"3\" align=\"center\" class=\"bordes_arriba_derecho_abajo\">FEBRERO</td>
    <td colspan=\"3\" align=\"center\" class=\"bordes_arriba_derecho_abajo\">MARZO</td>
    <td colspan=\"3\" align=\"center\" class=\"bordes_arriba_derecho_abajo\">ABRIL</td>
    <td colspan=\"3\" align=\"center\" class=\"bordes_arriba_derecho_abajo\">MAYO</td>
    <td colspan=\"3\" align=\"center\" class=\"bordes_arriba_derecho_abajo\">JUNIO</td>
    <td colspan=\"3\" align=\"center\" class=\"bordes_arriba_derecho_abajo\">JULIO</td>
    <td colspan=\"3\" align=\"center\" class=\"bordes_arriba_derecho_abajo\">AGOSTO</td>
    <td colspan=\"3\" align=\"center\" class=\"bordes_arriba_derecho_abajo\">SEPTIEMBRE</td>
    <td colspan=\"3\" align=\"center\" class=\"bordes_arriba_derecho_abajo\">OCTUBRE</td>
    <td colspan=\"3\" align=\"center\" class=\"bordes_arriba_derecho_abajo\">NOVIEMBRE</td>
    <td colspan=\"3\" align=\"center\" class=\"bordes_arriba_derecho_abajo\">DICIEMBRE</td>
     <td rowspan=\"2\" align=\"center\" class=\"bordes_arriba_derecho_abajo\" >CÓDIGO GAE</td>
  </tr>
  <tr>
    <td align=\"center\" class=\"bordes_derecho_abajo\">NIVEL DE ACTIVIDAD</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">CONSUMO</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">ÍNDICE</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">NIVEL DE ACTIVIDAD</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">CONSUMO</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">ÍNDICE</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">NIVEL DE ACTIVIDAD</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">CONSUMO</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">ÍNDICE</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">NIVEL DE ACTIVIDAD</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">CONSUMO</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">ÍNDICE</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">NIVEL DE ACTIVIDAD</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">CONSUMO</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">ÍNDICE</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">NIVEL DE ACTIVIDAD</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">CONSUMO</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">ÍNDICE</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">NIVEL DE ACTIVIDAD</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">CONSUMO</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">ÍNDICE</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">NIVEL DE ACTIVIDAD</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">CONSUMO</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">ÍNDICE</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">NIVEL DE ACTIVIDAD</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">CONSUMO</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">ÍNDICE</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">NIVEL DE ACTIVIDAD</span></td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">CONSUMO</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">ÍNDICE</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">NIVEL DE ACTIVIDAD</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">CONSUMO</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">ÍNDICE</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">NIVEL DE ACTIVIDAD</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">CONSUMO</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">ÍNDICE</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">NIVEL DE ACTIVIDAD</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">CONSUMO</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">ÍNDICE</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">NIVEL DE ACTIVIDAD</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">CONSUMO</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">ÍNDICE</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">NIVEL DE ACTIVIDAD</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">CONSUMO</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">ÍNDICE</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">NIVEL DE ACTIVIDAD</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">CONSUMO</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">ÍNDICE</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">NIVEL DE ACTIVIDAD</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">CONSUMO</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">ÍNDICE</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">NIVEL DE ACTIVIDAD</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">CONSUMO</td>
    <td align=\"center\" class=\"bordes_derecho_abajo\">ÍNDICE</td>
  </tr>
         ";


        $real_total_consumo = 0;
        $acumulado_total_consumo = 0;
        $estimado_total_consumo = 0;
        $propuesta_total_consumo = 0;
        $descuento_deterioro_total = 0;
        $plan_final_total_consumo = 0;
        $total_desglose_total_consumo = 0;
        $enero_total_consumo = 0;
        $febrero_total_consumo = 0;
        $marzo_total_consumo = 0;
        $abril_total_consumo = 0;
        $mayo_total_consumo = 0;
        $junio_total_consumo = 0;
        $julio_total_consumo = 0;
        $agosto_total_consumo = 0;
        $septiembre_total_consumo = 0;
        $octubre_total_consumo = 0;
        $noviembre_total_consumo = 0;
        $diciembre_total_consumo = 0;

        foreach ($data as $dat) {

            $real_indice = $dat->real_nivel_act == 0 ? 0 : $dat->real_consumo / $dat->real_nivel_act;
            $acumulado_indice = $dat->acumulado_nivel_act == 0 ? 0 : $dat->acumulado_consumo / $dat->acumulado_nivel_act;
            $estimado_indice = $dat->estimado_nivel_act == 0 ? 0 : $dat->estimado_consumo / $dat->estimado_nivel_act;
            $propuesta_indice = $dat->propuesta_plan_nivel_act == 0 ? 0 : $dat->propuesta_plan_consumo / $dat->propuesta_plan_nivel_act;
            $plan_final_indice = $dat->plan_final_nivel_act == 0 ? 0 : $dat->plan_final_consumo / $dat->plan_final_nivel_act;

            $enero_indice = $dat->enero_nivel_act == 0 ? 0 : $dat->enero_consumo / $dat->enero_nivel_act;
            $febrero_indice = $dat->febrero_nivel_act == 0 ? 0 : $dat->febrero_consumo / $dat->febrero_nivel_act;
            $marzo_indice = $dat->marzo_nivel_act == 0 ? 0 : $dat->marzo_consumo / $dat->marzo_nivel_act;
            $abril_indice = $dat->abril_nivel_act == 0 ? 0 : $dat->abril_consumo / $dat->abril_nivel_act;
            $mayo_indice = $dat->mayo_nivel_act == 0 ? 0 : $dat->mayo_consumo / $dat->mayo_nivel_act;
            $junio_indice = $dat->junio_nivel_act == 0 ? 0 : $dat->junio_consumo / $dat->junio_nivel_act;
            $julio_indice = $dat->julio_nivel_act == 0 ? 0 : $dat->julio_consumo / $dat->julio_nivel_act;
            $agosto_indice = $dat->agosto_nivel_act == 0 ? 0 : $dat->agosto_consumo / $dat->agosto_nivel_act;
            $septiembre_indice = $dat->septiembre_nivel_act == 0 ? 0 : $dat->septiembre_consumo / $dat->septiembre_nivel_act;
            $octubre_indice = $dat->octubre_nivel_act == 0 ? 0 : $dat->octubre_consumo / $dat->octubre_nivel_act;
            $noviembre_indice = $dat->noviembre_nivel_act == 0 ? 0 : $dat->noviembre_consumo / $dat->noviembre_nivel_act;
            $diciembre_indice = $dat->diciembre_nivel_act == 0 ? 0 : $dat->diciembre_consumo / $dat->diciembre_nivel_act;

            $nivelactiv = $dat->enero_nivel_act + $dat->febrero_nivel_act + $dat->marzo_nivel_act + $dat->abril_nivel_act + $dat->mayo_nivel_act
                + $dat->junio_nivel_act + $dat->julio_nivel_act + $dat->agosto_nivel_act + $dat->septiembre_nivel_act + $dat->octubre_nivel_act
                + $dat->noviembre_nivel_act + $dat->diciembre_nivel_act;

            $consumo = $dat->enero_consumo + $dat->febrero_consumo + $dat->marzo_consumo + $dat->abril_consumo + $dat->mayo_consumo
                + $dat->junio_consumo + $dat->julio_consumo + $dat->agosto_consumo + $dat->septiembre_consumo + $dat->octubre_consumo
                + $dat->noviembre_consumo + $dat->diciembre_consumo;

            $indice = $enero_indice + $febrero_indice + $marzo_indice + $abril_indice + $mayo_indice + $junio_indice + $julio_indice + $agosto_indice + $septiembre_indice + $octubre_indice
                + $noviembre_indice + $diciembre_indice;


            $descuento_deterioro = $dat->propuesta_plan_consumo - $dat->plan_final_consumo;

            $real_total_consumo += $dat->real_consumo;
            $acumulado_total_consumo += $dat->acumulado_consumo;
            $estimado_total_consumo += $dat->estimado_consumo;
            $propuesta_total_consumo += $dat->propuesta_plan_consumo;
            $descuento_deterioro_total += $descuento_deterioro;
            $plan_final_total_consumo += $dat->plan_final_consumo;
            $total_desglose_total_consumo += $consumo;
            $enero_total_consumo += $dat->enero_consumo;
            $febrero_total_consumo += $dat->febrero_consumo;
            $marzo_total_consumo += $dat->marzo_consumo;
            $abril_total_consumo += $dat->abril_consumo;
            $mayo_total_consumo += $dat->mayo_consumo;
            $junio_total_consumo += $dat->junio_consumo;
            $julio_total_consumo += $dat->julio_consumo;
            $agosto_total_consumo += $dat->agosto_consumo;
            $septiembre_total_consumo += $dat->septiembre_consumo;
            $octubre_total_consumo += $dat->octubre_consumo;
            $noviembre_total_consumo += $dat->noviembre_consumo;
            $diciembre_total_consumo += $dat->diciembre_consumo;


            $html .= "
<tr>
    <td align='center' class=\"bordes_derecho_abajo_izquierda\">$dat->codigo_mep_act</td>
    <td align='center' class=\"bordes_derecho_abajo\">$dat->actividad_nombre</td>
    <td align='center' class=\"bordes_derecho_abajo\">$dat->actividad_um</td>
    <td align='center' class=\"bordes_derecho_abajo\">$dat->real_nivel_act</td>
    <td align='center' class=\"bordes_derecho_abajo\" >$dat->real_consumo</td>
    <td align='center' class=\"bordes_derecho_abajo\">" . round($real_indice, 3) . "</td>
    <td align='center' class=\"bordes_derecho_abajo\">$dat->acumulado_nivel_act</td>
    <td align='center' class=\"bordes_derecho_abajo\">$dat->acumulado_consumo</td>
    <td align='center' class=\"bordes_derecho_abajo\">" . round($acumulado_indice, 3) . "</td>
    <td align='center' class=\"bordes_derecho_abajo\">$dat->estimado_nivel_act </td>
    <td align='center' class=\"bordes_derecho_abajo\">$dat->estimado_consumo </td>
    <td align='center' class=\"bordes_derecho_abajo\">" . round($estimado_indice, 3) . "</td>
    <td align='center' class=\"bordes_derecho_abajo\">$dat->propuesta_plan_nivel_act</td>
    <td align='center' class=\"bordes_derecho_abajo\">$dat->propuesta_plan_consumo</td>
    <td align='center' class=\"bordes_derecho_abajo\">" . round($propuesta_indice, 3) . "</td>
    <td align='center' class=\"bordes_derecho_abajo\">" . $descuento_deterioro . "</td>
    <td align='center' class=\"bordes_derecho_abajo\">$dat->plan_final_nivel_act</td>
    <td align='center' class=\"bordes_derecho_abajo\">$dat->plan_final_consumo</td>
    <td align='center' class=\"bordes_derecho_abajo\">" . round($plan_final_indice, 3) . "</td>
    <td align='center' class=\"bordes_derecho_abajo\">$nivelactiv</td>
    <td align='center' class=\"bordes_derecho_abajo\">$consumo</td>
    <td align='center' class=\"bordes_derecho_abajo\">" . round($indice, 3) . "</td>
    <td align='center' class=\"bordes_derecho_abajo\">$dat->enero_nivel_act </td>
    <td align='center' class=\"bordes_derecho_abajo\">$dat->enero_consumo </td>
    <td align='center' class=\"bordes_derecho_abajo\">" . round($enero_indice, 3) . "</td>
    <td align='center' class=\"bordes_derecho_abajo\">$dat->febrero_nivel_act</td>
    <td align='center' class=\"bordes_derecho_abajo\">$dat->febrero_consumo</td>
    <td align='center' class=\"bordes_derecho_abajo\">" . round($febrero_indice, 3) . "</td>
    <td align='center' class=\"bordes_derecho_abajo\">$dat->marzo_nivel_act</td>
    <td align='center' class=\"bordes_derecho_abajo\">$dat->marzo_consumo</td>
    <td align='center' class=\"bordes_derecho_abajo\">" . round($marzo_indice, 3) . "</td>
    <td align='center' class=\"bordes_derecho_abajo\">$dat->abril_nivel_act</td>
    <td align='center' class=\"bordes_derecho_abajo\">$dat->abril_consumo</td>
    <td align='center' class=\"bordes_derecho_abajo\">" . round($abril_indice, 3) . "</td>
    <td align='center' class=\"bordes_derecho_abajo\">$dat->mayo_nivel_act</td>
    <td align='center' class=\"bordes_derecho_abajo\">$dat->mayo_consumo</td>
    <td align='center' class=\"bordes_derecho_abajo\">" . round($mayo_indice, 3) . "</td>
    <td align='center' class=\"bordes_derecho_abajo\">$dat->junio_nivel_act</td>
    <td align='center' class=\"bordes_derecho_abajo\">$dat->junio_consumo</td>
    <td align='center' class=\"bordes_derecho_abajo\">" . round($junio_indice, 3) . "</td>
    <td align='center' class=\"bordes_derecho_abajo\">$dat->julio_nivel_act</td>
    <td align='center' class=\"bordes_derecho_abajo\">$dat->julio_consumo</td>
    <td align='center' class=\"bordes_derecho_abajo\">" . round($julio_indice, 3) . "</td>
    <td align='center' class=\"bordes_derecho_abajo\">$dat->agosto_nivel_act</td>
    <td align='center' class=\"bordes_derecho_abajo\">$dat->agosto_consumo</td>
    <td align='center' class=\"bordes_derecho_abajo\">" . round($agosto_indice, 3) . "</td>
    <td align='center' class=\"bordes_derecho_abajo\">$dat->septiembre_nivel_act</td>
    <td align='center' class=\"bordes_derecho_abajo\">$dat->septiembre_consumo</td>
    <td align='center' class=\"bordes_derecho_abajo\">" . round($septiembre_indice, 3) . "</td>
    <td align='center' class=\"bordes_derecho_abajo\">$dat->octubre_nivel_act</td>
    <td align='center' class=\"bordes_derecho_abajo\">$dat->octubre_consumo</td>
    <td align='center' class=\"bordes_derecho_abajo\">" . round($octubre_indice, 3) . "</td>
    <td align='center' class=\"bordes_derecho_abajo\">$dat->noviembre_nivel_act</td>
    <td align='center' class=\"bordes_derecho_abajo\">$dat->noviembre_consumo</td>
    <td align='center' class=\"bordes_derecho_abajo\">" . round($noviembre_indice, 3) . "</td>
    <td align='center' class=\"bordes_derecho_abajo\">$dat->diciembre_nivel_act</td>
    <td align='center' class=\"bordes_derecho_abajo\">$dat->diciembre_consumo</td>
    <td align='center' class=\"bordes_derecho_abajo\">" . round($diciembre_indice, 3) . "</td>
    <td align='center' class=\"bordes_derecho_abajo\">$dat->codigo_gae_act</td>
  </tr>

 
 ";
        }


        $html .= "
  <tr>
    <td class=\"bordes_derecho_abajo_izquierda\"></td>
    <td class=\"bordes_derecho_abajo\">TOTAL</td>
    <td class=\"bordes_derecho_abajo\"></td>
    <td class=\"bordes_derecho_abajo\"></span></td>
    <td align='center' class=\"bordes_derecho_abajo\">$real_total_consumo</td>
    <td class=\"bordes_derecho_abajo\"></td>
    <td class=\"bordes_derecho_abajo\"></td>
    <td align='center' class=\"bordes_derecho_abajo\">$acumulado_total_consumo</td>
    <td class=\"bordes_derecho_abajo\"></td>
    <td class=\"bordes_derecho_abajo\"></td>
    <td align='center' class=\"bordes_derecho_abajo\">$estimado_total_consumo</td>
    <td class=\"bordes_derecho_abajo\"></td>
    <td class=\"bordes_derecho_abajo\"></td>
    <td align='center' class=\"bordes_derecho_abajo\">$propuesta_total_consumo</td>
    <td class=\"bordes_derecho_abajo\"></td>
    <td align='center' class=\"bordes_derecho_abajo\">$descuento_deterioro_total</td>
    <td class=\"bordes_derecho_abajo\">&nbsp;</td>
    <td align='center' class=\"bordes_derecho_abajo\">$plan_final_total_consumo</td>
    <td class=\"bordes_derecho_abajo\"></td>
    <td class=\"bordes_derecho_abajo\">&nbsp;</td>
    <td align='center' class=\"bordes_derecho_abajo\">$total_desglose_total_consumo</td>
    <td class=\"bordes_derecho_abajo\"></span></td>
    <td class=\"bordes_derecho_abajo\">&nbsp;</td>
    <td align='center' class=\"bordes_derecho_abajo\">$enero_total_consumo</td>
    <td class=\"bordes_derecho_abajo\"></td>
    <td class=\"bordes_derecho_abajo\">&nbsp;</td>
    <td align='center' class=\"bordes_derecho_abajo\">$febrero_total_consumo</td>
    <td class=\"bordes_derecho_abajo\"></td>
    <td class=\"bordes_derecho_abajo\">&nbsp;</td>
    <td align='center' class=\"bordes_derecho_abajo\">$marzo_total_consumo</td>
    <td class=\"bordes_derecho_abajo\"></td>
    <td class=\"bordes_derecho_abajo\">&nbsp;</td>
    <td align='center' class=\"bordes_derecho_abajo\">$abril_total_consumo</td>
    <td class=\"bordes_derecho_abajo\"></td>
    <td class=\"bordes_derecho_abajo\">&nbsp;</td>
    <td align='center' class=\"bordes_derecho_abajo\">$mayo_total_consumo</td>
    <td class=\"bordes_derecho_abajo\"></td>
    <td class=\"bordes_derecho_abajo\" ></td>
    <td align='center' class=\"bordes_derecho_abajo\">$junio_total_consumo</td>
    <td class=\"bordes_derecho_abajo\"></td>
    <td class=\"bordes_derecho_abajo\">&nbsp;</td>
    <td align='center' class=\"bordes_derecho_abajo\">$julio_total_consumo</td>
    <td class=\"bordes_derecho_abajo\"></td>
    <td class=\"bordes_derecho_abajo\">&nbsp;</td>
    <td align='center' class=\"bordes_derecho_abajo\">$agosto_total_consumo</td>
    <td class=\"bordes_derecho_abajo\"></td>
    <td class=\"bordes_derecho_abajo\">&nbsp;</td>
    <td align='center' class=\"bordes_derecho_abajo\">$septiembre_total_consumo</td>
    <td class=\"bordes_derecho_abajo\"></td>
    <td class=\"bordes_derecho_abajo\">&nbsp;</td>
    <td align='center' class=\"bordes_derecho_abajo\">$octubre_total_consumo</td>
    <td class=\"bordes_derecho_abajo\"></td>
    <td class=\"bordes_derecho_abajo\">&nbsp;</td>
    <td align='center' class=\"bordes_derecho_abajo\">$noviembre_total_consumo</td>
    <td class=\"bordes_derecho_abajo\"></td>
    <td class=\"bordes_derecho_abajo\">&nbsp;</td>
    <td align='center' class=\"bordes_derecho_abajo\">$diciembre_total_consumo</td>
    <td class=\"bordes_derecho_abajo\"></td>
    <td class=\"bordes_derecho_abajo\">&nbsp;</td>
    
  </tr>
 ";

        $html .= "
      </table>
</body>
</html>
      ";

        return new JsonResponse(array('success' => true, 'html' => $html));

    }

    private function unidadesToString($_unidades)
    {
        $_string_unidades = "'" . $_unidades[0] . "'";
        for ($i = 1, $iMax = \count($_unidades); $i < $iMax; $i++) {
            $_string_unidades .= ",'" . $_unidades[$i] . "'";
        }
        return $_string_unidades;
    }

//    public function exportAction(Request $request)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $request = $this->getRequest();
//
//        $data = json_decode($request->get('store'));
//
//        $portador = $data[0]->portador_nombre;
//        $unidad = $data[0]->unidad_nombre;
//        $portador_unidad_M = $data[0]->actividad_um;
//        $anno_real = $data[0]->anno_real;
//        $anno_acum = $data[0]->anno_acumulado;
//        $anno_est = $data[0]->anno_acumulado;
//        $anno_prop = $data[0]->anno_propuesta;
//        $anno_pf = $data[0]->anno_propuesta;
//
//        $phpexcel = $this->get('phpexcel');
//        $excel_object = $phpexcel->createPHPExcelObject();
//        $active_sheet = $excel_object->getActiveSheet();
//        $active_sheet->setTitle('CDA 001' . $portador . $anno_pf);
//
//        $active_sheet->mergeCells('B2:E2');
//        $active_sheet->setCellValue('B2', 'MODELO CDA 001: ' . $portador);
//        $active_sheet->getStyle('B2')->getFont()->setSize(12);
//        $active_sheet->getStyle('B2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//        $active_sheet->mergeCells('B3:E3');
//        $active_sheet->setCellValue('B3', 'U.M para el consumo: ' . $portador_unidad_M);
//        $active_sheet->getStyle('B3')->getFont()->setSize(12);
//        $active_sheet->getStyle('B3')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//        $active_sheet->mergeCells('B4:E4');
//        $active_sheet->setCellValue('B4', 'Entidad: ' . $unidad);
//        $active_sheet->getStyle('B4')->getFont()->setSize(12);
//        $active_sheet->getStyle('B4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//
//        $active_sheet->mergeCells('B6:B7');
//        $active_sheet->setCellValue('B6', 'CÓDIGO');
//        $active_sheet->getStyle('B6')->getFont()->setSize(11);
//        $active_sheet->getStyle('B6')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('C6:F7');
//        $active_sheet->setCellValue('C6', 'ACTIVIDAD');
//        $active_sheet->getStyle('C6')->getFont()->setSize(11);
//        $active_sheet->getStyle('C6')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('G6:H7');
//        $active_sheet->setCellValue('G6', 'U.M N.ACT');
//        $active_sheet->getStyle('G6')->getFont()->setSize(11);
//        $active_sheet->getStyle('G6')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('I6:L6');
//        $active_sheet->setCellValue('I6', 'REAL');
//        $active_sheet->getStyle('I6')->getFont()->setSize(11);
//        $active_sheet->getStyle('I6')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('I7:J7');
//        $active_sheet->setCellValue('I7', 'NIVEL DE ACTIVIDAD');
//        $active_sheet->getStyle('I7')->getFont()->setSize(11);
//        $active_sheet->getStyle('I7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->setCellValue('K7', 'CONSUMO');
//        $active_sheet->getStyle('K7')->getFont()->setSize(11);
//        $active_sheet->getStyle('K7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->setCellValue('L7', 'ÍNDICE');
//        $active_sheet->getStyle('L7')->getFont()->setSize(11);
//        $active_sheet->getStyle('L7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('M6:P6');
//        $active_sheet->setCellValue('M6', 'ACUMULADO');
//        $active_sheet->getStyle('M6')->getFont()->setSize(11);
//        $active_sheet->getStyle('M6')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('M7:N7');
//        $active_sheet->setCellValue('M7', 'NIVEL DE ACTIVIDAD');
//        $active_sheet->getStyle('M7')->getFont()->setSize(11);
//        $active_sheet->getStyle('M7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->setCellValue('O7', 'CONSUMO');
//        $active_sheet->getStyle('O7')->getFont()->setSize(11);
//        $active_sheet->getStyle('O7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->setCellValue('P7', 'ÍNDICE');
//        $active_sheet->getStyle('P7')->getFont()->setSize(11);
//        $active_sheet->getStyle('P7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('Q6:T6');
//        $active_sheet->setCellValue('Q6', 'ESTIMADO');
//        $active_sheet->getStyle('Q6')->getFont()->setSize(11);
//        $active_sheet->getStyle('Q6')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('Q7:R7');
//        $active_sheet->setCellValue('Q7', 'NIVEL DE ACTIVIDAD');
//        $active_sheet->getStyle('Q7')->getFont()->setSize(11);
//        $active_sheet->getStyle('Q7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->setCellValue('S7', 'CONSUMO');
//        $active_sheet->getStyle('S7')->getFont()->setSize(11);
//        $active_sheet->getStyle('S7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->setCellValue('T7', 'ÍNDICE');
//        $active_sheet->getStyle('T7')->getFont()->setSize(11);
//        $active_sheet->getStyle('T7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('U6:X6');
//        $active_sheet->setCellValue('U6', 'PROPUESTA DE PLAN');
//        $active_sheet->getStyle('U6')->getFont()->setSize(11);
//        $active_sheet->getStyle('U6')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('U7:V7');
//        $active_sheet->setCellValue('U7', 'NIVEL DE ACTIVIDAD');
//        $active_sheet->getStyle('U7')->getFont()->setSize(11);
//        $active_sheet->getStyle('U7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->setCellValue('W7', 'CONSUMO');
//        $active_sheet->getStyle('W7')->getFont()->setSize(11);
//        $active_sheet->getStyle('W7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->setCellValue('X7', 'ÍNDICE');
//        $active_sheet->getStyle('X7')->getFont()->setSize(11);
//        $active_sheet->getStyle('X7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('Y6:AA7');
//        $active_sheet->setCellValue('Y6', 'DESCUENTOS POR DETERIORO');
//        $active_sheet->getStyle('Y6')->getFont()->setSize(11);
//        $active_sheet->getStyle('Y6')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('AB6:AE6');
//        $active_sheet->setCellValue('AB6', 'PLAN FINAL');
//        $active_sheet->getStyle('AB6')->getFont()->setSize(11);
//        $active_sheet->getStyle('AB6')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('AB7:AC7');
//        $active_sheet->setCellValue('AB7', 'NIVEL DE ACTIVIDAD');
//        $active_sheet->getStyle('AB7')->getFont()->setSize(11);
//        $active_sheet->getStyle('AB7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->setCellValue('AD7', 'CONSUMO');
//        $active_sheet->getStyle('AD7')->getFont()->setSize(11);
//        $active_sheet->getStyle('AD7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->setCellValue('AE7', 'ÍNDICE');
//        $active_sheet->getStyle('AE7')->getFont()->setSize(11);
//        $active_sheet->getStyle('AE7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('AF6:AI6');
//        $active_sheet->setCellValue('AF6', 'TOTAL SEGÚN DESGLOSE');
//        $active_sheet->getStyle('AF6')->getFont()->setSize(11);
//        $active_sheet->getStyle('AF6')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('AF7:AG7');
//        $active_sheet->setCellValue('AF7', 'NIVEL DE ACTIVIDAD');
//        $active_sheet->getStyle('AF7')->getFont()->setSize(11);
//        $active_sheet->getStyle('AF7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->setCellValue('AH7', 'CONSUMO');
//        $active_sheet->getStyle('AH7')->getFont()->setSize(11);
//        $active_sheet->getStyle('AH7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->setCellValue('AI7', 'ÍNDICE');
//        $active_sheet->getStyle('AI7')->getFont()->setSize(11);
//        $active_sheet->getStyle('AI7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('AJ6:AM6');
//        $active_sheet->setCellValue('AJ6', 'ENERO');
//        $active_sheet->getStyle('AJ6')->getFont()->setSize(11);
//        $active_sheet->getStyle('AJ6')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('AJ7:AK7');
//        $active_sheet->setCellValue('AJ7', 'NIVEL DE ACTIVIDAD');
//        $active_sheet->getStyle('AJ7')->getFont()->setSize(11);
//        $active_sheet->getStyle('AJ7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->setCellValue('AL7', 'CONSUMO');
//        $active_sheet->getStyle('AL7')->getFont()->setSize(11);
//        $active_sheet->getStyle('AL7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->setCellValue('AM7', 'ÍNDICE');
//        $active_sheet->getStyle('AM7')->getFont()->setSize(11);
//        $active_sheet->getStyle('AM7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('AN6:AQ6');
//        $active_sheet->setCellValue('AN6', 'FEBRERO');
//        $active_sheet->getStyle('AN6')->getFont()->setSize(11);
//        $active_sheet->getStyle('AN6')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('AN7:AO7');
//        $active_sheet->setCellValue('AN7', 'NIVEL DE ACTIVIDAD');
//        $active_sheet->getStyle('AN7')->getFont()->setSize(11);
//        $active_sheet->getStyle('AN7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->setCellValue('AP7', 'CONSUMO');
//        $active_sheet->getStyle('AP7')->getFont()->setSize(11);
//        $active_sheet->getStyle('AP7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->setCellValue('AQ7', 'ÍNDICE');
//        $active_sheet->getStyle('AQ7')->getFont()->setSize(11);
//        $active_sheet->getStyle('AQ7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('AR6:AU6');
//        $active_sheet->setCellValue('AR6', 'MARZO');
//        $active_sheet->getStyle('AR6')->getFont()->setSize(11);
//        $active_sheet->getStyle('AR6')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('AR7:AS7');
//        $active_sheet->setCellValue('AR7', 'NIVEL DE ACTIVIDAD');
//        $active_sheet->getStyle('AR7')->getFont()->setSize(11);
//        $active_sheet->getStyle('AR7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->setCellValue('AT7', 'CONSUMO');
//        $active_sheet->getStyle('AT7')->getFont()->setSize(11);
//        $active_sheet->getStyle('AT7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->setCellValue('AU7', 'ÍNDICE');
//        $active_sheet->getStyle('AU7')->getFont()->setSize(11);
//        $active_sheet->getStyle('AU7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('AV6:AY6');
//        $active_sheet->setCellValue('AV6', 'ABRIL');
//        $active_sheet->getStyle('AV6')->getFont()->setSize(11);
//        $active_sheet->getStyle('AV6')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('AV7:AW7');
//        $active_sheet->setCellValue('AV7', 'NIVEL DE ACTIVIDAD');
//        $active_sheet->getStyle('AV7')->getFont()->setSize(11);
//        $active_sheet->getStyle('AV7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->setCellValue('AX7', 'CONSUMO');
//        $active_sheet->getStyle('AX7')->getFont()->setSize(11);
//        $active_sheet->getStyle('AX7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->setCellValue('AY7', 'ÍNDICE');
//        $active_sheet->getStyle('AY7')->getFont()->setSize(11);
//        $active_sheet->getStyle('AY7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('AZ6:BC6');
//        $active_sheet->setCellValue('AZ6', 'MAYO');
//        $active_sheet->getStyle('AZ6')->getFont()->setSize(11);
//        $active_sheet->getStyle('AZ6')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('AZ7:BA7');
//        $active_sheet->setCellValue('AZ7', 'NIVEL DE ACTIVIDAD');
//        $active_sheet->getStyle('AZ7')->getFont()->setSize(11);
//        $active_sheet->getStyle('AZ7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->setCellValue('BB7', 'CONSUMO');
//        $active_sheet->getStyle('BB7')->getFont()->setSize(11);
//        $active_sheet->getStyle('BB7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->setCellValue('BC7', 'ÍNDICE');
//        $active_sheet->getStyle('BC7')->getFont()->setSize(11);
//        $active_sheet->getStyle('BC7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('BD6:BG6');
//        $active_sheet->setCellValue('BD6', 'JUNIO');
//        $active_sheet->getStyle('BD6')->getFont()->setSize(11);
//        $active_sheet->getStyle('BD6')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('BD7:BE7');
//        $active_sheet->setCellValue('BD7', 'NIVEL DE ACTIVIDAD');
//        $active_sheet->getStyle('BD7')->getFont()->setSize(11);
//        $active_sheet->getStyle('BD7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->setCellValue('BF7', 'CONSUMO');
//        $active_sheet->getStyle('BF7')->getFont()->setSize(11);
//        $active_sheet->getStyle('BF7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->setCellValue('BG7', 'ÍNDICE');
//        $active_sheet->getStyle('BG7')->getFont()->setSize(11);
//        $active_sheet->getStyle('BG7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('BH6:BK6');
//        $active_sheet->setCellValue('BH6', 'JULIO');
//        $active_sheet->getStyle('BH6')->getFont()->setSize(11);
//        $active_sheet->getStyle('BH6')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('BH7:BI7');
//        $active_sheet->setCellValue('BH7', 'NIVEL DE ACTIVIDAD');
//        $active_sheet->getStyle('BH7')->getFont()->setSize(11);
//        $active_sheet->getStyle('BH7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->setCellValue('BJ7', 'CONSUMO');
//        $active_sheet->getStyle('BJ7')->getFont()->setSize(11);
//        $active_sheet->getStyle('BJ7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->setCellValue('BK7', 'ÍNDICE');
//        $active_sheet->getStyle('BK7')->getFont()->setSize(11);
//        $active_sheet->getStyle('BK7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('BL6:BO6');
//        $active_sheet->setCellValue('BL6', 'AGOSTO');
//        $active_sheet->getStyle('BL6')->getFont()->setSize(11);
//        $active_sheet->getStyle('BL6')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('BL7:BM7');
//        $active_sheet->setCellValue('BL7', 'NIVEL DE ACTIVIDAD');
//        $active_sheet->getStyle('BL7')->getFont()->setSize(11);
//        $active_sheet->getStyle('BL7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->setCellValue('BN7', 'CONSUMO');
//        $active_sheet->getStyle('BN7')->getFont()->setSize(11);
//        $active_sheet->getStyle('BN7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->setCellValue('BO7', 'ÍNDICE');
//        $active_sheet->getStyle('BO7')->getFont()->setSize(11);
//        $active_sheet->getStyle('BO7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('BP6:BS6');
//        $active_sheet->setCellValue('BP6', 'SEPTIEMBRE');
//        $active_sheet->getStyle('BP6')->getFont()->setSize(11);
//        $active_sheet->getStyle('BP6')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('BP7:BQ7');
//        $active_sheet->setCellValue('BP7', 'NIVEL DE ACTIVIDAD');
//        $active_sheet->getStyle('BP7')->getFont()->setSize(11);
//        $active_sheet->getStyle('BP7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->setCellValue('BR7', 'CONSUMO');
//        $active_sheet->getStyle('BR7')->getFont()->setSize(11);
//        $active_sheet->getStyle('BR7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->setCellValue('BS7', 'ÍNDICE');
//        $active_sheet->getStyle('BS7')->getFont()->setSize(11);
//        $active_sheet->getStyle('BS7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('BT6:BW6');
//        $active_sheet->setCellValue('BT6', 'OCTUBRE');
//        $active_sheet->getStyle('BT6')->getFont()->setSize(11);
//        $active_sheet->getStyle('BT6')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('BT7:BU7');
//        $active_sheet->setCellValue('BT7', 'NIVEL DE ACTIVIDAD');
//        $active_sheet->getStyle('BT7')->getFont()->setSize(11);
//        $active_sheet->getStyle('BT7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->setCellValue('BV7', 'CONSUMO');
//        $active_sheet->getStyle('BV7')->getFont()->setSize(11);
//        $active_sheet->getStyle('BV7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->setCellValue('BW7', 'ÍNDICE');
//        $active_sheet->getStyle('BW7')->getFont()->setSize(11);
//        $active_sheet->getStyle('BW7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('BX6:CA6');
//        $active_sheet->setCellValue('BX6', 'NOVIEMBRE');
//        $active_sheet->getStyle('BX6')->getFont()->setSize(11);
//        $active_sheet->getStyle('BX6')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('BX7:BY7');
//        $active_sheet->setCellValue('BX7', 'NIVEL DE ACTIVIDAD');
//        $active_sheet->getStyle('BX7')->getFont()->setSize(11);
//        $active_sheet->getStyle('BX7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->setCellValue('BZ7', 'CONSUMO');
//        $active_sheet->getStyle('BZ7')->getFont()->setSize(11);
//        $active_sheet->getStyle('BZ7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->setCellValue('CA7', 'ÍNDICE');
//        $active_sheet->getStyle('CA7')->getFont()->setSize(11);
//        $active_sheet->getStyle('CA7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('CB6:CE6');
//        $active_sheet->setCellValue('CB6', 'DICIEMBRE');
//        $active_sheet->getStyle('CB6')->getFont()->setSize(11);
//        $active_sheet->getStyle('CB6')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('CB7:CC7');
//        $active_sheet->setCellValue('CB7', 'NIVEL DE ACTIVIDAD');
//        $active_sheet->getStyle('CB7')->getFont()->setSize(11);
//        $active_sheet->getStyle('CB7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->setCellValue('CD7', 'CONSUMO');
//        $active_sheet->getStyle('CD7')->getFont()->setSize(11);
//        $active_sheet->getStyle('CD7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->setCellValue('CE7', 'ÍNDICE');
//        $active_sheet->getStyle('CE7')->getFont()->setSize(11);
//        $active_sheet->getStyle('CE7')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $active_sheet->mergeCells('CF6:CG7');
//        $active_sheet->setCellValue('CF6', 'CÓDIGO GAE');
//        $active_sheet->getStyle('CF6')->getFont()->setSize(11);
//        $active_sheet->getStyle('CF6')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//
//        $pos = 0;
//        for ($row = 0; $row < count($data); $row++) {
//            $pos = 8 + $row;
//            $real_indice = $data[$row]->real_nivel_act == 0 ? 0 : $data[$row]->real_consumo / $data[$row]->real_nivel_act;
//            $acumulado_indice = $data[$row]->acumulado_nivel_act == 0 ? 0 : $data[$row]->acumulado_consumo / $data[$row]->acumulado_nivel_act;
//            $estimado_indice = $data[$row]->estimado_nivel_act == 0 ? 0 : $data[$row]->estimado_consumo / $data[$row]->estimado_nivel_act;
//            $propuesta_indice = $data[$row]->propuesta_plan_nivel_act == 0 ? 0 : $data[$row]->propuesta_plan_consumo / $data[$row]->propuesta_plan_nivel_act;
//            $plan_final_indice = $data[$row]->plan_final_nivel_act == 0 ? 0 : $data[$row]->plan_final_consumo / $data[$row]->plan_final_nivel_act;
//
//            $enero_indice = $data[$row]->enero_nivel_act == 0 ? 0 : $data[$row]->enero_consumo / $data[$row]->enero_nivel_act;
//            $febrero_indice = $data[$row]->febrero_nivel_act == 0 ? 0 : $data[$row]->febrero_consumo / $data[$row]->febrero_nivel_act;
//            $marzo_indice = $data[$row]->marzo_nivel_act == 0 ? 0 : $data[$row]->marzo_consumo / $data[$row]->marzo_nivel_act;
//            $abril_indice = $data[$row]->abril_nivel_act == 0 ? 0 : $data[$row]->abril_consumo / $data[$row]->abril_nivel_act;
//            $mayo_indice = $data[$row]->mayo_nivel_act == 0 ? 0 : $data[$row]->mayo_consumo / $data[$row]->mayo_nivel_act;
//            $junio_indice = $data[$row]->junio_nivel_act == 0 ? 0 : $data[$row]->junio_consumo / $data[$row]->junio_nivel_act;
//            $julio_indice = $data[$row]->julio_nivel_act == 0 ? 0 : $data[$row]->julio_consumo / $data[$row]->julio_nivel_act;
//            $agosto_indice = $data[$row]->agosto_nivel_act == 0 ? 0 : $data[$row]->agosto_consumo / $data[$row]->agosto_nivel_act;
//            $septiembre_indice = $data[$row]->septiembre_nivel_act == 0 ? 0 : $data[$row]->septiembre_consumo / $data[$row]->septiembre_nivel_act;
//            $octubre_indice = $data[$row]->octubre_nivel_act == 0 ? 0 : $data[$row]->octubre_consumo / $data[$row]->octubre_nivel_act;
//            $noviembre_indice = $data[$row]->noviembre_nivel_act == 0 ? 0 : $data[$row]->noviembre_consumo / $data[$row]->noviembre_nivel_act;
//            $diciembre_indice = $data[$row]->diciembre_nivel_act == 0 ? 0 : $data[$row]->diciembre_consumo / $data[$row]->diciembre_nivel_act;
//
//            $nivelactiv = $data[$row]->enero_nivel_act + $data[$row]->febrero_nivel_act + $data[$row]->marzo_nivel_act + $data[$row]->abril_nivel_act + $data[$row]->mayo_nivel_act
//                + $data[$row]->junio_nivel_act + $data[$row]->julio_nivel_act + $data[$row]->agosto_nivel_act + $data[$row]->septiembre_nivel_act + $data[$row]->octubre_nivel_act
//                + $data[$row]->noviembre_nivel_act + $data[$row]->diciembre_nivel_act;
//
//            $consumo = $data[$row]->enero_consumo + $data[$row]->febrero_consumo + $data[$row]->marzo_consumo + $data[$row]->abril_consumo + $data[$row]->mayo_consumo
//                + $data[$row]->junio_consumo + $data[$row]->julio_consumo + $data[$row]->agosto_consumo + $data[$row]->septiembre_consumo + $data[$row]->octubre_consumo
//                + $data[$row]->noviembre_consumo + $data[$row]->diciembre_consumo;
//
//            $indice = $enero_indice + $febrero_indice + $marzo_indice + $abril_indice + $mayo_indice + $junio_indice + $julio_indice + $agosto_indice + $septiembre_indice + $octubre_indice
//                + $noviembre_indice + $diciembre_indice;
//
//            $active_sheet->setCellValue('B' . $pos, $data[$row]->codigo_mep_act);
//            $active_sheet->getStyle('B' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('B' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->mergeCells('C' . $pos . ':F' . $pos);
//            $active_sheet->setCellValue('C' . $pos, $data[$row]->actividad_nombre);
//            $active_sheet->getStyle('C' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('C' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//            $active_sheet->mergeCells('G' . $pos . ':H' . $pos);
//            $active_sheet->setCellValue('G' . $pos, $data[$row]->actividad_um);
//            $active_sheet->getStyle('G' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('G' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->mergeCells('I' . $pos . ':J' . $pos);
//            $active_sheet->setCellValue('I' . $pos, $data[$row]->real_nivel_act);
//            $active_sheet->getStyle('I' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('I' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->setCellValue('K' . $pos, $data[$row]->real_consumo);
//            $active_sheet->getStyle('K' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('K' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->setCellValue('L' . $pos, $real_indice);
//            $active_sheet->getStyle('L' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('L' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->mergeCells('M' . $pos . ':N' . $pos);
//            $active_sheet->setCellValue('M' . $pos, $data[$row]->acumulado_nivel_act);
//            $active_sheet->getStyle('M' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('M' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->setCellValue('O' . $pos, $data[$row]->acumulado_consumo);
//            $active_sheet->getStyle('O' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('O' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->setCellValue('P' . $pos, $acumulado_indice);
//            $active_sheet->getStyle('P' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('P' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->mergeCells('Q' . $pos . ':R' . $pos);
//            $active_sheet->setCellValue('Q' . $pos, $data[$row]->estimado_nivel_act);
//            $active_sheet->getStyle('Q' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('Q' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->setCellValue('S' . $pos, $data[$row]->estimado_consumo);
//            $active_sheet->getStyle('S' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('S' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->setCellValue('T' . $pos, $estimado_indice);
//            $active_sheet->getStyle('T' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('T' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->mergeCells('U' . $pos . ':V' . $pos);
//            $active_sheet->setCellValue('U' . $pos, $data[$row]->propuesta_plan_nivel_act);
//            $active_sheet->getStyle('U' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('U' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->setCellValue('W' . $pos, $data[$row]->propuesta_plan_consumo);
//            $active_sheet->getStyle('W' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('W' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->setCellValue('X' . $pos, $propuesta_indice);
//            $active_sheet->getStyle('X' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('X' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->mergeCells('Y' . $pos . ':AA' . $pos);
//            $active_sheet->setCellValue('Y' . $pos, $data[$row]->propuesta_plan_consumo - $data[$row]->plan_final_consumo);
//            $active_sheet->getStyle('Y' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('Y' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->mergeCells('AB' . $pos . ':AC' . $pos);
//            $active_sheet->setCellValue('AB' . $pos, $data[$row]->plan_final_nivel_act);
//            $active_sheet->getStyle('AB' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('AB' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->setCellValue('AD' . $pos, $data[$row]->plan_final_consumo);
//            $active_sheet->getStyle('AD' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('AD' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->setCellValue('AE' . $pos, $plan_final_indice);
//            $active_sheet->getStyle('AE' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('AE' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->mergeCells('AF' . $pos . ':AG' . $pos);
//            $active_sheet->setCellValue('AF' . $pos, $nivelactiv);
//            $active_sheet->getStyle('AF' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('AF' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->setCellValue('AH' . $pos, $consumo);
//            $active_sheet->getStyle('AH' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('AH' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->setCellValue('AI' . $pos, $indice);
//            $active_sheet->getStyle('AI' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('AI' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->mergeCells('AJ' . $pos . ':AK' . $pos);
//            $active_sheet->setCellValue('AJ' . $pos, $data[$row]->enero_nivel_act);
//            $active_sheet->getStyle('AJ' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('AJ' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->setCellValue('AL' . $pos, $data[$row]->enero_consumo);
//            $active_sheet->getStyle('AL' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('AL' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->setCellValue('AM' . $pos, round($enero_indice, 3));
//            $active_sheet->getStyle('AM' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('AM' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->mergeCells('AN' . $pos . ':AO' . $pos);
//            $active_sheet->setCellValue('AN' . $pos, $data[$row]->febrero_nivel_act);
//            $active_sheet->getStyle('AN' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('AN' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->setCellValue('AP' . $pos, $data[$row]->febrero_consumo);
//            $active_sheet->getStyle('AP' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('AP' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->setCellValue('AQ' . $pos, round($febrero_indice, 3));
//            $active_sheet->getStyle('AQ' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('AQ' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->mergeCells('AR' . $pos . ':AS' . $pos);
//            $active_sheet->setCellValue('AR' . $pos, $data[$row]->marzo_nivel_act);
//            $active_sheet->getStyle('AR' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('AR' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->setCellValue('AT' . $pos, $data[$row]->marzo_consumo);
//            $active_sheet->getStyle('AT' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('AT' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->setCellValue('AU' . $pos, round($marzo_indice, 3));
//            $active_sheet->getStyle('AU' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('AU' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->mergeCells('AV' . $pos . ':AW' . $pos);
//            $active_sheet->setCellValue('AV' . $pos, $data[$row]->abril_nivel_act);
//            $active_sheet->getStyle('AV' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('AV' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->setCellValue('AX' . $pos, $data[$row]->abril_consumo);
//            $active_sheet->getStyle('AX' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('AX' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->setCellValue('AY' . $pos, round($abril_indice, 3));
//            $active_sheet->getStyle('AY' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('AY' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->mergeCells('AZ' . $pos . ':BA' . $pos);
//            $active_sheet->setCellValue('AZ' . $pos, $data[$row]->mayo_nivel_act);
//            $active_sheet->getStyle('AZ' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('AZ' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->setCellValue('BB' . $pos, $data[$row]->mayo_consumo);
//            $active_sheet->getStyle('BB' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('BB' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->setCellValue('BC' . $pos, round($mayo_indice, 3));
//            $active_sheet->getStyle('BC' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('BC' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->mergeCells('BD' . $pos . ':BE' . $pos);
//            $active_sheet->setCellValue('BD' . $pos, $data[$row]->junio_nivel_act);
//            $active_sheet->getStyle('BD' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('BD' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->setCellValue('BF' . $pos, $data[$row]->junio_consumo);
//            $active_sheet->getStyle('BF' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('BF' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->setCellValue('BG' . $pos, round($junio_indice, 3));
//            $active_sheet->getStyle('BG' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('BG' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->mergeCells('BH' . $pos . ':BI' . $pos);
//            $active_sheet->setCellValue('BH' . $pos, $data[$row]->julio_nivel_act);
//            $active_sheet->getStyle('BH' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('BH' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->setCellValue('BJ' . $pos, $data[$row]->julio_consumo);
//            $active_sheet->getStyle('BJ' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('BJ' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->setCellValue('BK' . $pos, round($julio_indice, 3));
//            $active_sheet->getStyle('BK' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('BK' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->mergeCells('BL' . $pos . ':BM' . $pos);
//            $active_sheet->setCellValue('BL' . $pos, $data[$row]->agosto_nivel_act);
//            $active_sheet->getStyle('BL' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('BL' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->setCellValue('BN' . $pos, $data[$row]->agosto_consumo);
//            $active_sheet->getStyle('BN' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('BN' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->setCellValue('BO' . $pos, round($agosto_indice, 3));
//            $active_sheet->getStyle('BO' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('BO' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->mergeCells('BP' . $pos . ':BQ' . $pos);
//            $active_sheet->setCellValue('BP' . $pos, $data[$row]->septiembre_nivel_act);
//            $active_sheet->getStyle('BP' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('BP' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->setCellValue('BR' . $pos, $data[$row]->septiembre_consumo);
//            $active_sheet->getStyle('BR' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('BR' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->setCellValue('BS' . $pos, round($septiembre_indice, 3));
//            $active_sheet->getStyle('BS' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('BS' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->mergeCells('BT' . $pos . ':BU' . $pos);
//            $active_sheet->setCellValue('BT' . $pos, $data[$row]->octubre_nivel_act);
//            $active_sheet->getStyle('BT' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('BT' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->setCellValue('BV' . $pos, $data[$row]->octubre_consumo);
//            $active_sheet->getStyle('BV' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('BV' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->setCellValue('BW' . $pos, round($octubre_indice, 3));
//            $active_sheet->getStyle('BW' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('BW' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->mergeCells('BX' . $pos . ':BY' . $pos);
//            $active_sheet->setCellValue('BX' . $pos, $data[$row]->noviembre_nivel_act);
//            $active_sheet->getStyle('BX' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('BX' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->setCellValue('BZ' . $pos, $data[$row]->noviembre_consumo);
//            $active_sheet->getStyle('BZ' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('BZ' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->setCellValue('CA' . $pos, round($noviembre_indice, 3));
//            $active_sheet->getStyle('CA' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('CA' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->mergeCells('CB' . $pos . ':CC' . $pos);
//            $active_sheet->setCellValue('CB' . $pos, $data[$row]->diciembre_nivel_act);
//            $active_sheet->getStyle('CB' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('CB' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->setCellValue('CD' . $pos, $data[$row]->diciembre_consumo);
//            $active_sheet->getStyle('CD' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('CD' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->setCellValue('CE' . $pos, round($diciembre_indice, 3));
//            $active_sheet->getStyle('CE' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('CE' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $active_sheet->mergeCells('CF' . $pos . ':CG' . $pos);
//            $active_sheet->setCellValue('CF' . $pos, $data[$row]->codigo_gae_act);
//            $active_sheet->getStyle('CF' . $pos)->getFont()->setSize(11);
//            $active_sheet->getStyle('CF' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        }
//        $pos++;
//        $active_sheet->getSheetView()->setZoomScale(100);
//        return Util::setExcelStreamedResponseHeaders($phpexcel->createStreamedResponse($phpexcel->createWriter($excel_object)), 'CDA 001' . '.xls');
//
//    }

}