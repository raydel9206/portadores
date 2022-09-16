<?php
/**
 * Created by PhpStorm.
 * User: yosley
 * Date: 13/10/2015
 * Time: 12:24
 */


namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\AdminBundle\Util\Util;
//use Geocuba\AdminBundle\Util\FechaUtil;
use Geocuba\PortadoresBundle\Entity\VehiculoTipoMantenimiento;
use Geocuba\PortadoresBundle\Util\FechaUtil;
use Geocuba\AdminBundle\Util\Validar;
use Geocuba\PortadoresBundle\Entity\AnexoUnico;
use Geocuba\PortadoresBundle\Entity\CombustibleKilometros;
use Doctrine\Common\CommonException;
use Doctrine\Common\Util\Debug;
use Geocuba\PortadoresBundle\Util\Utiles;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\Utils\ViewActionTrait;

class AnexoUnicoController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $session = $request->getSession();

        $fecha = FechaUtil::getfechaactual();

        $nunidadid = $request->get('nunidadid');
        $matricula = $request->get('nombre');

        $em = $this->getDoctrine()->getManager();
        $mes = $request->get('mes');
        $anno = $request->get('anno');


        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);
//        var_dump($mes);
//        var_dump($anno);die;
//        var_dump($_unidades);die;
        $entities = $em->getRepository('PortadoresBundle:AnexoUnico')->buscarAnexoUnico($_unidades, $matricula, $mes, $anno);

        $_data = array();

        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'persona' => $entity->getNpersonaid()->getId(),
                'npersonaid' => $entity->getNpersonaid()->getId(),
                'npersona' => $entity->getNpersonaid()->getNombre(),
                'vehiculo' => $entity->getNvehiculoid()->getId(),
                'nvehiculoid' => $entity->getNvehiculoid()->getId(),
                'matricula' => $entity->getMatricula(),
                'norma_plan' => (float)$entity->getNormaPlan(),
                'kilometraje_proximo_mantenimiento' => $entity->getKilometrajeProximoMantenimiento(),
                'kilometraje_mes_anterior' => $entity->getKilometrajeMesAnterior(),
                'combustible_estimado_tanque' => $entity->getCombustibleEstimadoTanque(),
                'kilometraje_cierre_mes' => $entity->getKilometrajeCierreMes(),
                'kilometraje' => $entity->getKilometrajeCierreMes(),
                'combustible_estimado_tanque_cierre' => $entity->getCombustibleEstimadoTanqueCierre(),
                'comb_estimado_tanke' => $entity->getCombustibleEstimadoTanqueCierre(),
                'kilometros_total_recorrido' => $entity->getKilometrosTotalRecorrido(),
                'combustible_total_consumido' => $entity->getCombustibleTotalConsumido(),
                'combustible_total_abastecido' => $entity->getCombustibleTotalAbastecido(),
                'indice_real' => $entity->getNormaReal(),
                'por_ciento_indice_real_plan' => $entity->getPorcientoNormaRealPlan(),
                'tipo_mantenimiento_id' => is_null($entity->getTipoMantenimientoid()) ? '' : $entity->getTipoMantenimientoid()->getId(),
                'tipo_mantenimiento' => is_null($entity->getTipoMantenimientoid()) ? '' : $entity->getTipoMantenimientoid()->getNombre(),
                'kilometraje_mantenimiento' => $entity->getKilometrajeMantenimiento(),
                'observaciones' => $entity->getObservaciones(),
                'fecha_anexo' => $entity->getFecha()->format('d/m/Y'),
                'mes' => FechaUtil::getNombreMes($entity->getMes()),
                'indice_real_plan' => $entity->getRealPlan()
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => \count($_data)));
    }

    public function loadCombKilometrosAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('PortadoresBundle:CombustibleKilometros')->findByAnexoUnicoid($request->get('anexoid'));
        $data = array();
        $data_grafica = array();
        if ($entities) {
            $norma = $entities[0]->getAnexoUnicoid()->getNvehiculoid()->getNorma();
            $kilometraje_inicial = $entities[0]->getAnexoUnicoid()->getKilometrajeMesAnterior();
            $combustible_estimado_tanque = $entities[0]->getAnexoUnicoid()->getCombustibleEstimadoTanque();
        }
        $comb_abast = 0;
        foreach ($entities as $entity) {
            $comb_abast += $entity->getCombustibleAbastecido();
            $indice = 0;
            if ($combustible_estimado_tanque + $comb_abast - $entity->getCombustibleEstimadoTanque() != 0) {
                $indice = ($entity->getKilometraje() - $kilometraje_inicial) / ($combustible_estimado_tanque + $comb_abast - $entity->getCombustibleEstimadoTanque());
            }
            $data[] = array(
                'id' => $entity->getId(),
                'fecha' => $entity->getFecha()->format('d/m/Y'),
                'nro_tarjeta' => $entity->getNtarjetaid()->getNroTarjeta(),
                'kilometraje' => $entity->getKilometraje(),
                'comb_abast' => $entity->getCombustibleAbastecido(),
                'comb_est_tanke' => $entity->getCombustibleEstimadoTanque(),
                'indice' => $indice
            );

//            $comb_abast += $entity->getCombustibleAbastecido();
//            $indice = 0;
//            if ($combustible_estimado_tanque + $comb_abast - $entity->getCombustibleEstimadoTanque() != 0) {
//                $indice = ($entity->getKilometraje() - $kilometraje_inicial) / ($combustible_estimado_tanque + $comb_abast - $entity->getCombustibleEstimadoTanque());
//            }
//            $data_grafica[] = array(
//                'fecha' => $entity->getFecha()->format('d/m/Y'),
//                'indice_mas_5' => $norma + $norma * 0.05,
//                'indice_menos_5' => $norma - $norma * 0.05,
//                'indice' => $indice
//            );
        }
        return new JsonResponse(array('success' => true, 'rows' => $data));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addAction(Request $request)
    {
        $comb_kilometros = $request->get('comb_kilometros');
        Validar::ToJSON($comb_kilometros);
        $em = $this->getDoctrine()->getManager();
        $fecha_anexo = $request->get('fecha_anexo');
//        print_r($request);die;
        $fecha_registro_anexo = date_create_from_format('d/m/Y', $fecha_anexo);

        $session = $request->getSession();
        $anno = $session->get('selected_year');
        $mes = $session->get('selected_month');
        $vehiculo_id = $request->get('vehiculo');

        $vehiculo_entity = $em->getRepository('PortadoresBundle:AnexoUnico')->findBy(array(
            'mes' => $mes,
            'anno' => $anno,
            'nvehiculoid' => $vehiculo_id
        ));

        if ($vehiculo_entity)
            return new JsonResponse(array('success' => false, 'cls' => 'warning', 'message' => 'El Anexo Único del mes seleccionado ya existe.'));

        $registro = $em->getConnection()->fetchAll(
            "Select  r.id,a.id as aid from datos.registro_combustible r join datos.registro_combustible_analisis a on a.registro_combustible_id=r.id where extract( month from r.fecha) ='$mes' and r.vehiculoid ='$vehiculo_id'"
        );
        if (count($registro) > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'warning', 'message' => 'El vehículo tiene registro de combustible en el mes ' . $mes));

        $registro = $em->getConnection()->fetchAll(
            "Select id from datos.registro_combustible r where date_part('month',r.fecha) =$mes and r.vehiculoid ='$vehiculo_id'"
        );
        if (count($registro) > 0) {
            $registro_combustible = $em->getRepository('PortadoresBundle:RegistroCombustible')->find($registro[0]['id']);
            $em->remove($registro_combustible);
        }

        try {
            $vehiculo_entity = $em->getRepository('PortadoresBundle:Vehiculo')->find($request->get('vehiculo'));
            $anexo = new AnexoUnico();
            $anexo->setMatricula($vehiculo_entity->getMatricula());
            $anexo->setNormaPlan($request->get('norma_plan'));
            $anexo->setKilometrajeMesAnterior($request->get('kilometraje_mes_anterior'));
            $anexo->setCombustibleEstimadoTanque($request->get('combustible_estimado_tanque'));
            $anexo->setKilometrajeProximoMantenimiento($request->get('kilometraje_proximo_mantenimiento'));
            $anexo->setFecha($fecha_registro_anexo);
            $anexo->setKilometrajeCierreMes($request->get('kilometraje'));
            $anexo->setCombustibleEstimadoTanqueCierre($request->get('comb_estimado_tanke'));
            $anexo->setKilometrosTotalRecorrido($request->get('km_total_recorrido'));
            $anexo->setCombustibleTotalConsumido($request->get('combustible_total_consumido'));
            $anexo->setNormaReal($request->get('indice_real'));
            $anexo->setPorcientoNormaRealPlan($request->get('por_ciento_indice_real_plan'));
            $anexo->setKilometrajeMantenimiento($request->get('kilometraje_mantenimiento'));
            $anexo->setObservaciones($request->get('observaciones'));
            $anexo->setAnno($anno);
            $anexo->setMes($mes);
            $anexo->setRealPlan($request->get('indice_real_plan'));

            $persona_entity = $em->getRepository('PortadoresBundle:Persona')->find($request->get('persona'));

            $anexo->setNpersonaid($persona_entity);
            $anexo->setNvehiculoid($vehiculo_entity);

            $tipo_mantenimiento_entity = $em->getRepository('PortadoresBundle:TipoMantenimiento')->find($request->get('tipo_mantenimiento_id'));

            $anexo->setTipoMantenimientoid($tipo_mantenimiento_entity);
            $anexo->setVisible(true);
            $em->persist($anexo);

            $comb_abastecido = 0;

            foreach ($comb_kilometros as $comb_kilometro) {
                $comb_abastecido += $comb_kilometro->comb_abast;
                $comb_kilometro_entity = new CombustibleKilometros();
                $comb_kilometro_entity->setAnexoUnicoid($anexo);
                $comb_kilometro_entity->setFecha(\DateTime::createFromFormat('d/m/Y', $comb_kilometro->fecha));
                $tarjeta_entity = $em->getRepository('PortadoresBundle:Tarjeta')->findOneBy(
                    array(
                        'nroTarjeta' => $comb_kilometro->nro_tarjeta,
                        'visible' => true
                    )
                );
                $comb_kilometro_entity->setNpersonaid($persona_entity);
                $comb_kilometro_entity->setNtarjetaid($tarjeta_entity);
                $comb_kilometro_entity->setKilometraje($comb_kilometro->kilometraje);
                $comb_kilometro_entity->setCombustibleAbastecido($comb_kilometro->comb_abast);
                $comb_kilometro_entity->setCombustibleEstimadoTanque($comb_kilometro->comb_est_tanke);
                $em->persist($comb_kilometro_entity);
            }

            $anexo->setCombustibleTotalAbastecido($comb_abastecido);

            $em->persist($anexo);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Anexo Único realizado con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }

    }

    public function modAction(Request $request /*,$comb_kilometros*/)
    {
        $em = $this->getDoctrine()->getManager();

        try {
            $em->transactional(function ($em) use ($request) {
                $comb_kilometros = $request->get('store');
                Validar::ToJSON($comb_kilometros);
                $fecha_anexo = $request->get('fecha_anexo');

                $fecha_registro_anexo = date_create_from_format('d/m/Y', $fecha_anexo);


                $vehiculo_entity = $em->getRepository('PortadoresBundle:Vehiculo')->find($request->get('vehiculo'));
                $anexo = $em->getRepository('PortadoresBundle:AnexoUnico')->find($request->get('id'));
                $anexo->setMatricula($vehiculo_entity->getMatricula());
                $anexo->setNormaPlan($request->get('norma_plan'));
                $anexo->setKilometrajeMesAnterior($request->get('kilometraje_mes_anterior'));
                $anexo->setCombustibleEstimadoTanque($request->get('combustible_estimado_tanque'));
                $anexo->setKilometrajeProximoMantenimiento($request->get('kilometraje_proximo_mantenimiento'));
                $anexo->setFecha($fecha_registro_anexo);
                $anexo->setKilometrajeCierreMes($request->get('kilometraje'));
                $anexo->setCombustibleEstimadoTanqueCierre($request->get('comb_estimado_tanke'));
                $anexo->setKilometrosTotalRecorrido($request->get('km_total_recorrido'));
                $anexo->setCombustibleTotalConsumido($request->get('combustible_total_consumido'));
                $anexo->setNormaReal($request->get('indice_real'));
                $anexo->setPorcientoNormaRealPlan($request->get('por_ciento_indice_real_plan'));
                $anexo->setKilometrajeMantenimiento($request->get('kilometraje_mantenimiento'));
                $anexo->setObservaciones($request->get('observaciones'));
                $anexo->setCombustibleTotalAbastecido($request->get('combustible_total_abastecido'));
                $anexo->setRealPlan($request->get('indice_real_plan'));

                $persona_entity = $em->getRepository('PortadoresBundle:Persona')->find($request->get('persona'));

                $anexo->setNpersonaid($persona_entity);
                $anexo->setNvehiculoid($vehiculo_entity);

                $tipo_mantenimiento_entity = $em->getRepository('PortadoresBundle:TipoMantenimiento')->find($request->get('tipo_mantenimiento_id'));

                $anexo->setTipoMantenimientoid($tipo_mantenimiento_entity);
                $anexo->setVisible(true);

                $comb_kilometro_entities = $em->getRepository('PortadoresBundle:CombustibleKilometros')->findBy(array('anexoUnicoid' => $anexo));
                foreach ($comb_kilometro_entities as $comb_kilometro_entity)
                    $em->remove($comb_kilometro_entity);

                $comb_abastecido = 0;

                for ($i = 0, $iMax = count($comb_kilometros); $i < $iMax; $i++) {
                    $comb_abastecido += $comb_kilometros[$i]->comb_abast;
                    $comb_kilometro_entity = new CombustibleKilometros();
                    $comb_kilometro_entity->setAnexoUnicoid($anexo);
                    $comb_kilometro_entity->setFecha(date_create($comb_kilometros[$i]->fecha));
                    $tarjeta_entity = $em->getRepository('PortadoresBundle:Tarjeta')->findBy(
                        array(
                            'nroTarjeta' => $comb_kilometros[$i]->nro_tarjeta,
                            'visible' => true
                        )
                    );
                    $comb_kilometro_entity->setNpersonaid($persona_entity);
                    $comb_kilometro_entity->setNtarjetaid($tarjeta_entity[0]);
                    $comb_kilometro_entity->setKilometraje($comb_kilometros[$i]->kilometraje);
                    $comb_kilometro_entity->setCombustibleAbastecido($comb_kilometros[$i]->comb_abast);
                    $comb_kilometro_entity->setCombustibleEstimadoTanque($comb_kilometros[$i]->comb_est_tanke);
                    $em->persist($comb_kilometro_entity);
                }

                $anexo->setCombustibleTotalAbastecido($comb_abastecido);

                $em->persist($anexo);
                $em->flush();

            });
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
        return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Anexo Único modificado con éxito.'));
    }

    public function delAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $entity = $em->getRepository('PortadoresBundle:AnexoUnico')->find($id);

        $entities = $em->getRepository('PortadoresBundle:CombustibleKilometros')->findBy(array(
            'anexoUnicoid'=>$entity
        ));

        foreach ($entities as $ent)
            $em->remove($ent);

        $em->remove($entity);
        try {
//            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Anexo Único eliminado con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function getLastAnexoVehiculoAction(Request $request)
    {
        $session = $request->getSession();
        $idvehiculo = $request->get('idvehiculo');
        $mes = $request->get('mes');
        $anno = $request->get('anno');
        $em = $this->getDoctrine()->getManager();
        $datos = array();
        if ($mes == 1) {
            $mes_ = 12;
            $anno_ = $anno - 1;
        } else {
            $mes_ = $mes - 1;
            $anno_ = $anno;
        }

        $vehiculo = $em->getRepository('PortadoresBundle:Vehiculo')->find($idvehiculo);


        $entity = $em->getRepository('PortadoresBundle:AnexoUnico')->findOneBy(array(
            'nvehiculoid' => $idvehiculo,
//            'mes' => $mes_,
//            'anno' => $anno_
        ), array('anno' => 'DESC', 'mes' => 'DESC'));

        if ($entity) {
            $datos = array(
                'kilometrajeCierreMes' => $vehiculo->getOdometro()?$entity->getKilometrajeCierreMes():0,
                'combustibleEstimadoTanqueCierre' => $entity->getCombustibleEstimadoTanqueCierre(),
                'kilometraje_proximo_Mantenimiento' => $entity->getKilometrajeProximoMantenimiento(),
//                'tipo_mantenimiento' => array('id'=>$entity->getTipoMantenimientoid()->getId(),
//                 'nombre'=>$entity->getTipoMantenimientoid()->getNombre()) ,
//                'kilometrajeMantenimiento' => $entity->getKilometrajeMantenimiento()
            );
            return new JsonResponse(array('success' => true, 'rows' => $datos));
        } else
            return new JsonResponse(array('success' => false));
    }

    public function getCombAbastecidoAction(Request $request)
    {
        $conn = $this->getDoctrine()->getConnection();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $idvehiculo = trim($request->get('idvehiculo'));
//        $mes = $session->get('selected_month');
        $mes = $request->get('mes');
        $anno = $request->get('anno');
//        $anno = $session->get('selected_year');
//        print_r($mes);die;
        $datos = array();

        $cant_litros = 0;
        $entities = $conn->fetchAll(
            "SELECT  sum(cant_litros) as total_litros
             FROM datos.liquidacion where nvehiculoid='$idvehiculo' and  EXTRACT(MONTH FROM fecha_vale)=$mes and  EXTRACT(YEAR FROM fecha_vale)=$anno GROUP BY nvehiculoid");
//print_r($entities);die;
        if ($entities != null) {
            $cant_litros = $entities[0]['total_litros'];
        }
        return new JsonResponse(array('success' => true, 'total_litros' => $cant_litros));

    }

    public function getDatosVehiculoAction(Request $request)
    {

        $conn = $this->getDoctrine()->getConnection();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $idvehiculo = trim($request->get('idvehiculo'));

        $datos = array();
        $entities = $conn->fetchAll(
            "select  vehi.matricula,perso.nombre,perso.id,  vehi.norma from nomencladores.vehiculo as vehi
JOIN nomencladores.tarjeta_vehiculo as tarveh ON tarveh.nvehiculoid=vehi.id
JOIN nomencladores.tarjeta_persona as tarjper ON tarjper.ntarjetaid=tarveh.ntarjetaid
JOIN nomencladores.tarjeta as tarj ON tarj.id=tarjper.ntarjetaid
JOIN nomencladores.persona as perso ON perso.id=tarjper.npersonaid
WHERE vehi.id='$idvehiculo' and vehi.visible = true");

//print_r($entities);die;
//        Debug::dump($entities);
        if ($entities) {
            $datos[] = array(
                'matricula' => $entities[0]['matricula'],
                'nombre' => $entities[0]['nombre'],
                'id' => $entities[0]['id'],
                'norma' => $entities[0]['norma']
            );

        }
//print_r($datos);die;
        return new JsonResponse(array('success' => true, 'datos' => $datos));

    }

    public function getKM_recorridosAction(Request $request)
    {

        $data_TM = array();
        $data_anexo = 0;
        $em = $this->getDoctrine()->getManager();
        $id_tipoMante = $request->get('id_tipoMante');
        $id_vehiculo = $request->get('id_vehiculo');
//        print_r($id_vehiculo);die;

        $tipo_mantenimiento_entity = $em->getRepository('PortadoresBundle:VehiculoTipoMantenimiento')->findBy(array(
            'nvehiculoid' => $id_vehiculo,
            'tipoMantenimientoid' => $id_tipoMante,
            'visible' => true
        ));

        if ($tipo_mantenimiento_entity) {
            foreach ($tipo_mantenimiento_entity as $tipo_entity) {
                $data_TM = array(
                    'km_mant' => $tipo_entity->getKilometros()
                );
            }

        }
        $data_MTaller = array();
        $entity_mant_taller = $em->getRepository('PortadoresBundle:MantenimientoTaller')->ultimoMantenimiento($id_vehiculo, $id_tipoMante);
//print_r($entity_mant_taller);die;
        if ($entity_mant_taller[0][1] != null) {
            $data_MTaller = array(
                'fechaentrada' => $entity_mant_taller[0][1],
            );


//            print_r($data_MTaller);die;
            $entity_anexo = $em->getRepository('PortadoresBundle:AnexoUnico')->getanexobyfecha($id_vehiculo, $data_MTaller['fechaentrada']);
//print_r($entity_anexo);die;
            if ($entity_anexo) {
                foreach ($entity_anexo as $entity_ane) {

                    $data_anexo += $entity_ane->getKilometrosTotalRecorrido();

                }

            }

            $km_faltan = $data_TM['km_mant'] - $data_anexo;


        } else {

            $entity_anexo = $em->getRepository('PortadoresBundle:AnexoUnico')->findBy(array
            (
                'nvehiculoid' => $id_vehiculo,


            ));
            if ($entity_anexo) {
                foreach ($entity_anexo as $entity_ane) {

                    $data_anexo += $entity_ane->getKilometrosTotalRecorrido();

                }

            }

            $km_faltan = $data_TM['km_mant'] - $data_anexo;

        }


        return new JsonResponse(array('success' => true, 'km_faltan' => $km_faltan));
    }

    public function loadTipoMantenimientoByAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id_vehiculo = $request->get('id');
        $kilometraje = $request->get('kilometraje');

        $data_ = array();
        $tipo_mantenimiento_entity = $em->getRepository('PortadoresBundle:VehiculoTipoMantenimiento')->findBy(array(
            'nvehiculoid' => $id_vehiculo,
            'visible' => true
        ));
        if ($tipo_mantenimiento_entity) {
            /** @var VehiculoTipoMantenimiento $tipo_entity */
            foreach ($tipo_mantenimiento_entity as $tipo_entity) {
                if ($tipo_entity->getKilometros() >= $kilometraje) {
                    $data_[] = array(
                        'id' => $tipo_entity->getTipoMantenimientoid()->getId(),
                        'nombre' => $tipo_entity->getTipoMantenimientoid()->getNombre(),
                        'kilometros' => $tipo_entity->getKilometros(),
                    );
                }
            }

        }
        return new JsonResponse(array('success' => true, 'total' => count($data_), 'rows' => $data_));
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

}