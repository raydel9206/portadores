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

class CentroCostoRepository extends EntityRepository
{
    /**
     * @param $_nombre
     * @param $_unidades
     * @param null $start
     * @param null $limit
     * @param bool $count
     * @return array|mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function buscarCentroCosto($_nombre, $_unidades, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('centroCosto');
        if ($count) {
            $qb->select('count(centroCosto)');
        }

        $qb->innerJoin('centroCosto.nunidadid', 'unidad');

        $qb->where($qb->expr()->in('centroCosto.nunidadid', ':unidadid'))
            ->setParameter('unidadid',$_unidades)
            ->andWhere($qb->expr()->eq('centroCosto.visible', ':visible'))
            ->setParameter('visible', true);

        if ($_nombre !== null && $_nombre != '') {
            $qb->andWhere('lower(centroCosto.nombre) like lower(:nombre)')
                ->setParameter('nombre', "%$_nombre%");
        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('unidad.nombre')
                ->setMaxResults($limit)
                ->setFirstResult($start)
                ->getQuery()->getResult();
        }
    }

    public function buscarCentroCostoCombo($_unidades)
    {
        $qb = $this->createQueryBuilder('centroCosto');
        $qb->select('centroCosto.id','centroCosto.nombre','centroCosto.codigo','unidad.id as nunidadid');

        $qb->innerJoin('centroCosto.nunidadid', 'unidad')
            ->where($qb->expr()->in('centroCosto.nunidadid', ':unidades'))
            ->setParameter('unidades',$_unidades)
            ->andWhere($qb->expr()->eq('centroCosto.visible', ':visible'))
            ->setParameter('visible', true);

        return $qb->orderBy('centroCosto.nombre', 'ASC')
            ->getQuery()->getResult();
    }
    /**
     * @param $_nombre
     * @param $_unidad
     * @param string $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function buscarCentroCostoRepetido($_nombre, $_unidad, $id = '')
    {
        $qb = $this->createQueryBuilder('centroCosto');
        $qb->select('count(centroCosto)');

        $qb->andWhere($qb->expr()->eq('centroCosto.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('lower(centroCosto.nombre) = lower(:nombre)')
                ->setParameter('nombre', $_nombre);
        }

        if (isset($_unidad) && $_unidad != '') {
            $qb->andWhere('centroCosto.nunidadid = :unidad')
                ->setParameter('unidad', $_unidad);
        }

        if (isset($id) && $id != '') {
            $qb->andWhere('centroCosto.id != :id')
                ->setParameter('id', $id);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }
}