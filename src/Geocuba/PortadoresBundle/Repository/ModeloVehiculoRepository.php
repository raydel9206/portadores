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

class ModeloVehiculoRepository extends EntityRepository
{

    /**
     * @param $_nombre
     * @param $_marca
     * @param $_tipo_equipo
     * @param null $start
     * @param null $limit
     * @param bool $count
     * @return array|mixed
     */
    public function buscarModelo($_nombre, $_marca, $_tipo_equipo, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('modelo');
        if ($count)
            $qb->select('count(modelo)');

        $qb->innerJoin('modelo.marcaVehiculoid', 'marcaVehiculoid');

        $qb->andWhere($qb->expr()->eq('modelo.visible', ':visible'))
            ->andWhere($qb->expr()->eq('marcaVehiculoid.visible', ':marcavisible'))
            ->setParameter('visible', true)
            ->setParameter('marcavisible', true);

        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('lower(modelo.nombre) like lower(:nombre)')
                ->setParameter('nombre', "%$_nombre%");
        }

        if (isset($_marca) && $_marca != '') {
            $qb->andWhere('marcaVehiculoid.id = :marca')
                ->setParameter('marca', "$_marca");
        }

        if (isset($_tipo_equipo) && $_tipo_equipo != '') {
            $qb->andWhere($qb->expr()->eq('marcaVehiculoid.tipoEquipo', ':tipoEquipo'))
                ->setParameter('tipoEquipo', $_tipo_equipo);
        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('marcaVehiculoid.nombre')
                ->addOrderBy('modelo.nombre')
                ->setMaxResults($limit)
                ->setFirstResult($start)
                ->getQuery()->getResult();
        }
    }

    public function buscarModeloRepetido($_nombre, $marcaid)
    {
        $qb = $this->createQueryBuilder('modelo');
        $qb->select('count(modelo)');

        $qb->andWhere($qb->expr()->eq('modelo.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('lower(modelo.nombre) = lower(:nombre)')
                ->setParameter('nombre', $_nombre);
        }

        if (isset($marcaid) && $marcaid != '') {
            $qb->andWhere('modelo.marcaVehiculoid = :marcaid')
                ->setParameter('marcaid', $marcaid);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function buscarModeloCombo($_marca)
    {
        $qb = $this->createQueryBuilder('modelo');
        $qb->select('modelo.id','modelo.nombre');

        $qb->innerJoin('modelo.marcaVehiculoid', 'marcaVehiculoid');

        if (isset($_marca) && $_marca != '') {
            $qb->andWhere('marcaVehiculoid.id = :marca')
                ->setParameter('marca', "$_marca");
        }

        $qb->andWhere($qb->expr()->eq('modelo.visible', ':visible'))
            ->setParameter('visible', true);

        return $qb->orderBy('modelo.nombre', 'ASC')
            ->getQuery()->getResult();
    }
}