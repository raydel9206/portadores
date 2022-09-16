<?php

namespace Geocuba\PortadoresBundle\Controller;

use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\ORM\EntityManager;
use Exception;
use Geocuba\PortadoresBundle\Entity\Anexo3Tecnologicos;
use Geocuba\PortadoresBundle\Entity\Anexo3TecnologicosDesglose;
use Geocuba\PortadoresBundle\Entity\RegistroOperacion;
use Geocuba\PortadoresBundle\Util\FechaUtil;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Throwable;


class Anexo3TecnologicosController extends Controller
{
    use ViewActionTrait;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loadAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $equipoId = trim($request->get('equipo_id'));
        $anno = $request->get('anno');
        $mes = $request->get('mes');

        /** @var Anexo3Tecnologicos $anexo3 */
        $anexo3 = $em->getRepository('PortadoresBundle:Anexo3Tecnologicos')->findOneBy(['equipo' => $equipoId, 'mes' => $mes, 'anno' => $anno]);

        $desglose = $em->getRepository('PortadoresBundle:Anexo3TecnologicosDesglose')->findBy(['anexo3' => $anexo3]);

        $extraData = [];
        if ($anexo3) {
            $extraData = [
                'operario_id' => $anexo3->getOperario()->getId(),
                'operario_nombre' => $anexo3->getOperario()->getNombre(),
                'responsable_id' => $anexo3->getResponsable()->getId(),
                'responsable_nombre' => $anexo3->getResponsable()->getNombre(),
                'area_id' => $anexo3->getArea()->getId(),
                'area_nombre' => $anexo3->getArea()->getNombre(),
                'folio' => $anexo3->getFolio(),
                'anexo_id' => $anexo3->getId()
            ];
        }

        $_rows = array_map(static function ($row) {
            /** @var Anexo3TecnologicosDesglose $row */
            return [
                'id' => $row->getId(),
                'anexo3_id' => $row->getAnexo3()->getId(),
                'dia' => $row->getDia(),
                'actividad' => $row->getActividad()->getNombre(),
                'actividad_id' => $row->getActividad()->getId(),
                'indice_normado' => $row->getIndiceNormado(),
                'hora_inicio' => $row->getHoraInicio(),
                'hora_parada' => $row->getHoraParada(),
                'tiempo_empleado' => $row->getTiempoEmpleado(),
                'nivel_act_real' => $row->getNivelActReal(),
                'combustible_debio_consumir' => $row->getCombustibleDebioConsumir(),
                'combustible_restante' => $row->getCombustibleRestante(),
                'combustible_real' => $row->getCombustibleReal(),
                'combustible_abastecido' => $row->getCombustibleAbastecido(),
                'indice_real' => $row->getIndiceReal(),
                'diferencia_real_plan' => $row->getDiferenciaRealPlan(),
                'porciento_desviacion' => $row->getPorcientoDesviacion()
            ];
        }, $desglose);

        usort($_rows, static function($a, $b) {
            return $a['dia'] - $b['dia'];
        });


        return new JsonResponse(['success' => true, 'rows' => $_rows, 'extra_data' => $extraData]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws MappingException
     * @throws Throwable
     */
    public function generateAction(Request $request): JsonResponse
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $equipoId = $request->get('equipo_id');
        $anno = $request->get('anno');
        $mes = $request->get('mes');

        $fechaDesde = $anno . '-' .'01-01 00:00:00';
        $fechaHasta = FechaUtil::getUltimoDiaMes($mes, $anno);

        $operaciones = $em->getRepository('PortadoresBundle:RegistroOperacion')->findAllBy($equipoId, $fechaDesde, $fechaHasta);
        if (!$operaciones) return new JsonResponse(['success' => false, 'message' => 'No existen operaciones registradas en el mes', 'cls' => 'warning']);


        try {
            $em->transactional(static function ($em) use ($request, $operaciones, $equipoId, $mes, $anno) {
                /** @var EntityManager $em */

                $oldAnexo3 = $em->getRepository('PortadoresBundle:Anexo3Tecnologicos')->findOneBy(['equipo' => $equipoId, 'mes' => $mes, 'anno' => $anno]);
                $desglose = $em->getRepository('PortadoresBundle:Anexo3TecnologicosDesglose')->findBy(['anexo3' => $oldAnexo3]);

                if ($oldAnexo3) $em->remove($oldAnexo3);
                if ($desglose) foreach ($desglose as $d) $em->remove($d);

                $areaId = $request->get('area_id');
                $operarioId = $request->get('operario_id');
                $responsableId = $request->get('responsable_id');

                $equipo = $em->find('PortadoresBundle:EquipoTecnologico', $equipoId);

                $anexo3 = new Anexo3Tecnologicos();
                $anexo3->setAnno($anno)
                    ->setMes($mes)
                    ->setFolio($request->get('folio'))
                    ->setArea($em->find('PortadoresBundle:Area', $areaId))
                    ->setOperario($em->find('PortadoresBundle:Persona', $operarioId))
                    ->setResponsable($em->find('PortadoresBundle:Persona', $responsableId))
                    ->setEquipo($equipo)
                    ->setTipoCombustible($equipo->getTipoCombustible());

                $em->persist($anexo3);

                foreach ($operaciones as $operacion) {
                    /** @var RegistroOperacion $operacion */
                    $indiceConsumoReal = $operacion->getConsumoReal() / $operacion->getNivelActividadReal();
                    $indiceConsumoNormado = $operacion->getIndiceNormado();
                    $desglose = new Anexo3TecnologicosDesglose();
                    $desglose->setAnexo3($anexo3)
                        ->setDia($operacion->getFecha()->format('d'))
                        ->setActividad($operacion->getActividad())
                        ->setIndiceNormado($indiceConsumoNormado)
                        ->setHoraInicio($operacion->getHoraArranque()->format('h:i'))
                        ->setHoraParada($operacion->getHoraParada()->format('h:i'))
                        ->setTiempoEmpleado($operacion->getTiempoTrabajado())
                        ->setNivelActReal($operacion->getNivelActividadReal())
                        ->setCombustibleDebioConsumir($operacion->getConsumoNormado())
                        ->setCombustibleRestante($operacion->getCombustibleFinal())
                        ->setCombustibleReal($operacion->getConsumoReal())
                        ->setCombustibleAbastecido($operacion->getCombustibleAbastecido())
                        ->setIndiceReal($indiceConsumoReal)
                        ->setDiferenciaRealPlan($operacion->getConsumoReal() - $operacion->getConsumoNormado())
                        ->setPorcientoDesviacion($indiceConsumoReal / $indiceConsumoNormado * 100 - 100);

                    $em->persist($desglose);
                }
            });

            $em->clear();
        } catch (Exception $e) {
            $em->clear();
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));

        }

        return new JsonResponse(['success' => true, 'message' => 'Análisis realizado con éxito']);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function saveAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $records = json_decode($request->get('data'), true);

        foreach ($records as $record) {
            /** @var Anexo3TecnologicosDesglose $record */
            $recordDb = $em->find('PortadoresBundle:Anexo3TecnologicosDesglose', $record['id']);
            $recordDb->setCombustibleRestante($record['combustible_restante']);

            $em->persist($recordDb);
        }

        try {
            $em->flush();
        } catch (Exception $e) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }

        return new JsonResponse(['success' => true, 'message' => 'Datos actualizados con éxito']);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updExtraDataAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $areaId = $request->get('area_id');
        $operarioId = $request->get('operario_id');
        $responsableId = $request->get('responsable_id');

        $anexo3 = $em->find('PortadoresBundle:Anexo3Tecnologicos', $request->get('anexo_id'));
        $anexo3->setArea($em->find('PortadoresBundle:Area', $areaId))
            ->setOperario($em->find('PortadoresBundle:Persona', $operarioId))
            ->setResponsable($em->find('PortadoresBundle:Persona', $responsableId));

        $em->persist($anexo3);

        try {
            $em->flush();
        } catch (Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }

        return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Datos extras del análisis actualizados con éxito.'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function exportAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $records = json_decode($request->get('records'));
        $extraData = json_decode($request->get('extra_data'));

        $anexo3 = $em->find('PortadoresBundle:Anexo3Tecnologicos', $extraData->anexo_id);


        $html = '
