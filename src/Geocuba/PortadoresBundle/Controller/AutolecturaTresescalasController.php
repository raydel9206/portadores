<?php
/**
 * Created by PhpStorm.
 * User: yosley
 * Date: 25/11/2015
 * Time: 8:29
 */

namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\PortadoresBundle\Entity\AutolecturaEstadisticas;
use Geocuba\AdminBundle\Util\FechaUtil;
use Geocuba\PortadoresBundle\Entity\AutolecturaTresescalas;
use Geocuba\PortadoresBundle\Entity\AutolecturaAutoinspeccion;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Geocuba\AdminBundle\Util\Util;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Util\Debug;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Component\HttpFoundation\Response;

class AutolecturaTresescalasController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {

        $_user = $this->get('security.context')->getToken()->getUser();
        $servicio_id = $request->get('servicio_id');

        $em = $this->getDoctrine()->getManager();

        $dominio = $em->getRepository('MySecurityBundle:Dominio')->findByUsersid($_user->getId());
        $_unidades[] = $dominio[0]->getUserUnidadid()->getId();
        $dominio_unidades = $em->getRepository('MySecurityBundle:DominioUnidades')->findByDominioid($dominio[0]->getId());
        foreach ($dominio_unidades as $unidad) {
            $_unidades[] = $unidad->getUnidadid()->getId();
        }

        $qb = $em->createQueryBuilder();
        $qb->select('autolectura')
            ->from('PortadoresBundle:AutolecturaTresescalas', 'autolectura')
            ->innerJoin('autolectura.serviciosid', 'idservicio')
            ->Where($qb->expr()->in('idservicio.nunidadid', $_unidades))
            ->andWhere($qb->expr()->eq('idservicio.visible', 'true'));
        if (isset($servicio_id) && $servicio_id !== '')
            $qb->andWhere($qb->expr()->eq('autolectura.serviciosid', ':servicio_id'))
                ->setParameter('servicio_id', $servicio_id);

        $entities = $qb->orderBy('autolectura.fechaLectura', 'ASC')
            ->getQuery()
            ->getResult();

        $qb = $em->createQueryBuilder();
        $qb->select('count(autolectura)')
            ->from('PortadoresBundle:AutolecturaTresescalas', 'autolectura')
            ->innerJoin('autolectura.serviciosid', 'idservicio')
            ->Where($qb->expr()->in('idservicio.nunidadid', $_unidades))
            ->andWhere($qb->expr()->eq('idservicio.visible', 'true'));

        $total = $qb->getQuery()->getSingleScalarResult();

        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'fecha_lectura' => $entity->getFechaLectura()->format('D,j M, Y '),
                'fecha' => $entity->getFechaLectura()->format('Y-m-d'),
                'serviciosid' => $entity->getServiciosid()->getId(),
                'nombreserviciosid' => $entity->getServiciosid()->getNombreServicio(),
                'lectura_pico' => $entity->getLecturaPico(),
                'lectura_mad' => $entity->getLecturaMad(),
                'lectura_dia' => $entity->getLecturaDia(),
                'lectura_reactivo' => $entity->getLecturaReactivo(),
                'lectura_pico_maxD' => $entity->getLecturaMaxdemPico(),
                'lectura_mad_maxD' => $entity->getLecturaMaxdemMad(),
                'lectura_dia_maxD' => $entity->getLecturaMaxdemDia(),
                'consumo' => $entity->getConsumo(),
                'consumo_total_mad' => $entity->getConsumoTotalMad(),
                'consumo_total_dia' => $entity->getConsumoTotalDia(),
                'consumo_total_real' => $entity->getConsumoTotalReal(),
                'consumo_total_porciento' => $entity->getConsumoTotalPorciento(),
                'consumo_total_plan' => $entity->getConsumoTotalPlan(),
                'consumo_pico_plan' => $entity->getConsumoPicoPlan(),
                'consumo_pico_real' => $entity->getConsumoPicoReal(),
                'consumo_pico_porciento' => $entity->getConsumoPicoPorciento(),
                'mes' => $entity->getMes(),
                'anno' => $entity->getAnno()
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    public function getautolecturasbyserviciosAction(Request $request)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();

        $mes_abierto = $session->get('selected_month');
        $anno_abierto = $session->get('selected_year');
        $anno = $anno_abierto;
        $fecha_inicial = $anno_abierto . '-' . $mes_abierto . '-01';

        if ($mes_abierto !== 12) {
            $mes_siguiente = $mes_abierto + 1;
        } else {
            $mes_siguiente = 1;
            $anno = $anno + 1;
        }

        $fecha_final = $anno . '-' . $mes_siguiente . '-05';
        $idservicios = $request->get('id');
        $entities = $em->getRepository('PortadoresBundle:AutolecturaTresescalas')->getAutolecturaTresescalasMes($idservicios, $fecha_inicial, $fecha_final);
        $_data = array();
        $sql = "select     
           max(lectura_maxdem_mad) as mad, 
           max(lectura_maxdem_pico) as pico, 
           max(lectura_maxdem_dia) as dia
           from datos.autolectura_tresescalas as a where a.mes = '$mes_abierto' and a.anno = '$anno_abierto' and a.serviciosid = '$idservicios'";
        $consulta = $this->getDoctrine()->getConnection()->fetchAll($sql);
        $mxm_dmd_real = max($consulta[0]);

