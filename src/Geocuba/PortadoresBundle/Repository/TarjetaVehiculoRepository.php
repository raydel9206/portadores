<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Javier
 * Date: 16/01/14
 * Time: 16:09
 * To change this template use File | Settings | File Templates.
 */

namespace Geocuba\PortadoresBundle\Repository;

use Doctrine\ORM\EntityRepository;

class TarjetaVehiculoRepository extends EntityRepository
{
    public function FindListNtarjetaNvehiculo($start = null, $limit = null)
    {
        $consulta = $this->getEntityManager()->createQuery('Select a FROM PortadoresBundle:TarjetaVehiculo a
        where a.visible = true Order By a.id ASC');

        if ($limit != -1) {
            $consulta->setMaxResults($limit);
            $consulta->setFirstResult($start);
        }

        return $consulta->getResult();

    }

    public function buscarXTarjeta($tarjeta, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('TarjetaVehiculo');
        if ($count) {
            $qb->select('count(TarjetaVehiculo)');
        }

        $qb->where($qb->expr()->eq('TarjetaVehiculo.ntarjetaid', ':tarjeta'))
            ->andWhere($qb->expr()->eq('TarjetaVehiculo.visible', ':visible'))
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

    public function buscarXVehiculo($vehiculo, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('TarjetaVehiculo');
        if ($count) {
            $qb->select('count(TarjetaVehiculo)');
        }

        $qb->where($qb->expr()->eq('TarjetaVehiculo.ntarjetaid', ':tarjeta'))
            ->andWhere($qb->expr()->eq('TarjetaVehiculo.visible', ':visible'))
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