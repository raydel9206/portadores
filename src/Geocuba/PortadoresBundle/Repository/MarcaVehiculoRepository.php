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

class MarcaVehiculoRepository extends EntityRepository
{

    public function buscarMarcaVehiculo($buscar_nombre, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('marca');
        if ($count)
            $qb->select('count(marca)');

        $qb->Where($qb->expr()->eq('marca.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($buscar_nombre) && $buscar_nombre != '') {
            $qb->andWhere('lower(marca.nombre) like lower(:nombre)')
                ->setParameter('nombre', "%$buscar_nombre%");
        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('marca.nombre')
                ->setMaxResults($limit)
                ->setFirstResult($start)
                ->getQuery()->getResult();
        }
    }

    public function buscarmarcaCombo()
    {
        $qb = $this->createQueryBuilder('marca');
        $qb->select('marca.id', 'marca.nombre')
            ->andWhere($qb->expr()->eq('marca.visible', ':visible'))
            ->setParameter('visible', true);

        return $qb->orderBy('marca.nombre', 'ASC')
            ->getQuery()->getResult();
    }
}