<?php
/**
 * Created by PhpStorm.
 * User: Mire
 * Date: 16/04/2020
 * Time: 10:05
 */

namespace Geocuba\PortadoresBundle\Repository;
use Doctrine\ORM\EntityRepository;

class SubcuentaRepository extends EntityRepository
{

    /**
     * @param $_cuenta
     * @param null $start
     * @param null $limit
     * @param bool $count
     * @return array|mixed
     */
    public function buscarSubCuenta($_cuenta, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('subcuenta');
        if ($count)
            $qb->select('count(subcuenta)');

        if (isset($_cuenta) && $_cuenta != '') {
            $qb->andWhere('subcuenta.cuenta = :cuenta')
                ->setParameter('cuenta', $_cuenta);
        }

        $qb->andWhere($qb->expr()->eq('subcuenta.visible', ':visible'))
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
     * @param $_cuenta
     * @param string $id
     * @return mixed
     */
    public function buscarSubCuentaRepetido($_no_cuenta, $_cuenta, $id = '')
    {
        $qb = $this->createQueryBuilder('subcuenta');
        $qb->select('count(subcuenta)');

        $qb->andWhere($qb->expr()->eq('subcuenta.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_no_cuenta) && $_no_cuenta != '') {
            $qb->andWhere('subcuenta.nroSubcuenta = :nroSubcuenta')
                ->setParameter('nroSubcuenta', $_no_cuenta);
        }

        if (isset($_cuenta) && $_cuenta != '') {
            $qb->andWhere('subcuenta.cuenta = :cuenta')
                ->setParameter('cuenta', $_cuenta);
        }

        if (isset($id) && $id != '') {
            $qb->andWhere('subcuenta.id != :id')
                ->setParameter('id', $id);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

}