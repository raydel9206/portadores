<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pfcadenas
 * Date: 16/12/2016
 * Time: 10:00
 * To change this template use File | Settings | File Templates.
 */

namespace Geocuba\PortadoresBundle\Repository;
use Doctrine\ORM\EntityRepository;

class UnidadMedidaRepository extends EntityRepository
{

    /**
     * @param $_nombre
     * @param null $start
     * @param null $limit
     * @param bool $count
     * @return array|mixed
     */
    public function buscarUnidadMedida($_nombre, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('unidadMedida');
        if ($count)
            $qb->select('count(unidadMedida)');

        $qb->andWhere($qb->expr()->eq('unidadMedida.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('lower(unidadMedida.nombre) like lower(:nombre)')
                ->setParameter('nombre', "%$_nombre%");
        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->setMaxResults($limit)
                ->setFirstResult($start)
                ->getQuery()->getResult();
        }
    }

    /**
     * @param $_nombre
     * @param string $id
     * @return mixed
     */
    public function buscarUnidadMedidaRepetido($_nombre, $id = '')
    {
        $qb = $this->createQueryBuilder('unidadMedida');
        $qb->select('count(unidadMedida)');

        $qb->andWhere($qb->expr()->eq('unidadMedida.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('lower(unidadMedida.nombre) = lower(:nombre)')
                ->setParameter('nombre', $_nombre);
        }

        if (isset($id) && $id != '') {
            $qb->andWhere('unidadMedida.id != :id')
                ->setParameter('id', $id);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }
}