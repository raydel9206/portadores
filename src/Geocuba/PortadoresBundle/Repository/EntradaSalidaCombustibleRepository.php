<?php

namespace Geocuba\PortadoresBundle\Repository;
use Doctrine\ORM\EntityRepository;

class EntradaSalidaCombustibleRepository extends EntityRepository {

    /**
     * @param $tanqueId
     * @param $fechaInicio
     * @param $fechaFinal
     * @param string $order
     * @return array|mixed
     */
    public function findAllBy($tanqueId, $fechaInicio, $fechaFinal, $order = 'ASC')
    {
        $qb = $this->createQueryBuilder('entradaSalida');
        $qb->select('entradaSalida')
            ->Where($qb->expr()->eq('entradaSalida.tanque', ':tanqueId'))
            ->setParameter('tanqueId', $tanqueId);

        if ($fechaInicio){
            $qb->andWhere($qb->expr()->gte('entradaSalida.fecha', ':fechaInicio'))
            ->setParameter('fechaInicio', $fechaInicio);
        }
        if ($fechaFinal){
            $qb->andWhere($qb->expr()->lte('entradaSalida.fecha', ':fechaFinal'))
                ->setParameter('fechaFinal', $fechaFinal);
        }

        return $qb->orderBy('entradaSalida.fecha', $order)->getQuery()->getResult();
    }
}