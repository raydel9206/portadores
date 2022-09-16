<?php
/**
 * Created by PhpStorm.
 * User: yosley
 * Date: 14/07/2017
 * Time: 01:04 PM
 */

namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\PortadoresBundle\Entity\PlanAgua;
use Geocuba\PortadoresBundle\Entity\Plan;
use Geocuba\PortadoresBundle\Entity\PlanPe;
use Doctrine\Common\CommonException;
use Doctrine\Common\Util\Debug;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Geocuba\AdminBundle\Util\Util;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;

class PlanAnualPEController extends Controller
{
    use ViewActionTrait;

    public function getVehiculosPlanPEAction(Request $request)
    {
        $session = $request->getSession();
        $nunidadid = $request->get('unidadid');
        $em = $this->getDoctrine()->getManager();
        $anno = $session->get('selected_year');

        if ($nunidadid) {
            $_unidades[0] = $nunidadid;
//            $unidades = $em->getRepository('PortadoresBundle:Unidad')->findBy(array('padreid' => $nunidadid));
//            $i = 1;
//            /** @var Unidad $unidad */
//            foreach ($unidades as $unidad) {
//                $_unidades[$i] = $unidad->getId();
//                $i++;
//            }
        } else {
            $_user = $this->get('security.token_storage')->getToken()->getUser();
            $unidades = $em->getRepository('AdminBundle:UsuarioUnidad')->findBy(array('usuario' => $_user->getId()));
            foreach ($unidades as $unidad) {
                $_unidades[] = $unidad->getUnidad()->getId();
            }
        }

        $_datosAll = array();

        $total_diesel=0;
        $total_gasolina=0;
        $total_anual_fisico=0;
        $total_diesel_anual_fisico=0;
        $total_gasolina_anual_fisico=0;
        $total_plan_lubric_anual_fisico=0;
        $total_plan_elect_anual_fisico=0;
        $total_plan_glp_anual_fisico=0;
        $diesel_cuc=0;
        $gasolina_cuc=0;
        $total_cuc=0;
        $lubricante_cuc=0;

        for ($i = 0; $i < count($_unidades); $i++) {

            $entities_plan = $em->getRepository('PortadoresBundle:Plan')->findBy(array(
                'unidad' => $_unidades[$i],
                'anno' => $anno
            ));

            if ($entities_plan) {
//                print_r('tiene apaln');die;
                foreach ($entities_plan as $entity_plannes) {

                    $total_diesel+=$entity_plannes->getDiesel();
                    $total_gasolina+=$entity_plannes->getGasolina();
                    $total_anual_fisico+=$entity_plannes->getDiesel() + $entity_plannes->getGasolina();
                    $total_diesel_anual_fisico+=$entity_plannes->getDiesel();
                    $total_gasolina_anual_fisico+=$entity_plannes->getGasolina();
                    $total_plan_lubric_anual_fisico+=$entity_plannes->getLubricantes();
                    $total_plan_elect_anual_fisico+=$entity_plannes->getElectricidad();
                    $total_plan_glp_anual_fisico+=$entity_plannes->getGlp();
                    $diesel_cuc+=$entity_plannes->getDieselcuc();
                    $gasolina_cuc+=$entity_plannes->getGasolinacuc();
                    $total_cuc+=$entity_plannes->getDieselcuc() + $entity_plannes->getGasolinacuc();
                    $lubricante_cuc+=$entity_plannes->getLubricantecuc();

                    $_datosAll = array(
                        'SUB_TOTAL' => array('total_inventario' => 0,
                            'total_diesel' => $total_diesel,
                            'total_gasolina' => $total_gasolina
                        ),
                        'TOTAL_ANUAL_FISICO_L' =>
                            array(
                                'total_anual_fisico' =>$total_anual_fisico ,
                                'total_diesel_anual_fisico' => $total_diesel_anual_fisico,
                                'total_gasolina_anual_fisico' => $total_gasolina_anual_fisico,
                                'total_plan_lubric_anual_fisico' => $total_plan_lubric_anual_fisico,
                                'total_plan_elect_anual_fisico' => $total_plan_elect_anual_fisico,
                                'total_plan_glp_anual_fisico' => $total_plan_glp_anual_fisico
                            ),
                        'Valor_CUC' => array(
                            'diesel_cuc' => $diesel_cuc,
                            'gasolina_cuc' =>$gasolina_cuc ,
                            'total_cuc' => $total_cuc,
                            'lubricante_cuc' => $lubricante_cuc
                        ),
                        'plan_pe' => array()
                    );
                    $plan = $em->getRepository('PortadoresBundle:PlanPe')->findBy(array(
                        'idplan' => $entity_plannes->getId()
                    ));

//                    print_r($_datosAll);die;

                    if ($plan) {
                        foreach ($plan as $entity_plan) {
                            $_datosAll['plan_pe'][] = array(

                                'id' => $entity_plan->getId(),
                                'id_plan' => $entity_plan->getIdplan()->getId(),
                                'id_vehiculo' => $entity_plan->getInventarioFisico()->getId(),
                                'inventarioid' => $entity_plan->getInventarioFisico()->getId(),
                                'inventario' => $entity_plan->getInventarioFisico()->getMatricula(),
                                'Actividad' => $entity_plan->getActividad()->getNombre(),
                                'actividadid' => $entity_plan->getActividad()->getId(),
                                'modelo' => $entity_plan->getInventarioFisico()->getNmodeloVehiculoid()->getNmarcaVehiculoid()->getNombre() . ' -- ' . $entity_plan->getInventarioFisico()->getNmodeloVehiculoid()->getNombre(),
                                'diesel' => $entity_plan->getCantDiesel(),
                                'gasolina' => $entity_plan->getCantGasolina(),
                                'plan_lubric' => $entity_plan->getPlanLubric(),
                                'ind_consumo' => $entity_plan->getIndConsumo(),
                                'km_diesel' => $entity_plan->getKmDiesel(),
                                'km_gasolina' => $entity_plan->getKmGasolina(),
                                'diesel_anual' => $entity_plan->getDieselAnual(),
                                'gasolina_anual' => $entity_plan->getGasolinaAnual(),
                                'anno' => $entity_plan->getAnno(),
                            );

                        }
                    }
                }
//                print_r($_datosAll);die;
            }
            else {
//                print_r('No tiene apaln');die;
                $_datosAll = array(
                    'SUB_TOTAL' => array('total_inventario' => 0,
                        'total_diesel' => 0,
                        'total_gasolina' => 0
                    ),
                    'TOTAL_ANUAL_FISICO_L' =>
                        array(
                            'total_anual_fisico' => 0,
                            'total_diesel_anual_fisico' => 0,
                            'total_gasolina_anual_fisico' => 0,
                            'total_plan_lubric_anual_fisico' => 0,
                            'total_plan_elect_anual_fisico' => 0,
                            'total_plan_glp_anual_fisico' => 0
                        ),
                    'Valor_CUC' => array(
                        'diesel_cuc' => 0,
                        'gasolina_cuc' => 0,
                        'total_cuc' => 0,
                        'lubricante_cuc' => 0
                    ),
                    'plan_pe' => array()
                );
                $entities = $em->getRepository('PortadoresBundle:Vehiculo')->buscarVehiculobyunidades($_unidades);
                if ($entities)
                    foreach ($entities as $entity) {
                        $actividadid = '-';
                        $actividad = '-';
                        if ($entity->getActividad()) {
                            $actividadid = $entity->getActividad()->getId();
                            $actividad = $entity->getActividad()->getNombre();
                        }

                        $_datosAll['plan_pe'][] = array(
                            'id' => '',
                            'id_plan' => '',
                            'id_vehiculo' => $entity->getId(),
                            'inventarioid' => $entity->getId(),
                            'inventario' => $entity->getMatricula(),
                            'Actividad' => $actividad,
                            'actividadid' => $actividadid,
                            'modelo' => $entity->getNmodeloVehiculoid()->getNmarcaVehiculoid()->getNombre() . ' -- ' . $entity->getNmodeloVehiculoid()->getNombre(),
                            'diesel' => 0,
                            'gasolina' => 0,
                            'plan_lubric' => 0,
                            'ind_consumo' => $entity->getNorma(),
                            'km_diesel' => 0,
                            'km_gasolina' => 0,
                            'diesel_anual' => 0,
                            'gasolina_anual' => 0,
                            'anno' => 0,
                        );


                    }


            }

        }

        return new JsonResponse(array('rows' => $_datosAll, 'total' => count($_datosAll)));
    }

