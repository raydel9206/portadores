<?php

namespace Geocuba\PortadoresBundle\Controller;

use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Geocuba\AdminBundle\Entity\Usuario;
use Geocuba\PortadoresBundle\Entity\Anexo3Gee;
use Geocuba\PortadoresBundle\Entity\GrupoElectrogeno;
use Geocuba\PortadoresBundle\Entity\RegistroOperacionGrupoElectrogeno;
use Geocuba\PortadoresBundle\Entity\Unidad;
use Geocuba\PortadoresBundle\Util\DocumentosEnum;
use Geocuba\PortadoresBundle\Util\FechaUtil;
use Geocuba\PortadoresBundle\Util\Utiles;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Throwable;


class Anexo3GeeController extends Controller
{
    use ViewActionTrait;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loadAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $anno = $request->get('anno');
        $mes = $request->get('mes');
        $quincena = $request->get('quincena');

        $unidadId = $this->getUser() instanceof Usuario ? $this->getUser()->getUnidad()->getId() : 'apr_portadores_0';
        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($unidadId), $_unidades);

        $anexos3 = $em->getRepository('PortadoresBundle:Anexo3Gee')->findBy(['unidad' => $unidadId, 'quincena' => $quincena, 'mes' => $mes, 'anno' => $anno], ['unidad' => 'ASC']);

        $_rows = array_map(static function ($entity) {
            /** @var Anexo3Gee $entity */
            return [
                'equipo_id' => $entity->getId(),
                'equipo_descripcion' => $entity->getGrupo()->getDescripcion(),
                'equipo_marca' => $entity->getGrupo()->getModeloTecnologico()->getMarcaTecnologica()->getNombre(),
                'unidad_id' => $entity->getUnidad()->getId(),
                'unidad_nombre' => $entity->getUnidad()->getNombre(),
                'municipio_id' => $entity->getMunicipio()->getId(),
                'municipio_nombre' => $entity->getMunicipio()->getNombre(),
                'kva' => round($entity->getKva(), 2),
                'cant_oper_sin_carga' => round($entity->getCantOperSinCarga()),
                'cant_oper_con_carga' => round($entity->getCantOperConCarga()),
                'horas_sin_carga' => round($entity->getHorasSinCarga(), 2),
                'horas_con_carga' => round($entity->getHorasConCarga(), 2),
                'energia_generada' => round($entity->getEnergiaGenerada(), 2),
                'comb_consumido_sin_carga' => round($entity->getCombConsumidoSinCarga(), 2),
                'comb_consumido_con_carga' => round($entity->getCombConsumidoConCarga(), 2),
                'comb_consumido_total' => round($entity->getCombConsumidoTotal(), 2),
                'indice_consumo' => round($entity->getIndiceConsumo(), 2),
                'indice_cargabilidad' => round($entity->getIndiceCargabilidad(), 2),
                'porciento_cargabilidad' => round($entity->getPorcientoCargabilidad(), 2)
            ];
        }, $anexos3);

