<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pfcadenas
 * Date: 9/11/2016
 * Time: 15:07
 * To change this template use File | Settings | File Templates.
 */

namespace Geocuba\PortadoresBundle\Repository;

use Doctrine\ORM\EntityRepository;

class TarjetaPersonaRepository extends EntityRepository
{
    public function buscarXTarjeta($tarjeta, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('TarjetaPersona');
        if ($count)
            $qb->select('count(TarjetaPersona)');

        $qb->Where($qb->expr()->eq('TarjetaPersona.ntarjetaid', ':tarjeta'))
            ->andWhere($qb->expr()->eq('TarjetaPersona.visible', ':visible'))
            ->setParameter('tarjeta', "$tarjeta")
            ->setParameter('visible', true);

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->setMaxResults($limit)
                ->setFirstResult($start)
                ->getQuery()->getResult();
        }
    }
}