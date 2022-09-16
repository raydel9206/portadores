<?php
/**
 * Created by PhpStorm.
 * User: Mire
 * Date: 16/04/2020
 * Time: 10:28
 */

namespace Geocuba\PortadoresBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CuentaRepository extends EntityRepository
{

    /**
     * @param $_no_cuenta
     * @param $_unidades
     * @param null $start
     * @param null $limit
     * @param bool $count
     * @return array|mixed
     */
    public function buscarCuenta($_no_cuenta, $_unidades, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('cuenta');
        if ($count)
            $qb->select('count(cuenta)');


        if (isset($_no_cuenta) && $_no_cuenta != '') {
            $qb->andWhere('cuenta.nroCuenta = :norCuenta')
                ->setParameter('norCuenta', $_no_cuenta);
        }

        if ($_unidades !== null && $_unidades != '') {
            $qb->andWhere($qb->expr()->in('cuenta.unidad', $_unidades));
        }

        $qb->andWhere($qb->expr()->eq('cuenta.visible', ':visible'))
            ->setParameter('visible', true);

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->setMaxResults($limit)
                ->setFirstResult($start)
                ->getQuery()->getResult();
        }
    }

    /**
     * @param $_no_cuenta
     * @param $_unidad
     * @param string $id
     * @return mixed
     */
    public function buscarCuentaRepetido($_no_cuenta, $_unidad, $id = '')
    {
        $qb = $this->createQueryBuilder('cuenta');
        $qb->select('count(cuenta)');

        $qb->andWhere($qb->expr()->eq('cuenta.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_no_cuenta) && $_no_cuenta != '') {
            $qb->andWhere('cuenta.nroCuenta = :nroCuenta')
                ->setParameter('nroCuenta', $_no_cuenta);
        }

        if (isset($_unidad) && $_unidad != '') {
            $qb->andWhere('cuenta.unidad = :unidad')
                ->setParameter('unidad', $_unidad);
        }

        if (isset($id) && $id != '') {
            $qb->andWhere('cuenta.id != :id')
                ->setParameter('id', $id);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }
}