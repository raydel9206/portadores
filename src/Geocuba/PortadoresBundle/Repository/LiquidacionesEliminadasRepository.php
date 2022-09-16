<?php

namespace Geocuba\PortadoresBundle\Repository;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityRepository;

class LiquidacionesEliminadasRepository extends EntityRepository
{
    /**
     * @param $_noVale
     * @param $_unidades
     * @param $fechaDesde
     * @param $fechaHasta
     * @param null $start
     * @param null $limit
     * @param bool $count
     * @return array|mixed
     */
    public function buscarLiq($_unidades, $_noVale, $tarjetaid, $anticipoid, $fechaDesde, $fechaHasta, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('liq');
        if ($count)
            $qb->select('count(liq)');

        if (isset($tarjetaid) && $tarjetaid != '') {
            $qb->Where('liq.ntarjetaid = :ntarjetaid')
                ->setParameter('ntarjetaid', $tarjetaid);
        }

        if (isset($anticipoid) && $anticipoid != '') {
            $qb->andWhere('liq.anticipo = :anticipoid')
                ->setParameter('anticipoid', $anticipoid);
        }

        if (isset($_unidades) && $_unidades != '') {
            $qb->andWhere($qb->expr()->in('liq.nunidadid', ':unidades'))
                ->setParameter('unidades', $_unidades);
        }

        if (isset($_noVale) && $_noVale != '') {
            $qb->andWhere('clearstr(liq.nroVale) like clearstr(:noVale)')
                ->setParameter('noVale', "%$_noVale%");
        }

        if (isset($fechaDesde) && $fechaDesde != '' && isset($fechaHasta) && $fechaHasta != '') {
            $qb->andWhere('liq.fechaVale between :fechaDesde and :fechaHasta')
                ->setParameter('fechaDesde', $fechaDesde)
                ->setParameter('fechaHasta', $fechaHasta);
        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('liq.fechaRegistro', 'DESC')
                ->setMaxResults($limit)
                ->setFirstResult($start)
                ->getQuery()->getResult();
        }
    }

    public function buscarEntregas($_unidades, $_noVale, $tarjetaid, $fechaDesde, $fechaHasta, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('liquidacion');
        if ($count)
            $qb->select('count(liquidacion)');

        $qb->innerJoin('liquidacion.ntarjetaid', 'tarjeta');
//        $qb->innerJoin('liquidacion.anticipo', 'anticipo');

        if (isset($tarjetaid) && $tarjetaid != '') {
            $qb->Where('liquidacion.ntarjetaid = :tarjetaid')
                ->setParameter('tarjetaid', $tarjetaid);
        }

        if (isset($_unidades) && $_unidades != '') {
            $qb->andWhere($qb->expr()->in('liquidacion.nunidadid', ':unidades'))
                ->setParameter('unidades', $_unidades);
        }

        if (isset($_noVale) && $_noVale != '') {
            $qb->andWhere('clearstr(liquidacion.nroVale) like clearstr(:noVale)')
                ->setParameter('noVale', "%$_noVale%");
        }

        if (isset($fechaDesde) && $fechaDesde != '' && isset($fechaHasta) && $fechaHasta != '') {
            $qb->andWhere('liquidacion.fechaVale between :fechaDesde and :fechaHasta')
                ->setParameter('fechaDesde', $fechaDesde)
                ->setParameter('fechaHasta', $fechaHasta);
        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('liquidacion.fechaRegistro', 'DESC')
                ->setMaxResults($limit)
                ->setFirstResult($start)
                ->getQuery()->getResult();
        }
    }

    /**
     * @param $_tarjeta
     * @param $_unidad
     * @param $fechaDesde
     * @param $fechaHasta
     * @param bool $count
     * @return array|mixed
     */
    public function buscarLiquidacionCorregir($_tarjeta, $_unidad, $fechaDesde, $fechaHasta, $count = false)
    {
        $qb = $this->createQueryBuilder('liq');
        if ($count)
            $qb->select('count(liquidacion)');

        if (isset($_tarjeta) && $_tarjeta != '') {
            $qb->andWhere('liq.ntarjetaid = :tarjeta')
                ->setParameter('tarjeta', "$_tarjeta");
        }
        if (isset($_unidad) && $_unidad != '') {
            $qb->andWhere('liq.nunidadid = :unidad')
                ->setParameter('unidad', "$_unidad");
        }
        if (isset($fechaDesde) && $fechaDesde != '' && isset($fechaHasta) && $fechaHasta != '') {
            $qb->andWhere('liq.fechaVale between :fechaDesde and :fechaHasta')
                ->setParameter('fechaDesde', $fechaDesde)
                ->setParameter('fechaHasta', $fechaHasta);
        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('liquidacion.fechaVale', 'ASC')
                ->getQuery()->getResult();
        }
    }


}