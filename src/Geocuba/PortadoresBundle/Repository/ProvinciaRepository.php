<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Javier
 * Date: 16/01/14
 * Time: 16:09
 * To change this template use File | Settings | File Templates.
 */

namespace Geocuba\PortadoresBundle\Repository;
use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityRepository;

class ProvinciaRepository extends EntityRepository
{
    /**
     * @param $_nombre
     * @param string $_codigo
     * @param string $id
     * @return mixed
     */
    public function buscarProvinciaRepetido($_nombre, $_codigo)
    {
        $qb = $this->createQueryBuilder('provincia');
        $qb->select('count(provincia)');

        $qb->andWhere($qb->expr()->eq('provincia.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('lower(provincia.nombre) = lower(:nombre)')
                ->setParameter('nombre', $_nombre);
        }

        if (isset($_codigo) && $_codigo != '') {
            $qb->andWhere('provincia.codigo = :codigo')
                ->setParameter('codigo', $_codigo);
        }

        if (isset($id) && $id != '') {
            $qb->andWhere('provincia.id != :id')
                ->setParameter('id', $id);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

}