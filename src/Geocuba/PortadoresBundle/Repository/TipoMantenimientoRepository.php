<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pfcadnas
 * Date: 9/12/2016
 * Time: 15:40
 * To change this template use File | Settings | File Templates.
 */

namespace Geocuba\PortadoresBundle\Repository;

use Doctrine\ORM\EntityRepository;

class TipoMantenimientoRepository extends EntityRepository
{

    public function buscarTipoMantenimiento($_clasificacion, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('tipoMantenimiento');
        if ($count)
            $qb->select('count(tipoMantenimiento)');

        $qb->andWhere($qb->expr()->eq('tipoMantenimiento.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_clasificacion) && $_clasificacion != '') {
            $qb->andWhere('lower(tipoMantenimiento.clasificacion) = lower(:clasificacion)')
                ->setParameter('clasificacion', $_clasificacion);
        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('tipoMantenimiento.nombre', 'ASC')
                ->setMaxResults($limit)
                ->setFirstResult($start)
                ->getQuery()->getResult();
        }
    }

    public function buscarTipoMantenimientoCombo($_clasificacion)
    {
        $qb = $this->createQueryBuilder('tipoMantenimiento');
        $qb->select('tipoMantenimiento.id', 'tipoMantenimiento.nombre');

        if (isset($_clasificacion) && $_clasificacion != '') {
            $qb->andWhere('lower(tipoMantenimiento.clasificacion=) = lower(:clasificacion)')
                ->setParameter('clasificacion', $_clasificacion);
        }
            $qb->Where($qb->expr()->eq('tipoMantenimiento.visible', ':visible'))
                ->setParameter('visible', true);

        return $qb->orderBy('tipoMantenimiento.nombre', 'ASC')
            ->getQuery()->getResult();
    }
}