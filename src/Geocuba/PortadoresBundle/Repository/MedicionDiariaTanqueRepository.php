<?php

namespace Geocuba\PortadoresBundle\Repository;
use DateTime;
use Doctrine\ORM\EntityRepository;

class MedicionDiariaTanqueRepository extends EntityRepository {

    public function findAllBy($tanqueId, $fechaInicio, $fechaFinal, $order = 'ASC')
    {
        $qb = $this->createQueryBuilder('medicion');
        $qb->select('medicion')
            ->Where($qb->expr()->eq('medicion.tanque', ':tanqueId'))
            ->setParameter('tanqueId', $tanqueId);

        if ($fechaInicio){
            $qb->andWhere($qb->expr()->gte('medicion.fecha', ':fechaInicio'))
                ->setParameter('fechaInicio', $fechaInicio);
        }
        if ($fechaFinal){
            $qb->andWhere($qb->expr()->lte('medicion.fecha', ':fechaFinal'))
                ->setParameter('fechaFinal', $fechaFinal);
        }

        return $qb->orderBy('medicion.fecha', $order)->getQuery()->getResult();
    }

    /**
     * @param DateTime $fecha
     * @return array|mixed
     */
    public function findByDate($fecha)
    {
        $fechaAntes = $fecha->format('Y-m-d') . ' 00:00:00';
        $fechaDespues = $fecha->format('Y-m-d') . ' 23:59:59';

        $qb = $this->createQueryBuilder('medicion');
        $qb->select('medicion')
            ->Where($qb->expr()->between('medicion.fecha', ':fechaAntes', ':fechaDespues'))
            ->setParameter('fechaAntes', $fechaAntes)
            ->setParameter('fechaDespues', $fechaDespues);

        return $qb->orderBy('medicion.fecha', 'DESC')->getQuery()->getResult();
    }
}