        foreach ($entities as $entity) {
            $servicio = $em->getRepository('PortadoresBundle:Servicio')->find($idservicios);
            $kwh = $entity->getConsumoTotalMad() + $entity->getConsumoTotalDia() + $entity->getConsumoPicoReal();
            $kvarh = $entity->getConsumo();

            $factor_potencia = ($kwh !== null && $kwh !== 0) ? round(cos(atan($kvarh / $kwh)), 3) : '0';
            $perdidas_transf = $this->Perdida_TAction($servicio, $entity);
            $_data[] = array(
                'id' => $entity->getId(),
                'fecha_lectura' => $entity->getFechaLectura()->format('D,j M, Y '),
                'fecha' => $entity->getFechaLectura()->format('Y-m-d'),
                'serviciosid' => $entity->getServiciosid()->getId(),
                'nombreserviciosid' => $entity->getServiciosid()->getNombreServicio(),
                'lectura_pico' => $entity->getLecturaPico(),
                'lectura_mad' => $entity->getLecturaMad(),
                'lectura_dia' => $entity->getLecturaDia(),
                'lectura_total' => $entity->getLecturaPico() + $entity->getLecturaMad() + $entity->getLecturaDia(),
                'lectura_reactivo' => $entity->getLecturaReactivo(),
                'lectura_pico_maxD' => $entity->getLecturaMaxdemPico(),
                'lectura_mad_maxD' => $entity->getLecturaMaxdemMad(),
                'lectura_dia_maxD' => $entity->getLecturaMaxdemDia(),
                'mxm_dmd_real' => $mxm_dmd_real,
                'factor_potencia' => !is_nan($factor_potencia) ? round($factor_potencia, 3) : 0.00,
                'perdidas_transf' => round($perdidas_transf, 2),
                'consumo' => $entity->getConsumo() ?? 0.00,
                'consumo_total_mad' => ($perdidas_transf !== 0) ? round($entity->getConsumoTotalMad() + ($perdidas_transf / 3), 3) : $entity->getConsumoTotalMad(),
                'consumo_total_dia' => ($perdidas_transf !== 0) ? round($entity->getConsumoTotalDia() + ($perdidas_transf / 2), 3) : $entity->getConsumoTotalDia(),
                'consumo_total_real' => ($perdidas_transf !== 0) ? round($entity->getConsumoTotalReal() + $perdidas_transf, 3) : $entity->getConsumoTotalReal(),
                'consumo_total_porciento' => $entity->getConsumoTotalPorciento() ?? 0.00,
                'consumo_total_plan' => $entity->getConsumoTotalPlan(),
                'consumo_pico_plan' => $entity->getConsumoPicoPlan(),
                'consumo_pico_real' => ($perdidas_transf !== 0) ? round($entity->getConsumoPicoReal() + ($perdidas_transf / 6), 3) : $entity->getConsumoPicoReal(),
                'consumo_pico_porciento' => $entity->getConsumoPicoPorciento() ?? 0.00,
                'mes' => $entity->getMes(),
                'anno' => $entity->getAnno()
            );
        }
        return new JsonResponse(array('success' => true, 'rows' => $_data, 'total' => \count($_data)));
    }

    public function getservicioAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $id = trim($request->get('id'));
        $entity = $em->getRepository('PortadoresBundle:Servicio')->find($id);
        $banco = $em->getRepository('PortadoresBundle:BancoTransformadores')->findOneBy(array('id' => $entity->getCapacBancoTranformadores()));
        $turno = $em->getRepository('PortadoresBundle:TurnoTrabajo')->findOneBy(array('id' => $entity->getTurnosTrabajo()));
        $var = array(
            'id' => $entity->getId(),
            'nombre_servicio' => $entity->getNombreServicio(),
            'codigo_cliente' => $entity->getCodigoCliente(),
            'factor_metrocontador' => $entity->getFactorMetrocontador(),
            'MaximaDemandaContratada' => $entity->getMaximaDemandaContratada(),
            'control' => $entity->getControl(),
            'ruta' => $entity->getRuta(),
            'folio' => $entity->getFolio(),
            'direccion' => $entity->getDireccion(),
            'factor_combustible' => $entity->getFactorCombustible(),
            'indice_consumo' => $entity->getIndiceConsumo(),
            'consumo_prom_anno' => $entity->getConsumoPromedioAnno(),
            'consumo_prom_plan' => $entity->getConsumoPromedioPlan(),
            'consumo_prom_real' => $entity->getConsumoPromedioReal(),
            'capac_banco_transf' => $banco->getCapacidad(),
            'banco_pfe' => $banco->getPfe(),
            'banco_pcu' => $banco->getPcu(),
            'tipo_servicio' => $entity->getServicioElectrico(),
            'turno_trabajo' => $turno->getTurno(),
            'turno_trabajo_horas' => $turno->getHoras(),
            'nunidadid' => $entity->getNunidadid()->getId(),
            'nombreunidadid' => $entity->getNunidadid()->getNombre(),
            'provicianid' => $entity->getProvinciaid()->getId(),
            'nombreprovicianid' => $entity->getProvinciaid()->getNombre(),
            'tarifaid' => $entity->getNtarifaid()->getId(),
            'nombretarifaid' => $entity->getNtarifaid()->getNombre(),
            'nactividadid' => $entity->getNactividadid()->getId(),
            'nombrenactividadid' => $entity->getNactividadid()->getNombre(),
            'num_nilvel_actividadid' => $entity->getNactividadid()->getUmActividad()->getId(),
            'nombreum_nilvel_actividadid' => $entity->getNactividadid()->getUmActividad()->getNivelActividad()

        );
        return new JsonResponse(array('rows' => $var, 'total' => \count($var)));
    }

    public function validarlecturasAction($datos_lecturas)
    {

        $lectura_menor = false;
        $fechaayer = $datos_lecturas['fecha_lectura'];
        $Fecha = $fechaayer->sub(new \DateInterval('P1D'));
        $Fechaayer = $Fecha->format('Y-m-d');;
        $datos = array();

        $entities = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:AutolecturaTresescalas')->findBy(
            array(
                'fechaLectura' => $Fecha,
                'serviciosid' => $datos_lecturas['servicioid']
            )
        );

        if ($entities) {
            foreach ($entities as $entity) {
                $datos[] = array(
                    'id' => $entity->getId(),
                    'fecha_lectura' => $entity->getFechaLectura()->format('D,j M, Y '),
                    'serviciosid' => $entity->getServiciosid()->getId(),
                    'nombreserviciosid' => $entity->getServiciosid()->getNombreServicio(),
                    'lectura_pico' => $entity->getLecturaPico(),
                    'lectura_mad' => $entity->getLecturaMad(),
                    'lectura_dia' => $entity->getLecturaDia(),
                    'lectura_reactivo' => $entity->getLecturaReactivo(),
                    'lectura_pico_maxD' => $entity->getLecturaMaxdemPico(),
                    'lectura_mad_maxD' => $entity->getLecturaMaxdemMad(),
                    'lectura_dia_maxD' => $entity->getLecturaMaxdemDia(),
                    'consumo' => $entity->getConsumo(),
                    'consumo_total_mad' => $entity->getConsumoTotalMad(),
                    'consumo_total_dia' => $entity->getConsumoTotalDia(),
                    'consumo_total_real' => $entity->getConsumoTotalReal(),
                    'consumo_total_porciento' => $entity->getConsumoTotalPorciento(),
                    'consumo_total_plan' => $entity->getConsumoTotalPlan(),
                    'consumo_pico_plan' => $entity->getConsumoPicoPlan(),
                    'consumo_pico_real' => $entity->getConsumoPicoReal(),
                    'consumo_pico_porciento' => $entity->getConsumoPicoPorciento(),
                    'mes' => $entity->getMes(),
                    'anno' => $entity->getAnno()
                );
            }
            if ($datos[0]['lectura_pico'] > $datos_lecturas['lectura_pico'] || $datos[0]['lectura_mad'] > $datos_lecturas['lectura_mad'] || $datos[0]['lectura_dia'] > $datos_lecturas['lectura_dia'] || $datos[0]['lectura_reactivo'] > $datos_lecturas['lectura_reactivo']) {
                $lectura_menor = true;
            }

        } else {
            $lectura_menor = false;
        }

        return $lectura_menor;

    }

    public function addAutolecturaTresescalasAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $serviciosid = trim($request->get('serviciosid'));
        $lectura_pico = trim($request->get('lectura_pico'));
        $lectura_mad = trim($request->get('lectura_mad'));
        $lectura_dia = trim($request->get('lectura_dia'));

        $maxpico = trim($request->get('lectura_pico_maxD'));
        $maxdadru = trim($request->get('lectura_mad_maxD'));
        $maxdia = trim($request->get('lectura_dia_maxD'));

        $lectura_pico_maxD = (isset($maxpico)) ? $maxpico : 0;

        $lectura_mad_maxD = isset($maxdadru) ? $maxdadru : 0;

        $lectura_dia_maxD = isset($maxdia) ? $maxdia : 0;

        $lectura_reactivo = trim($request->get('lectura_reactivo'));

        $fecha_lectura = date_create_from_format('d/m/Y', trim($request->get('fecha_lectura')));
        $mes = $fecha_lectura->format('m');
        $anno = $fecha_lectura->format('Y');

        $datos_lecturas = array(
            'lectura_pico' => $lectura_pico,
            'lectura_mad' => $lectura_mad,
            'lectura_dia' => $lectura_dia,
            'lectura_pico_maxD' => $lectura_pico_maxD,
            'lectura_mad_maxD' => $lectura_mad_maxD,
            'lectura_dia_maxD' => $lectura_dia_maxD,
            'lectura_reactivo' => $lectura_reactivo,
            'fecha_lectura' => $fecha_lectura,
            'servicioid' => $serviciosid
        );

        $var = $this->validarlecturasAction($datos_lecturas);

        $datos_plan = array();

        $fecha_lectura_ = date_create_from_format('d/m/Y', trim($request->get('fecha_lectura')));

        $entities_servicio = $em->getRepository('PortadoresBundle:DesgloseServicios')->findBy(array('idservicio' => $serviciosid, 'mes' => $mes, 'anno' => $anno));
        $entities = $em->getRepository('PortadoresBundle:AutolecturaTresescalas')->findBy(array('fechaLectura' => $fecha_lectura_, 'serviciosid' => $serviciosid));

        if (!$entities_servicio) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Este servicio no se ha desglosado. Desglose el servicio primero.'));
        }

        $entities_desglose = $em->getRepository('PortadoresBundle:DesgloseElectricidad')->findBy(array(
            'fechaDesglose' => $fecha_lectura_,
            'iddesgloseServicios' => $entities_servicio[0]->getId()
        ));

        if (!$entities_desglose) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Este servicio no tiene un plan asignado para la fecha seleccionada.Desglose el servicio primero.'));
        }

        foreach ($entities_desglose as $entity) {
            $datos_plan[] = array(
                'plan_pico' => $entity->getPlanPico(),
                'plan_diario' => $entity->getPlanDiario(),
                'perdidasT' => $entity->getPerdidast()
            );
        }

        if ($entities) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'La Autolectura correspondiente al dia de hoy fue realizada.'));
        }

        if ($var) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Las lecturas tomadas no pueden ser menores que las del día anterior. Por favor revise'));
        }

        $entity = new AutolecturaTresescalas();
        $entity->setFechaLectura(date_create_from_format('d/m/Y', trim($request->get('fecha_lectura'))));
        $entity->setServiciosid($em->getRepository('PortadoresBundle:Servicio')->find($serviciosid));
        $entity->setLecturaReactivo($lectura_reactivo);
        $entity->setLecturaDia($lectura_dia);
        $entity->setLecturaMad($lectura_mad);
        $entity->setLecturaPico($lectura_pico);
        $entity->setLecturaMaxdemDia($lectura_dia_maxD);
        $entity->setLecturaMaxdemMad($lectura_mad_maxD);
        $entity->setLecturaMaxdemPico($lectura_pico_maxD);
        $entity->setMes($fecha_lectura->format('n'));
        $entity->setAnno($fecha_lectura->format('Y'));
        $entity->setConsumoTotalPlan($datos_plan[0]['plan_diario']);
        $entity->setConsumoPicoPlan($datos_plan[0]['plan_pico']);
        $entity->setCambioMetro(false);

        try {
            $em->persist($entity);
            $datos = array(
                'id' => $entity->getId(),
                'serviciosid' => $serviciosid,
                'nombreserviciosid' => $entity->getServiciosid()->getNombreServicio(),
                'lectura_pico' => $lectura_pico,
                'lectura_mad' => $lectura_mad,
                'lectura_dia' => $lectura_dia,
                'lectura_pico_maxD' => $lectura_pico_maxD,
                'lectura_mad_maxD' => $lectura_mad_maxD,
                'lectura_dia_maxD' => $lectura_dia_maxD,
                'lectura_reactivo' => $lectura_reactivo,
                'fecha_lectura' => $fecha_lectura_,
                'perdidasT' => $datos_plan[0]['perdidasT']
            );
            $this->consumoAction($datos);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Autolectura Realizada con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function damemesAction($Nromes)
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

    public function getIsLastLectMayorAction(Request $request)
    {
        $servicio = $request->get('servicio');

        $fecha_lectura = $request->get('fecha');
        $fechaObj = date_create_from_format('Y-m-d', $fecha_lectura);
        $anno = $fechaObj->format('Y');

        $sql = "select * from datos.autolectura_tresescalas as aut
                where (select extract(Year from aut.fecha_lectura)) = $anno and aut.serviciosid ='$servicio'
                order by aut.fecha_lectura DESC";
        $datos = $this->getDoctrine()->getConnection()->fetchAll($sql);
        $succes = $lastLect = ($datos[0]['fecha_lectura'] > $fecha_lectura) ? false : true;

        return new JsonResponse(array('success' => $succes, 'valor' => $lastLect));
    }

    public function modAutolecturaTresescalasAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = $request->get('id');
        $lectura_pico = trim($request->get('lectura_pico'));
        $lectura_mad = trim($request->get('lectura_mad'));
        $lectura_dia = trim($request->get('lectura_dia'));
        $lectura_pico_maxD = trim($request->get('lectura_pico_maxD'));
        $lectura_mad_maxD = trim($request->get('lectura_mad_maxD'));
        $lectura_dia_maxD = trim($request->get('lectura_dia_maxD'));
        $lectura_reactivo = trim($request->get('lectura_reactivo'));
        $serviciosid = trim($request->get('servicio'));
        $fecha_lectura = new \DateTime(trim($request->get('fecha_autolectura')));

        $entity = $em->getRepository('PortadoresBundle:AutolecturaTresescalas')->find($id);
        $entity->setLecturaReactivo($lectura_reactivo);
        $entity->setLecturaDia($lectura_dia);
        $entity->setLecturaMad($lectura_mad);
        $entity->setLecturaPico($lectura_pico);
        $entity->setLecturaMaxdemDia($lectura_dia_maxD);
        $entity->setLecturaMaxdemMad($lectura_mad_maxD);
        $entity->setLecturaMaxdemPico($lectura_pico_maxD);

        try {
            $em->persist($entity);
            $em->flush();

            $datos_lecturas = array(
                'id' => $entity->getId(),
                'serviciosid' => $serviciosid,
                'nombreserviciosid' => $entity->getServiciosid()->getNombreServicio(),
                'lectura_pico' => $lectura_pico,
                'lectura_mad' => $lectura_mad,
                'lectura_dia' => $lectura_dia,
                'lectura_pico_maxD' => $lectura_pico_maxD,
                'lectura_mad_maxD' => $lectura_mad_maxD,
                'lectura_dia_maxD' => $lectura_dia_maxD,
                'lectura_reactivo' => $lectura_reactivo,
                'fecha_lectura' => $fecha_lectura);

            $this->consumoAction($datos_lecturas);
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Autolectura Modificada  con éxito.'));
            return $response;
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }

    }

    //Consumo de electricidad sin perdida de transformacion
    public function consumoAction($datos_lecturas)
    {

        $em = $this->getDoctrine()->getManager();

        $fechaayer = $datos_lecturas['fecha_lectura'];
        $Fecha = $fechaayer->sub(new \DateInterval('P1D'));

        $entity = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:AutolecturaTresescalas')->findOneBy(
            array(
                'fechaLectura' => $Fecha,
                'serviciosid' => $datos_lecturas['serviciosid']
            )
        );

        if ($entity) {
            $consumo = ((double)$datos_lecturas['lectura_reactivo'] - (double)$entity->getLecturaReactivo());

            $consumo_total_madrugada = round($datos_lecturas['lectura_mad'] - (double)$entity->getLecturaMad(), 3);

            $consumo_total_pico = round($datos_lecturas['lectura_pico'] - (double)$entity->getLecturaPico(), 3);

            $consumo_total_dia = round($datos_lecturas['lectura_dia'] - $entity->getLecturaDia(), 3);

            $consumo_total_real = round((($datos_lecturas['lectura_mad'] - (double)$entity->getLecturaMad()) + ($datos_lecturas['lectura_pico'] - (double)$entity->getLecturaPico()) + ($datos_lecturas['lectura_dia'] - $entity->getLecturaDia())), 3);

            $idayer = $entity->getId();
            $entitynew = $em->getRepository('PortadoresBundle:AutolecturaTresescalas')->find($idayer);
            $entitynew->setconsumo($consumo);
            $entitynew->setConsumoTotalMad($consumo_total_madrugada);
            $entitynew->setConsumoTotalDia($consumo_total_dia);
            $entitynew->setConsumoPicoReal($consumo_total_pico);
            $entitynew->setConsumoTotalReal($consumo_total_real);
            $entitynew->setUltima(false);
            $em->persist($entitynew);
            $em->flush();
        } else {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function acumuladosAction(Request $request)
    {
        $session = $request->getSession();
        $mes_abierto = $session->get('selected_month');
        $anno_abierto = $session->get('selected_year');
        $acumulado_real = 0;
        $acumulado_plan = 0;
        $acumulado_real_pico = 0;
        $acumulado_plan_pico = 0;

        $em = $this->getDoctrine()->getManager();

        $fecha_inicial = $anno_abierto . '-' . $mes_abierto . '-01';
        $fecha_final = FechaUtil::getUltimoDiaMes($mes_abierto, $anno_abierto);

        $idservicios = $request->get('id');
        $entities_auto = $em->getRepository('PortadoresBundle:AutolecturaTresescalas')->getAutolecturaTresescalasMes($idservicios, $fecha_inicial, $fecha_final);

        $valores = array();
        foreach ($entities_auto as $entity) {
            /**@var AutolecturaTresescalas $entity */
            $servicio = $em->getRepository('PortadoresBundle:Servicio')->find($idservicios);
            $perdidas_transf = $this->Perdida_TAction($servicio, $entity);
            $valores[] = array(
                'id' => $entity->getId(),
                'plan' => round($entity->getConsumoTotalPlan(), 2),
                'real' => round(floatval($entity->getConsumoTotalReal()) + $perdidas_transf, 2),
                'real_pico' => round(floatval($entity->getConsumoPicoReal()) + ($perdidas_transf / 6), 2),
                'plan_pico' => round($entity->getConsumoPicoPlan(), 2),
            );
        }

        for ($i = 0, $iMax = \count($valores); $i < $iMax; $i++) {
            if ($i < $iMax) {
                $acumulado_real += $valores[$i]['real'];
                $acumulado_real_pico += $valores[$i]['real_pico'];
            }
            if($i < $iMax - 1){
                $acumulado_plan += $valores[$i]['plan'];
                $acumulado_plan_pico += $valores[$i]['plan_pico'];
            }
        }

        $acumulados = array(
            'acumulado_real' => $acumulado_real,
            'acumulado_plan' => $acumulado_plan,
            'acumulado_real_pico' => $acumulado_real_pico,
            'acumulado_plan_pico' => $acumulado_plan_pico,
            'diferencia' => $acumulado_plan - $acumulado_real,
            '_plan_real' => ($acumulado_plan != null) ? round(($acumulado_real / $acumulado_plan) * 100, 2) : '0');

        return new JsonResponse(array('array' => $acumulados));
    }

    public function cambiometroAction(Request $request)
    {

        $session = $request->getSession();
        $mes = $session->get('current_month');

        $entities = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:AutolecturaTresescalas')->findBy(
            array(
                'mes' => $mes
            )
        );

        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'fecha_lectura' => $entity->getFechaLectura()->format('D,j M, Y '),
                'serviciosid' => $entity->getServiciosid()->getId(),
                'nombreserviciosid' => $entity->getServiciosid()->getNombreServicio(),
                'lectura_pico' => $entity->getLecturaPico(),
                'lectura_mad' => $entity->getLecturaMad(),
                'lectura_dia' => $entity->getLecturaDia(),
                'lectura_reactivo' => $entity->getLecturaReactivo(),
                'lectura_pico_maxD' => $entity->getLecturaMaxdemPico(),
                'lectura_mad_maxD' => $entity->getLecturaMaxdemMad(),
                'lectura_dia_maxD' => $entity->getLecturaMaxdemDia(),
                'consumo' => $entity->getConsumo(),
                'consumo_total_mad' => $entity->getConsumoTotalMad(),
                'consumo_total_dia' => $entity->getConsumoTotalDia(),
                'consumo_total_real' => $entity->getConsumoTotalReal(),
                'consumo_total_porciento' => $entity->getConsumoTotalPorciento(),
                'consumo_total_plan' => $entity->getConsumoTotalPlan(),
                'consumo_pico_plan' => $entity->getConsumoPicoPlan(),
                'consumo_pico_real' => $entity->getConsumoPicoReal(),
                'consumo_pico_porciento' => $entity->getConsumoPicoPorciento(),
                'mes' => $entity->getMes(),
                'anno' => $entity->getAnno()
            );
        }

        $lecturas = 0;
        $promedio = 0;
        for ($i = 0, $iMax = \count($_data); $i < $iMax; $i++) {
            $lecturas += $_data[$i]['lectura_reactivo'];
        }

        $promedio = round($lecturas / \count($_data));
        return new JsonResponse(array('rows' => $promedio));
        $Fecha = $fecha_lectura_->sub(new \DateInterval('P1D'));
        $Fechaayer = $Fecha->format('Y-m-d');
        $entities = $em->getRepository('PortadoresBundle:AutolecturaTresescalas')->findByFechaLectura($Fechaayer);

        foreach ($entities as $entity) {
            $datos_plan = array(
                'id' => $entity->getId(),
                'serviciosid' => $$entity->getServiciosid()->getId(),
            );
        }
        $entityinsert = new AutolecturaTresescalas();
        $entityinsert->setServiciosid($em->getRepository('PortadoresBundle:Servicio')->find($datos_plan['serviciosid']));
        $entityinsert->setLecturaDia($_datos['promedio_dia']);
        $entityinsert->setLecturaPico($_datos['promedio_pico']);
        $entityinsert->setLecturaMad($_datos['promedio_madru']);
        $entityinsert->setLecturaReactivo($_datos['promedio_reactivo']);
        $entityinsert->setLecturaMaxdemMad($_datos['promedio_mad_MaxD']);
        $entityinsert->setLecturaMaxdemPico($_datos['promedio_picoMaxD']);
        $entityinsert->setLecturaMaxdemDia($_datos['promedio_dia_MaxD']);

        $entityinsert->setConsumoTotalMad($_datos['promedio_consumo_total_mad']);
        $entityinsert->setConsumoTotalDia($_datos['promedio_consumo_total_dia']);
        $entityinsert->setConsumoTotalReal($_datos['promedio_consumo_total_real']);
        $entityinsert->setConsumoTotalPorciento($_datos['promedio_consumo_total_porciento']);
        $entityinsert->setConsumoTotalPlan($_datos['promedio_total_plan']);
        $entityinsert->setConsumoPicoPlan($_datos['promedio_pico_plan']);
        $entityinsert->setConsumoPicoReal($_datos['promedio_pico_real']);
        $entityinsert->setConsumoPicoPorciento($_datos['promedio_pico_porciento']);
        $entityinsert->setConsumo($_datos['promedio_consumo']);
        $entityinsert->setCambioMetro(true);
        try {
            $em->persist($entityinsert);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Cambio realizado.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));

        }
    }

    public function factorPAction($servicioid)
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $mes_abierto = $session->get('current_month');
        $anno_abierto = $session->get('current_year');

        $fecha_inicial = $anno_abierto . '-' . $mes_abierto . '-01';
        $fecha_final = FechaUtil::getUltimoDiaMes($mes_abierto, $anno_abierto);
        $entities = $em->getRepository('PortadoresBundle:AutolecturaTresescalas')->getAutolecturaTresescalasMes($servicioid, $fecha_inicial, $fecha_final);
        $_data = array();

        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'fecha_lectura' => $entity->getFechaLectura()->format('D,j M, Y '),
                'serviciosid' => $entity->getServiciosid()->getId(),
                'nombreserviciosid' => $entity->getServiciosid()->getNombreServicio(),
                'lectura_pico' => $entity->getLecturaPico(),
                'lectura_mad' => $entity->getLecturaMad(),
                'lectura_dia' => $entity->getLecturaDia(),
                'lectura_reactivo' => $entity->getLecturaReactivo(),
                'lectura_pico_maxD' => $entity->getLecturaMaxdemPico(),
                'lectura_mad_maxD' => $entity->getLecturaMaxdemMad(),
                'lectura_dia_maxD' => $entity->getLecturaMaxdemDia(),
                'consumo' => $entity->getConsumo(),
                'consumo_total_mad' => $entity->getConsumoTotalMad(),
                'consumo_total_dia' => $entity->getConsumoTotalDia(),
                'consumo_total_real' => $entity->getConsumoTotalReal(),
                'consumo_total_porciento' => $entity->getConsumoTotalPorciento(),
                'consumo_total_plan' => $entity->getConsumoTotalPlan(),
                'consumo_pico_plan' => $entity->getConsumoPicoPlan(),
                'consumo_pico_real' => $entity->getConsumoPicoReal(),
                'consumo_pico_porciento' => $entity->getConsumoPicoPorciento(),
                'mes' => $entity->getMes(),
                'anno' => $entity->getAnno()
            );
        }
        $energiareactiva = 0;
        $energiaactiva = 0;

        for ($i = 0, $iMax = \count($_data); $i < $iMax; $i++) {
            $energiareactiva += $_data[$i]['consumo'];
            $energiaactiva += $_data[$i]['consumo_total_real'];

        }
        $datos_factor = array();
        if ($energiaactiva > 0) {
            $factorP = round(cos(atan($energiareactiva / $energiaactiva)), 3);

            if ($factorP > 0.92) {
                if ($factorP > 0.96) {
                    $factorPnew = 0.96;
                    $datos_factor = array(
                        'factorP' => $factorPnew,
                        'descrip' => 'Bonificación'
                    );
                }
                $datos_factor = array(
                    'factorP' => $factorP,
                    'descrip' => 'Bonificación'
                );
            } else if ($factorP < 0.90) {
                $datos_factor = array(
                    'factorP' => $factorP,
                    'descrip' => 'Penalización'
                );
            }
        } else {
            $factorP = -1;
            $datos_factor = array(
                'factorP' => $factorP,
                'descrip' => 'Falta Energía Activa'
            );
        }
        return $datos_factor;
    }

    public function penalizacionMDAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $lectura_dia = $request->get('lectura_dia');
        $lectura_pico = $request->get('lectura_pico');
        $lectura_mad = $request->get('lectura_mad');
        $servicioid = $request->get('servicioid');

        $entity = $em->getRepository('PortadoresBundle:Servicio')->find($servicioid);
        $var = array(
            'id' => $entity->getId(),
            'nombre_servicio' => $entity->getNombreServicio(),
            'MaximaDemandaContratada' => $entity->getMaximaDemandaContratada(),

        );
        $valores = array($lectura_dia, $lectura_pico, $lectura_mad);

        $mayorvalor_ = max($valores);

        if ($mayorvalor_ > $var['MaximaDemandaContratada']) {
            $precio = array(
                'valor' => ($mayorvalor_ - $var['MaximaDemandaContratada']) * 21,
                'descrip' => 'DEMANDA EXCEDIDA'
            );
        } else
            $precio = array(
                'valor' => ' ',
                'descrip' => 'DEMANDA NO EXCEDIDA'
            );
        return new JsonResponse(array('rows' => $precio));
    }

