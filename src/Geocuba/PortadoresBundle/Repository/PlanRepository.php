<?php
/**
 * Created by PhpStorm.
 * User: yosley
 * Date: 17/11/2017
 * Time: 13:43
 */

namespace Geocuba\PortadoresBundle\Repository;
use Doctrine\ORM\EntityRepository;

class PlanRepository extends EntityRepository
{

    public function getplanresumen($unidades,$anno,$count=false)
    {

        $qb = $this->createQueryBuilder('Planes');
        if ($count)
            $qb->select('count(Planes)')
                ->andWhere($qb->expr()->in('Planes.unidad', $unidades))
                ->andWhere($qb->expr()->eq('Planes.visible', ':visible'))
                ->andWhere($qb->expr()->eq('Planes.anno', $anno))
                ->setParameter('visible', true);
        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->getQuery()->getResult();
        }
    }

}