<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Javier
 * Date: 16/01/14
 * Time: 16:09
 * To change this template use File | Settings | File Templates.
 */

namespace Geocuba\PortadoresBundle\Repository;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityRepository;

class DetalleGastoRepository extends EntityRepository
{
    public function buscarDetalleGastoRepetido($unidad_id, $codigo, $id = '')
    {
        $qb = $this->createQueryBuilder('detalleGasto');
        $qb->select('count(detalleGasto)');

        $qb->andWhere($qb->expr()->eq('detalleGasto.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($codigo) && $codigo != '') {
            $qb->andWhere('lower(detalleGasto.codigo) = lower(:codigo)')
                ->setParameter('codigo', $codigo);
        }

        if (isset($unidad_id) && $unidad_id != '') {
            $qb->andWhere('detalleGasto.unidad = :unidadid')
                ->setParameter('unidadid', $unidad_id);
        }

        if (isset($id) && $id != '') {
            $qb->andWhere('detalleGasto.id != :id')
                ->setParameter('id', $id);
        }

        $qb->andWhere($qb->expr()->eq('detalleGasto.visible', ':visible'))
            ->setParameter('visible', true);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function buscarDetalleGasto($unidad_id, $_portadores = '', $monedaid = '')
    {
        $qb = $this->createQueryBuilder('detalleGasto');

        if (isset($_portadores) && $_portadores != '' && count($_portadores)>0) {
            $qb->innerJoin('detalleGasto.ntipoCombustibleid', 'tipoCombustible');
        }

        if (isset($monedaid) && $monedaid != '') {
            $qb->innerJoin('detalleGasto.moneda', 'moneda');
        }

        if (isset($_portadores) && $_portadores != '' && count($_portadores)>0) {
            $qb->where($qb->expr()->in('tipoCombustible.portadorid', ':portadores'))
                ->setParameter('portadores', $_portadores);
        }

        if (isset($unidad_id) && $unidad_id != '') {
            $qb->andWhere('detalleGasto.unidad = :unidadid')
                ->setParameter('unidadid', $unidad_id);
        }

        if (isset($monedaid) && $monedaid != '') {
            $qb->andWhere('moneda.id = :monedaid')
                ->setParameter('monedaid', $monedaid);
        }

        $qb->andWhere($qb->expr()->eq('detalleGasto.visible', ':visible'))
            ->setParameter('visible', true);

        return $qb->orderBy('detalleGasto.codigo', 'ASC')
            ->getQuery()->getResult();

    }

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
//    /**
//     * @param $_nombre
//     * @param $_unidad
//     * @param string $id
//     * @return mixed
//     * @throws \Doctrine\ORM\NonUniqueResultException
//     */
//    public function buscarElementoGastoRepetido($_nombre, $_unidad, $id = '')
//    {
//        $qb = $this->createQueryBuilder('centroCosto');
//        $qb->select('count(centroCosto)');
//
//        $qb->andWhere($qb->expr()->eq('centroCosto.visible', ':visible'))
//            ->setParameter('visible', true);
//
//        if (isset($_nombre) && $_nombre != '') {
//            $qb->andWhere('lower(centroCosto.nombre) = lower(:nombre)')
//                ->setParameter('nombre', $_nombre);
//        }
//
//        if (isset($_unidad) && $_unidad != '') {
//            $qb->andWhere('centroCosto.nunidadid = :unidad')
//                ->setParameter('unidad', $_unidad);
//        }
//
//        if (isset($id) && $id != '') {
//            $qb->andWhere('centroCosto.id != :id')
//                ->setParameter('id', $id);
//        }
//
//        return $qb->getQuery()->getSingleScalarResult();
//    }
}