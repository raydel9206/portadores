<?php
/**
 * Created by PhpStorm.
 * User: pfcadenas
 * Date: 11/11/2016
 * Time: 15:43
 */

namespace Geocuba\PortadoresBundle\Controller;

//use Geocuba\AdminBundle\Util\Util;
use Doctrine\Common\Util\Debug;
use Geocuba\PortadoresBundle\Entity\Vehiculo;
use Geocuba\PortadoresBundle\Util\FechaUtil;
use Geocuba\PortadoresBundle\Entity\PlanificacionCombustible;
use Geocuba\PortadoresBundle\Entity\PlanificacionCombustibleCuc;
use Geocuba\PortadoresBundle\Util\DocumentosEnum;
use Doctrine\Common\CommonException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;

class PlanCombustibleController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $_vehiculo = trim($request->get('vehiculo'));
        $_anno = (trim($request->get('anno')) != '') ? trim($request->get('anno')) : $request->getSession()->get('current_year');
        $_tipoCombustible = ($request->get('tipo_combustibleid') !== '-1') ? trim($request->get('tipo_combustibleid')) : null;
        $nunidadid = $request->get('unidadid');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $vehiculos = $em->getRepository('PortadoresBundle:Vehiculo')->buscarVehiculoTipoCombustible($_vehiculo, $_tipoCombustible, $_unidades);
        $total = $em->getRepository('PortadoresBundle:Vehiculo')->buscarVehiculoTipoCombustible($_vehiculo, $_tipoCombustible, $_unidades);

        $entities = array();
        foreach ($vehiculos as $vehiculo) {
            /**@var Vehiculo $vehiculo */
            $planificacion = $em->getRepository('PortadoresBundle:PlanificacionCombustible')->findOneBy(array(
                'vehiculoid' => $vehiculo->getId(),
                'anno' => $_anno,
                'visible' => true
            ));

            if (!$planificacion) {
                $planificacion = new PlanificacionCombustible();
                $planificacion->setVehiculoid($vehiculo);
                $planificacion->setPrecioCombustible($vehiculo->getNtipoCombustibleid()->getPrecio());
                $planificacion->setAnno($_anno);
                $planificacion->setAprobada(false);
                $planificacion->setVisible(true);
                $em->persist($planificacion);
            }

            $planificacionCUC = $em->getRepository('PortadoresBundle:PlanificacionCombustibleCuc')->findOneBy(array(
                'vehiculoid' => $vehiculo->getId(),
                'anno' => $_anno,
                'visible' => true
            ));

            if (!$planificacionCUC) {
                $planificacionCUC = new PlanificacionCombustibleCuc();
                $planificacionCUC->setVehiculoid($vehiculo);
                $planificacionCUC->setPrecioCombustible($vehiculo->getNtipoCombustibleid()->getPrecio());
                $planificacionCUC->setAnno($_anno);
                $planificacionCUC->setAprobada(false);
                $planificacionCUC->setVisible(true);

                $em->persist($planificacionCUC);
            }

            $em->flush();

            $entities [] = $planificacion;
        }

        $_data = array();
        foreach ($entities as $entityMN) {
            $combustible_litros_total_mn = round($entityMN->getCombustibleLitrosEne() + $entityMN->getCombustibleLitrosFeb()
                + $entityMN->getCombustibleLitrosMar() + $entityMN->getCombustibleLitrosAbr() + $entityMN->getCombustibleLitrosMay()
                + $entityMN->getCombustibleLitrosJun() + $entityMN->getCombustibleLitrosJul() + $entityMN->getCombustibleLitrosAgo()
                + $entityMN->getCombustibleLitrosSep() + $entityMN->getCombustibleLitrosOct() + $entityMN->getCombustibleLitrosNov()
                + $entityMN->getCombustibleLitrosDic(), 2);
            $nivel_act_kms_total_mn = round($entityMN->getNivelActKmsEne() + $entityMN->getNivelActKmsFeb() + $entityMN->getNivelActKmsMar()
                + $entityMN->getNivelActKmsAbr() + $entityMN->getNivelActKmsMay() + $entityMN->getNivelActKmsJun() + $entityMN->getNivelActKmsJul()
                + $entityMN->getNivelActKmsAgo() + $entityMN->getNivelActKmsSep() + $entityMN->getNivelActKmsOct() + $entityMN->getNivelActKmsNov()
                + $entityMN->getNivelActKmsDic(), 2);
            $liquido_freno_total = round($entityMN->getLiquidoFrenoEne() + $entityMN->getLiquidoFrenoFeb() + $entityMN->getLiquidoFrenoMar()
                + $entityMN->getLiquidoFrenoAbr() + $entityMN->getLiquidoFrenoMay() + $entityMN->getLiquidoFrenoJun() + $entityMN->getLiquidoFrenoJul()
                + $entityMN->getLiquidoFrenoAgo() + $entityMN->getLiquidoFrenoSep() + $entityMN->getLiquidoFrenoOct() + $entityMN->getLiquidoFrenoNov()
                + $entityMN->getLiquidoFrenoDic(), 2);
            $lubricante_total = round($entityMN->getLubricanteEne() + $entityMN->getLubricanteFeb() + $entityMN->getLubricanteMar()
                + $entityMN->getLubricanteAbr() + $entityMN->getLubricanteMay() + $entityMN->getLubricanteJun() + $entityMN->getLubricanteJul()
                + $entityMN->getLubricanteAgo() + $entityMN->getLubricanteSep() + $entityMN->getLubricanteOct() + $entityMN->getLubricanteNov()
                + $entityMN->getLubricanteDic(), 2);


            $entityCUC = $em->getRepository('PortadoresBundle:PlanificacionCombustibleCuc')->findOneBy(array(
                'anno' => $entityMN->getAnno(),
                'vehiculoid' => $entityMN->getVehiculoid()->getId(),
                'visible' => true
            ));
            $combustible_litros_total_cuc = round($entityCUC->getCombustibleLitrosEne() + $entityCUC->getCombustibleLitrosFeb()
                + $entityCUC->getCombustibleLitrosMar() + $entityCUC->getCombustibleLitrosAbr() + $entityCUC->getCombustibleLitrosMay()
                + $entityCUC->getCombustibleLitrosJun() + $entityCUC->getCombustibleLitrosJul() + $entityCUC->getCombustibleLitrosAgo()
                + $entityCUC->getCombustibleLitrosSep() + $entityCUC->getCombustibleLitrosOct() + $entityCUC->getCombustibleLitrosNov()
                + $entityCUC->getCombustibleLitrosDic(), 2);
            $nivel_act_kms_total_cuc = round($entityCUC->getNivelActKmsEne() + $entityCUC->getNivelActKmsFeb() + $entityCUC->getNivelActKmsMar()
                + $entityCUC->getNivelActKmsAbr() + $entityCUC->getNivelActKmsMay() + $entityCUC->getNivelActKmsJun() + $entityCUC->getNivelActKmsJul()
                + $entityCUC->getNivelActKmsAgo() + $entityCUC->getNivelActKmsSep() + $entityCUC->getNivelActKmsOct() + $entityCUC->getNivelActKmsNov()
                + $entityCUC->getNivelActKmsDic(), 2);


            $norma = $entityMN->getVehiculoid()->getNormaFar();
            $_data[] = array(
                'id' => $entityMN->getId(),
                'aprobada' => $entityMN->getAprobada(),
                'vehiculoid' => $entityMN->getVehiculoid()->getId(),
                'vehiculo' => $entityMN->getVehiculoid()->getMatricula(),
                'actividad_id' => $entityMN->getVehiculoid()->getActividad()->getId(),
                'actividad_nombre' => $entityMN->getVehiculoid()->getActividad()->getNombre(),
                'vehiculo_marca' => $entityMN->getVehiculoid()->getNmodeloid()->getMarcaVehiculoid()->getNombre(),
                'vehiculo_denominacion' => $entityMN->getVehiculoid()->getNdenominacionVehiculoid()->getNombre(),
                'tipo_combustible' => $entityMN->getVehiculoid()->getNtipoCombustibleid()->getNombre(),
                'vehiculo_norma' => floatval($norma),
                'vehiculo_norma_lubricante' => 0.015,
                'vehiculo_norma_liquido_freno' => 0.00015,
                'anno' => $entityMN->getAnno(),

                'combustible_litros_ene_mn' => $entityMN->getCombustibleLitrosEne(),
                'combustible_litros_feb_mn' => $entityMN->getCombustibleLitrosFeb(),
                'combustible_litros_mar_mn' => $entityMN->getCombustibleLitrosMar(),
                'combustible_litros_abr_mn' => $entityMN->getCombustibleLitrosAbr(),
                'combustible_litros_may_mn' => $entityMN->getCombustibleLitrosMay(),
                'combustible_litros_jun_mn' => $entityMN->getCombustibleLitrosJun(),
                'combustible_litros_jul_mn' => $entityMN->getCombustibleLitrosJul(),
                'combustible_litros_ago_mn' => $entityMN->getCombustibleLitrosAgo(),
                'combustible_litros_sep_mn' => $entityMN->getCombustibleLitrosSep(),
                'combustible_litros_oct_mn' => $entityMN->getCombustibleLitrosOct(),
                'combustible_litros_nov_mn' => $entityMN->getCombustibleLitrosNov(),
                'combustible_litros_dic_mn' => $entityMN->getCombustibleLitrosDic(),
                'combustible_litros_total_mn' => $combustible_litros_total_mn,
                'combustible_litros_total_anno_mn' => $entityMN->getCombustibleLitrosTotal(),

                'combustible_litros_ene_cuc' => $entityCUC->getCombustibleLitrosEne(),
                'combustible_litros_feb_cuc' => $entityCUC->getCombustibleLitrosFeb(),
                'combustible_litros_mar_cuc' => $entityCUC->getCombustibleLitrosMar(),
                'combustible_litros_abr_cuc' => $entityCUC->getCombustibleLitrosAbr(),
                'combustible_litros_may_cuc' => $entityCUC->getCombustibleLitrosMay(),
                'combustible_litros_jun_cuc' => $entityCUC->getCombustibleLitrosJun(),
                'combustible_litros_jul_cuc' => $entityCUC->getCombustibleLitrosJul(),
                'combustible_litros_ago_cuc' => $entityCUC->getCombustibleLitrosAgo(),
                'combustible_litros_sep_cuc' => $entityCUC->getCombustibleLitrosSep(),
                'combustible_litros_oct_cuc' => $entityCUC->getCombustibleLitrosOct(),
                'combustible_litros_nov_cuc' => $entityCUC->getCombustibleLitrosNov(),
                'combustible_litros_dic_cuc' => $entityCUC->getCombustibleLitrosDic(),
                'combustible_litros_total_cuc' => $combustible_litros_total_cuc,
                'combustible_litros_total_anno_cuc' => $entityCUC->getCombustibleLitrosTotal(),

                'combustible_litros_ene' => $entityMN->getCombustibleLitrosEne() + $entityCUC->getCombustibleLitrosEne(),
                'combustible_litros_feb' => $entityMN->getCombustibleLitrosFeb() + $entityCUC->getCombustibleLitrosFeb(),
                'combustible_litros_mar' => $entityMN->getCombustibleLitrosMar() + $entityCUC->getCombustibleLitrosMar(),
                'combustible_litros_abr' => $entityMN->getCombustibleLitrosAbr() + $entityCUC->getCombustibleLitrosAbr(),
                'combustible_litros_may' => $entityMN->getCombustibleLitrosMay() + $entityCUC->getCombustibleLitrosMay(),
                'combustible_litros_jun' => $entityMN->getCombustibleLitrosJun() + $entityCUC->getCombustibleLitrosJun(),
                'combustible_litros_jul' => $entityMN->getCombustibleLitrosJul() + $entityCUC->getCombustibleLitrosJul(),
                'combustible_litros_ago' => $entityMN->getCombustibleLitrosAgo() + $entityCUC->getCombustibleLitrosAgo(),
                'combustible_litros_sep' => $entityMN->getCombustibleLitrosSep() + $entityCUC->getCombustibleLitrosSep(),
                'combustible_litros_oct' => $entityMN->getCombustibleLitrosOct() + $entityCUC->getCombustibleLitrosOct(),
                'combustible_litros_nov' => $entityMN->getCombustibleLitrosNov() + $entityCUC->getCombustibleLitrosNov(),
                'combustible_litros_dic' => $entityMN->getCombustibleLitrosDic() + $entityCUC->getCombustibleLitrosDic(),
                'combustible_litros_total' => $combustible_litros_total_mn + $combustible_litros_total_cuc,
                'combustible_litros_total_anno' => $entityMN->getCombustibleLitrosTotal() + $entityCUC->getCombustibleLitrosTotal(),

                'nivel_act_kms_ene_mn' => $entityMN->getNivelActKmsEne(),
                'nivel_act_kms_feb_mn' => $entityMN->getNivelActKmsFeb(),
                'nivel_act_kms_mar_mn' => $entityMN->getNivelActKmsMar(),
                'nivel_act_kms_abr_mn' => $entityMN->getNivelActKmsAbr(),
                'nivel_act_kms_may_mn' => $entityMN->getNivelActKmsMay(),
                'nivel_act_kms_jun_mn' => $entityMN->getNivelActKmsJun(),
                'nivel_act_kms_jul_mn' => $entityMN->getNivelActKmsJul(),
                'nivel_act_kms_ago_mn' => $entityMN->getNivelActKmsAgo(),
                'nivel_act_kms_sep_mn' => $entityMN->getNivelActKmsSep(),
                'nivel_act_kms_oct_mn' => $entityMN->getNivelActKmsOct(),
                'nivel_act_kms_nov_mn' => $entityMN->getNivelActKmsNov(),
                'nivel_act_kms_dic_mn' => $entityMN->getNivelActKmsDic(),
                'nivel_act_kms_total_mn' => $nivel_act_kms_total_mn,
                'nivel_act_kms_total_anno_mn' => $entityMN->getNivelActKmsTotal(),

                'nivel_act_kms_ene_cuc' => $entityCUC->getNivelActKmsEne(),
                'nivel_act_kms_feb_cuc' => $entityCUC->getNivelActKmsFeb(),
                'nivel_act_kms_mar_cuc' => $entityCUC->getNivelActKmsMar(),
                'nivel_act_kms_abr_cuc' => $entityCUC->getNivelActKmsAbr(),
                'nivel_act_kms_may_cuc' => $entityCUC->getNivelActKmsMay(),
                'nivel_act_kms_jun_cuc' => $entityCUC->getNivelActKmsJun(),
                'nivel_act_kms_jul_cuc' => $entityCUC->getNivelActKmsJul(),
                'nivel_act_kms_ago_cuc' => $entityCUC->getNivelActKmsAgo(),
                'nivel_act_kms_sep_cuc' => $entityCUC->getNivelActKmsSep(),
                'nivel_act_kms_oct_cuc' => $entityCUC->getNivelActKmsOct(),
                'nivel_act_kms_nov_cuc' => $entityCUC->getNivelActKmsNov(),
                'nivel_act_kms_dic_cuc' => $entityCUC->getNivelActKmsDic(),
                'nivel_act_kms_total_cuc' => $nivel_act_kms_total_cuc,
                'nivel_act_kms_total_anno_cuc' => $entityCUC->getNivelActKmsTotal(),

                'nivel_act_kms_ene' => $entityMN->getNivelActKmsEne() + $entityCUC->getNivelActKmsEne(),
                'nivel_act_kms_feb' => $entityMN->getNivelActKmsFeb() + $entityCUC->getNivelActKmsFeb(),
                'nivel_act_kms_mar' => $entityMN->getNivelActKmsMar() + $entityCUC->getNivelActKmsMar(),
                'nivel_act_kms_abr' => $entityMN->getNivelActKmsAbr() + $entityCUC->getNivelActKmsAbr(),
                'nivel_act_kms_may' => $entityMN->getNivelActKmsMay() + $entityCUC->getNivelActKmsMay(),
                'nivel_act_kms_jun' => $entityMN->getNivelActKmsJun() + $entityCUC->getNivelActKmsJun(),
                'nivel_act_kms_jul' => $entityMN->getNivelActKmsJul() + $entityCUC->getNivelActKmsJul(),
                'nivel_act_kms_ago' => $entityMN->getNivelActKmsAgo() + $entityCUC->getNivelActKmsAgo(),
                'nivel_act_kms_sep' => $entityMN->getNivelActKmsSep() + $entityCUC->getNivelActKmsSep(),
                'nivel_act_kms_oct' => $entityMN->getNivelActKmsOct() + $entityCUC->getNivelActKmsOct(),
                'nivel_act_kms_nov' => $entityMN->getNivelActKmsNov() + $entityCUC->getNivelActKmsNov(),
                'nivel_act_kms_dic' => $entityMN->getNivelActKmsDic() + $entityCUC->getNivelActKmsDic(),
                'nivel_act_kms_total' => $nivel_act_kms_total_mn + $nivel_act_kms_total_cuc,
                'nivel_act_kms_total_anno' => $entityMN->getNivelActKmsTotal() + $entityCUC->getNivelActKmsTotal(),

                'liquido_freno_ene' => $entityMN->getLiquidoFrenoEne(),
                'liquido_freno_feb' => $entityMN->getLiquidoFrenoFeb(),
                'liquido_freno_mar' => $entityMN->getLiquidoFrenoMar(),
                'liquido_freno_abr' => $entityMN->getLiquidoFrenoAbr(),
                'liquido_freno_may' => $entityMN->getLiquidoFrenoMay(),
                'liquido_freno_jun' => $entityMN->getLiquidoFrenoJun(),
                'liquido_freno_jul' => $entityMN->getLiquidoFrenoJul(),
                'liquido_freno_ago' => $entityMN->getLiquidoFrenoAgo(),
                'liquido_freno_sep' => $entityMN->getLiquidoFrenoSep(),
                'liquido_freno_oct' => $entityMN->getLiquidoFrenoOct(),
                'liquido_freno_nov' => $entityMN->getLiquidoFrenoNov(),
                'liquido_freno_dic' => $entityMN->getLiquidoFrenoDic(),
                'liquido_freno_total' => $liquido_freno_total,
                'liquido_freno_total_anno' => $entityMN->getLiquidoFrenoTotal(),

                'lubricante_ene' => $entityMN->getLubricanteEne(),
                'lubricante_feb' => $entityMN->getLubricanteFeb(),
                'lubricante_mar' => $entityMN->getLubricanteMar(),
                'lubricante_abr' => $entityMN->getLubricanteAbr(),
                'lubricante_may' => $entityMN->getLubricanteMay(),
                'lubricante_jun' => $entityMN->getLubricanteJun(),
                'lubricante_jul' => $entityMN->getLubricanteJul(),
                'lubricante_ago' => $entityMN->getLubricanteAgo(),
                'lubricante_sep' => $entityMN->getLubricanteSep(),
                'lubricante_oct' => $entityMN->getLubricanteOct(),
                'lubricante_nov' => $entityMN->getLubricanteNov(),
                'lubricante_dic' => $entityMN->getLubricanteDic(),
                'lubricante_total' => $lubricante_total,
                'lubricante_total_anno' => $entityMN->getLubricanteTotal()
            );
        }
        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    public function loadPlanifCombVehiculoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $nunidadid = $request->get('unidadid');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $qb = $em->createQueryBuilder();
        $entities = $qb->select('a')
            ->from('PortadoresBundle:Vehiculo', 'a')
            ->where($qb->expr()->in('a.nunidadid', $_unidades))
            ->andWhere('a.visible = true')
            ->getQuery()->getResult();

        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'matricula' => $entity->getMatricula(),
                'norma' => $entity->getNorma(),
                'nro_inventario' => $entity->getNroInventario(),
                'nro_serie_carreceria' => $entity->getNroSerieCarreceria(),
                'nro_serie_motor' => $entity->getNroSerieMotor(),
                'color' => $entity->getColor(),
                'nro_circulacion' => $entity->getNroCirculacion(),
                'fecha_expiracion_circulacion' => (!is_null($entity->getFechaExpiracionCirculacion())) ? $entity->getFechaExpiracionCirculacion()->format('d/m/Y') : '',
                'anno_fabricacion' => $entity->getAnnoFabricacion(),
                'nmarca_vehiculoid' => $entity->getNmodeloid()->getMarcaVehiculoid()->getId(),
                'nestado_tecnicoid' => $entity->getNestadoTecnicoid()->getId(),
                'ndenominacion_vehiculoid' => $entity->getNdenominacionVehiculoid()->getId(),
                'nunidadid' => $entity->getNunidadid()->getId(),
                'nunidad' => $entity->getNunidadid()->getNombre(),
                'ntipo_combustibleid' => $entity->getNtipoCombustibleid()->getId()
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => count($_data)));
    }

    public function crearAction(Request $request)
    {

        $session = $request->getSession();
        $ipo_combustibleid = $request->get('ipo_combustibleid');
        $unidadid = $request->get('unidadid');
        $anno = $session->get('selected_year');
        $em = $this->getDoctrine()->getManager();

        $unidad = $em->getRepository('PortadoresBundle:Unidad')->find($unidadid);
        $tipo_combustible = $em->getRepository('Portadores:TipoCombustible')->find($ipo_combustibleid);
        $vehiculos = $em->getRepository('PortadoresBundle:Vehiculo')->findBy(array('ntipoCombustibleid' => $tipo_combustible, 'visible' => true, 'nunidadid' => $unidad));

        foreach ($vehiculos as $vehiculo) {
            /**@var Vehiculo $vehiculo */
            $planificacion = new PlanificacionCombustible();
            $planificacion->setVehiculoid($vehiculo);
            $planificacion->setPrecioCombustible($vehiculo->getNtipoCombustibleid()->getPrecio());
            $planificacion->setAnno($anno);
            $planificacion->setVisible(true);

            try {
                $em->persist($planificacion);
                $em->flush();
            } catch (\Exception $ex) {

                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
            }
        }

        return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Planificación de Combustible adicionada con éxito.'));

    }

    public function fastPrecioAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

