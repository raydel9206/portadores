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

class AreaRepository extends EntityRepository {

    /**
     * @param $_nombre
     * @param null $start
     * @param null $limit
     * @param bool $count
     * @param string $unidad
     * @return array|mixed
     */
    public function buscarArea($_nombre, $start = null, $limit = null, $count = false,$unidad)
    {
//        print_r($unidad);die;
        $qb = $this->createQueryBuilder('area');
        if ($count)
            $qb->select('count(area)');

        $qb->Where($qb->expr()->eq('area.unidad', ':unidad'))
            ->setParameter('unidad', "%$unidad%");
        $qb->andWhere($qb->expr()->eq('area.visible', ':visible'))
            ->setParameter('visible', true);



        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('lower(area.nombre) like lower(:nombre)')
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

    public function buscarAreaCombo($_unidades)
    {
        $qb = $this->createQueryBuilder('area');
        $qb->select('area.id','area.nombre','unidad.id as nunidadid');

        $qb->innerJoin('area.unidad', 'unidad')
            ->where($qb->expr()->in('area.unidad', ':unidades'))
            ->setParameter('unidades',$_unidades)
            ->andWhere($qb->expr()->eq('area.visible', ':visible'))
            ->setParameter('visible', true);

        return $qb->orderBy('area.nombre', 'ASC')
            ->getQuery()->getResult();
    }

    /**
     * @param $_nombre
     * @param string $id
     * @return mixed
     */
    public function buscarAreaRepetido($_nombre, $id = '')
    {
        $qb = $this->createQueryBuilder('area');
        $qb->select('count(area)');

        $qb->andWhere($qb->expr()->eq('area.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('lower(area.nombre) = lower(:nombre)')
                ->setParameter('nombre', $_nombre);
        }

        if (isset($id) && $id != '') {
            $qb->andWhere('area.id != :id')
                ->setParameter('id', $id);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

}