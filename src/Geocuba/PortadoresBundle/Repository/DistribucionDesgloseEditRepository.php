<?php
/**
 * Created by PhpStorm.
 * User: rherrerag
 * Date: 9/4/2017
 * Time: 2:45 p.m.
 */

namespace Geocuba\PortadoresBundle\Repository;


use Doctrine\ORM\EntityRepository;

class DistribucionDesgloseEditRepository extends EntityRepository
{
    public function buscarDistribucionCombustibleEdit($dist_combustible,$dist_comb_desg)
    {
        $qb = $this->createQueryBuilder('distribucion_combustible_edit');


        if (isset($dist_combustible) && $dist_combustible != '') {
            $qb->andWhere('distribucion_combustible_edit.distCombustible = :distCombustible')
                ->setParameter('distCombustible', "$dist_combustible");
        }

        if (isset($dist_comb_desg) && $dist_comb_desg != '') {
            $qb->andWhere('distribucion_combustible_edit.distCombDesg = :distCombDesg')
                ->setParameter('distCombDesg', "$dist_comb_desg");
        }

        return $qb->orderBy('distribucion_combustible_edit.vehiculoDenominacion', 'ASC')
            ->orderBy('distribucion_combustible_edit.matricula', 'ASC')
             ->getQuery()->getResult();
    }
}