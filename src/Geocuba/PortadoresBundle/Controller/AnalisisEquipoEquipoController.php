<?php
/**
 * Created by PhpStorm.
 * User: kireny
 * Date: 11/07/2017
 * Time: 16:04
 */

namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\AdminBundle\Entity\DominioUnidades;
use Geocuba\PortadoresBundle\Util\FechaUtil;
use Geocuba\AdminBundle\Util\Util;
use Geocuba\PortadoresBundle\Entity\AnexoUnico;
use Geocuba\PortadoresBundle\Entity\CombustibleKilometros;
use Geocuba\PortadoresBundle\Entity\Vehiculo;
use Doctrine\Common\CommonException;
use Doctrine\Common\Util\Debug;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;
use Geocuba\PortadoresBundle\Util\DocumentosEnum;

class AnalisisEquipoEquipoController extends Controller
{
    use ViewActionTrait;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $conn = $this->get('database_connection');

        $nunidadid = $request->get('unidad');
        $tipo_combustibleid = $request->get('tipoCombustible');
        $export = $request->get('export');

        $qb = $em->createQueryBuilder();
        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $qb->select('vehiculo.id, marca.nombre marca_vehiculo, vehiculo.matricula, vehiculo.normaFar, tipoComb.id tipoCombustible, vehiculo.nroOrden, modelo.nombre modelo_vehiculo')
            ->from('PortadoresBundle:Vehiculo', 'vehiculo')
            ->innerJoin('vehiculo.nmodeloid', 'modelo')
            ->innerJoin('vehiculo.ntipoCombustibleid', 'tipoComb')
            ->innerJoin('modelo.marcaVehiculoid', 'marca')
            ->where('vehiculo.visible = true')
            ->andWhere($qb->expr()->in('vehiculo.nunidadid', $_unidades));

        if ($tipo_combustibleid !== "null" && $tipo_combustibleid !== "") {
            $qb->andWhere('vehiculo.ntipoCombustibleid = :tipoCombustible')
                ->setParameter('tipoCombustible', "$tipo_combustibleid");
        }

        $vehiculos = $qb->getQuery()->getResult();

        $_data_au = array();
        $anno = $request->get('anno');
        $mes = $request->get('mes');

