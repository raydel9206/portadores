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

class AnticipoRepository extends EntityRepository
{
    public function buscarAnticipo($_noVale, $_matricula, $_nroTarjeta, $_estado, $_unidades, $fechaDesde, $fechaHasta, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('anticipo');
        if ($count)
            $qb->select('count(anticipo)');

        $qb->innerJoin('anticipo.tarjeta', 'tarjeta')
            ->innerJoin('anticipo.vehiculo', 'vehiculo')
            ->where($qb->expr()->in('tarjeta.nunidadid', ':unidades'))
            ->setParameter('unidades',$_unidades)
            ->andWhere($qb->expr()->in('vehiculo.nunidadid', ':unidades'))
            ->setParameter('unidades',$_unidades)
            ->andWhere($qb->expr()->eq('anticipo.visible', 'true'));

        if ($_noVale !== null && $_noVale != '') {
            $qb->andWhere('clearstr(anticipo.noVale) like clearstr(:noVale)')
                ->setParameter('noVale', "%$_noVale%");
        }
        if ($_matricula !== null && $_matricula != '') {
            $qb->andWhere('vehiculo.matricula like :matricula')
                ->setParameter('matricula', "%$_matricula%");
        }
        if ($_nroTarjeta !== null && $_nroTarjeta != '') {
            $qb->andWhere('clearstr(tarjeta.nroTarjeta) like clearstr(:nroTarjeta)')
                ->setParameter('nroTarjeta', "%$_nroTarjeta%");
        }
        if ($_estado !== null && $_estado != '') {
            if($_estado == '0')
                $qb->andWhere($qb->expr()->eq('anticipo.abierto', 'false'));
            if($_estado == '1')
                $qb->andWhere($qb->expr()->eq('anticipo.abierto', 'true'));
        }
        if ($fechaDesde !== null && $fechaDesde != '' && $fechaHasta !== null && $fechaHasta != '') {
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


    public function buscarTarjetaCombo($_unidades, $fechaDesde, $fechaHasta)
    {
        $qb = $this->createQueryBuilder('anticipo');
        $qb->select('tarjeta.id as tarjeta_id','tarjeta.nroTarjeta as nro_tarjeta', 'tipo_combustible.id as tipo_combustible_id');

        $qb->innerJoin('anticipo.tarjeta', 'tarjeta')
            ->innerJoin('tarjeta.ntipoCombustibleid', 'tipo_combustible')
            ->Where($qb->expr()->in('tarjeta.nunidadid', $_unidades))
            ->andWhere($qb->expr()->eq('anticipo.visible', ':visible'))
            ->setParameter('visible', true);

        if ($fechaDesde !== null && $fechaDesde != '' && $fechaHasta !== null && $fechaHasta != '') {
            $qb->andWhere('anticipo.fecha between :fechaDesde and :fechaHasta')
                ->setParameter('fechaDesde',  $fechaDesde)
                ->setParameter('fechaHasta', $fechaHasta);
        }

        return $qb->orderBy('anticipo.fecha', 'ASC')
                 ->getQuery()->getResult();
    }

    public function buscarTarjetaAnticipoCombo($_unidades)
    {
        $qb = $this->createQueryBuilder('anticipo');
        $qb->select('distinct tarjeta.id as tarjeta_id','tarjeta.nroTarjeta as nro_tarjeta', 'tipo_combustible.id as tipo_combustible_id');

        $qb->innerJoin('anticipo.tarjeta', 'tarjeta')
            ->innerJoin('tarjeta.ntipoCombustibleid', 'tipo_combustible')
            ->Where($qb->expr()->in('tarjeta.nunidadid', $_unidades))
            ->andWhere($qb->expr()->eq('anticipo.visible', ':visible'))
            ->setParameter('visible', true)
            ->andWhere($qb->expr()->eq('anticipo.abierto', 'true'));

        if (isset($fechaDesde) && $fechaDesde != '' && isset($fechaHasta) && $fechaHasta != '') {
            $qb->andWhere('anticipo.fecha between :anticipo and :fechaHasta')
                ->setParameter('fechaDesde', $fechaDesde)
                ->setParameter('fechaHasta', $fechaHasta);
        }

        return $qb->getQuery()->getResult();
    }

}