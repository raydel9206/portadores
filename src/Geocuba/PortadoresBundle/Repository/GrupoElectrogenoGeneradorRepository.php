<?php

namespace Geocuba\PortadoresBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class GrupoElectrogenoGeneradorRepository extends EntityRepository
{
    public function findAllBy($_noSerie, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('generador');
        if ($count)
            $qb->select('count(generador)');

        if ($_noSerie) {
            $qb->andWhere('lower(generador.noSerie) like lower(:noSerie)')
                ->setParameter('noSerie', "%$_noSerie%");
        }

        if ($count) {
            try {
                return $qb->getQuery()->getSingleScalarResult();
            } catch (NonUniqueResultException $e) {
                return null;
            }
        }

        return $qb->orderBy('generador.noSerie', 'ASC')
            ->setMaxResults($limit)
            ->setFirstResult($start)
            ->getQuery()->getResult();
    }
}