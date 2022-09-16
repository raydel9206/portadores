<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pfcadenas
 * Date: 15/12/2016
 * Time: 16:40
 * To change this template use File | Settings | File Templates.
 */

namespace Geocuba\PortadoresBundle\Repository;
use Doctrine\ORM\EntityRepository;

class MonedaRepository extends EntityRepository
{
    /**
     * @param $_nombre
     * @param string $id
     * @return mixed
     */
    public function buscarMonedaRepetido($_nombre, $id = '')
    {
        $qb = $this->createQueryBuilder('moneda');
        $qb->select('count(moneda)');

        $qb->andWhere($qb->expr()->eq('moneda.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('lower(moneda.nombre) = lower(:nombre)')
                ->setParameter('nombre', $_nombre);
        }

        if (isset($id) && $id != '') {
            $qb->andWhere('moneda.id != :id')
                ->setParameter('id', $id);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }
}