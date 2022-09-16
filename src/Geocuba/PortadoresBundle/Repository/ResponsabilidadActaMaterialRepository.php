<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pfcadenas
 * Date: 15/12/2016
 * Time: 13:50
 * To change this template use File | Settings | File Templates.
 */

namespace Geocuba\PortadoresBundle\Repository;
use Doctrine\ORM\EntityRepository;

class ResponsabilidadActaMaterialRepository extends EntityRepository
{

    /**
     * @param $_nombre
     * @param null $start
     * @param null $limit
     * @param bool $count
     * @return array|mixed
     */
    public function buscarResponsabilidadActaMaterial($_nombre, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('ResponsabilidadActaMaterial');
        if ($count)
            $qb->select('count(ResponsabilidadActaMaterial)');


        $qb->Where($qb->expr()->eq('ResponsabilidadActaMaterial.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('lower(ResponsabilidadActaMaterial.nombre) like lower(:nombre)')
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
    public function buscarResponsabilidadActaMaterialRepetido($_nombre, $id = '')
    {
        $qb = $this->createQueryBuilder('ResponsabilidadActaMaterial');
        $qb->select('count(ResponsabilidadActaMaterial)');

        $qb->andWhere($qb->expr()->eq('ResponsabilidadActaMaterial.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('lower(ResponsabilidadActaMaterial.nombre) = lower(:nombre)')
                ->setParameter('nombre', $_nombre);
        }

        if (isset($id) && $id != '') {
            $qb->andWhere('ResponsabilidadActaMaterial.id != :id')
                ->setParameter('id', $id);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }
}