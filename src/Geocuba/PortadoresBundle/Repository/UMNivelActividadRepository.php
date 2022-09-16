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

class UMNivelActividadRepository extends EntityRepository
{
    /**
     * @param $_nombre
     * @param null $start
     * @param null $limit
     * @param bool $count
     * @return array|mixed
     */
    public function buscarUmNivelActividad($_nombre, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('umactividad');
        if ($count)
            $qb->select('count(umactividad)');

        $qb->andWhere($qb->expr()->eq('umactividad.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('lower(umactividad.nivelActividad) like lower(:nombre)')
                ->setParameter('nombre', "%$_nombre%");
        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('umactividad.nivelActividad', 'ASC')
                ->setMaxResults($limit)
                ->setFirstResult($start)
                ->getQuery()->getResult();
        }
    }

    /**
     * @param $_nombre
     * @param string $id
     * @return mixed
     */
    public function buscarUmNivelActividadRepetido($_nombre, $id = '')
    {
        $qb = $this->createQueryBuilder('umactividad');
        $qb->select('count(umactividad)');

        $qb->andWhere($qb->expr()->eq('umactividad.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('lower(umactividad.nivelActividad) = lower(:nombre)')
                ->setParameter('nombre', $_nombre);
        }

        if (isset($id) && $id != '') {
            $qb->andWhere('umactividad.id != :id')
                ->setParameter('id', $id);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }
}