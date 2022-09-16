<?php

namespace Geocuba\PortadoresBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class GrupoElectrogenoMotorRepository extends EntityRepository
{
    public function findAllBy($_noSerie, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('motor');
        if ($count)
            $qb->select('count(motor)');

        if ($_noSerie) {
            $qb->andWhere('lower(motor.noSerie) like lower(:noSerie)')
                ->setParameter('noSerie', "%$_noSerie%");
        }

        if ($count) {
            try {
                return $qb->getQuery()->getSingleScalarResult();
            } catch (NonUniqueResultException $e) {
                return null;
            }
        }

        return $qb->orderBy('motor.noSerie', 'ASC')
            ->setMaxResults($limit)
            ->setFirstResult($start)
            ->getQuery()->getResult();
    }
}