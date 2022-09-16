<?php
/**
 * Created by PhpStorm.
 * User: Yosley
 * Date: 07/10/2015
 * Time: 9:58
 */

namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\PortadoresBundle\Util\FechaUtil;
use Geocuba\AdminBundle\Util\Util;
use Geocuba\PortadoresBundle\Entity\Accidente;
use Geocuba\PortadoresBundle\Entity\Nparalizacion;
use Doctrine\Common\CommonException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;


class AccidenteController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $_matricula = trim($request->get('nombre'));
        $start = $request->get('start');
        $limit = $request->get('limit');
        $nunidadid = $request->get('nunidadid');
        //$choferes ='';
        $_data = array();
        $em = $this->getDoctrine()->getManager();

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $entities = $em->getRepository('PortadoresBundle:Accidente')->buscarAccidentes($_matricula, $_unidades, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:Accidente')->buscarAccidentes($_matricula, $_unidades, $start, $limit, true);


        foreach ($entities as $entity) {
            $choferes = $em->getRepository('PortadoresBundle:VehiculoPersona')->findBy(array('idvehiculo' => $entity->getVehiculoid()->getId(),'visible'=>true));
            $nombre_chofer = count($choferes) > 0 ? $choferes[0]->getIdpersona()->getNombre(): 'Sin Asignar Chofer';
            if (count($choferes) > 1) {
                for ($i = 1; $i < count($choferes); $i++) {
                    $nombre_chofer .= ', '.$choferes[$i]->getIdpersona()->getNombre();
                }
            }
            $_data[] = array(
                'id' => $entity->getId(),
                'vehiculoid' => $entity->getVehiculoid()->getId(),
                'vehiculo_marca' => $entity->getVehiculoid()->getNmodeloid()->getMarcaVehiculoid()->getNombre() . ' - ' . $entity->getVehiculoid()->getNmodeloid()->getNombre(),
                'vehiculo_matricula' => $entity->getVehiculoid()->getMatricula(),
                'chofer' => $nombre_chofer,
                'asignado' => $entity->getAsignado(),
                'nota_informativa' => $entity->getNotaInformativa(),
                'fecha_accidente' => ($entity->getFechaAccidente() != null) ? $entity->getFechaAccidente()->format('d/m/Y') : null,
                'fecha_indemnizacion' => ($entity->getFechaIndemnizado() != null) ? $entity->getFechaIndemnizado()->format('d/m/Y') : null,
                'importe_indemnizacion' => $entity->getImporteIndemnizacion(),
                'nunidadid' => $entity->getVehiculoid()->getNunidadid()->getId(),
                'nunidadnombre' => $entity->getVehiculoid()->getNunidadid()->getNombre(),
            );
        };
        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $vehiculoid = $request->get('vehiculoid');
        $asignado = $request->get('asignado');
        $nota_informativa = $request->get('nota_informativa');
        $fecha_accidente = $request->get('fecha_accidente');
        $fecha_indemnizado = $request->get('fecha_indemnizacion');
        $importe_indemnizacion = $request->get('importe_indemnizacion');

        $var = date_create_from_format('d/m/Y', $fecha_accidente);
        $var1 = null;
        if (isset($fecha_indemnizado) && $fecha_indemnizado != '')
            $var1 = date_create_from_format('d/m/Y', $fecha_indemnizado);

        $entities_lic = $em->getRepository('PortadoresBundle:Accidente')->findBy(array(
            'vehiculoid' => $vehiculoid,
            'visible' => true
        ));
        if ($entities_lic)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Este vehículo ya está accidentado.'));

        $entity = new Accidente();
        $entity->setVehiculoid($em->getRepository('PortadoresBundle:Vehiculo')->find($vehiculoid));
        $entity->setAsignado($asignado);
        $entity->setNotaInformativa($nota_informativa);
        $entity->setFechaAccidente($var);
        $entity->setFechaIndemnizado($var1);
        $entity->setImporteIndemnizacion((isset($importe_indemnizacion) && $importe_indemnizacion != '') ? $importe_indemnizacion : null);
        $entity->setVisible(true);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Accidente registrado con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function modAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $vehiculoid = $request->get('vehiculoid');
        $asignado = $request->get('asignado');
        $nota_informativa = $request->get('nota_informativa');
        $fecha_accidente = $request->get('fecha_accidente');
        $fecha_indemnizado = $request->get('fecha_indemnizacion');
        $importe_indemnizacion = $request->get('importe_indemnizacion');
        $var = date_create_from_format('d/m/Y', $fecha_accidente);
        $var1 = null;
        if (isset($fecha_indemnizado) && $fecha_indemnizado != '')
            $var1 = date_create_from_format('d/m/Y', $fecha_indemnizado);

        $entity = $em->getRepository('PortadoresBundle:Accidente')->find($id);
        $entity->setVehiculoid($em->getRepository('PortadoresBundle:Vehiculo')->find($vehiculoid));
        $entity->setAsignado($asignado);
        $entity->setNotaInformativa($nota_informativa);
        $entity->setFechaAccidente($var);
        $entity->setFechaIndemnizado($var1);
        $entity->setImporteIndemnizacion((isset($importe_indemnizacion) && $importe_indemnizacion != '') ? $importe_indemnizacion : null);
        $entity->setVisible(true);
        try {
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Accidente modificado con éxito.'));
            return $response;
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function delAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');

        $entity = $em->getRepository('PortadoresBundle:Accidente')->find($id);
        $entity->setVisible(false);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Accidente eliminado con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function exportToExcelAction(Request $request)
    {
        $nunidadid = $request->get('nunidadid');

        $_data = array();
        $em = $this->getDoctrine()->getManager();

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $entities = $em->getRepository('PortadoresBundle:Accidente')->buscarAccidentes(null, $_unidades);

        foreach ($entities as $entity) {
            $choferes = $em->getRepository('PortadoresBundle:VehiculoPersona')->findBy(array('idvehiculo' => $entity->getVehiculoid()->getId(), 'visible'=>true));
            $nombre_chofer = count($choferes) > 0 ? $choferes[0]->getIdpersona()->getNombre(): 'Sin Asignar Chofer';
            if (count($choferes) > 1) {
                for ($i = 1; $i < count($choferes); $i++) {
                    $nombre_chofer .= ', '.$choferes[$i]->getIdpersona()->getNombre();
                }
            }
            $_data[] = array(
                'id' => $entity->getId(),
                'vehiculoid' => $entity->getVehiculoid()->getId(),
                'vehiculo_marca' => $entity->getVehiculoid()->getNmodeloid()->getMarcaVehiculoid()->getNombre() . ' - ' . $entity->getVehiculoid()->getNmodeloid()->getNombre(),
                'vehiculo_matricula' => $entity->getVehiculoid()->getMatricula(),
                'chofer' => $nombre_chofer,
                'asignado' => $entity->getAsignado(),
                'nota_informativa' => $entity->getNotaInformativa(),
                'fecha_accidente' => ($entity->getFechaAccidente() != null) ? $entity->getFechaAccidente()->format('d/m/Y') : null,
                'fecha_indemnizacion' => ($entity->getFechaIndemnizado() != null) ? $entity->getFechaIndemnizado()->format('d/m/Y') : null,
                'importe_indemnizacion' => $entity->getImporteIndemnizacion(),
                'nunidadid' => $entity->getVehiculoid()->getNunidadid()->getId(),
                'nunidadnombre' => $entity->getVehiculoid()->getNunidadid()->getNombre(),
            );
        }
        $session = $request->getSession();
        $anno = $session->get('current_year');
        $mes = FechaUtil::getNombreMes($session->get('current_month'));

        $phpexcel = $this->get('phpexcel');
        $excel_object = $phpexcel->createPHPExcelObject();
        $active_sheet = $excel_object->getActiveSheet();
        $active_sheet->setTitle('Accidente');
        //Header
        $active_sheet->mergeCells('A1:K1');
        $active_sheet->mergeCells('A2:K2');
        $active_sheet->mergeCells('A3:K3');
        $active_sheet->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $active_sheet->getStyle('A2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $active_sheet->getStyle('A3')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $active_sheet->setCellValue('A1', 'REPORTE DE ACCIDENTES');
        $active_sheet->setCellValue('A2', 'MES: ' . $mes . '    AÑo: ' . $anno);

        $active_sheet->setCellValue('A4', 'Nro.');
        $active_sheet->getStyle('A4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $active_sheet->setCellValue('B4', 'POLO');
        $active_sheet->getStyle('B4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $active_sheet->setCellValue('C4', 'Tipo de auto');
        $active_sheet->getStyle('C4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $active_sheet->setCellValue('D4', 'Matricula');
        $active_sheet->getStyle('D4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $active_sheet->setCellValue('E4', 'Nombre del chofer');
        $active_sheet->getStyle('E4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $active_sheet->setCellValue('F4', 'Asignado a');
        $active_sheet->getStyle('F4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $active_sheet->setCellValue('G4', 'Fecha de accidente');
        $active_sheet->getStyle('G4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $active_sheet->setCellValue('H4', 'Nota Informativa');
        $active_sheet->getStyle('H4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $active_sheet->setCellValue('I4', 'Fecha Indemnizado');
        $active_sheet->getStyle('I4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $active_sheet->setCellValue('J4', 'Importe Indemnizacion');
        $active_sheet->getStyle('J4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $pos = 5;
        for ($row = 0; $row < count($_data); $row++) {
            $pos += $row;
            $active_sheet->setCellValue('A' . $pos, $row + 1);
            $active_sheet->setCellValue('B' . $pos, $_data[$row]['nunidadnombre']);
            $active_sheet->setCellValue('C' . $pos, $_data[$row]['vehiculo_marca']);
            $active_sheet->setCellValue('D' . $pos, $_data[$row]['vehiculo_matricula']);
            $active_sheet->setCellValue('E' . $pos, $_data[$row]['chofer']);
            $active_sheet->setCellValue('F' . $pos, $_data[$row]['asignado']);
            $active_sheet->setCellValue('G' . $pos, $_data[$row]['fecha_accidente']);
            $active_sheet->setCellValue('H' . $pos, $_data[$row]['nota_informativa']);
            $active_sheet->setCellValue('I' . $pos, $_data[$row]['fecha_indemnizacion']);
            $active_sheet->setCellValue('J' . $pos, $_data[$row]['importe_indemnizacion']);
        }

        $pos += 3;
        $active_sheet->mergeCells('A' . $pos . ':J' . $pos);
        $active_sheet->getStyle('A' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $active_sheet->setCellValue('A' . $pos, 'Índice accidentabilidad: ' . '(calculo)');

        $_elaborado = $request->get('elaboradoid');
        $_revisado = $request->get('revisadoid');
        $_aprobado = $request->get('aprobadoid');

        $pos += 4;
        $active_sheet->mergeCells('A' . $pos . ':C' . $pos);
        $active_sheet->getStyle('A' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $active_sheet->setCellValue('A' . $pos, 'Elaborado: ' . $em->getRepository('PortadoresBundle:Npersona')->find($_elaborado)->getNombre());
        $active_sheet->setCellValue('E' . $pos, 'Revisado: ' . $em->getRepository('PortadoresBundle:Npersona')->find($_revisado)->getNombre());
        $active_sheet->setCellValue('G' . $pos, 'Aprobado: ' . $em->getRepository('PortadoresBundle:Npersona')->find($_aprobado)->getNombre());

        $pos += 1;
        $active_sheet->mergeCells('A' . $pos . ':C' . $pos);
        $active_sheet->getStyle('A' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $active_sheet->setCellValue('A' . $pos, $em->getRepository('PortadoresBundle:Npersona')->find($_elaborado)->getCargoid()->getNombre());
        $active_sheet->setCellValue('E' . $pos, $em->getRepository('PortadoresBundle:Npersona')->find($_revisado)->getCargoid()->getNombre());
        $active_sheet->setCellValue('G' . $pos, $em->getRepository('PortadoresBundle:Npersona')->find($_aprobado)->getCargoid()->getNombre());

        $pos += 2;
        $active_sheet->mergeCells('A' . $pos . ':C' . $pos);
        $active_sheet->setCellValue('A' . $pos, '_______________');
        $active_sheet->getStyle('A' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $active_sheet->setCellValue('E' . $pos, '_______________');
        $active_sheet->getStyle('E' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $active_sheet->setCellValue('G' . $pos, '_______________');
        $active_sheet->getStyle('G' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::VERTICAL_CENTER);


        $pos += 1;
        $active_sheet->mergeCells('A' . $pos . ':C' . $pos);
        $active_sheet->setCellValue('A' . $pos, 'FIRMA');
        $active_sheet->getStyle('A' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $active_sheet->setCellValue('E' . $pos, 'FIRMA');
        $active_sheet->getStyle('E' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $active_sheet->setCellValue('G' . $pos, 'FIRMA');
        $active_sheet->getStyle('G' . $pos)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::VERTICAL_CENTER);


        return Util::setExcelStreamedResponseHeaders($phpexcel->createStreamedResponse($phpexcel->createWriter($excel_object)), 'Accidente (' . $mes . '-' . $anno . ').xls');
    }
}