//formula segun la UNE  IT = (k x CVP+CFP) + (k x CVD+CFD) + (k x CVM+CFM) + (k x PT)
//Dice el especialista de portadores que en la Tarifa M1-A se le cobra penalizacion por la mxm demanda real de las tres lecturas
//En contra de la resolucion. Por tanto no cambiar si dice otra cosa.

    public function importefacturaAction(Request $request)
    {

        //'- Energía activa registrada en el metro contador según las escalas correspondientes al Día, Pico y Madrugada << MÁS >> Máxima Demanda Contratada y cobrada al precio de la tarifa
        /// << MÁS >> Pérdidas de Transformación << MÁS >> Penalización por incumplimiento de la máxima demanda
        /// << MÁS  o MENOS>> Penalización o Bonificación por Factor de Potencia
        $session = $request->getSession();
        $mes_abierto = $session->get('selected_month');
        $anno_abierto = $session->get('selected_year');
        $em = $this->getDoctrine()->getManager();

        $servicios = array();

        $_user = $this->get('security.context')->getToken()->getUser();
        $dominio = $em->getRepository('MySecurityBundle:Dominio')->findByUsersid($_user->getId());
        $_unidades[] = $dominio[0]->getUserUnidadid()->getId();
        $dominio_unidades = $em->getRepository('MySecurityBundle:DominioUnidades')->findByDominioid($dominio[0]->getId());
        foreach ($dominio_unidades as $unidad) {
            $_unidades[] = $unidad->getUnidadid()->getId();
        }


        $qb = $em->createQueryBuilder();
        $qb->select('servicios')
            ->from('PortadoresBundle:Servicio', 'servicios')
            ->Where($qb->expr()->in('servicios.nunidadid', $_unidades))
            ->andWhere($qb->expr()->eq('servicios.visible', 'true'))
            ->orderBy('servicios.nombreServicio', 'ASC');

        $entities = $qb->getQuery()->getResult();

        foreach ($entities as $entity) {
            if ($entity->getServicioMayor()) {
                $servicioid = $entity->getId();
                $banco = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:BancoTransformadores')->findOneBy(array('id' => $entity->getCapacBancoTransformadores()));
                $turno = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:TurnoTrabajo')->findOneBy(array('id' => $entity->getTurnosTrabajo()));

                $cap_total_banco = $this->CapacidadTotalBancoByServicio($servicioid);
                $sql = "select     
                       max(lectura_maxdem_mad) as mad, 
                       max(lectura_maxdem_pico) as pico, 
                       max(lectura_maxdem_dia) as dia
                       from datos.autolectura_tresescalas as a where a.mes = '$mes_abierto' and a.anno = '$anno_abierto' and a.serviciosid = '$servicioid'";

                $consulta = $this->getDoctrine()->getConnection()->fetchAll($sql);

                $mxm_dmd_real = max($consulta[0]);

                $servicios[] = array(
                    'id' => $entity->getId(),
                    'nombre_servicio' => $entity->getNombreServicio(),
                    'codigo_cliente' => $entity->getCodigoCliente(),
                    'factor_metrocontador' => $entity->getFactorMetrocontador(),
                    'MaximaDemandaContratada' => $entity->getMaximaDemandaContratada(),
                    'control' => $entity->getControl(),
                    'ruta' => $entity->getRuta(),
                    'folio' => $entity->getFolio(),
                    'direccion' => $entity->getDireccion(),
                    'factor_combustible' => $entity->getFactorCombustible(),
                    'indice_consumo' => $entity->getIndiceConsumo(),
                    'consumo_prom_anno' => $entity->getConsumoPromedioAnno(),
                    'consumo_prom_plan' => $entity->getConsumoPromedioPlan(),
                    'consumo_prom_real' => $entity->getConsumoPromedioReal(),
                    'capac_banco_transf' => $cap_total_banco,
                    'tipo_servicio' => $entity->getServicioElectrico(),
                    'id_turno_trabajo' => $turno->getId(),
                    'mxm_dem_real' => $mxm_dmd_real,
                    'turno_trabajo' => $turno->getTurno(),
                    'turno_trabajo_horas' => $turno->getHoras(),
                    'nunidadid' => $entity->getNunidadid()->getId(),
                    'nombreunidadid' => $entity->getNunidadid()->getNombre(),
                    'provicianid' => $entity->getProvinciaid()->getId(),
                    'nombreprovicianid' => $entity->getProvinciaid()->getNombre(),
                    'tarifaid' => $entity->getNtarifaid()->getId(),
                    'nombretarifaid' => $entity->getNtarifaid()->getNombre(),
                    'nactividadid' => $entity->getNactividadid()->getId(),
                    'nombrenactividadid' => $entity->getNactividadid()->getNombre(),
                    'num_nilvel_actividadid' => $entity->getNactividadid()->getUmActividad()->getId(),
                    'nombreum_nilvel_actividadid' => $entity->getNactividadid()->getUmActividad()->getNivelActividad());
            }
        }

        $importe_servicios = array();
        $fecha_inicial = $anno_abierto . '-' . $mes_abierto . '-01';
        $fecha_final = FechaUtil::getUltimoDiaMes($mes_abierto, $anno_abierto);

        for ($i = 0, $iMax = \count($servicios); $i < $iMax; $i++) {
            $entities = $em->getRepository('PortadoresBundle:AutolecturaTresescalas')->getAutolecturaTresescalasMes($servicios[$i]['id'], $fecha_inicial, $fecha_final);
            $_data = array();
            $_data_lecturaMXD_pico = array();
            $_data_lecturaMXD_dia = array();
            $_data_lecturaMXD_madrugada = array();

            $consumo_total_mad = 0;
            $consumo_total_dia = 0;
            $consumo_total_pico = 0;
            $consumo_reactivo = 0;
            $consumo_total = 0;
            foreach ($entities as $entity) {
                $perdidas = $this->Perdida_TAction($entity->getServiciosid(), $entity);

                $_data_lecturaMXD_pico[] = array(
                    'lectura_pico_maxD' => $entity->getLecturaMaxdemPico());
                $_data_lecturaMXD_dia[] = array(
                    'lectura_dia_maxD' => $entity->getLecturaMaxdemDia());
                $_data_lecturaMXD_madrugada[] = array(
                    'lectura_mad_maxD' => $entity->getLecturaMaxdemMad());

                $consumo_total_mad += round($entity->getConsumoTotalMad() + (round($perdidas, 2) / 3), 2);
                $consumo_total += round($entity->getConsumoTotalMad() + $entity->getConsumoTotalDia() + $entity->getConsumoPicoReal(), 2);
                $consumo_total_dia += round($entity->getConsumoTotalDia() + (round($perdidas, 2) / 2), 2);
                $consumo_total_pico += round($entity->getConsumoPicoReal() + (round($perdidas, 2) / 6), 2);
                $consumo_reactivo += $entity->getConsumo();

                $_data[] = array(
                    'id' => $entity->getId(),
                    'fecha_lectura' => $entity->getFechaLectura()->format('D,j M, Y '),
                    'serviciosid' => $entity->getServiciosid()->getId(),
                    'nombreserviciosid' => $entity->getServiciosid()->getNombreServicio(),
                    'lectura_pico' => $entity->getLecturaPico(),
                    'lectura_mad' => $entity->getLecturaMad(),
                    'lectura_dia' => $entity->getLecturaDia(),
                    'lectura_reactivo' => $entity->getLecturaReactivo(),
                    'lectura_pico_maxD' => $entity->getLecturaMaxdemPico(),
                    'lectura_mad_maxD' => $entity->getLecturaMaxdemMad(),
                    'lectura_dia_maxD' => $entity->getLecturaMaxdemDia(),
                    'consumo' => $entity->getConsumo(),
                    'perdidas' => $perdidas,
                    'consumo_total_mad' => $entity->getConsumoTotalMad(),
                    'consumo_total_dia' => $entity->getConsumoTotalDia(),
                    'consumo_total_real' => $entity->getConsumoTotalReal(),
                    'consumo_total_porciento' => $entity->getConsumoTotalPorciento(),
                    'consumo_total_plan' => $entity->getConsumoTotalPlan(),
                    'consumo_pico_plan' => $entity->getConsumoPicoPlan(),
                    'consumo_pico_real' => $entity->getConsumoPicoReal(),
                    'consumo_pico_porciento' => $entity->getConsumoPicoPorciento(),
                    'mes' => $entity->getMes(),
                    'anno' => $entity->getAnno()
                );
            }

            if ($_data) {
                if ($servicios[$i]['nombretarifaid'] == 'M1-A') {

                    //factor de combustible  del servicio
                    $factorK = $servicios[$i]['factor_combustible'];

                    $precioKWh_dia = 0.0241;
                    $precioKWh_mad = 0.0161;
                    $precioKWh_pico = 0.0481;

                    $tarifaid = $servicios[$i]['tarifaid'];

                    // Para horario pico
                    $valor_horario_pico = ($precioKWh_pico * $factorK + 0.064) * $consumo_total_pico;
                    //Para horario madrugada
                    $valor_horario_madrugada = ($precioKWh_mad * $factorK + 0.064) * $consumo_total_mad;
                    //Para horario dia
                    $valor_horario_dia = ($precioKWh_dia * $factorK + 0.064) * $consumo_total_dia;


                    //Demanda contratada para  horario pico en caso de esta tarifa
                    $DemandaContratada = $servicios[$i]['MaximaDemandaContratada'];

                    $idserv = $servicios[$i]['id'];

                    $MXD_total = $servicios[$i]['mxm_dem_real'];

                    //PENALIZACION POR MAXIMA DEMANDA
                    $cargo_exceso = 0;
                    if ($MXD_total > $DemandaContratada) {
                        $valor_exceso = $MXD_total - $DemandaContratada;
                        //Cargo fijo mensual  por la demanda
                        $cargo_fijo_MXD = $DemandaContratada * 7;
                        //Exceso se cobra al triple del precio (21)
                        $cargo_exceso = $valor_exceso * 21;
                        $valor_MXD = $cargo_fijo_MXD + $cargo_exceso;
                    } else {
                        $cargo_fijo_MXD = $DemandaContratada * 7;
                        $valor_MXD = $cargo_fijo_MXD;
                    }

                    //-----FACTURACION DE  CARGO FIJO MENSUAL---------\\
                    $importe_factura_M1_A_cargo_fijo_mensual = $valor_horario_pico + $valor_horario_madrugada + $valor_horario_dia + $valor_MXD;

                    //---FACTURACION DE CARGO FIJO MENSUAL MAS LAS PENALIZACIONES ---\\
                    $importe_factura_M1_A = $importe_factura_M1_A_cargo_fijo_mensual;

                    //---BONIFICACION O PENALIZACION POR FACTOR DE POTENCIA---\\
                    $factorP = round(cos(atan($consumo_reactivo / $consumo_total)), 2);

                    $bonificacion = 0;
                    $penalizacion = 0;
                    if ($factorP >= 0.92) {
                        $valor_bonificacion = (0.92 - $factorP) / $factorP;
                        $bonificacion = $importe_factura_M1_A_cargo_fijo_mensual * $valor_bonificacion;

                        //IMPORTE  DE L AFACTURA PARA EL SERVICIOS  DADA  TU TARIFA---
                        $IMPORTE_FACTURA_M1_A = $importe_factura_M1_A + $bonificacion;

                        //---------------------------------------
                    } elseif ($factorP < 0.90) {
                        $valor_penaliza = 0.90 - $factorP / $factorP;
                        $penalizacion = $importe_factura_M1_A_cargo_fijo_mensual * $valor_penaliza;

                        //IMPORTE  DE L AFACTURA PARA EL SERVICIOS  DADA  TU TARIFA--
                        $IMPORTE_FACTURA_M1_A = $importe_factura_M1_A + $penalizacion;
                    } elseif ($factorP > 0.96) {
                        $valor_bonificacion = (0.92 - 0.96) / 0.96;
                        $bonificacion = $importe_factura_M1_A_cargo_fijo_mensual * $valor_bonificacion;
                        $IMPORTE_FACTURA_M1_A = $importe_factura_M1_A + $bonificacion;
                    }

                    $importe_servicios[] = array(
                        'servicio' => $servicios[$i]['nombre_servicio'],
                        'tarifa' => $servicios[$i]['nombretarifaid'],
                        'bonificacion' => round($bonificacion, 2) * -1,
                        'penalizacion' => round($penalizacion, 2),
                        'pena_demanda' => round($cargo_exceso, 2),
                        'factorP' => round($factorP, 2),
                        'importe' => round($IMPORTE_FACTURA_M1_A, 2),
                        'cargo_fijo_mensual' => round($valor_MXD, 2)
                    );
                }
            }
        }

        return new JsonResponse(array('importe_factura' => $importe_servicios));

    }

    public function Perdida_TAction($servicio, $autolectura)
    {
        $em = $this->getDoctrine()->getManager();
        $servicioid = $servicio->getId();

        $sql1 = "select s.cap_transf1,
                        s.cap_transf2,
                        s.cap_transf3,
                        s.cap_transf4,
                        s.cap_transf5
                from nomencladores.servicio as s where s.id = '$servicioid'";

        $consulta1 = $this->getDoctrine()->getConnection()->fetchAll($sql1);

        $arrayPfe = array();
        $arrayPCu = array();
        for ($i = 1; $i <= 5; $i++) {
            $keyPfe = 'cap_transf' . $i;
            $trans = ($consulta1[0][$keyPfe] !== null) ? $em->getRepository('PortadoresBundle:BancoTransformadores')->find($consulta1[0][$keyPfe]) : null;
            if ($trans) {
                $arrayPfe[] = $trans->getPfe();
                $arrayPCu[] = $trans->getPcu();
            }
        }

        $perdidas_dia = 0;
        for ($i = 0, $iMax = \count($arrayPfe); $i < $iMax; $i++) {
            $kwh = $autolectura->getConsumoTotalMad() + $autolectura->getConsumoTotalDia() + $autolectura->getConsumoPicoReal();
            $kvarh = $autolectura->getConsumo();
            $factor_potencia = ($kwh !== null && $kwh !== 0) ? round(cos(atan($kvarh / $kwh)), 3) : '0';
            $servicio = $em->getRepository('PortadoresBundle:Servicio')->find($servicioid);
            $turno = $em->getRepository('PortadoresBundle:TurnoTrabajo')->find($servicio->getTurnosTrabajo());
            $horas_turno = $turno->getHoras();

            $T1 = $horas_turno;
            $t3 = 24;
            $kvar = round(($T1 !== null) ? $kwh / $T1 * $factor_potencia : '0', 3);
            $kvan = $servicio->getCapBancoMayor();
            $perdidas_transf = ($kvarh !== null) ? $arrayPfe[$i] * $t3 + (($kvar / $kvan) ** 3) * $arrayPCu[$i] * $T1 : '0';
            $perdidas_dia += $perdidas_transf;
        }
        return round($perdidas_dia, 3);
    }

    public function existedesgloseAction(Request $request)
    {
        $idservicio = $request->get('servicio');
        $mes = $request->get('mes');
        $datos_plan = array();

        $existe = false;
        $entities_desglose = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:DesgloseServicios')->findBy(array(
            'idservicio' => $idservicio,
            'mes' => $mes
        ));

        if (!$entities_desglose) {
            return new JsonResponse(array('success' => false, 'valor' => $existe));
        }

        foreach ($entities_desglose as $entity) {
            $datos_plan = array(
                'id' => $entity->getId(),
            );
        }
        $entities_desglose1 = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:DesgloseElectricidad')->findBy(array(
            'iddesgloseServicios' => $datos_plan['id'],
            'mes' => $mes
        ));
        if ($entities_desglose1) {
            $existe = true;
        }
        return new JsonResponse(array('success' => true, 'valor' => $existe));

    }

    public function cleanautolecturasAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $servicio_id = $request->get('servicio_id');
        $fecha_lectura = $request->get('fecha_lectura');

        $entity = $em->getRepository('PortadoresBundle:AutolecturaTresescalas')->cleanAllAutolecturas($servicio_id, $fecha_lectura);

        $sql = "select max(at.fecha_lectura)as fecha, max(at.id) as id from datos.autolectura_tresescalas as at where at.serviciosid = '$servicio_id'";
        $consulta = $this->getDoctrine()->getConnection()->fetchAll($sql);

        $autolectura = $em->getRepository('PortadoresBundle:AutolecturaTresescalas')->find($consulta[0]['id']);
        /**@var AutolecturaTresescalas $autolectura */
        $autolectura->setConsumo(0.00);
        $autolectura->setConsumoPicoReal(0.00);
        $autolectura->setConsumoTotalDia(0.00);
        $autolectura->setConsumoTotalMad(0.00);
        $autolectura->setConsumoTotalReal(0.00);
        $autolectura->setConsumoTotalReal(0.00);
        $autolectura->setUltima(true);
        $em->persist($autolectura);
        $em->flush();

        $response = new JsonResponse();
        $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Los datos han sido Eliminados.Introdúscalos nuevamente'));
        return $response;

    }

    public function bitacoraAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $mes = $request->get('mes');
        $anno = $request->get('anno');
        if ($mes !== 12) {
            $mes_siguiente = $mes + 1;
            $fechaFin = $anno . '-' . $mes_siguiente . '-01';
        } else {
            $mes_siguiente = '01';
            $fechaFin = $anno + 1 . '-' . $mes_siguiente . '-01';
        }
        $fecha = $anno . '-' . $mes . '-01';

        $servicio_id = trim($request->get('servicio'));

        $valores = array();
        $entities_auto = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:AutolecturaTresescalas')->getAutolecturbyMesAbierto($servicio_id, $fecha, $fechaFin);
        
        /**@var AutolecturaTresescalas $entities_auto */
        for ($i = 0, $iMax = sizeof($entities_auto); $i < $iMax; $i++) {
            $servicio = $em->getRepository('PortadoresBundle:Servicio')->find($servicio_id);
            $kwh = $entities_auto[$i]->getConsumoTotalMad() + $entities_auto[$i]->getConsumoTotalDia() + $entities_auto[$i]->getConsumoPicoReal();
            $kvarh = $entities_auto[$i]->getConsumo();

            $factor_potencia = ($kwh !== null && $kwh !== 0) ? round(cos(atan($kvarh / $kwh)), 3) : '0';
            $perdidas_transf = $this->Perdida_TAction($servicio, $entities_auto[$i]);
            $valores[] = array(
                'lectura_pico' => $entities_auto[$i]->getLecturaPico(),
                'lectura_mad' => $entities_auto[$i]->getLecturaMad(),
                'lectura_dia' => $entities_auto[$i]->getLecturaDia(),
                'lectura_react' => $entities_auto[$i]->getLecturaReactivo(),

                'consumo_mad' => ($perdidas_transf !== 0) ? $entities_auto[$i]->getConsumoTotalMad() + ($perdidas_transf / 3) : $entities_auto[$i]->getConsumoTotalMad(),
                'consumo_dia' => ($perdidas_transf !== 0) ? $entities_auto[$i]->getConsumoTotalDia() + ($perdidas_transf / 2) : $entities_auto[$i]->getConsumoTotalDia(),
                'consumo_pico' => ($perdidas_transf !== 0) ? $entities_auto[$i]->getConsumoPicoReal() + ($perdidas_transf / 6) : $entities_auto[$i]->getConsumoPicoReal(),


                'id' => $entities_auto[$i]->getId(),
                'mes_fecha' => $entities_auto[$i]->getFechaLectura()->format('n'),
                'dia_fecha' => $entities_auto[$i]->getFechaLectura()->format('j'),
                'anno_fecha' => $entities_auto[$i]->getFechaLectura()->format('Y'),
                'serviciosid' => $entities_auto[$i]->getServiciosid()->getId(),
                'nombreserviciosid' => $entities_auto[$i]->getServiciosid()->getNombreServicio(),


                'lectura_total' => $entities_auto[$i]->getLecturaDia() + $entities_auto[$i]->getLecturaMad() + $entities_auto[$i]->getLecturaPico(),
                'consumo_total_real' => ($perdidas_transf !== 0) ? round($entities_auto[$i]->getConsumoTotalReal() + $perdidas_transf, 2) : round($entities_auto[$i]->getConsumoTotalReal(), 2),
                'consumo_total_porciento' => $entities_auto[$i]->getConsumoTotalPorciento(),
                'plan_pico' => $entities_auto[$i]->getConsumoPicoPlan(),
                'plan_diario' => $entities_auto[$i]->getConsumoTotalPlan(),

                'fp' => !is_nan($factor_potencia) ? round($factor_potencia, 3) : 0.00,
            );
        }

        $bitacora = array();
        $consumo_pico_acum = 0;
        $consumo_acum = 0;
        $real_acum = 0;
        $real_pico_acum = 0;
        for ($i = 0, $iMax = \count($valores); $i < $iMax; $i++) {
            $j = $i + 1;
            $existe = array_key_exists($j, $valores);
            if ($existe) {

                $real_pico_acum += $valores[$i]['consumo_pico'];
                $consumo_pico_acum += $valores[$i]['plan_pico'];
                $consumo_acum += $valores[$i]['plan_diario'];
                $real_acum += $valores[$i]['consumo_total_real'];

                $bitacora[] = array(
                    'dia' => $valores[$i]['dia_fecha'],
                    'lectura_mad' => $valores[$i]['lectura_mad'],
                    'lectura_dia' => $valores[$i]['lectura_dia'],
                    'lectura_pico' => $valores[$i]['lectura_pico'],
                    'lectura_react' => $valores[$i]['lectura_react'],

                    'consumo_mad' => round($valores[$i]['consumo_mad'], 2),
                    'consumo_dia' => round($valores[$i]['consumo_dia'], 2),
                    'consumo_pico' => round($valores[$i]['consumo_pico'], 2),
                    'consumo_total_react' => round($valores[$j]['lectura_react'] - $valores[$i]['lectura_react'], 2),

                    'plan_pico' => round($valores[$i]['plan_pico'], 2),
                    'real_pico' => round($valores[$i]['consumo_pico'], 2),
                    'real_plan' => round($valores[$i]['plan_pico'] - $valores[$i]['consumo_pico'], 2),

                    'plan_total' => round($valores[$i]['plan_diario'], 2),
                    'consumo_total' => round($valores[$i]['consumo_total_real'], 2),
                    'consumo_total_real_plan' => round($valores[$i]['plan_diario'] - $valores[$i]['consumo_total_real'], 2),

                    'consumo_pico_acum' => round($consumo_pico_acum, 2),
                    'real_pico_acum' => round($real_pico_acum, 2),
                    'acum_pico_real' => round($consumo_pico_acum - $real_pico_acum, 2),

                    'consumo_acum' => round($consumo_acum, 2),
                    'real_acum' => round($real_acum, 2),
                    'acum_real' => round($consumo_acum - $real_acum, 2),

                    'mes' => $mes,
                    'anno' => $anno,
                    'fp' => round($valores[$i]['fp'], 2),
                );
            }
        }

        return new JsonResponse(array('rows' => $bitacora, 'total' => \count($bitacora)));

    }

    public function printBitacoraAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->request->get('store'));

        $session = $request->getSession();

        $mes = $data[0]->mes;
        $name_mes = FechaUtil::getNombreMes($mes);
        $ano = $data[0]->anno;
        $tipo_lectura = $request->request->get('tipo_lectura');
        $lectura = 0;