<html xmlns:o="urn:schemas-microsoft-com:office:office"
    xmlns:x="urn:schemas-microsoft-com:office:excel"
    xmlns="http://www.w3.org/TR/REC-html40">
    <head>
        <meta http-equiv=Content-Type content="text/html; charset=windows-1252">
        <meta name=ProgId content=Excel.Sheet>
        <meta name=Generator content="Microsoft Excel 15">
        <link rel=File-List href="Libro1_archivos/filelist.xml">
        ' . $this->getStyles() ."        
    </head>

    <body>
        <div id=\"Libro1_9688\" align=center x:publishsource=\"Excel\">
            <table border=0 cellpadding=0 cellspacing=0 width=1485 style='border-collapse:collapse;table-layout:fixed;width:1115pt'>
                <col width=80 style='width:60pt'>
                <col width=173 style='mso-width-source:userset;mso-width-alt:6326;width:130pt'>
                <col width=80 span=7 style='width:60pt'>
                <col width=93 style='mso-width-source:userset;mso-width-alt:3401;width:70pt'>
                <col width=89 style='mso-width-source:userset;mso-width-alt:3254;width:67pt'>
                <col width=90 style='mso-width-source:userset;mso-width-alt:3291;width:68pt'>
                <col width=80 span=5 style='width:60pt'>
                <tr height=25 style='height:18.75pt'>
                    <td colspan=15 height=25 class=xl889688 width=1485 style='height:18.75pt;width:1115pt'>Anexo 3 Cuantificaci&oacute;n Equipo a Equipo (Tecnol&oacute;gicos)</td>
                </tr>
                <tr height=25 style='height:18.75pt'>
                    <td colspan=15 height=25 class=xl899688 style='height:18.75pt'>Modelo Reporte de Operaci&oacute;n y Consumo de Combustible de los Equipos Tecnol&oacute;gicos.</td>
                    </tr>
                <tr height=25 style='height:18.75pt'>
                    <td height=25 class=xl909688 style='height:18.75pt'></td>
                    <td class=xl909688></td>
                    <td class=xl909688></td>
                    <td class=xl909688></td>
                    <td class=xl909688>Mes:</td>
                    <td colspan=2 class=xl919688>". FechaUtil::getNombreMes($anexo3->getMes()) . '</td>
                    <td class=xl909688>Anno:</td>
                    <td class=xl919688>' . $anexo3->getAnno() ."</td>
                    <td class=xl909688></td>
                    <td class=xl909688></td>
                    <td class=xl909688></td>
                    <td class=xl909688></td>
                    <td class=xl909688></td>
                    <td class=xl909688></td>
                    <td class=xl909688></td>
                </tr>
                <tr height=25 style='height:18.75pt'>
                    <td colspan=2 height=25 class=xl919688 style='height:18.75pt'>Nombre del Operario:</td>
                    <td colspan=3 class=xl919688>". $anexo3->getOperario()->getNombre() . '</td>
                    <td class=xl909688></td>
                    <td class=xl909688></td>
                    <td class=xl909688>Area:</td>
                    <td colspan=3 class=xl919688>' . $anexo3->getArea()->getNombre() ."</td>
                    <td class=xl909688></td>
                    <td class=xl909688></td>
                    <td class=xl909688></td>
                    <td class=xl909688></td>
                </tr>
                <tr height=25 style='height:18.75pt'>
                    <td colspan=2 height=25 class=xl919688 style='height:18.75pt'>Nombre del Responsable:</td>
                    <td colspan=3 class=xl919688>". $anexo3->getResponsable()->getNombre() . '</td>
                    <td class=xl909688></td>
                    <td class=xl909688></td>
                    <td class=xl909688></td>
                    <td colspan=2 class=xl929688>Combustible:</td>
                    <td class=xl909688></td>
                    <td class=xl909688>' . $anexo3->getTipoCombustible()->getNombre() ."</td>
                    <td class=xl909688></td>
                    <td class=xl909688></td>
                    <td class=xl909688></td>
                </tr>
                <tr height=25 style='height:18.75pt'>
                    <td colspan=2 height=25 class=xl919688 style='height:18.75pt'>Tipo de Equipo:</td>
                    <td colspan=2 class=xl919688>". $anexo3->getEquipo()->getDenominacionTecnologica()->getNombre() . '</td>
                    <td class=xl909688></td>
                    <td class=xl909688></td>
                    <td colspan=2 class=xl899688>Marca del Equipo:</td>
                    <td class=xl909688>' . $anexo3->getEquipo()->getModeloTecnologico()->getMarcaTecnologica()->getNombre() . '</td>
                    <td class=xl909688></td>
                    <td class=xl909688></td>
                    <td colspan=2 class=xl919688>Modelo del Equipo:</td>
                    <td colspan=2 class=xl919688>' . $anexo3->getEquipo()->getModeloTecnologico()->getNombre() ."</td>
                </tr>
                <tr height=25 style='height:18.75pt'>
                    <td height=25 class=xl939688 style='height:18.75pt'></td>
                    <td class=xl939688></td>
                    <td class=xl939688></td>
                    <td class=xl939688></td>
                    <td class=xl939688></td>
                    <td class=xl939688></td>
                    <td class=xl939688></td>
                    <td class=xl939688></td>
                    <td class=xl939688></td>
                    <td class=xl939688></td>
                    <td class=xl939688></td>
                    <td class=xl939688></td>
                    <td class=xl939688></td>
                    <td class=xl939688></td>
                    <td class=xl939688></td>
                    <td class=xl939688></td>
                </tr>
                <tr height=41 style='mso-height-source:userset;height:30pt'>
                    <td rowspan=3 height=145 class=xl639688 style='border-bottom:.5pt solid black;height:50pt'>Dia</td>
                    <td rowspan=3 class=xl639688 style='border-bottom:.5pt solid black'>Actividad</td>
                    <td rowspan=3 class=xl649688 width=85 style='border-bottom:.5pt solid black;width:60pt'>Indice de Consumo Normado L/h</td>
                    <td rowspan=3 class=xl659688 width=85 style='border-bottom:.5pt solid black;width:60pt'>Hora Inicio hh:mm</td>
                    <td rowspan=3 class=xl659688 width=85 style='border-bottom:.5pt solid black;width:60pt'>Hora Final hh:mm</td>
                    <td rowspan=3 class=xl649688 width=85 style='border-bottom:.5pt solid black;width:60pt'>Tiempo empleado hh:mm</td>
                    <td rowspan=3 class=xl649688 width=85 style='border-bottom:.5pt solid black;width:60pt'>Nivel de actividad Real ejecutado Horas</td>
                    <td rowspan=3 class=xl969688 width=95 style='border-bottom:.5pt solid black;width:70pt'>Comb. que debi&oacute; Consumir (L)</td>
                    <td rowspan=3 class=xl649688 width=95 style='border-bottom:.5pt solid black;width:67pt'>Comb. en tanque al concluir las operaciones (L)</td>
                    <td rowspan=3 class=xl979688 width=95 style='border-bottom:.5pt solid black;width:68pt'>Comb. real consumido (L)</td>
                    <td rowspan=3 class=xl649688 width=85 style='border-bottom:.5pt solid black;width:60pt'>Cantidad de comb. Abastecido</td>
                    <td rowspan=3 class=xl649688 width=85 style='border-bottom:.5pt solid black;width:60pt'>Indice de consumo real (L/h)</td>
                    <td rowspan=3 class=xl649688 width=85 style='border-bottom:.5pt solid black;width:60pt'>Diferencia consumo Real - Plan (L)</td>
                    <td rowspan=3 class=xl649688 width=85 style='border-bottom:.5pt solid black;width:60pt'>% de desviacion</td>
                    <td rowspan=3 class=xl649688 width=85 style='border-bottom:.5pt solid black;width:60pt'>Firma del Operario</td>
                </tr>
                <tr height=38 style='mso-height-source:userset;height:10pt'></tr>
                <tr height=38 style='mso-height-source:userset;height:10pt'></tr>";

        $dias = FechaUtil::getCantidadDiasMes($anexo3->getMes(), $anexo3->getAnno());

        for ($i = 1; $i <= $dias; $i++){

            $recordsTemp = array_filter($records, static function($record) use ($i) {
                return (int)$record->dia === $i;
            });

            if ($recordsTemp) {
                foreach ($recordsTemp as $record) {
                    $html .= "
                        <tr height=21 style='height:15.75pt'>
                            <td height=21 class=xl729688 style='height:15.75pt;border-top:none'>". $record->dia ."</td>
                            <td class=xl739688 width=173 style='border-top:none;border-left:none;width:130pt'>". $record->actividad ."</td>
                            <td class=xl749688 style='border-top:none;border-left:none'>". $record->indice_normado ."</td>
                            <td class=xl759688 style='border-top:none;border-left:none'>". $record->hora_inicio ."</td>
                            <td class=xl759688 style='border-top:none;border-left:none'>". $record->hora_parada ."</td>
                            <td class=xl759688 style='border-top:none;border-left:none'>". $record->tiempo_empleado ."</td>
                            <td class=xl759688 style='border-top:none;border-left:none'>". $record->nivel_act_real ."</td>
                            <td class=xl759688 style='border-top:none;border-left:none'>". $record->combustible_debio_consumir ."</td>
                            <td class=xl769688 style='border-top:none;border-left:none'>". $record->combustible_restante ."</td>
                            <td class=xl749688 style='border-top:none;border-left:none'>". $record->combustible_real ."</td>
                            <td class=xl779688 style='border-left:none'>". $record->combustible_abastecido ."</td>
                            <td class=xl749688 style='border-top:none;border-left:none'>". $record->indice_real ."</td>
                            <td class=xl749688 style='border-top:none;border-left:none'>". $record->diferencia_real_plan ."</td>
                            <td class=xl749688 style='border-top:none;border-left:none'>". $record->porciento_desviacion ."</td>
                            <td class=xl749688 style='border-top:none;border-left:none'></td>
                        </tr>";
                }
            }
            else {
                $html .= "
                <tr height=21 style='height:15.75pt'>
                    <td height=21 class=xl729688 style='height:15.75pt;border-top:none'>". $i ."</td>
                    <td class=xl739688 width=173 style='border-top:none;border-left:none;width:130pt'></td>
                    <td class=xl749688 style='border-top:none;border-left:none'></td>
                    <td class=xl759688 style='border-top:none;border-left:none'></td>
                    <td class=xl759688 style='border-top:none;border-left:none'></td>
                    <td class=xl759688 style='border-top:none;border-left:none'></td>
                    <td class=xl759688 style='border-top:none;border-left:none'></td>
                    <td class=xl759688 style='border-top:none;border-left:none'></td>
                    <td class=xl769688 style='border-top:none;border-left:none'></td>
                    <td class=xl749688 style='border-top:none;border-left:none'></td>
                    <td class=xl779688 style='border-left:none'></td>
                    <td class=xl749688 style='border-top:none;border-left:none'></td>
                    <td class=xl749688 style='border-top:none;border-left:none'></td>
                    <td class=xl749688 style='border-top:none;border-left:none'></td>
                    <td class=xl749688 style='border-top:none;border-left:none'></td>
                </tr>";
            }

        }

        $html .= "
                <tr height=21 style='height:15.75pt'>
                    <td colspan=5 height=21 class=xl799688 style='height:15.75pt'>Subtotales por actividades</td>
                    <td class=xl809688 style='border-top:none;border-left:none'>&nbsp;</td>
                    <td class=xl819688 style='border-top:none;border-left:none'>0.0</td>
                    <td class=xl819688 style='border-top:none;border-left:none'>0.0</td>
                    <td class=xl819688 style='border-top:none;border-left:none'>&nbsp;</td>
                    <td class=xl819688 style='border-top:none;border-left:none'>0.0</td>
                    <td class=xl819688 style='border-top:none;border-left:none'>0.0</td>
                    <td class=xl829688 style='border-top:none;border-left:none'>#�DIV/0!</td>
                    <td class=xl829688 style='border-top:none;border-left:none'>0.0</td>
                    <td class=xl829688 style='border-top:none;border-left:none'>#�DIV/0!</td>
                    <td class=xl839688 style='border-top:none;border-left:none'>&nbsp;</td>
                </tr>
                <tr height=21 style='height:15.75pt'>
                    <td colspan=5 height=21 class=xl799688 style='height:15.75pt'>Total Mes</td>
                    <td class=xl849688 style='border-top:none;border-left:none'>&nbsp;</td>
                    <td class=xl859688 style='border-top:none;border-left:none'>&nbsp;</td>
                    <td class=xl869688 style='border-top:none;border-left:none'>&nbsp;</td>
                    <td class=xl879688 style='border-top:none;border-left:none'>0.0</td>
                    <td class=xl859688 style='border-top:none;border-left:none'>&nbsp;</td>
                    <td class=xl859688 style='border-top:none;border-left:none'>&nbsp;</td>
                    <td class=xl849688 style='border-top:none;border-left:none'>&nbsp;</td>
                    <td class=xl849688 style='border-top:none;border-left:none'>&nbsp;</td>
                    <td class=xl849688 style='border-top:none;border-left:none'>&nbsp;</td>
                    <td class=xl839688 style='border-top:none;border-left:none'>&nbsp;</td>
                </tr>
                <tr height=25 style='height:18.75pt'>
                    <td colspan=15 height=25 class=xl949688 width=1485 style='height:18.75pt;width:1115pt'>
                        <font class=\"font59688\">Observaciones: </font>
                        <font class=\"font59688\">Mencionar que el indice de consumo global durante el mes no se deteriora.</font>
                    </td>
                </tr>
                <tr height=25 style='height:18.75pt'>
                    <td height=25 class=xl959688 width=80 style='height:18.75pt;width:60pt'></td>
                    <td class=xl959688 width=173 style='width:130pt'></td>
                    <td class=xl959688 width=80 style='width:60pt'></td>
                    <td class=xl959688 width=80 style='width:60pt'></td>
                    <td class=xl959688 width=80 style='width:60pt'></td>
                    <td class=xl959688 width=80 style='width:60pt'></td>
                    <td class=xl959688 width=80 style='width:60pt'></td>
                    <td class=xl959688 width=80 style='width:60pt'></td>
                    <td class=xl959688 width=80 style='width:60pt'></td>
                    <td class=xl959688 width=93 style='width:70pt'></td>
                    <td class=xl959688 width=89 style='width:67pt'></td>
                    <td class=xl959688 width=90 style='width:68pt'></td>
                    <td class=xl959688 width=80 style='width:60pt'></td>
                    <td class=xl959688 width=80 style='width:60pt'></td>
                    <td class=xl959688 width=80 style='width:60pt'></td>
                </tr>
                <tr height=25 style='height:18.75pt'>
                    <td height=25 class=xl909688 style='height:18.75pt'>Revisado por:</td>
                    <td colspan=3 class=xl919688>Ing. Osvaldo Aguiar Felix</td>
                    <td class=xl909688></td>
                    <td class=xl909688></td>
                    <td class=xl909688></td>
                    <td class=xl909688></td>
                    <td class=xl909688></td>
                    <td class=xl909688></td>
                    <td class=xl909688></td>
                    <td class=xl909688></td>
                    <td class=xl909688></td>
                    <td class=xl909688></td>
                    <td class=xl909688></td>
                </tr>
                <tr height=25 style='height:18.75pt'>
                    <td height=25 class=xl909688 style='height:18.75pt'>Cargo:</td>
                    <td colspan=3 class=xl919688>Especialista Energetico</td>
                    <td class=xl909688></td>
                    <td class=xl909688></td>
                    <td class=xl909688>Firma:</td>
                    <td class=xl909688></td>
                    <td class=xl909688></td>
                    <td class=xl909688></td>
                    <td class=xl909688></td>
                    <td class=xl909688></td>
                    <td class=xl909688></td>
                    <td class=xl909688></td>
                    <td class=xl909688></td>
                </tr>
            </table>
        </div>
    </body>
