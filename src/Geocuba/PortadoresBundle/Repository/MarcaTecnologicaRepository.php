<?php

namespace Geocuba\PortadoresBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class MarcaTecnologicaRepository extends EntityRepository
{

    public function findAllBy($_nombre, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('marca');
        if ($count) $qb->select('count(marca)');

        if ($_nombre) {
            $qb->andWhere('clearstr(marca.nombre) like clearstr(:nombre)')
                ->setParameter('nombre', "%$_nombre%");
        }

        if ($count) {
            try {
                return $qb->getQuery()->getSingleScalarResult();
            } catch (NonUniqueResultException $e) {
                return null;
            }
        }

        return $qb->orderBy('marca.nombre')
            ->setMaxResults($limit)
            ->setFirstResult($start)
            ->getQuery()->getResult();
    }

    public function duplicates($nombre, $id = null) {
        $qb = $this->createQueryBuilder('marca');
        $qb->where('clearstr(marca.nombre) like clearstr(:nombre)')
            ->setParameter(':nombre', $nombre);

        if ($id){
            $qb->andWhere($qb->expr()->neq('marca.id', ':id'))
                ->setParameter('id', $id);
        }

        return $qb->getQuery()->getResult();
    }
}