//        $vehiculos = $em->getRepository('PortadoresBundle:Vehiculo')->findBy(array('ntipoCombustibleid' => $tipo_combustible, 'visible' => true, 'nunidadid' => $unidad));
        $entities = $em->getRepository('PortadoresBundle:PlanificacionCombustibleCuc')->findBy(array(
            'visible' => true
        ));
        foreach ($entities as $ent) {
            /**@var PlanificacionCombustible $ent */
            $vehiculo = $em->getRepository('PortadoresBundle:Vehiculo')->findOneBy(array('id' =>$ent->getVehiculoid()->getId()));
            /**@var Vehiculo $vehiculo */
            $ent->setPrecioCombustible($vehiculo->getNtipoCombustibleid()->getPrecio());
            $em->persist($ent);
        }
        try {

            $em->flush();
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }

        return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Planificación de Combustible adicionada con éxito.'));

    }

    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $anno = $session->get('selected_year');
        $vehiculoid = $request->get('vehiculoid');

        $vehiculo = $em->getRepository('PortadoresBundle:Vehiculo')->find($vehiculoid);

        $entities = $em->getRepository('PortadoresBundle:PlanificacionCombustible')->findBy(array(
            'vehiculoid' => $vehiculoid,
            'anno' => $anno,
            'visible' => true
        ));
        if ($entities) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe una Planificación para ese
            vehículo.'));
        }

        $entityMN = new PlanificacionCombustible();
        $entityMN->setVehiculoid($vehiculo);
        $entityMN->setPrecioCombustible($vehiculo->getNtipoCombustibleid()->getPrecio());
        $entityMN->setAnno($anno);
        $entityMN->setVisible(true);

        $entityCUC = new PlanificacionCombustibleCuc();
        $entityCUC->setVehiculoid($vehiculo);
        $entityCUC->setPrecioCombustible($vehiculo->getNtipoCombustibleid()->getPrecio());
        $entityCUC->setAnno($anno);
        $entityCUC->setVisible(true);

        try {
            $em->persist($entityMN);
            $em->persist($entityCUC);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Planificación de Combustible adicionada con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function modAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $store = json_decode($request->get('store'));

        for ($i = 0; $i < count($store); $i++) {
            $vehiculo = $em->getRepository('PortadoresBundle:Vehiculo')->find($store[$i]->vehiculoid);

            $entityMN = $em->getRepository('PortadoresBundle:PlanificacionCombustible')->findOneBy(array(
                'vehiculoid' => $store[$i]->vehiculoid,
                'anno' => $store[$i]->anno,
                'visible' => true
            ));
            /**
             * @var PlanificacionCombustible $entity
             */
            if (!is_null($entityMN)) {

                $entityMN->setVehiculoid($vehiculo);
                $entityMN->setPrecioCombustible($vehiculo->getNtipoCombustibleid()->getPrecio());
                $entityMN->setCombustibleLitrosEne($store[$i]->combustible_litros_ene_mn);
                $entityMN->setCombustibleLitrosFeb($store[$i]->combustible_litros_feb_mn);
                $entityMN->setCombustibleLitrosMar($store[$i]->combustible_litros_mar_mn);
                $entityMN->setCombustibleLitrosAbr($store[$i]->combustible_litros_abr_mn);
                $entityMN->setCombustibleLitrosMay($store[$i]->combustible_litros_may_mn);
                $entityMN->setCombustibleLitrosJun($store[$i]->combustible_litros_jun_mn);
                $entityMN->setCombustibleLitrosJul($store[$i]->combustible_litros_jul_mn);
                $entityMN->setCombustibleLitrosAgo($store[$i]->combustible_litros_ago_mn);
                $entityMN->setCombustibleLitrosSep($store[$i]->combustible_litros_sep_mn);
                $entityMN->setCombustibleLitrosOct($store[$i]->combustible_litros_oct_mn);
                $entityMN->setCombustibleLitrosNov($store[$i]->combustible_litros_nov_mn);
                $entityMN->setCombustibleLitrosDic($store[$i]->combustible_litros_dic_mn);
                $entityMN->setCombustibleLitrosTotal($store[$i]->combustible_litros_total_anno_mn);
                $entityMN->setNivelActKmsEne($store[$i]->nivel_act_kms_ene_mn);
                $entityMN->setNivelActKmsFeb($store[$i]->nivel_act_kms_feb_mn);
                $entityMN->setNivelActKmsMar($store[$i]->nivel_act_kms_mar_mn);
                $entityMN->setNivelActKmsAbr($store[$i]->nivel_act_kms_abr_mn);
                $entityMN->setNivelActKmsMay($store[$i]->nivel_act_kms_may_mn);
                $entityMN->setNivelActKmsJun($store[$i]->nivel_act_kms_jun_mn);
                $entityMN->setNivelActKmsJul($store[$i]->nivel_act_kms_jul_mn);
                $entityMN->setNivelActKmsAgo($store[$i]->nivel_act_kms_ago_mn);
                $entityMN->setNivelActKmsSep($store[$i]->nivel_act_kms_sep_mn);
                $entityMN->setNivelActKmsOct($store[$i]->nivel_act_kms_oct_mn);
                $entityMN->setNivelActKmsNov($store[$i]->nivel_act_kms_nov_mn);
                $entityMN->setNivelActKmsDic($store[$i]->nivel_act_kms_dic_mn);
                $entityMN->setNivelActKmsTotal($store[$i]->nivel_act_kms_total_anno_mn);
                $entityMN->setLubricanteEne($store[$i]->lubricante_ene);
                $entityMN->setLubricanteFeb($store[$i]->lubricante_feb);
                $entityMN->setLubricanteMar($store[$i]->lubricante_mar);
                $entityMN->setLubricanteAbr($store[$i]->lubricante_abr);
                $entityMN->setLubricanteMay($store[$i]->lubricante_may);
                $entityMN->setLubricanteJun($store[$i]->lubricante_jun);
                $entityMN->setLubricanteJul($store[$i]->lubricante_jul);
                $entityMN->setLubricanteAgo($store[$i]->lubricante_ago);
                $entityMN->setLubricanteSep($store[$i]->lubricante_sep);
                $entityMN->setLubricanteOct($store[$i]->lubricante_oct);
                $entityMN->setLubricanteNov($store[$i]->lubricante_nov);
                $entityMN->setLubricanteDic($store[$i]->lubricante_dic);
                $entityMN->setLubricanteTotal($store[$i]->lubricante_total_anno);
                $entityMN->setLiquidoFrenoEne($store[$i]->liquido_freno_ene);
                $entityMN->setLiquidoFrenoFeb($store[$i]->liquido_freno_feb);
                $entityMN->setLiquidoFrenoMar($store[$i]->liquido_freno_mar);
                $entityMN->setLiquidoFrenoAbr($store[$i]->liquido_freno_abr);
                $entityMN->setLiquidoFrenoMay($store[$i]->liquido_freno_may);
                $entityMN->setLiquidoFrenoJun($store[$i]->liquido_freno_jun);
                $entityMN->setLiquidoFrenoJul($store[$i]->liquido_freno_jul);
                $entityMN->setLiquidoFrenoAgo($store[$i]->liquido_freno_ago);
                $entityMN->setLiquidoFrenoSep($store[$i]->liquido_freno_sep);
                $entityMN->setLiquidoFrenoOct($store[$i]->liquido_freno_oct);
                $entityMN->setLiquidoFrenoNov($store[$i]->liquido_freno_nov);
                $entityMN->setLiquidoFrenoDic($store[$i]->liquido_freno_dic);
                $entityMN->setLiquidoFrenoTotal($store[$i]->liquido_freno_total_anno);

                try {
                    $em->persist($entityMN);
                    $em->flush();
                } catch (\Exception $ex) {
                    return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
                }
            }

            $entityCUC = $em->getRepository('PortadoresBundle:PlanificacionCombustibleCuc')->findOneBy(array(
                'vehiculoid' => $store[$i]->vehiculoid,
                'anno' => $store[$i]->anno,
                'visible' => true
            ));
            /**
             * @var PlanificacionCombustibleCuc $entity
             */
            if (!is_null($entityCUC)) {

                $entityCUC->setVehiculoid($vehiculo);
                $entityCUC->setPrecioCombustible($vehiculo->getNtipoCombustibleid()->getPrecio());
                $entityCUC->setCombustibleLitrosEne($store[$i]->combustible_litros_ene_cuc);
                $entityCUC->setCombustibleLitrosFeb($store[$i]->combustible_litros_feb_cuc);
                $entityCUC->setCombustibleLitrosMar($store[$i]->combustible_litros_mar_cuc);
                $entityCUC->setCombustibleLitrosAbr($store[$i]->combustible_litros_abr_cuc);
                $entityCUC->setCombustibleLitrosMay($store[$i]->combustible_litros_may_cuc);
                $entityCUC->setCombustibleLitrosJun($store[$i]->combustible_litros_jun_cuc);
                $entityCUC->setCombustibleLitrosJul($store[$i]->combustible_litros_jul_cuc);
                $entityCUC->setCombustibleLitrosAgo($store[$i]->combustible_litros_ago_cuc);
                $entityCUC->setCombustibleLitrosSep($store[$i]->combustible_litros_sep_cuc);
                $entityCUC->setCombustibleLitrosOct($store[$i]->combustible_litros_oct_cuc);
                $entityCUC->setCombustibleLitrosNov($store[$i]->combustible_litros_nov_cuc);
                $entityCUC->setCombustibleLitrosDic($store[$i]->combustible_litros_dic_cuc);
                $entityCUC->setCombustibleLitrosTotal($store[$i]->combustible_litros_total_anno_cuc);
                $entityCUC->setNivelActKmsEne($store[$i]->nivel_act_kms_ene_cuc);
                $entityCUC->setNivelActKmsFeb($store[$i]->nivel_act_kms_feb_cuc);
                $entityCUC->setNivelActKmsMar($store[$i]->nivel_act_kms_mar_cuc);
                $entityCUC->setNivelActKmsAbr($store[$i]->nivel_act_kms_abr_cuc);
                $entityCUC->setNivelActKmsMay($store[$i]->nivel_act_kms_may_cuc);
                $entityCUC->setNivelActKmsJun($store[$i]->nivel_act_kms_jun_cuc);
                $entityCUC->setNivelActKmsJul($store[$i]->nivel_act_kms_jul_cuc);
                $entityCUC->setNivelActKmsAgo($store[$i]->nivel_act_kms_ago_cuc);
                $entityCUC->setNivelActKmsSep($store[$i]->nivel_act_kms_sep_cuc);
                $entityCUC->setNivelActKmsOct($store[$i]->nivel_act_kms_oct_cuc);
                $entityCUC->setNivelActKmsNov($store[$i]->nivel_act_kms_nov_cuc);
                $entityCUC->setNivelActKmsDic($store[$i]->nivel_act_kms_dic_cuc);
                $entityCUC->setNivelActKmsTotal($store[$i]->nivel_act_kms_total_anno_cuc);
                $entityCUC->setLubricanteEne($store[$i]->lubricante_ene);
                $entityCUC->setLubricanteFeb($store[$i]->lubricante_feb);
                $entityCUC->setLubricanteMar($store[$i]->lubricante_mar);
                $entityCUC->setLubricanteAbr($store[$i]->lubricante_abr);
                $entityCUC->setLubricanteMay($store[$i]->lubricante_may);
                $entityCUC->setLubricanteJun($store[$i]->lubricante_jun);
                $entityCUC->setLubricanteJul($store[$i]->lubricante_jul);
                $entityCUC->setLubricanteAgo($store[$i]->lubricante_ago);
                $entityCUC->setLubricanteSep($store[$i]->lubricante_sep);
                $entityCUC->setLubricanteOct($store[$i]->lubricante_oct);
                $entityCUC->setLubricanteNov($store[$i]->lubricante_nov);
                $entityCUC->setLubricanteDic($store[$i]->lubricante_dic);
                $entityCUC->setLubricanteTotal($store[$i]->lubricante_total_anno);
                $entityCUC->setLiquidoFrenoEne($store[$i]->liquido_freno_ene);
                $entityCUC->setLiquidoFrenoFeb($store[$i]->liquido_freno_feb);
                $entityCUC->setLiquidoFrenoMar($store[$i]->liquido_freno_mar);
                $entityCUC->setLiquidoFrenoAbr($store[$i]->liquido_freno_abr);
                $entityCUC->setLiquidoFrenoMay($store[$i]->liquido_freno_may);
                $entityCUC->setLiquidoFrenoJun($store[$i]->liquido_freno_jun);
                $entityCUC->setLiquidoFrenoJul($store[$i]->liquido_freno_jul);
                $entityCUC->setLiquidoFrenoAgo($store[$i]->liquido_freno_ago);
                $entityCUC->setLiquidoFrenoSep($store[$i]->liquido_freno_sep);
                $entityCUC->setLiquidoFrenoOct($store[$i]->liquido_freno_oct);
                $entityCUC->setLiquidoFrenoNov($store[$i]->liquido_freno_nov);
                $entityCUC->setLiquidoFrenoDic($store[$i]->liquido_freno_dic);
                $entityCUC->setLiquidoFrenoTotal($store[$i]->liquido_freno_total_anno);

                try {
                    $em->persist($entityCUC);
                    $em->flush();
                } catch (\Exception $ex) {
                    return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
                }
            }
        }
        $response = new JsonResponse();
        $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Planificación modificada con éxito.'));
        return $response;
    }

    public function delAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $ids = $request->get('id');
        $flag = false;
        for ($i = 0; $i < count($ids); $i++) {
            $entityMN = $em->getRepository('PortadoresBundle:PlanificacionCombustible')->find($ids[$i]);

            $entityCUC = $em->getRepository('PortadoresBundle:PlanificacionCombustibleCuc')->findOneBy(array(
                'vehiculoid' => $entityMN->getVehiculoid()->getId(),
                'anno' => $entityMN->getAnno(),
                'visible' => true
            ));

            if (!$entityMN->getAprobada()) {
                $entityMN->setVisible(false);
                $entityCUC->setVisible(false);
                $em->persist($entityMN);
                $em->persist($entityCUC);
            } else {
                $flag = true;
            }
        }
        try {
            $em->flush();
            if ($flag)
                return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Planificación eliminada con éxito, las planificaciones aprobadas no se pueden eliminar.'));
            else
                return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Planificación eliminada con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function aprobarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $store = json_decode($request->get('store'));

        for ($i = 0; $i < count($store); $i++) {
            $entityMN = $em->getRepository('PortadoresBundle:PlanificacionCombustible')->find($store[$i]->id);

            $entityCUC = $em->getRepository('PortadoresBundle:PlanificacionCombustibleCuc')->findOneBy(array(
                'vehiculoid' => $entityMN->getVehiculoid()->getId(),
                'anno' => $entityMN->getAnno(),
                'visible' => true
            ));

            $entityMN->setAprobada(true);
            $entityCUC->setAprobada(true);

            try {
                $em->persist($entityMN);
                $em->persist($entityCUC);
                $em->flush();
            } catch (\Exception $ex) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
            }
        }
        return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Planificación aprobada con éxito.'));
    }

    public function desaprobarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $store = json_decode($request->get('store'));
        for ($i = 0; $i < count($store); $i++) {
            $entityMN = $em->getRepository('PortadoresBundle:PlanificacionCombustible')->find($store[$i]->id);

            $entityCUC = $em->getRepository('PortadoresBundle:PlanificacionCombustibleCuc')->findOneBy(array(
                'vehiculoid' => $entityMN->getVehiculoid()->getId(),
                'anno' => $entityMN->getAnno(),
                'visible' => true
            ));

            $entityMN->setAprobada(false);
            $entityCUC->setAprobada(false);

            try {
                $em->persist($entityMN);
                $em->persist($entityCUC);
                $em->flush();
            } catch (\Exception $ex) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
            }
        }
        return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Planificaciones desaprobada con éxito.'));
    }

    public function printAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $nunidadid = $request->get('nunidadid');
        $_anno = trim($request->get('anno'));
        $_tipoCombustible = $_tipoCombustible = ($request->get('tipo_combustibleid') !== '-1') ? trim($request->get('tipo_combustibleid')) : null;

        $_opcion = trim($request->get('opcion'));

        $mes = FechaUtil::getNombreMes($_opcion);

        $field = $mes ? 'Mes:' : '';

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $planificacion = $em->getRepository('PortadoresBundle:PlanificacionCombustible')->buscarPlanificacionCombustible('', $_anno, $_tipoCombustible, $_unidades, true);

        if (count($planificacion) == 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'No existen planificaciones aprobadas.'));

        $_html = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
        <html xmlns='http://www.w3.org/1999/xhtml'>
        <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <title>PLAN MENSUAL DE MOTORRECURSOS Y COMBUSTIBLES</title>
        <style>
           table {
                border:0 solid;
                border-radius:0;
                font-family: 'Arial', serif;
                font-size: 12px;
                border-collapse: collapse;
            }
            td{
                height: 10px;
                padding: 2px;
            }
        </style>
        </head>

        <body>
        <table cellspacing='0' cellpadding='5' border='1' width='100%'>
            <tr>
            <td colspan='12' style='text-align: left;border: none;'><strong>APROBADO:</strong> ______________________________  </td>
            </tr>
            <tr>
            <td colspan='12' style='text-align: left;border: none;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Director General Empresa (UEB)  </td>
            </tr>
            <tr>
            <td colspan='3' style='border: none;'></td>
            <td colspan='6' style='border: none;'><div style='text-align:center'><strong>PLAN MENSUAL DE MOTORRECURSOS Y COMBUSTIBLES</strong></div></td>
            <td colspan='3' style='border: none;'></td>
            </tr>
            <tr>
            <td colspan='4' style='border: none;'></td>
            <td colspan='2' style='border: none;'><div style='text-align:left'><strong>Mes: </strong>$mes</div></td>
            <td colspan='2' style='border: none;'><div style='text-align:right'><strong>Año: </strong>" . $planificacion[0]->getAnno() . "</div></td>
            <td colspan='4' style='border: none;'></td>
            </tr>
            <tr>
            <td style='text-align: center;'><strong>No.</strong></td>
            <td style='text-align: center;'><strong>Marca</strong></td>
            <td style='text-align: center;'><strong>Matrícula</strong></td>
            <td style='text-align: center;'><strong>Kms. MN</strong></td>
            <td style='text-align: center;'><strong>Kms. CUC</strong></td>
            <td style='text-align: center;'><strong>TOTAL</strong></td>
            <td style='text-align: center;'><strong>Tipo</strong></td>
            <td style='text-align: center;'><strong>Comb. MN</strong></td>
            <td style='text-align: center;'><strong>Comb. CUC</strong></td>
            <td style='text-align: center;'><strong>TOTAL</strong></td>
            <td style='text-align: center;'><strong>Lubricante</strong></td>
            <td style='text-align: center;'><strong>Líq. Freno</strong></td>
            </tr>
            ";

        $i = 0;
        foreach ($planificacion as $entityMN) {

            $entityCUC = $em->getRepository('PortadoresBundle:PlanificacionCombustibleCuc')->findOneBy(array(
                'vehiculoid' => $entityMN->getVehiculoid()->getId(),
                'anno' => $entityMN->getAnno(),
                'visible' => true
            ));

            $i++;
            $matricula = $entityMN->getVehiculoid()->getMatricula();
            $marca = $entityMN->getVehiculoid()->getNmodeloid()->getMarcaVehiculoid()->getNombre();
            $tipoCombustible = $entityMN->getVehiculoid()->getNtipoCombustibleid()->getNombre();
            switch ($_opcion) {
                case 1:

                    $_html .= "<tr>
                                <td style='text-align: center;'>" . $i . "</td>
                                <td>" . $marca . "</td>
                                <td>" . $matricula . "</td>
                                <td style='text-align: center;'>" . $entityMN->getNivelActKmsEne() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getNivelActKmsEne() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getNivelActKmsEne() + $entityCUC->getNivelActKmsEne()) . "</td>
                                <td style='text-align: center;'>" . $tipoCombustible . "</td>
                                <td style='text-align: center;'>" . $entityMN->getCombustibleLitrosEne() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getCombustibleLitrosEne() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getCombustibleLitrosEne() + $entityCUC->getCombustibleLitrosEne()) . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getLubricanteEne() . "</td>
                                <td style='text-align: center;'>" . $entityMN->getLiquidoFrenoEne() . "</td>
                                </tr>
                                ";
                    break;
                case 2:

                    $_html .= "<tr>
                                <td style='text-align: center;'>" . $i . "</td>
                                <td>" . $marca . "</td>
                                <td>" . $matricula . "</td>
                                <td style='text-align: center;'>" . $entityMN->getNivelActKmsFeb() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getNivelActKmsFeb() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getNivelActKmsFeb() + $entityCUC->getNivelActKmsFeb()) . "</td>
                                <td style='text-align: center;'>" . $tipoCombustible . "</td>
                                <td style='text-align: center;'>" . $entityMN->getCombustibleLitrosFeb() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getCombustibleLitrosFeb() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getCombustibleLitrosFeb() + $entityCUC->getCombustibleLitrosFeb()) . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getLubricanteFeb() . "</td>
                                <td style='text-align: center;'>" . $entityMN->getLiquidoFrenoFeb() . "</td>
                                </tr>
                                ";
                    break;
                case 3:
                    $_html .= "<tr>
                                <td style='text-align: center;'>" . $i . "</td>
                                <td>" . $marca . "</td>
                                <td>" . $matricula . "</td>
                                <td style='text-align: center;'>" . $entityMN->getNivelActKmsMar() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getNivelActKmsMar() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getNivelActKmsMar() + $entityCUC->getNivelActKmsMar()) . "</td>
                                <td style='text-align: center;'>" . $tipoCombustible . "</td>
                                <td style='text-align: center;'>" . $entityMN->getCombustibleLitrosMar() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getCombustibleLitrosMar() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getCombustibleLitrosMar() + $entityCUC->getCombustibleLitrosMar()) . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getLubricanteMar() . "</td>
                                <td style='text-align: center;'>" . $entityMN->getLiquidoFrenoMar() . "</td>
                                 </tr>
                                ";
                    break;
                case 4:
                    $_html .= "<tr>
                                <td style='text-align: center;'>" . $i . "</td>
                                <td>" . $marca . "</td>
                                <td>" . $matricula . "</td>
                                <td style='text-align: center;'>" . $entityMN->getNivelActKmsAbr() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getNivelActKmsAbr() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getNivelActKmsAbr() + $entityCUC->getNivelActKmsAbr()) . "</td>
                                <td style='text-align: center;'>" . $tipoCombustible . "</td>
                                <td style='text-align: center;'>" . $entityMN->getCombustibleLitrosAbr() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getCombustibleLitrosAbr() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getCombustibleLitrosAbr() + $entityCUC->getCombustibleLitrosAbr()) . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getLubricanteAbr() . "</td>
                                <td style='text-align: center;'>" . $entityMN->getLiquidoFrenoAbr() . "</td>
                                </tr>
                                ";
                    break;
                case 5:
                    $_html .= "<tr>
                                <td style='text-align: center;'>" . $i . "</td>
                                <td>" . $marca . "</td>
                                <td>" . $matricula . "</td>
                                <td style='text-align: center;'>" . $entityMN->getNivelActKmsMay() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getNivelActKmsMay() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getNivelActKmsMay() + $entityCUC->getNivelActKmsMay()) . "</td>
                                <td style='text-align: center;'>" . $tipoCombustible . "</td>
                                <td style='text-align: center;'>" . $entityMN->getCombustibleLitrosMay() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getCombustibleLitrosMay() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getCombustibleLitrosMay() + $entityCUC->getCombustibleLitrosMay()) . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getLubricanteMay() . "</td>
                                <td style='text-align: center;'>" . $entityMN->getLiquidoFrenoMay() . "</td>
                                 </tr>
                                ";
                    break;
                case 6:
                    $_html .= "<tr>
                                <td style='text-align: center;'>" . $i . "</td>
                                <td>" . $marca . "</td>
                                <td>" . $matricula . "</td>
                                <td style='text-align: center;'>" . $entityMN->getNivelActKmsJun() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getNivelActKmsJun() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getNivelActKmsJun() + $entityCUC->getNivelActKmsJun()) . "</td>
                                <td style='text-align: center;'>" . $tipoCombustible . "</td>
                                <td style='text-align: center;'>" . $entityMN->getCombustibleLitrosJun() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getCombustibleLitrosJun() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getCombustibleLitrosJun() + $entityCUC->getCombustibleLitrosJun()) . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getLubricanteJun() . "</td>
                                <td style='text-align: center;'>" . $entityMN->getLiquidoFrenoJun() . "</td>
                                </tr>
                                ";
                    break;
                case 7:
                    $_html .= "<tr>
                                <td style='text-align: center;'>" . $i . "</td>
                                <td>" . $marca . "</td>
                                <td>" . $matricula . "</td>
                                <td style='text-align: center;'>" . $entityMN->getNivelActKmsJul() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getNivelActKmsJul() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getNivelActKmsJul() + $entityCUC->getNivelActKmsJul()) . "</td>
                                <td style='text-align: center;'>" . $tipoCombustible . "</td>
                                <td style='text-align: center;'>" . $entityMN->getCombustibleLitrosJul() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getCombustibleLitrosJul() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getCombustibleLitrosJul() + $entityCUC->getCombustibleLitrosJul()) . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getLubricanteJul() . "</td>
                                <td style='text-align: center;'>" . $entityMN->getLiquidoFrenoJul() . "</td>
                                 </tr>
                                ";
                    break;
                case 8:
                    $_html .= "<tr>
                                <td style='text-align: center;'>" . $i . "</td>
                                <td>" . $marca . "</td>
                                <td>" . $matricula . "</td>
                                <td style='text-align: center;'>" . $entityMN->getNivelActKmsAgo() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getNivelActKmsAgo() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getNivelActKmsAgo() + $entityCUC->getNivelActKmsAgo()) . "</td>
                                <td style='text-align: center;'>" . $tipoCombustible . "</td>
                                <td style='text-align: center;'>" . $entityMN->getCombustibleLitrosAgo() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getCombustibleLitrosAgo() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getCombustibleLitrosAgo() + $entityCUC->getCombustibleLitrosAgo()) . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getLubricanteAgo() . "</td>
                                <td style='text-align: center;'>" . $entityMN->getLiquidoFrenoAgo() . "</td>
                                </tr>
                                ";
                    break;
                case 9:
                    $_html .= "<tr>
                                <td style='text-align: center;'>" . $i . "</td>
                                <td>" . $marca . "</td>
                                <td>" . $matricula . "</td>
                                <td style='text-align: center;'>" . $entityMN->getNivelActKmsSep() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getNivelActKmsSep() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getNivelActKmsSep() + $entityCUC->getNivelActKmsSep()) . "</td>
                                <td style='text-align: center;'>" . $tipoCombustible . "</td>
                                <td style='text-align: center;'>" . $entityMN->getCombustibleLitrosSep() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getCombustibleLitrosSep() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getCombustibleLitrosSep() + $entityCUC->getCombustibleLitrosSep()) . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getLubricanteSep() . "</td>
                                <td style='text-align: center;'>" . $entityMN->getLiquidoFrenoSep() . "</td>
                                 </tr>
                                ";
                    break;
                case 10:
                    $_html .= "<tr>
                                <td style='text-align: center;'>" . $i . "</td>
                                <td>" . $marca . "</td>
                                <td>" . $matricula . "</td>
                                <td style='text-align: center;'>" . $entityMN->getNivelActKmsOct() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getNivelActKmsOct() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getNivelActKmsOct() + $entityCUC->getNivelActKmsOct()) . "</td>
                                <td style='text-align: center;'>" . $tipoCombustible . "</td>
                                <td style='text-align: center;'>" . $entityMN->getCombustibleLitrosOct() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getCombustibleLitrosOct() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getCombustibleLitrosOct() + $entityCUC->getCombustibleLitrosOct()) . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getLubricanteOct() . "</td>
                                <td style='text-align: center;'>" . $entityMN->getLiquidoFrenoOct() . "</td>
                                </tr>
                                ";
                    break;
                case 11:
                    $_html .= "<tr>
                                <td style='text-align: center;'>" . $i . "</td>
                                <td>" . $marca . "</td>
                                <td>" . $matricula . "</td>
                                <td style='text-align: center;'>" . $entityMN->getNivelActKmsNov() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getNivelActKmsNov() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getNivelActKmsNov() + $entityCUC->getNivelActKmsNov()) . "</td>
                                <td style='text-align: center;'>" . $tipoCombustible . "</td>
                                <td style='text-align: center;'>" . $entityMN->getCombustibleLitrosNov() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getCombustibleLitrosNov() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getCombustibleLitrosNov() + $entityCUC->getCombustibleLitrosNov()) . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getLubricanteNov() . "</td>
                                <td style='text-align: center;'>" . $entityMN->getLiquidoFrenoNov() . "</td>
                                </tr>
                                ";
                    break;
                case 12:
                    $_html .= "<tr>
                                <td style='text-align: center;'>" . $i . "</td>
                                <td>" . $marca . "</td>
                                <td>" . $matricula . "</td>
                                <td style='text-align: center;'>" . $entityMN->getNivelActKmsDic() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getNivelActKmsDic() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getNivelActKmsDic() + $entityCUC->getNivelActKmsDic()) . "</td>
                                <td style='text-align: center;'>" . $tipoCombustible . "</td>
                                <td style='text-align: center;'>" . $entityMN->getCombustibleLitrosDic() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getCombustibleLitrosDic() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getCombustibleLitrosDic() + $entityCUC->getCombustibleLitrosDic()) . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getLubricanteDic() . "</td>
                                <td style='text-align: center;'>" . $entityMN->getLiquidoFrenoDic() . "</td>
                                </tr>
                                ";
                    break;
                default:
                    $combustible_litros_total_mn = $entityMN->getCombustibleLitrosEne() + $entityMN->getCombustibleLitrosFeb()
                        + $entityMN->getCombustibleLitrosMar() + $entityMN->getCombustibleLitrosAbr() + $entityMN->getCombustibleLitrosMay()
                        + $entityMN->getCombustibleLitrosJun() + $entityMN->getCombustibleLitrosJul() + $entityMN->getCombustibleLitrosAgo()
                        + $entityMN->getCombustibleLitrosSep() + $entityMN->getCombustibleLitrosOct() + $entityMN->getCombustibleLitrosNov()
                        + $entityMN->getCombustibleLitrosDic();
                    $combustible_litros_total_cuc = $entityCUC->getCombustibleLitrosEne() + $entityCUC->getCombustibleLitrosFeb()
                        + $entityCUC->getCombustibleLitrosMar() + $entityCUC->getCombustibleLitrosAbr() + $entityCUC->getCombustibleLitrosMay()
                        + $entityCUC->getCombustibleLitrosJun() + $entityCUC->getCombustibleLitrosJul() + $entityCUC->getCombustibleLitrosAgo()
                        + $entityCUC->getCombustibleLitrosSep() + $entityCUC->getCombustibleLitrosOct() + $entityCUC->getCombustibleLitrosNov()
                        + $entityCUC->getCombustibleLitrosDic();

                    $nivel_act_kms_total_mn = $entityMN->getNivelActKmsEne() + $entityMN->getNivelActKmsFeb() + $entityMN->getNivelActKmsMar()
                        + $entityMN->getNivelActKmsAbr() + $entityMN->getNivelActKmsMay() + $entityMN->getNivelActKmsJun() + $entityMN->getNivelActKmsJul()
                        + $entityMN->getNivelActKmsAgo() + $entityMN->getNivelActKmsSep() + $entityMN->getNivelActKmsOct() + $entityMN->getNivelActKmsNov()
                        + $entityMN->getNivelActKmsDic();
                    $nivel_act_kms_total_cuc = $entityCUC->getNivelActKmsEne() + $entityCUC->getNivelActKmsFeb() + $entityCUC->getNivelActKmsMar()
                        + $entityCUC->getNivelActKmsAbr() + $entityCUC->getNivelActKmsMay() + $entityCUC->getNivelActKmsJun() + $entityCUC->getNivelActKmsJul()
                        + $entityCUC->getNivelActKmsAgo() + $entityCUC->getNivelActKmsSep() + $entityCUC->getNivelActKmsOct() + $entityCUC->getNivelActKmsNov()
                        + $entityCUC->getNivelActKmsDic();

                    $lubricante_total = $entityMN->getLubricanteEne() + $entityMN->getLubricanteFeb() + $entityMN->getLubricanteMar()
                        + $entityMN->getLubricanteAbr() + $entityMN->getLubricanteMay() + $entityMN->getLubricanteJun() + $entityMN->getLubricanteJul()
                        + $entityMN->getLubricanteAgo() + $entityMN->getLubricanteSep() + $entityMN->getLubricanteOct() + $entityMN->getLubricanteNov()
                        + $entityMN->getLubricanteDic();
                    $liquido_freno_total = $entityMN->getLiquidoFrenoEne() + $entityMN->getLiquidoFrenoFeb() + $entityMN->getLiquidoFrenoMar()
                        + $entityMN->getLiquidoFrenoAbr() + $entityMN->getLiquidoFrenoMay() + $entityMN->getLiquidoFrenoJun() + $entityMN->getLiquidoFrenoJul()
                        + $entityMN->getLiquidoFrenoAgo() + $entityMN->getLiquidoFrenoSep() + $entityMN->getLiquidoFrenoOct() + $entityMN->getLiquidoFrenoNov()
                        + $entityMN->getLiquidoFrenoDic();

                    $_html .= "<tr>
                                <td style='text-align: center;'>" . $i . "</td>
                                <td>" . $marca . "</td>
                                <td>" . $matricula . "</td>
                                <td style='text-align: center;'>" . $nivel_act_kms_total_mn . "</td>
                                <td style='text-align: center;'>" . $nivel_act_kms_total_cuc . "</td>
                                <td style='text-align: center;'>" . ($nivel_act_kms_total_mn + $nivel_act_kms_total_cuc) . "</td>
                                <td style='text-align: center;'>" . $tipoCombustible . "</td>
                                <td style='text-align: center;'>" . $combustible_litros_total_mn . "</td>
                                <td style='text-align: center;'>" . $combustible_litros_total_cuc . "</td>
                                <td style='text-align: center;'>" . ($combustible_litros_total_mn + $combustible_litros_total_cuc) . "</td>
                                <td style='text-align: center;'>" . $lubricante_total . "</td>
                                <td style='text-align: center;'>" . $liquido_freno_total . "</td>
                                </tr>
                                ";
                    break;
            }
        }

        $_html .= "<tr>
                    <td style='text-align: left; border-right: none; border-bottom: none;'></td>
                    <td colspan='5' style='text-align: left; border-left: none; border-right: none; border-bottom: none;'><strong>Tec. A de Producción</strong></td>
                    <td colspan='5' style='text-align: right; border-left: none; border-right: none; border-bottom: none'><strong>Director Producción</strong></td>
                    <td style='text-align: right; border-left: none; border-bottom: none'></td>
                    </tr>
                    <tr>
                    <td style='text-align: left; border-right: none; border-top: none;'></td>
                    <td colspan='5' style='text-align: left; border-left: none; border-right: none; border-top: none;'></td>
                    <td colspan='5' style='text-align: right; border-left: none; border-right: none; border-top: none'></td>
                    <td style='text-align: right; border-left: none; border-top: none'></td>
                    </tr>
                    ";

        $_html .= "</table>
        </body>
        </html>";

        return new Response(json_encode(array('success' => true, 'html' => $_html)));
    }

    public function printOldAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $nunidadid = $request->get('nunidadid');
        $_anno = trim($request->get('anno'));
        $_tipoCombustible = $_tipoCombustible = ($request->get('tipo_combustibleid') !== '-1') ? trim($request->get('tipo_combustibleid')) : null;

        $_opcion = trim($request->get('opcion'));

        $mes = FechaUtil::getNombreMes($_opcion);

        $field = $mes ? 'Mes:' : '';

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $planificacion = $em->getRepository('PortadoresBundle:PlanificacionCombustible')->buscarPlanificacionCombustible('', $_anno, $_tipoCombustible, $_unidades, true);
        $tipoCombustible = $em->getRepository('PortadoresBundle:TipoCombustible')->find($_tipoCombustible);
        $comb_name = $tipoCombustible !== null ? $tipoCombustible->getNombre() : 'Todos';

        if (count($planificacion) == 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'No existen planificaciones aprobadas.'));

        $combP = 0;
        $combmnt = 0;
        $combcuct = 0;
        $kmsmnt = 0;
        $kmscuct = 0;
        $lubt = 0;
        $liqt = 0;

        $_html = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
        <html xmlns='http://www.w3.org/1999/xhtml'>
        <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <title>PLAN DE MOTORRECURSOS Y COMBUSTIBLES</title>
        <style>
           table {
                border:0 solid;
                border-radius:0;
                font-family: 'Arial', serif;
                font-size: 12px;
                border-collapse: collapse;
            }
            td{
                height: 10px;
                padding: 2px;
            }
        </style>
        </head>

        <body>
        <table cellspacing='0' cellpadding='5' border='1' width='100%'>
            <tr>
                <td colspan='14' style='text-align: left;border: none; padding: 0;'><img src='../../assets/img/PNG/logo.png' height='44px' width='160'></td>
            </tr>
            <tr>
            <td colspan='3' style='border: none;'></td>
            <td colspan='8' style='border: none;'><div style='text-align:center; font-size: 16px;'><strong>PLAN DE MOTORRECURSOS Y COMBUSTIBLES</strong></div></td>
            <td colspan='3' style='border: none;'></td>
            </tr>
            <tr>
            <td colspan='3' style='border: none;'></td>
            <td colspan='3' style='border: none;'><div style='text-align:left; font-size: 14px;'><strong>Combustible: </strong>" . $comb_name . "</div></td>
            <td colspan='2' style='border: none;'><div style='text-align:left; font-size: 14px;'><strong> $field </strong>$mes</div></td>
            <td colspan='3' style='border: none;'><div style='text-align:right; font-size: 14px;'><strong>Año: </strong>" . $planificacion[0]->getAnno() . "</div></td>
            <td colspan='3' style='border: none;'></td>
            </tr>
            <tr>
            <td style='text-align: center;'><strong>No.</strong></td>
            <td style='text-align: center;'><strong>Matrícula</strong></td>
            <td style='text-align: center;'><strong>Marca/Modelo</strong></td>
             <td style='text-align: center;'><strong>ASIGN(LT)MEP</strong></td>
            <td style='text-align: center;'><strong>ASIGN(LT)GAE</strong></td>
            <td style='text-align: center;'><strong>TOTAL(LT)</strong></td>
            <td style='text-align: center;'><strong>TOTAL($)</strong></td>
            <td style='text-align: center;'><strong>KMS/LTS</strong></td>
            <td style='text-align: center;'><strong>Kms. MEP</strong></td>
            <td style='text-align: center;'><strong>Kms. GAE</strong></td>
            <td style='text-align: center;'><strong>TOTAL</strong></td>
            <td style='text-align: center;'><strong>Indice</strong></td>
            <td style='text-align: center;'><strong>Lubricante</strong></td>
            <td style='text-align: center;'><strong>Líq. Freno</strong></td>
            </tr>
            ";

        $i = 0;
        foreach ($planificacion as $entityMN) {

            $entityCUC = $em->getRepository('PortadoresBundle:PlanificacionCombustibleCuc')->findOneBy(array(
                'vehiculoid' => $entityMN->getVehiculoid()->getId(),
                'anno' => $entityMN->getAnno(),
                'visible' => true
            ));

            $i++;
            $matricula = $entityMN->getVehiculoid()->getMatricula();
            $marca = $entityMN->getVehiculoid()->getNmodeloid()->getMarcaVehiculoid()->getNombre() . '/' . $entityMN->getVehiculoid()->getNmodeloid()->getNombre();
            $norma = round($entityMN->getVehiculoid()->getNorma(), 2);
            switch ($_opcion) {
                case 1:
                    $combP += $entityMN->getPrecioCombustible() * ($entityMN->getCombustibleLitrosEne() + $entityCUC->getCombustibleLitrosEne());
                    $combmnt += $entityMN->getCombustibleLitrosEne();
                    $combcuct += $entityCUC->getCombustibleLitrosEne();
                    $kmsmnt += $entityMN->getNivelActKmsEne();
                    $kmscuct += $entityCUC->getNivelActKmsEne();
                    $lubt += $entityCUC->getLubricanteEne();
                    $liqt += $entityMN->getLiquidoFrenoEne();
                    $indice = ($entityMN->getNivelActKmsEne() + $entityCUC->getNivelActKmsEne() == 0) ? 0 : round(($entityMN->getCombustibleLitrosEne() + $entityCUC->getCombustibleLitrosEne()) / ($entityMN->getNivelActKmsEne() + $entityCUC->getNivelActKmsEne()), 4);
                    $_html .= "<tr>
                                <td style='text-align: center;'>" . $i . "</td>
                                <td>" . $matricula . "</td>
                                <td>" . $marca . "</td>
                                <td style='text-align: center;'>" . $entityMN->getCombustibleLitrosEne() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getCombustibleLitrosEne() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getCombustibleLitrosEne() + $entityCUC->getCombustibleLitrosEne()) . "</td>
                                <td style='text-align: center;'>" . $entityMN->getPrecioCombustible() * ($entityMN->getCombustibleLitrosEne() + $entityCUC->getCombustibleLitrosEne()) . "</td>
                                <td style='text-align: center;'>" . $norma . "</td>
                                <td style='text-align: center;'>" . $entityMN->getNivelActKmsEne() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getNivelActKmsEne() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getNivelActKmsEne() + $entityCUC->getNivelActKmsEne()) . "</td>
                                <td style='text-align: center;'>" . $indice . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getLubricanteEne() . "</td>
                                <td style='text-align: center;'>" . $entityMN->getLiquidoFrenoEne() . "</td>
                                </tr>
                                ";
                    break;
                case 2:
                    $combP += $entityMN->getPrecioCombustible() * ($entityMN->getCombustibleLitrosFeb() + $entityCUC->getCombustibleLitrosFeb());
                    $combmnt += $entityMN->getCombustibleLitrosFeb();
                    $combcuct += $entityCUC->getCombustibleLitrosFeb();
                    $kmsmnt += $entityMN->getNivelActKmsFeb();
                    $kmscuct += $entityCUC->getNivelActKmsFeb();
                    $lubt += $entityCUC->getLubricanteFeb();
                    $liqt += $entityMN->getLiquidoFrenoFeb();
                    $indice = (($entityMN->getNivelActKmsFeb() + $entityCUC->getNivelActKmsFeb()) == 0) ? 0 : round(($entityMN->getCombustibleLitrosFeb() + $entityCUC->getCombustibleLitrosFeb()) / ($entityMN->getNivelActKmsFeb() + $entityCUC->getNivelActKmsFeb()), 4);
                    $_html .= "<tr>
                                <td style='text-align: center;'>" . $i . "</td>
                                <td>" . $matricula . "</td>
                                <td>" . $marca . "</td>
                                <td style='text-align: center;'>" . $entityMN->getCombustibleLitrosFeb() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getCombustibleLitrosFeb() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getCombustibleLitrosFeb() + $entityCUC->getCombustibleLitrosFeb()) . "</td>
                                <td style='text-align: center;'>" . $entityMN->getPrecioCombustible() * ($entityMN->getCombustibleLitrosFeb() + $entityCUC->getCombustibleLitrosFeb()) . "</td>
                                <td style='text-align: center;'>" . $norma . "</td>
                                <td style='text-align: center;'>" . $entityMN->getNivelActKmsFeb() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getNivelActKmsFeb() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getNivelActKmsFeb() + $entityCUC->getNivelActKmsFeb()) . "</td>
                                <td style='text-align: center;'>" . $indice . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getLubricanteFeb() . "</td>
                                <td style='text-align: center;'>" . $entityMN->getLiquidoFrenoFeb() . "</td>
                                </tr>
                                ";
                    break;
                case 3:
                    $combP += $entityMN->getPrecioCombustible() * ($entityMN->getCombustibleLitrosMar() + $entityCUC->getCombustibleLitrosMar());
                    $combmnt += $entityMN->getCombustibleLitrosMar();
                    $combcuct += $entityCUC->getCombustibleLitrosMar();
                    $kmsmnt += $entityMN->getNivelActKmsMar();
                    $kmscuct += $entityCUC->getNivelActKmsMar();
                    $lubt += $entityCUC->getLubricanteMar();
                    $liqt += $entityMN->getLiquidoFrenoMar();
                    $indice = (($entityMN->getNivelActKmsMar() + $entityCUC->getNivelActKmsMar()) == 0) ? 0 : round(($entityMN->getCombustibleLitrosMar() + $entityCUC->getCombustibleLitrosMar()) / ($entityMN->getNivelActKmsMar() + $entityCUC->getNivelActKmsMar()), 4);
                    $_html .= "<tr>
                                <td style='text-align: center;'>" . $i . "</td>
                                <td>" . $matricula . "</td>
                                <td>" . $marca . "</td>
                                <td style='text-align: center;'>" . $entityMN->getCombustibleLitrosMar() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getCombustibleLitrosMar() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getCombustibleLitrosMar() + $entityCUC->getCombustibleLitrosMar()) . "</td>
                                <td style='text-align: center;'>" . $entityMN->getPrecioCombustible() * ($entityMN->getCombustibleLitrosMar() + $entityCUC->getCombustibleLitrosMar()) . "</td>
                                <td style='text-align: center;'>" . $norma . "</td>
                                <td style='text-align: center;'>" . $entityMN->getNivelActKmsMar() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getNivelActKmsMar() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getNivelActKmsMar() + $entityCUC->getNivelActKmsMar()) . "</td>
                                <td style='text-align: center;'>" . $indice . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getLubricanteMar() . "</td>
                                <td style='text-align: center;'>" . $entityMN->getLiquidoFrenoMar() . "</td>
                                 </tr>
                                ";
                    break;
                case 4:
                    $combP += $entityMN->getPrecioCombustible() * ($entityMN->getCombustibleLitrosAbr() + $entityCUC->getCombustibleLitrosAbr());
                    $combmnt += $entityMN->getCombustibleLitrosAbr();
                    $combcuct += $entityCUC->getCombustibleLitrosAbr();
                    $kmsmnt += $entityMN->getNivelActKmsAbr();
                    $kmscuct += $entityCUC->getNivelActKmsAbr();
                    $lubt += $entityCUC->getLubricanteAbr();
                    $liqt += $entityMN->getLiquidoFrenoAbr();
                    $indice = (($entityMN->getNivelActKmsAbr() + $entityCUC->getNivelActKmsAbr()) == 0) ? 0 : round(($entityMN->getCombustibleLitrosAbr() + $entityCUC->getCombustibleLitrosAbr()) / ($entityMN->getNivelActKmsAbr() + $entityCUC->getNivelActKmsAbr()), 4);
                    $_html .= "<tr>
                                <td style='text-align: center;'>" . $i . "</td>
                                <td>" . $matricula . "</td>
                                <td>" . $marca . "</td>
                                <td style='text-align: center;'>" . $entityMN->getCombustibleLitrosAbr() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getCombustibleLitrosAbr() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getCombustibleLitrosAbr() + $entityCUC->getCombustibleLitrosAbr()) . "</td>
                                <td style='text-align: center;'>" . $entityMN->getPrecioCombustible() * ($entityMN->getCombustibleLitrosAbr() + $entityCUC->getCombustibleLitrosAbr()) . "</td>
                                <td style='text-align: center;'>" . $norma . "</td>
                                <td style='text-align: center;'>" . $entityMN->getNivelActKmsAbr() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getNivelActKmsAbr() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getNivelActKmsAbr() + $entityCUC->getNivelActKmsAbr()) . "</td>
                                <td style='text-align: center;'>" . $indice . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getLubricanteAbr() . "</td>
                                <td style='text-align: center;'>" . $entityMN->getLiquidoFrenoAbr() . "</td>
                                </tr>
                                ";
                    break;
                case 5:
                    $combP += $entityMN->getPrecioCombustible() * ($entityMN->getCombustibleLitrosMay() + $entityCUC->getCombustibleLitrosMay());
                    $combmnt += $entityMN->getCombustibleLitrosMay();
                    $combcuct += $entityCUC->getCombustibleLitrosMay();
                    $kmsmnt += $entityMN->getNivelActKmsMay();
                    $kmscuct += $entityCUC->getNivelActKmsMay();
                    $lubt += $entityCUC->getLubricanteMay();
                    $liqt += $entityMN->getLiquidoFrenoMay();
                    $indice = (($entityMN->getNivelActKmsMay() + $entityCUC->getNivelActKmsMay()) == 0) ? 0 : round(($entityMN->getCombustibleLitrosMay() + $entityCUC->getCombustibleLitrosMay()) / ($entityMN->getNivelActKmsMay() + $entityCUC->getNivelActKmsMay()), 4);
                    $_html .= "<tr>
                                <td style='text-align: center;'>" . $i . "</td>
                                <td>" . $matricula . "</td>
                                <td>" . $marca . "</td>
                                <td style='text-align: center;'>" . $entityMN->getCombustibleLitrosMay() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getCombustibleLitrosMay() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getCombustibleLitrosMay() + $entityCUC->getCombustibleLitrosMay()) . "</td>
                                <td style='text-align: center;'>" . $entityMN->getPrecioCombustible() * ($entityMN->getCombustibleLitrosMay() + $entityCUC->getCombustibleLitrosMay()) . "</td>
                                <td style='text-align: center;'>" . $norma . "</td>
                                <td style='text-align: center;'>" . $entityMN->getNivelActKmsMay() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getNivelActKmsMay() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getNivelActKmsMay() + $entityCUC->getNivelActKmsMay()) . "</td>
                                <td style='text-align: center;'>" . $indice . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getLubricanteMay() . "</td>
                                <td style='text-align: center;'>" . $entityMN->getLiquidoFrenoMay() . "</td>
                                 </tr>
                                ";
                    break;
                case 6:
                    $combP += $entityMN->getPrecioCombustible() * ($entityMN->getCombustibleLitrosJun() + $entityCUC->getCombustibleLitrosJun());
                    $combmnt += $entityMN->getCombustibleLitrosJun();
                    $combcuct += $entityCUC->getCombustibleLitrosJun();
                    $kmsmnt += $entityMN->getNivelActKmsJun();
                    $kmscuct += $entityCUC->getNivelActKmsJun();
                    $lubt += $entityCUC->getLubricanteJun();
                    $liqt += $entityMN->getLiquidoFrenoJun();
                    $indice = (($entityMN->getNivelActKmsJun() + $entityCUC->getNivelActKmsJun()) == 0) ? 0 : round(($entityMN->getCombustibleLitrosJun() + $entityCUC->getCombustibleLitrosJun()) / ($entityMN->getNivelActKmsJun() + $entityCUC->getNivelActKmsJun()), 4);
                    $_html .= "<tr>
                                <td style='text-align: center;'>" . $i . "</td>
                                <td>" . $matricula . "</td>
                                <td>" . $marca . "</td>
                                <td style='text-align: center;'>" . $entityMN->getCombustibleLitrosJun() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getCombustibleLitrosJun() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getCombustibleLitrosJun() + $entityCUC->getCombustibleLitrosJun()) . "</td>
                                <td style='text-align: center;'>" . $entityMN->getPrecioCombustible() * ($entityMN->getCombustibleLitrosJun() + $entityCUC->getCombustibleLitrosJun()) . "</td>
                                <td style='text-align: center;'>" . $norma . "</td>
                                <td style='text-align: center;'>" . $entityMN->getNivelActKmsJun() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getNivelActKmsJun() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getNivelActKmsJun() + $entityCUC->getNivelActKmsJun()) . "</td>
                                <td style='text-align: center;'>" . $indice . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getLubricanteJun() . "</td>
                                <td style='text-align: center;'>" . $entityMN->getLiquidoFrenoJun() . "</td>
                                </tr>
                                ";
                    break;
                case 7:
                    $combP += $entityMN->getPrecioCombustible() * ($entityMN->getCombustibleLitrosJul() + $entityCUC->getCombustibleLitrosJul());
                    $combmnt += $entityMN->getCombustibleLitrosJul();
                    $combcuct += $entityCUC->getCombustibleLitrosJul();
                    $kmsmnt += $entityMN->getNivelActKmsJul();
                    $kmscuct += $entityCUC->getNivelActKmsJul();
                    $lubt += $entityCUC->getLubricanteJun();
                    $liqt += $entityMN->getLiquidoFrenoJun();
                    $indice = (($entityMN->getNivelActKmsJul() + $entityCUC->getNivelActKmsJul()) == 0) ? 0 : round(($entityMN->getCombustibleLitrosJul() + $entityCUC->getCombustibleLitrosJul()) / ($entityMN->getNivelActKmsJul() + $entityCUC->getNivelActKmsJul()), 4);
                    $_html .= "<tr>
                                <td style='text-align: center;'>" . $i . "</td>
                                <td>" . $matricula . "</td>
                                <td>" . $marca . "</td>
                                <td style='text-align: center;'>" . $entityMN->getCombustibleLitrosJul() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getCombustibleLitrosJul() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getCombustibleLitrosJul() + $entityCUC->getCombustibleLitrosJul()) . "</td>
                                <td style='text-align: center;'>" . $entityMN->getPrecioCombustible() * ($entityMN->getCombustibleLitrosJul() + $entityCUC->getCombustibleLitrosJul()) . "</td>
                                <td style='text-align: center;'>" . $norma . "</td>
                                <td style='text-align: center;'>" . $entityMN->getNivelActKmsJul() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getNivelActKmsJul() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getNivelActKmsJul() + $entityCUC->getNivelActKmsJul()) . "</td>
                                <td style='text-align: center;'>" . $indice . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getLubricanteJul() . "</td>
                                <td style='text-align: center;'>" . $entityMN->getLiquidoFrenoJul() . "</td>
                                 </tr>
                                ";
                    break;
                case 8:
                    $combP += $entityMN->getPrecioCombustible() * ($entityMN->getCombustibleLitrosAgo() + $entityCUC->getCombustibleLitrosAgo());
                    $combmnt += $entityMN->getCombustibleLitrosAgo();
                    $combcuct += $entityCUC->getCombustibleLitrosAgo();
                    $kmsmnt += $entityMN->getNivelActKmsAgo();
                    $kmscuct += $entityCUC->getNivelActKmsAgo();
                    $lubt += $entityCUC->getLubricanteAgo();
                    $liqt += $entityMN->getLiquidoFrenoAgo();
                    $indice = (($entityMN->getNivelActKmsAgo() + $entityCUC->getNivelActKmsAgo()) == 0) ? 0 : round(($entityMN->getCombustibleLitrosAgo() + $entityCUC->getCombustibleLitrosAgo()) / ($entityMN->getNivelActKmsAgo() + $entityCUC->getNivelActKmsAgo()), 4);
                    $_html .= "<tr>
                                <td style='text-align: center;'>" . $i . "</td>
                                <td>" . $matricula . "</td>
                                <td>" . $marca . "</td>
                                <td style='text-align: center;'>" . $entityMN->getCombustibleLitrosAgo() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getCombustibleLitrosAgo() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getCombustibleLitrosAgo() + $entityCUC->getCombustibleLitrosAgo()) . "</td>
                                <td style='text-align: center;'>" . $entityMN->getPrecioCombustible() * ($entityMN->getCombustibleLitrosAgo() + $entityCUC->getCombustibleLitrosAgo()) . "</td>
                                <td style='text-align: center;'>" . $norma . "</td>
                                <td style='text-align: center;'>" . $entityMN->getNivelActKmsAgo() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getNivelActKmsAgo() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getNivelActKmsAgo() + $entityCUC->getNivelActKmsAgo()) . "</td>
                                <td style='text-align: center;'>" . $indice . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getLubricanteAgo() . "</td>
                                <td style='text-align: center;'>" . $entityMN->getLiquidoFrenoAgo() . "</td>
                                </tr>
                                ";
                    break;
                case 9:
                    $combP += $entityMN->getPrecioCombustible() * ($entityMN->getCombustibleLitrosSep() + $entityCUC->getCombustibleLitrosSep());
                    $combmnt += $entityMN->getCombustibleLitrosSep();
                    $combcuct += $entityCUC->getCombustibleLitrosSep();
                    $kmsmnt += $entityMN->getNivelActKmsSep();
                    $kmscuct += $entityCUC->getNivelActKmsSep();
                    $lubt += $entityCUC->getLubricanteSep();
                    $liqt += $entityMN->getLiquidoFrenoSep();
                    $indice = (($entityMN->getNivelActKmsSep() + $entityCUC->getNivelActKmsSep()) == 0) ? 0 : round(($entityMN->getCombustibleLitrosSep() + $entityCUC->getCombustibleLitrosSep()) / ($entityMN->getNivelActKmsSep() + $entityCUC->getNivelActKmsSep()), 4);
                    $_html .= "<tr>
                                <td style='text-align: center;'>" . $i . "</td>
                                <td>" . $matricula . "</td>
                                <td>" . $marca . "</td>
                                <td style='text-align: center;'>" . $entityMN->getCombustibleLitrosSep() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getCombustibleLitrosSep() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getCombustibleLitrosSep() + $entityCUC->getCombustibleLitrosSep()) . "</td>
                                <td style='text-align: center;'>" . $entityMN->getPrecioCombustible() * ($entityMN->getCombustibleLitrosSep() + $entityCUC->getCombustibleLitrosSep()) . "</td>
                                <td style='text-align: center;'>" . $norma . "</td>
                                <td style='text-align: center;'>" . $entityMN->getNivelActKmsSep() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getNivelActKmsSep() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getNivelActKmsSep() + $entityCUC->getNivelActKmsSep()) . "</td>
                                <td style='text-align: center;'>" . $indice . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getLubricanteSep() . "</td>
                                <td style='text-align: center;'>" . $entityMN->getLiquidoFrenoSep() . "</td>
                                 </tr>
                                ";
                    break;
                case 10:
                    $combP += $entityMN->getPrecioCombustible() * ($entityMN->getCombustibleLitrosOct() + $entityCUC->getCombustibleLitrosOct());
                    $combmnt += $entityMN->getCombustibleLitrosOct();
                    $combcuct += $entityCUC->getCombustibleLitrosOct();
                    $kmsmnt += $entityMN->getNivelActKmsOct();
                    $kmscuct += $entityCUC->getNivelActKmsOct();
                    $lubt += $entityCUC->getLubricanteOct();
                    $liqt += $entityMN->getLiquidoFrenoOct();
                    $indice = (($entityMN->getNivelActKmsOct() + $entityCUC->getNivelActKmsOct()) == 0) ? 0 : round(($entityMN->getCombustibleLitrosOct() + $entityCUC->getCombustibleLitrosOct()) / ($entityMN->getNivelActKmsOct() + $entityCUC->getNivelActKmsOct()), 4);
                    $_html .= "<tr>
                                <td style='text-align: center;'>" . $i . "</td>
                                <td>" . $matricula . "</td>
                                <td>" . $marca . "</td>
                                <td style='text-align: center;'>" . $entityMN->getCombustibleLitrosOct() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getCombustibleLitrosOct() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getCombustibleLitrosOct() + $entityCUC->getCombustibleLitrosOct()) . "</td>
                                <td style='text-align: center;'>" . $entityMN->getPrecioCombustible() * ($entityMN->getCombustibleLitrosOct() + $entityCUC->getCombustibleLitrosOct()) . "</td>
                                <td style='text-align: center;'>" . $norma . "</td>
                                <td style='text-align: center;'>" . $entityMN->getNivelActKmsOct() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getNivelActKmsOct() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getNivelActKmsOct() + $entityCUC->getNivelActKmsOct()) . "</td>
                                <td style='text-align: center;'>" . $indice . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getLubricanteOct() . "</td>
                                <td style='text-align: center;'>" . $entityMN->getLiquidoFrenoOct() . "</td>
                                </tr>
                                ";
                    break;
                case 11:
                    $combP += $entityMN->getPrecioCombustible() * ($entityMN->getCombustibleLitrosNov() + $entityCUC->getCombustibleLitrosNov());
                    $combmnt += $entityMN->getCombustibleLitrosNov();
                    $combcuct += $entityCUC->getCombustibleLitrosNov();
                    $kmsmnt += $entityMN->getNivelActKmsNov();
                    $kmscuct += $entityCUC->getNivelActKmsNov();
                    $lubt += $entityCUC->getLubricanteNov();
                    $liqt += $entityMN->getLiquidoFrenoNov();
                    $indice = (($entityMN->getNivelActKmsNov() + $entityCUC->getNivelActKmsNov()) == 0) ? 0 : round(($entityMN->getCombustibleLitrosNov() + $entityCUC->getCombustibleLitrosNov()) / ($entityMN->getNivelActKmsNov() + $entityCUC->getNivelActKmsNov()), 4);
                    $_html .= "<tr>
                                <td style='text-align: center;'>" . $i . "</td>
                                <td>" . $matricula . "</td>
                                <td>" . $marca . "</td>
                                <td style='text-align: center;'>" . $entityMN->getCombustibleLitrosNov() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getCombustibleLitrosNov() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getCombustibleLitrosNov() + $entityCUC->getCombustibleLitrosNov()) . "</td>
                                <td style='text-align: center;'>" . $entityMN->getPrecioCombustible() * ($entityMN->getCombustibleLitrosNov() + $entityCUC->getCombustibleLitrosNov()) . "</td>
                                <td style='text-align: center;'>" . $norma . "</td>
                                <td style='text-align: center;'>" . $entityMN->getNivelActKmsNov() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getNivelActKmsNov() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getNivelActKmsNov() + $entityCUC->getNivelActKmsNov()) . "</td>
                                <td style='text-align: center;'>" . $indice . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getLubricanteNov() . "</td>
                                <td style='text-align: center;'>" . $entityMN->getLiquidoFrenoNov() . "</td>
                                </tr>
                                ";
                    break;
                case 12:
                    $combP += $entityMN->getPrecioCombustible() * ($entityMN->getCombustibleLitrosDic() + $entityCUC->getCombustibleLitrosDic());
                    $combmnt += $entityMN->getCombustibleLitrosDic();
                    $combcuct += $entityCUC->getCombustibleLitrosDic();
                    $kmsmnt += $entityMN->getNivelActKmsDic();
                    $kmscuct += $entityCUC->getNivelActKmsDic();
                    $lubt += $entityCUC->getLubricanteDic();
                    $liqt += $entityMN->getLiquidoFrenoDic();
                    $indice = (($entityMN->getNivelActKmsDic() + $entityCUC->getNivelActKmsDic()) == 0) ? 0 : round(($entityMN->getCombustibleLitrosDic() + $entityCUC->getCombustibleLitrosDic()) / ($entityMN->getNivelActKmsDic() + $entityCUC->getNivelActKmsDic()), 4);
                    $_html .= "<tr>
                                <td style='text-align: center;'>" . $i . "</td>
                                <td>" . $matricula . "</td>
                                <td>" . $marca . "</td>
                                <td style='text-align: center;'>" . $entityMN->getCombustibleLitrosDic() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getCombustibleLitrosDic() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getCombustibleLitrosDic() + $entityCUC->getCombustibleLitrosDic()) . "</td>
                                <td style='text-align: center;'>" . $entityMN->getPrecioCombustible() * ($entityMN->getCombustibleLitrosDic() + $entityCUC->getCombustibleLitrosDic()) . "</td>
                                <td style='text-align: center;'>" . $norma . "</td>
                                <td style='text-align: center;'>" . $entityMN->getNivelActKmsDic() . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getNivelActKmsDic() . "</td>
                                <td style='text-align: center;'>" . ($entityMN->getNivelActKmsDic() + $entityCUC->getNivelActKmsDic()) . "</td>
                                <td style='text-align: center;'>" . $indice . "</td>
                                <td style='text-align: center;'>" . $entityCUC->getLubricanteDic() . "</td>
                                <td style='text-align: center;'>" . $entityMN->getLiquidoFrenoDic() . "</td>
                                </tr>
                                ";
                    break;
                default:
                    $combustible_litros_total_mn = $entityMN->getCombustibleLitrosEne() + $entityMN->getCombustibleLitrosFeb()
                        + $entityMN->getCombustibleLitrosMar() + $entityMN->getCombustibleLitrosAbr() + $entityMN->getCombustibleLitrosMay()
                        + $entityMN->getCombustibleLitrosJun() + $entityMN->getCombustibleLitrosJul() + $entityMN->getCombustibleLitrosAgo()
                        + $entityMN->getCombustibleLitrosSep() + $entityMN->getCombustibleLitrosOct() + $entityMN->getCombustibleLitrosNov()
                        + $entityMN->getCombustibleLitrosDic();
                    $combustible_litros_total_cuc = $entityCUC->getCombustibleLitrosEne() + $entityCUC->getCombustibleLitrosFeb()
                        + $entityCUC->getCombustibleLitrosMar() + $entityCUC->getCombustibleLitrosAbr() + $entityCUC->getCombustibleLitrosMay()
                        + $entityCUC->getCombustibleLitrosJun() + $entityCUC->getCombustibleLitrosJul() + $entityCUC->getCombustibleLitrosAgo()
                        + $entityCUC->getCombustibleLitrosSep() + $entityCUC->getCombustibleLitrosOct() + $entityCUC->getCombustibleLitrosNov()
                        + $entityCUC->getCombustibleLitrosDic();

                    $nivel_act_kms_total_mn = $entityMN->getNivelActKmsEne() + $entityMN->getNivelActKmsFeb() + $entityMN->getNivelActKmsMar()
                        + $entityMN->getNivelActKmsAbr() + $entityMN->getNivelActKmsMay() + $entityMN->getNivelActKmsJun() + $entityMN->getNivelActKmsJul()
                        + $entityMN->getNivelActKmsAgo() + $entityMN->getNivelActKmsSep() + $entityMN->getNivelActKmsOct() + $entityMN->getNivelActKmsNov()
                        + $entityMN->getNivelActKmsDic();
                    $nivel_act_kms_total_cuc = $entityCUC->getNivelActKmsEne() + $entityCUC->getNivelActKmsFeb() + $entityCUC->getNivelActKmsMar()
                        + $entityCUC->getNivelActKmsAbr() + $entityCUC->getNivelActKmsMay() + $entityCUC->getNivelActKmsJun() + $entityCUC->getNivelActKmsJul()
                        + $entityCUC->getNivelActKmsAgo() + $entityCUC->getNivelActKmsSep() + $entityCUC->getNivelActKmsOct() + $entityCUC->getNivelActKmsNov()
                        + $entityCUC->getNivelActKmsDic();

                    $lubricante_total = $entityMN->getLubricanteEne() + $entityMN->getLubricanteFeb() + $entityMN->getLubricanteMar()
                        + $entityMN->getLubricanteAbr() + $entityMN->getLubricanteMay() + $entityMN->getLubricanteJun() + $entityMN->getLubricanteJul()
                        + $entityMN->getLubricanteAgo() + $entityMN->getLubricanteSep() + $entityMN->getLubricanteOct() + $entityMN->getLubricanteNov()
                        + $entityMN->getLubricanteDic();
                    $liquido_freno_total = $entityMN->getLiquidoFrenoEne() + $entityMN->getLiquidoFrenoFeb() + $entityMN->getLiquidoFrenoMar()
                        + $entityMN->getLiquidoFrenoAbr() + $entityMN->getLiquidoFrenoMay() + $entityMN->getLiquidoFrenoJun() + $entityMN->getLiquidoFrenoJul()
                        + $entityMN->getLiquidoFrenoAgo() + $entityMN->getLiquidoFrenoSep() + $entityMN->getLiquidoFrenoOct() + $entityMN->getLiquidoFrenoNov()
                        + $entityMN->getLiquidoFrenoDic();

                    $combmnt += $combustible_litros_total_mn;
                    $combcuct += $combustible_litros_total_cuc;
                    $kmsmnt += $nivel_act_kms_total_mn;
                    $kmscuct += $nivel_act_kms_total_cuc;
                    $lubt += $lubricante_total;
                    $liqt += $liquido_freno_total;
                    $indice = (($nivel_act_kms_total_mn + $nivel_act_kms_total_cuc) == 0) ? 0 : round(($combustible_litros_total_mn + $combustible_litros_total_cuc) / ($nivel_act_kms_total_mn + $nivel_act_kms_total_cuc), 4);
                    $_html .= "<tr>
                                <td style='text-align: center;'>" . $i . "</td>
                                <td>" . $matricula . "</td>
                                <td>" . $marca . "</td>
                                <td style='text-align: center;'>" . $combustible_litros_total_mn . "</td>
                                <td style='text-align: center;'>" . $combustible_litros_total_cuc . "</td>
                                <td style='text-align: center;'>" . ($combustible_litros_total_mn + $combustible_litros_total_cuc) . "</td>
                                <td style='text-align: center;'>" . $tipoCombustible->getPrecio() * ($combustible_litros_total_mn + $combustible_litros_total_cuc) . "</td>
                                <td style='text-align: center;'>" . $norma . "</td>
                                <td style='text-align: center;'>" . $nivel_act_kms_total_mn . "</td>
                                <td style='text-align: center;'>" . $nivel_act_kms_total_cuc . "</td>
                                <td style='text-align: center;'>" . ($nivel_act_kms_total_mn + $nivel_act_kms_total_cuc) . "</td>
                                <td style='text-align: center;'>" . $indice . "</td>
                                <td style='text-align: center;'>" . $lubricante_total . "</td>
                                <td style='text-align: center;'>" . $liquido_freno_total . "</td>
                                </tr>
                                ";
                    break;
            }
        }

        $indice = (($kmsmnt + $kmscuct) == 0) ? 0 : round(($combmnt + $combcuct) / ($kmsmnt + $kmscuct), 4);
        $_html .= "<tr>
                    <td colspan='3' style='text-align: center;'><strong>Total</strong></td>
                    <td style='text-align: center;'><strong>" . $combmnt . "</strong></td>
                    <td style='text-align: center;'><strong>" . $combcuct . "</strong></td>
                    <td style='text-align: center;'><strong>" . ($combmnt + $combcuct) . "</strong></td>
                    <td style='text-align: center;'><strong>" . $combP . "</strong></td>
                    <td style='text-align: center;'><strong></strong></td>
                    <td style='text-align: center;'><strong>" . $kmsmnt . "</strong></td>
                    <td style='text-align: center;'><strong>" . $kmscuct . "</strong></td>
                    <td style='text-align: center;'><strong>" . ($kmsmnt + $kmscuct) . "</strong></td>
                    <td style='text-align: center;'><strong>" . $indice . "</strong></td>
                    <td style='text-align: center;'><strong>" . $lubt . "</strong></td>
                    <td style='text-align: center;'><strong>" . $liqt . "</strong></td>
                    </tr>
                    ";

        $pieFirma = $this->get('portadores.piefirma')->getPieFirma(DocumentosEnum::planCombustible, $nunidadid);
        $_html .= "<tr>
                <td colspan='14' style='text-align: left;'>$pieFirma</td>
            </tr>";

        $_html .= "</table>
        </body>
        </html>";

        return new Response(json_encode(array('success' => true, 'html' => $_html)));
    }
}