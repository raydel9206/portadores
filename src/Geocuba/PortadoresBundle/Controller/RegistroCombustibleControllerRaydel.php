<?php
/**
 * Created by PhpStorm.
 * User: pfcadenas
 * Date: 16/08/2016
 * Time: 16:26
 */

namespace Geocuba\PortadoresBundle\Controller;

//use Geocuba\MySecurityBundle\Util\Util;
use Geocuba\PortadoresBundle\Util\FechaUtil;
use Geocuba\PortadoresBundle\Util\MonedaEnum;
use Geocuba\PortadoresBundle\Entity\PlanificacionCombustible;
use Geocuba\PortadoresBundle\Entity\PlanificacionCombustibleCuc;
use Geocuba\PortadoresBundle\Entity\RegistroCombustible;
use Geocuba\PortadoresBundle\Entity\RegistroCombustibleAnalisis;
use Geocuba\PortadoresBundle\Entity\RegistroCombustiblePlanificacion;
use Doctrine\Common\CommonException;
use Doctrine\Common\Util\Debug;
use Geocuba\PortadoresBundle\Util\Utiles;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Geocuba\PortadoresBundle\Util\DocumentosEnum;


class RegistroCombustibleControllerRaydel extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $vehiculo = $request->get('vehiculo');
        $start = $request->get('start');
        $limit = $request->get('limit');

        $session = $request->getSession();
        $anno = $request->get('anno');
        $mes = $request->get('mes');

        $fechaActual = $anno . '-' . $mes . '-01';

//        $_user = $this->getUser();
//        $dominio = $em->getRepository('MySecurityBundle:Dominio')->findByUsersid($_user->getId());
//        $_unidades[] = $dominio[0]->getUserUnidadid()->getId();
//        $dominio_unidades = $em->getRepository('MySecurityBundle:DominioUnidades')->findByDominioid($dominio[0]->getId());
//        foreach ($dominio_unidades as $unidad) {
//            $_unidades[] = $unidad->getUnidadid()->getId();
//        }

        $nunidadid = trim($request->get('unidadid'));
        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $qb = $em->createQueryBuilder();
        $qb->select('registroCombustible')
            ->from('PortadoresBundle:RegistroCombustible', 'registroCombustible')
            ->innerJoin('registroCombustible.vehiculoid', 'vehiculo')
            ->Where('registroCombustible.visible = true')
            ->andWhere('registroCombustible.fecha = :fecha')
            ->andWhere($qb->expr()->in('vehiculo.nunidadid', $_unidades))
            ->setparameter(':fecha', $fechaActual);

        if ($vehiculo) {
            $qb->andWhere('registroCombustible.vehiculoid = :id')
                ->setParameter('id', $vehiculo);
        }

        $entities = $qb->orderBy('vehiculo.nroOrden', 'ASC')
            ->getQuery()