//        $plan_mes = $data[\count($data) - 1]->consumo_acum_plan;

        $_html = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
        <html xmlns='http://www.w3.org/1999/xhtml'>
         <head>
          <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
           <title>Cumplimiento del plan de consumo de energía eléctrica total</title>
            <style>
              table.main {
                border:0px solid;
              border-radius:10px 10px 10px 10px;
              font-family: 'Arial', Times, serif;
              font-size: 5px;
              }
            .sinborde{
                border-color:#FFF;
            }
            .bordearriba{
                border-top:#000;
            }
            table.main1 {	
             border:0px solid;
             border-radius:10px 10px 10px 10px;
             font-family: 'Arial', Times, serif;
             font-size: 5px;
            }
            </style>
         </head>
          <body>
          <table width='1000' cellspacing='0' cellpadding='0'>
          <tr>
          <td align='center'>ANEXO No.1</td></tr>
          <td align='center'>TABLA 1A</td></tr>
          <td align='center'>Cumplimiento del plan de consumo de energía eléctrica total, mes $name_mes:</td></tr>
          </table>

            <table  width='1000' cellspacing='0' cellpadding='0' border='1'>
            <tr>
              <td rowspan='2' style='text-align: center'>Día</td>
              <td rowspan='1' colspan='4' style='text-align: center'>Lectura del contador</td>
              <td rowspan='1' colspan='4' style='text-align: center'>Consumo Diario KWh y Kvarh</td>
              <td rowspan='1' colspan='3' style='text-align: center'>Consumo diario Pico KWh</td>
              <td rowspan='1' colspan='3' style='text-align: center'>Consumo diario Total KWh</td>
              <td rowspan='1' colspan='3' style='text-align: center'>Consumo acumulado Pico KWh</td>
              <td rowspan='1' colspan='4' style='text-align: center'>Consumo acumulado Total KWh</td>
              <td rowspan='2' style='text-align: center'>Firma del Responsable</td>
            </tr>
            <tr>
                <td rowspan='1' colspan='1' style='text-align: center'>MAD</td>
                <td rowspan='1' colspan='1' style='text-align: center'>DIA</td>
                <td rowspan='1' colspan='1' style='text-align: center'>PICO</td>
                <td rowspan='1' colspan='1' style='text-align: center'>REACT</td>
                
                <td rowspan='1' colspan='1' style='text-align: center'>MAD</td>
                <td rowspan='1' colspan='1' style='text-align: center'>DIA</td>
                <td rowspan='1' colspan='1' style='text-align: center'>PICO</td>
                <td rowspan='1' colspan='1' style='text-align: center'>REACT</td>
                
                <td rowspan='1' colspan='1' style='text-align: center'>Plan Pico</td>
                <td rowspan='1' colspan='1' style='text-align: center'>Real Pico</td>
                <td rowspan='1' colspan='1' style='text-align: center'>Real-Plan</td>
                
                <td rowspan='1' colspan='1' style='text-align: center'>Real</td>
                <td rowspan='1' colspan='1' style='text-align: center'>Plan</td>
                <td rowspan='1' colspan='1' style='text-align: center'>Real-Plan</td>
                
                <td rowspan='1' colspan='1' style='text-align: center'>Real</td>
                <td rowspan='1' colspan='1' style='text-align: center'>Plan</td>
                <td rowspan='1' colspan='1' style='text-align: center'>Real-Plan</td>
                
                <td rowspan='1' colspan='1' style='text-align: center'>Real</td>
                <td rowspan='1' colspan='1' style='text-align: center'>Plan</td>
                <td rowspan='1' colspan='1' style='text-align: center'>Real-Plan</td>
                <td rowspan='1' colspan='1' style='text-align: center'>Fp</td>
                
            </tr>";


        for ($i = 0, $iMax = \count($data); $i < $iMax; $i++) {
            $_html .= "<tr>
              <td width='200' style='text-align: center'>" . $data[$i]->dia . " </td>
              <td width='50' style='text-align: center'>" . $data[$i]->lectura_mad . " </td>
              <td colspan='1' style='text-align: center'> " . $data[$i]->lectura_dia . "</td>
              <td colspan='1' style='text-align: center'> " . $data[$i]->lectura_pico . "</td>
              <td colspan='1' style='text-align: center'> " . $data[$i]->lectura_react . "</td>
              
              <td colspan='1' style='text-align: center'> " . $data[$i]->consumo_mad . "</td>
              <td colspan='1' style='text-align: center'>" . $data[$i]->consumo_dia . "</td>
              <td colspan='1' style='text-align: center'>" . $data[$i]->consumo_pico . "</td>
              <td colspan='1' style='text-align: center'>" . $data[$i]->consumo_total_react . "</td>
              
              <td colspan='1' style='text-align: center'>" . $data[$i]->plan_pico . "</td>
              <td colspan='1' style='text-align: center'>" . $data[$i]->real_pico . "</td>
              <td colspan='1' style='text-align: center'>" . $data[$i]->real_plan . "</td>
              
              <td colspan='1' style='text-align: center'>" . $data[$i]->plan_total . "</td>
              <td colspan='1' style='text-align: center'>" . $data[$i]->consumo_total . "</td>
              <td colspan='1' style='text-align: center'>" . $data[$i]->consumo_total_real_plan . "</td>
              
              <td colspan='1' style='text-align: center'>" . $data[$i]->consumo_pico_acum . "</td>
              <td colspan='1' style='text-align: center'>" . $data[$i]->real_pico_acum . "</td>
              <td colspan='1' style='text-align: center'>" . $data[$i]->acum_pico_real . "</td>
              
              <td colspan='1' style='text-align: center'>" . $data[$i]->consumo_acum . "</td>
              <td colspan='1' style='text-align: center'>" . $data[$i]->real_acum . "</td>
              <td colspan='1' style='text-align: center'>" . $data[$i]->acum_real . "</td>
              <td colspan='1' style='text-align: center'>" . $data[$i]->fp . "</td>
              
              <td colspan='1' style='text-align: center'> " . ' ' . "</td>
            </tr>";
        }
        $_html .= "
            </table>
              </body>
             </html>";
        return new JsonResponse((array('success' => true, 'html' => $_html)));
    }

    public function addAutoInspeccionAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->request->get('store'));
        $mes = $data[0];

        for ($i = 1, $iMax = \count($data); $i < $iMax; $i++) {
            $dia = null;
            $durante_horario_pico = '';
            $fuera_horario_pico = '';
            $responsable = '';
            if (!empty($data[$i]->dia)) {
                $dia = date_create($data[$i]->dia);
            }
            if (!empty($data[$i]->durante_horario_pico)) {
                $durante_horario_pico = $data[$i]->durante_horario_pico;
            }
            if (!empty($data[$i]->fuera_horario_pico)) {
                $fuera_horario_pico = $data[$i]->fuera_horario_pico;
            }
            if (!empty($data[$i]->responsable)) {
                $responsable = $data[$i]->responsable;
            }

            $entity = $em->getRepository('PortadoresBundle:AutolecturaAutoinspeccion')->find($data[$i]->id);
            if (!$entity)
                $entity = new AutolecturaAutoinspeccion();
            $entity->setFecha($dia);
            $entity->setMes($mes);
            $entity->setDuranteHorarioPico($durante_horario_pico);
            $entity->setFueraHorarioPico($fuera_horario_pico);
            $entity->setResponsable($responsable);
            $em->persist($entity);
            $em->flush();
        }
        return new JsonResponse((array('success' => true, 'cls' => 'success', 'message' => 'Auto inspección guardada con éxito.')));
    }

    public function loadAutoinspeccionAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $mes = $request->get('mes');
        $entities = $em->getRepository('PortadoresBundle:AutolecturaTresescalas')->getFechaAutoinspeccion($mes);
        $data = array();
        $mesBitacora = $mes;
        $entities_autolectura = $em->getRepository('PortadoresBundle:AutolecturaTresescalas')->getFechaAutolecturaTresescalas($mesBitacora);

        if (\count($entities) == 0) {
            foreach ($entities_autolectura as $entity) {
                $data[] = array(
                    'id' => '',
                    'dia' => date_format($entity->getFechaLectura(), 'D,j M, Y '),
                    'durante_horario_pico' => '',
                    'fuera_horario_pico' => '',
                    'responsable' => ''
                );
            }
        } else {
            foreach ($entities_autolectura as $entity) {
                $data[] = array(
                    'id' => '',
                    'dia' => date_format($entity->getFechaLectura(), 'D,j M, Y '),
                    'durante_horario_pico' => '',
                    'fuera_horario_pico' => '',
                    'responsable' => ''
                );
            }
            for ($i = 0, $iMax = \count($data); $i < $iMax; $i++) {
                $fecha = date_create($data[$i]['dia']);
                $entity = $em->getRepository('PortadoresBundle:AutolecturaAutoinspeccion')->findByFecha($fecha);
                if (\count($entity) > 0)
                    $data[$i] = array(
                        'id' => $entity[0]->getId(),
                        'dia' => date_format($entity[0]->getFecha(), 'D,j M, Y '),
                        'durante_horario_pico' => $entity[0]->getDuranteHorarioPico(),
                        'fuera_horario_pico' => $entity[0]->getFueraHorarioPico(),
                        'responsable' => $entity[0]->getResponsable()
                    );
            }
        }
        return new JsonResponse(array('rows' => $data, 'total' => \count($data)));
    }

    public function printBitacoraAutoinspeccionAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->request->get('store'));
        $mes_mostrar = ' ';

        $guardar = $data[0]->dia;
        $cortar = explode(',', $guardar);
        $a2 = $cortar[1];
        $picar = explode(' ', $a2);
        $mes = $picar[1];

        if ($mes == 'Ene') {
            $mes_mostrar = 'ENERO';
        }
        if ($mes == 'Feb') {
            $mes_mostrar = 'FEBRERO';
        }
        if ($mes == 'Mar') {
            $mes_mostrar = 'MARZO';
        }
        if ($mes == 'Abr') {
            $mes_mostrar = 'ABRIL';
        }
        if ($mes == 'May') {
            $mes_mostrar = 'MAYO';
        }
        if ($mes == 'Jun') {
            $mes_mostrar = 'JUNIO';
        }
        if ($mes == 'Jul') {
            $mes_mostrar = 'JULIO';
        }
        if ($mes == 'Ago') {
            $mes_mostrar = 'AGOSTO';
        }
        if ($mes == 'Sep') {
            $mes_mostrar = 'SEPTIEMBRE';
        }
        if ($mes == 'Oct') {
            $mes_mostrar = 'OCTUBRE';
        }
        if ($mes == 'Nov') {
            $mes_mostrar = 'NOVIEMBRE';
        }
        if ($mes == 'Dic') {
            $mes_mostrar = 'DICIEMBRE';
        }

        $_html = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
        <html xmlns='http://www.w3.org/1999/xhtml'>
         <head>
          <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
           <title>Autoinspección</title>
            <style>
              table.main {
                border:0px solid;
              border-radius:10px 10px 10px 10px;
              font-family: 'Times New Roman', Times, serif;
              font-size: 18px;
              }
            .sinborde{
                border-color:#FFF;
            }
            .bordearriba{
                border-top:#000;
            }
            table.main1 {	border:0px solid;
             border-radius:10px 10px 10px 10px;
             font-family: 'Times New Roman', Times, serif;
             font-size: 18px;
            }
            </style>
         </head>
          <body>
          <table width='1000' cellspacing='0' cellpadding='0'>
            <tr>
                <td style='text-align: center'> DEFICIENCIAS O VIOLACIONES DETECTADAS EN LA AUTOINSPECCIÓN,</br> MES $mes_mostrar</td>
            </tr>
          </table>

            <table  width='1000' cellspacing='0' cellpadding='0' border='1'>
            <tr>
              <td rowspan='2' style='text-align: center'>Día</td>
              <td rowspan='1' colspan='2' style='text-align: center'>Deficiencias o violaciones detectadas en la autoinspección</td>
              <td rowspan='2' style='text-align: center'>Firma </br>Responsable</td>
            </tr>
            <tr>
                <td rowspan='1' colspan='1' style='text-align: center'>Durante el horario pico</td>
                <td rowspan='1' style='text-align: center'>Fuera del horario pico</td>
            </tr>";
        for ($i = 0, $iMax = \count($data); $i < $iMax; $i++) {
            $guardar = $data[$i]->dia;
            $cortar = explode(',', $guardar);
            $a2 = $cortar[1];
            $picar = explode(' ', $a2);
            $dia = $picar[0];

            $_html .= "<tr>
              <td width='200' style='text-align: center'>" . $dia . " </td>
              <td colspan='1' style='text-align: center'>" . $data[$i]->durante_horario_pico . " </td>
              <td colspan='1' style='text-align: center'> " . $data[$i]->fuera_horario_pico . "</td>
              <td width='200' style='text-align: center'> " . $data[$i]->responsable . "</td>
            </tr>";
        }
        $_html .= "
            </table>
              </body>
             </html>";
        return new JsonResponse((array('success' => true, 'html' => $_html)));
    }

    /*!!!!!!!!!perdida de trasnformaacion  PT = PFE * t3 + (kVAr / kVAn)2 * PCU * T1!!!!!!!!!      MENSUAL   */
    public function CalcularPerdidasByServicio($idservicio, $mes_abierto, $anno_abierto)
    {

        $ultimo_dia_mes = date_create_from_format('Y-m-d', FechaUtil::getUltimoDiaMes($mes_abierto, $anno_abierto))->format('d');

        $em = $this->getDoctrine()->getManager();
        $fecha_inicial = $anno_abierto . '-' . $mes_abierto . '-01';
        $fecha_inicial = FechaUtil::getUltimoDiaMes($mes_abierto, $anno_abierto);
        $entities = $em->getRepository('PortadoresBundle:AutolecturaTresescalas')->getAutolecturaTresescalasMes($idservicio, $fecha_inicial, $fecha_final);
        $sql1 = "select s.cap_transf1,
                        s.cap_transf2,
                        s.cap_transf3,
                        s.cap_transf4,
                        s.cap_transf5
                from nomencladores.servicio as s where s.id = '$idservicio'";

        $consulta1 = $this->getDoctrine()->getConnection()->fetchAll($sql1);

        $arrayPfe = array();
        $arrayPCu = array();
        for ($i = 1; $i <= 5; $i++) {
            $keyPfe = 'cap_transf' . $i;
            $trans = ($consulta1[0][$keyPfe] != null) ? $em->getRepository('PortadoresBundle:BancoTransformadores')->find($consulta1[0][$keyPfe]) : null;
            if ($trans) {
                $arrayPfe[] = $trans->getPfe();
                $arrayPCu[] = $trans->getPcu();
            }
        }

        $perdidasTotales = 0;
        for ($i = 0, $iMax = \count($arrayPfe); $i < $iMax; $i++) {
            $kwh = $kvarh = 0;
            foreach ($entities as $entity) {
                $kwh += $entity->getConsumoTotalMad() + $entity->getConsumoTotalDia() + $entity->getConsumoPicoReal();
                $kvarh += $entity->getConsumo();
            }
            $factor_potencia = ($kwh != null) ? round(cos(atan($kvarh / $kwh)), 2) : '0';

            $servicio = $em->getRepository('PortadoresBundle:Servicio')->find($idservicio);
            $servicio_turno = $servicio->getTurnosTrabajo();
            $turno = $em->getRepository('PortadoresBundle:TurnoTrabajo')->find($servicio_turno);

            $horas_turno = $turno->getHoras();

            $T1 = ($horas_turno);
            $t3 = 24 * $ultimo_dia_mes;

            $kvar = round(($T1 != null) ? $kwh / $T1 * $factor_potencia : '0', 2);

            $kvan = $servicio->getCapBancoMayor();

            $perdidas_transf = ($kvarh != null) ? $arrayPfe[$i] * $t3 + pow($kvar / $kvan, 2) * $arrayPCu[$i] * $T1 : '0';

            $perdidasTotales += $perdidas_transf;
        }
        return $perdidasTotales;
    }

}