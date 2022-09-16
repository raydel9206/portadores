<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pfcadenas
 * Date: 27/09/16
 * Time: 8:00
 * To change this template use File | Settings | File Templates.
 */

namespace Geocuba\PortadoresBundle\Repository;

use Doctrine\ORM\EntityRepository;

class FamiliaRepository extends EntityRepository
{
    /**
     * @param $nombre
     * @param null $start
     * @param null $limit
     * @param bool $count
     * @return array|mixed
     */
    public function buscarFamilia($nombre, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('Familia');
        if ($count)
            $qb->select('count(Familia)');

        $qb->andWhere($qb->expr()->eq('Familia.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($nombre) && $nombre != '') {
            $qb->andWhere('lower(Familia.nombre) like lower(:nombre)')
                ->setParameter('nombre', "%$nombre%");
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
     * @param $nombre
     * @param string $id
     * @return array|mixed
     */
    public function buscarFamiliaRepetido($nombre, $id = '')
    {
        $qb = $this->createQueryBuilder('Familia');
        $qb->select('count(Familia)');

        $qb->andWhere($qb->expr()->eq('Familia.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($nombre) && $nombre != '') {
            $qb->andWhere('lower(Familia.nombre) = lower(:nombre)')
                ->setParameter('nombre', $nombre);
        }

        if (isset($id) && $id != '') {
            $qb->andWhere('Familia.id != :id')
                ->setParameter('id', $id);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }
}