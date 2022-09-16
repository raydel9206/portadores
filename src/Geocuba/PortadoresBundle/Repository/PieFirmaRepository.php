<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pfcadenas
 * Date: 25/04/2017
 * Time: 13:25
 * To change this template use File | Settings | File Templates.
 */

namespace Geocuba\PortadoresBundle\Repository;

use Doctrine\ORM\EntityRepository;

class PieFirmaRepository extends EntityRepository
{
    /**
     * @param null $start
     * @param null $limit
     * @param bool $count
     * @return array|mixed
     */
    public function buscarPieFirma($_unidades, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('pieFirma');

        if ($count)
            $qb->select('count(pieFirma)');

        if ($_unidades !== null && $_unidades != '') {
            $qb->andWhere($qb->expr()->in('pieFirma.nunidadid', $_unidades));
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
     * @param $documento
     * @param string $id
     * @return mixed
     */
    public function buscarPieFirmaRepetido($documento, $_unidades, $id = '')
    {
        $qb = $this->createQueryBuilder('pieFirma');
        $qb->select('count(pieFirma)');

        if (isset($documento) && $documento != '') {
            $qb->andWhere('clearstr(pieFirma.documento) = clearstr(:documento)')
                ->setParameter('documento', $documento);
        }

        if (isset($id) && $id != '') {
            $qb->andWhere('pieFirma.id != :id')
                ->setParameter('id', $id);
        }

        if ($_unidades !== null && $_unidades != '') {
            $qb->andWhere($qb->expr()->in('pieFirma.nunidadid', $_unidades));
        }

        return $qb->getQuery()->getSingleScalarResult();
    }
}