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

class TipoCombustibleRepository extends EntityRepository
{
    /**
     * @param $_nombre
     * @param null $start
     * @param null $limit
     * @param bool $count
     * @return array|mixed
     */
    public function buscarTipoCombustible($_nombre, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('tipoCombustible');
        if ($count)
            $qb->select('count(tipoCombustible)');

        $qb->andWhere($qb->expr()->eq('tipoCombustible.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('lower(tipoCombustible.nombre) like lower(:nombre)')
                ->setParameter('nombre', "%$_nombre%");
        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('tipoCombustible.nombre', 'ASC')
                ->setMaxResults($limit)
                ->setFirstResult($start)
                ->getQuery()->getResult();
        }
    }

    public function buscartipoCombustibleCombo()
    {
        $qb = $this->createQueryBuilder('tipoCombustible');
        $qb->select('tipoCombustible.id', 'tipoCombustible.nombre', 'tipoCombustible.precio', 'tipoCombustible.codigo')
            ->andWhere($qb->expr()->eq('tipoCombustible.visible', ':visible'))
            ->setParameter('visible', true);

        return $qb->orderBy('tipoCombustible.nombre', 'ASC')
            ->getQuery()->getResult();
    }

    /**
     * @param $_nombre
     * @param string $id
     * @return mixed
     */
    public function buscarTipoCombustibleRepetido($_nombre, $id = '')
    {
        $qb = $this->createQueryBuilder('tipoCombustible');
        $qb->select('count(tipoCombustible)');

        $qb->andWhere($qb->expr()->eq('tipoCombustible.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('lower(tipoCombustible.nombre) = lower(:nombre)')
                ->setParameter('nombre', $_nombre);
        }

        if (isset($id) && $id != '') {
            $qb->andWhere('tipoCombustible.id != :id')
                ->setParameter('id', $id);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function buscarPortadoresconTComb(){
        $qb = $this->createQueryBuilder('tipocombustible');
        $qb->select('portador.id');

        $qb->innerJoin('tipocombustible.portadorid', 'portador')
            ->Where($qb->expr()->eq('tipocombustible.visible', ':visible'))
            ->setParameter('visible', true)
            ->andWhere($qb->expr()->eq('portador.visible', ':visible'))
            ->setParameter('visible', true);

        return $qb->groupBy('portador.id')
            ->getQuery()->getResult();
    }
}