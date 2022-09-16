<?php

namespace Geocuba\PortadoresBundle\Repository;
use Doctrine\ORM\EntityRepository;

class RegistroOperacionCalderaRepository extends EntityRepository {

    /**
     * @param $equipo
     * @param $fechaDesde
     * @param $fechaHasta
     * @return array|mixed
     */
    public function findAllBy($equipo, $fechaDesde, $fechaHasta)
    {
        $qb = $this->createQueryBuilder('registro');
        $qb->where($qb->expr()->eq('registro.equipoTecnologico', ':equipo'))
            ->andWhere($qb->expr()->between('registro.fecha', ':fechaDesde', ':fechaHasta'))
            ->setParameter('equipo', $equipo)
            ->setParameter('fechaDesde', $fechaDesde)
            ->setParameter('fechaHasta', $fechaHasta);

        return $qb->orderBy('registro.fecha')
            ->addOrderBy('registro.horaArranque')
            ->getQuery()->getResult();
    }
}