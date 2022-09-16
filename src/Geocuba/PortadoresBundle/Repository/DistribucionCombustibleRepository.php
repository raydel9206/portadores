<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pfcadenas
 * Date: 02/06/2016
 * Time: 16:40
 * To change this template use File | Settings | File Templates.
 */

namespace Geocuba\PortadoresBundle\Repository;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityRepository;

class DistribucionCombustibleRepository extends EntityRepository
{
    public function buscarDistribucionCombustible($_tipoCombustible, $_unidades, $mes, $anno, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('distribucion');
        if ($count)
            $qb->select('count(distribucion)');

        $qb->andWhere($qb->expr()->eq('distribucion.visible', 'true'));

        if (isset($_tipoCombustible) && $_tipoCombustible != '') {
            $qb->andWhere('distribucion.tipoCombustible = :tipoCombustible')
                ->setParameter('tipoCombustible', "$_tipoCombustible");
        }

        if (isset($_unidades) && $_unidades != '') {
            $qb->andWhere($qb->expr()->in('distribucion.nunidadid', $_unidades));
        }

        if (isset($mes) && $mes != '') {
            $qb->andWhere('distribucion.mes = :mes')
                ->setParameter('mes', "$mes");
        }

        if (isset($anno) && $anno != '') {
            $qb->andWhere('distribucion.anno = :anno')
                ->setParameter('anno', "$anno");
        }

//        if (isset($fechaDesde) && $fechaDesde != '' && isset($fechaHasta) && $fechaHasta != '') {
//            $qb->andWhere('distribucion.fecha between :fechaDesde and :fechaHasta')
//                ->setParameter('fechaDesde', $fechaDesde)
//                ->setParameter('fechaHasta', $fechaHasta);
//        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('distribucion.fecha', 'ASC')
                ->setMaxResults($limit)
                ->setFirstResult($start)
                ->getQuery()->getResult();
        }
    }

    public function buscarDistribucionCombustibleSinCheque($_tipoCombustible, $_unidades, $aprobada = false, $fechaDesde = null, $fechaHasta = null, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('distribucion');
        if ($count)
            $qb->select('count(distribucion)');

        $qb->andWhere($qb->expr()->eq('distribucion.visible', 'true'));

        if (isset($aprobada) && $aprobada != '') {
            $qb->andWhere('distribucion.aprobada = :aprobada')
                ->setParameter('aprobada', "$aprobada");
        }

        if (isset($_tipoCombustible) && $_tipoCombustible != '') {
            $qb->andWhere('distribucion.tipoCombustible = :tipoCombustible')
                ->setParameter('tipoCombustible', "$_tipoCombustible");
        }

        if (isset($_unidades) && $_unidades != '') {
            $qb->andWhere($qb->expr()->in('distribucion.nunidadid', $_unidades));
        }

        if (isset($fechaDesde) && $fechaDesde != '' && isset($fechaHasta) && $fechaHasta != '') {
            $qb->andWhere('distribucion.fecha between :fechaDesde and :fechaHasta')
                ->setParameter('fechaDesde', $fechaDesde)
                ->setParameter('fechaHasta', $fechaHasta);
        }

        $qb->andWhere('distribucion.cheque is NULL');


        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('distribucion.fecha', 'ASC')
                ->setMaxResults($limit)
                ->setFirstResult($start)
                ->getQuery()->getResult();
        }
    }

    public function buscarByCheque($cheque_id)
    {
        $qb = $this->createQueryBuilder('distribucion');
        $qb->select('distribucion.id');

        $qb->Where('distribucion.cheque = :cheque')
            ->setParameter('cheque', $cheque_id);

        $qb->andWhere($qb->expr()->eq('distribucion.visible', ':visible'))
            ->setParameter('visible', true);

        return $qb->getQuery()->getResult();
    }
}