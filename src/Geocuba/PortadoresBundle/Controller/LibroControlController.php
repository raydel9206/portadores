<?php
/**
 * Created by PhpStorm.
 * User: orlando
 * Date: 12/12/2016
 */


namespace Geocuba\PortadoresBundle\Controller;
use Geocuba\AdminBundle\Util\Util;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\DocumentosEnum;
use Geocuba\PortadoresBundle\Util\Utiles;


class LibroControlController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $nunidadid = $request->get('unidadid');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $fechaDesdeStr = trim($request->get('fechaDesde'));
        $fechaHastaStr = trim($request->get('fechaHasta'));
        if (is_null($fechaDesdeStr) || $fechaDesdeStr == '' || is_null($fechaHastaStr) || $fechaHastaStr == '')
            return new JsonResponse(array('rows' => array(), 'total' => 0));

        $fechaDesde = date_create_from_format('d/m/Y', trim($request->get('fechaDesde')));
        $fechaHasta = date_create_from_format('d/m/Y', trim($request->get('fechaHasta')));

        $fechaDesdeStr = $fechaDesde->format('Y-m-d');
        $fechaHastaStr = $fechaHasta->format('Y-m-d');

        $start = $request->get('start');
        $limit = $request->get('limit');

        $entities = $em->getRepository('PortadoresBundle:Ordenes')->buscarOrdenesLibro($fechaDesdeStr, $fechaHastaStr, $_unidades, 2, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:ordenes')->buscarOrdenesLibro($fechaDesdeStr, $fechaHastaStr, $_unidades, 2,$start, $limit, true);



        $_data = array();
        foreach ($entities as $entity) {
            if($entity->getTrabajoOtroTipo() == '' || $entity->getTrabajoOtroTipo() == null)
                $mantenimiento = $entity->getTipoMantenimiento()->getNombre() .' / '. $entity->getTipoMantenimiento()->getClasificacion()->getNombre();
            else
                $mantenimiento = $entity->getTrabajoOtroTipo();
            if($entity->getFechaCierre()){
                $fechaCierre = $entity->getFechaCierre()->format('Y-m-d');
                $horaCierre = $entity->getFechaCierre()->format('g:i A');
            }
            else{
                $fechaCierre = '';
                $horaCierre = '';
            }

            $_data[] = array(
                'noOrden' => $entity->getNoOrden(),
                'matricula' => $entity->getNvehiculoid()->getMatricula(),
                'marca' => $entity->getNvehiculoid()->getNmodeloid()->getNmarcaVehiculoid()->getNombre(),
                'modelo' => $entity->getNvehiculoid()->getNmodeloid()->getNombre(),
                'unidad' => $entity->getNunidadid()->getNombre(),
                'fechaEmision' => $entity->getFechaEmision()->format('Y-m-d'). ' - ' . $entity->getFechaEmision()->format('g:i A'),
                'horaEmision' => $entity->getFechaEmision()->format('g:i A'),
                'fechaCierre' => $fechaCierre .' - '. $horaCierre,
                'horaCierre' => $horaCierre,
                'operacion' => $mantenimiento .' // '. $entity->getServiciosTerceros(),
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    public function printAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $nunidadid = $request->get('unidadid');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $fechaDesdeStr = trim($request->get('fechaDesde'));
        $fechaHastaStr = trim($request->get('fechaHasta'));
        if (is_null($fechaDesdeStr) || $fechaDesdeStr == '' || is_null($fechaHastaStr) || $fechaHastaStr == '')
            return new JsonResponse(array('rows' => array(), 'total' => 0));

        $fechaDesde = date_create_from_format('d/m/Y', trim($request->get('fechaDesde')));
        $fechaHasta = date_create_from_format('d/m/Y', trim($request->get('fechaHasta')));

        $fechaDesdeStr = $fechaDesde->format('Y-m-d');
        $fechaHastaStr = $fechaHasta->format('Y-m-d');

        $start = $request->get('start');
        $limit = $request->get('limit');

        $entities = $em->getRepository('PortadoresBundle:Ordenes')->buscarOrdenesLibro($fechaDesdeStr, $fechaHastaStr, $_unidades, 2, $start, $limit);

        $_html = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
        <html xmlns='http://www.w3.org/1999/xhtml'>
        <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <title>REPORTE - LIBRO DE CONTROL</title>
        <style>

            table {
                border:0 solid;
                border-radius:0;
                font-family: 'Arial', serif;
                font-size: 14px;
                border-collapse: collapse;
            }

            td{
                width: 40px;
                height: 20px;
            }

        </style>
        </head>

        <body>
        <table cellspacing='0' cellpadding='5' border='1' width='100%'>

         <tr>
            <td colspan='9' style='text-align: center;border: none;font-size: 14px;'><strong>REPORTE - LIBRO DE CONTROL</strong></td>
         </tr>

         <tr>
            <td colspan='5' style='text-align: center;border: none;font-size: 12px;'><strong>Desde:</strong> $fechaDesdeStr</td>
            <td colspan='4' style='text-align: center;border: none;font-size: 12px;'><strong>Hasta:</strong> $fechaHastaStr</td>
         </tr>

         <tr>
            <td style='text-align: center;width: 5%'><strong>No. Orden</strong></td>
            <td style='text-align: center;width: 10%'><strong>Matrícula</strong></td>
            <td style='text-align: center;width: 10%'><strong>Marca</strong></td>
            <td style='text-align: center;width: 10%;'><strong>Modelo</strong></td>
            <td style='text-align: center;width: 15%;'><strong>Fecha/Hora Emisión</strong></td>
            <td style='text-align: center;width: 15%;'><strong>Fecha/Hora Cierre</strong></td>
            <td style='text-align: center;width: 20%;'><strong>Operaciones</strong></td>

         </tr>";

        $unidad = -1;
        foreach ($entities as $entity) {

            if($entity->getTrabajoOtroTipo() == '' || $entity->getTrabajoOtroTipo() == null)
                $mantenimiento = $entity->getTipoMantenimiento()->getNombre() .' / '. $entity->getTipoMantenimiento()->getClasificacion()->getNombre();
            else
                $mantenimiento = $entity->getTrabajoOtroTipo();
            if($entity->getFechaCierre()){
                $fechaCierre = $entity->getFechaCierre()->format('Y-m-d');
                $horaCierre = $entity->getFechaCierre()->format('g:i A');
            }
            else{
                $fechaCierre = '';
                $horaCierre = '';
            }

            if($unidad != $entity->getNunidadid()->getNombre())
            {
                $unidad = $entity->getNunidadid()->getNombre();
                $_html .= "<tr>
                        <td colspan='9' style='text-align: center;'><strong>" . $entity->getNunidadid()->getNombre() . "<strong></td>
                    </tr>";
            }

            $_html .= "<tr>
                <td style='text-align: left;'>" . $entity->getNoOrden() . "</td>
                <td style='text-align: left;'>" . $entity->getNvehiculoid()->getMatricula() . "</td>
                <td style='text-align: left;'>" . $entity->getNvehiculoid()->getNmodeloid()->getNmarcaVehiculoid()->getNombre() . "</td>
                <td style='text-align: left;'>" . $entity->getNvehiculoid()->getNmodeloid()->getNombre() . "</td>
                <td style='text-align: center;'>" . $entity->getFechaEmision()->format('Y-m-d') . ' - ' .$entity->getFechaEmision()->format('g:i A') . "</td>
                <td style='text-align: center;'>" . $fechaCierre . ' - ' .$horaCierre. "</td>
                <td style='text-align: left;'>" . $mantenimiento .' // '. $entity->getServiciosTerceros() . "</td>
            </tr>";
        }

        $_html .= "<tr>
                <td colspan='5' style='text-align: center;border: none;'>CONFECCIONADO POR: _______________________________________________  </td>
                <td colspan='4' style='text-align: center;border: none;'>APROBADO POR: __________________________________________________</td>
            </tr>";

        $_html .= "</table>
                </body>
            </html>";

        return new Response(json_encode(array('success' => true, 'html' => $_html)));
    }
}