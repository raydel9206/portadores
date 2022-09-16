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


class ChoferRepository extends EntityRepository {

    public function buscarChofer($_nombre, $_unidades, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('chofer');
        if ($count)
            $qb->select('count(chofer)');

        $qb->innerJoin('chofer.npersonaid', 'persona')
            ->Where($qb->expr()->in('persona.nunidadid', $_unidades))
            ->andWhere($qb->expr()->eq('chofer.visible', 'true'));

        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('trim(lower(persona.nombre)) like lower(:nombre)')
                ->setParameter('nombre', "%$_nombre%");
        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('persona.nombre', 'ASC')
                ->setMaxResults($limit)
                ->setFirstResult($start)
                ->getQuery()->getResult();
        }
    }
}