</html>";

//        var_dump($anexo3->getMes());
//        var_dump($records[0]);

//        die;

        return new JsonResponse(['success' => true, 'html' => $html]);
    }

    private function getStyles() {
        // Agregar a la clase .xl729688 para obtener formato fecha
        // mso-number-format:"dd\\-mm\\-yy\\;\\@";

        return '        
        <style id="Libro1_9688_Styles">
            <!--table
                {mso-displayed-decimal-separator:"\\.";
                mso-displayed-thousand-separator:"\\,";}
            -->
            .font59688
                {color:black;
                font-size:14.0pt;
                font-weight:700;
                font-style:normal;
                text-decoration:none;
                font-family:Calibri, sans-serif;
                mso-font-charset:0;}
            .font69688
                {color:black;
                font-size:14.0pt;
                font-weight:400;
                font-style:normal;
                text-decoration:none;
                font-family:Calibri, sans-serif;
                mso-font-charset:0;}
            .xl159688
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
            .xl639688
                {padding-top:1px;
                padding-right:1px;
                padding-left:1px;
                mso-ignore:padding;
                color:black;
                font-size:12.0pt;
                font-weight:700;
                font-style:normal;
                text-decoration:none;
                font-family:Calibri, sans-serif;
                mso-font-charset:0;
                mso-number-format:General;
                text-align:center;
                vertical-align:middle;
                border-top:.5pt solid windowtext;
                border-right:.5pt solid windowtext;
                border-bottom:none;
                border-left:.5pt solid windowtext;
                mso-background-source:auto;
                mso-pattern:auto;
                white-space:nowrap;}
            .xl649688
                {padding-top:1px;
                padding-right:1px;
                padding-left:1px;
                mso-ignore:padding;
                color:black;
                font-size:12.0pt;
                font-weight:700;
                font-style:normal;
                text-decoration:none;
                font-family:Calibri, sans-serif;
                mso-font-charset:0;
                mso-number-format:General;
                text-align:center;
                vertical-align:middle;
                border-top:.5pt solid windowtext;
                border-right:.5pt solid windowtext;
                border-bottom:none;
                border-left:.5pt solid windowtext;
                mso-background-source:auto;
                mso-pattern:auto;
                white-space:normal;}
            .xl659688
                {padding-top:1px;
                padding-right:1px;
                padding-left:1px;
                mso-ignore:padding;
                color:windowtext;
                font-size:12.0pt;
                font-weight:700;
                font-style:normal;
                text-decoration:none;
                font-family:Calibri, sans-serif;
                mso-font-charset:0;
                mso-number-format:General;
                text-align:center;
                vertical-align:middle;
                border:.5pt solid windowtext;
                mso-background-source:auto;
                mso-pattern:auto;
                white-space:normal;}
            .xl669688
                {padding-top:1px;
                padding-right:1px;
                padding-left:1px;
                mso-ignore:padding;
                color:black;
                font-size:12.0pt;
                font-weight:700;
                font-style:normal;
                text-decoration:none;
                font-family:Calibri, sans-serif;
                mso-font-charset:0;
                mso-number-format:General;
                text-align:center;
                vertical-align:middle;
                border-top:none;
                border-right:.5pt solid windowtext;
                border-bottom:none;
                border-left:.5pt solid windowtext;
                mso-background-source:auto;
                mso-pattern:auto;
                white-space:nowrap;}
            .xl679688
                {padding-top:1px;
                padding-right:1px;
                padding-left:1px;
                mso-ignore:padding;
                color:black;
                font-size:12.0pt;
                font-weight:700;
                font-style:normal;
                text-decoration:none;
                font-family:Calibri, sans-serif;
                mso-font-charset:0;
                mso-number-format:General;
                text-align:center;
                vertical-align:middle;
                border-top:none;
                border-right:.5pt solid windowtext;
                border-bottom:none;
                border-left:.5pt solid windowtext;
                mso-background-source:auto;
                mso-pattern:auto;
                white-space:normal;}
            .xl689688
                {padding-top:1px;
                padding-right:1px;
                padding-left:1px;
                mso-ignore:padding;
                color:black;
                font-size:12.0pt;
                font-weight:700;
                font-style:normal;
                text-decoration:none;
                font-family:Calibri, sans-serif;
                mso-font-charset:0;
                mso-number-format:General;
                text-align:center;
                vertical-align:middle;
                border-top:none;
                border-right:.5pt solid windowtext;
                border-bottom:.5pt solid windowtext;
                border-left:.5pt solid windowtext;
                mso-background-source:auto;
                mso-pattern:auto;
                white-space:nowrap;}
            .xl699688
                {padding-top:1px;
                padding-right:1px;
                padding-left:1px;
                mso-ignore:padding;
                color:black;
                font-size:12.0pt;
                font-weight:700;
                font-style:normal;
                text-decoration:none;
                font-family:Calibri, sans-serif;
                mso-font-charset:0;
                mso-number-format:General;
                text-align:center;
                vertical-align:middle;
                border-top:none;
                border-right:.5pt solid windowtext;
                border-bottom:.5pt solid windowtext;
                border-left:.5pt solid windowtext;
                mso-background-source:auto;
                mso-pattern:auto;
                white-space:normal;}
            .xl709688
                {padding-top:1px;
                padding-right:1px;
                padding-left:1px;
                mso-ignore:padding;
                color:black;
                font-size:12.0pt;
                font-weight:700;
                font-style:normal;
                text-decoration:none;
                font-family:Calibri, sans-serif;
                mso-font-charset:0;
                mso-number-format:General;
                text-align:center;
                vertical-align:middle;
                border-top:none;
                border-right:none;
                border-bottom:.5pt solid windowtext;
                border-left:.5pt solid windowtext;
                mso-background-source:auto;
                mso-pattern:auto;
                white-space:normal;}
            .xl719688
                {padding-top:1px;
                padding-right:1px;
                padding-left:1px;
                mso-ignore:padding;
                color:black;
                font-size:12.0pt;
                font-weight:700;
                font-style:normal;
                text-decoration:none;
                font-family:Calibri, sans-serif;
                mso-font-charset:0;
                mso-number-format:General;
                text-align:center;
                vertical-align:middle;
                border-top:none;
                border-right:.5pt solid windowtext;
                border-bottom:.5pt solid windowtext;
                border-left:none;
                mso-background-source:auto;
                mso-pattern:auto;
                white-space:normal;}
            .xl729688
                {padding-top:1px;
                padding-right:1px;
                padding-left:1px;
                mso-ignore:padding;
                color:black;
                font-size:12.0pt;
                font-weight:400;
                font-style:normal;
                text-decoration:none;
                font-family:Calibri, sans-serif;
                mso-font-charset:0;
                text-align:center;
                vertical-align:middle;
                border:.5pt solid windowtext;
                mso-background-source:auto;
                mso-pattern:auto;
                white-space:nowrap;}
            .xl739688
                {padding-top:1px;
                padding-right:1px;
                padding-left:1px;
                mso-ignore:padding;
                color:black;
                font-size:12.0pt;
                font-weight:400;
                font-style:normal;
                text-decoration:none;
                font-family:Calibri, sans-serif;
                mso-font-charset:0;
                mso-number-format:"\\@";
                text-align:center;
                vertical-align:middle;
                border:.5pt solid windowtext;
                mso-background-source:auto;
                mso-pattern:auto;
                white-space:normal;}
            .xl749688
                {padding-top:1px;
                padding-right:1px;
                padding-left:1px;
                mso-ignore:padding;
                color:black;
                font-size:12.0pt;
                font-weight:400;
                font-style:normal;
                text-decoration:none;
                font-family:Calibri, sans-serif;
                mso-font-charset:0;
                mso-number-format:"0\\.0";
                text-align:center;
                vertical-align:middle;
                border:.5pt solid windowtext;
                mso-background-source:auto;
                mso-pattern:auto;
                white-space:nowrap;}
            .xl759688
                {padding-top:1px;
                padding-right:1px;
                padding-left:1px;
                mso-ignore:padding;
                color:black;
                font-size:12.0pt;
                font-weight:400;
                font-style:normal;
                text-decoration:none;
                font-family:Calibri, sans-serif;
                mso-font-charset:0;
                mso-number-format:"h\\:mm\\;\\@";
                text-align:center;
                vertical-align:middle;
                border:.5pt solid windowtext;
                mso-background-source:auto;
                mso-pattern:auto;
                white-space:nowrap;}
            .xl769688
                {padding-top:1px;
                padding-right:1px;
                padding-left:1px;
                mso-ignore:padding;
                color:black;
                font-size:12.0pt;
                font-weight:400;
                font-style:normal;
                text-decoration:none;
                font-family:Calibri, sans-serif;
                mso-font-charset:0;
                mso-number-format:Fixed;
                text-align:center;
                vertical-align:middle;
                border:.5pt solid windowtext;
                mso-background-source:auto;
                mso-pattern:auto;
                white-space:nowrap;}
            .xl779688
                {padding-top:1px;
                padding-right:1px;
                padding-left:1px;
                mso-ignore:padding;
                color:black;
                font-size:12.0pt;
                font-weight:400;
                font-style:normal;
                text-decoration:none;
                font-family:Calibri, sans-serif;
                mso-font-charset:0;
                mso-number-format:"0\\.0";
                text-align:center;
                vertical-align:middle;
                border-top:none;
                border-right:.5pt solid windowtext;
                border-bottom:.5pt solid windowtext;
                border-left:.5pt solid windowtext;
                mso-background-source:auto;
                mso-pattern:auto;
                white-space:nowrap;}
            .xl789688
                {padding-top:1px;
                padding-right:1px;
                padding-left:1px;
                mso-ignore:padding;
                color:black;
                font-size:12.0pt;
                font-weight:400;
                font-style:normal;
                text-decoration:none;
                font-family:Calibri, sans-serif;
                mso-font-charset:0;
                mso-number-format:General;
                text-align:center;
                vertical-align:middle;
                border:.5pt solid windowtext;
                mso-background-source:auto;
                mso-pattern:auto;
                white-space:nowrap;}
            .xl799688
                {padding-top:1px;
                padding-right:1px;
                padding-left:1px;
                mso-ignore:padding;
                color:black;
                font-size:12.0pt;
                font-weight:700;
                font-style:normal;
                text-decoration:none;
                font-family:Calibri, sans-serif;
                mso-font-charset:0;
                mso-number-format:General;
                text-align:center;
                vertical-align:middle;
                border:.5pt solid windowtext;
                mso-background-source:auto;
                mso-pattern:auto;
                white-space:nowrap;}
            .xl809688
                {padding-top:1px;
                padding-right:1px;
                padding-left:1px;
                mso-ignore:padding;
                color:black;
                font-size:12.0pt;
                font-weight:400;
                font-style:normal;
                text-decoration:none;
                font-family:Calibri, sans-serif;
                mso-font-charset:0;
                mso-number-format:"h\\:mm\\;\\@";
                text-align:general;
                vertical-align:middle;
                border:.5pt solid windowtext;
                mso-background-source:auto;
                mso-pattern:auto;
                white-space:nowrap;}
            .xl819688
                {padding-top:1px;
                padding-right:1px;
                padding-left:1px;
                mso-ignore:padding;
                color:black;
                font-size:12.0pt;
                font-weight:700;
                font-style:normal;
                text-decoration:none;
                font-family:Calibri, sans-serif;
                mso-font-charset:0;
                mso-number-format:"0\\.0";
                text-align:center;
                vertical-align:middle;
                border:.5pt solid windowtext;
                mso-background-source:auto;
                mso-pattern:auto;
                white-space:nowrap;}
            .xl829688
                {padding-top:1px;
                padding-right:1px;
                padding-left:1px;
                mso-ignore:padding;
                color:black;
                font-size:12.0pt;
                font-weight:700;
                font-style:normal;
                text-decoration:none;
                font-family:Calibri, sans-serif;
                mso-font-charset:0;
                mso-number-format:"0\\.0";
                text-align:center;
                vertical-align:middle;
                border:.5pt solid windowtext;
                mso-background-source:auto;
                mso-pattern:auto;
                white-space:nowrap;}
            .xl839688
                {padding-top:1px;
                padding-right:1px;
                padding-left:1px;
                mso-ignore:padding;
                color:black;
                font-size:12.0pt;
                font-weight:400;
                font-style:normal;
                text-decoration:none;
                font-family:Calibri, sans-serif;
                mso-font-charset:0;
                mso-number-format:General;
                text-align:general;
                vertical-align:middle;
                border:.5pt solid windowtext;
                mso-background-source:auto;
                mso-pattern:auto;
                white-space:nowrap;}
            .xl849688
                {padding-top:1px;
                padding-right:1px;
                padding-left:1px;
                mso-ignore:padding;
                color:black;
                font-size:12.0pt;
                font-weight:400;
                font-style:normal;
                text-decoration:none;
                font-family:Calibri, sans-serif;
                mso-font-charset:0;
                mso-number-format:General;
                text-align:general;
                vertical-align:middle;
                border:.5pt solid windowtext;
                background:white;
                mso-pattern:black none;
                white-space:nowrap;}
            .xl859688
                {padding-top:1px;
                padding-right:1px;
                padding-left:1px;
                mso-ignore:padding;
                color:black;
                font-size:12.0pt;
                font-weight:700;
                font-style:normal;
                text-decoration:none;
                font-family:Calibri, sans-serif;
                mso-font-charset:0;
                mso-number-format:General;
                text-align:center;
                vertical-align:middle;
                border:.5pt solid windowtext;
                background:white;
                mso-pattern:black none;
                white-space:nowrap;}
            .xl869688
                {padding-top:1px;
                padding-right:1px;
                padding-left:1px;
                mso-ignore:padding;
                color:black;
                font-size:12.0pt;
                font-weight:700;
                font-style:normal;
                text-decoration:none;
                font-family:Calibri, sans-serif;
                mso-font-charset:0;
                mso-number-format:0;
                text-align:center;
                vertical-align:middle;
                border:.5pt solid windowtext;
                background:white;
                mso-pattern:black none;
                white-space:nowrap;}
            .xl879688
                {padding-top:1px;
                padding-right:1px;
                padding-left:1px;
                mso-ignore:padding;
                color:black;
                font-size:12.0pt;
                font-weight:700;
                font-style:normal;
                text-decoration:none;
                font-family:Calibri, sans-serif;
                mso-font-charset:0;
                mso-number-format:"0\\.0";
                text-align:center;
                vertical-align:middle;
                border:.5pt solid windowtext;
                background:white;
                mso-pattern:black none;
                white-space:nowrap;}
            .xl889688
                {padding-top:1px;
                padding-right:1px;
                padding-left:1px;
                mso-ignore:padding;
                color:black;
                font-size:14.0pt;
                font-weight:700;
                font-style:normal;
                text-decoration:none;
                font-family:Calibri, sans-serif;
                mso-font-charset:0;
                mso-number-format:General;
                text-align:center;
                vertical-align:middle;
                mso-background-source:auto;
                mso-pattern:auto;
                white-space:nowrap;}
            .xl899688
                {padding-top:1px;
                padding-right:1px;
                padding-left:1px;
                mso-ignore:padding;
                color:black;
                font-size:14.0pt;
                font-weight:400;
                font-style:normal;
                text-decoration:none;
                font-family:Calibri, sans-serif;
                mso-font-charset:0;
                mso-number-format:General;
                text-align:center;
                vertical-align:middle;
                mso-background-source:auto;
                mso-pattern:auto;
                white-space:nowrap;}
            .xl909688
                {padding-top:1px;
                padding-right:1px;
                padding-left:1px;
                mso-ignore:padding;
                color:black;
                font-size:14.0pt;
                font-weight:400;
                font-style:normal;
                text-decoration:none;
                font-family:Calibri, sans-serif;
                mso-font-charset:0;
                mso-number-format:General;
                text-align:general;
                vertical-align:middle;
                mso-background-source:auto;
                mso-pattern:auto;
                white-space:nowrap;}
            .xl919688
                {padding-top:1px;
                padding-right:1px;
                padding-left:1px;
                mso-ignore:padding;
                color:black;
                font-size:14.0pt;
                font-weight:400;
                font-style:normal;
                text-decoration:none;
                font-family:Calibri, sans-serif;
                mso-font-charset:0;
                mso-number-format:General;
                text-align:left;
                vertical-align:middle;
                mso-background-source:auto;
                mso-pattern:auto;
                white-space:nowrap;}
            .xl929688
                {padding-top:1px;
                padding-right:1px;
                padding-left:1px;
                mso-ignore:padding;
                color:black;
                font-size:14.0pt;
                font-weight:400;
                font-style:normal;
                text-decoration:none;
                font-family:Calibri, sans-serif;
                mso-font-charset:0;
                mso-number-format:General;
                text-align:right;
                vertical-align:middle;
                mso-background-source:auto;
                mso-pattern:auto;
                white-space:nowrap;}
            .xl939688
                {padding-top:1px;
                padding-right:1px;
                padding-left:1px;
                mso-ignore:padding;
                color:black;
                font-size:14.0pt;
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
            .xl949688
                {padding-top:1px;
                padding-right:1px;
                padding-left:1px;
                mso-ignore:padding;
                color:black;
                font-size:14.0pt;
                font-weight:400;
                font-style:normal;
                text-decoration:none;
                font-family:Calibri, sans-serif;
                mso-font-charset:0;
                mso-number-format:General;
                text-align:left;
                vertical-align:top;
                mso-background-source:auto;
                mso-pattern:auto;
                white-space:normal;}
            .xl959688
                {padding-top:1px;
                padding-right:1px;
                padding-left:1px;
                mso-ignore:padding;
                color:black;
                font-size:14.0pt;
                font-weight:400;
                font-style:normal;
                text-decoration:none;
                font-family:Calibri, sans-serif;
                mso-font-charset:0;
                mso-number-format:General;
                text-align:general;
                vertical-align:middle;
                mso-background-source:auto;
                mso-pattern:auto;
                white-space:normal;}
            .xl969688
                {padding-top:1px;
                padding-right:1px;
                padding-left:1px;
                mso-ignore:padding;
                color:black;
                font-size:12.0pt;
                font-weight:700;
                font-style:normal;
                text-decoration:none;
                font-family:Calibri, sans-serif;
                mso-font-charset:0;
                mso-number-format:General;
                text-align:center;
                vertical-align:middle;
                border-top:.5pt solid windowtext;
                border-right:none;
                border-bottom:none;
                border-left:.5pt solid windowtext;
                mso-background-source:auto;
                mso-pattern:auto;
                white-space:normal;}
            .xl979688
                {padding-top:1px;
                padding-right:1px;
                padding-left:1px;
                mso-ignore:padding;
                color:black;
                font-size:12.0pt;
                font-weight:700;
                font-style:normal;
                text-decoration:none;
                font-family:Calibri, sans-serif;
                mso-font-charset:0;
                mso-number-format:General;
                text-align:center;
                vertical-align:middle;
                border-top:.5pt solid windowtext;
                border-right:.5pt solid windowtext;
                border-bottom:none;
                border-left:none;
                mso-background-source:auto;
                mso-pattern:auto;
                white-space:normal;}
            .xl989688
                {padding-top:1px;
                padding-right:1px;
                padding-left:1px;
                mso-ignore:padding;
                color:black;
                font-size:12.0pt;
                font-weight:700;
                font-style:normal;
                text-decoration:none;
                font-family:Calibri, sans-serif;
                mso-font-charset:0;
                mso-number-format:General;
                text-align:center;
                vertical-align:middle;
                border-top:none;
                border-right:none;
                border-bottom:none;
                border-left:.5pt solid windowtext;
                mso-background-source:auto;
                mso-pattern:auto;
                white-space:normal;}
            .xl999688
                {padding-top:1px;
                padding-right:1px;
                padding-left:1px;
                mso-ignore:padding;
                color:black;
                font-size:12.0pt;
                font-weight:700;
                font-style:normal;
                text-decoration:none;
                font-family:Calibri, sans-serif;
                mso-font-charset:0;
                mso-number-format:General;
                text-align:center;
                vertical-align:middle;
                border-top:none;
                border-right:.5pt solid windowtext;
                border-bottom:none;
                border-left:none;
                mso-background-source:auto;
                mso-pattern:auto;
                white-space:normal;}
        </style>        
        ';
    }
}