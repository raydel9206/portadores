<?php
/**
 * Created by PhpStorm.
 * User: asisoftware13
 * Date: 5/4/2021
 * Time: 7:06 a.m.
 */

namespace Geocuba\PortadoresBundle\Repository;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityRepository;


class TrasladoRepository extends EntityRepository
{
    /**
     * @param $_unidades
     * @return array|mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function buscarTrasladoHacia($_unidades)
    {
        $qb = $this->createQueryBuilder('traslado');

        $qb->innerJoin('traslado.vehiculo', 'vehiculo')
            ->where($qb->expr()->in('traslado.hacia', ':unidades'))
            ->setParameter('unidades', $_unidades);

        return $qb->orderBy('vehiculo.matricula', 'ASC')
            ->getQuery()->getResult();
    }

    public function buscarTrasladoDesde($_unidades)
    {
        $qb = $this->createQueryBuilder('traslado');

        $qb->innerJoin('traslado.vehiculo', 'vehiculo')
            ->where($qb->expr()->in('traslado.desde', ':unidades'))
            ->setParameter('unidades', $_unidades);

        return $qb->orderBy('vehiculo.matricula', 'ASC')
            ->getQuery()->getResult();
    }
}