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

class CajaRepository extends EntityRepository {

    /**
     * @param $_nombre
     * @param $_unidad
     * @param $_unidades
     * @param null $start
     * @param null $limit
     * @param bool $count
     * @return array|mixed
     */
    public function buscarCaja($_nombre, /*$_unidad,*/ $_unidades, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('caja');
        if ($count)
            $qb->select('count(caja)');

        $qb->innerJoin('caja.nunidadid', 'unidad');

        $qb->Where($qb->expr()->in('caja.nunidadid', $_unidades))
            ->andWhere($qb->expr()->eq('caja.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('lower(caja.nombre) like lower(:nombre)')
                ->setParameter('nombre', "%$_nombre%");
        }

//        if (isset($_unidad) && $_unidad != '') {
//            $qb->andWhere('caja.nunidadid = :unidad')
//                ->setParameter('unidad', "$_unidad");
//        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('unidad.nombre')
                ->setMaxResults($limit)
                ->setFirstResult($start)
                ->getQuery()->getResult();
        }
    }
    
    public function buscarCajaCombo($_unidades)
    {
        $qb = $this->createQueryBuilder('caja');
        $qb->select('caja.id','caja.nombre','unidad.id as nunidadid');

        $qb->innerJoin('caja.nunidadid', 'unidad')
            ->where($qb->expr()->in('caja.nunidadid', ':unidades'))
            ->setParameter('unidades',$_unidades)
            ->andWhere($qb->expr()->eq('caja.visible', ':visible'))
            ->setParameter('visible', true);

        return $qb->orderBy('caja.nombre', 'ASC')
            ->getQuery()->getResult();
    }

    /**
     * @param $_nombre
     * @param $_unidad
     * @param string $id
     * @return mixed
     */
    public function buscarCajaRepetido($_nombre, $_unidad, $id = '')
    {
        $qb = $this->createQueryBuilder('caja');
        $qb->select('count(caja)');

        $qb->andWhere($qb->expr()->eq('caja.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('lower(caja.nombre) = lower(:nombre)')
                ->setParameter('nombre', $_nombre);
        }

        if (isset($_unidad) && $_unidad != '') {
            $qb->andWhere('caja.nunidadid = :unidad')
                ->setParameter('unidad', $_unidad);
        }

        if (isset($id) && $id != '') {
            $qb->andWhere('caja.id != :id')
                ->setParameter('id', $id);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }
}