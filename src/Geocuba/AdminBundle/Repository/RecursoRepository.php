<?php

namespace Geocuba\AdminBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Geocuba\AdminBundle\Entity\Recurso;

/**
 * Class RecursoRepository
 * @package Geocuba\AdminBundle\Repository
 */
class RecursoRepository extends EntityRepository
{
    /**
     * @param $groups_ids
     * @return Recurso[]
     */
    public function findByGrupos($groups_ids)
    {
        $qb = $this->createQueryBuilder('recurso');

        $qb->andWhere(
            $qb->expr()->in('recurso.grupo', $groups_ids)
        );

        return $qb->setCacheable(true)->getQuery()->getResult();
    }
}
