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

class ProductoRepository extends EntityRepository
{
    /**
     * @param $_nombre
     * @param null $start
     * @param null $limit
     * @param bool $count
     * @return array|mixed
     */
    public function buscarProducto($_nombre, $fila, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('producto');
        if ($count)
            $qb->select('count(producto)');

        $qb->andWhere($qb->expr()->eq('producto.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('lower(producto.nombre) like lower(:nombre)')
                ->setParameter('nombre', "%$_nombre%");
        }

        if (isset($fila) && $fila != '') {
            $qb->andWhere('lower(producto.fila) like lower(:fila)')
                ->setParameter('fila', "%$fila%");
        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('producto.fila', 'ASC')
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
    public function buscarProductoRepetido($_nombre, $fila, $id = '')
    {
        $qb = $this->createQueryBuilder('producto');
        $qb->select('count(producto)');

        $qb->andWhere($qb->expr()->eq('producto.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('lower(producto.nombre) = lower(:nombre)')
                ->setParameter('nombre', $_nombre);
        }

        if (isset($_nombre) && $_nombre != '') {
            $qb->orWhere('lower(producto.fila) = lower(:fila)')
                ->setParameter('fila', $fila);
        }

        if (isset($id) && $id != '') {
            $qb->andWhere('producto.id != :id')
                ->setParameter('id', $id);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

}