<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pfcadenas
 * Date: 02/06/2016
 * Time: 16:40
 * To change this template use File | Settings | File Templates.
 */

namespace Geocuba\PortadoresBundle\Repository;

use Doctrine\ORM\EntityRepository;

class DistribucionCombustibleDesgloseRepository extends EntityRepository
{
    public function buscarAnticipo($_noVale, $_matricula, $_nroTarjeta, $_estado, $_unidades, $fechaDesde, $fechaHasta, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('anticipo');
        if ($count)
            $qb->select('count(anticipo)');

        $qb->innerJoin('anticipo.tarjeta', 'tarjeta')
            ->innerJoin('anticipo.vehiculo', 'vehiculo')
            ->Where($qb->expr()->in('tarjeta.nunidadid', $_unidades))
            ->andWhere($qb->expr()->in('vehiculo.nunidadid', $_unidades))
            ->andWhere($qb->expr()->eq('anticipo.visible', 'true'));
        if (isset($_noVale) && $_noVale != '') {
            $qb->andWhere('clearstr(anticipo.noVale) like clearstr(:noVale)')
                ->setParameter('noVale', "%$_noVale%");
        }
        if (isset($_matricula) && $_matricula != '') {
            $qb->andWhere('vehiculo.matricula like :matricula')
                ->setParameter('matricula', "%$_matricula%");
        }
        if (isset($_nroTarjeta) && $_nroTarjeta != '') {
            $qb->andWhere('clearstr(tarjeta.nroTarjeta) like clearstr(:nroTarjeta)')
                ->setParameter('nroTarjeta', "%$_nroTarjeta%");
        }
        if (isset($_estado) && $_estado != '') {
            if($_estado == '0')
                $qb->andWhere($qb->expr()->eq('anticipo.abierto', 'false'));
            if($_estado == '1')
                $qb->andWhere($qb->expr()->eq('anticipo.abierto', 'true'));
        }
        if (isset($fechaDesde) && $fechaDesde != '' && isset($fechaHasta) && $fechaHasta != '') {
            $qb->andWhere('anticipo.fecha between :fechaDesde and :fechaHasta')
                ->setParameter('fechaDesde',  $fechaDesde)
                ->setParameter('fechaHasta', $fechaHasta);
        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('anticipo.fecha', 'ASC')
                ->setMaxResults($limit)
                ->setFirstResult($start)
                ->getQuery()->getResult();
        }
    }
}