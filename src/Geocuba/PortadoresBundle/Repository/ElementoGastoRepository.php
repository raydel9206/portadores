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

class ElementoGastoRepository extends EntityRepository
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
//    public function buscarElementosGasto($_unidades)
//    {
//        $qb = $this->createQueryBuilder('elementoGasto');
//
//        $qb->innerJoin('elementoGasto.ncentrocostoid', 'centroCosto');
//        $qb->innerJoin('centroCosto.unidad', 'unidad');
//
//        $qb->andWhere($qb->expr()->eq('elementoGasto.visible', ':visible'))
//            ->setParameter('visible', true);
//
//        if ($_unidades !== null && $_unidades != '') {
//            $qb->andWhere($qb->expr()->in('centroCosto.nunidadid', $_unidades));
//        }
//
//        return $qb->orderBy('centroCosto.nombre')
//            ->getQuery()->getResult();
//    }

//    public function buscarElementoGasto($_nombre, $_unidades, $start = null, $limit = null, $count = false)
//    {
//        $qb = $this->createQueryBuilder('centroCosto');
//        if ($count) {
//            $qb->select('count(centroCosto)');
//        }
//
//        $qb->innerJoin('centroCosto.nunidadid', 'unidad');
//
//        $qb->where($qb->expr()->in('centroCosto.nunidadid', ':unidadid'))
//            ->setParameter('unidadid',$_unidades)
//            ->andWhere($qb->expr()->eq('centroCosto.visible', ':visible'))
//            ->setParameter('visible', true);
//
//        if ($_nombre !== null && $_nombre != '') {
//            $qb->andWhere('lower(centroCosto.nombre) like lower(:nombre)')
//                ->setParameter('nombre', "%$_nombre%");
//        }
//
//        if ($count) {
//            return $qb->getQuery()->getSingleScalarResult();
//        } else {
//            return $qb->orderBy('unidad.nombre')
//                ->setMaxResults($limit)
//                ->setFirstResult($start)
//                ->getQuery()->getResult();
//        }
//    }
//
//    public function buscarElementoGastoCombo($_unidades)
//    {
//        $qb = $this->createQueryBuilder('centroCosto');
//        $qb->select('centroCosto.id','centroCosto.nombre','unidad.id as nunidadid');
//
//        $qb->innerJoin('centroCosto.nunidadid', 'unidad')
//            ->where($qb->expr()->in('centroCosto.nunidadid', ':unidades'))
//            ->setParameter('unidades',$_unidades)
//            ->andWhere($qb->expr()->eq('centroCosto.visible', ':visible'))
//            ->setParameter('visible', true);
//
//        return $qb->orderBy('centroCosto.nombre', 'ASC')
//            ->getQuery()->getResult();
//    }
    /**
     * @param $_nombre
     * @param $_unidad
     * @param string $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function buscarElementoGastoRepetido($unidad_id, $codigo, $id = '')
    {
        $qb = $this->createQueryBuilder('elementoGasto');
        $qb->select('count(elementoGasto)');

        $qb->andWhere($qb->expr()->eq('elementoGasto.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($codigo) && $codigo != '') {
            $qb->andWhere('lower(elementoGasto.codigo) = lower(:codigo)')
                ->setParameter('codigo', $codigo);
        }

        if (isset($id) && $id != '') {
            $qb->andWhere('elementoGasto.id != :id')
                ->setParameter('id', $id);
        }

        if (isset($unidad_id) && $unidad_id != '') {
            $qb->andWhere('elementoGasto.unidad = :unidadid')
                ->setParameter('unidadid', $unidad_id);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }
}