    public function addPlanAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $var = array();
        $session = $request->getSession();
        $year = $session->get('selected_year');
        $valores = $request->get('datos');

        $nunidadid = $request->get('unidadid');
        $fecha = new \DateTime(trim($request->get('fecha')));

        $entities_plan = $em->getRepository('PortadoresBundle:Plan')->findBy(array(
            'unidad' => $nunidadid,
            'anno' => $year
        ));
        $id_plan = '';
//   Debug::dump($entities_plan);die;
        if ($entities_plan) {
            foreach ($entities_plan as $plan) {
                $id_plan = $plan->getId();
//                Debug::dump($id_plan);die;

                $entities_plan_ = $em->getRepository('PortadoresBundle:Plan')->find($id_plan);
                $entities_plan_->setAnno($year);
                $entities_plan_->setDiesel($request->get('diesel'));
                $entities_plan_->setDieselcuc($request->get('dieselcuc'));
                $entities_plan_->setGasolina($request->get('gasolina'));
                $entities_plan_->setGasolinacuc($request->get('gasolina_cuc'));
                $entities_plan_->setElectricidad($request->get('pla_elect'));
                $entities_plan_->setGlp($request->get('plan_glp'));
                $entities_plan_->setLubricantes($request->get('lubric'));
                $entities_plan_->setLubricantecuc($request->get('lubricante_cuc'));
                $entities_plan_->setVisible(true);
                $entities_plan_->setFecha($fecha);
                $entities_plan_->setUnidad($em->getRepository('PortadoresBundle:Unidad')->find($nunidadid));
                $em->persist($entities_plan_);
                $em->flush();
            }

            $entities_planpe = $em->getRepository('PortadoresBundle:PlanPe')->findBy(array(
                'idplan' => $id_plan,
            ));

//            Debug::dump($entities_planpe);die;
            if ($entities_planpe) {
                foreach ($entities_planpe as $planpe) {
                    $id_planpe = $planpe->getId();

                    $entities_planpe = $em->getRepository('PortadoresBundle:PlanPe')->find($id_planpe);
//                    Debug::dump($entities_planpe);die;
                    $em->remove($entities_planpe);
                    $em->flush();
                }

                for ($i = 0; $i < count($valores); $i++) {

                    $entity = new PlanPe();
                    $entity->setInventarioFisico($em->getRepository('PortadoresBundle:Vehiculo')->find($valores[$i]['id_vehiculo']));
                    $entity->setActividad($em->getRepository('PortadoresBundle:Actividad')->find($valores[$i]['actividad']));
                    $entity->setCantDiesel($valores[$i]['diesel']);
                    $entity->setCantGasolina($valores[$i]['gasolina']);
                    $entity->setDieselAnual($valores[$i]['diesel_anual']);
                    $entity->setGasolinaAnual($valores[$i]['gasolina_anual']);
                    $entity->setPlanLubric($valores[$i]['plan_lubric']);
                    $entity->setIndConsumo($valores[$i]['ind_consumo']);
                    $entity->setKmDiesel($valores[$i]['km_diesel']);
                    $entity->setKmGasolina($valores[$i]['km_gasolina']);
                    $entity->setUnidadid($em->getRepository('PortadoresBundle:Unidad')->find($valores[$i]['unidad']));
                    $entity->setFecha($fecha);
                    $entity->setAnno($year);
                    $entity->setIdplan($em->getRepository('PortadoresBundle:Plan')->find($id_plan));
                    $em->persist($entity);
                    $em->flush();
                }


            }
        } else {

            $entity1 = new Plan();
            $entity1->setAnno($year);
            $entity1->setDiesel($request->get('diesel'));
            $entity1->setDieselcuc($request->get('dieselcuc'));
            $entity1->setGasolina($request->get('gasolina'));
            $entity1->setGasolinacuc($request->get('gasolina_cuc'));
            $entity1->setElectricidad($request->get('pla_elect'));
            $entity1->setGlp($request->get('plan_glp'));
            $entity1->setLubricantes($request->get('lubric'));
            $entity1->setLubricantecuc($request->get('lubricante_cuc'));
            $entity1->setVisible(true);
            $entity1->setFecha($fecha);
            $entity1->setUnidad($em->getRepository('PortadoresBundle:Unidad')->find($nunidadid));
            $em->persist($entity1);
            $id_plan = $entity1->getId();


            for ($i = 0; $i < count($valores); $i++) {

                $entity = new PlanPe();
                $entity->setInventarioFisico($em->getRepository('PortadoresBundle:Vehiculo')->find($valores[$i]['id_vehiculo']));
                $entity->setActividad($em->getRepository('PortadoresBundle:Actividad')->find($valores[$i]['actividad']));
                $entity->setCantDiesel($valores[$i]['diesel']);
                $entity->setCantGasolina($valores[$i]['gasolina']);
                $entity->setDieselAnual($valores[$i]['diesel_anual']);
                $entity->setGasolinaAnual($valores[$i]['gasolina_anual']);
                $entity->setPlanLubric($valores[$i]['plan_lubric']);
                $entity->setIndConsumo($valores[$i]['ind_consumo']);
                $entity->setKmDiesel($valores[$i]['km_diesel']);
                $entity->setKmGasolina($valores[$i]['km_gasolina']);
                $entity->setUnidadid($em->getRepository('PortadoresBundle:Unidad')->find($valores[$i]['unidad']));
                $entity->setFecha($fecha);
                $entity->setAnno($year);
                $entity->setIdplan($em->getRepository('PortadoresBundle:Plan')->find($id_plan));
                $em->persist($entity);
                $em->flush();
            }
        }
        try {

            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Plan  realizado con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));

        }


    }

    public function getTotalesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $valores = $request->get('datos');
        $id_plan = $request->get('id_plan');

        $alldata = array();

        $entity_planes = $em->getRepository('PortadoresBundle:Plan')->find($id_plan);

        if ($entity_planes) {
//            print_r($entity_planes->getDiesel());die;
//            Debug::dump($entity_planes->getDiesel());die
            $alldata = array(
                'SUB_TOTAL' => array('total_inventario' => count($valores),
                    'total_diesel' => $entity_planes->getDiesel(),
                    'total_gasolina' => $entity_planes->getGasolina()
                ),
                'TOTAL_ANUAL_FISICO_L' =>
                    array(
                        'total_anual_fisico' => $entity_planes->getDiesel() + $entity_planes->getGasolina(),
                        'total_diesel_anual_fisico' => $entity_planes->getDiesel(),
                        'total_gasolina_anual_fisico' => $entity_planes->getGasolina(),
                        'total_plan_lubric_anual_fisico' => $entity_planes->getLubricantes(),
                        'total_plan_elect_anual_fisico' => $entity_planes->getElectricidad(),
                        'total_plan_glp_anual_fisico' => $entity_planes->getGlp()
                    ),
                'Valor_CUC' => array(
                    'diesel_cuc' => $entity_planes->getDieselcuc(),
                    'gasolina_cuc' => $entity_planes->getGasolinacuc(),
                    'total_cuc' => $entity_planes->getDieselcuc() + $entity_planes->getGasolinacuc(),
                    'lubricante_cuc' => $entity_planes->getLubricantecuc()
                )
            );


        }

