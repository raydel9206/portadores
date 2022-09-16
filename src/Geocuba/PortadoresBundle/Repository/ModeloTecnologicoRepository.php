<?php

namespace Geocuba\PortadoresBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class ModeloTecnologicoRepository extends EntityRepository
{

    public function findAllBy($_nombre, $marcaId, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('modelo');
        if ($count) $qb->select('count(modelo)');

        $qb->where($qb->expr()->eq('modelo.marcaTecnologica', ':marca'))
            ->setParameter('marca', $marcaId);

        if ($_nombre) {
            $qb->andWhere('clearstr(modelo.nombre) like clearstr(:nombre)')
                ->setParameter('nombre', "%$_nombre%");
        }

        if ($count) {
            try {
                return $qb->getQuery()->getSingleScalarResult();
            } catch (NonUniqueResultException $e) {
                return null;
            }
        }

        return $qb->orderBy('modelo.nombre')
            ->setMaxResults($limit)
            ->setFirstResult($start)
            ->getQuery()->getResult();
    }

    public function duplicates($nombre, $marcaId, $modeloId = null) {
        $qb = $this->createQueryBuilder('modelo');
        $qb->where($qb->expr()->eq('modelo.marcaTecnologica', ':marca'))
            ->andWhere('clearstr(modelo.nombre) like clearstr(:nombre)')
            ->setParameter('marca', $marcaId)
            ->setParameter(':nombre', $nombre);

        if ($modeloId){
            $qb->andWhere($qb->expr()->neq('modelo.id', ':modelo'))
                ->setParameter(':modelo', $modeloId);
        }

        return $qb->getQuery()->getResult();
    }
}