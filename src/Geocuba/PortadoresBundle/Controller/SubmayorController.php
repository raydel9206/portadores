<?php
/**
 * Created by PhpStorm.
 * User: javier
 * Date: 20/05/2016
 * Time: 14:12
 */


namespace Geocuba\PortadoresBundle\Controller;
use Geocuba\AdminBundle\Util\FechaUtil;
use Geocuba\AdminBundle\Util\Util;
use Doctrine\Common\CommonException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;

class SubmayorController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $noValeAnticipo = strtolower(trim($request->get('noValeAnticipo')));
        $filtro = '';
        if($noValeAnticipo != '')
            $filtro = " lower(anticipo.no_vale) like lower('%$noValeAnticipo%') and ";

        $anno = $request->get('anno');
        $mes = $request->get('mes');
        $nunidadid = $request->get('unidadid');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $unidades_string = $this->unidadesToString($_unidades);

        $fechaFinal = FechaUtil::getUltimoDiaMes($mes, $anno). ' 24:00:00';
        $fechaInicial = $anno. '-' . $mes . '-01' . " 00:00:00";

        $conn = $this->get('database_connection');
        $entities = $conn->fetchAll("SELECT anticipo.id, anticipo.no_vale, anticipo.importe, anticipo.cantidad, anticipo.fecha, to_char(anticipo.fecha,'DD/MM/YYYY') as gfecha,
        max(persona.nombre) as chofer, max(vehiculo.matricula) as matricula, max(tipo_combustible.nombre) as tipo_combustible,
        (case when anticipo.visible=false and anticipo.transito=true then 'Cancelado/Tránsito' when anticipo.visible=false then 'Cancelado' when anticipo.transito=true then 'Tránsito' end) as actividad,
        max(liquidacion.centro_costo) as centro_costo, max(tarjeta.nro_tarjeta) as nro_tarjeta
        FROM datos.anticipo
        left join nomencladores.persona ON anticipo.npersonaid = persona.id
        left join nomencladores.vehiculo ON anticipo.vehiculo = vehiculo.id
        left join nomencladores.tarjeta ON anticipo.tarjeta = tarjeta.id
        left join nomencladores.tipo_combustible ON tarjeta.ntipo_combustibleid = tipo_combustible.id

        left join( select anticipo as ant, max(nombre ) as centro_costo
            from datos.liquidacion
            inner join nomencladores.centro_costo ON liquidacion.ncentrocostoid = centro_costo.id
            group by anticipo ) as liquidacion ON liquidacion.ant = anticipo.id

        where $filtro tarjeta.nunidadid in ($unidades_string) and anticipo.fecha between '$fechaInicial' and '$fechaFinal'
        group by anticipo.id
        order by anticipo;");

        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity['id'],
                'noValeAnticipo' => $entity['no_vale'],
                'fechaVale' => $entity['gfecha'],
                'gfecha' => $entity['gfecha'],
                'matricula' => $entity['matricula'],
                'ntarjeta' => $entity['nro_tarjeta'],
                'choferNombre' => $entity['chofer'],
                'actividad' => $entity['actividad'],
                'tipoCombustible' => $entity['tipo_combustible'],
                'cantLitros' => $entity['cantidad'],
                'centro_costo' => $entity['centro_costo'],
            );
        }
        return new JsonResponse(array('rows' => $_data, 'total' => count($_data)));
    }

    public function printAction(Request $request)
    {
        $data = json_decode($request->get('store'));

        $group = $request->get('group');

        $anno = $request->get('anno');
        $mes = FechaUtil::getNombreMes($request->get('mes'));
        $_html = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
        <html xmlns='http://www.w3.org/1999/xhtml'>
         <head>
          <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
           <title>Submayor de Vales de Anticipo y Liquidación de Combustible</title>
            <style>
            table {
                border:0 solid;
                border-radius:0;
                font-family: 'Arial', serif;
                font-size: 11px;
                border-collapse: collapse;
            }
            td{
                height: 10px;
                padding: 2px;
            }
        </style>
         </head>
          <body>
          <header>
                <img  src='../../assets/img/PNG/logo.png' height='60px' width='160px'>
            </header>
            </br>
           <table cellspacing='0' cellpadding='5' border='1' width='100%'>             
             <tr>
              <td colspan='8' style='text-align: center; border: none; font-size: 14px;'><strong>Submayor de Vales de Anticipo y Liquidación de Combustible</strong></td>
             </tr>
             <tr>
              <td colspan='2' style='text-align: center; border: none'></td>
              <td colspan='2' style='text-align: left; border: none;font-size: 12px;'><strong>Año:</strong><strong>$anno</strong></td>
              <td colspan='2' style='text-align: right; border: none;font-size: 12px;'><strong>Mes:</strong><strong>$mes</strong></td>
              <td colspan='2' style='text-align: center; border: none'></td>
             </tr>
            <tr>
              <td style='text-align: center'><strong>Vale</strong></td>
              <td style='text-align: center'><strong>Fecha</strong></td>
              <td style='text-align: center'><strong>Chofer</strong></td>
              <td style='text-align: center' ><strong>Chapa</strong></td>
              <td style='text-align: center' ><strong>Centro Costo</strong></td>
              <td style='text-align: center' ><strong>Depósito</strong></td>
              <td style='text-align: center'><strong>Tipo de Combustible</strong></td>
              <td style='text-align: center'><strong>Cantidad</strong></td>
              <td style='text-align: center'><strong>Observaciones</strong></td>
            </tr>";
        $noTarjeta=array();
        foreach($data as $da){
            if (array_search($da->$group, $noTarjeta) === false)
                $noTarjeta[] = $da->$group;
        }
        $arr_totales = array();
        foreach ($noTarjeta as $no) {
            $arr_totales[] = array('$group' => $no, 'total_cantidad' => 0);
        }

        if(count($data)>0){

            $noTarjeta=$data[0]->$group;
        }

        $noT = ''; $sumar=0;
        for($i=0;$i<count($data);$i++){
            if($noT != $data[$i]->$group){
                $noT = $data[$i]->$group;
                $total = 0;
                foreach($data as $arr){
                    if($arr->$group == $noT){
                        $total += 1;
                    }
                }
                $anticipo = 'Anticipo';
                if($total>1) $anticipo = 'Anticipos';
                $_html .= "<tr>
                    <td colspan='9' style='text-align: left;'><strong>" . $data[$i]->$group  ."</strong>(". $total ." ". $anticipo .")</td>
                </tr>";
            }
            $_html.="<tr>
              <td style='text-align: center'> ".$data[$i]->noValeAnticipo."</td>
              <td style='text-align: center'> ".$data[$i]->fechaVale."</td>
              <td style='text-align: left'>".$data[$i]->choferNombre."</td>
              <td style='text-align: center'>".$data[$i]->matricula."</td>
              <td style='text-align: left'>".$data[$i]->centro_costo."</td>
              <td style='text-align: left'>".$data[$i]->ntarjeta."</td>
              <td style='text-align: center'> ".$data[$i]->tipoCombustible."</td>
              <td style='text-align: center'> ".$data[$i]->cantLitros."</td>
              <td style='text-align: left'> ".$data[$i]->actividad."</td>
            </tr>
            ";
          if($i>=count($data)-1){
                  $sumar+=$data[$i]->cantLitros;
                  $_html .= "<tr>
                    <td style='text-align: center'></td>
                    <td style='text-align: center'></td>
                    <td style='text-align: center'><strong>Total</strong></td>
                    <td style='text-align: center'></td>
                    <td style='text-align: center'></td>
                    <td style='text-align: center'></td>
                    <td style='text-align: center'></td>
                    <td style='text-align: center'><strong>$sumar</strong></td>
                    <td style='text-align: center'></td>
                    </tr>";
          }
           else{
               if ($noTarjeta != $data[$i+1]->$group) {
                   $noTarjeta = $data[$i+1]->$group;
                   $sumar+=$data[$i]->cantLitros;
                   $_html .= "<tr>
                    <td style='text-align: center'></td>
                    <td style='text-align: center'></td>
                    <td style='text-align: center'><strong>Total</strong></td>
                    <td style='text-align: center'></td>
                    <td style='text-align: center'></td>
                    <td style='text-align: center'></td>
                    <td style='text-align: center'></td>
                    <td style='text-align: center'><strong>$sumar</strong></td>
                    <td style='text-align: center'></td>
                    </tr>";
                   $sumar=0;
               }
               else{
                   $sumar+=$data[$i]->cantLitros;
               }
           }
        }

        $_html.="
               </table>
               </body>
             </html>";
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