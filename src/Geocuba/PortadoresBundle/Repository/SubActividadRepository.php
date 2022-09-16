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

class SubActividadRepository extends EntityRepository
{
    /**
     * @param $_nombre
     * @param null $start
     * @param null $limit
     * @param bool $count
     * @return array|mixed
     */
    public function buscarSubactivudades($_nombre, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('subactividad');
        if ($count)
            $qb->select('count(subactividad)');

        $qb->innerJoin('subactividad.nactividadid', 'actividad')
            ->andWhere($qb->expr()->eq('subactividad.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('lower(subactividad.nombre) like lower(:nombre)')
                ->setParameter('nombre', "%$_nombre%");
        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('actividad.nombre', 'ASC')
                ->setMaxResults($limit)
                ->setFirstResult($start)
                ->getQuery()->getResult();
        }
    }

    /**
     * @param $_nombre
     * @param $_actividad
     * @param string $id
     * @return mixed
     */
    public function buscarSubactivudadesRepetido($_nombre, $_actividad, $id = '')
    {
        $qb = $this->createQueryBuilder('subactividad');
        $qb->select('count(subactividad)');

        $qb->andWhere($qb->expr()->eq('subactividad.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('lower(subactividad.nombre) = lower(:nombre)')
                ->setParameter('nombre', $_nombre);
        }

        if (isset($_actividad) && $_actividad != '') {
            $qb->andWhere('subactividad.nactividadid = :actividad')
                ->setParameter('actividad', $_actividad);
        }

        if (isset($id) && $id != '') {
            $qb->andWhere('subactividad.id != :id')
                ->setParameter('id', $id);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }
}