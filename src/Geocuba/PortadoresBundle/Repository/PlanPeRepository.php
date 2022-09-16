<?php
/**
 * Created by PhpStorm.
 * User: Yos
 * Date: 30/07/2017
 * Time: 12:48 AM
 */

namespace Geocuba\PortadoresBundle\Repository;
use Doctrine\ORM\EntityRepository;

class PlanPeRepository extends EntityRepository
{


    public function getPlan($unidad)
    {
//        $consulta = $this->getEntityManager()->createQuery('Select a. FROM PortadoresBundle:Servicios a
//        where a.serviciosid =:serviciosid Order By a.id ASC');
//
//        $consulta->setParameters(array(
//            'serviciosid' => $servicios
//        ));
////        print_r($consulta->getResult());die;
//        return $consulta->getResult();
    }



}