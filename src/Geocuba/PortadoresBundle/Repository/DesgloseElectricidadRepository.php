<?php
/**
 * Created by PhpStorm.
 * User: yosley
 * Date: 19/11/2016
 * Time: 17:43
 */

namespace Geocuba\PortadoresBundle\Repository;


use Doctrine\ORM\EntityRepository;

class DesgloseElectricidadRepository extends EntityRepository
{

    public function desglose()
    {

        $consulta = $this->getEntityManager()->createQuery('Select desg FROM PortadoresBundle:DesgloseElectricidad desg
        Order By desg.id ASC');
        $data = array();
        $enero = array();
        $febrero = array();
        $marzo = array();
        $abril = array();
        $mayo = array();
        $junio = array();
        $julio = array();
        $agosto = array();
        $septiembre = array();
        $octubre = array();
        $noviembre = array();
        $diciembre = array();

        foreach ($consulta->getResult() as $entity) {
            if ($entity->getMes() == 1) {
                $enero[] = array('id' => $entity->getId(), 'planPico' => $entity->getPlanPico(), 'planDiario' => $entity->getPlanDiario(), 'fechaDesglose' => $entity->getFechaDesglose()->format('Y-m-j'));
            } elseif ($entity->getMes() == 2) {
                $febrero[] = array('id' => $entity->getId(), 'planPico' => $entity->getPlanPico(), 'planDiario' => $entity->getPlanDiario(), 'fechaDesglose' => $entity->getFechaDesglose()->format('Y-m-j'));
            } elseif ($entity->getMes() == 3) {
                $marzo[] = array('id' => $entity->getId(), 'planPico' => $entity->getPlanPico(), 'planDiario' => $entity->getPlanDiario(), 'fechaDesglose' => $entity->getFechaDesglose()->format('Y-m-j'));
            } elseif ($entity->getMes() == 4) {
                $abril[] = array('id' => $entity->getId(), 'planPico' => $entity->getPlanPico(), 'planDiario' => $entity->getPlanDiario(), 'fechaDesglose' => $entity->getFechaDesglose()->format('Y-m-j'));
            } elseif ($entity->getMes() == 5) {
                $mayo[] = array('id' => $entity->getId(), 'planPico' => $entity->getPlanPico(), 'planDiario' => $entity->getPlanDiario(), 'fechaDesglose' => $entity->getFechaDesglose()->format('Y-m-j'));
            } elseif ($entity->getMes() == 6) {
                $junio[] = array('id' => $entity->getId(), 'planPico' => $entity->getPlanPico(), 'planDiario' => $entity->getPlanDiario(), 'fechaDesglose' => $entity->getFechaDesglose()->format('Y-m-j'));
            } elseif ($entity->getMes() == 7) {
                $julio[] = array('id' => $entity->getId(), 'planPico' => $entity->getPlanPico(), 'planDiario' => $entity->getPlanDiario(), 'fechaDesglose' => $entity->getFechaDesglose()->format('Y-m-j'));
            } elseif ($entity->getMes() == 8) {
                $agosto[] = array('id' => $entity->getId(), 'planPico' => $entity->getPlanPico(), 'planDiario' => $entity->getPlanDiario(), 'fechaDesglose' => $entity->getFechaDesglose()->format('Y-m-j'));
            } elseif ($entity->getMes() == 9) {
                $septiembre[] = array('id' => $entity->getId(), 'planPico' => $entity->getPlanPico(), 'planDiario' => $entity->getPlanDiario(), 'fechaDesglose' => $entity->getFechaDesglose()->format('Y-m-j'));
            } elseif ($entity->getMes() == 10) {
                $octubre[] = array('id' => $entity->getId(), 'planPico' => $entity->getPlanPico(), 'planDiario' => $entity->getPlanDiario(), 'fechaDesglose' => $entity->getFechaDesglose()->format('Y-m-j'));
            } elseif ($entity->getMes() == 11) {
                $noviembre[] = array('id' => $entity->getId(), 'planPico' => $entity->getPlanPico(), 'planDiario' => $entity->getPlanDiario(), 'fechaDesglose' => $entity->getFechaDesglose()->format('Y-m-j'));
            } elseif ($entity->getMes() == 12) {
                $diciembre[] = array('id' => $entity->getId(), 'planPico' => $entity->getPlanPico(), 'planDiario' => $entity->getPlanDiario(), 'fechaDesglose' => $entity->getFechaDesglose()->format('Y-m-j'));
            }

        }
        $meses = array('enero' => $enero, 'febrero' => $febrero, 'marzo' => $marzo, 'abril' => $abril, 'mayo' => $mayo, 'junio' => $junio, 'julio' => $julio, 'agosto' => $agosto, 'septiembre' => $septiembre, 'octubre' => $octubre, 'noviembre' => $noviembre, 'diciembre' => $diciembre);
        return $meses;
    }

    public function getdesgloseElectricidad($id_desglose_servicio){
        $consulta = $this->getEntityManager()->createQuery('Select d FROM PortadoresBundle:DesgloseElectricidad d
        where d.iddesgloseServicios =:$iddesglose Order By d.id ASC');

        $consulta->setParameters(array(
            'serviciosid' => $id_desglose_servicio
        ));
        return $consulta->getResult();
    }

    public function getDesgFecha($mes,$anno,$fecha,$servicioid)
    {
        $consulta = $this->getEntityManager()->createQuery('Select a FROM PortadoresBundle:DesgloseElectricidad a where  a.mes =:mes and a.anno =:anno and a.fechaDesglose =:fecha and a.iddesgloseServicios =:serviciosid ');
        $consulta->setParameters(array(
            'mes' => $mes,
            'anno' => $anno,
            'fecha' => $fecha,
            'serviciosid' => $servicioid,
        ));
        return $consulta->getResult();

    }
}