<?php
/**
 * Created by PhpStorm.
 * User: yosley
 * Date: 02/11/2015
 * Time: 15:43
 */

namespace Geocuba\PortadoresBundle\Controller;

use Doctrine\Common\Util\Debug;
use Geocuba\AdminBundle\Util\Util;
use Geocuba\PortadoresBundle\Entity\Servicio;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;

class ServicioController extends Controller

{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $_nombre = trim($request->get('nombre_servicio'));
        //  $id = $request->get('id');
        $start = $request->get('start');
        $limit = $request->get('limit');
        $nunidadid = $request->get('unidadid');

        $em = $this->getDoctrine()->getManager();


        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $qb = $em->createQueryBuilder();
        $qb->select('servicios')
            ->from('PortadoresBundle:Servicio', 'servicios')
            ->Where($qb->expr()->in('servicios.nunidadid', $_unidades))
            ->andWhere($qb->expr()->eq('servicios.visible', 'true'));
        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('lower(servicios.nombreServicio) like lower(:nombreServicio)')
                ->setParameter('nombreServicio', "%$_nombre%");
        }

        $entities = $qb->orderBy('servicios.nombreServicio', 'ASC')
            ->getQuery()
            ->setMaxResults($limit)
            ->setFirstResult($start)
            ->getResult();

        $qb = $em->createQueryBuilder();
        $qb->select('count(servicios)')
            ->from('PortadoresBundle:Servicio', 'servicios')
            ->Where($qb->expr()->in('servicios.nunidadid', $_unidades))
            ->andWhere($qb->expr()->eq('servicios.visible', 'true'));
        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('UPPER(servicios.nombreServicio) like UPPER(:nombreServicio)')
                ->setParameter('nombreServicio', "%$_nombre%");
        }

        $total = $qb->getQuery()->getSingleScalarResult();

        $_data = array();
        foreach ($entities as $entity) {

            $cap_transf1 = $em->getRepository('PortadoresBundle:BancoTransformadores')->findOneBy(array('id' => $entity->getCapTransf1()));
            $cap_transf2 = $em->getRepository('PortadoresBundle:BancoTransformadores')->findOneBy(array('id' => $entity->getCapTransf2()));
            $cap_transf3 = $em->getRepository('PortadoresBundle:BancoTransformadores')->findOneBy(array('id' => $entity->getCapTransf3()));
            $cap_transf4 = $em->getRepository('PortadoresBundle:BancoTransformadores')->findOneBy(array('id' => $entity->getCapTransf4()));
            $cap_transf5 = $em->getRepository('PortadoresBundle:BancoTransformadores')->findOneBy(array('id' => $entity->getCapTransf5()));

            $cap_banco_prep = $em->getRepository('PortadoresBundle:BancoTransformadores')->findOneBy(array('id' => $entity->getCapacBancoTransformadores()));
            $turno = $em->getRepository('PortadoresBundle:TurnoTrabajo')->findOneBy(array('id' => $entity->getTurnosTrabajo()));

            $_data[] = array(
                'id' => $entity->getId(),
                'nombre_servicio' => $entity->getNombreServicio(),
                'codigo_cliente' => $entity->getCodigoCliente(),
                'numero' => $entity->getNumero(),
                'MaximaDemandaContratada' => $entity->getMaximaDemandaContratada(),
                'control' => $entity->getControl(),
                'ruta' => $entity->getRuta(),
                'folio' => $entity->getFolio(),
                'direccion' => $entity->getDireccion(),
                'factor_combustible' => $entity->getFactorCombustible(),


                'id_cap_transf1' => isset($cap_transf1) ? $cap_transf1->getId() : '',
                'id_cap_transf2' => isset($cap_transf2) ? $cap_transf2->getId() : '',
                'id_cap_transf3' => isset($cap_transf3) ? $cap_transf3->getId() : '',
                'id_cap_transf4' => isset($cap_transf4) ? $cap_transf4->getId() : '',
                'id_cap_transf5' => isset($cap_transf5) ? $cap_transf5->getId() : '',

                'cap_transf1' => isset($cap_transf1) ? $cap_transf1->getCapacidad() : '',
                'cap_transf2' => isset($cap_transf2) ? $cap_transf2->getCapacidad() : '',
                'cap_transf3' => isset($cap_transf3) ? $cap_transf3->getCapacidad() : '',
                'cap_transf4' => isset($cap_transf4) ? $cap_transf4->getCapacidad() : '',
                'cap_transf5' => isset($cap_transf5) ? $cap_transf5->getCapacidad() : '',

                'cap_banco' => $entity->getCapacBancoTransformadores(),
                'cap_banco_nombre' => isset($cap_banco_prep) ? $entity->getCapacBancoTransformadores()->getCapacidad() : '',
                'cap_banco_mayor' => $entity->getCapBancoMayor(),
                'cant_transf' => $entity->getCantTransfBanco(),

                'trans1_pfe' => isset($cap_transf1) ? $cap_transf1->getPfe() : '',
                'trans2_pfe' => isset($cap_transf2) ? $cap_transf2->getPfe() : '',
                'trans3_pfe' => isset($cap_transf3) ? $cap_transf3->getPfe() : '',
                'trans4_pfe' => isset($cap_transf4) ? $cap_transf4->getPfe() : '',
                'trans5_pfe' => isset($cap_transf5) ? $cap_transf5->getPfe() : '',

                'trans1_pcu' => isset($cap_transf1) ? $cap_transf1->getPcu() : '',
                'trans2_pcu' => isset($cap_transf2) ? $cap_transf2->getPcu() : '',
                'trans3_pcu' => isset($cap_transf3) ? $cap_transf3->getPcu() : '',
                'trans4_pcu' => isset($cap_transf4) ? $cap_transf4->getPcu() : '',
                'trans5_pcu' => isset($cap_transf5) ? $cap_transf5->getPcu() : '',
                'tipo_servicio' => $entity->getServicioElectrico(),
                'id_turno_trabajo' => $turno->getTurno(),
                'turno_trabajo' => $turno->getId(),
                'turno_trabajo_horas' => $turno->getHoras(),
                'nunidadid' => $entity->getNunidadid()->getId(),
                'nombreunidadid' => $entity->getNunidadid()->getNombre(),
                'provicianid' => $entity->getProvinciaid()->getId(),
                'nombreprovicianid' => $entity->getProvinciaid()->getNombre(),
                'municipioid' => $entity->getMunicipio()->getId(),
                'municipio' => $entity->getMunicipio()->getNombre(),
                'tarifaid' => $entity->getNtarifaid()->getId(),
                'nombretarifaid' => $entity->getNtarifaid()->getNombre(),
                'nactividadid' => $entity->getNactividadid()->getId(),
                'nombrenactividadid' => $entity->getNactividadid()->getNombre(),
                'num_nilvel_actividadid' => $entity->getNactividadid()->getUmActividad()->getId(),
                'nombreum_nilvel_actividadid' => $entity->getNactividadid()->getUmActividad()->getNivelActividad(),
                'servicio_mayor' => $entity->getServiciomayor(),
                'servicio_prepago' => $entity->getServicioPrepago(),
                'metro_regresivo' => $entity->getMetroRegresivo()
            );


        }
        return new JsonResponse(array('rows' => $_data, 'total' => $total));

    }


    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $nombre_servicio = trim($request->get('nombre_servicio'));
        $codigo_cliente = trim($request->get('codigo_cliente'));
        $control = trim($request->get('control'));
        $municipioid = trim($request->get('municipioid'));
        $ruta = trim($request->get('ruta'));
        $folio = trim($request->get('folio'));
        $direccion = trim($request->get('direccion'));
        $max_demanda = trim($request->get('MaximaDemandaContratada'));
        $factor_combustible = trim($request->get('factor_combustible'));
        $tipo_servicio = trim($request->get('tipo_servicio'));
        $turno_trabajo = trim($request->get('turno_trabajo'));
        $nunidadid = trim($request->get('unidadid'));
        $provicianid = trim($request->get('provicianid'));
        $tarifaid = trim($request->get('tarifaid'));
        $nactividadid = trim($request->get('nactividadid'));
        $num_nilvel_actividadid = trim($request->get('num_nilvel_actividadid'));
        $mayor = trim($request->get('servicio_mayor'));
        $prepago = trim($request->get('servicio_prepago'));
        $numero = trim($request->get('numero'));
        $metro_regresivo = $request->get('metro_regresivo');

        $cant_transf_banco = $request->get('cant_transf');
        $cap_banco = $request->get('cap_banco');
        $cap_banco_mayor = $request->get('cap_banco_mayor');
        $cap_transf1 = $request->get('cap_transf1');
        $cap_transf2 = $request->get('cap_transf2');
        $cap_transf3 = $request->get('cap_transf3');
        $cap_transf4 = $request->get('cap_transf4');
        $cap_transf5 = $request->get('cap_transf5');

        $entity = new Servicio();
        $entity->setNombreServicio($nombre_servicio);
        $entity->setCodigoCliente($codigo_cliente);
        $entity->setControl($control);
        $entity->setRuta($ruta);
        $entity->setFolio($folio);
        $entity->setMunicipio($em->getRepository('PortadoresBundle:Municipio')->find($municipioid));
        $entity->setDireccion($direccion);
        $entity->setFactorCombustible($factor_combustible);
        $entity->setMaximaDemandaContratada($max_demanda);
        $entity->setServicioElectrico($tipo_servicio);
        $entity->setTurnosTrabajo($em->getRepository('PortadoresBundle:TurnoTrabajo')->find($turno_trabajo));
        $entity->setNunidadid($em->getRepository('PortadoresBundle:Unidad')->find($nunidadid));
        $entity->setProvinciaid($em->getRepository('PortadoresBundle:Provincia')->find($provicianid));
        $entity->setNtarifaid($em->getRepository('PortadoresBundle:Tarifa')->find($tarifaid));
        $entity->setNactividadid($em->getRepository('PortadoresBundle:Actividad')->find($nactividadid));
        $entity->setCapBancoMayor($cap_banco_mayor);
        $entity->setCantTransfBanco($cant_transf_banco);
        $entity->getCapacBancoTransformadores($cap_banco);
        $entity->setCapTransf1($em->getRepository('PortadoresBundle:BancoTransformadores')->find($cap_transf1));
        $entity->setCapTransf2($em->getRepository('PortadoresBundle:BancoTransformadores')->find($cap_transf2));
        $entity->setCapTransf3($em->getRepository('PortadoresBundle:BancoTransformadores')->find($cap_transf3));
        $entity->setCapTransf4($em->getRepository('PortadoresBundle:BancoTransformadores')->find($cap_transf4));
        $entity->setCapTransf5($em->getRepository('PortadoresBundle:BancoTransformadores')->find($cap_transf5));
        if ($mayor == '') {
            $entity->setServicioMayor(false);
        } else {
            $entity->setServicioMayor(true);
        }

        if ($prepago == '') {
            $entity->setServicioPrepago(false);
        } else {
            $entity->setServicioPrepago(true);
        }

        if ($metro_regresivo == '') {
            $entity->setMetroRegresivo(false);
        } else {
            $entity->setMetroRegresivo(true);
        }

        $entity->setNumero($numero);
        $entity->setVisible(true);
        try {
            $em->persist($entity);
            $em->flush();
            return new Response(json_encode(array('success' => true, 'cls' => 'success', 'message' => 'Servicio adicionado con éxito.'), JSON_UNESCAPED_UNICODE));
        } catch (\Exception $ex) {
            return new Response(json_encode(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'), JSON_UNESCAPED_UNICODE));
        }
    }


    public function modAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $municipioid = trim($request->get('municipioid'));
        $nombre_servicio = trim($request->get('nombre_servicio'));
        $codigo_cliente = trim($request->get('codigo_cliente'));
        $control = trim($request->get('control'));
        $ruta = trim($request->get('ruta'));
        $folio = trim($request->get('folio'));
        $direccion = trim($request->get('direccion'));
        $max_demanda = trim($request->get('MaximaDemandaContratada'));
        $factor_combustible = trim($request->get('factor_combustible'));
        $tipo_servicio = trim($request->get('tipo_servicio'));
        $turno_trabajo = trim($request->get('turno_trabajo'));
        $nunidadid = trim($request->get('unidadid'));
        $provicianid = trim($request->get('provicianid'));
        $tarifaid = trim($request->get('tarifaid'));
        $nactividadid = trim($request->get('nactividadid'));
        $mayor = trim($request->get('servicio_mayor'));
        $prepago = trim($request->get('servicio_prepago'));
        $numero = trim($request->get('numero'));
        $metro_regresivo = $request->get('metro_regresivo');

        $cant_transf_banco = $request->get('cant_transf');
        $cap_banco = $request->get('cap_banco');
        $cap_banco_mayor = $request->get('cap_banco_mayor');
        $cap_transf1 = $request->get('cap_transf1');
        $cap_transf2 = $request->get('cap_transf2');
        $cap_transf3 = $request->get('cap_transf3');
        $cap_transf4 = $request->get('cap_transf4');
        $cap_transf5 = $request->get('cap_transf5');

        $entity = $em->getRepository('PortadoresBundle:Servicio')->find($id);
        $entity->setNombreServicio($nombre_servicio);
        $entity->setCodigoCliente($codigo_cliente);
        $entity->setControl($control);
        $entity->setRuta($ruta);
        $entity->setFolio($folio);
        $entity->setMunicipio($em->getRepository('PortadoresBundle:Municipio')->find($municipioid));
        $entity->setDireccion($direccion);
        $entity->setFactorCombustible($factor_combustible);
        $entity->setMaximaDemandaContratada($max_demanda);
        $entity->setServicioElectrico($tipo_servicio);
        $entity->setTurnosTrabajo($em->getRepository('PortadoresBundle:TurnoTrabajo')->find($turno_trabajo));
        $entity->setNunidadid($em->getRepository('PortadoresBundle:Unidad')->find($nunidadid));
        $entity->setProvinciaid($em->getRepository('PortadoresBundle:Provincia')->find($provicianid));
        $entity->setNtarifaid($em->getRepository('PortadoresBundle:Tarifa')->find($tarifaid));
        $entity->setNactividadid($em->getRepository('PortadoresBundle:Actividad')->find($nactividadid));


        if ($cant_transf_banco != '') {
            $entity->setCantTransfBanco($cant_transf_banco);
        }

        if ($mayor == '') {
            $entity->setServicioMayor(false);
        } else {
            $entity->setServicioMayor(true);
            $entity->setCapBancoMayor($cap_banco_mayor);
            $entity->setCapTransf1($em->getRepository('PortadoresBundle:BancoTransformadores')->find($cap_transf1));
            $entity->setCapTransf2($em->getRepository('PortadoresBundle:BancoTransformadores')->find($cap_transf2));
            $entity->setCapTransf3($em->getRepository('PortadoresBundle:BancoTransformadores')->find($cap_transf3));
            $entity->setCapTransf4($em->getRepository('PortadoresBundle:BancoTransformadores')->find($cap_transf4));
            $entity->setCapTransf5($em->getRepository('PortadoresBundle:BancoTransformadores')->find($cap_transf5));
            $entity->setNumero($numero);
        }

        if ($prepago == '') {
            $entity->setServicioPrepago(false);
        } else {
            $entity->setServicioPrepago(true);
            $entity->setCapBancoMayor($cap_banco_mayor);
            $entity->setCapTransf1($em->getRepository('PortadoresBundle:BancoTransformadores')->find($cap_transf1));
            $entity->setCapTransf2($em->getRepository('PortadoresBundle:BancoTransformadores')->find($cap_transf2));
            $entity->setCapTransf3($em->getRepository('PortadoresBundle:BancoTransformadores')->find($cap_transf3));
            $entity->setCapTransf4($em->getRepository('PortadoresBundle:BancoTransformadores')->find($cap_transf4));
            $entity->setCapTransf5($em->getRepository('PortadoresBundle:BancoTransformadores')->find($cap_transf5));
            $entity->setNumero($numero);
        }

        if ($metro_regresivo == '') {
            $entity->setMetroRegresivo(false);
        } else {
            $entity->setMetroRegresivo(true);
        }


        try {
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Servicio modificado con éxito.'));
            return $response;
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function delAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $entity = $em->getRepository('PortadoresBundle:Servicio')->find($id);
        $entity->setVisible(false);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Servicio eliminado con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }


}