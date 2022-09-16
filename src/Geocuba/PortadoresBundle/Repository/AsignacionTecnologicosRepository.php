<?php

namespace Geocuba\PortadoresBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Geocuba\PortadoresBundle\Util\FechaUtil;

class AsignacionTecnologicosRepository extends EntityRepository
{
    public function findAllBy($_unidades, $mes, $anno, $tipoCombustibleId = null)
    {
        $fechaDesde = $anno . '-' . $mes . '-' . '1';
        $fechaHasta = FechaUtil::getUltimoDiaMes($mes, $anno);

        $qb = $this->createQueryBuilder('asignacion');
        $qb->where($qb->expr()->in('asignacion.unidad', $_unidades))
            ->andWhere('asignacion.fecha between :fechaDesde and :fechaHasta')
            ->setParameters([
                'fechaDesde' => $fechaDesde,
                'fechaHasta' => $fechaHasta
            ]);

        if ($tipoCombustibleId) {
            $qb->andWhere($qb->expr()->eq('asignacion.tipoCombustible', ':tipoCombustibleId'))
                ->setParameter('tipoCombustibleId', $tipoCombustibleId);
        }

        return $qb->orderBy('asignacion.fecha', 'ASC')
            ->getQuery()->getResult();
    }
}