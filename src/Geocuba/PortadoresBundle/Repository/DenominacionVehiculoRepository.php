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

class DenominacionVehiculoRepository extends EntityRepository
{

    public function buscarDenominacionVehiculo($_nombre, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('denominacion');
        if ($count)
            $qb->select('count(denominacion)');

        $qb->andWhere($qb->expr()->eq('denominacion.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('lower(denominacion.nombre) like lower(:nombre)')
                ->setParameter('nombre', "%$_nombre%");
        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('denominacion.orden', 'ASC')
                ->setMaxResults($limit)
                ->setFirstResult($start)
                ->getQuery()->getResult();
        }
    }

    public function buscarDenominacionVehiculoCombo()
    {
        $qb = $this->createQueryBuilder('denominacion');
        $qb->select('denominacion.id','denominacion.nombre')
            ->Where($qb->expr()->eq('denominacion.visible', ':visible'))
            ->setParameter('visible', true);

        return $qb->orderBy('denominacion.nombre', 'ASC')
            ->getQuery()->getResult();
    }
}