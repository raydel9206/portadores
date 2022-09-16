<?php
/**
 * Created by PhpStorm.
 * User: rherrerag
 * Date: 26/02/2018
 * Time: 15:13
 */

namespace Geocuba\PortadoresBundle\Controller;

use Doctrine\Common\Util\Debug;
use Exception;
use Geocuba\PortadoresBundle\Entity\Modelo5073;
use Geocuba\PortadoresBundle\Entity\RegistroOperacionGrupoElectrogeno;
use Geocuba\PortadoresBundle\Util\FechaUtil;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Geocuba\PortadoresBundle\Util\Utiles;
use Geocuba\PortadoresBundle\Util\Datos;

class Modelo5073Controller extends Controller
{

    use ViewActionTrait;

    private $consumo_diesel_transp = 0;

    /**
     * @param Request $request
     * @return array|JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $action = $request->get('accion');
        $unidadid = $request->get('unidadid');
        $anno = $request->get('anno');
        $mes = $request->get('mes');

        $modelo = $em->getRepository('PortadoresBundle:Modelo5073')->buscarModelo5073($anno, $mes, $unidadid);

        $_data = array();

        /** @var Modelo5073 $entity */
        foreach ($modelo as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'unidadid' => $entity->getUnidadid()->getId(),
                'unidad_nombre' => $entity->getUnidadid()->getNombre(),
                'productos_id' => $entity->getProducto()->getId(),
                'producto' => $entity->getProducto()->getNombre(),
                'fila' => $entity->getProducto()->getFila(),
                'um' => $entity->getProducto()->getUM()->getNombre(),
                'inv_inicial' => $entity->getInventarioInicialFisico(),
                'inv_inicial_original' => $entity->getInventarioInicialFisico(),
                'compras_cupet' => $entity->getComprasCupet(),
                'otras_entradas' => $entity->getOtrasEntradas(),
                'consumo_directo' => $entity->getConsumoDirecto(),
                'consumo_indirecto' => $entity->getConsumoIndirecto(),
                'otras_salidas' => $entity->getOtrasSalidas(),
                'inv_final' => $entity->getInventarioFinalFisico(),
                'asignado_mes' => $entity->getAsignadoMes(),
                'efectua_carga' => $entity->getRecibidoEfectuaCargar(),
                'consumo' => $entity->getConsumo(),
                'entregados_consumo' => $entity->getEntregaConsumo(),
                'saldo_final_total' => $entity->getSaldoFinalTotal(),
                'proximo_mes' => $entity->getSaldoFinalUtilizarProximoMes(),
                'disponible_fincimex' => $entity->getSaldoFinalDisponibleFincimex(),
                'ca_real' => $entity->getAcumuladoReal(),
                'ca_ano_anterior' => $entity->getAcumuladoAnnoAnterior()
            );
        }

        if ($action) {
            return $_data;
        }

        return new JsonResponse(array('rows' => $_data, 'total' => count($_data)));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function generarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $this->consumo_diesel_transp = 0;

        try {
            $em->transactional(function ($em) use ($request) {
                $conn = $this->get('database_connection');
                $unidadid = $request->get('unidadid');
                $anno = $request->get('anno');
                $mes = $request->get('mes');

                if ($mes == 1) {
                    $anno_anterior = $anno - 1;
                    $mes_anterior = 12;
                } else {
                    $anno_anterior = $anno;
                    $mes_anterior = $mes - 1;
                }

                if ($mes == 12) {
                    $anno_posterior = $anno + 1;
                    $mes_posterior = 1;
                } else {
                    $anno_posterior = $anno;
                    $mes_posterior = $mes + 1;
                }

                $unidad = $em->getRepository('PortadoresBundle:Unidad')->find($unidadid);

                $_unidades = [];
                Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($unidadid), $_unidades);

                $unidades_string = $this->unidadesToString($_unidades);

                $modelos = $em->getRepository('PortadoresBundle:Modelo5073')->findBy(array('mes' => $mes, 'anno' => $anno, 'unidadid' => $unidad));
                foreach ($modelos as $modelo) {
                    try {
                        $em->remove($modelo);
                        $em->flush();

                    } catch (\Exception $ex) {
                        if ($ex instanceof HttpException) {
                            return new JsonResponse(['success' => false, 'message' => $ex->getMessage()]);
                        } else {
                            /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
                            throw new HttpException(\Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR, $ex->getMessage(), $ex);
                        }
                    }
                }

                $productos = $em->getRepository('PortadoresBundle:Producto')->findBy(array(
                    'enblanco' => false,
                    'visible' => true
                ), array('fila' => 'ASC'));

                /*Obtener los tipos de combustible que se tienen registrado*/
                $tipo_combustibles = $conn->fetchAll("SELECT fila FROM nomencladores.tipo_combustible 
                where visible=true");

                $filas = array();
                foreach ($tipo_combustibles as $tipo_combustible)
                    $filas[] = $tipo_combustible['fila'];

                if (!in_array(370, $filas)) $filas[] = '370';
                if (!in_array(360, $filas)) $filas[] = '360';

                foreach ($productos as $producto) {
                    $modelo_anterior = $em->getRepository('PortadoresBundle:Modelo5073')->findOneBy(array('mes' => $mes_anterior, 'anno' => $anno_anterior, 'producto' => $producto->getId(), 'unidadid' => $unidadid));
                    $modelo_anno_anterior = $em->getRepository('PortadoresBundle:Modelo5073')->findOneBy(array('mes' => $mes, 'anno' => $anno - 1, 'producto' => $producto->getId(), 'unidadid' => $unidadid));

                    //Tipos de Combustible
                    if (in_array($producto->getFila(), $filas)) {
                        if($producto->getFila() == 370 || $producto->getFila() == 360) {
                            $tipo_combustible = $em->getRepository('PortadoresBundle:TipoCombustible')->findOneBy([
                                'fila' => 370,
                                'visible' => true
                            ]);
                            if (!$tipo_combustible) {
                                $tipo_combustible = $em->getRepository('PortadoresBundle:TipoCombustible')->findOneBy([
                                    'fila' => 360,
                                    'visible' => true
                                ]);
                            }
                        }
                        else {
                            $tipo_combustible = $em->getRepository('PortadoresBundle:TipoCombustible')->findOneBy(array(
                                'fila' => $producto->getFila(),
                                'visible' => true
                            ));
                        }

                        $id_tc = $tipo_combustible->getId();

                        //Asignado en el mes
                        $asignaciones = $conn->fetchAll("SELECT sum(cantidad) as cantidad FROM datos.asignacion where mes = '$mes' and anno = '$anno' and tipo_combustible='$id_tc'  and unidad in ($unidades_string) and visible=true group by tipo_combustible");

                        $total_asignado = !isset($asignaciones[0]['cantidad'])?0:$asignaciones[0]['cantidad'];

                        //Total Abastecido Transporte
                        $abastecido = $conn->fetchAll("select sum(ht.salida_cantidad) as suma_comb from datos.historial_tarjeta ht join nomencladores.tarjeta t on ht.tarjetaid = t.id where ht.mes<='$mes' and ht.anno='$anno' and ht.liquidacionid notnull and nunidadid in ($unidades_string) and t.ntipo_combustibleid = '$id_tc' group by anno,mes limit 1");

//                        $consumo_transporte = 0;
                        $consumo_transporte = !isset($abastecido[0]['suma_comb']) ? 0: $abastecido[0]['suma_comb'];

                        $consumo_diesel_transp = 0;
                        if($tipo_combustible->getPortadorid()->getNombre()=='DIESEL'){
                            $this->consumo_diesel_transp += $consumo_transporte;
                        }

                        //Total $consumo (Adicionar Tiro Directo)(OJO, no)
                         $consumo = $consumo_transporte;

                        //Disponible_Fincimex
                        $disponible_fincimex = Datos::getPlanDisponibleFincimex($em, $unidad, $tipo_combustible, null);

                        //Obtener Saldo Fincimex
                        $saldo_fincimex = Datos::getSaldoDisponibleFincimex($em, $unidad, $tipo_combustible,null);

                        //Obtener Saldo caja
                        $saldo_caja = Datos::getSaldoCaja($em, $unidad, $tipo_combustible, null);

                        //Para utilizar en el próximo mes: (columna 12)
                        //Cantidad de combustible físico, equivalente al que se reporta como saldo final total, pero que fue asignado para ser utilizado en el próximo mes de forma parcial o totalmente esté o no pagado.
                        $sum_asignaciones_proximo_mes = $em->getConnection()->fetchAll("SELECT sum(cantidad) as suma_asignaciones from datos.asignacion a 
                          where a.tipo_combustible = '$id_tc' and date_part('month',a.para_mes) = $mes_posterior and date_part('year',a.para_mes) = $anno_posterior  and unidad in ($unidades_string) GROUP BY a.tipo_combustible");
                        $utilizar_proximo_mes = !isset($sum_asignaciones_proximo_mes[0]['suma_asignaciones']) ? 0: $sum_asignaciones_proximo_mes[0]['suma_asignaciones'];

                        /*----Falta definir como es que se va a gestionar----*/
                        $entrega_consumo = 0;
                        $recibido_efectua_carga = 0;
                        $consumo_directo = 0;
                        $consumo_indirecto = 0;
                        /*----------------------------------------*/

                        // Consumo directo-------------------------------------------------------
                        $consumo_directo = 0;
                        $compras_cupet = 0;
                        $otras_entradas = 0;
                        $otras_salidas = 0;
                        if ($producto->getFila() == 360) {              // Consumo GLP volumen
                            $sqlGlpLitros = "select sum(consumo) as consumo
                                                from datos.mediciones_diarias_tanques as md
                                                join nomencladores.tanques as tq on tq.id = md.tanque_id                                                
                                                where tq.tipo_combustible_id = '$id_tc' 
                                                    and extract('month' from md.fecha) = $mes and extract('year' from md.fecha) = $anno
                                                    and tq.unidad_medida_id = 'static_measure_unit_1' and tq.unidad_id in ($unidades_string)";
                            $consumoDirecto = $conn->fetchAll($sqlGlpLitros);
                            if (count($consumoDirecto) > 0) $consumo_directo += $consumoDirecto[0]['consumo'];

                            $consumo_directo /= 1000;

                            // Compras Cupet
                            $sqlComprasCupet = "select sum(esc.cantidad) as cantidad 
                                                from datos.entradas_salidas_combustible as esc
                                                join nomencladores.tanques as tq on tq.id = esc.tanque_id
                                                where esc.tipo = 'compras_cupet' and tq.unidad_medida_id = 'static_measure_unit_1'
                                                and extract('month' from esc.fecha) = $mes and extract('year' from esc.fecha) = $anno  and tq.unidad_id in ($unidades_string)";

                            $comprasCupet = $conn->fetchAll($sqlComprasCupet);
                            if (count($comprasCupet) > 0) $compras_cupet += $comprasCupet[0]['cantidad'];

                            $compras_cupet /= 1000;

                            // Otras entradas
                            $sqlOtrasEntradas = "select sum(case when esc.cantidad > 0 then esc.cantidad else 0 end) as cantidad 
                                                from datos.entradas_salidas_combustible as esc
                                                join nomencladores.tanques as tq on tq.id = esc.tanque_id
                                                where esc.tipo = 'otros' and tq.unidad_medida_id = 'static_measure_unit_1'
                                                and extract('month' from esc.fecha) = $mes and extract('year' from esc.fecha) = $anno  and tq.unidad_id in ($unidades_string)";

                            $otrasEntradas = $conn->fetchAll($sqlOtrasEntradas);
                            if (count($otrasEntradas) > 0) $otras_entradas += $otrasEntradas[0]['cantidad'];

                            $otras_entradas /= 1000;

                            // Otras salidas
                            $sqlOtrasSalidas = "select sum(case when esc.cantidad < 0 then (esc.cantidad * -1) else 0 end) as cantidad 
                                                from datos.entradas_salidas_combustible as esc
                                                join nomencladores.tanques as tq on tq.id = esc.tanque_id
                                                where esc.tipo = 'otros' and tq.unidad_medida_id = 'static_measure_unit_1'
                                                and extract('month' from esc.fecha) = $mes and extract('year' from esc.fecha) = $anno  and tq.unidad_id in ($unidades_string)";

                            $otrasSalidas = $conn->fetchAll($sqlOtrasSalidas);
                            if (count($otrasSalidas) > 0) $otras_salidas += $otrasSalidas[0]['cantidad'];

                            $otras_salidas /= 1000;
                        }
                        elseif ($producto->getFila() == 370) {        // Consumo GLP masa
                            $sqlGlpKg = "select sum(consumo) as consumo
                                                from datos.mediciones_diarias_tanques as md
                                                join nomencladores.tanques as tq on tq.id = md.tanque_id
                                                where tq.tipo_combustible_id = '$id_tc' 
                                                    and extract('month' from md.fecha) = $mes and extract('year' from md.fecha) = $anno
                                                    and tq.unidad_medida_id = 'static_measure_unit_2' and tq.unidad_id in ($unidades_string)";
                            $consumoDirecto = $conn->fetchAll($sqlGlpKg);
                            if (count($consumoDirecto) > 0) $consumo_directo += $consumoDirecto[0]['consumo'];

                            // Compras Cupet
                            $sqlComprasCupet = "select sum(esc.cantidad) as cantidad 
                                                from datos.entradas_salidas_combustible as esc
                                                join nomencladores.tanques as tq on tq.id = esc.tanque_id
                                                where esc.tipo = 'compras_cupet' and tq.unidad_medida_id = 'static_measure_unit_2'
                                                and extract('month' from esc.fecha) = $mes and extract('year' from esc.fecha) = $anno and tq.unidad_id in ($unidades_string)";

                            $comprasCupet = $conn->fetchAll($sqlComprasCupet);
                            if (count($comprasCupet) > 0) $compras_cupet += $comprasCupet[0]['cantidad'];

                            // Otras entradas
                            $sqlOtrasEntradas = "select sum(case when esc.cantidad > 0 then esc.cantidad else 0 end) as cantidad 
                                                from datos.entradas_salidas_combustible as esc
                                                join nomencladores.tanques as tq on tq.id = esc.tanque_id
                                                where esc.tipo = 'otros' and tq.unidad_medida_id = 'static_measure_unit_2'
                                                and extract('month' from esc.fecha) = $mes and extract('year' from esc.fecha) = $anno and tq.unidad_id in ($unidades_string)";

                            $otrasEntradas = $conn->fetchAll($sqlOtrasEntradas);
                            if (count($otrasEntradas) > 0) $otras_entradas += $otrasEntradas[0]['cantidad'];

                            // Otras salidas
                            $sqlOtrasSalidas = "select sum(case when esc.cantidad < 0 then (esc.cantidad * -1) else 0 end) as cantidad 
                                                from datos.entradas_salidas_combustible as esc
                                                join nomencladores.tanques as tq on tq.id = esc.tanque_id
                                                where esc.tipo = 'otros' and tq.unidad_medida_id = 'static_measure_unit_2'
                                                and extract('month' from esc.fecha) = $mes and extract('year' from esc.fecha) = $anno and tq.unidad_id in ($unidades_string)";

                            $otrasSalidas = $conn->fetchAll($sqlOtrasSalidas);
                            if (count($otrasSalidas) > 0) $otras_salidas += $otrasSalidas[0]['cantidad'];
                        }
                        else {
                            $sqlConsumoDirecto = "select sum(ro.consumo_real) as consumo
                                                from datos.registro_operaciones as ro
                                                join nomencladores.equipos_tecnologicos as et on et.id = ro.equipo_tecnologico_id
                                                join nomencladores.tipo_combustible as tc on tc.id = et.tipo_combustible_id
                                            
                                                where tc.id = '$id_tc' and extract('month' from ro.fecha) = $mes and extract('year' from ro.fecha) = $anno
                                                group by tc.id";

                            $sqlConsumoDirectoRecirculacionCalderas = "select sum(roc.consumo_real_recirculacion) as consumo
                                                                    from datos.registro_operaciones as ro
                                                                    join datos.registro_operaciones_calderas as roc on roc.id = ro.id
                                                                    join nomencladores.equipos_tecnologicos as et on et.id = ro.equipo_tecnologico_id
                                                                    join nomencladores.calderas as ca on ca.id = et.id
                                                                    join nomencladores.tipo_combustible as tc on tc.id = ca.tipo_combustible_recirculacion_id
                                                                    
                                                                    where tc.id = '$id_tc' and extract('month' from ro.fecha) = $mes and extract('year' from ro.fecha) = $anno
                                                                    group by tc.id";

                            $consumoDirecto = $conn->fetchAll($sqlConsumoDirecto);
                            $consumoDirectoRecirculacionCalderas = $conn->fetchAll($sqlConsumoDirectoRecirculacionCalderas);
                            if (count($consumoDirecto) > 0) $consumo_directo += $consumoDirecto[0]['consumo'];
                            if (count($consumoDirectoRecirculacionCalderas) > 0) $consumo_directo += $consumoDirectoRecirculacionCalderas[0]['consumo'];

                            // Si no hay nada en los equipos revisa en los tanques
                            if ($consumo_directo == 0) {
                                $sqlConsumoDirecto = "select sum(consumo) as consumo
                                                        from datos.mediciones_diarias_tanques as md
                                                        join nomencladores.tanques as tq on tq.id = md.tanque_id
                                                        where tq.tipo_combustible_id = '$id_tc' 
                                                            and extract('month' from md.fecha) = $mes and extract('year' from md.fecha) = $anno
                                                        GROUP BY tq.tipo_combustible_id";
                                $consumoDirecto = $conn->fetchAll($sqlConsumoDirecto);
                                if (count($consumoDirecto) > 0) $consumo_directo += $consumoDirecto[0]['consumo'];
                            }

                            $consumo_directo /= 1000;

                            // Compras Cupet
                            $sqlComprasCupet = "select sum(esc.cantidad) as cantidad 
                                                from datos.entradas_salidas_combustible as esc
                                                join nomencladores.tanques as tq on tq.id = esc.tanque_id
                                                where tq.tipo_combustible_id = '$id_tc' and esc.tipo = 'compras_cupet'
                                                and extract('month' from esc.fecha) = $mes and extract('year' from esc.fecha) = $anno
                                                group by tq.tipo_combustible_id";

                            $comprasCupet = $conn->fetchAll($sqlComprasCupet);
                            if (count($comprasCupet) > 0) $compras_cupet += $comprasCupet[0]['cantidad'];

                            $compras_cupet /= 1000;

                            // Otras entradas
                            $sqlOtrasEntradas = "select sum(case when esc.cantidad > 0 then esc.cantidad else 0 end) as cantidad 
                                                from datos.entradas_salidas_combustible as esc
                                                join nomencladores.tanques as tq on tq.id = esc.tanque_id
                                                where tq.tipo_combustible_id = '$id_tc' and esc.tipo = 'otros'
                                                and extract('month' from esc.fecha) = $mes and extract('year' from esc.fecha) = $anno
                                                group by tq.tipo_combustible_id";

                            $otrasEntradas = $conn->fetchAll($sqlOtrasEntradas);
                            if (count($otrasEntradas) > 0) $otras_entradas += $otrasEntradas[0]['cantidad'];

                            $otras_entradas /= 1000;

                            // Otras salidas
                            $sqlOtrasSalidas = "select sum(case when esc.cantidad < 0 then (esc.cantidad * -1) else 0 end) as cantidad 
                                                from datos.entradas_salidas_combustible as esc
                                                join nomencladores.tanques as tq on tq.id = esc.tanque_id
                                                where tq.tipo_combustible_id = '$id_tc' and esc.tipo = 'otros'
                                                and extract('month' from esc.fecha) = $mes and extract('year' from esc.fecha) = $anno
                                                group by tq.tipo_combustible_id";

                            $otrasSalidas = $conn->fetchAll($sqlOtrasSalidas);
                            if (count($otrasSalidas) > 0) $otras_salidas += $otrasSalidas[0]['cantidad'];

                            $otras_salidas /= 1000;
                        }

                        $inventarioInicialFisico = $modelo_anterior ? $modelo_anterior->getInventarioFinalFisico() : 0;
                        $inventarioFinalFisico = $inventarioInicialFisico + $compras_cupet + $otras_entradas - $consumo_directo - $consumo_indirecto - $otras_salidas;
                        //---------------------------------------------------------------------------------------------------------------------------------------------------

//                        $consumo_acumulado = $consumo_transporte + $recibido_efectua_carga + $entrega_consumo + $consumo_directo + $consumo_indirecto;

                        $modelo = new Modelo5073();
                        $modelo->setMes($mes);
                        $modelo->setAnno($anno);
                        $modelo->setUnidadid($unidad);
                        $modelo->setProducto($producto);
                        $modelo->setInventarioInicialFisico($inventarioInicialFisico);
                        $modelo->setComprasCupet($compras_cupet);
                        $modelo->setOtrasEntradas($otras_entradas);
                        $modelo->setConsumoDirecto($consumo_directo);
                        $modelo->setConsumoIndirecto(null);
                        $modelo->setOtrasSalidas($otras_salidas);
                        $modelo->setInventarioFinalFisico($inventarioFinalFisico);
                        $modelo->setAsignadoMes($total_asignado / 1000);
                        $modelo->setRecibidoEfectuaCargar(null);
                        $modelo->setConsumo($consumo/1000);
                        $modelo->setEntregaConsumo(null);
                        $modelo->setSaldoFinalTotal(($disponible_fincimex + $saldo_caja + $saldo_fincimex)/1000);
                        $modelo->setSaldoFinalUtilizarProximoMes($utilizar_proximo_mes/1000);
                        $modelo->setSaldoFinalDisponibleFincimex($disponible_fincimex/1000);
                        $modelo->setAcumuladoReal($mes == 1 ? $consumo/1000 : $modelo_anterior ? $modelo_anterior->getAcumuladoReal()+$consumo/1000:0);
                        $modelo->setAcumuladoAnnoAnterior($modelo_anno_anterior ? $modelo_anno_anterior->getAcumuladoReal() : null );

                        //Electricidad

                    }
                    elseif($producto->getFila() == '950') {
                        $fecha_inicial = $anno . '-' . $mes . '-01';
                        $fecha_final = FechaUtil::getUltimoDiaMes($mes, $anno);

                        $servicios = $em->getRepository('PortadoresBundle:Servicio')->findByVisible(true);
                        $consumo_electricidad = 0;
                        foreach ($servicios as $service) {
                            if ($service->getServicioMayor()) {
                                $autolecturas = $em->getRepository('PortadoresBundle:AutolecturaTresescalas')->getAutolecturaTresescalas($service->getId(), $mes, $anno, $fecha_inicial);

                                foreach ($autolecturas as $auto) {
                                    // Verificar si hay que tomar en cuenta las perdidas de transformacion para el consumo diario de cada servicio
                                    $consumo_electricidad += $auto->getConsumoTotalDia();
                                }
                            } else {
                                $prepago = $em->getRepository('PortadoresBundle:Autolecturaprepago')->getAutolecturbyMesAbierto($service->getId(), $fecha_inicial, $fecha_final);

                                foreach ($prepago as $auto_prep) {
                                    //Lo propio
                                    $consumo_electricidad += $auto_prep->getConsumoTotalDia();
                                }
                            }
                        }

                        $acumulado_real = $mes == 1 ? $consumo_electricidad/1000 : ($modelo_anterior ? $modelo_anterior->getAcumuladoReal() + $consumo_electricidad : 0)/1000;

                        $modelo = new Modelo5073();
                        $modelo->setMes($mes);
                        $modelo->setAnno($anno);
                        $modelo->setUnidadid($unidad);
                        $modelo->setProducto($producto);
                        $modelo->setInventarioInicialFisico(null);
                        $modelo->setComprasCupet(null);
                        $modelo->setOtrasEntradas(null);
                        $modelo->setConsumoDirecto($consumo_electricidad/1000);
                        $modelo->setConsumoIndirecto(null);
                        $modelo->setOtrasSalidas(null);
                        $modelo->setInventarioFinalFisico(null);
                        $modelo->setAsignadoMes(null);
                        $modelo->setRecibidoEfectuaCargar(null);
                        $modelo->setConsumo(null);
                        $modelo->setEntregaConsumo(null);
                        $modelo->setSaldoFinalTotal(null);
                        $modelo->setSaldoFinalUtilizarProximoMes(null);
                        $modelo->setSaldoFinalDisponibleFincimex(null);
                        $modelo->setAcumuladoReal($acumulado_real);
                        $modelo->setAcumuladoAnnoAnterior( $modelo_anno_anterior ? $modelo_anno_anterior->getAcumuladoReal() : 0);
                    }
                    else {
                        $acumulado_real =
                            $mes != 1 ?
                                $modelo_anterior ?
                                    $producto->getFila() == 920 ? $modelo_anterior->getAcumuladoReal() + $this->consumo_diesel_transp/1000 : $modelo_anterior->getAcumuladoReal() :
                                null :
                            $this->consumo_diesel_transp/1000;
                        $acumulado_anno_anterior = $modelo_anno_anterior ? $modelo_anno_anterior->getAcumuladoAnnoAnterior() : null;

                        $consumo_directo = 0;
                        if ($producto->getFila() == 920) {      // Consumo total de diesel directo en transorte
                            $sqlConsumoTransporteDirecto = "select sum(esc.cantidad * -1) as cantidad 
                                                                from datos.entradas_salidas_combustible as esc
                                                                join nomencladores.tanques as tq on tq.id = esc.tanque_id
                                                                join nomencladores.tipo_combustible as tc on tc.id = tq.tipo_combustible_id
                                                                join nomencladores.portador as port on port.id = tc.portadorid
                                                                where port.nombre = 'DIESEL' and esc.tipo = 'transporte' and tq.unidad_id in ($unidades_string)";
                            $consumoTranporteDirecto = $conn->fetchAll($sqlConsumoTransporteDirecto);
                            if (count($consumoTranporteDirecto) > 0) $consumo_directo += $consumoTranporteDirecto[0]['cantidad'];

                            $consumo_directo /= 1000;
                            $acumulado_real += $consumo_directo;
                        }
                        elseif($producto->getFila() == 954) {
                            $acumulado_real = $conn->fetchAll("select count(*) 
																from nomencladores.grupos_electrogenos as ge 
																join nomencladores.equipos_tecnologicos as et on ge.id = et.id
																where et.unidad_id in ($unidades_string)")[0]['count'];
                        }
                        elseif($producto->getFila() == 960) {
                            $fechaDesde = $anno . '-' . $mes . '-' . '1';
                            $fechaHasta = FechaUtil::getUltimoDiaMes($mes, $anno);

                            $sqlOperaciones = "select sum(rog.energia_generada) as consumo
                                                from datos.registro_operaciones as ro
                                                join datos.registro_operaciones_grupos_electrogenos as rog on rog.id = ro.id
                                                join nomencladores.equipos_tecnologicos as et on et.id = ro.equipo_tecnologico_id
                                                where et.unidad_id in ($unidades_string) and rog.con_carga = true
                                                and extract('month' from ro.fecha) = $mes and extract('year' from ro.fecha) = $anno;";
                            $energiaGenerada = $conn->fetchAll($sqlOperaciones);

                            if (count($energiaGenerada) > 0) $consumo_directo += $energiaGenerada[0]['consumo'];

                            $consumo_directo /= 1000;
                            $acumulado_real += $consumo_directo;
                        }
                        elseif($producto->getFila() == 970) {
                            $fechaDesde = $anno . '-' . $mes . '-' . '1';
                            $fechaHasta = FechaUtil::getUltimoDiaMes($mes, $anno);

                            $sqlOperaciones = "select sum(ro.consumo_real) as consumo
                                                from datos.registro_operaciones as ro
                                                join datos.registro_operaciones_grupos_electrogenos as rog on rog.id = ro.id
                                                join nomencladores.equipos_tecnologicos as et on et.id = ro.equipo_tecnologico_id
                                                where et.unidad_id in ($unidades_string) and rog.con_carga = true
                                                and extract('month' from ro.fecha) = $mes and extract('year' from ro.fecha) = $anno;";
                            $energiaGenerada = $conn->fetchAll($sqlOperaciones);

                            if (count($energiaGenerada) > 0) $consumo_directo += $energiaGenerada[0]['consumo'];

                            $consumo_directo /= 1000;
                            $acumulado_real += $consumo_directo;
                        }
                        elseif($producto->getFila() == 950) {
                            $consumoTresescalas = $conn->fetchAll("select sum(autot.consumo_total_real) as consumo 
                                                                        from datos.autolectura_tresescalas as autot
                                                                        join datos.servicios as s on s.id = autot.serviciosid
                                                                        where s.nunidadid in ($unidades_string) and autot.mes = '$mes' and autot.anno = '$anno'");

                            $consumoPrepago = $conn->fetchAll("select sum(autop.consumo_total_real) as consumo 
                                                                    from datos.autolecturaprepago as autop
                                                                    join datos.servicios as s on s.id = autop.serviciosid
                                                                    where s.nunidadid in ($unidades_string) 
                                                                    and extract('month' from autop.fecha_lectura) = '$mes' and extract('year' from autop.fecha_lectura) = '$anno'");

                            if (count($consumoTresescalas) > 0) $consumo_directo += $consumoTresescalas[0]['consumo'];
                            if (count($consumoPrepago) > 0) $consumo_directo += $consumoPrepago [0]['consumo'];
                        }

                        $inventarioInicialFisico = $modelo_anterior ? $modelo_anterior->getInventarioFinalFisico() : 0;
                        $inventarioFinalFisico = $inventarioInicialFisico - $consumo_directo;

                        //Otros Productos que no se gestionan Sistema
                        $modelo = new Modelo5073();
                        $modelo->setMes($mes);
                        $modelo->setAnno($anno);
                        $modelo->setUnidadid($unidad);
                        $modelo->setProducto($producto);
                        $modelo->setInventarioInicialFisico($inventarioInicialFisico);
                        $modelo->setComprasCupet(null);
                        $modelo->setOtrasEntradas(null);
                        $modelo->setConsumoDirecto($consumo_directo);
                        $modelo->setConsumoIndirecto(null);
                        $modelo->setOtrasSalidas(null);
                        $modelo->setInventarioFinalFisico($inventarioFinalFisico);
                        $modelo->setAsignadoMes(null);
                        $modelo->setRecibidoEfectuaCargar(null);
                        $modelo->setConsumo($producto->getFila()==920?$this->consumo_diesel_transp/1000:null);
                        $modelo->setEntregaConsumo(null);
                        $modelo->setSaldoFinalTotal(null);
                        $modelo->setSaldoFinalUtilizarProximoMes(null);
                        $modelo->setSaldoFinalDisponibleFincimex(null);
                        $modelo->setAcumuladoReal($acumulado_real);
                        $modelo->setAcumuladoAnnoAnterior( $acumulado_anno_anterior);
                    }

                    try {
                        $em->persist($modelo);
                        $em->flush();

                    } catch (\Exception $ex) {

                        if ($ex instanceof HttpException) {
                            return new JsonResponse(['success' => false, 'message' => $ex->getMessage()]);
                        }

                        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
                        throw new HttpException(\Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR, $ex->getMessage(), $ex);
                    }
                }
            });

        } catch (Exception $e) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => $e->getMessage()));
        }


        return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Modelo 5073 generado con éxito.'));

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function guardarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $datos = json_decode($request->get('store'));
        for ($i = 0, $iMax = count($datos); $i < $iMax; $i++) {
            $entity = $em->getRepository('PortadoresBundle:Modelo5073')->find($datos[$i]->id);

            $entity->setInventarioInicialFisico($datos[$i]->inv_inicial!=0?$datos[$i]->inv_inicial:null);
            $entity->setComprasCupet($datos[$i]->compras_cupet!=0?$datos[$i]->compras_cupet:null);
            $entity->setOtrasEntradas($datos[$i]->otras_entradas!=0?$datos[$i]->otras_entradas:null);
            $entity->setConsumoDirecto($datos[$i]->consumo_directo!=0?$datos[$i]->consumo_directo:null);
            $entity->setConsumoIndirecto($datos[$i]->consumo_indirecto!=0?$datos[$i]->consumo_indirecto:null);
            $entity->setOtrasSalidas($datos[$i]->otras_salidas!=0?$datos[$i]->otras_salidas:null);
            $entity->setInventarioFinalFisico($datos[$i]->inv_final!=0?$datos[$i]->inv_final:null);
            $entity->setAsignadoMes($datos[$i]->asignado_mes!=0?$datos[$i]->asignado_mes:null);
            $entity->setRecibidoEfectuaCargar($datos[$i]->efectua_carga!=0?$datos[$i]->efectua_carga:null);
            $entity->setConsumo($datos[$i]->consumo!=0?$datos[$i]->consumo:null);
            $entity->setEntregaConsumo($datos[$i]->entregados_consumo!=0?$datos[$i]->entregados_consumo:null);
            $entity->setSaldoFinalTotal($datos[$i]->saldo_final_total!=0?$datos[$i]->saldo_final_total:null);
            $entity->setSaldoFinalUtilizarProximoMes($datos[$i]->proximo_mes!=0?$datos[$i]->proximo_mes:null);
            $entity->setAcumuladoReal($datos[$i]->ca_real!=0?$datos[$i]->ca_real:null);
            $entity->setAcumuladoAnnoAnterior($datos[$i]->ca_ano_anterior!=0?$datos[$i]->ca_ano_anterior:null);

            try {
                $em->persist($entity);
                $em->flush();

            } catch (\Exception $ex) {
                if ($ex instanceof HttpException) {
                    return new JsonResponse(['success' => false, 'message' => $ex->getMessage()]);
                } else {
                    /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
                    throw new HttpException(\Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR, $ex->getMessage(), $ex);
                }
            }

        }

        $response = new JsonResponse();
        $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Cambios al modelo 5073 Guardados Correctamente.'));
        return $response;

    }

    /**
     * @param Request $request
     * @return Response
     */
    public function printAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $datos = $this->loadAction($request);


        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'ONEI');
        $sheet->setCellValue('A3', 'Oficina Nacional de Estadística e Información ');
        $sheet->getStyle('A1:A3')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_JUSTIFY);
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getStyle('A1')->getFont()->setBold(true);

        $sheet->setCellValue('B1', 'Sistema de Información de Estadísticas Nacional (SIEN)');
        $sheet->mergeCells('B1:C3');
        $sheet->getStyle('B1:C3')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_JUSTIFY);
        $sheet->getStyle('B1:C3')->getAlignment()->setWrapText(true);

        $sheet->mergeCells('D1:D3');

        $sheet->setCellValue('E1', 'BALANCE DE CONSUMO DE PORTADORES ENERGETICOS ');
        $sheet->setCellValue('E2', '1__Consumidor 2__Servicentro');
        $sheet->getStyle('E1:J3')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getStyle('E1:J1')->getAlignment()->setWrapText(true);
        $sheet->getStyle('E2:J2')->getAlignment()->setWrapText(true);

        $sheet->setCellValue('A4', 'Centro Informante:');


        $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000'],
                ],
            ],

        ];
        $sheet->getStyle('A1:A3')->applyFromArray($styleArray);
        $sheet->getStyle('B1:C3')->applyFromArray($styleArray);
        $sheet->getStyle('D1:D3')->applyFromArray($styleArray);
        $sheet->getStyle('E1:J3')->applyFromArray($styleArray);
        $sheet->getStyle('K1:N3')->applyFromArray($styleArray);
        $sheet->getStyle('O1:Q3')->applyFromArray($styleArray);
        $sheet->getStyle('R1:S3')->applyFromArray($styleArray);
        $sheet->getStyle('A4:M4')->applyFromArray($styleArray);
        $sheet->getStyle('N4:S4')->applyFromArray($styleArray);
        $sheet->getStyle('A5:S5')->applyFromArray($styleArray);

        $writer = new Xls($spreadsheet);

        $response = new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            }, \Symfony\Component\HttpFoundation\Response::HTTP_OK, ['Content-Type' => 'application/vnd.ms-excel', 'Pragma' => 'public', 'Cache-Control' => 'maxage=0']);


        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'Consolidado mes.xls'));
        return $response;

    }

    /**
     * @param Request $request
     * @return Response
     */
    public function exportAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $anno = $request->get('anno');
        $mes = $request->get('mes');

        $unidad = $em->getRepository('PortadoresBundle:Unidad')->find($request->get('unidadid'));

        $summary = json_decode($request->get('summary'));

        $datos = $this->loadAction($request);

        $_html = "<html xmlns:v=\"urn:schemas-microsoft-com:vml\"
