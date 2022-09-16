<?php

namespace Geocuba\PortadoresBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class TanqueRepository extends EntityRepository {

    /**
     * @param $_unidades
     * @param null $start
     * @param null $limit
     * @param bool $count
     * @return array|mixed
     * @throws NonUniqueResultException
     */
    public function findAllBy($_unidades, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('tanque');
        if ($count)
            $qb->select('count(tanque)');

        $qb->innerJoin('tanque.unidad', 'unidad');

        if($_unidades)
            $qb->andWhere($qb->expr()->in('tanque.unidad', $_unidades));

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        }

        return $qb->orderBy('unidad.nombre')
            ->addOrderBy('tanque.descripcion')
            ->setMaxResults($limit)
            ->setFirstResult($start)
            ->getQuery()->getResult();
    }

    public function findDuplicate($numeroInventario, $unidad, $id = null)
    {
        $qb = $this->createQueryBuilder('tanque');

        $qb->Where($qb->expr()->eq('tanque.numeroInventario', ':numeroInventario'))
            ->setParameter('numeroInventario', $numeroInventario)
            ->andWhere($qb->expr()->eq('tanque.unidad', ':unidad'))
            ->setParameter('unidad', $unidad);
        if ($id)
            $qb->andWhere($qb->expr()->neq('tanque.id', ':id'))
                ->setParameter('id', $id);

        return $qb->getQuery()->getResult();
    }
}