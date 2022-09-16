<?php
/**
 * Created by JetBrains PhpStorm.
 * User: orlando
 * Date: 16/01/14
 * Time: 16:09
 * To change this template use File | Settings | File Templates.
 */

namespace Geocuba\PortadoresBundle\Repository;
use Doctrine\ORM\EntityRepository;

class CuentaGastoRepository extends EntityRepository
{
    /**
     * @param $_no_cuenta
     * @param $_unidad
     * @param $_unidades
     * @param null $start
     * @param null $limit
     * @param bool $count
     * @return array|mixed
     */
    public function buscarCuentaGasto($_no_cuenta, $_unidades, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('cuentaGasto');
        if ($count)
            $qb->select('count(cuentaGasto)');

        $qb->innerJoin('cuentaGasto.centroCosto', 'centroCosto');


        if (isset($_no_cuenta) && $_no_cuenta != '') {
            $qb->andWhere('lower(cuentaGasto.noCuenta) like lower(:noCuenta)')
                ->setParameter('noCuenta', "%$_no_cuenta%");
        }

        if ($_unidades !== null && $_unidades != '') {
            $qb->andWhere($qb->expr()->in('centroCosto.nunidadid', $_unidades));
        }

        $qb->andWhere($qb->expr()->eq('cuentaGasto.visible', ':visible'))
            ->setParameter('visible', true);

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('centroCosto.codigo')
                ->setMaxResults($limit)
                ->setFirstResult($start)
                ->getQuery()->getResult();
        }
    }

//    public function buscarCuentaGastoCombo($_unidades)
//    {
//        $qb = $this->createQueryBuilder('cuentaGasto');
//
//        $qb->innerJoin('cuentaGasto.nunidadid', 'unidad');
//
//        $qb->Where($qb->expr()->in('cuentaGasto.nunidadid', $_unidades))
//            ->andWhere($qb->expr()->eq('cuentaGasto.visible', ':visible'))
//            ->setParameter('visible', true);
//
//        if (isset($_unidad) && $_unidad != '') {
//            $qb->andWhere('cuentaGasto.nunidadid = :unidad')
//                ->setParameter('unidad', "$_unidad");
//        }
//
//        return $qb->orderBy('unidad.nombre')
//            ->getQuery()->getResult();
//    }

    /**
     * @param $_no_cuenta
     * @param $_unidad
     * @param string $id
     * @return mixed
     */
    public function buscarCuentaGastoRepetido($_no_cuenta, $_unidad, $id = '')
    {
        $qb = $this->createQueryBuilder('cuentaGasto');
        $qb->select('count(cuentaGasto)');

        $qb->innerJoin('cuentaGasto.centroCosto', 'centroCosto');

        $qb->andWhere($qb->expr()->eq('cuentaGasto.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_no_cuenta) && $_no_cuenta != '') {
            $qb->andWhere('lower(cuentaGasto.noCuenta) = lower(:noCuenta)')
                ->setParameter('noCuenta', $_no_cuenta);
        }

        if (isset($_unidad) && $_unidad != '') {
            $qb->andWhere('centroCosto.nunidadid = :unidad')
                ->setParameter('unidad', $_unidad);
        }

        if (isset($id) && $id != '') {
            $qb->andWhere('cuentaGasto.id != :id')
                ->setParameter('id', $id);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }
}