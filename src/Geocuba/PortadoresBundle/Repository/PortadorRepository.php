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

class PortadorRepository extends EntityRepository
{
    /**
     * @param $_nombre
     * @param null $start
     * @param null $limit
     * @param bool $count
     * @return array|mixed
     */
    public function buscarPortador($_nombre, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('portador');
        if ($count)
            $qb->select('count(portador)');

        $qb->andWhere($qb->expr()->eq('portador.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('lower(portador.nombre) like lower(:nombre)')
                ->setParameter('nombre', "%$_nombre%");
        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('portador.nombre', 'ASC')
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
    public function buscarPortadorRepetido($_nombre, $id = '')
    {
        $qb = $this->createQueryBuilder('portador');
        $qb->select('count(portador)');

        $qb->andWhere($qb->expr()->eq('portador.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('lower(portador.nombre) = lower(:nombre)')
                ->setParameter('nombre', $_nombre);
        }

        if (isset($id) && $id != '') {
            $qb->andWhere('portador.id != :id')
                ->setParameter('id', $id);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

//    public function buscarPortadoresconTComb(){
//        $qb = $this->createQueryBuilder('caja');
//        $qb->select('caja.id','caja.nombre','unidad.id as nunidadid');
//
//        $qb->innerJoin('caja.nunidadid', 'unidad')
//            ->where($qb->expr()->in('caja.nunidadid', ':unidades'))
//            ->setParameter('unidades',$_unidades)
//            ->andWhere($qb->expr()->eq('caja.visible', ':visible'))
//            ->setParameter('visible', true);
//
//        return $qb->orderBy('caja.nombre', 'ASC')
//            ->getQuery()->getResult();
//    }
}