//            ->setMaxResults($limit)
//            ->setFirstResult($start)
            ->getResult();

        $qb1 = $em->createQueryBuilder();
        $qb1->select('count(registroCombustible)')
            ->from('PortadoresBundle:RegistroCombustible', 'registroCombustible')
            ->innerJoin('registroCombustible.vehiculoid', 'vehiculo')
            ->Where('registroCombustible.visible = true')
            ->andWhere('registroCombustible.fecha = :fecha')
            ->andWhere($qb->expr()->in('vehiculo.nunidadid', $_unidades))
            ->setparameter(':fecha', $fechaActual);

        if ($vehiculo) {
            $qb1->andWhere('registroCombustible.vehiculoid = :id')
                ->setParameter('id', $vehiculo);
        }

        $count = $qb1->getQuery()->getSingleScalarResult();

        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'fecha_registro' => $entity->getFecha()->format('m/Y'),
                'fecha_planif' => $entity->getFecha()->format('m/d/Y'),
                'vehiculoid' => $entity->getVehiculoid()->getId(),
                'nroOrden' => $entity->getVehiculoid()->getNroOrden(),
                'vehiculonorma' => ($entity->getVehiculoid()->getNormaFar() > 0) ? $entity->getVehiculoid()->getNormaFar() : $entity->getVehiculoid()->getNorma(),
                'vehiculochapa' => $entity->getVehiculoid()->getMatricula(),
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $count));
    }

    // ya
    public function addAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $_date = '01-' . str_replace('/', '-', $request->get('fecha_registro'));
        $fecha_registro = new \DateTime($_date);
        $fecha_registro = new \DateTime($fecha_registro->format('Y-m-d'));

        $vehiculos = $em->getRepository('PortadoresBundle:Vehiculo')->findByVisible(true);

        foreach ($vehiculos as $vehiculo) {
            $entities_lic = $em->getRepository('PortadoresBundle:RegistroCombustible')->findOneBy(array(
                'vehiculoid' => $vehiculo->getId(),
                'fecha' => $fecha_registro,
                'visible' => true
            ));

            $anexo = $em->getRepository('PortadoresBundle:AnexoUnico')->findOneBy(array(
                'nvehiculoid' => $vehiculo->getId(),
                'mes' => intval($fecha_registro->format('m'), 10),
                'visible' => true
            ));

            if (!$entities_lic && !$anexo) {
                $entity = new RegistroCombustible();
                $entity->setFecha($fecha_registro);
                $entity->setVehiculoid($vehiculo);
                $entity->setNormaPlan($vehiculo->getNorma());
                $entity->setVisible(true);

                $em->persist($entity);
            }
        }
        try {
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Registro de combustible adicionado con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    /*    public function modAction(Request $request)
        {
            $em = $this->getDoctrine()->getManager();

            $id = $request->get('id');

            $_date = '01-' . str_replace('/', '-', $request->get('fecha_registro'));
            $fecha_registro = new \DateTime($_date);
            $fecha_registro = new \DateTime($fecha_registro->format('Y-m-d'));

            $vehiculo_id = $request->get('vehiculoid');

            $entities_lic = $em->getRepository('PortadoresBundle:RegistroCombustible')->findBy(array(
                'vehiculoid' => $vehiculo_id,
                'fecha' => $fecha_registro,
                'visible' => true
            ));

            foreach ($entities_lic as $entity) {
                if ($entity->getId() != $id)
                    return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Existe un registro de combustible con esa fecha y vehículo.'));
            }

            $entidad = $em->getRepository('PortadoresBundle:RegistroCombustible')->find($id);
            $entidad->setFecha($fecha_registro);
            $entidad->setVehiculoid($em->getRepository('PortadoresBundle:Nvehiculo')->find($vehiculo_id));
            try {
                $em->persist($entidad);
                $em->flush();
                $response = new JsonResponse();
                $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Registro de combustible modificado con éxito.'));
                return $response;
            } catch (CommonException $ex) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
            }
        }
    */


    public function delAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');

        $entity = $em->getRepository('PortadoresBundle:RegistroCombustible')->find($id);

        try {
            $em->remove($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Registro de combustible eliminado con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    private function getMesEspannol($mes)
    {
        $arr = array('01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio',
            '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre');
        return $arr[$mes];
    }

    private function getPlanificacion($em, $registroCombustible)
    {
        $planificacion = array();

        /**
         * @var PlanificacionCombustible $planificacionCombustibleCUP
         */
        $planificacionCombustibleCUP = $em->getRepository('PortadoresBundle:PlanificacionCombustible')->findOneBy(array(
            'anno' => $registroCombustible->getFecha()->format('Y'),
            'vehiculoid' => $registroCombustible->getVehiculoid()->getId(),
            'visible' => true,
        ));

        /**
         * @var PlanificacionCombustibleCuc $planificacionCombustibleCUC
         */
        $planificacionCombustibleCUC = $em->getRepository('PortadoresBundle:PlanificacionCombustibleCuc')->findOneBy(array(
            'anno' => $registroCombustible->getFecha()->format('Y'),
            'vehiculoid' => $registroCombustible->getVehiculoid()->getId(),
            'visible' => true,
        ));


        if (is_null($planificacionCombustibleCUP)) {
            $planificacion['combustiblecup'] = 0;
            $planificacion['lubricantecup'] = 0;
            $planificacion['kmcup'] = 0;
        }
        if (is_null($planificacionCombustibleCUC)) {
            $planificacion['kmcuc'] = 0;
            $planificacion['lubricantecuc'] = 0;
            $planificacion['combustiblecuc'] = 0;
        }

        switch ($registroCombustible->getFecha()->format('m')) {
            case '01':
                if (!is_null($planificacionCombustibleCUP)) {
                    $planificacion['combustiblecup'] = $planificacionCombustibleCUP->getCombustibleLitrosEne();
                    $planificacion['lubricantecup'] = $planificacionCombustibleCUP->getLubricanteEne();
                    $planificacion['kmcup'] = $planificacionCombustibleCUP->getNivelActKmsEne();
                }

                if (!is_null($planificacionCombustibleCUC)) {
                    $planificacion['kmcuc'] = $planificacionCombustibleCUC->getNivelActKmsEne();
                    $planificacion['lubricantecuc'] = $planificacionCombustibleCUC->getLubricanteEne();
                    $planificacion['combustiblecuc'] = $planificacionCombustibleCUC->getCombustibleLitrosEne();
                }
                break;
            case '02':
                if (!is_null($planificacionCombustibleCUP)) {
                    $planificacion['combustiblecup'] = $planificacionCombustibleCUP->getCombustibleLitrosFeb();
                    $planificacion['lubricantecup'] = $planificacionCombustibleCUP->getLubricanteFeb();
                    $planificacion['kmcup'] = $planificacionCombustibleCUP->getNivelActKmsFeb();
                }
                if (!is_null($planificacionCombustibleCUC)) {
                    $planificacion['kmcuc'] = $planificacionCombustibleCUC->getNivelActKmsFeb();
                    $planificacion['lubricantecuc'] = $planificacionCombustibleCUC->getLubricanteFeb();
                    $planificacion['combustiblecuc'] = $planificacionCombustibleCUC->getCombustibleLitrosFeb();
                }
                break;
            case '03':
                if (!is_null($planificacionCombustibleCUP)) {
                    $planificacion['combustiblecup'] = $planificacionCombustibleCUP->getCombustibleLitrosMar();
                    $planificacion['lubricantecup'] = $planificacionCombustibleCUP->getLubricanteMar();
                    $planificacion['kmcup'] = $planificacionCombustibleCUP->getNivelActKmsMar();
                }
                if (!is_null($planificacionCombustibleCUC)) {
                    $planificacion['kmcuc'] = $planificacionCombustibleCUC->getNivelActKmsMar();
                    $planificacion['lubricantecuc'] = $planificacionCombustibleCUC->getLubricanteMar();
                    $planificacion['combustiblecuc'] = $planificacionCombustibleCUC->getCombustibleLitrosMar();
                }
                break;
            case '04':
                if (!is_null($planificacionCombustibleCUP)) {
                    $planificacion['combustiblecup'] = $planificacionCombustibleCUP->getCombustibleLitrosAbr();
                    $planificacion['lubricantecup'] = $planificacionCombustibleCUP->getLubricanteAbr();
                    $planificacion['kmcup'] = $planificacionCombustibleCUP->getNivelActKmsAbr();
                }
                if (!is_null($planificacionCombustibleCUC)) {
                    $planificacion['kmcuc'] = $planificacionCombustibleCUC->getNivelActKmsAbr();
                    $planificacion['lubricantecuc'] = $planificacionCombustibleCUC->getLubricanteAbr();
                    $planificacion['combustiblecuc'] = $planificacionCombustibleCUC->getCombustibleLitrosAbr();
                }
                break;
            case '05':
                if (!is_null($planificacionCombustibleCUP)) {
                    $planificacion['combustiblecup'] = $planificacionCombustibleCUP->getCombustibleLitrosMay();
                    $planificacion['lubricantecup'] = $planificacionCombustibleCUP->getLubricanteMay();
                    $planificacion['kmcup'] = $planificacionCombustibleCUP->getNivelActKmsMay();
                }
                if (!is_null($planificacionCombustibleCUC)) {
                    $planificacion['kmcuc'] = $planificacionCombustibleCUC->getNivelActKmsMay();
                    $planificacion['lubricantecuc'] = $planificacionCombustibleCUC->getLubricanteMay();
                    $planificacion['combustiblecuc'] = $planificacionCombustibleCUC->getCombustibleLitrosMay();
                }
                break;
            case '06':
                if (!is_null($planificacionCombustibleCUP)) {
                    $planificacion['combustiblecup'] = $planificacionCombustibleCUP->getCombustibleLitrosJun();
                    $planificacion['lubricantecup'] = $planificacionCombustibleCUP->getLubricanteJun();
                    $planificacion['kmcup'] = $planificacionCombustibleCUP->getNivelActKmsJun();
                }
                if (!is_null($planificacionCombustibleCUC)) {
                    $planificacion['kmcuc'] = $planificacionCombustibleCUC->getNivelActKmsJun();
                    $planificacion['lubricantecuc'] = $planificacionCombustibleCUC->getLubricanteJun();
                    $planificacion['combustiblecuc'] = $planificacionCombustibleCUC->getCombustibleLitrosJun();
                }
                break;
            case '07':
                if (!is_null($planificacionCombustibleCUP)) {
                    $planificacion['combustiblecup'] = $planificacionCombustibleCUP->getCombustibleLitrosJul();
                    $planificacion['lubricantecup'] = $planificacionCombustibleCUP->getLubricanteJul();
                    $planificacion['kmcup'] = $planificacionCombustibleCUP->getNivelActKmsJul();
                }
                if (!is_null($planificacionCombustibleCUC)) {
                    $planificacion['kmcuc'] = $planificacionCombustibleCUC->getNivelActKmsJul();
                    $planificacion['lubricantecuc'] = $planificacionCombustibleCUC->getLubricanteJul();
                    $planificacion['combustiblecuc'] = $planificacionCombustibleCUC->getCombustibleLitrosJul();
                }
                break;
            case '08':
                if (!is_null($planificacionCombustibleCUP)) {
                    $planificacion['combustiblecup'] = $planificacionCombustibleCUP->getCombustibleLitrosAgo();
                    $planificacion['lubricantecup'] = $planificacionCombustibleCUP->getLubricanteAgo();
                    $planificacion['kmcup'] = $planificacionCombustibleCUP->getNivelActKmsAgo();
                }
                if (!is_null($planificacionCombustibleCUC)) {
                    $planificacion['kmcuc'] = $planificacionCombustibleCUC->getNivelActKmsAgo();
                    $planificacion['lubricantecuc'] = $planificacionCombustibleCUC->getLubricanteAgo();
                    $planificacion['combustiblecuc'] = $planificacionCombustibleCUC->getCombustibleLitrosAgo();
                }
                break;
            case '09':
                if (!is_null($planificacionCombustibleCUP)) {
                    $planificacion['combustiblecup'] = $planificacionCombustibleCUP->getCombustibleLitrosSep();
                    $planificacion['lubricantecup'] = $planificacionCombustibleCUP->getLubricanteSep();
                    $planificacion['kmcup'] = $planificacionCombustibleCUP->getNivelActKmsSep();
                }
                if (!is_null($planificacionCombustibleCUC)) {
                    $planificacion['kmcuc'] = $planificacionCombustibleCUC->getNivelActKmsSep();
                    $planificacion['lubricantecuc'] = $planificacionCombustibleCUC->getLubricanteSep();
                    $planificacion['combustiblecuc'] = $planificacionCombustibleCUC->getCombustibleLitrosSep();
                }
                break;
            case '10':
                if (!is_null($planificacionCombustibleCUP)) {
                    $planificacion['combustiblecup'] = $planificacionCombustibleCUP->getCombustibleLitrosOct();
                    $planificacion['lubricantecup'] = $planificacionCombustibleCUP->getLubricanteOct();
                    $planificacion['kmcup'] = $planificacionCombustibleCUP->getNivelActKmsOct();
                }
                if (!is_null($planificacionCombustibleCUC)) {
                    $planificacion['kmcuc'] = $planificacionCombustibleCUC->getNivelActKmsOct();
                    $planificacion['lubricantecuc'] = $planificacionCombustibleCUC->getLubricanteOct();
                    $planificacion['combustiblecuc'] = $planificacionCombustibleCUC->getCombustibleLitrosOct();
                }
                break;
            case '11':
                if (!is_null($planificacionCombustibleCUP)) {
                    $planificacion['combustiblecup'] = $planificacionCombustibleCUP->getCombustibleLitrosNov();
                    $planificacion['lubricantecup'] = $planificacionCombustibleCUP->getLubricanteNov();
                    $planificacion['kmcup'] = $planificacionCombustibleCUP->getNivelActKmsNov();
                }
                if (!is_null($planificacionCombustibleCUC)) {
                    $planificacion['kmcuc'] = $planificacionCombustibleCUC->getNivelActKmsNov();
                    $planificacion['lubricantecuc'] = $planificacionCombustibleCUC->getLubricanteNov();
                    $planificacion['combustiblecuc'] = $planificacionCombustibleCUC->getCombustibleLitrosNov();
                }
                break;
            case '12':
                if (!is_null($planificacionCombustibleCUP)) {
                    $planificacion['combustiblecup'] = $planificacionCombustibleCUP->getCombustibleLitrosDic();
                    $planificacion['lubricantecup'] = $planificacionCombustibleCUP->getLubricanteDic();
                    $planificacion['kmcup'] = $planificacionCombustibleCUP->getNivelActKmsDic();
                }
                if (!is_null($planificacionCombustibleCUC)) {
                    $planificacion['kmcuc'] = $planificacionCombustibleCUC->getNivelActKmsDic();
                    $planificacion['lubricantecuc'] = $planificacionCombustibleCUC->getLubricanteDic();
                    $planificacion['combustiblecuc'] = $planificacionCombustibleCUC->getCombustibleLitrosDic();
                }
                break;
        }

        return $planificacion;
    }

    // ya
    public function printAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = $request->request->get('id');
        $registroCombustible = $em->getRepository('PortadoresBundle:RegistroCombustible')->find($id);
        $conceptos = $em->getRepository('PortadoresBundle:ConceptoRegistroCombustible')->findByVisible(true);

        $piefirma = $em->getRepository('PortadoresBundle:PieFirma')->findOneBy(array(
            'documento' => DocumentosEnum::registroCombustible,
            'nunidadid' => $registroCombustible
        ));

        $_html = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
        <html xmlns='http://www.w3.org/1999/xhtml'>
        <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <title>REGISTRO DE COMBUSTIBLE</title>
        <style>

            table.main {
                border:0px solid;
                border-radius:5px 5px 5px 5px;
                font-family: 'Times New Roman', Times, serif;
                font-size: 10px;
            }

            table {
                border:0 solid;
                border-radius:0;
                font-family: 'Arial', serif;
                font-size: 8px;
                border-collapse: collapse;
            }

            td{
                width: 40px;
                height: 11px;
                font-weight: bold;
            }

            table.fecha td{
                width: 20px;
                border:1px solid;
                border-collapse: collapse;
            }

            .sinborde{
                border-color:#FFF;
            }
            .bordearriba{
                border-top:#000;
            }
        table.main1 {	border:0px solid;
            border-radius:5px 5px 5px 5px;
            font-family: 'Times New Roman', Times, serif;
            font-size: 10px;
        }
        </style>
        </head>

        <body>
        <table cellspacing='0' cellpadding='0' border='1' width='100%'>
         <tr>
            <td colspan='100' style='text-align: center;border-bottom: 0;'><strong>MINFAR - GEOCUBA</strong></td>
            <td colspan='180' style='text-align: center;border-bottom: 0;'><strong>REGISTRO DE COMBUSTIBLE Y LUBRICANTE POR FUENTE DE ABASTECIMIENTO</strong></td>
            <td colspan='80' style='text-align: center;border-bottom: 0;'><strong>DENOMICACIÓN</strong></td>
            <td colspan='60' style='text-align: left'><strong>APROBADO POR:</strong></td>
         </tr>

         <tr>
            <td colspan='100' style='text-align: left;border-top: 0;border-bottom: 0;'><strong>ENTIDAD: </strong></td>
            <td colspan='180' style='text-align: left;border-top: 0;border-bottom: 0;'></td>
            <td colspan='80' style='text-align: center;border-top: 0;border-bottom: 0;'><strong></strong></td>
            <td colspan='30' style='text-align: center'><strong>FECHA</strong></td>
            <td colspan='30' style='text-align: center;border-bottom: 0;' ><strong>No. REGISTRO: " . $registroCombustible->getVehiculoid()->getNroOrden() . "</strong></td>

         </tr>

         <tr>
            <td colspan='100' style='text-align: left;border-top: 0;border-bottom: 0;'><strong>UM COMB. Y LUB:  LT</strong></td>
            <td colspan='180' style='text-align: left;border-top: 0;border-bottom: 0;'><strong>PARA EL MES: </strong><strong>" . $this->getMesEspannol($registroCombustible->getFecha()->format('m')) . " del " . $registroCombustible->getFecha()->format('Y') . "</strong></td>
            <td colspan='80' style='text-align: center;border-top: 0;border-bottom: 0;'><strong>  " . $registroCombustible->getVehiculoid()->getNdenominacionVehiculoid()->getNombre() . "</strong></td>
            <td colspan='10' style='text-align: center'><strong>D</strong></td>
            <td colspan='10' style='text-align: center'><strong>M</strong></td>
            <td colspan='10' style='text-align: center'><strong>A</strong></td>
            <td rowspan='2' colspan='30' style='text-align: center;border-top: 0;' ><strong></strong></td>
         </tr>

         <tr>
            <td colspan='100' style='text-align: left;border-top: 0;'><strong>UM MOTORR:  KM (MH)</strong></td>
            <td colspan='180' style='text-align: center;border-top: 0;'><strong></strong></td>
            <td colspan='80' style='text-align: center;border-top: 0;'><strong></strong></td>
            <td colspan='10' style='text-align: center'><strong></strong></td>
            <td colspan='10' style='text-align: center'><strong></strong></td>
            <td colspan='10' style='text-align: center'><strong></strong></td>
         </tr>

         <tr>
            <td colspan='100' style='text-align: left'><strong>MARCA: </strong>" . $registroCombustible->getVehiculoid()->getNmodeloid()->getMarcaVehiculoid()->getNombre() . "</td>
            <td colspan='180' style='text-align: left'><strong>SERIE: </strong>" . $registroCombustible->getVehiculoid()->getNroSerieCarreceria() . "</td>
            <td colspan='80' style='text-align: left;font-size:10px;'><strong>MATRÍCULA: " . $registroCombustible->getVehiculoid()->getMatricula() . "</strong></td>
            <td colspan='60' style='text-align: left'><strong>INVENTARIO: </strong>" . $registroCombustible->getVehiculoid()->getNroInventario() . "</td>
         </tr>

         <tr>
            <td colspan='140' style='text-align: center'><strong>KILOMETROS (MH)</strong></td>
            <td colspan='180' style='text-align: center'><strong>COMBUSTIBLE PLANIFICADO PARA EL MES</strong></td>
            <td colspan='100' style='text-align: center'><strong>LUBRICANTE PLANIFICADO PARA EL MES</strong></td>
         </tr>";

        $analisis_del_mes = $em->getRepository('PortadoresBundle:RegistroCombustibleAnalisis')->findBy(array(
            'conceptoid' => 3,
            'registroCombustible' => $registroCombustible->getId(),
            'visible' => true,
        ));
        $real = 0.0;
        foreach ($analisis_del_mes as $analisis) {
            $real += $analisis->getKm();
        }

        $norma = $registroCombustible->getVehiculoid()->getNormaFar();

        $planificados = $this->getPlanificacion($em, $registroCombustible);

        $totalRecibidoCUPPlanificado = $planificados['combustiblecup'];
        $totalRecibidoCUCPlanificado = $planificados['combustiblecuc'];
        $totalLubricanteCUPPlanificado = $planificados['lubricantecup'];

        $total = $totalRecibidoCUPPlanificado + $totalRecibidoCUCPlanificado;
        $totalkmCUPPlanificado = $planificados['kmcup'];
        $totalKmCUCPlanificado = $planificados['kmcuc'];

        $_html .= "<tr>
            <td colspan='80' style='text-align: left;border-bottom: 0;border-right: 0;'><strong>PLANIFICADOS: <u>" . ($totalkmCUPPlanificado + $totalKmCUCPlanificado) . "</u></strong></td>
            <td colspan='60' style='text-align: left;border-bottom: 0;border-left: 0;'><strong>REALES: <u>" . number_format($real, 2, '.', '') . "</u></strong></td>
            <td colspan='60' style='text-align: left;border-bottom: 0;border-right: 0;'><strong>TOTAL: <u>" . ($totalRecibidoCUPPlanificado + $totalRecibidoCUCPlanificado) . "</u></strong></td>
            <td colspan='60' style='text-align: left;border-bottom: 0;border-left: 0;border-right: 0;'><strong></strong></td>
            <td colspan='60' style='text-align: left;border-bottom: 0;border-left: 0;'><strong>TIPO: <u>" . $registroCombustible->getVehiculoid()->getNtipoCombustibleid()->getNombre() . "</u></strong></td>
           <td colspan='50' style='text-align: left;border-bottom: 0;border-right: 0;'><strong>TOTAL: <u>" . $totalLubricanteCUPPlanificado . "</u></strong></td>
           <td colspan='50' style='text-align: left;border-bottom: 0;border-left: 0;'><strong>TIPO: ____</strong></td>
         </tr>
         <tr>
            <td colspan='50' style='text-align: left;border-bottom: 0;border-top: 0;border-right: 0;'><strong>FAR: <u>" . $totalkmCUPPlanificado . "</u></strong></td>
            <td colspan='50' style='text-align: left;border-bottom: 0;border-top: 0;border-left: 0;border-right: 0;'><strong>MLC: <u>" . $totalKmCUCPlanificado . "</u></strong></td>
            <td colspan='40' style='text-align: left;border-bottom: 0;border-top: 0;border-left: 0;'><strong></strong></td>
            <td colspan='60' style='text-align: left;border-bottom: 0;border-top: 0;border-right: 0;'><strong>SERV. FAR: <u>" . $totalRecibidoCUPPlanificado . "</u></strong></td>
            <td colspan='60' style='text-align: left;border-bottom: 0;border-top: 0;border-left: 0;border-right: 0;'><strong>MLC: <u>" . $totalRecibidoCUCPlanificado . "</u></strong></td>
            <td colspan='60' style='text-align: left;border-bottom: 0;border-top: 0;border-left: 0;'><strong></strong></td>
           <td colspan='50' style='text-align: left;border-bottom: 0;border-top: 0;border-right: 0;'><strong>SERV. FAR: ____</strong></td>
           <td colspan='50' style='text-align: left;border-bottom: 0;border-top: 0;border-left: 0;'><strong>MLC: ____</strong></td>
         </tr>
         <tr>
           <td colspan='50' style='text-align: left;border-top: 0;border-right: 0;'><strong>B. GOBIERNO: ____</strong></td>
           <td colspan='50' style='text-align: left;border-top: 0;border-left: 0;border-right: 0;'><strong>COMB. FÍS: ____</strong></td>
           <td colspan='40' style='text-align: left;border-top: 0;border-left: 0;'><strong>TRANS: ____</strong></td>
           <td colspan='60' style='text-align: left;border-top: 0;border-right: 0;'><strong>B. GOBIERNO: ____</strong></td>
           <td colspan='60' style='text-align: left;border-top: 0;border-left: 0;border-right: 0;'><strong>COMB. FÍS: ____</strong></td>
           <td colspan='60' style='text-align: left;border-top: 0;border-left: 0;'><strong>TRANS: ____</strong></td>
           <td colspan='50' style='text-align: left;border-top: 0;border-right: 0;'><strong>TRANS: ____</strong></td>
           <td colspan='50' style='text-align: left;border-top: 0;border-left: 0;'><strong>TALLER: ____</strong></td>
         </tr>
          <tr>
           <td rowspan='3' colspan='20' style='text-align: center'><strong>FECHA</strong></td>
           <td colspan='60' style='text-align: center'><strong>SERVICENTRO FAR</strong></td>
           <td colspan='60' style='text-align: center'><strong>BONOS GOBIERNO</strong></td>
           <td colspan='60' style='text-align: center'><strong>TARJETA MLC</strong></td>
           <td colspan='60' style='text-align: center'><strong>COMBUSTIBLE FÍSICO</strong></td>
           <td colspan='120' style='text-align: center'><strong>ABASTECIMIENTO TRÁNSITO</strong></td>
           <td colspan='40' style='text-align: center'><strong>TOTAL</strong></td>
         </tr>
         <tr>
           <td rowspan='2' colspan='20' style='text-align: center'><strong>Recibido</strong></td>
           <td rowspan='2' colspan='20' style='text-align: center'><strong>Saldo</strong></td>
           <td rowspan='2' colspan='20' style='text-align: center'><strong>Firma</strong></td>
           <td rowspan='2' colspan='20' style='text-align: center'><strong>Recibido</strong></td>
           <td rowspan='2' colspan='20' style='text-align: center'><strong>Saldo</strong></td>
           <td rowspan='2' colspan='20' style='text-align: center'><strong>Firma</strong></td>
           <td rowspan='2' colspan='20' style='text-align: center'><strong>Recibido</strong></td>
           <td rowspan='2' colspan='20' style='text-align: center'><strong>Saldo</strong></td>
           <td rowspan='2' colspan='20' style='text-align: center'><strong>Firma</strong></td>
           <td rowspan='2' colspan='20' style='text-align: center'><strong>Recibido</strong></td>
           <td rowspan='2' colspan='20' style='text-align: center'><strong>Saldo</strong></td>
           <td rowspan='2' colspan='20' style='text-align: center'><strong>Firma</strong></td>
           <td colspan='40' style='text-align: center'><strong>Bonos Militares</strong></td>
           <td colspan='40' style='text-align: center'><strong>Serv. Far</strong></td>
           <td colspan='40' style='text-align: center'><strong>Tarjeta MLC</strong></td>
           <td rowspan='2' colspan='20' style='text-align: center'><strong>Recibido</strong></td>
           <td rowspan='2' colspan='20' style='text-align: center'><strong>Saldo</strong></td>
         </tr>
         <tr>
           <td colspan='20' style='text-align: center'><strong>Recibido</strong></td>
           <td colspan='20' style='text-align: center'><strong>Firma</strong></td>
           <td colspan='20' style='text-align: center'><strong>Recibido</strong></td>
           <td colspan='20' style='text-align: center'><strong>Firma</strong></td>
           <td colspan='20' style='text-align: center'><strong>Recibido</strong></td>
           <td colspan='20' style='text-align: center'><strong>Firma</strong></td>
         </tr>";

        $totalRecibidoCUP = 0.0;
        $totalRecibidoCUC = 0.0;
        $anno = $registroCombustible->getFecha()->format('Y');
        $mes = $registroCombustible->getFecha()->format('m');
        $fechaDesde = $anno . '-' . $mes . '-' . '1' . ' ' . '00:00:00';
        $fechaHasta = FechaUtil::getUltimoDiaMes($mes, $anno);
        $liquidaciones = $em->getRepository('PortadoresBundle:Liquidacion')->buscarLiquidacionRegistroCombustible($registroCombustible->getVehiculoid()->getId(), $fechaDesde, $fechaHasta);

        foreach ($liquidaciones as $liquidacion) {
            $_html .= "<tr>
                       <td colspan='20' style='text-align: center'>" . $liquidacion->getFechaVale()->format('d-m') . "</td>
                       <td colspan='20' style='text-align: center'></td>
                       <td colspan='20' style='text-align: center'></td>
                       <td colspan='20' style='text-align: center'></td>";

            if ($liquidacion->getNtarjetaid()->getMonedaid()->getId() == MonedaEnum::cup) //CUP
            {
                $totalRecibidoCUPPlanificado -= $liquidacion->getCantLitros();
                $_html .= "<td colspan='20' style='text-align: center'>" . $liquidacion->getCantLitros() . "</td>
                       <td colspan='20' style='text-align: center'>" . $totalRecibidoCUPPlanificado . "</td>
                       <td colspan='20' style='text-align: center'></td>
                       <td colspan='20' style='text-align: center'></td>
                       <td colspan='20' style='text-align: center'></td>
                       <td colspan='20' style='text-align: center'></td>";

                $totalRecibidoCUP += floatval($liquidacion->getCantLitros());
            } else {
                $totalRecibidoCUCPlanificado -= $liquidacion->getCantLitros();
                $_html .= "<td colspan='20' style='text-align: center'></td>
                       <td colspan='20' style='text-align: center'></td>
                       <td colspan='20' style='text-align: center'></td>
                       <td colspan='20' style='text-align: center'>" . $liquidacion->getCantLitros() . "</td>
                       <td colspan='20' style='text-align: center'>" . round($totalRecibidoCUCPlanificado, 2) . "</td>
                       <td colspan='20' style='text-align: center'></td>";
                $totalRecibidoCUC += floatval($liquidacion->getCantLitros());
            }

            $_html .= "<td colspan='20' style='text-align: center'></td>
                       <td colspan='20' style='text-align: center'></td>
                       <td colspan='20' style='text-align: center'></td>
                       <td colspan='20' style='text-align: center'></td>
                       <td colspan='20' style='text-align: center'></td>
                       <td colspan='20' style='text-align: center'></td>
                       <td colspan='20' style='text-align: center'></td>
                       <td colspan='20' style='text-align: center'></td>
                       <td colspan='20' style='text-align: center'></td>";

            $total = $totalRecibidoCUCPlanificado + $totalRecibidoCUPPlanificado;


            if ($liquidacion->getNtarjetaid()->getMonedaid()->getId() == MonedaEnum::cup) //CUP
            {
                $_html .= "<td colspan='20' style='text-align: center'>" . $liquidacion->getCantLitros() . "</td>
                        <td colspan='20' style='text-align: center'>" . round($total, 2) . "</td>
                    </tr>";
            } else {
                $_html .= "<td colspan='20' style='text-align: center'>" . $liquidacion->getCantLitros() . "</td>
                        <td colspan='20' style='text-align: center'>" . round($total, 2) . "</td>
                    </tr>";
            }
        }

        $strTotalRecibidoCUP = ($totalRecibidoCUP != 0) ? number_format($totalRecibidoCUP, 2, '.', '') : '';
        $strTotalRecibidoCUC = ($totalRecibidoCUC != 0) ? number_format($totalRecibidoCUC, 2, '.', '') : '';
        $strTotal = ($totalRecibidoCUP + $totalRecibidoCUC != 0) ? number_format($totalRecibidoCUP + $totalRecibidoCUC, 2, '.', '') : '';
        $_html .= "<tr>
           <td colspan='20' style='text-align: center'><strong>TOTALES</strong></td>
           <td colspan='20' style='text-align: center'></td>
           <td colspan='20' style='text-align: center'></td>
           <td colspan='20' style='text-align: center'></td>
           <td colspan='20' style='text-align: center'>" . round($strTotalRecibidoCUP, 2) . "</td>
           <td colspan='20' style='text-align: center'></td>
           <td colspan='20' style='text-align: center'></td>
           <td colspan='20' style='text-align: center'>" . round($strTotalRecibidoCUC, 2) . "</td>
           <td colspan='20' style='text-align: center'></td>
           <td colspan='20' style='text-align: center'></td>
           <td colspan='20' style='text-align: center'></td>
           <td colspan='20' style='text-align: center'></td>
           <td colspan='20' style='text-align: center'></td>
           <td colspan='20' style='text-align: center'></td>
           <td colspan='20' style='text-align: center'></td>
           <td colspan='20' style='text-align: center'></td>
           <td colspan='20' style='text-align: center'></td>
           <td colspan='20' style='text-align: center'></td>
           <td colspan='20' style='text-align: center'></td>
           <td colspan='20' style='text-align: center'>" . round($strTotal, 2) . "</td>
           <td colspan='20' style='text-align: center'></td>
         </tr>

         <tr>
           <td colspan='180' style='text-align: left;border-right: 0;'><strong>NORMA ESTABLECIDA COMB. PARA EL MES: " . number_format($norma, 2, '.', '') . " KM/LT</strong></td>
           <td colspan='140' style='text-align: left;border-left: 0;'><strong>ANALISIS DEL CONSUMO</strong></td>
           <td colspan='120' style='text-align: center'><strong>NORMA REAL DE COMB EN EL MES: _NORMA_REAL_ KM/LT</strong></td>
         </tr>

         ";

        $_html .= "<tr>
           <td rowspan='2' colspan='105' style='text-align: center'><strong>CONCEPTO</strong></td>";

        $date = $registroCombustible->getFecha();
        $day = $date->format('N');

        $numerosemana = 1;
        $di = 1;
        $df = $di + 7 - $day;

        $_html .= "
           <td colspan='30' style='text-align: left;border-right: 0;'><strong>Sem del: </strong> $di</td>
           <td colspan='15' style='text-align: left;border-left: 0;'><strong>al: </strong> $df</td>";

        if ($date->format('y') % 4 == 0) {
            $ultimosDias = array('01' => 31, '02' => 29, '03' => 31, '04' => 30, '05' => 31, '06' => 30,
                '07' => 31, '08' => 31, '09' => 30, '10' => 31, '11' => 30, '12' => 31);
        } else {
            $ultimosDias = array('01' => 31, '02' => 28, '03' => 31, '04' => 30, '05' => 31, '06' => 30,
                '07' => 31, '08' => 31, '09' => 30, '10' => 31, '11' => 30, '12' => 31);
        }

        $last = $ultimosDias[$date->format('m')];

        while ($df < $last) {

            $di = $df + 1;
            $df = $di + 6;

            if ($di + 6 > $last) {
                $df = $last;
            }
            $numerosemana++;

            $_html .= "
               <td colspan='30' style='text-align: left;border-right: 0;'><strong>Sem del: </strong> $di</td>
               <td colspan='15' style='text-align: left;border-left: 0;'><strong>al: </strong> $df</td>";
        }
        $semanasrestantesReal = $numerosemana;
        while ($numerosemana < 6) {
            $numerosemana++;
            $_html .= "
               <td colspan='30' style='text-align: left;border-right: 0;'><strong>Sem del:</strong></td>
               <td colspan='15' style='text-align: left;border-left: 0;'><strong>al:</strong></td>";
        }

        $_html .= "<td colspan='45' style='text-align: center'><strong>TOTAL</strong></td>
            </tr>";

        $_html .= "<tr>
           <td colspan='15' style='text-align: center'><strong>Comb.</strong></td>
           <td colspan='15' style='text-align: center'><strong>Lub.</strong></td>
           <td colspan='15' style='text-align: center'><strong>Km(Mh)</strong></td>
           <td colspan='15' style='text-align: center'><strong>Comb.</strong></td>
           <td colspan='15' style='text-align: center'><strong>Lub.</strong></td>
           <td colspan='15' style='text-align: center'><strong>Km(Mh)</strong></td>
           <td colspan='15' style='text-align: center'><strong>Comb.</strong></td>
           <td colspan='15' style='text-align: center'><strong>Lub.</strong></td>
           <td colspan='15' style='text-align: center'><strong>Km(Mh)</strong></td>
           <td colspan='15' style='text-align: center'><strong>Comb.</strong></td>
           <td colspan='15' style='text-align: center'><strong>Lub.</strong></td>
           <td colspan='15' style='text-align: center'><strong>Km(Mh)</strong></td>
           <td colspan='15' style='text-align: center'><strong>Comb.</strong></td>
           <td colspan='15' style='text-align: center'><strong>Lub.</strong></td>
           <td colspan='15' style='text-align: center'><strong>Km(Mh)</strong></td>
           <td colspan='15' style='text-align: center'><strong>Comb.</strong></td>
           <td colspan='15' style='text-align: center'><strong>Lub.</strong></td>
           <td colspan='15' style='text-align: center'><strong>Km(Mh)</strong></td>
           <td colspan='15' style='text-align: center'><strong>Comb.</strong></td>
           <td colspan='15' style='text-align: center'><strong>Lub.</strong></td>
           <td colspan='15' style='text-align: center'><strong>Km(Mh)</strong></td>
         </tr>";

        $flagInicial = false;
        $normaReal = 0.0;
        foreach ($conceptos as $concepto) {

            $_html .= "<tr>
                <td colspan='105' style='text-align: left'><strong>" . $concepto->getNombre() . "</strong></td>";

            $combustible = 0.0;
            $lubricante = 0.0;
            $km = 0.0;

            $semana = 0;
            while ($semana++ < $numerosemana) {
                $analisis = $em->getRepository('PortadoresBundle:RegistroCombustibleAnalisis')->findOneBy(array(
                    'numerosemana' => $semana,
                    'conceptoid' => $concepto->getId(),
                    'registroCombustible' => $registroCombustible->getId(),
                    'visible' => true,
                ));
                if (!is_null($analisis)) {

                    if ($concepto->getId() == 5) {
                        $signo_combustible = ($analisis->getCombustible() > 0) ? '+' : '';
                        $signo_km = ($analisis->getKm() > 0) ? '+' : '';

//                        $strCombustible = ($analisis->getCombustible() != '0.00') ? $analisis->getCombustible() : '';
//                        $strLubricante = ($analisis->getLubricante() != '0.00') ? $analisis->getLubricante() : '';
//                        $strKm = ($analisis->getKm() != '0.00') ? $analisis->getKm() : '';
                        $strCombustible = $analisis->getCombustible();
                        $strLubricante = $analisis->getLubricante();
                        $strKm = $analisis->getKm();
                        $_html .= "<td colspan='15' style='text-align: center'>" . $signo_combustible . $strCombustible . "</td>
                       <td colspan='15' style='text-align: center'>" . $strLubricante . "</td>
                       <td colspan='15' style='text-align: center'>" . $signo_km . $strKm . "</td>";
                    } else {

//                        $strCombustible = ($analisis->getCombustible() != '0.00') ? $analisis->getCombustible() : '';
//                        $strLubricante = ($analisis->getLubricante() != '0.00') ? $analisis->getLubricante() : '';
//                        $strKm = ($analisis->getKm() != '0.00') ? $analisis->getKm() : '';
                        $strCombustible = $analisis->getCombustible();
                        $strLubricante = $analisis->getLubricante();
                        $strKm = $analisis->getKm();
                        $_html .= "
                       <td colspan='15' style='text-align: center'>" . $strCombustible . "</td>
                       <td colspan='15' style='text-align: center'>" . $strLubricante . "</td>
                       <td colspan='15' style='text-align: center'>" . $strKm . "</td>";
                    }

                    if ($concepto->getId() == 1 && !$flagInicial) {
                        $combustible = $analisis->getCombustible();
                        $lubricante = $analisis->getLubricante();
                        $km = $analisis->getKm();
                        $flagInicial = true;
                    }
                    if ($concepto->getId() == 4 && $analisis->getCombustible() != 0) {
                        $combustible = $analisis->getCombustible();
                        $lubricante = $analisis->getLubricante();
                        $km = $analisis->getKm();
                    }
                    if ($concepto->getId() != 1 && $concepto->getId() != 4) {
                        $combustible += $analisis->getCombustible();
                        $lubricante += $analisis->getLubricante();
                        $km += $analisis->getKm();
                    }
                } else {
                    $_html .= "
                       <td colspan='15' style='text-align: center'></td>
                       <td colspan='15' style='text-align: center'></td>
                       <td colspan='15' style='text-align: center'></td>";
                }
            }

            if ($concepto->getId() == 5) {
                $signo_combustible = ($combustible > 0) ? '+' : '';
                $signo_km = ($km > 0) ? '+' : '';

//                $strCombustible = ($combustible != 0) ? number_format($combustible, 2) : '';
//                $strLubricante = ($lubricante != 0) ? number_format($lubricante, 2) : '';
//                $strKm = ($km != 0) ? number_format($km, 2) : '';
                $strCombustible = number_format($combustible, 2, '.', '');
                $strLubricante = number_format($lubricante, 2, '.', '');
                $strKm = number_format($km, 2, '.', '');
                $_html .= "<td colspan='15' style='text-align: center'>" . $signo_combustible . $strCombustible . "</td>
                       <td colspan='15' style='text-align: center'>" . $strLubricante . "</td>
                       <td colspan='15' style='text-align: center'>" . $signo_km . $strKm . "</td>";
            } else {
//                $strCombustible = ($combustible != 0) ? number_format($combustible, 2) : '';
//                $strLubricante = ($lubricante != 0) ? number_format($lubricante, 2) : '';
//                $strKm = ($km != 0) ? number_format($km, 2) : '';
                $strCombustible = number_format($combustible, 2, '.', '');
                $strLubricante = number_format($lubricante, 2, '.', '');
                $strKm = number_format($km, 2, '.', '');
                $_html .= "<td colspan='15' style='text-align: center'>" . $strCombustible . "</td>
                       <td colspan='15' style='text-align: center'>" . $strLubricante . "</td>
                       <td colspan='15' style='text-align: center'>" . $strKm . "</td>";
            }
            $_html .= "</tr>";

            if ($concepto->getId() == 3 && $km != 0)
                $normaReal = $combustible * 100 / $km;
        }

        $_html = str_replace('_NORMA_REAL_', number_format($normaReal, 2, '.', ''), $_html);

        $_html .= "<tr>
           <td colspan='150' style='text-align: center;border-bottom: 0;'><strong>REVISADO POR: ___________________ FIRMA:</strong></td>
           <td colspan='15' style='text-align: center'><strong>D</strong></td>
           <td colspan='15' style='text-align: center'><strong>M</strong></td>
           <td colspan='15' style='text-align: center'><strong>A</strong></td>
           <td colspan='180' style='text-align: center;border-bottom: 0;'><strong>ELABORADO POR: ___________________ FIRMA: ______________ CARGO: Téc.A.Producción</strong></td>
           <td colspan='15' style='text-align: center'><strong>D</strong></td>
           <td colspan='15' style='text-align: center'><strong>M</strong></td>
           <td colspan='15' style='text-align: center'><strong>A</strong></td>
         </tr>

         <tr>
           <td colspan='150' style='text-align: center;border-top: 0;'><strong>CARGO: Esp. de Transporte</strong></td>
           <td colspan='15' style='text-align: center'><strong></strong></td>
           <td colspan='15' style='text-align: center'><strong></strong></td>
           <td colspan='15' style='text-align: center'><strong></strong></td>
           <td colspan='180' style='text-align: center;border-top: 0;'></td>
           <td colspan='15' style='text-align: center'><strong></strong></td>
           <td colspan='15' style='text-align: center'><strong></strong></td>
           <td colspan='15' style='text-align: center'><strong></strong></td>
         </tr>

        ";

        $_html .= "</table>
        </body>
        </html>";

        return new Response(json_encode(array('success' => true, 'html' => $_html)));
    }

    // ya
    public function loadPlanificacionAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = $request->get('id');
        $start = $request->get('start');
        $limit = $request->get('limit');

        $qb = $em->createQueryBuilder();
        $qb->select('registroCombustiblePlanificacion')
            ->from('PortadoresBundle:RegistroCombustiblePlanificacion', 'registroCombustiblePlanificacion')
            ->Where('registroCombustiblePlanificacion.visible = true')
            ->andWhere('registroCombustiblePlanificacion.registroCombustible = :registroCombustible')
            ->setParameter('registroCombustible', $id);

        $entities = $qb->orderBy('registroCombustiblePlanificacion.fecha', 'ASC')
            ->getQuery()
            ->setMaxResults($limit)
            ->setFirstResult($start)
            ->getResult();

        $qb1 = $em->createQueryBuilder();
        $qb1->select('count(registroCombustiblePlanificacion)')
            ->from('PortadoresBundle:RegistroCombustiblePlanificacion', 'registroCombustiblePlanificacion')
            ->Where('registroCombustiblePlanificacion.visible = true')
            ->andWhere('registroCombustiblePlanificacion.registroCombustible = :registroCombustible')
            ->setParameter('registroCombustible', $id);

        $count = $qb1->getQuery()->getSingleScalarResult();

        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'fecha_planificacion' => $entity->getFecha()->format('d/m/Y'),
                'monedaid' => $entity->getMonedaid()->getId(),
                'monedanombre' => $entity->getMonedaid()->getNombre(),
                'recibido' => $entity->getRecibido(),
//                'saldo' => $entity->getSaldo(),
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $count));
    }

    /*    public function addPlanificacionAction(Request $request)
        {
            $em = $this->getDoctrine()->getManager();

            $registroid = $request->get('registroid');
            $registroCombustible = $em->getRepository('PortadoresBundle:RegistroCombustible')->find($registroid);

            $_date = str_replace('/', '-', $request->get('fecha_planificacion'));
            $fecha_registro = new \DateTime($_date);
            $fecha_registro = new \DateTime($fecha_registro->format('Y-m-d'));

            $moneda_id = $request->get('monedaid');
            $recibido = $request->get('recibido');
            $saldo = $request->get('saldo');

            $entity = new RegistroCombustiblePlanificacion();
            $entity->setFecha($fecha_registro);
            $entity->setMonedaid($em->getRepository('PortadoresBundle:Nmoneda')->find($moneda_id));
            $entity->setRegistroCombustible($registroCombustible);
            $entity->setRecibido($recibido);
    //        $entity->setSaldo($saldo);
            $entity->setVisible(true);

            $em->persist($entity);


            $date = new \DateTime('01-' . $fecha_registro->format('m') . '-' . $fecha_registro->format('Y'));
            $day = $date->format('N');
            $numerosemana = -1;
            $di = 0;
            $df = 0;

            if ($fecha_registro->format('Y') % 4 == 0) {
                $ultimosDias = array('01' => 31, '02' => 29, '03' => 31, '04' => 30, '05' => 31, '06' => 30,
                    '07' => 31, '08' => 31, '09' => 30, '10' => 31, '11' => 30, '12' => 31);
            } else {
                $ultimosDias = array('01' => 31, '02' => 28, '03' => 31, '04' => 30, '05' => 31, '06' => 30,
                    '07' => 31, '08' => 31, '09' => 30, '10' => 31, '11' => 30, '12' => 31);
            }
            $last = $ultimosDias[$fecha_registro->format('m')];

            while ($df < $last) {

                if ($numerosemana == -1) {
                    $di = 1;
                    $df = $di + 7 - $day;
                } else {
                    $di = $df + 1;
                    $df = $di + 6;
                }

                if ($di + 6 > $last) {
                    $df = $last;
                }
                $numerosemana++;

                if (intval($fecha_registro->format('d')) >= $di && intval($fecha_registro->format('d')) <= $df)
                    break;
            }

            $entidadAnalisis = $em->getRepository('PortadoresBundle:RegistroCombustibleAnalisis')->findOneBy(array(
                'semana' => $di . '-' . $df,
                'conceptoid' => 2,
                'registroCombustible' => $registroid,
                'visible' => true
            ));

            if (is_null($entidadAnalisis)) {

                $entidadAnalisis = new RegistroCombustibleAnalisis();
                $entidadAnalisis->setSemana($di . '-' . $df);
                $entidadAnalisis->setNumerosemana($numerosemana + 1);
                $entidadAnalisis->setConceptoid($em->getRepository('PortadoresBundle:NconceptoRegistroCombustible')->find(2));
                $entidadAnalisis->setRegistroCombustible($registroCombustible);

                $entidadAnalisis->setCombustible($recibido);
                $entidadAnalisis->setLubricante(0);
                $entidadAnalisis->setKm(0);
                $entidadAnalisis->setVisible(true);

            } else {

                $entidadAnalisis->setCombustible($entidadAnalisis->getCombustible() + $recibido);
            }

            $em->persist($entidadAnalisis);

            try {
                $em->flush();
                return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Planificación adicionada con éxito.'));
            } catch (CommonException $ex) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
            }
        }

        public function modPlanificacionAction(Request $request)
        {
            $em = $this->getDoctrine()->getManager();

            $id = $request->get('id');

            $moneda_id = $request->get('monedaid');
            $recibido = $request->get('recibido');

            $entity = $em->getRepository('PortadoresBundle:RegistroCombustiblePlanificacion')->find($id);

            $recibido_anterior = $entity->getRecibido();

            $entity->setMonedaid($em->getRepository('PortadoresBundle:Nmoneda')->find($moneda_id));
            $entity->setRecibido($recibido);
            $entity->setVisible(true);

            $em->persist($entity);

            $fecha_registro = $entity->getFecha();
            $date = new \DateTime('01-' . $fecha_registro->format('m') . '-' . $fecha_registro->format('Y'));
            $day = $date->format('N');
            $numerosemana = -1;
            $di = 0;
            $df = 0;

            if ($fecha_registro->format('Y') % 4 == 0) {
                $ultimosDias = array('01' => 31, '02' => 29, '03' => 31, '04' => 30, '05' => 31, '06' => 30,
                    '07' => 31, '08' => 31, '09' => 30, '10' => 31, '11' => 30, '12' => 31);
            } else {
                $ultimosDias = array('01' => 31, '02' => 28, '03' => 31, '04' => 30, '05' => 31, '06' => 30,
                    '07' => 31, '08' => 31, '09' => 30, '10' => 31, '11' => 30, '12' => 31);
            }
            $last = $ultimosDias[$fecha_registro->format('m')];

            while ($df < $last) {

                if ($numerosemana == -1) {
                    $di = 1;
                    $df = $di + 7 - $day;
                } else {
                    $di = $df + 1;
                    $df = $di + 6;
                }

                if ($di + 6 > $last) {
                    $df = $last;
                }
                $numerosemana++;

                if (intval($fecha_registro->format('d')) >= $di && intval($fecha_registro->format('d')) <= $df)
                    break;
            }

            $entidadAnalisis = $em->getRepository('PortadoresBundle:RegistroCombustibleAnalisis')->findOneBy(array(
                'semana' => $di . '-' . $df,
                'conceptoid' => 2,
                'registroCombustible' => $entity->getRegistroCombustible()->getId(),
                'visible' => true
            ));

            if (is_null($entidadAnalisis)) {

                $entidadAnalisis = new RegistroCombustibleAnalisis();
                $entidadAnalisis->setSemana($di . '-' . $df);
                $entidadAnalisis->setNumerosemana($numerosemana + 1);
                $entidadAnalisis->setConceptoid($em->getRepository('PortadoresBundle:NconceptoRegistroCombustible')->find(2));
                $entidadAnalisis->setRegistroCombustible($entity->getRegistroCombustible());

                $entidadAnalisis->setCombustible($recibido);
                $entidadAnalisis->setLubricante(0);
                $entidadAnalisis->setKm(0);
                $entidadAnalisis->setVisible(true);

            } else {

                $entidadAnalisis->setCombustible($entidadAnalisis->getCombustible() - $recibido_anterior + $recibido);
            }

            $em->persist($entidadAnalisis);

            try {
                $em->flush();
                $response = new JsonResponse();
                $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Planificación modificada con éxito.'));
                return $response;
            } catch (CommonException $ex) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
            }
        }

        public function delPlanificacionAction(Request $request)
        {
            $em = $this->getDoctrine()->getManager();
            $id = $request->get('id');

            $entity = $em->getRepository('PortadoresBundle:RegistroCombustiblePlanificacion')->find($id);
            $entity->setVisible(false);

            $em->persist($entity);

            $fecha_registro = $entity->getFecha();

            $date = new \DateTime('01-' . $fecha_registro->format('m') . '-' . $fecha_registro->format('Y'));
            $day = $date->format('N');
            $numerosemana = -1;
            $di = 0;
            $df = 0;

            if ($fecha_registro->format('Y') % 4 == 0) {
                $ultimosDias = array('01' => 31, '02' => 29, '03' => 31, '04' => 30, '05' => 31, '06' => 30,
                    '07' => 31, '08' => 31, '09' => 30, '10' => 31, '11' => 30, '12' => 31);
            } else {
                $ultimosDias = array('01' => 31, '02' => 28, '03' => 31, '04' => 30, '05' => 31, '06' => 30,
                    '07' => 31, '08' => 31, '09' => 30, '10' => 31, '11' => 30, '12' => 31);
            }
            $last = $ultimosDias[$fecha_registro->format('m')];

            while ($df < $last) {

                if ($numerosemana == -1) {
                    $di = 1;
                    $df = $di + 7 - $day;
                } else {
                    $di = $df + 1;
                    $df = $di + 6;
                }

                if ($di + 6 > $last) {
                    $df = $last;
                }
                $numerosemana++;

                if (intval($fecha_registro->format('d')) >= $di && intval($fecha_registro->format('d')) <= $df)
                    break;
            }

            $entidadAnalisis = $em->getRepository('PortadoresBundle:RegistroCombustibleAnalisis')->findOneBy(array(
                'semana' => $di . '-' . $df,
                'conceptoid' => 2,
                'registroCombustible' => $entity->getRegistroCombustible()->getId(),
                'visible' => true
            ));

            if (!is_null($entidadAnalisis)) {

                $entidadAnalisis->setCombustible($entidadAnalisis->getCombustible() - $entity->getRecibido());

                $em->persist($entidadAnalisis);
            }

            try {
                $em->flush();
                return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Planificación eliminada con éxito.'));
            } catch (CommonException $ex) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
            }
        }*/

    // ya
    public function loadAnalisisAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = $request->get('id');
        $start = $request->get('start');
        $limit = $request->get('limit');

        $qb = $em->createQueryBuilder();
        $qb->select('registroCombustibleAnalisis')
            ->from('PortadoresBundle:RegistroCombustibleAnalisis', 'registroCombustibleAnalisis')
            ->Where('registroCombustibleAnalisis.visible = true')
            ->andWhere('registroCombustibleAnalisis.registroCombustible = :registroCombustible')
            ->setParameter('registroCombustible', $id);

        $entities = $qb->orderBy('registroCombustibleAnalisis.semana', 'ASC')
            ->getQuery()
            ->setMaxResults($limit)
            ->setFirstResult($start)
            ->getResult();

        $qb1 = $em->createQueryBuilder();
        $qb1->select('count(registroCombustibleAnalisis)')
            ->from('PortadoresBundle:RegistroCombustibleAnalisis', 'registroCombustibleAnalisis')
            ->Where('registroCombustibleAnalisis.visible = true')
            ->andWhere('registroCombustibleAnalisis.registroCombustible = :registroCombustible')
            ->setParameter('registroCombustible', $id);

        $count = $qb1->getQuery()->getSingleScalarResult();

        $_data = array();
        foreach ($entities as $entity) {
            /**
             * @var  RegistroCombustibleAnalisis $entity
             */
            $_data[] = array(
                'id' => $entity->getId(),
                'semana' => $entity->getSemana(),
                'numerosemana' => $entity->getNumerosemana(),
                'conceptoid' => $entity->getConceptoid()->getId(),
                'conceptonombre' => $entity->getConceptoid()->getNombre(),
                'combustible' => $entity->getCombustible(),
                'lubricante' => $entity->getLubricante(),
                'km' => $entity->getKm(),
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $count));
    }

    // ya
    public function loadAnalisisDataAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $_data = array();

        $registroid = $request->get('registroid');
        $registroCombustible = $em->getRepository('PortadoresBundle:RegistroCombustible')->find($registroid);

        $cInicial = 0;
        $lInicial = 0;
        $kInicial = 0;

        $qb = $em->createQuerybuilder();
        $registrosCombustibleAnterior = $qb
            ->select('registrocombustible')
            ->from('PortadoresBundle:RegistroCombustible', 'registrocombustible')
            ->where($qb->expr()->eq('registrocombustible.visible', 'true'))
            ->andWhere($qb->expr()->eq('registrocombustible.vehiculoid', ':vehiculo'))
            ->andWhere($qb->expr()->lt('registrocombustible.fecha', ':fecha'))
            ->setparameter('vehiculo', $registroCombustible->getVehiculoid()->getId())
            ->setparameter('fecha', $registroCombustible->getFecha())
            ->addOrderBy('registrocombustible.fecha', 'ASC')
            ->getQuery()->getResult();

        if (count($registrosCombustibleAnterior) > 0) {
            $i = 1;
            $iMax = \count($registrosCombustibleAnterior);
            $registrosCombustibleAnalisis = [];
            while ($i <= $iMax && \count($registrosCombustibleAnalisis) === 0) {
                $registrosCombustibleAnalisis = $em->getRepository('PortadoresBundle:RegistroCombustibleAnalisis')->findBy(array(
                    'conceptoid' => 4,
                    'registroCombustible' => $registrosCombustibleAnterior[$iMax - $i]->getId(),
                    'visible' => true
                ), array('numerosemana' => 'asc'));
                $i++;
            }


            if (count($registrosCombustibleAnalisis) > 0) {
                $cInicial = $registrosCombustibleAnalisis[count($registrosCombustibleAnalisis) - 1]->getCombustible();
                $lInicial = $registrosCombustibleAnalisis[count($registrosCombustibleAnalisis) - 1]->getLubricante();
                $kInicial = $registrosCombustibleAnalisis[count($registrosCombustibleAnalisis) - 1]->getKm();
            }
        }

        $date = new \DateTime('01-' . $registroCombustible->getFecha()->format('m') . '-' . $registroCombustible->getFecha()->format('Y'));
        $day = $date->format('N');

        if ($date->format('y') % 4 == 0) {
            $ultimosDias = array('01' => 31, '02' => 29, '03' => 31, '04' => 30, '05' => 31, '06' => 30,
                '07' => 31, '08' => 31, '09' => 30, '10' => 31, '11' => 30, '12' => 31);
        } else {
            $ultimosDias = array('01' => 31, '02' => 28, '03' => 31, '04' => 30, '05' => 31, '06' => 30,
                '07' => 31, '08' => 31, '09' => 30, '10' => 31, '11' => 30, '12' => 31);
        }
        $last = $ultimosDias[$date->format('m')];

        $j = 0;
        while ($j++ < 6) {

            $concepto = $em->getRepository('PortadoresBundle:ConceptoRegistroCombustible')->find($j);
            $_fila = array();

            $_fila[] = $concepto->getNombre();

            $numerosemana = -1;
            $di = 0;
            $df = 0;

            while ($df < $last) {

                if ($numerosemana == -1) {
                    $di = 1;
                    $df = $di + 7 - $day;
                } else {
                    $di = $df + 1;
                    $df = $di + 6;
                }

                if ($di + 6 > $last) {
                    $df = $last;
                }
                $numerosemana++;

                $entity = $em->getRepository('PortadoresBundle:RegistroCombustibleAnalisis')->findOneBy(array(
                    'semana' => $di . '-' . $df,
                    'conceptoid' => $j,
                    'registroCombustible' => $registroid,
                    'visible' => true
                ));

                if ($j == 1 && $di == 1) {
                    if (!is_null($entity)) {
                        $_fila[] = number_format($entity->getCombustible(), 2, '.', '');
                        $_fila[] = number_format($entity->getLubricante(), 2, '.', '');
                        $_fila[] = number_format($entity->getKm(), 2, '.', '');
                    } else {
                        $_fila[] = number_format($cInicial, 2, '.', '');
                        $_fila[] = number_format($lInicial, 2, '.', '');
                        $_fila[] = number_format($kInicial, 2, '.', '');
                    }
//                }elseif ($j == 2){
//                    if (!is_null($entity)) {
//                        $_fila[] = number_format($entity->getCombustible(), 2, '.', '');
//                        $_fila[] = number_format($entity->getLubricante(), 2, '.', '');
//                        $_fila[] = number_format($entity->getKm(), 2, '.', '');
//                    } else {
//                        $anno = $registroCombustible->getFecha()->format('Y');
//                        $mes = $registroCombustible->getFecha()->format('m');
//                        $fechaDesde = $anno . '-' . $mes . '-' . $di;
//                        $fechaHasta = $anno . '-' . $mes . '-' . $df . ' 23:59:59';
//                        $liquidaciones = $em->getRepository('PortadoresBundle:Liquidacion')->buscarLiquidacionRegistroCombustible($registroCombustible->getVehiculoid()->getId(),$fechaDesde,$fechaHasta);
//                        $cliquidacion = 0;
//                        foreach($liquidaciones as $liquidacion)
//                        {
//                            $cliquidacion += $liquidacion->getCantLitros();
//                        }
//
//                        $_fila[] = number_format($cliquidacion, 2, '.', '');
//                        $_fila[] = 0;//number_format($cliquidacion*0.015, 2, '.', '');
//                        $_fila[] = number_format($cliquidacion*$registroCombustible->getVehiculoid()->getNorma(), 2, '.', '');
//                    }
                } else {
                    if (!is_null($entity)) {
                        $_fila[] = number_format($entity->getCombustible(), 2, '.', '');
                        $_fila[] = number_format($entity->getLubricante(), 2, '.', '');
                        $_fila[] = number_format($entity->getKm(), 2, '.', '');
                    } else {
                        $_fila[] = number_format(0, 2);
                        $_fila[] = number_format(0, 2);
                        $_fila[] = number_format(0, 2);
                    }
                }

            }
            $_data[] = $_fila;
        }

        $j = 0;
        while ($j < $numerosemana && ($_data[0][$j * 3 + 1] == 0 && $_data[0][$j * 3 + 2] == 0 && $_data[0][$j * 3 + 3] == 0)) $j++;
        $_data[0] [] = number_format($_data[0][$j * 3 + 1], 2);
        $_data[0] [] = number_format($_data[0][$j * 3 + 2], 2);
        $_data[0] [] = number_format($_data[0][$j * 3 + 3], 2);

        $totalReciboComb = 0.0;
        $totalReciboLub = 0.0;
        $totalReciboKm = 0.0;
        for ($j = 0; $j <= $numerosemana; $j++) {
            $totalReciboComb += number_format($_data[1][$j * 3 + 1], 2, '.', '');
            $totalReciboLub += number_format($_data[1][$j * 3 + 2], 2, '.', '');
            $totalReciboKm += number_format($_data[1][$j * 3 + 3], 2, '.', '');
        }
        $_data[1] [] = number_format($totalReciboComb, 2);
        $_data[1] [] = number_format($totalReciboLub, 2);
        $_data[1] [] = number_format($totalReciboKm, 2);


        $totalRealComb = 0.0;
        $totalRealLub = 0.0;
        $totalRealKm = 0.0;
        for ($j = 0; $j <= $numerosemana; $j++) {
            $totalRealComb += number_format($_data[2][$j * 3 + 1], 2, '.', '');
            $totalRealLub += number_format($_data[2][$j * 3 + 2], 2, '.', '');
            $totalRealKm += number_format($_data[2][$j * 3 + 3], 2, '.', '');
        }
        $_data[2] [] = number_format($totalRealComb, 2);
        $_data[2] [] = number_format($totalRealLub, 2);
        $_data[2] [] = number_format($totalRealKm, 2);

        $j = $numerosemana;
        while ($j > 0 && ($_data[3][$j * 3 + 1] == 0 && $_data[3][$j * 3 + 2] == 0 && $_data[3][$j * 3 + 3] == 0)) $j--;
        $_data[3] [] = number_format($_data[3][$j * 3 + 1], 2);
        $_data[3] [] = number_format($_data[3][$j * 3 + 2], 2);
        $_data[3] [] = number_format($_data[3][$j * 3 + 3], 2);

        $totalAhorroComb = 0.0;
        $totalAhorroLub = 0.0;
        $totalAhorroKm = 0.0;
        for ($j = 0; $j <= $numerosemana; $j++) {
            $totalAhorroComb += number_format($_data[4][$j * 3 + 1], 2, '.', '');
            $totalAhorroLub += number_format($_data[4][$j * 3 + 2], 2, '.', '');
            $totalAhorroKm += number_format($_data[4][$j * 3 + 3], 2, '.', '');
        }
        $_data[4] [] = number_format($totalAhorroComb, 2);
        $_data[4] [] = number_format($totalAhorroLub, 2);
        $_data[4] [] = number_format($totalAhorroKm, 2);

        $totalCombNorma = 0.0;
        $totalLubNorma = 0.0;
        $totalKmNorma = 0.0;
        for ($j = 0; $j <= $numerosemana; $j++) {
            $totalCombNorma += number_format($_data[5][$j * 3 + 1], 2, '.', '');
        }
        $_data[5] [] = number_format($totalCombNorma, 2);
        $_data[5] [] = number_format($totalLubNorma, 2);
        $_data[5] [] = number_format($totalKmNorma, 2);

        return new JsonResponse(array('rows' => $_data));
    }

    // ya
    public function addAnalisisAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $registroid = $request->get('registroid');

        $store = json_decode($request->get('store'));

        $registroCombustible = $em->getRepository('PortadoresBundle:RegistroCombustible')->find($registroid);

        $date = $registroCombustible->getFecha();
        $day = $date->format('N');
        $numerosemana = -1;
        $di = 0;
        $df = 0;

        if ($date->format('y') % 4 == 0) {
            $ultimosDias = array('01' => 31, '02' => 29, '03' => 31, '04' => 30, '05' => 31, '06' => 30,
                '07' => 31, '08' => 31, '09' => 30, '10' => 31, '11' => 30, '12' => 31);
        } else {
            $ultimosDias = array('01' => 31, '02' => 28, '03' => 31, '04' => 30, '05' => 31, '06' => 30,
                '07' => 31, '08' => 31, '09' => 30, '10' => 31, '11' => 30, '12' => 31);
        }
        $last = $ultimosDias[$date->format('m')];

        while ($df < $last) {

            if ($numerosemana == -1) {
                $di = 1;
                $df = $di + 7 - $day;
            } else {
                $di = $df + 1;
                $df = $di + 6;
            }

            if ($di + 6 > $last) {
                $df = $last;
            }
            $numerosemana++;

            $j = 7;
            while ($j-- > 1) {

                $c = $numerosemana * 3 + 1;
                $l = $numerosemana * 3 + 2;
                $k = $numerosemana * 3 + 3;

                $suma = $store[1]->$c + $store[1]->$l + $store[1]->$k
                    + $store[2]->$c + $store[2]->$l + $store[2]->$k;

//                if ( (($j == 4 || $j == 1) && $suma != 0) || $j == 2 || $j == 3) {

                $entity = $em->getRepository('PortadoresBundle:RegistroCombustibleAnalisis')->findOneBy(array(
                    'semana' => $di . '-' . $df,
                    'conceptoid' => $j,
                    'registroCombustible' => $registroid,
                    'visible' => true
                ));
                if (!is_null($entity) && $suma == 0)
                    $em->remove($entity);
                if (is_null($entity) && $suma != 0)
                    $entity = new RegistroCombustibleAnalisis();

                if ($suma != 0) {

                    $entity->setSemana($di . '-' . $df);
                    $entity->setNumerosemana($numerosemana + 1);
                    $entity->setConceptoid($em->getRepository('PortadoresBundle:ConceptoRegistroCombustible')->find($j));
                    $entity->setRegistroCombustible($registroCombustible);

                    $entity->setCombustible($store[$j - 1]->$c);
                    $entity->setLubricante($store[$j - 1]->$l);
                    $entity->setKm($store[$j - 1]->$k);

                    $entity->setVisible(true);

                    $em->persist($entity);
                }
            }
        }

        try {
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Análisis gestionado con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    /*    public function modAnalisisAction(Request $request)
        {
            $em = $this->getDoctrine()->getManager();

            $id = $request->get('id');

            $registroid = $request->get('registroid');
            $semana = $request->get('semana');
            $numerosemana = $request->get('numerosemana');
            $concepto_id = $request->get('conceptoid');
            $combustible = $request->get('combustible');
            $lubricante = $request->get('lubricante');
            $km = $request->get('km');

            $entities_lic = $em->getRepository('PortadoresBundle:RegistroCombustibleAnalisis')->findBy(array(
                'semana' => $semana,
                'conceptoid' => $concepto_id,
                'registroCombustible' => $registroid,
                'visible' => true
            ));

            foreach ($entities_lic as $entity) {
                if ($entity->getId() != $id)
                    return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Existe un análisis para el concepto en la semana seleccionada.'));
            }

            $entity = $em->getRepository('PortadoresBundle:RegistroCombustibleAnalisis')->find($id);
            $entity->setSemana($semana);
            $entity->setNumerosemana($numerosemana);
            $entity->setConceptoid($em->getRepository('PortadoresBundle:NconceptoRegistroCombustible')->find($concepto_id));
            $entity->setCombustible($combustible);
            $entity->setLubricante($lubricante);
            $entity->setKm($km);

            try {
                $em->persist($entity);
                $em->flush();
                $response = new JsonResponse();
                $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Análisis modificado con éxito.'));
                return $response;
            } catch (CommonException $ex) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
            }
        }

        public function delAnalisisAction(Request $request)
        {
            $em = $this->getDoctrine()->getManager();
            $id = $request->get('id');

            $entity = $em->getRepository('PortadoresBundle:RegistroCombustibleAnalisis')->find($id);
            $entity->setVisible(false);

            try {
                $em->persist($entity);
                $em->flush();
                return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Análisis eliminado con éxito.'));
            } catch (CommonException $ex) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
            }
        }*/
}