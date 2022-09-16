<?php

namespace Geocuba\PortadoresBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class DenominacionTecnologicaRepository extends EntityRepository
{
    public function findAllBy($_nombre, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('denominacion');
        if ($count)
            $qb->select('count(denominacion)');

        $qb->andWhere($qb->expr()->eq('denominacion.visible', ':visible'))
            ->setParameter('visible', true);

        if ($_nombre) {
            $qb->andWhere('lower(denominacion.nombre) like lower(:nombre)')
                ->setParameter('nombre', "%$_nombre%");
        }

        if ($count) {
            try {
                return $qb->getQuery()->getSingleScalarResult();
            } catch (NonUniqueResultException $e) {
                return null;
            }
        }

        return $qb->orderBy('denominacion.nombre', 'ASC')
            ->setMaxResults($limit)
            ->setFirstResult($start)
            ->getQuery()->getResult();
    }
}