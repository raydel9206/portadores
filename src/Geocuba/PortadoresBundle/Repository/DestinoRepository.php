<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pfcadenas
 * Date: 8/12/2016
 * Time: 09:30
 * To change this template use File | Settings | File Templates.
 */

namespace Geocuba\PortadoresBundle\Repository;
use Doctrine\ORM\EntityRepository;

class DestinoRepository extends EntityRepository {

    public function buscarDestino($_nombre, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('destino');
        if ($count)
            $qb->select('count(destino)');

        $qb->andWhere($qb->expr()->eq('destino.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('clearstr(destino.nombre) like clearstr(:nombre)')
                ->setParameter('nombre', "%$_nombre%");
        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('destino.nombre', 'ASC')
                ->setMaxResults($limit)
                ->setFirstResult($start)
                ->getQuery()->getResult();
        }
    }
}