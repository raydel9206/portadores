<?php
/**
 * Created by PhpStorm.
 * User: pfcadenas
 * Date: 1/09/2016
 * Time: 9:22
 */

namespace Geocuba\PortadoresBundle\Controller;


use Doctrine\Common\Util\Debug;
use Geocuba\PortadoresBundle\Util\FechaUtil;
use Geocuba\PortadoresBundle\Util\Utiles;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Geocuba\PortadoresBundle\Util\DocumentosEnum;


class ReporteControlCombustibleVehiculoController extends Controller
{
    use ViewActionTrait;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = trim($request->get('id'));

        $unidadid = trim($request->get('unidadid'));
        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($unidadid), $_unidades);
        $unidades_string = $this->unidadesToString($_unidades);

        $export = trim($request->get('export'));

        if ($export != true) {
            $start = $request->get('start');
            $limit = $request->get('limit');
        }

        $anno = intval($request->get('anno'));
        $mes = intval($request->get('mes'));


        $sql = "select * from (
                    select max(subquery.nunidadid) as nunidadid,
                    subquery.matricula,
                    subquery.id,
                    '-'as vale,
                    subquery.fecha,
                    max(subquery.comb_abastecido) as comb_abastecido,
                    max(subquery.entrada) as entrada
                    from (select
                            v.nunidadid,
                            v.matricula as  matricula,
                            v.id as  id,
                            '-' as vale,
                            date( concat( extract( year from ant.fecha), '-',extract( month from ant.fecha), '-1'  )  ) as fecha,
                            0 as comb_abastecido,
                            (CASE
                            WHEN (extract( month from ant.fecha) = 1) THEN pcup.combustible_litros_ene
                            WHEN (extract( month from ant.fecha) = 2) THEN pcup.combustible_litros_feb
                            WHEN (extract( month from ant.fecha) = 3) THEN pcup.combustible_litros_mar
                            WHEN (extract( month from ant.fecha) = 4) THEN pcup.combustible_litros_abr
                            WHEN (extract( month from ant.fecha) = 5) THEN pcup.combustible_litros_may
                            WHEN (extract( month from ant.fecha) = 6) THEN pcup.combustible_litros_jun
                            WHEN (extract( month from ant.fecha) = 7) THEN pcup.combustible_litros_jul
                            WHEN (extract( month from ant.fecha) = 8) THEN pcup.combustible_litros_ago
                            WHEN (extract( month from ant.fecha) = 9) THEN pcup.combustible_litros_sep
                            WHEN (extract( month from ant.fecha) = 10) THEN pcup.combustible_litros_oct
                            WHEN (extract( month from ant.fecha) = 11) THEN pcup.combustible_litros_nov
                            WHEN (extract( month from ant.fecha) = 12) THEN pcup.combustible_litros_dic
                            ELSE 0

                            END +
                            CASE
                            WHEN (extract( month from ant.fecha) = 1) THEN pcuc.combustible_litros_ene
                            WHEN (extract( month from ant.fecha) = 2) THEN pcuc.combustible_litros_feb
                            WHEN (extract( month from ant.fecha) = 3) THEN pcuc.combustible_litros_mar
                            WHEN (extract( month from ant.fecha) = 4) THEN pcuc.combustible_litros_abr
                            WHEN (extract( month from ant.fecha) = 5) THEN pcuc.combustible_litros_may
                            WHEN (extract( month from ant.fecha) = 6) THEN pcuc.combustible_litros_jun
                            WHEN (extract( month from ant.fecha) = 7) THEN pcuc.combustible_litros_jul
                            WHEN (extract( month from ant.fecha) = 8) THEN pcuc.combustible_litros_ago
                            WHEN (extract( month from ant.fecha) = 9) THEN pcuc.combustible_litros_sep
                            WHEN (extract( month from ant.fecha) = 10) THEN pcuc.combustible_litros_oct
                            WHEN (extract( month from ant.fecha) = 11) THEN pcuc.combustible_litros_nov
                            WHEN (extract( month from ant.fecha) = 12) THEN pcuc.combustible_litros_dic
                            ELSE 0

                            END) as entrada

                            from datos.anticipo as ant
                            inner join nomencladores.vehiculo as v on v.id = ant.vehiculo
                            inner join nomencladores.tarjeta as tj on tj.id = ant.tarjeta
                            inner join datos.planificacion_combustible as pcup on pcup.vehiculoid =  v.id
                            inner join datos.planificacion_combustible_cuc as pcuc on pcuc.vehiculoid =  v.id

                            where pcup.visible = true and pcuc.visible = true and ant.visible = true and pcup.anno = $anno and pcuc.anno = $anno) as subquery

                            group by subquery.matricula,subquery.fecha, subquery.id

                    Union

                    select
                    tj.nunidadid,
                    v.matricula as  matricula,
                    v.id as  id,
                    lq.nro_vale as vale,
                    lq.fecha_vale as fecha,
                    lq.cant_litros,
                    0 as entrada

                    from datos.anticipo as ant
                    inner join nomencladores.vehiculo as v on v.id = ant.vehiculo
                    inner join nomencladores.tarjeta as tj on tj.id = ant.tarjeta
                    inner join datos.liquidacion as lq on lq.anticipo =  ant.id
                    ) as conjunto

                    where nunidadid in ($unidades_string)  and extract( year from fecha) = $anno and extract( month from fecha) = $mes and id = '$id'
                    order by fecha,entrada
                   ";

        $entities = $this->getDoctrine()->getConnection()->fetchAll($sql);

        if ($export != true) {
            $sql = "select count(*) from (
                    select
                     max(subquery.nunidadid) as nunidadid,
                     subquery.matricula,
                     subquery.id,
                     '-'as vale,
                     subquery.fecha,
                     max(subquery.comb_abastecido) as comb_abastecido,
                     max(subquery.entrada) as entrada
                     from (select
                            v.nunidadid,
                            v.matricula as  matricula,
                            v.id as  id,
                            '-' as vale,
                            date( concat( extract( year from ant.fecha), '-',extract( month from ant.fecha), '-1'  )  ) as fecha,
                            0 as comb_abastecido,
                            (CASE
                            WHEN (extract( month from ant.fecha) = 1) THEN pcup.combustible_litros_ene
                            WHEN (extract( month from ant.fecha) = 2) THEN pcup.combustible_litros_feb
                            WHEN (extract( month from ant.fecha) = 3) THEN pcup.combustible_litros_mar
                            WHEN (extract( month from ant.fecha) = 4) THEN pcup.combustible_litros_abr
                            WHEN (extract( month from ant.fecha) = 5) THEN pcup.combustible_litros_may
                            WHEN (extract( month from ant.fecha) = 6) THEN pcup.combustible_litros_jun
                            WHEN (extract( month from ant.fecha) = 7) THEN pcup.combustible_litros_jul
                            WHEN (extract( month from ant.fecha) = 8) THEN pcup.combustible_litros_ago
                            WHEN (extract( month from ant.fecha) = 9) THEN pcup.combustible_litros_sep
                            WHEN (extract( month from ant.fecha) = 10) THEN pcup.combustible_litros_oct
                            WHEN (extract( month from ant.fecha) = 11) THEN pcup.combustible_litros_nov
                            WHEN (extract( month from ant.fecha) = 12) THEN pcup.combustible_litros_dic
                            ELSE 0

                            END +
                            CASE
                            WHEN (extract( month from ant.fecha) = 1) THEN pcuc.combustible_litros_ene
                            WHEN (extract( month from ant.fecha) = 2) THEN pcuc.combustible_litros_feb
                            WHEN (extract( month from ant.fecha) = 3) THEN pcuc.combustible_litros_mar
                            WHEN (extract( month from ant.fecha) = 4) THEN pcuc.combustible_litros_abr
                            WHEN (extract( month from ant.fecha) = 5) THEN pcuc.combustible_litros_may
                            WHEN (extract( month from ant.fecha) = 6) THEN pcuc.combustible_litros_jun
                            WHEN (extract( month from ant.fecha) = 7) THEN pcuc.combustible_litros_jul
                            WHEN (extract( month from ant.fecha) = 8) THEN pcuc.combustible_litros_ago
                            WHEN (extract( month from ant.fecha) = 9) THEN pcuc.combustible_litros_sep
                            WHEN (extract( month from ant.fecha) = 10) THEN pcuc.combustible_litros_oct
                            WHEN (extract( month from ant.fecha) = 11) THEN pcuc.combustible_litros_nov
                            WHEN (extract( month from ant.fecha) = 12) THEN pcuc.combustible_litros_dic
                            ELSE 0

                            END) as entrada

                            from datos.anticipo as ant
                            inner join nomencladores.vehiculo as v on v.id = ant.vehiculo
                            inner join nomencladores.tarjeta as tj on tj.id = ant.tarjeta
                            inner join datos.planificacion_combustible as pcup on pcup.vehiculoid =  v.id
                            inner join datos.planificacion_combustible_cuc as pcuc on pcuc.vehiculoid =  v.id

                            where pcup.visible = true and pcuc.visible = true) as subquery

                            group by subquery.matricula,subquery.fecha, subquery.id

                    Union

                    select
                    tj.nunidadid,
                    v.matricula as  matricula,
                    v.id as  id,
                    lq.nro_vale as vale,
                    lq.fecha_vale as fecha,
                    lq.cant_litros,
                    0 as entrada

                    from datos.anticipo as ant
                    inner join nomencladores.vehiculo as v on v.id = ant.vehiculo
                    inner join nomencladores.tarjeta as tj on tj.id = ant.tarjeta
                    inner join datos.liquidacion as lq on lq.anticipo =  ant.id
                    ) as conjunto

                    where nunidadid = '$unidadid' and extract( year from fecha) = $anno and extract( month from fecha) = $mes and id = '$id'";

            $total = $this->getDoctrine()->getConnection()->fetchAll($sql);
        }

        $_data = array();

        $existencia = 0;
        foreach ($entities as $entity) {

            $existencia = $existencia + (float)$entity['entrada'] - (float)$entity['comb_abastecido'];

            $_data[] = array(
                'matricula' => $entity['matricula'],
                'vale' => $entity['vale'],
                'fecha' => date_create($entity['fecha'])->format('Y-m-d'),
                'comb_abastecido' => round($entity['comb_abastecido'], 2),
                'entrada' => round($entity['entrada'], 2),
                'existencia' => round($existencia, 2),
            );
        }

        if ($export) {
            return $_data;
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total[0]['count']));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loadVehiculoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $unidaid = $request->get('unidadid');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($unidaid), $_unidades);

        $entities = $em->getRepository('PortadoresBundle:Vehiculo')->buscarVehiculoCombo($_unidades, null);

        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'chapa' => $entity['matricula']
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => count($_data)));
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function printAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $chapa = $request->get('chapa');

        $entities = $this->loadAction($request);

        $anno = $request->get('anno');
        $mes = $request->get('mes');
        $mesNombre = FechaUtil::getNombreMes($mes);


        $_html = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
        <html xmlns='http://www.w3.org/1999/xhtml'>
        <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <title>Control de Combustible por Veh&iacute;culos</title>
        <style>
            table {
                border:0 solid;
                border-radius:0;
                font-family: 'Arial', serif;
                font-size: 11px;
                border-collapse: collapse;
            }
            td{                
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
            <td colspan='5' style='text-align: center;border: none; font-size: 14px;'><strong>Control Combustible por Veh&iacute;culos</strong></td>
         </tr>

         <tr>
            <td colspan='1' style='text-align: center;border: none;font-size: 12px;'><strong>Matrícula:</strong><strong>$chapa</strong></td>
            <td colspan='2' style='text-align: center;border: none;font-size: 12px;'><strong>Año:</strong><strong>$anno</strong></td>
            <td colspan='1' style='text-align: center;border: none;font-size: 12px;'><strong>Mes:</strong><strong>$mesNombre</strong></td>
            <td colspan='1' style='text-align: center;border: none;'></td>
         </tr>
         <tr>
            <td style='text-align: center;'><strong>Vale</strong></td>
            <td style='text-align: center;'><strong>Fecha</strong></td>
            <td style='text-align: center;'><strong>Comb. Abastecido(L)</strong></td>
            <td style='text-align: center;'><strong>Entrada</strong></td>
            <td style='text-align: center;'><strong>Existencia(L)</strong></td>
         </tr>";

        $existencia = 0;
        $total_comb = 0;
        foreach ($entities as $entity) {
            $total_comb += $entity['comb_abastecido'];
            $existencia = $existencia + (float)$entity['entrada'] - (float)$entity['comb_abastecido'];
            $_html .= "<tr>
                <td style='text-align: center;'>" . $entity['vale'] . "</td>
                <td style='text-align: center;'>" . date_format(new \DateTime($entity['fecha']), 'd/m/Y') . "</td>
                <td style='text-align: right;'>" . round($entity['comb_abastecido'], 2) . "</td>
                <td style='text-align: right;'>" . round($entity['entrada'], 2) . "</td>
                <td style='text-align: right;'>" . round($existencia, 2) . "</td>        
            </tr>";
        }
        $_html .= "<tr>
                <td style='text-align: center;'><strong>Total</strong></td>
                <td style='text-align: center;'></td>
                <td style='text-align: right;'><strong>" . $total_comb . "</strong></td>
                <td style='text-align: right;'></td>
                <td style='text-align: right;'></td>        
            </tr>";
        $_html .= "
        </table>
        ";

        $pieFirma = $this->get('portadores.piefirma')->getPieFirmaDistribucion(DocumentosEnum::controlCombustibleVehiculos, $request->get('unidadid'));
        $_html .= "
        <br>
        <br>
        
        $pieFirma";

        $_html .= "
        </body>
        </html>";
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