<?php

namespace Geocuba\AdminBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class EventoRepository
 * @package Geocuba\AdminBundle\Repository
 */
class EventoRepository extends EntityRepository
{
    /**
     * @param \DateTime $from
     * @param \DateTime $until
     * @param string $action
     * @param string $userid
     * @param string $entity
     * @param int $limit
     * @param int $start
     * @return array
     */
    public function findAllBy($from, $until, $action, $userid, $entity, $limit, $start)
    {
        $qb = $this->createQueryBuilder('evento');

        if ($from) {
            $qb->andWhere($qb->expr()->gte('evento.fecha', $qb->expr()->literal($from->format(DATE_ISO8601))));
        }

        if ($until) {
            $qb->andWhere($qb->expr()->lte('evento.fecha', $qb->expr()->literal($until->format(DATE_ISO8601))));
        }

        if ($action) {
            $qb->andWhere($qb->expr()->eq('evento.tipo', $action));
        }

        if ($userid) {
            $qb->andWhere($qb->expr()->eq('evento.usuario', $qb->expr()->literal($userid)));
        }

        if ($entity) {
            $qb->andWhere($qb->expr()->eq('evento.entidad', $qb->expr()->literal($entity)));
        }

        $query = $qb
            ->setMaxResults($limit)
            ->setFirstResult($start)
            ->orderBy('evento.fecha', 'DESC')
            ->setCacheable(true)
            ->getQuery();

        // error_log($query->getSQL());

        return $query->getResult();
    }
}