        foreach ($vehiculos as $vehiculo) {
            /**@var Vehiculo $veh*/
            $veh = $em->getRepository('PortadoresBundle:Vehiculo')->findOneBy(array('id' => $vehiculo['id']));

            $sqlreg = $conn->fetchAll("select registro_combustible.norma_plan as norma_plan from datos.registro_combustible
            inner join nomencladores.vehiculo nvehiculo on nvehiculo.id = registro_combustible.vehiculoid and nvehiculo.id = '" . $vehiculo['id'] . "'
            where date_part('YEAR', registro_combustible.fecha) = $anno and date_part('MONTH', registro_combustible.fecha) = $mes
            order by registro_combustible.norma_plan DESC limit 1");

            $result_llegada = $conn->fetchAll("select registro_combustible_analisis.* from datos.registro_combustible
            inner join nomencladores.vehiculo nvehiculo on nvehiculo.id = registro_combustible.vehiculoid and nvehiculo.id = '" . $vehiculo['id'] . "'
            inner join datos.registro_combustible_analisis on registro_combustible_analisis.registro_combustible_id = registro_combustible.id
            where registro_combustible_analisis.conceptoid = '4' and date_part('YEAR', registro_combustible.fecha) = $anno and date_part('MONTH', registro_combustible.fecha) = $mes
            order by numerosemana DESC limit 1");

            $sql = "select registro_combustible_analisis.* from datos.registro_combustible
            inner join nomencladores.vehiculo nvehiculo on nvehiculo.id = registro_combustible.vehiculoid and nvehiculo.id = '" . $vehiculo['id'] . "'
            inner join datos.registro_combustible_analisis on registro_combustible_analisis.registro_combustible_id = registro_combustible.id
            where registro_combustible_analisis.conceptoid = '1' and date_part('YEAR', registro_combustible.fecha) = $anno and date_part('MONTH', registro_combustible.fecha) = $mes
            order by numerosemana limit 1";
            $result_salida = $conn->fetchAll($sql);

            $combustible_abastecido = $conn->fetchAll("select sum(rca.combustible) abastecido
                                                                   from datos.registro_combustible as rc
                                                                   join datos.registro_combustible_analisis as rca on rc.id = rca.registro_combustible_id

                                                             where rc.vehiculoid = '" . $vehiculo['id'] . "'
                                                                   and rca.conceptoid = '2'
                                                                   and extract(YEAR from rc.fecha) = $anno
                                                                   and extract(MONTH from rc.fecha) = $mes");

            $result_kmt_trab = $conn->fetchAll("select   sum(registro_combustible_analisis.km) from datos.registro_combustible
            inner join nomencladores.vehiculo nvehiculo on nvehiculo.id = registro_combustible.vehiculoid and nvehiculo.id = '" . $vehiculo['id'] . "'
            inner join datos.registro_combustible_analisis on registro_combustible_analisis.registro_combustible_id = registro_combustible.id
            where registro_combustible_analisis.conceptoid = '3' and date_part('YEAR', registro_combustible.fecha) = $anno and date_part('MONTH', registro_combustible.fecha)= $mes and  registro_combustible.visible = true
            limit 1");

            $comb_salir = empty($result_salida[0]['combustible']) ? 0 : $result_salida[0]['combustible'];
            $comb_llegar = empty($result_llegada[0]['combustible']) ? 0 : $result_llegada[0]['combustible'];
            $comb_abastecido = empty($combustible_abastecido[0]['abastecido']) ? 0 : $combustible_abastecido[0]['abastecido'];
            $kms_trabajado = is_null($result_kmt_trab[0]['sum']) ? 0 : $result_kmt_trab[0]['sum'];

            $comb_real_consumido = $comb_salir + $comb_abastecido - $comb_llegar;
            $norma_plan = (\count($sqlreg) > 0) ? round(100 / $vehiculo['normaFar'], 2) : 0;
            $norma_real = ($kms_trabajado !== 0) ? $comb_real_consumido * 100 / $kms_trabajado : 0;

            if ($kms_trabajado !== 0) {
                $_data_au[] = array(
                    'matricula' => $vehiculo['matricula'],
                    'marca' => $vehiculo['marca_vehiculo'],
                    'modelo' => $vehiculo['modelo_vehiculo'],
                    'descripcion' => $veh->getNdenominacionVehiculoid()->getNombre(),
                    'actividad_nombre' => '',
                    'unidadid' => $veh->getNunidadid()->getId(),
                    'unidad_nombre' => $veh->getNunidadid()->getNombre(),
                    'nivel_actividad_real' => $kms_trabajado,
                    'consumo_real' => $comb_real_consumido,
                    'indice_consumo_fabricante' => 0,
                    'indice_real' => ($kms_trabajado == 0) ? 0 : round($norma_real, 2),
                    'indice_plan' => ($norma_plan !== 0) ? (round (100 / $norma_plan,2)) : 0,
                    'comb_debio_consumir' => ($norma_plan !== 0) ? $kms_trabajado / $norma_plan : 0,
                    'diferencia_consumo' => ($norma_plan !== 0) ?   $kms_trabajado / $norma_plan - $comb_real_consumido: 0,
                    'desviacion_indice_normado' => ($norma_plan !== 0 && $comb_real_consumido !== 0) ? (100 / $norma_plan) - $norma_real : 0,
                    'desviacion_indice_normado_abs' => ($kms_trabajado == 0) ? '0,00' : abs((100 / $norma_plan) - $norma_real),
                    'tipo_combustible' => $vehiculo['tipoCombustible'],
                );
            }
        }

        if ($export) {
            return $_data_au;
        }

        return new JsonResponse(array('rows' => $_data_au, 'total' => count($_data_au)));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loadResumenAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $anno = $request->get('anno');
        $mes = $request->get('mes');
        $conn = $this->get('database_connection');
        $nunidadid = $request->get('unidad');
        $tipo_combustibleid = $request->get('tipoCombustible');

        $qb = $em->createQueryBuilder();
        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $qb->select('vehiculo.id, marca.nombre marca_vehiculo, vehiculo.matricula, vehiculo.normaFar, tipoComb.id tipoCombustible, vehiculo.nroOrden, modelo.nombre modelo_vehiculo')
            ->from('PortadoresBundle:Vehiculo', 'vehiculo')
            ->innerJoin('vehiculo.nmodeloid', 'modelo')
            ->innerJoin('vehiculo.ntipoCombustibleid', 'tipoComb')
            ->innerJoin('modelo.marcaVehiculoid', 'marca')
            ->where('vehiculo.visible = true')
            ->andWhere($qb->expr()->in('vehiculo.nunidadid', $_unidades));

        if ($tipo_combustibleid !== "null" && $tipo_combustibleid !== "") {
            $qb->andWhere('vehiculo.ntipoCombustibleid = :tipoCombustible')
                ->setParameter('tipoCombustible', "$tipo_combustibleid");
        }

        $vehiculos = $qb->getQuery()->getResult();

        $_data_au = array();
        $abs = array();
        foreach ($vehiculos as $vehiculo) {
            /**@var Vehiculo $veh*/
            $veh = $em->getRepository('PortadoresBundle:Vehiculo')->findOneBy(array('id' => $vehiculo['id']));
            $abs = array();
            for ($i = 1; $i <= 12; $i++) {
                $sqlreg = $conn->fetchAll("select registro_combustible.norma_plan as norma_plan from datos.registro_combustible
            inner join nomencladores.vehiculo nvehiculo on nvehiculo.id = registro_combustible.vehiculoid and nvehiculo.id = '" . $vehiculo['id'] . "'
            where date_part('YEAR', registro_combustible.fecha) = $anno and date_part('MONTH', registro_combustible.fecha) = $mes
            order by registro_combustible.norma_plan DESC limit 1");

                $result_llegada = $conn->fetchAll("select registro_combustible_analisis.* from datos.registro_combustible
            inner join nomencladores.vehiculo nvehiculo on nvehiculo.id = registro_combustible.vehiculoid and nvehiculo.id = '" . $vehiculo['id'] . "'
            inner join datos.registro_combustible_analisis on registro_combustible_analisis.registro_combustible_id = registro_combustible.id
            where registro_combustible_analisis.conceptoid = '4' and date_part('YEAR', registro_combustible.fecha) = $anno and date_part('MONTH', registro_combustible.fecha) = $i
            order by numerosemana DESC limit 1");

                $sql = "select registro_combustible_analisis.* from datos.registro_combustible
            inner join nomencladores.vehiculo nvehiculo on nvehiculo.id = registro_combustible.vehiculoid and nvehiculo.id = '" . $vehiculo['id'] . "'
            inner join datos.registro_combustible_analisis on registro_combustible_analisis.registro_combustible_id = registro_combustible.id
            where registro_combustible_analisis.conceptoid = '1' and date_part('YEAR', registro_combustible.fecha) = $anno and date_part('MONTH', registro_combustible.fecha) = $i
            order by numerosemana limit 1";
                $result_salida = $conn->fetchAll($sql);

                $combustible_abastecido = $conn->fetchAll("select sum(rca.combustible) abastecido
                                                                   from datos.registro_combustible as rc
                                                                   join datos.registro_combustible_analisis as rca on rc.id = rca.registro_combustible_id

                                                             where rc.vehiculoid = '" . $vehiculo['id'] . "'
                                                                   and rca.conceptoid = '2'
                                                                   and extract(YEAR from rc.fecha) = $anno
                                                                   and extract(MONTH from rc.fecha) = $i");

                $result_kmt_trab = $conn->fetchAll("select   sum(registro_combustible_analisis.km) from datos.registro_combustible
            inner join nomencladores.vehiculo nvehiculo on nvehiculo.id = registro_combustible.vehiculoid and nvehiculo.id = '" . $vehiculo['id'] . "'
            inner join datos.registro_combustible_analisis on registro_combustible_analisis.registro_combustible_id = registro_combustible.id
            where registro_combustible_analisis.conceptoid = '3' and date_part('YEAR', registro_combustible.fecha) = $anno and date_part('MONTH', registro_combustible.fecha)= $i and  registro_combustible.visible = true
            limit 1");

                $comb_salir = empty($result_salida[0]['combustible']) ? 0 : $result_salida[0]['combustible'];
                $comb_llegar = empty($result_llegada[0]['combustible']) ? 0 : $result_llegada[0]['combustible'];
                $comb_abastecido = empty($combustible_abastecido[0]['abastecido']) ? 0 : $combustible_abastecido[0]['abastecido'];
                $kms_trabajado = is_null($result_kmt_trab[0]['sum']) ? 0 : $result_kmt_trab[0]['sum'];

                $comb_real_consumido = $comb_salir + $comb_abastecido - $comb_llegar;
                $norma_plan = (\count($sqlreg) > 0) ? round(100 / $sqlreg[0]['norma_plan'], 2) : 0;
                $norma_real = ($kms_trabajado !== 0) ? $comb_real_consumido * 100 / $kms_trabajado : 0;


                array_push($abs, ($kms_trabajado == 0) ? 0 : abs($norma_plan - $norma_real));
            }


            $_data_au[] = array(
                'matricula' => $veh->getMatricula(),
                'marca' => $veh->getNmodeloid()->getMarcaVehiculoid()->getNombre(),
                'modelo' => $veh->getNmodeloid()->getNombre(),
                'descripcion' => $veh->getNdenominacionVehiculoid()->getNombre(),
                'unidad_nombre' => $veh->getNunidadid()->getNombre(),
                'indice_plan' => $norma_plan,
                'abs_1' =>  ($abs[0] === 0) ?  0 : $abs[0],
                'abs_2' =>  ($abs[1]=== 0) ?  0 : $abs[1],
                'abs_3' =>  ($abs[2]=== 0) ?  0 : $abs[2],
                'abs_4' =>  ($abs[3]=== 0) ?  0 : $abs[3],
                'abs_5' =>  ($abs[4]=== 0) ?  0 : $abs[4],
                'abs_6' =>  ($abs[5]=== 0) ?  0 : $abs[5],
                'abs_7' =>  ($abs[6]=== 0) ?  0 : $abs[6],
                'abs_8' =>  ($abs[7]=== 0) ?  0 : $abs[7],
                'abs_9' =>  ($abs[8]=== 0) ?  0 : $abs[8],
                'abs_10' =>  ($abs[9]=== 0) ?  0 : $abs[9],
                'abs_11' =>  ($abs[10]=== 0) ?  0 : $abs[10],
                'abs_12' =>  ($abs[11]=== 0) ?  0 : $abs[11],
            );
        }

        return new JsonResponse(array('rows' => $_data_au, 'total' => count($_data_au)));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loadAcumuladoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $conn = $this->get('database_connection');
        $export = $request->get('export');
        $nunidadid = $request->get('unidad');
        $tipo_combustibleid = $request->get('tipoCombustible');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $qb->select('vehiculo.id, marca.nombre marca_vehiculo, vehiculo.matricula, vehiculo.normaFar, tipoComb.id tipoCombustible, vehiculo.nroOrden, modelo.nombre modelo_vehiculo')
            ->from('PortadoresBundle:Vehiculo', 'vehiculo')
            ->innerJoin('vehiculo.nmodeloid', 'modelo')
            ->innerJoin('vehiculo.ntipoCombustibleid', 'tipoComb')
            ->innerJoin('modelo.marcaVehiculoid', 'marca')
            ->where('vehiculo.visible = true')
            ->andWhere($qb->expr()->in('vehiculo.nunidadid', $_unidades));

        if ($tipo_combustibleid !== "null" && $tipo_combustibleid !== "") {
            $qb->andWhere('vehiculo.ntipoCombustibleid = :tipoCombustible')
                ->setParameter('tipoCombustible', "$tipo_combustibleid");
        }

        $vehiculos = $qb->getQuery()->getResult();

        $_data_aux_acum = array();
        $anno = $request->get('anno');
        $mes = $request->get('mes');
        foreach ($vehiculos as $vehiculo) {
            /**@var Vehiculo $veh*/
            $veh = $em->getRepository('PortadoresBundle:Vehiculo')->findOneBy(array('id' => $vehiculo['id']));

            $sqlreg = $conn->fetchAll("select registro_combustible.norma_plan as norma_plan from datos.registro_combustible
            inner join nomencladores.vehiculo nvehiculo on nvehiculo.id = registro_combustible.vehiculoid and nvehiculo.id = '" . $vehiculo['id'] . "'
            where date_part('YEAR', registro_combustible.fecha) = $anno and date_part('MONTH', registro_combustible.fecha) = $mes
            order by registro_combustible.norma_plan DESC limit 1");

            for ($i = $mes; $i >= 1; $i--) {
                $result_llegada = $conn->fetchAll("select registro_combustible_analisis.* from datos.registro_combustible
            inner join nomencladores.vehiculo nvehiculo on nvehiculo.id = registro_combustible.vehiculoid and nvehiculo.id = '" . $vehiculo['id'] . "'
            inner join datos.registro_combustible_analisis on registro_combustible_analisis.registro_combustible_id = registro_combustible.id
            where registro_combustible_analisis.conceptoid = '4' and date_part('YEAR', registro_combustible.fecha) = $anno and date_part('MONTH', registro_combustible.fecha) = $i
            order by numerosemana DESC limit 1");
                if ($result_llegada) {
                    if ($result_llegada[0]['combustible'] != 0) {
                        break;
                    }
                }
            }

            $result_salida = $conn->fetchAll("select registro_combustible_analisis.* from datos.registro_combustible
            inner join nomencladores.vehiculo nvehiculo on nvehiculo.id = registro_combustible.vehiculoid and nvehiculo.id = '" . $vehiculo['id'] . "'
            inner join datos.registro_combustible_analisis on registro_combustible_analisis.registro_combustible_id = registro_combustible.id
            where registro_combustible_analisis.conceptoid = '1' and date_part('YEAR', registro_combustible.fecha) = $anno
            order by registro_combustible.fecha, numerosemana limit 1");

            $result_kmt_trab = $conn->fetchAll("select      sum(registro_combustible_analisis.km) from datos.registro_combustible
            inner join nomencladores.vehiculo nvehiculo on nvehiculo.id = registro_combustible.vehiculoid and nvehiculo.id = '" . $vehiculo['id'] . "'
            inner join datos.registro_combustible_analisis on registro_combustible_analisis.registro_combustible_id = registro_combustible.id
            where registro_combustible_analisis.conceptoid = '3' and date_part('YEAR', registro_combustible.fecha) = $anno and  registro_combustible.visible = true
            limit 1");

            $combustible_abastecido = $conn->fetchAll("select sum(rca.combustible) abastecido
                                                                   from datos.registro_combustible as rc
                                                                   join datos.registro_combustible_analisis as rca on rc.id = rca.registro_combustible_id

                                                             where rc.vehiculoid = '" . $vehiculo['id'] . "'
                                                                   and rca.conceptoid = '2'
                                                                   and extract(YEAR from rc.fecha) = $anno
                                                                   and extract(MONTH from rc.fecha) <= $mes");


            $comb_salir = empty($result_salida[0]['combustible']) ? 0 : $result_salida[0]['combustible'];
            $comb_llegar = empty($result_llegada[0]['combustible']) ? 0 : $result_llegada[0]['combustible'];
            $comb_abastecido = empty($combustible_abastecido[0]['abastecido']) ? 0 : $combustible_abastecido[0]['abastecido'];
            $kms_trabajado = is_null($result_kmt_trab[0]['sum']) ? 0 : $result_kmt_trab[0]['sum'];

            $comb_real_consumido = $comb_salir + $comb_abastecido - $comb_llegar;
            $norma_plan = (sizeof($sqlreg ) > 0) ? round(100 / $sqlreg[0]['norma_plan'], 2) : 0;
            $norma_real = ($kms_trabajado !== 0) ? $comb_real_consumido * 100 / $kms_trabajado : 0;

            if ($kms_trabajado !== 0) {
                $_data_aux_acum[] = array(
                    'matricula' => $vehiculo['matricula'],
                    'marca' => $vehiculo['marca_vehiculo'],
                    'modelo' => $vehiculo['modelo_vehiculo'],
                    'descripcion' => $veh->getNdenominacionVehiculoid()->getNombre(),
                    'actividad_nombre' => '',
                    'unidadid' => $veh->getNunidadid()->getId(),
                    'unidad_nombre' => $veh->getNunidadid()->getNombre(),
                    'nivel_actividad_real' => $kms_trabajado,
                    'consumo_real' => round($comb_real_consumido, 2),
                    'indice_consumo_fabricante' => 0,
                    'indice_real' => ($kms_trabajado == 0) ? 0 : round($norma_real, 2),
                    'indice_plan' => $norma_plan,
                    'comb_debio_consumir' => ($norma_plan !== 0) ? round($kms_trabajado / $norma_plan, 2) : 0,
                    'diferencia_consumo' => ($norma_plan !== 0) ? round($comb_real_consumido - ($kms_trabajado / $norma_plan), 2) : 0,
                    'desviacion_indice_normado' => ($norma_plan !== 0 && $comb_real_consumido !== 0) ? round($norma_plan - $norma_real, 2) : 0,
                    'desviacion_indice_normado_abs' => ($kms_trabajado == 0) ? '0,00' : round(abs($norma_plan - $norma_real), 2),
                    'tipo_combustible' => $vehiculo['tipoCombustible'],
                );
            }
        }
        if ($export) {
            return $_data_aux_acum;
        }
        return new JsonResponse(array('rows' => $_data_aux_acum, 'total' => count($_data_aux_acum)));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function printAction(Request $request)
    {

        $unidad_nombre = $request->get('unidad_nombre');
        $nombre_combustible = $request->get('nombre_combustible');

        $mes = $request->get('mes');
        $anno = $request->get('anno');

        $mes = FechaUtil::getNombreMes($mes);

        $datos = ($request->get('exporttype') ==="mensual") ? $this->loadAction($request): $this->loadAcumuladoAction($request);
        $title = ($request->get('exporttype') ==="mensual") ? "MENSUAL": "ACUMULADO";

        $_html = "<html xmlns:v=\"urn:schemas-microsoft-com:vml\"
xmlns:o=\"urn:schemas-microsoft-com:office:office\"
xmlns:x=\"urn:schemas-microsoft-com:office:excel\"
xmlns=\"http://www.w3.org/TR/REC-html40\">

<head>
<meta http-equiv=Content-Type content=\"text/html; charset=utf-8\">
<meta name=ProgId content=Excel.Sheet>
<meta name=Generator content=\"Microsoft Excel 15\">
<link rel=File-List
href=\"ANEXOS%20CERTIFICACION%20AUTOCONTROL_files/filelist.xml\">
<!--[if !mso]>
<style>
v\:* {behavior:url(#default#VML);}
o\:* {behavior:url(#default#VML);}
x\:* {behavior:url(#default#VML);}
.shape {behavior:url(#default#VML);}
</style>
<![endif]-->
<style id=\"ANEXOS CERTIFICACION AUTOCONTROL_32029_Styles\">
<!--table
	{mso-displayed-decimal-separator:\"\,\";
	mso-displayed-thousand-separator:\"\.\";}
.xl6632029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
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
.xl6732029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:16.0pt;
	font-weight:400;
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
.xl6832029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:16.0pt;
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
.xl6932029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl7032029
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"\@\";
	text-align:general;
	vertical-align:bottom;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl7132029
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"\@\";
	text-align:left;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl7232029
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
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
.xl7332029
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"\@\";
	text-align:general;
	vertical-align:bottom;
	border-top:none;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl7432029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:bottom;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl7532029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
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
.xl7632029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:12.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl8432029
	{padding:0px;
	mso-ignore:padding;
	color:red;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl8532029
	{padding:0px;
	mso-ignore:padding;
	color:red;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl8632029
	{padding:0px;
	mso-ignore:padding;
	color:red;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl8732029
	{padding:0px;
	mso-ignore:padding;
	color:red;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.0\";
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl8832029
	{padding:0px;
	mso-ignore:padding;
	color:red;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:Fixed;
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl8932029
	{padding:0px;
	mso-ignore:padding;
	color:red;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:Fixed;
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl9032029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:Fixed;
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl9132029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:12.0pt;
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
.xl9232029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:12.0pt;
	font-weight:400;
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
.xl9332029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:12.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.0\";
	text-align:center;
	vertical-align:bottom;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl9432029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:12.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:Fixed;
	text-align:center;
	vertical-align:bottom;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl9532029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:12.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"\@\";
	text-align:general;
	vertical-align:bottom;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl9632029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:400;
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
.xl9732029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:12.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.0\";
	text-align:center;
	vertical-align:bottom;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:nowrap;}
.xl9832029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:12.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:Fixed;
	text-align:center;
	vertical-align:bottom;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:nowrap;}
.xl9932029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:12.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:nowrap;}
.xl10032029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:12.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:Fixed;
	text-align:center;
	vertical-align:bottom;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:nowrap;}
.xl10132029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:12.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.0\";
	text-align:center;
	vertical-align:bottom;
	border:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:nowrap;}
.xl10232029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:12.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:Fixed;
	text-align:center;
	vertical-align:bottom;
	border:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:nowrap;}
.xl10532029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:12.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:nowrap;}
.xl10632029
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:10.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:nowrap;}
.xl10832029
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.0\";
	text-align:center;
	vertical-align:bottom;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:nowrap;}
.xl11032029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:16.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"mmm\\-yy\";
	text-align:center;
	vertical-align:bottom;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl11132029
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:10.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	background:white;
	mso-pattern:black none;
	white-space:nowrap;}
.xl11432029
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:nowrap;}
.xl11532029
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:nowrap;}
.xl11932029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:12.0pt;
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
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:nowrap;}
.xl12032029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.0\";
	text-align:center;
	vertical-align:bottom;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:nowrap;}
.xl12132029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:nowrap;}
.xl12232029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:nowrap;}
.xl12332029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:12.0pt;
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
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:nowrap;}
.xl12432029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"0\.0\";
	text-align:center;
	vertical-align:bottom;
	border:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:nowrap;}
.xl12532029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:700;
	font-style:normal;
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
	background:white;
	mso-pattern:black none;
	white-space:nowrap;}
.xl12632029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:nowrap;}
.xl12732029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:12.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl12832029
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:10.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:bottom;
	background:white;
	mso-pattern:black none;
	white-space:nowrap;}
.xl12932029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:bottom;
	background:white;
	mso-pattern:black none;
	white-space:nowrap;}
.xl13032029
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:10.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:bottom;
	background:white;
	mso-pattern:black none;
	white-space:nowrap;}
.xl13132029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:12.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"\@\";
	text-align:left;
	vertical-align:bottom;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl13232029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
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
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;
	mso-rotate:90;}
.xl13332029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
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
	white-space:normal;
	mso-rotate:90;}
.xl13432029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
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
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl13532029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:700;
	font-style:normal;
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
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl13632029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:10.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
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
.xl13732029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:16.0pt;
	font-weight:400;
	font-style:normal;
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
	white-space:normal;}
.xl13832029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:16.0pt;
	font-weight:400;
	font-style:normal;
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
	white-space:normal;}
.xl13932029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:16.0pt;
	font-weight:400;
	font-style:normal;
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
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl14032029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl14132029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl14232029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl14332029
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl14432029
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"\@\";
	text-align:left;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl14532029
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"\@\";
	text-align:left;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl14632029
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"\@\";
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl14732029
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
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
.xl14832029
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:\"\@\";
	text-align:center;
	vertical-align:bottom;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
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

<div id=\"ANEXOS CERTIFICACION AUTOCONTROL_32029\" align=center
x:publishsource=\"Excel\">

<table border=0 cellpadding=0 cellspacing=0 width=1189 class=xl6632029
 style='border-collapse:collapse;table-layout:fixed;width:893pt'>
 <col class=xl6632029 width=35 style='mso-width-source:userset;mso-width-alt:
 1280;width:26pt'>
 <col class=xl9632029 width=126 style='mso-width-source:userset;mso-width-alt:
 4608;width:95pt'>
 <col class=xl9632029 width=81 style='mso-width-source:userset;mso-width-alt:
 2962;width:61pt'>
 <col class=xl9632029 width=87 style='mso-width-source:userset;mso-width-alt:
 3181;width:65pt'>
 <col class=xl9632029 width=89 style='mso-width-source:userset;mso-width-alt:
 3254;width:67pt'>
 <col class=xl9632029 width=83 style='mso-width-source:userset;mso-width-alt:
 3035;width:62pt'>
 <col class=xl9632029 width=108 style='mso-width-source:userset;mso-width-alt:
 3949;width:81pt'>
 <col class=xl9632029 width=62 style='mso-width-source:userset;mso-width-alt:
 2267;width:47pt'>
 <col class=xl9632029 width=63 style='mso-width-source:userset;mso-width-alt:
 2304;width:47pt'>
 <col class=xl9632029 width=98 style='mso-width-source:userset;mso-width-alt:
 3584;width:74pt'>
 <col class=xl9632029 width=121 style='mso-width-source:userset;mso-width-alt:
 4425;width:91pt'>
 <col class=xl9632029 width=76 style='mso-width-source:userset;mso-width-alt:
 2779;width:57pt'>
 <col class=xl6632029 width=80 span=2 style='width:60pt'>
 <tr height=27 style='height:20.25pt'>
  <td height=27 class=xl6632029 width=35 style='height:20.25pt;width:26pt'><a
  name=\"RANGE!A1:L23\"></a></td>
  <td class=xl6732029 width=126 style='width:95pt'></td>
  <td colspan=10 class=xl6832029 width=868 style='width:652pt'>ANALISIS DE LOS
  CONSUMOS EQUIPO A EQUIPO $title</td>
  <td class=xl6632029 width=80 style='width:60pt'></td>
  <td class=xl6632029 width=80 style='width:60pt'></td>
 </tr>
 <tr height=27 style='height:20.25pt'>
  <td height=27 class=xl6632029 style='height:20.25pt'></td>
  <td class=xl6732029></td>
  <td class=xl6832029></td>
  <td class=xl6832029></td>
  <td class=xl6732029></td>
  <td class=xl6732029></td>
  <td class=xl6732029></td>
  <td class=xl6732029></td>
  <td class=xl6732029></td>
  <td class=xl6732029></td>
  <td class=xl6732029></td>
  <td class=xl6732029></td>
  <td class=xl6632029></td>
  <td class=xl6632029></td>
 </tr>
 <tr height=27 style='height:20.25pt'>
  <td height=27 class=xl6632029 style='height:20.25pt'></td>
  <td colspan=2 class=xl14232029 style='border-right:.5pt solid black'>Empresa:<span
  style='mso-spacerun:yes'> </span></td>
  <td class=xl6932029>&nbsp;</td>
  <td colspan=3 class=xl14632029 style='border-right:.5pt solid black'>" . strtoupper($unidad_nombre) . "</td>
  <td class=xl7032029></td>
  <td class=xl7032029></td>
  <td class=xl6732029></td>
  <td class=xl6732029></td>
  <td class=xl6732029></td>
  <td class=xl6632029></td>
  <td class=xl6632029></td>
 </tr>
 <tr height=27 style='height:20.25pt'>
  <td height=27 class=xl6632029 style='height:20.25pt'></td>
  <td colspan=2 class=xl14432029 style='border-right:.5pt solid black'>Actividad:</td>
  <td class=xl7132029 style='border-top:none'>&nbsp;</td>
  <td colspan=3 class=xl14632029 style='border-right:.5pt solid black'>&nbsp;</td>
  <td class=xl7032029></td>
  <td class=xl7232029></td>
  <td class=xl6732029></td>
  <td class=xl6732029></td>
  <td class=xl6732029></td>
  <td class=xl6632029></td>
  <td class=xl6632029></td>
 </tr>
 <tr height=27 style='height:20.25pt'>
  <td height=27 class=xl6632029 style='height:20.25pt'></td>
  <td colspan=2 class=xl14432029 style='border-right:.5pt solid black'>Producto:</td>
  <td class=xl7132029 style='border-top:none'>&nbsp;</td>
  <td colspan=3 class=xl14632029 style='border-right:.5pt solid black'>" . strtoupper($nombre_combustible) . "</td>
  <td class=xl7032029></td>
  <td class=xl7232029></td>
  <td class=xl6732029></td>
  <td class=xl11032029>" . strtoupper($mes) . "</td>
  <td class=xl6732029>" . $anno . "</td>
  <td class=xl6632029></td>
  <td class=xl6632029></td>
 </tr>
 <tr height=28 style='height:21.0pt'>
  <td height=28 class=xl6632029 style='height:21.0pt'></td>
  <td class=xl7332029>&nbsp;</td>
  <td class=xl7432029></td>
  <td class=xl7432029></td>
  <td class=xl7332029>&nbsp;</td>
  <td class=xl7332029>&nbsp;</td>
  <td class=xl7232029></td>
  <td class=xl7232029></td>
  <td class=xl7232029></td>
  <td class=xl6732029></td>
  <td class=xl6732029></td>
  <td class=xl6732029></td>
  <td class=xl6632029></td>
  <td class=xl6632029></td>
 </tr>
 <tr height=35 style='mso-height-source:userset;height:26.25pt'>
  <td rowspan=2 height=144 class=xl13532029 width=35 style='border-bottom:1.0pt solid black;
  height:108.0pt;width:26pt'>No</td>
  <td colspan=3 class=xl13732029 width=294 style='border-right:1.0pt solid black;
  border-left:none;width:221pt'>Datos del Equipo</td>
  <td rowspan=2 class=xl13432029 width=89 style='border-bottom:1.0pt solid black;
  border-top:none;width:67pt'>Nivel de Actividad. Real<span
  style='mso-spacerun:yes'>  </span>(UM) Km</td>
  <td rowspan=2 class=xl13432029 width=83 style='border-bottom:1.0pt solid black;
  border-top:none;width:62pt'>Consumo. Real<span
  style='mso-spacerun:yes'>                        </span>( lts)</td>
  <td rowspan=2 class=xl13432029 width=108 style='border-bottom:1.0pt solid black;
  width:81pt'>Combustible que Debió Consumir.<span
  style='mso-spacerun:yes'>            </span>( Lts)</td>
  <td rowspan=2 class=xl13232029 width=62 style='border-bottom:1.0pt solid black;
  width:47pt'>Indice consumo real (UM)/lts</td>
  <td rowspan=2 class=xl13232029 width=63 style='border-bottom:1.0pt solid black;
  width:47pt'>Indice. Cons.Normado.<span style='mso-spacerun:yes'>  
  </span>(UM)/lts</td>
  <td rowspan=2 class=xl13432029 width=98 style='border-bottom:1.0pt solid black;
  width:74pt'>Diferencias en Consumo (litros).</td>
  <td rowspan=2 class=xl13432029 width=121 style='border-bottom:1.0pt solid black;
  width:91pt'>% Desviacion del indice normado.</td>
  <td rowspan=2 class=xl14032029 width=76 style='border-bottom:1.0pt solid black;
  width:57pt'>Desv. Abs.</td>
  <td class=xl6632029></td>
  <td class=xl6632029></td>
 </tr>
 <tr height=109 style='mso-height-source:userset;height:81.75pt'>
  <td height=109 class=xl7532029 width=126 style='height:81.75pt;border-left:
  none;width:95pt'>Descripción y tipo de vehículo</td>
  <td class=xl7532029 width=81 style='border-left:none;width:61pt'>chapa</td>
  <td class=xl7532029 width=87 style='border-left:none;width:65pt'>Indice de
  Consumo por datos de fábrica (UM)/lts</td>
  <td class=xl6632029></td>
  <td class=xl6632029></td>
 </tr>";

        $cont = 0;
        $nivel_act_total = 0;
        $consumo_total = 0;
        $comb_debio_cons_total = 0;
        $diferencia_cons_total = 0;
        foreach ($datos as $key => $value) {
            $cont++;
            $nivel_act_total += floatval($datos[$key]['nivel_actividad_real']);
            $consumo_total += floatval($datos[$key]['consumo_real']);
            $comb_debio_cons_total += ($datos[$key]['comb_debio_consumir']);
            $diferencia_cons_total += floatval($datos[$key]['diferencia_consumo']);
            $indice_real_total = $consumo_total == 0 ? 0 : $nivel_act_total / $consumo_total;
            $desv_total = $comb_debio_cons_total == 0 ? 0 : $diferencia_cons_total / $comb_debio_cons_total * 100;
            $desv_abs_total = abs($desv_total);


            $_html .= "<tr height=21 style='height:15.75pt'>
  <td height=21 class=xl7632029 align=right style='height:15.75pt'>" . $cont . "</td>
  <td class=xl10532029>" . $datos[$key]['descripcion'] . "</td>
  <td class=xl10632029>" . $datos[$key]['matricula'] . "</td>
  <td class=xl11432029>" . number_format($datos[$key]['indice_consumo_fabricante'], 2, ',', ".") . "</td>
  <td class=xl11532029 style='border-left:none'>" . number_format($datos[$key]['nivel_actividad_real'], 0) . "</td>
  <td class=xl11532029 style='border-left:none'>" . number_format($datos[$key]['consumo_real'], 0) . "</td>";

            if ($datos[$key]['nivel_actividad_real'] != 0) {
                $_html .= "<td class=xl10832029 style='border-left:none'>" . number_format(floatval($datos[$key]['comb_debio_consumir']), 2, ',', ".") . "</td>";
            } else {
                $_html .= "<td class=xl9732029 style='border-left:none;font-size:12.0pt;color:#CCCCFF;
  font-weight:400;text-decoration:none;text-underline-style:none;text-line-through:
  none;font-family:Arial;border-top:none;border-right:.5pt solid windowtext;
  border-bottom:.5pt solid windowtext;border-left:.5pt solid windowtext;
  background:#CCCCFF;mso-pattern:black none'>0,00</td>";
            }

            if ($datos[$key]['nivel_actividad_real'] != 0) {
                $_html .= "<td class=xl9832029 style='border-left:none'>" . number_format(floatval($datos[$key]['indice_real']), 2, ',', ".") . "</td>";
            } else {
                $_html .= "<td class=xl9832029 style='border-left:none;font-size:12.0pt;color:#CCCCFF;
  font-weight:400;text-decoration:none;text-underline-style:none;text-line-through:
  none;font-family:Arial;border-top:none;border-right:.5pt solid windowtext;
  border-bottom:.5pt solid windowtext;border-left:.5pt solid windowtext;
  background:#CCCCFF;mso-pattern:black none'>0,00</td>";
            }

            $_html .= "
  <td class=xl9932029 style='border-left:none'>" . number_format(floatval($datos[$key]['indice_plan']), 2, ',', ".") . "</td>";

            if ($datos[$key]['nivel_actividad_real'] != 0) {
                $_html .= "<td class=xl10232029 style='border-top:none;border-left:none'>" . number_format(floatval($datos[$key]['diferencia_consumo']), 2, ',', ".") . "</td>";
            } else {
                $_html .= "<td class=xl9832029 style='border-left:none;font-size:12.0pt;color:#CCCCFF;
  font-weight:400;text-decoration:none;text-underline-style:none;text-line-through:
  none;font-family:Arial;border-top:none;border-right:.5pt solid windowtext;
  border-bottom:.5pt solid windowtext;border-left:.5pt solid windowtext;
  background:#CCCCFF;mso-pattern:black none'>0,00</td>";
            }

            if ($datos[$key]['nivel_actividad_real'] != 0) {
                $_html .= "<td class=xl10232029 style='border-top:none;border-left:none'>" . number_format(floatval($datos[$key]['desviacion_indice_normado']), 2, ',', ".") . "</td>";
            } else {
                $_html .= "<td class=xl9832029 style='border-left:none;font-size:12.0pt;color:#CCCCFF;
  font-weight:400;text-decoration:none;text-underline-style:none;text-line-through:
  none;font-family:Arial;border-top:none;border-right:.5pt solid windowtext;
  border-bottom:.5pt solid windowtext;border-left:.5pt solid windowtext;
  background:#CCCCFF;mso-pattern:black none'>0,00</td>";
            }

            if (floatval($datos[$key]['desviacion_indice_normado_abs']) < 5) {
                $_html .= "<td class=xl9832029 style='border-left:none'>" . number_format(floatval($datos[$key]['desviacion_indice_normado_abs']), 2, ',', ".") . "</td>";
            } else {
                $_html .= "<td class=xl982096 style='border-left:none;font-size:12.0pt;color:windowtext;
  font-weight:400;text-decoration:none;text-underline-style:none;text-line-through:
  none;font-family:Arial;border-top:none;border-right:.5pt solid windowtext;
  border-bottom:.5pt solid windowtext;border-left:.5pt solid windowtext;
  background:red;mso-pattern:black none'>" . number_format(floatval($datos[$key]['desviacion_indice_normado_abs']), 2, ',', ".") . "</td>";
            }


            $_html .= "<td class=xl11132029>&nbsp;</td>
  <td class=xl12832029>&nbsp;</td>
 </tr>";

        }

        $_html .= "<tr height=22 style='height:16.5pt'>
  <td height=22 class=xl8432029 style='height:16.5pt;border-top:none'>&nbsp;</td>
  <td class=xl8532029 style='border-top:none'>&nbsp;</td>
  <td class=xl8632029 style='border-top:none;border-left:none'>&nbsp;</td>
  <td class=xl8632029 style='border-top:none;border-left:none'>&nbsp;</td>
  <td class=xl8632029 style='border-top:none;border-left:none'>" . number_format($nivel_act_total, 0) . "</td>
  <td class=xl8732029 style='border-top:none;border-left:none'>" . number_format($consumo_total, 1, ',', ".") . "</td>
  <td class=xl8732029 style='border-top:none;border-left:none'>" . number_format($comb_debio_cons_total, 1, ',', ".") . "</td>
  <td class=xl8832029 style='border-top:none;border-left:none'>" . number_format($indice_real_total, 2, ',', ".") . "</td>
  <td class=xl8832029 style='border-top:none;border-left:none'>&nbsp;</td>
  <td class=xl8832029 style='border-top:none;border-left:none'>" . number_format($diferencia_cons_total, 2, ',', ".") . "</td>
  <td class=xl8932029 style='border-top:none;border-right:none'>" . number_format($desv_total, 2, ',', ".") . "</td>";

        if ($desv_abs_total < 5) {
            $_html .= "<td class=xl9032029 style='border-top:none'>" . number_format($desv_abs_total, 2, ',', ".") . "</td>";
        } else {
            $_html .= "<td class=xl982096 style='border-left:none;font-size:12.0pt;color:windowtext;
  font-weight:400;text-decoration:none;text-underline-style:none;text-line-through:
  none;font-family:Arial;border-top:none;border-right:.5pt solid windowtext;
  border-bottom:.5pt solid windowtext;border-left:.5pt solid windowtext;
  background:red;mso-pattern:black none'>" . number_format($desv_abs_total, 2, ',', ".") . "</td>";
        }

        $_html .= "<td class=xl6632029></td>
  <td class=xl6632029></td>
 </tr>";

        $_html .= "<tr height=20 style='height:15.0pt'>
  <td height=20 class=xl9132029 style='height:15.0pt'></td>
  <td class=xl9232029></td>
  <td class=xl9232029></td>
  <td class=xl9232029></td>
  <td class=xl9232029></td>
  <td class=xl9332029></td>
  <td class=xl9332029></td>
  <td class=xl9432029></td>
  <td class=xl9432029></td>
  <td class=xl9332029></td>
  <td class=xl9432029></td>
  <td class=xl9432029></td>
  <td class=xl6632029></td>
  <td class=xl6632029></td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20 class=xl9132029 style='height:15.0pt'></td>
  <td class=xl9232029></td>
  <td class=xl9232029></td>
  <td class=xl9232029></td>
  <td class=xl9532029></td>
  <td class=xl9532029></td>
  <td class=xl9132029></td>
  <td class=xl9432029></td>
  <td class=xl9432029></td>
  <td class=xl9332029></td>
  <td class=xl9432029></td>
  <td class=xl9432029></td>
  <td class=xl6632029></td>
  <td class=xl6632029></td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20 class=xl9132029 style='height:15.0pt'></td>
  <td class=xl9232029></td>
  <td class=xl9232029></td>
  <td class=xl9232029></td>
  <td colspan=3 class=xl13132029></td>
  <td class=xl9432029></td>
  <td class=xl9432029></td>
  <td class=xl9432029></td>
  <td class=xl9432029></td>
  <td class=xl9432029></td>
  <td class=xl6632029></td>
  <td class=xl6632029></td>
 </tr>
 <![if supportMisalignedColumns]>
 <tr height=0 style='display:none'>
  <td width=35 style='width:26pt'></td>
  <td width=126 style='width:95pt'></td>
  <td width=81 style='width:61pt'></td>
  <td width=87 style='width:65pt'></td>
  <td width=89 style='width:67pt'></td>
  <td width=83 style='width:62pt'></td>
  <td width=108 style='width:81pt'></td>
  <td width=62 style='width:47pt'></td>
  <td width=63 style='width:47pt'></td>
  <td width=98 style='width:74pt'></td>
  <td width=121 style='width:91pt'></td>
  <td width=76 style='width:57pt'></td>
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
        for ($i = 1, $iMax = count($_unidades); $i < $iMax; $i++) {
            $_string_unidades .= ",'" . $_unidades[$i] . "'";
        }
        return $_string_unidades;
    }
}