//        print_r($alldata);die;
        return new JsonResponse(array('rows' => $alldata, 'total' => count($alldata)));

    }

    public function getResumenplanAction(Request $request)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $nunidadid = trim($request->get('unidadid'));
        $anno = $session->get('selected_year');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $_data = array();
        $entities = $em->getRepository('PortadoresBundle:Plan')->getplanresumen($_unidades, $anno);
        if ($entities) {
            foreach ($entities as $entity) {
                $_data[] = array(
                    'id' => $entity->getId(),
                    'total_diesel_RESUMEN' => $entity->getDiesel(),
                    'total_gasolina_RESUMEN' => $entity->getGasolina(),
                    'total_diesel_t' => $entity->getDiesel(),
                    'total_gasolina_t' => $entity->getGasolina(),
                    'total_plan_elect_resumen' => $entity->getElectricidad(),
                    'total_plan_lubric_resumen' => $entity->getLubricantes(),
                    'total_plan_glp_resumen' => $entity->getGlp(),
                    'total_diesel_importe_resumen' => $entity->getDieselcuc(),
                    'total_gasolina_importe_resumen' => $entity->getGasolinacuc(),
                    'total_cuc_resumen' => $entity->getDieselcuc() + $entity->getGasolinacuc(),
                    'anno' => $entity->getAnno(),
                );
            }
        }
        return new JsonResponse(array('rows' => $_data, 'total' => count($_data)));

    }


}