xmlns:o=\"urn:schemas-microsoft-com:office:office\"
xmlns:x=\"urn:schemas-microsoft-com:office:excel\"
xmlns=\"http://www.w3.org/TR/REC-html40\">

<head>
<meta http-equiv=Content-Type content=\"text/html; charset=utf-8\">
<meta name=ProgId content=Excel.Sheet>
<meta name=Generator content=\"Microsoft Excel 15\">
<link rel=File-List href=\"Modelos%205073%20Año-2018_files/filelist.xml\">
<!--[if !mso]>
<style>
v\:* {behavior:url(#default#VML);}
o\:* {behavior:url(#default#VML);}
x\:* {behavior:url(#default#VML);}
.shape {behavior:url(#default#VML);}
</style>
<![endif]-->
<style id=\"Modelos 5073 Año-2018_7414_Styles\">
<!--table
	{mso-displayed-decimal-separator:\"\,\";
	mso-displayed-thousand-separator:\"\.\";}
.font57414
	{color:black;
	font-size:9.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;}
.font67414
	{color:black;
	font-size:9.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Tahoma, sans-serif;
	mso-font-charset:0;}
.font77414
	{color:black;
	font-size:9.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Tahoma, sans-serif;
	mso-font-charset:0;}
.xl157414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial;
	mso-generic-font-family:auto;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl637414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl647414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
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
	white-space:normal;}
.xl657414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl667414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl677414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl687414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:\"\@\";
	text-align:center;
	vertical-align:bottom;
	border:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl697414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:\"\@\";
	text-align:center;
	vertical-align:bottom;
	border-top:none;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl707414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:Fixed;
	text-align:center;
	vertical-align:bottom;
	border-top:none;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl717414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:Fixed;
	text-align:center;
	vertical-align:bottom;
	border-top:1.0pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl727414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:Fixed;
	text-align:center;
	vertical-align:bottom;
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl737414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:\"\@\";
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl747414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:Fixed;
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
.xl757414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:Fixed;
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl767414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:Fixed;
	text-align:general;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	background:#969696;
	mso-pattern:black none;
	white-space:nowrap;}
.xl777414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl787414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	background:#969696;
	mso-pattern:black none;
	white-space:nowrap;}
.xl797414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl807414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl817414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl827414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl837414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl847414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl857414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:11.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl867414
	{padding:0px;
	mso-ignore:padding;
	color:red;
	font-size:10.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:Fixed;
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl877414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	background:#969696;
	mso-pattern:black none;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl887414
	{padding:0px;
	mso-ignore:padding;
	color:red;
	font-size:10.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:Fixed;
	text-align:center;
	vertical-align:bottom;
	border:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl897414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:9.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:middle;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl907414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:9.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:middle;
	border-top:none;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl917414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:9.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl927414
	{padding:0px;
	mso-ignore:padding;
	color:red;
	font-size:10.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:Fixed;
	text-align:center;
	vertical-align:bottom;
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl937414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:Fixed;
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	background:#969696;
	mso-pattern:black none;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl947414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	background:#969696;
	mso-pattern:black none;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl957414
	{padding:0px;
	mso-ignore:padding;
	color:red;
	font-size:10.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:Fixed;
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl967414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:Fixed;
	text-align:center;
	vertical-align:bottom;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl977414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	background:#969696;
	mso-pattern:black none;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl987414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:none;
	background:#969696;
	mso-pattern:black none;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl997414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
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
.xl1007414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:Fixed;
	text-align:general;
	vertical-align:bottom;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl1017414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:Fixed;
	text-align:general;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl1027414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	background:#969696;
	mso-pattern:black none;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl1037414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	background:#969696;
	mso-pattern:black none;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl1047414
	{padding:0px;
	mso-ignore:padding;
	color:red;
	font-size:10.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:Standard;
	text-align:general;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl1057414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:Fixed;
	text-align:general;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	background:#969696;
	mso-pattern:black none;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl1067414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:Fixed;
	text-align:general;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	background:#969696;
	mso-pattern:black none;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl1077414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:Fixed;
	text-align:general;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	background:#969696;
	mso-pattern:black none;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl1087414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:Fixed;
	text-align:general;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	background:#969696;
	mso-pattern:black none;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl1097414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1107414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1117414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl1127414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1137414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
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
.xl1147414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:none;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1157414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1167414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
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
.xl1177414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:none;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1187414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1197414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:\"\@\";
	text-align:center;
	vertical-align:bottom;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1207414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:Fixed;
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl1217414
	{padding:0px;
	mso-ignore:padding;
	color:red;
	font-size:10.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:Fixed;
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl1227414
	{padding:0px;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-ignore:padding;
	mso-font-charset:0;
	mso-number-format:Fixed;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	text-align:center;
	vertical-align:bottom;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	white-space:nowrap;}
.xl1237414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	background:#969696;
	mso-pattern:black none;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl1247414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:Fixed;
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
.xl1257414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1267414
	{padding:0px;
	mso-ignore:padding;
	color:red;
	font-size:10.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:Fixed;
	text-align:center;
	vertical-align:bottom;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl1277414
	{padding:0px;
	mso-ignore:padding;
	color:red;
	font-size:10.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:Fixed;
	text-align:center;
	vertical-align:bottom;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1287414
	{padding:0px;
	mso-ignore:padding;
	color:red;
	font-size:10.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:Fixed;
	text-align:center;
	vertical-align:bottom;
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1297414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:\"mmm\\-yy\";
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1307414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:\"mmm\\-yy\";
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1317414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:\"mmm\\-yy\";
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1327414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:9.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:right;
	vertical-align:middle;
	border-top:none;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1337414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:9.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:right;
	vertical-align:middle;
	border-top:none;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1347414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:9.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:\"\@\";
	text-align:center;
	vertical-align:middle;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1357414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:9.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:\"\@\";
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1367414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:9.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:right;
	vertical-align:middle;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl1377414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:9.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:right;
	vertical-align:middle;
	border-top:none;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl1387414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:9.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl1397414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:9.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl1407414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:\"mmm\\-yy\";
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1417414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:\"mmm\\-yy\";
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1427414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl1437414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:8.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl1447414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:8.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl1457414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:8.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl1467414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1477414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1487414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1497414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1507414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1517414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1527414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1537414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1547414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1557414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1567414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1577414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1587414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1597414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1607414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1617414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1627414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1637414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1647414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:8.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl1657414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:8.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl1667414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1677414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:9.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl1687414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:9.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl1697414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:9.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl1707414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1717414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1727414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1737414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1747414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1757414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
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
.xl1767414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:none;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1777414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1787414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:underline;
	text-underline-style:single;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1797414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:none;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1807414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1817414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:9.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:top;
	border-top:none;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl1827414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:9.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:top;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl1837414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:9.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:top;
	border-top:none;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl1847414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:9.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:top;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl1857414
	{padding:0px;
	mso-ignore:padding;
	color:red;
	font-size:10.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1867414
	{padding:0px;
	mso-ignore:padding;
	color:red;
	font-size:10.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1877414
	{padding:0px;
	mso-ignore:padding;
	color:red;
	font-size:10.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1887414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:9.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:top;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl1897414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:9.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:top;
	border-top:none;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl1907414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:20.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:bottom;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl1917414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:20.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:bottom;
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl1927414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:9.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:121;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl1937414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:9.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:121;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl1947414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:1.0pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1957414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:1.0pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1967414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl1977414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:bottom;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl1987414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:8.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
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
.xl1997414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:8.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl2007414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:9.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:121;
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl2017414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl2027414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl2037414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
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
	white-space:normal;}
.xl2047414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
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
	white-space:normal;}
.xl2057414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:8.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl2067414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:8.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl2077414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:middle;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl2087414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:middle;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl2097414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
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
	white-space:normal;}
.xl2107414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
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
	white-space:normal;}
.xl2117414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:20.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
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
	white-space:normal;}
.xl2127414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:20.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl2137414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:20.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:none;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl2147414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:20.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl2157414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:bottom;
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl2167414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
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
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl2177414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:unlocked visible;
	white-space:nowrap;}
.xl2187414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:9.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:bottom;
	border-top:none;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl2197414
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:9.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:\"Book Antiqua\", serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:bottom;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
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

<div id=\"Modelos 5073 Año-2018_7414\" align=center x:publishsource=\"Excel\">

<table border=0 cellpadding=0 cellspacing=0 width=1751 style='border-collapse:
 collapse;table-layout:fixed;width:1313pt'>
 <col width=159 style='mso-width-source:userset;mso-width-alt:5814;width:119pt'>
 <col width=80 style='width:60pt'>
 <col width=42 style='mso-width-source:userset;mso-width-alt:1536;width:32pt'>
 <col width=71 style='mso-width-source:userset;mso-width-alt:2596;width:53pt'>
 <col width=199 style='mso-width-source:userset;mso-width-alt:7277;width:149pt'>
 <col width=80 span=2 style='width:60pt'>
 <col width=80 style='width:60pt'>
 <col width=80 span=12 style='width:60pt'>
 <tr height=41 style='mso-height-source:userset;height:30.75pt'>
  <td colspan=2 height=41 class=xl1907414 width=239 style='border-right:1.0pt solid black;
  height:30.75pt;width:179pt'>O.N.E.I.<span
  style='mso-spacerun:yes'>              </span></td>
  <td colspan=3 class=xl1927414 width=312 style='border-right:1.0pt solid black;
  border-left:none;width:234pt'>Sistema de Informacion Estadisticas Nacional</td>
  <td colspan=3 class=xl2017414 width=240 style='border-right:1.0pt solid black;
  width:180pt'>&nbsp;</td>
  <td colspan=5 class=xl1297414 width=400 style='border-right:1.0pt solid black;
  border-left:none;width:300pt'>INFORME MENSUAL Y ACUMULADO HASTA</td>
  <td colspan=3 class=xl1447414 width=240 style='width:180pt'>&nbsp;</td>
  <td colspan=4 class=xl2037414 width=320 style='border-right:1.0pt solid black;
  width:240pt'>&nbsp;</td>
 </tr>
 <tr height=20 style='mso-height-source:userset;height:15.0pt'>
  <td colspan=2 height=20 class=xl2187414 width=239 style='border-right:1.0pt solid black;
  height:15.0pt;width:179pt'>OFICINA NACIONAL DE <br>ESTADISTICAS E INFORMACION</td>
  <td colspan=3 rowspan=3 class=xl1817414 width=312 style='border-right:1.0pt solid black;
  border-bottom:1.0pt solid black;width:234pt'>( SIEN )</td>
  <td class=xl897414 width=80 style='width:60pt'></td>
  <td class=xl897414 width=80 style='width:60pt'></td>
  <td class=xl897414 width=80 style='width:60pt'></td>
  <td rowspan=3 class=xl1327414 style='border-bottom:1.0pt solid black'>MES:</td>
  <td rowspan=3 class=xl1347414 style='border-bottom:1.0pt solid black'><span
  style='mso-spacerun:yes'> </span>" . strtoupper(FechaUtil::getNombreMes($mes)) . "</td>
  <td rowspan=3 class=xl1367414 style='border-bottom:1.0pt solid black'>AÑO:</td>
  <td rowspan=3 class=xl1387414 style='border-bottom:1.0pt solid black'>" . $anno . "</td>
  <td rowspan=3 class=xl1407414 style='border-bottom:1.0pt solid black'>&nbsp;</td>
  <td colspan=3 rowspan=3 class=xl2057414 width=240 style='border-bottom:1.0pt solid black;
  width:180pt'></td>
  <td colspan=4 rowspan=3 class=xl2077414 width=320 style='border-right:1.0pt solid black;
  border-bottom:1.0pt solid black;width:240pt'></td>
 </tr>
 <tr height=20 style='mso-height-source:userset;height:15.0pt'>
  <td colspan=2 rowspan=2 height=39 class=xl2117414 width=239 style='border-right:
  1.0pt solid black;border-bottom:1.0pt solid black;height:29.25pt;width:179pt'>&nbsp;</td>
  <td class=xl917414 width=80 style='width:60pt'>2</td>
  <td class=xl897414 width=80 style='width:60pt'></td>
  <td class=xl897414 width=80 style='width:60pt'></td>
 </tr>
 <tr height=19 style='mso-height-source:userset;height:14.25pt'>
  <td height=19 class=xl907414 width=80 style='height:14.25pt;width:60pt'>&nbsp;</td>
  <td class=xl907414 width=80 style='width:60pt'>&nbsp;</td>
  <td class=xl907414 width=80 style='width:60pt'>&nbsp;</td>
 </tr>
 <tr height=22 style='height:16.5pt'>
  <td height=22 class=xl857414 style='height:16.5pt;'>Centro
  informante:</td>
  <td colspan=13 class=xl1977414 style='border-right:1.0pt solid black'>" . strtoupper($unidad->getSiglas()) . "</td>
  <td class=xl847414 colspan=2>Codigo centro informante:<span
  style='mso-spacerun:yes'> </span></td>
  <td class=xl997414>" . $unidad->getCodigo() . "</td>
  <td class=xl997414>&nbsp;</td>
  <td colspan=2 class=xl2167414 style='border-right:1.0pt solid black'>&nbsp;</td>
 </tr>
 <tr height=19 style='mso-height-source:userset;height:14.25pt'>
  <td colspan=3 rowspan=3 height=90 class=xl1527414 style='border-right:1.0pt solid black;
  border-bottom:1.0pt solid black;height:67.5pt'>Productos</td>
  <td rowspan=3 class=xl1617414 style='border-bottom:1.0pt solid black;
  border-top:none'>Fila</td>
  <td rowspan=3 class=xl1617414 style='border-bottom:1.0pt solid black;
  border-top:none'>U/M</td>
  <td colspan=6 class=xl1987414 style='border-right:1.0pt solid black'>&nbsp;</td>
  <td colspan=7 class=xl1437414 width=560 style='border-right:1.0pt solid black;
  border-left:none;width:420pt'>OPERACIONES DEL MES EN MILES DE LITROS POR
  TARJETA MAGNETICA</td>
  <td colspan=2 rowspan=2 class=xl1437414 width=160 style='border-right:1.0pt solid black;
  border-bottom:1.0pt solid black;width:120pt'>CONSUMO ACUMULADO</td>
 </tr>
 <tr height=19 style='mso-height-source:userset;height:14.25pt'>
  <td rowspan=2 height=71 class=xl1117414 width=80 style='border-bottom:1.0pt solid black;
  height:53.25pt;border-top:none;width:60pt'>Compras a CUPET</td>
  <td rowspan=2 class=xl1117414 width=80 style='border-bottom:1.0pt solid black;
  border-top:none;width:60pt'>Otras Entradas</td>
  <td colspan=2 class=xl1127414 style='border-right:1.0pt solid black;
  border-left:none'>Consumo</td>
  <td rowspan=2 class=xl1117414 width=80 style='border-bottom:1.0pt solid black;
  border-top:none;width:60pt'>Otras Salidas</td>
  <td rowspan=2 class=xl1117414 width=80 style='border-bottom:1.0pt solid black;
  border-top:none;width:60pt'>Inventario Final</td>
  <td rowspan=2 class=xl1117414 width=80 style='border-bottom:1.0pt solid black;
  width:60pt'>Asignado en el mes</td>
  <td rowspan=2 class=xl1117414 width=80 style='border-bottom:1.0pt solid black;
  width:60pt'>Recibido del que efectua la carga</td>
  <td rowspan=2 class=xl1117414 width=80 style='border-bottom:1.0pt solid black;
  width:60pt'>Consumo</td>
  <td rowspan=2 class=xl1117414 width=80 style='border-bottom:1.0pt solid black;
  width:60pt'>Entregado para consumo</td>
  <td rowspan=2 class=xl1117414 width=80 style='border-bottom:1.0pt solid black;
  width:60pt'>Saldo Final Total<span style='mso-spacerun:yes'> </span></td>
  <td rowspan=2 class=xl1117414 width=80 style='border-bottom:1.0pt solid black;
  width:60pt'><span style='mso-spacerun:yes'> </span>de Ello: Carga<span
  style='mso-spacerun:yes'>  </span>para el Proximo Mes</td>
  <td rowspan=2 class=xl1117414 width=80 style='border-bottom:1.0pt solid black;
  width:60pt'>De Ello : disponible en FINCIMEX</td>
 </tr>
 <tr height=52 style='mso-height-source:userset;height:39.0pt'>
  <td height=52 class=xl647414 width=80 style='height:39.0pt;border-top:none;
  border-left:none;width:60pt'>Directo</td>
  <td class=xl657414 width=80 style='border-top:none;width:60pt'>Indirecto</td>
  <td class=xl667414 width=80 style='border-top:none;border-left:none;
  width:60pt'>Real</td>
  <td class=xl1117414 width=80 style='border-top:none;width:60pt'>Año anterior</td>
 </tr>
 <tr height=19 style='height:14.25pt'>
  <td colspan=3 height=19 class=xl1947414 style='border-right:1.0pt solid black;
  height:14.25pt'>A</td>
  <td class=xl1137414 style='border-top:none'>B</td>
  <td class=xl677414 style='border-top:none'>C</td>
  <td class=xl1197414 style='border-top:none;border-left:none'>1</td>
  <td class=xl1197414 style='border-top:none'>2</td>
  <td class=xl1197414 style='border-top:none'>3</td>
  <td class=xl1197414 style='border-top:none'>4</td>
  <td class=xl1197414 style='border-top:none'>5</td>
  <td class=xl1197414 style='border-top:none'>6</td>
  <td class=xl1197414 style='border-top:none'>7</td>
  <td class=xl1197414 style='border-top:none'>8</td>
  <td class=xl1197414 style='border-top:none'>9</td>
  <td class=xl1197414 style='border-top:none'>10</td>
  <td class=xl1197414 style='border-top:none'>11</td>
  <td class=xl687414 style='border-top:none'>12</td>
  <td class=xl687414 style='border-top:none;border-left:none'>13</td>
  <td class=xl687414 style='border-left:none'>14</td>
  <td class=xl687414 style='border-left:none'>15</td>
 </tr>";

        foreach ($datos as $key => $dato) {

            if ($datos[$key]['fila'] < 954) {
                $_html .= "      
                 <tr height=20 style='height:15.0pt'>
                  <td colspan=3 height=20 class=xl1467414 style='border-right:1.0pt solid black;
                    height:15.0pt'>".$datos[$key]['producto']."</td>
                  <td class=xl697414>".$datos[$key]['fila']."</td>
                  <td class=xl707414>".$datos[$key]['um']."</td>
                  <td class=xl707414 style='border-top:none'>".($datos[$key]['compras_cupet']?number_format($datos[$key]['compras_cupet'],2,',','.'): '')."</td>
                  <td class=xl1227414 style='border-top:none'>".($datos[$key]['otras_entradas']?number_format($datos[$key]['otras_entradas'],2,',','.'): '')."</td>
                  <td class=xl717414 style='border-top:none'>".($datos[$key]['consumo_directo']?number_format($datos[$key]['consumo_directo'],2,',','.'): '')."</td>
                  <td class=xl1227414 style='border-top:none'>".($datos[$key]['consumo_indirecto']?number_format($datos[$key]['consumo_indirecto'],2,',','.'): '')."</td>
                  <td class=xl1227414 style='border-top:none'>".($datos[$key]['otras_salidas']?number_format($datos[$key]['otras_salidas'],2,',','.'): '')."</td>
                  <td class=xl1227414 style='border-top:none'>".($datos[$key]['inv_final']?number_format($datos[$key]['inv_final'],2,',','.'): '')."</td>
                  <td class=xl1227414 style='border-top:none'>".($datos[$key]['asignado_mes']?number_format($datos[$key]['asignado_mes'],2,',','.'): '')."</td>
                  <td class=xl707414 style='border-top:none'>".($datos[$key]['efectua_carga']?number_format($datos[$key]['efectua_carga'],2,',','.'): '')."</td>
                  <td class=xl1227414 style='border-top:none'>".($datos[$key]['consumo']?number_format($datos[$key]['consumo'],2,',','.'): '')."</td>
                  <td class=xl1227414 style='border-top:none'>".($datos[$key]['entregados_consumo']?number_format($datos[$key]['entregados_consumo'],2,',','.'): '')."</td>
                  <td class=xl707414 style='border-top:none'>".($datos[$key]['saldo_final_total'] ?number_format($datos[$key]['saldo_final_total'],2,',','.'): '')."</td>
                  <td class=xl727414 style='border-top:none'>".($datos[$key]['proximo_mes']?number_format($datos[$key]['proximo_mes'],2,',','.'): '')."</td>
                  <td class=xl967414 style='border-top:none'>".($datos[$key]['disponible_fincimex']?number_format($datos[$key]['disponible_fincimex'],2,',','.'): '')."</td>
                  <td class=xl1267414 style='border-top:none'>".($datos[$key]['ca_real']?number_format($datos[$key]['ca_real'],2,',','.'): '')."</td>
                  <td class=xl927414 style='border-top:none'>".($datos[$key]['ca_ano_anterior']?number_format($datos[$key]['ca_ano_anterior'],2,',','.'): '')."</td>
                 </tr>";
            }
            else{
                break;
            }

        }

        $_html .= "      
                 <tr height=19 style='height:14.25pt'>
                  <td colspan=3 height=19 class=xl1677414 width=281 style='border-right:1.0pt solid black;
                  height:14.25pt;width:211pt'>Sobre grupo electrogeno de emergencia y
                  sincronizados</td>
                  <td class=xl767414 style='border-top:none;border-left:none'>&nbsp;</td>
                  <td class=xl767414 style='border-top:none'>&nbsp;</td>
                  <td class=xl1057414 style='border-top:none'>&nbsp;</td>
                  <td class=xl1057414 style='border-top:none'>&nbsp;</td>
                  <td class=xl1067414 style='border-top:none'>&nbsp;</td>
                  <td class=xl1057414 style='border-top:none'>&nbsp;</td>
                  <td class=xl1057414 style='border-top:none'>&nbsp;</td>
                  <td class=xl1057414 style='border-top:none'>&nbsp;</td>
                  <td class=xl1027414 style='border-top:none'>&nbsp;</td>
                  <td class=xl1027414 style='border-top:none'>&nbsp;</td>
                  <td class=xl1027414 style='border-top:none'>&nbsp;</td>
                  <td class=xl1027414 style='border-top:none'>&nbsp;</td>
                  <td class=xl1027414 style='border-top:none'>&nbsp;</td>
                  <td class=xl877414 style='border-top:none'>&nbsp;</td>
                  <td class=xl1237414 style='border-top:none'>&nbsp;</td>
                  <td class=xl1027414 style='border-top:none'>&nbsp;</td>
                  <td class=xl877414 style='border-top:none'>&nbsp;</td>
                 </tr>";

        foreach ($datos as $key => $dato) {
//            var_dump($datos[$key]['compras_cupet']);
            if ($datos[$key]['fila'] >= 954) {
                $_html .= "      
                 <tr height=20 style='height:15.0pt'>
                  <td colspan=3 height=20 class=xl1467414 style='border-right:1.0pt solid black;
                    height:15.0pt'>".$datos[$key]['producto']."</td>
                  <td class=xl697414>".$datos[$key]['fila']."</td>
                  <td class=xl707414>".$datos[$key]['um']."</td>
                  <td class=xl707414 style='border-top:none'>".($datos[$key]['compras_cupet']?number_format($datos[$key]['compras_cupet'],2,',','.'): '')."</td>
                  <td class=xl1227414 style='border-top:none'>".($datos[$key]['otras_entradas']?number_format($datos[$key]['otras_entradas'],2,',','.'): '')."</td>
                  <td class=xl717414 style='border-top:none'>".($datos[$key]['consumo_directo']?number_format($datos[$key]['consumo_directo'],2,',','.'): '')."</td>
                  <td class=xl1227414 style='border-top:none'>".($datos[$key]['consumo_indirecto']?number_format($datos[$key]['consumo_indirecto'],2,',','.'): '')."</td>
                  <td class=xl1227414 style='border-top:none'>".($datos[$key]['otras_salidas']?number_format($datos[$key]['otras_salidas'],2,',','.'): '')."</td>
                  <td class=xl1227414 style='border-top:none'>".($datos[$key]['inv_final']?number_format($datos[$key]['inv_final'],2,',','.'): '')."</td>
                  <td class=xl1227414 style='border-top:none'>".($datos[$key]['asignado_mes']?number_format($datos[$key]['asignado_mes'],2,',','.'): '')."</td>
                  <td class=xl707414 style='border-top:none'>".($datos[$key]['efectua_carga']?number_format($datos[$key]['efectua_carga'],2,',','.'): '')."</td>
                  <td class=xl1227414 style='border-top:none'>".($datos[$key]['consumo']?number_format($datos[$key]['consumo'],2,',','.'): '')."</td>
                  <td class=xl1227414 style='border-top:none'>".($datos[$key]['entregados_consumo']?number_format($datos[$key]['entregados_consumo'],2,',','.'): '')."</td>
                  <td class=xl707414 style='border-top:none'>".($datos[$key]['saldo_final_total'] ?number_format($datos[$key]['saldo_final_total'],2,',','.'): '')."</td>
                  <td class=xl727414 style='border-top:none'>".($datos[$key]['proximo_mes']?number_format($datos[$key]['proximo_mes'],2,',','.'): '')."</td>
                  <td class=xl967414 style='border-top:none'>".($datos[$key]['disponible_fincimex']?number_format($datos[$key]['disponible_fincimex'],2,',','.'): '')."</td>
                  <td class=xl1267414 style='border-top:none'>".($datos[$key]['ca_real']?number_format($datos[$key]['ca_real'],2,',','.'): '')."</td>
                  <td class=xl927414 style='border-top:none'>".($datos[$key]['ca_ano_anterior']?number_format($datos[$key]['ca_ano_anterior'],2,',','.'): '')."</td>
                 </tr>";
            }
        }

        $_html .= "
 <tr height=21 style='height:15.75pt'>
  <td colspan=3 height=21 class=xl847414 style='border-right:1.0pt solid black;
  height:15.75pt'>SUMA DE CONTROL (pagina 1 de 1)</td>
  <td class=xl1137414>999</td>
  <td class=xl787414>&nbsp;</td>
  <td class=xl1277414>".$summary[0]->compras_cupet."</td>
  <td class=xl887414>".$summary[0]->otras_entradas."</td>
  <td class=xl887414>".$summary[0]->consumo_directo."</td>
  <td class=xl887414>".$summary[0]->consumo_indirecto."</td>
  <td class=xl887414>".$summary[0]->otras_salidas."</td>
  <td class=xl887414>".$summary[0]->inv_final."</td>
  <td class=xl887414>".$summary[0]->asignado_mes."</td>
  <td class=xl1277414>".$summary[0]->efectua_carga."</td>
  <td class=xl887414>".$summary[0]->consumo."</td>
  <td class=xl887414>".$summary[0]->entregados_consumo."</td>
  <td class=xl1277414>".$summary[0]->saldo_final_total."</td>
  <td class=xl887414>".$summary[0]->proximo_mes."</td>
  <td class=xl1287414>".$summary[0]->disponible_fincimex."</td>
  <td class=xl887414>".$summary[0]->ca_real."</td>
  <td class=xl887414>".$summary[0]->ca_ano_anterior."</td>
 </tr>
 <tr height=19 style='height:14.25pt'>
  <td colspan=20 height=19 class=xl1747414 style='height:14.25pt'></td>
 </tr>
 <tr height=19 style='height:14.25pt'>
  <td colspan=20 height=19 class=xl1127414 style='border-right:1.0pt solid black;
  height:14.25pt'>PRODUCTO A INFORMAR EN LAS FILAS EN<span
  style='mso-spacerun:yes'>  </span>BANCO</td>
 </tr>
 <tr height=19 style='height:14.25pt'>
  <td colspan=2 height=19 class=xl1767414 style='height:14.25pt'>Productos</td>
  <td class=xl677414 style='border-top:none'>Fila</td>
  <td class=xl677414 style='border-top:none;border-left:none'>U.M.</td>
  <td class=xl1127414 style='border-top:none;border-left:none'>Productos</td>
  <td class=xl677414 style='border-top:none'>Fila</td>
  <td colspan=4 class=xl1167414>Productos</td>
  <td class=xl677414 style='border-top:none'>Fila</td>
  <td colspan=2 class=xl1127414 style='border-right:1.0pt solid black;
  border-left:none'>U.M.</td>
  <td colspan=3 class=xl1167414>&nbsp;</td>
  <td class=xl1167414>&nbsp;</td>
  <td class=xl1167414>&nbsp;</td>
  <td class=xl677414 style='border-top:none'>Fila</td>
  <td class=xl1187414>U.M.</td>
 </tr>
 <tr height=18 style='height:13.5pt'>
  <td colspan=2 height=18 class=xl1147414 style='height:13.5pt'>Petroleo crudo
  650</td>
  <td class=xl797414>26</td>
  <td class=xl797414 style='border-left:none'>Miles L</td>
  <td class=xl1257414 style='border-top:none;border-left:none'>Mezcla
  Fuel-Diesel (IFO-380)</td>
  <td class=xl797414>48</td>
  <td colspan=4 class=xl1747414>Kerosina<span style='mso-spacerun:yes'> </span></td>
  <td class=xl797414>70</td>
  <td colspan=2 class=xl1757414 style='border-right:1.0pt solid black;
  border-left:none'>Miles L</td>
  <td colspan=3 class=xl1747414></td>
  <td class=xl807414 style='border-top:none'>&nbsp;</td>
  <td class=xl817414 style='border-left:none'>&nbsp;</td>
  <td class=xl797414 style='border-left:none'>84</td>
  <td class=xl1157414>Miles L</td>
 </tr>
 <tr height=18 style='height:13.5pt'>
  <td colspan=2 height=18 class=xl1147414 style='height:13.5pt'>Petroleo crudo
  900</td>
  <td class=xl797414>27</td>
  <td class=xl797414 style='border-left:none'>Miles L</td>
  <td class=xl1147414 style='border-left:none'>Otras mezclas Fuel- Diesel</td>
  <td class=xl797414>49</td>
  <td colspan=4 class=xl1747414>Alcohol desnaturalizado</td>
  <td class=xl797414>72</td>
  <td colspan=2 class=xl1757414 style='border-right:1.0pt solid black;
  border-left:none'>hL</td>
  <td colspan=3 class=xl1747414></td>
  <td class=xl817414>&nbsp;</td>
  <td class=xl817414 style='border-left:none'>&nbsp;</td>
  <td class=xl797414 style='border-left:none'>85</td>
  <td class=xl1157414>Miles L</td>
 </tr>
 <tr height=18 style='height:13.5pt'>
  <td colspan=2 height=18 class=xl1147414 style='height:13.5pt'>Petroleo crudo
  1100</td>
  <td class=xl797414>28</td>
  <td class=xl797414 style='border-left:none'>Miles L</td>
  <td class=xl1147414 style='border-left:none'>Solvente Nafta especial</td>
  <td class=xl797414>55</td>
  <td colspan=4 class=xl1747414>Gas Natural</td>
  <td class=xl797414>74</td>
  <td colspan=2 class=xl1757414 style='border-right:1.0pt solid black;
  border-left:none'>Miles m</td>
  <td colspan=3 class=xl1747414></td>
  <td class=xl817414>&nbsp;</td>
  <td class=xl817414 style='border-left:none'>&nbsp;</td>
  <td class=xl797414 style='border-left:none'>&nbsp;</td>
  <td class=xl1157414>&nbsp;</td>
 </tr>
 <tr height=18 style='height:13.5pt'>
  <td colspan=2 height=18 class=xl1147414 style='height:13.5pt'>Petroleo crudo
  1400</td>
  <td class=xl797414>29</td>
  <td class=xl797414 style='border-left:none'>Miles L</td>
  <td class=xl1147414 style='border-left:none'>Solvente sustituto de aguarras</td>
  <td class=xl797414>56</td>
  <td colspan=4 class=xl1777414>Gas manufacturado</td>
  <td class=xl797414 style='border-left:none'>76</td>
  <td colspan=2 class=xl1757414 style='border-right:1.0pt solid black;
  border-left:none'>Miles m</td>
  <td colspan=3 class=xl1747414></td>
  <td class=xl817414>&nbsp;</td>
  <td class=xl817414 style='border-left:none'>&nbsp;</td>
  <td class=xl797414 style='border-left:none'>86</td>
  <td class=xl1157414>Miles L</td>
 </tr>
 <tr height=18 style='height:13.5pt'>
  <td colspan=2 height=18 class=xl1147414 style='height:13.5pt'>Petroleo
  combustible ligero</td>
  <td class=xl797414>33</td>
  <td class=xl797414 style='border-left:none'>Miles L</td>
  <td class=xl1147414 style='border-left:none'>Nafta reductora de viscosidad</td>
  <td class=xl797414>57</td>
  <td colspan=4 class=xl1787414>Compras a entidades extranjera</td>
  <td class=xl797414 style='border-left:none'>&nbsp;</td>
  <td colspan=2 class=xl1757414 style='border-right:1.0pt solid black;
  border-left:none'>&nbsp;</td>
  <td colspan=3 class=xl1777414>&nbsp;</td>
  <td class=xl817414 style='border-left:none'>&nbsp;</td>
  <td class=xl817414 style='border-left:none'>&nbsp;</td>
  <td class=xl797414 style='border-left:none'>87</td>
  <td class=xl1157414>Miles L</td>
 </tr>
 <tr height=18 style='height:13.5pt'>
  <td colspan=2 height=18 class=xl1147414 style='height:13.5pt'>Petroleo
  combustible mediano</td>
  <td class=xl797414>34</td>
  <td class=xl797414 style='border-left:none'>Miles L</td>
  <td class=xl1147414 style='border-left:none'>Asfalto de petroleo 50-70</td>
  <td class=xl797414>61</td>
  <td colspan=4 class=xl1777414>Mezcla Fuel-Diesel (IFO-180)</td>
  <td class=xl797414 style='border-left:none'>80</td>
  <td colspan=2 class=xl1757414 style='border-right:1.0pt solid black;
  border-left:none'>Miles L</td>
  <td colspan=3 class=xl1777414>&nbsp;</td>
  <td class=xl817414 style='border-left:none'>&nbsp;</td>
  <td class=xl817414 style='border-left:none'>&nbsp;</td>
  <td class=xl797414 style='border-left:none'>88</td>
  <td class=xl1157414>Miles L</td>
 </tr>
 <tr height=18 style='height:13.5pt'>
  <td colspan=2 height=18 class=xl1147414 style='height:13.5pt'>Petroleo
  combustible pesado</td>
  <td class=xl797414>35</td>
  <td class=xl797414 style='border-left:none'>Miles L</td>
  <td class=xl1147414 style='border-left:none'>Asfalto de petroleo 150-200</td>
  <td class=xl797414>62</td>
  <td colspan=4 class=xl1747414>Mezcla Fuel-Diesel (IFO-380)</td>
  <td class=xl797414>81</td>
  <td colspan=2 class=xl1757414 style='border-right:1.0pt solid black;
  border-left:none'>Miles L</td>
  <td colspan=3 class=xl1777414>&nbsp;</td>
  <td class=xl817414 style='border-left:none'>&nbsp;</td>
  <td class=xl817414 style='border-left:none'>&nbsp;</td>
  <td class=xl797414 style='border-left:none'>89</td>
  <td class=xl1157414>Miles L</td>
 </tr>
 <tr height=18 style='height:13.5pt'>
  <td colspan=2 height=18 class=xl1147414 style='height:13.5pt'>Petroleo
  combustible Extrapesado</td>
  <td class=xl797414>36</td>
  <td class=xl797414 style='border-left:none'>Miles L</td>
  <td class=xl1147414 style='border-left:none'>Asfalto de petroleo diluido</td>
  <td class=xl797414>63</td>
  <td colspan=4 class=xl1747414>Petroleo combustible<span
  style='mso-spacerun:yes'> </span></td>
  <td class=xl797414>82</td>
  <td colspan=2 class=xl1757414 style='border-right:1.0pt solid black;
  border-left:none'>Miles L</td>
  <td colspan=3 class=xl1777414>&nbsp;</td>
  <td class=xl817414 style='border-left:none'>&nbsp;</td>
  <td class=xl817414 style='border-left:none'>&nbsp;</td>
  <td class=xl797414 style='border-left:none'>90</td>
  <td class=xl1157414>Miles L</td>
 </tr>
 <tr height=19 style='height:14.25pt'>
  <td colspan=2 height=19 class=xl1177414 style='height:14.25pt'>Mezclas
  Fuel-Diesel (IFO-180)</td>
  <td class=xl827414>47</td>
  <td class=xl827414 style='border-left:none'>Miles L</td>
  <td class=xl1177414 style='border-left:none'>Turbocombustible</td>
  <td class=xl827414>68</td>
  <td colspan=4 class=xl1177414 style='border-right:1.0pt solid black;
  border-left:none'>Combustible Diesel</td>
  <td class=xl827414 style='border-left:none'>83</td>
  <td colspan=2 class=xl1767414 style='border-right:1.0pt solid black;
  border-left:none'>Miles L</td>
  <td colspan=3 class=xl1797414>&nbsp;</td>
  <td class=xl837414>&nbsp;</td>
  <td class=xl837414 style='border-left:none'>&nbsp;</td>
  <td class=xl827414 style='border-left:none'>91</td>
  <td class=xl1187414>Miles L</td>
 </tr>
 <![if supportMisalignedColumns]>
 <tr height=0 style='display:none'>
  <td width=159 style='width:119pt'></td>
  <td width=80 style='width:60pt'></td>
  <td width=42 style='width:32pt'></td>
  <td width=71 style='width:53pt'></td>
  <td width=199 style='width:149pt'></td>
  <td width=80 style='width:60pt'></td>
  <td width=80 style='width:60pt'></td>
  <td width=80 style='width:60pt'></td>
  <td width=80 style='width:60pt'></td>
  <td width=80 style='width:60pt'></td>
  <td width=80 style='width:60pt'></td>
  <td width=80 style='width:60pt'></td>
  <td width=80 style='width:60pt'></td>
  <td width=80 style='width:60pt'></td>
  <td width=80 style='width:60pt'></td>
  <td width=80 style='width:60pt'></td>
  <td width=80 style='width:60pt'></td>
  <td width=80 style='width:60pt'></td>
  <td width=80 style='width:60pt'></td>
  <td width=80 style='width:60pt'></td>
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
        return new Response(json_encode(array('success' => true, 'html' => $_html)));
    }


    private function unidadesToString($_unidades)
    {
        $_string_unidades = "'" . $_unidades[0] . "'";
        for ($i = 1; $i < count($_unidades); $i++) {
            $_string_unidades .= ",'" . $_unidades[$i] . "'";
        }
        return $_string_unidades;
    }

}