//        usort($_rows, static function($a, $b) {
//            return $a['dia'] - $b['dia'];
//        });

        return new JsonResponse(['success' => true, 'rows' => $_rows]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws NonUniqueResultException
     * @throws MappingException
     * @throws Throwable
     */
    public function generateAction(Request $request): JsonResponse
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $mes = $request->get('mes');
        $anno = $request->get('anno');
        $quincena = $request->get('quincena');
        $unidadId = $request->get('unidad_id');
        if (!$unidadId)
            $unidadId = $this->getUser() instanceof Usuario ? $this->getUser()->getUnidad()->getId() : 'apr_portadores_0';

        $firstDay = $quincena === 'segunda' ? 15 : 1;
        $lastDay = $quincena === 'primera' ? 15 : FechaUtil::getCantidadDiasMes($mes, $anno);



        $startDate = "$anno-$mes-$firstDay 00:00:00";
        $endDate = "$anno-$mes-$lastDay 23:59:59";

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($unidadId), $_unidades);

        $equipos = $em->getRepository('PortadoresBundle:EquipoTecnologico')->findAllBy($_unidades, 'static_tec_denomination_2');
        if (!$equipos)
            return new JsonResponse(['success' => true, 'cls' => 'warning', 'message' => 'No existe ningún grupo electr&oacute;geno registrado en la undiad.']);
        try {
            $em->transactional(static function ($em) use ($unidadId, $quincena, $startDate, $endDate, $mes, $anno, $equipos) {
                /** @var EntityManager $em */
                $anexos3 = $em->getRepository('PortadoresBundle:Anexo3Gee')->findBy(['unidad' => $unidadId, 'quincena' => $quincena, 'mes' => $mes, 'anno' => $anno], ['unidad' => 'ASC']);
                if ($anexos3) foreach ($anexos3 as $anexo) $em->remove($anexo);


                array_walk($equipos, static function ($equipo) use ($em, $startDate, $endDate, $mes, $anno, $quincena) {
                    /** @var GrupoElectrogeno $equipo */
                    $cantOperSinCarga = 0;
                    $cantOperConCarga = 0;
                    $horasSinCarga = 0;
                    $horasConCarga = 0;
                    $energiaGenerada = 0;
                    $combConsumidoSinCarga = 0;
                    $combConsumidoConCarga = 0;
                    $demandaLiberada = 0;

                    $registros = $em->getRepository('PortadoresBundle:RegistroOperacionGrupoElectrogeno')->findAllBy($equipo->getId(), $startDate, $endDate);

                    array_walk($registros, static function ($registro) use (
                        $em, &$cantOperSinCarga, &$cantOperConCarga,
                        &$horasSinCarga, &$horasConCarga, &$energiaGenerada, &$combConsumidoSinCarga, &$combConsumidoConCarga, &$demandaLiberada
                    ) {
                        /** @var RegistroOperacionGrupoElectrogeno $registro */
                        if ($registro->getConCarga()) {
                            $cantOperConCarga++;
                            $horasConCarga += round($registro->getTiempoTrabajado(false) / 60, 2);
                            $energiaGenerada += (float)$registro->getEnergiaGenerada();
                            $combConsumidoConCarga += (float)$registro->getConsumoReal();
                        } else {
                            $cantOperSinCarga++;
                            $horasSinCarga += round($registro->getTiempoTrabajado(false) / 60, 2);
                            $combConsumidoSinCarga += (float)$registro->getConsumoReal();
                        }
                        $demandaLiberada += (float)$registro->getDemandaLiberada();
                    });

                    $anexo3gee = new Anexo3Gee();
                    $anexo3gee->setMunicipio($equipo->getMunicipio())
                        ->setUnidad($equipo->getUnidad())
                        ->setGrupo($equipo)
                        ->setKva($equipo->getCapacidadKva())
                        ->setCantOperSinCarga($cantOperSinCarga)
                        ->setCantOperConCarga($cantOperConCarga)
                        ->setHorasSinCarga($horasSinCarga)
                        ->setHorasConCarga($horasConCarga)
                        ->setEnergiaGenerada($energiaGenerada)
                        ->setCombConsumidoSinCarga($combConsumidoSinCarga)
                        ->setCombConsumidoConCarga($combConsumidoConCarga)
                        ->setCombConsumidoTotal($combConsumidoSinCarga + $combConsumidoConCarga)
                        ->setIndiceConsumo(($horasConCarga + $horasSinCarga) != 0 ? (($combConsumidoConCarga + $combConsumidoSinCarga) / ($horasConCarga + $horasSinCarga)) : 0)
                        ->setIndiceCargabilidad($equipo->getIndiceCargabilidad())
                        ->setPorcientoCargabilidad(($equipo->getCapacidadKva() != 0 && $horasConCarga != 0) ? (($energiaGenerada/$horasConCarga * 100) / $equipo->getCapacidadKva()) : 0)
                        ->setMes($mes)
                        ->setAnno($anno)
                        ->setQuincena($quincena);

                    $em->persist($anexo3gee);
                });
            });
            $em->clear();
        } catch (Exception $e) {
            $em->clear();
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }

        return new JsonResponse(['success' => true]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function exportAction(Request $request) {

        $em = $this->getDoctrine()->getManager();
        /** @var Unidad $unidad */
        $unidad = $this->getUser()->getUnidad();

        $quincena = $request->get('quincena');
        $mes = (int) $request->get('mes');
        $anno = $request->get('anno');
        $records = json_decode($request->get('records'));

        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

        $html = '
<html xmlns:v="urn:schemas-microsoft-com:vml"
xmlns:o="urn:schemas-microsoft-com:office:office"
xmlns:x="urn:schemas-microsoft-com:office:excel"
xmlns="http://www.w3.org/TR/REC-html40">

<head>
<meta http-equiv=Content-Type content="text/html; charset=windows-1252">
<meta name=ProgId content=Excel.Sheet>
<meta name=Generator content="Microsoft Excel 15">
<link rel=File-List href="anexo%203%20gee_archivos/filelist.xml">
<!--[if !mso]>
<style>
v\:* {behavior:url(#default#VML);}
o\:* {behavior:url(#default#VML);}
x\:* {behavior:url(#default#VML);}
.shape {behavior:url(#default#VML);}
</style>
<![endif]-->
' . $this->getStyles() . '
</head>

<body>
<!--[if !excel]>&nbsp;&nbsp;<![endif]-->
<!--La siguiente informaci&oacute;n se gener&oacute; mediante el Asistente para publicar como
p&aacute;gina web de Microsoft Excel.-->
<!--Si se vuelve a publicar el mismo elemento desde Excel, se reemplazar&aacute; toda
la informaci&oacute;n comprendida entre las etiquetas DIV.-->
<!----------------------------->
<!--INICIO DE LOS RESULTADOS DEL ASISTENTE PARA PUBLICAR COMO PÁGINA WEB DE
EXCEL -->
<!----------------------------->

<div id="Tabla combustible 2019_27280" align=center x:publishsource="Excel">

<table border=0 cellpadding=0 cellspacing=0 width=2036 class=xl3296827280
 style=\'border-collapse:collapse;table-layout:fixed;width:1530pt\'>
 <col class=xl3296827280 width=122 style=\'mso-width-source:userset;mso-width-alt:
 4461;width:92pt\'>
 <col class=xl3296827280 width=115 style=\'mso-width-source:userset;mso-width-alt:
 4205;width:86pt\'>
 <col class=xl3296827280 width=116 style=\'mso-width-source:userset;mso-width-alt:
 4242;width:87pt\'>
 <col class=xl3296827280 width=100 style=\'mso-width-source:userset;mso-width-alt:
 3657;width:75pt\'>
 <col class=xl3296827280 width=106 style=\'mso-width-source:userset;mso-width-alt:
 3876;width:80pt\'>
 <col class=xl3296827280 width=99 span=2 style=\'mso-width-source:userset;
 mso-width-alt:3620;width:74pt\'>
 <col class=xl3296827280 width=98 style=\'mso-width-source:userset;mso-width-alt:
 3584;width:74pt\'>
 <col class=xl3296827280 width=102 span=2 style=\'mso-width-source:userset;
 mso-width-alt:3730;width:77pt\'>
 <col class=xl3296827280 width=112 style=\'mso-width-source:userset;mso-width-alt:
 4096;width:84pt\'>
 <col class=xl3296827280 width=102 style=\'mso-width-source:userset;mso-width-alt:
 3730;width:77pt\'>
 <col class=xl3296827280 width=100 style=\'mso-width-source:userset;mso-width-alt:
 3657;width:75pt\'>
 <col class=xl3296827280 width=102 style=\'mso-width-source:userset;mso-width-alt:
 3730;width:77pt\'>
 <col class=xl3296827280 width=105 style=\'mso-width-source:userset;mso-width-alt:
 3840;width:79pt\'>
 <col class=xl3296827280 width=115 style=\'mso-width-source:userset;mso-width-alt:
 4205;width:86pt\'>
 <col class=xl3296827280 width=108 style=\'mso-width-source:userset;mso-width-alt:
 3949;width:81pt\'>
 <col class=xl3296827280 width=128 style=\'mso-width-source:userset;mso-width-alt:
 4681;width:96pt\'>
 <col class=xl3296827280 width=105 style=\'mso-width-source:userset;mso-width-alt:
 3840;width:79pt\'>
 <tr height=26 style=\'mso-height-source:userset;height:20.1pt\'>
  <td height=26 class=xl3296827280 width=122 style=\'height:20.1pt;width:92pt\'></td>
  <td class=xl3296827280 width=115 style=\'width:86pt\'></td>
  <td class=xl3296827280 width=116 style=\'width:87pt\'></td>
  <td class=xl3296827280 width=100 style=\'width:75pt\'></td>
  <td class=xl3296827280 width=106 style=\'width:80pt\'></td>
  <td class=xl3296827280 width=99 style=\'width:74pt\'></td>
  <td class=xl3296827280 width=99 style=\'width:74pt\'></td>
  <td class=xl3296827280 width=98 style=\'width:74pt\'></td>
  <td class=xl3296827280 width=102 style=\'width:77pt\'></td>
  <td class=xl3296827280 width=102 style=\'width:77pt\'></td>
  <td class=xl3296827280 width=112 style=\'width:84pt\'></td>
  <td class=xl3296827280 width=102 style=\'width:77pt\'></td>
  <td class=xl3296827280 width=100 style=\'width:75pt\'></td>
  <td class=xl3296827280 width=102 style=\'width:77pt\'></td>
  <td class=xl3296827280 width=105 style=\'width:79pt\'></td>
  <td class=xl3296827280 width=115 style=\'width:86pt\'></td>
  <td class=xl3296827280 width=108 style=\'width:81pt\'></td>
  <td class=xl3296827280 width=128 style=\'width:96pt\'></td>
  <td class=xl3299427280 width=105 style=\'width:79pt\'></td>
 </tr>
 <tr height=26 style=\'mso-height-source:userset;height:20.1pt\'>
  <td height=26 class=xl3296827280 style=\'height:20.1pt\'></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3299627280>Hoja:</td>
  <td class=xl3299127280>&nbsp;</td>
  <td class=xl3296827280></td>
 </tr>
 <tr height=26 style=\'mso-height-source:userset;height:20.1pt\'>
  <td colspan=18 height=26 class=xl3302127280 style=\'height:20.1pt\'>ANEXO 3</td>
  <td class=xl3297127280></td>
 </tr>
 <tr height=26 style=\'mso-height-source:userset;height:20.1pt\'>
  <td colspan=18 height=26 class=xl3302127280 style=\'height:20.1pt\'>UNI&Oacute;N
  EL&Eacute;CTRICA</td>
  <td class=xl3297127280></td>
 </tr>
 <tr height=26 style=\'mso-height-source:userset;height:20.1pt\'>
  <td colspan=18 height=26 width=1931 style=\'height:20.1pt;width:1451pt\'
  align=left valign=top><span style=\'mso-ignore:vglayout;
  position:absolute;z-index:5;margin-left:338px;margin-top:21px;width:210px;
  height:134px\'>
  </span><![endif]><span style=\'mso-ignore:vglayout2\'> 
  <table cellpadding=0 cellspacing=0>
   <tr>
    <td colspan=18 height=26 class=xl3302127280 width=1931 style=\'height:20.1pt;
    width:1451pt\'><span style=\'mso-spacerun:yes\'>      </span>DIRECCI&Oacute;N DE
    GENERACI&Oacute;N DE EMERGENCIA</td>
   </tr>
  </table>
  </span></td>
  <td class=xl3296827280></td>
 </tr>
 <tr height=26 style=\'mso-height-source:userset;height:20.1pt\'>
  <td colspan=18 height=26 class=xl3302127280 style=\'height:20.1pt\'>RESUMEN
  QUINCENAL DE OPERACIONES POR ENTIDAD PROVINCIAL</td>
  <td class=xl3296827280></td>
 </tr>
 <tr height=26 style=\'mso-height-source:userset;height:20.1pt\'>
  <td height=26 class=xl3296827280 style=\'height:20.1pt\'></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
 </tr>
 <tr height=26 style=\'mso-height-source:userset;height:20.1pt\'>
  <td height=26 class=xl3297027280 style=\'height:20.1pt\'></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296927280>Primera</td>
  <td class=xl3296927280 style=\'border-left:none\'>Segunda</td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
 </tr>
 <tr height=26 style=\'mso-height-source:userset;height:20.1pt\'>
  <td height=26 class=xl3299227280 style=\'height:20.1pt\'>Provincia:</td>
  <td class=xl3299127280>'. $this->toHtml($unidad->getMunicipio()->getProvinciaid()->getNombre()) .'</td>
  <td class=xl3299227280>Entidad:</td>
  <td colspan=3 class=xl3302227280 style=\'border-right:1.0pt solid black\'>'. $this->toHtml($unidad->getNombre()) .'</td>
  <td class=xl3299227280>Quincena:</td>
  <td class=xl3299127280 style=\'border-top:none\'>'. ($quincena === 'ambas' || $quincena === 'primera' ? 'X' : '') .'</td>
  <td class=xl3299127280 style=\'border-top:none;border-left:none\'>'. ($quincena === 'ambas' || $quincena === 'segunda' ? 'X' : '') .'</td>
  <td class=xl3296827280></td>
  <td class=xl3299227280>Mes:</td>
  <td colspan=2 class=xl3302527280 style=\'border-right:1.0pt solid black\'>'. $meses[$mes] .'</td>
  <td class=xl3299227280>A&ntilde;o:</td>
  <td class=xl3299127280>'. $anno .'</td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
 </tr>
 <tr height=26 style=\'mso-height-source:userset;height:20.1pt\'>
  <td height=26 class=xl3296827280 style=\'height:20.1pt\'></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
 </tr>
 <tr height=26 style=\'mso-height-source:userset;height:20.1pt\'>
  <td rowspan=3 height=93 class=xl3301227280 width=122 style=\'border-bottom:
  1.0pt solid black;height:70.95pt;width:92pt\'>Municipio</td>
  <td colspan=2 rowspan=3 class=xl3300827280 width=231 style=\'border-right:
  1.0pt solid black;border-bottom:1.0pt solid black;width:173pt\'>Centro</td>
  <td rowspan=3 class=xl3301227280 width=100 style=\'border-bottom:1.0pt solid black;
  width:75pt\'>Cantidad GEE</td>
  <td colspan=2 rowspan=3 class=xl3300827280 width=205 style=\'border-right:
  1.0pt solid black;border-bottom:1.0pt solid black;width:154pt\'>Organismo</td>
  <td rowspan=3 class=xl3301227280 width=99 style=\'border-bottom:1.0pt solid black;
  width:74pt\'>Marca</td>
  <td rowspan=3 class=xl3301227280 width=98 style=\'border-bottom:1.0pt solid black;
  width:74pt\'>Potencia kVA</td>
  <td colspan=2 rowspan=2 class=xl3300827280 width=204 style=\'border-right:
  1.0pt solid black;width:154pt\'>Cantidad de Opraciones</td>
  <td colspan=2 rowspan=2 class=xl3300827280 width=214 style=\'border-right:
  1.0pt solid black;width:161pt\'>Total de horas trabajadas</td>
  <td rowspan=3 class=xl3301227280 width=100 style=\'border-bottom:1.0pt solid black;
  width:75pt\'>Energ&iacute;a generada kWh</td>
  <td colspan=2 rowspan=2 class=xl3300827280 width=207 style=\'border-right:
  1.0pt solid black;width:156pt\'>Combustible consumido<span
  style=\'mso-spacerun:yes\'>                </span>Lts</td>
  <td rowspan=3 class=xl3301227280 width=115 style=\'border-bottom:1.0pt solid black;
  width:86pt\'>Litros/hora</td>
  <td rowspan=3 class=xl3301627280 style=\'border-bottom:1.0pt solid black\'>g/kWh</td>
  <td rowspan=3 class=xl3300327280 width=128 style=\'border-bottom:1.0pt solid black;
  width:96pt\'>Cargabilidad promedio<span
  style=\'mso-spacerun:yes\'>                 </span>%</td>
  <td class=xl3296827280></td>
 </tr>
 <tr height=41 style=\'mso-height-source:userset;height:30.75pt\'>
  <td height=41 class=xl3296827280 style=\'height:30.75pt\'></td>
 </tr>
 <tr height=26 style=\'mso-height-source:userset;height:20.1pt\'>
  <td height=26 class=xl3297227280 width=102 style=\'height:20.1pt;width:77pt\'>Con
  carga</td>
  <td class=xl3297327280 width=102 style=\'border-left:none;width:77pt\'>Sin
  carga</td>
  <td class=xl3297227280 width=112 style=\'width:84pt\'>Con carga</td>
  <td class=xl3297227280 width=102 style=\'border-left:none;width:77pt\'>Sin
  carga</td>
  <td class=xl3297227280 width=102 style=\'width:77pt\'>Con carga</td>
  <td class=xl3297227280 width=105 style=\'border-left:none;width:79pt\'>Sin
  carga</td>
  <td class=xl3296827280></td>
 </tr>';

        $totalOperConCarga = 0;
        $totalOperSinCarga = 0;
        $totalHorasConCarga = 0;
        $totalHorasSinCarga = 0;
        $totalEnergiaGen = 0;
        $totalConsumoConCarga = 0;
        $totalConsumoSinCarga = 0;
        foreach ($records as $record) {
            $totalOperConCarga += $record->cant_oper_con_carga;
            $totalOperSinCarga += $record->cant_oper_sin_carga;
            $totalHorasConCarga += $record->horas_con_carga;
            $totalHorasSinCarga += $record->horas_sin_carga;
            $totalEnergiaGen+= $record->energia_generada;
            $totalConsumoConCarga += $record->comb_consumido_con_carga;
            $totalConsumoSinCarga += $record->comb_consumido_sin_carga;

            $html .= '
 <tr height=26 style=\'mso - height - source:userset;height:20.1pt\'>
  <td height=26 class=xl3297627280 width=122 style=\'height:20.1pt;width:200pt\'>'. $this->toHtml($record->municipio_nombre) .'</td>
  <td colspan=2 class=xl3300627280 width=231 style=\'border - right:.5pt solid black;
  border - left:none;width:350pt\'>'. $this->toHtml($record->unidad_nombre) .'</td>
  <td class=xl3297627280 width=100 style=\'border - left:none;width:75pt\'>1</td>
  <td colspan=2 class=xl3300627280 width=205 style=\'border - right:.5pt solid black;
  border - left:none;width:154pt\'></td>
  <td class=xl3297627280 width=99 style=\'border - left:none;width:74pt\'>'. $this->toHtml($record->equipo_marca) .'</td>
  <td class=xl3297727280 width=98 style=\'border - left:none;width:74pt\'>'. $this->toHtml($record->kva) .'</td>
  <td class=xl3297827280 style=\'border - left:none\'>'. $this->toHtml($record->cant_oper_con_carga) .'</td>
  <td class=xl3297827280 style=\'border - left:none\'>'. $this->toHtml($record->cant_oper_sin_carga) .'</td>
  <td class=xl3297927280 style=\'border - left:none\'>'. $this->toHtml($record->horas_con_carga) .'</td>
  <td class=xl3297927280 style=\'border - left:none\'>'. $this->toHtml($record->horas_sin_carga) .'</td>
  <td class=xl3297927280 style=\'border - left:none\'>'. $this->toHtml($record->energia_generada) .'</td>
  <td class=xl3297927280 style=\'border - left:none\'>'. $this->toHtml($record->comb_consumido_con_carga) .'</td>
  <td class=xl3297927280 style=\'border - left:none\'>'. $this->toHtml($record->comb_consumido_sin_carga) .'</td>
  <td class=xl3297927280 style=\'border - left:none\'>'. $this->toHtml($record->indice_consumo) .'</td>
  <td class=xl3299327280 style=\'border - left:none\'>'. $this->toHtml($record->indice_cargabilidad) .'</td>
  <td class=xl3299527280 style=\'border - left:none\'>'. $this->toHtml($record->porciento_cargabilidad) .'</td>
  <td class=xl3296827280></td>
 </tr>';
        }


        $pieFirma = $em->getRepository('PortadoresBundle:PieFirma')->findOneBy(['documento' => DocumentosEnum::anexo3GEE, 'nunidadid' => $unidad]);
        $revisa = $pieFirma ? $em->find('PortadoresBundle:Persona', $pieFirma->getRevisa()) : null;
        $aprueba = $pieFirma ? $em->find('PortadoresBundle:Persona', $pieFirma->getAutoriza()) : null;


        $html .= '<tr height=26 style=\'mso-height-source:userset;height:20.1pt\'>
  <td colspan=7 height=26 class=xl3300027280 style=\'border-right:.5pt solid black;
  height:20.1pt\'>&nbsp;</td>
  <td class=xl3298627280 style=\'border-top:none;border-left:none\'>Total:</td>
  <td class=xl3298727280 style=\'border-top:none;border-left:none\'>'. $totalOperConCarga .'</td>
  <td class=xl3298727280 style=\'border-top:none;border-left:none\'>'. $totalOperSinCarga .'</td>
  <td class=xl3298827280 style=\'border-top:none;border-left:none\'>'. $totalHorasConCarga .'</td>
  <td class=xl3298827280 style=\'border-top:none;border-left:none\'>'. $totalOperSinCarga .'</td>
  <td class=xl3298827280 style=\'border-top:none;border-left:none\'>'. $totalEnergiaGen .'</td>
  <td class=xl3298827280 style=\'border-top:none;border-left:none\'>'. $totalConsumoConCarga .'</td>
  <td class=xl3298827280 style=\'border-top:none;border-left:none\'>'. $totalConsumoSinCarga .'</td>
  <td class=xl3298527280 style=\'border-top:none;border-left:none\'>&nbsp;</td>
  <td class=xl3298527280 style=\'border-top:none;border-left:none\'>&nbsp;</td>
  <td class=xl3298527280 style=\'border-top:none;border-left:none\'>&nbsp;</td>
  <td class=xl3296827280></td>
 </tr>
 <tr height=26 style=\'mso-height-source:userset;height:20.1pt\'>
  <td height=26 class=xl3296827280 style=\'height:20.1pt\'></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3297427280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3297427280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
 </tr>
 <tr height=26 style=\'mso-height-source:userset;height:20.1pt\'>
  <td height=26 class=xl3296827280 style=\'height:20.1pt\'></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
 </tr>
 <tr height=26 style=\'mso-height-source:userset;height:20.1pt\'>
  <td height=26 class=xl3298927280 colspan=3 style=\'height:20.1pt\'>Representantes
  de la Entidad:</td>
  <td class=xl3299727280></td>
  <td class=xl3299727280></td>
  <td class=xl3299027280></td>
  <td class=xl3299027280></td>
  <td class=xl3299027280></td>
  <td class=xl3299027280></td>
  <td class=xl3299027280></td>
  <td class=xl3297527280></td>
  <td class=xl3297027280></td>
  <td class=xl3297027280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
 </tr>
 <tr height=26 style=\'mso-height-source:userset;height:20.1pt\'>
  <td height=26 class=xl3298927280 style=\'height:20.1pt\'></td>
  <td class=xl3299727280></td>
  <td class=xl3299727280></td>
  <td class=xl3299727280></td>
  <td class=xl3299727280></td>
  <td class=xl3299027280></td>
  <td class=xl3299027280></td>
  <td class=xl3299027280></td>
  <td class=xl3299027280></td>
  <td class=xl3299027280></td>
  <td class=xl3297527280></td>
  <td class=xl3297027280></td>
  <td class=xl3297027280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
 </tr>
 <tr height=26 style=\'mso-height-source:userset;height:20.1pt\'>
  <td height=26 class=xl3299027280 colspan=3 style=\'height:20.1pt\'>Firma:
  ______________________<span style=\'mso-spacerun:yes\'>  </span></td>
  <td class=xl3299727280></td>
  <td class=xl3299727280></td>
  <td class=xl3299027280 colspan=4><span style=\'mso-spacerun:yes\'> </span>Firma
  y Cu&ntilde;o: _______________________</td>
  <td class=xl3299727280></td>
  <td class=xl3297527280></td>
  <td class=xl3297527280></td>
  <td class=xl3297027280></td>
  <td class=xl3297027280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
 </tr>
 <tr height=26 style=\'mso-height-source:userset;height:20.1pt\'>
  <td height=26 class=xl3299027280 style=\'height:20.1pt\'></td>
  <td class=xl3299727280></td>
  <td class=xl3299727280></td>
  <td class=xl3299727280></td>
  <td class=xl3299727280></td>
  <td class=xl3299027280></td>
  <td class=xl3299027280></td>
  <td class=xl3299027280></td>
  <td class=xl3299027280></td>
  <td class=xl3299727280></td>
  <td class=xl3297527280></td>
  <td class=xl3297527280></td>
  <td class=xl3297027280></td>
  <td class=xl3297027280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
 </tr>
 <tr height=26 style=\'mso-height-source:userset;height:20.1pt\'>
  <td height=26 class=xl3299027280 colspan=3 style=\'height:20.1pt\'>Nombre: '. ($revisa ? $this->toHtml($revisa->getNombre()) : '') .'</td>
  <td class=xl3299727280></td>
  <td class=xl3299727280></td>
  <td class=xl3299027280 colspan=3>Nombre: '. ($aprueba ? $this->toHtml($aprueba->getNombre()) : '') .'</td>
  <td class=xl3299027280></td>
  <td class=xl3299727280></td>
  <td class=xl3297527280></td>
  <td class=xl3297527280></td>
  <td class=xl3297027280></td>
  <td class=xl3297027280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
 </tr>
 <tr height=26 style=\'mso-height-source:userset;height:20.1pt\'>
  <td height=26 class=xl3299027280 colspan=3 style=\'height:20.1pt\'>'. ($revisa ? $this->toHtml($revisa->getCargoid()->getNombre()) : '') .'</td>
  <td class=xl3299727280></td>
  <td class=xl3299727280></td>
  <td class=xl3299027280 colspan=3>'. ($aprueba ? $this->toHtml($aprueba->getCargoid()->getNombre()) : '') .'</td>
  <td class=xl3299027280></td>
  <td class=xl3299727280></td>
  <td class=xl3297527280></td>
  <td class=xl3297527280></td>
  <td class=xl3297027280></td>
  <td class=xl3297027280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
 </tr>
 <tr height=26 style=\'mso-height-source:userset;height:20.1pt\'>
  <td height=26 class=xl3299027280 colspan=3 style=\'height:20.1pt\'><span
  style=\'mso-spacerun:yes\'>                                                   </span></td>
  <td class=xl3299727280></td>
  <td class=xl3299727280></td>
  <td class=xl3299027280><span style=\'mso-spacerun:yes\'>  </span></td>
  <td class=xl3299027280></td>
  <td class=xl3299027280></td>
  <td class=xl3299027280></td>
  <td class=xl3299727280></td>
  <td class=xl3297527280></td>
  <td class=xl3297527280></td>
  <td class=xl3297027280></td>
  <td class=xl3297027280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
 </tr>
 <tr height=26 style=\'mso-height-source:userset;height:20.1pt\'>
  <td height=26 class=xl3298927280 colspan=3 style=\'height:20.1pt\'>Representante
  de la UEB de GEP</td>
  <td class=xl3299727280></td>
  <td class=xl3299727280></td>
  <td class=xl3299027280></td>
  <td class=xl3299027280></td>
  <td class=xl3299027280></td>
  <td class=xl3299027280></td>
  <td class=xl3299727280></td>
  <td class=xl3297527280></td>
  <td class=xl3297527280></td>
  <td class=xl3297027280></td>
  <td class=xl3297027280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
 </tr>
 <tr height=26 style=\'mso-height-source:userset;height:20.1pt\'>
  <td height=26 class=xl3298927280 style=\'height:20.1pt\'></td>
  <td class=xl3299727280></td>
  <td class=xl3299727280></td>
  <td class=xl3299727280></td>
  <td class=xl3299727280></td>
  <td class=xl3299027280></td>
  <td class=xl3299027280></td>
  <td class=xl3299027280></td>
  <td class=xl3299027280></td>
  <td class=xl3299727280></td>
  <td class=xl3297527280></td>
  <td class=xl3297527280></td>
  <td class=xl3297027280></td>
  <td class=xl3297027280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
 </tr>
 <tr height=26 style=\'mso-height-source:userset;height:20.1pt\'>
  <td height=26 class=xl3299027280 colspan=3 style=\'height:20.1pt\'>Firma:
  _________________<span style=\'mso-spacerun:yes\'>  </span></td>
  <td class=xl3299727280></td>
  <td class=xl3299727280></td>
  <td class=xl3299027280 colspan=3>Firma: _________________<span
  style=\'mso-spacerun:yes\'>  </span></td>
  <td class=xl3299027280></td>
  <td class=xl3299727280></td>
  <td class=xl3297527280></td>
  <td class=xl3297527280></td>
  <td class=xl3297027280></td>
  <td class=xl3297027280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
 </tr>
 <tr height=26 style=\'mso-height-source:userset;height:20.1pt\'>
  <td height=26 class=xl3299027280 style=\'height:20.1pt\'></td>
  <td class=xl3299727280></td>
  <td class=xl3299727280></td>
  <td class=xl3299727280></td>
  <td class=xl3299727280></td>
  <td class=xl3299027280></td>
  <td class=xl3299027280></td>
  <td class=xl3299027280></td>
  <td class=xl3299027280></td>
  <td class=xl3299727280></td>
  <td class=xl3297527280></td>
  <td class=xl3297527280></td>
  <td class=xl3297027280></td>
  <td class=xl3297027280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
 </tr>
 <tr height=26 style=\'mso-height-source:userset;height:20.1pt\'>
  <td height=26 class=xl3299027280 colspan=4 style=\'height:20.1pt\'>Nombre:
  ____________________________________</td>
  <td class=xl3299727280></td>
  <td class=xl3299027280 colspan=5>Nombre: ____________________________________</td>
  <td class=xl3297527280></td>
  <td class=xl3297527280></td>
  <td class=xl3297027280></td>
  <td class=xl3297027280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
 </tr>
 <tr height=26 style=\'mso-height-source:userset;height:20.1pt\'>
  <td height=26 class=xl3299027280 colspan=3 style=\'height:20.1pt\'>Especialista
  Explotaci&oacute;n UEB-DGE Prov.</td>
  <td class=xl3299727280></td>
  <td class=xl3299727280></td>
  <td class=xl3299027280 colspan=4>Especialista Combustible UEB-DGE Prov.</td>
  <td class=xl3299727280></td>
  <td class=xl3297527280></td>
  <td class=xl3297527280></td>
  <td class=xl3297027280></td>
  <td class=xl3297027280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
 </tr>
 <tr height=26 style=\'mso-height-source:userset;height:20.1pt\'>
  <td height=26 class=xl3296827280 style=\'height:20.1pt\'></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3297027280 colspan=2><span
  style=\'mso-spacerun:yes\'>                                                   </span></td>
  <td class=xl3297027280></td>
  <td class=xl3297027280></td>
  <td class=xl3297027280 colspan=2><span
  style=\'mso-spacerun:yes\'>                                                   </span></td>
  <td class=xl3297027280></td>
  <td class=xl3297027280></td>
  <td class=xl3297027280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
  <td class=xl3296827280></td>
 </tr>
 <![if supportMisalignedColumns]>
 <tr height=0 style=\'display:none\'>
  <td width=122 style=\'width:92pt\'></td>
  <td width=115 style=\'width:86pt\'></td>
  <td width=116 style=\'width:87pt\'></td>
  <td width=100 style=\'width:75pt\'></td>
  <td width=106 style=\'width:80pt\'></td>
  <td width=99 style=\'width:74pt\'></td>
  <td width=99 style=\'width:74pt\'></td>
  <td width=98 style=\'width:74pt\'></td>
  <td width=102 style=\'width:77pt\'></td>
  <td width=102 style=\'width:77pt\'></td>
  <td width=112 style=\'width:84pt\'></td>
  <td width=102 style=\'width:77pt\'></td>
  <td width=100 style=\'width:75pt\'></td>
  <td width=102 style=\'width:77pt\'></td>
  <td width=105 style=\'width:79pt\'></td>
  <td width=115 style=\'width:86pt\'></td>
  <td width=108 style=\'width:81pt\'></td>
  <td width=128 style=\'width:96pt\'></td>
  <td width=105 style=\'width:79pt\'></td>
 </tr>
 <![endif]>
</table>

</div>


<!----------------------------->
<!--FINAL DE LOS RESULTADOS DEL ASISTENTE PARA PUBLICAR COMO PÁGINA WEB DE
EXCEL-->
<!----------------------------->
</body>

</html>
';
        return new JsonResponse(['success' => true, 'html' => $html]);
    }

    private function getStyles() {
        // Agregar a la clase .xl729688 para obtener formato fecha
        // mso-number-format:"dd\\-mm\\-yy\\;\\@";

        return '        
        <style id="Tabla combustible 2019_27280_Styles">
            <!--table
	{mso-displayed-decimal-separator:"\.";
	mso-displayed-thousand-separator:"\,";}
.font527280
	{color:black;
	font-size:9.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Tahoma, sans-serif;
	mso-font-charset:0;}
.font627280
	{color:black;
	font-size:11.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Tahoma, sans-serif;
	mso-font-charset:0;}
.font727280
	{color:black;
	font-size:14.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Tahoma, sans-serif;
	mso-font-charset:0;}
.xl3296827280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:11.0pt;
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
.xl3296927280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:13.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl3297027280
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:9.0pt;
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
.xl3297127280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:13.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:middle;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl3297227280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:13.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl3297327280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:13.0pt;
	font-weight:700;
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
.xl3297427280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:11.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;
	mso-number-format:Fixed;
	text-align:center;
	vertical-align:middle;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl3297527280
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
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
.xl3297627280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
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
.xl3297727280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:"0\.0";
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl3297827280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:0;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl3297927280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:Fixed;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl3298027280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:16.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:"d\\-m\\-yy\;\@";
	text-align:center;
	vertical-align:middle;
	border:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl3298127280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:16.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl3298227280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:16.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:0;
	text-align:center;
	vertical-align:middle;
	border:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl3298327280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:16.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:Fixed;
	text-align:center;
	vertical-align:middle;
	border:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl3298427280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:16.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:"0\.0";
	text-align:center;
	vertical-align:middle;
	border:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl3298527280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:16.0pt;
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
.xl3298627280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:13.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl3298727280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:0;
	text-align:center;
	vertical-align:middle;
	border:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl3298827280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:Fixed;
	text-align:center;
	vertical-align:middle;
	border:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl3298927280
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:16.0pt;
	font-weight:700;
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
.xl3299027280
	{padding:0px;
	mso-ignore:padding;
	color:windowtext;
	font-size:16.0pt;
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
.xl3299127280
	{padding:0px;
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
	border:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl3299227280
	{padding:0px;
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
.xl3299327280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
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
.xl3299427280
	{padding:0px;
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
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl3299527280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:Fixed;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl3299627280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:16.0pt;
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
.xl3299727280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:16.0pt;
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
.xl3299827280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:16.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl3299927280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:16.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl3300027280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:16.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl3300127280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:16.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl3300227280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:16.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl3300327280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:13.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
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
.xl3300427280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:13.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
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
	white-space:normal;}
.xl3300527280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:13.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
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
.xl3300627280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:12.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
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
.xl3300727280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:16.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
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
.xl3300827280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:13.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
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
.xl3300927280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:13.0pt;
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
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl3301027280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:13.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
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
	white-space:normal;}
.xl3301127280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:13.0pt;
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
	border-bottom:none;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl3301227280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:13.0pt;
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
.xl3301327280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:13.0pt;
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
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl3301427280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:13.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
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
.xl3301527280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:13.0pt;
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
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:normal;}
.xl3301627280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:13.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
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
.xl3301727280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:13.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
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
.xl3301827280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:13.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
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
.xl3301927280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:13.0pt;
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
.xl3302027280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:13.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
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
.xl3302127280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:16.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl3302227280
	{padding:0px;
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
	border-top:1.0pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl3302327280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:16.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl3302427280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:16.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:.5pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl3302527280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:16.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
.xl3302627280
	{padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:16.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:left;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	mso-background-source:auto;
	mso-pattern:auto;
	white-space:nowrap;}
-->
        </style>        
        ';
    }

    private function toHtml($text) {
        return htmlentities($text, 0, 'UTF-8');
    }
}