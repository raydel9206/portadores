<?php
/**
 * Created by PhpStorm.
 * User: yosley
 * Date: 10/03/2016
 * Time: 12:09
 */

namespace Geocuba\PortadoresBundle\Repository;


use Doctrine\ORM\EntityRepository;

class CDA001Repository extends EntityRepository
{

    /**
     * @param $portadorid
     * @param $_unidad
     * @param null $start
     * @param null $limit
     * @param bool $count
     * @return array|mixed
     */
    public function buscarCda001($portadorid, $_unidad, $anno, $moneda)
    {
        $qb = $this->createQueryBuilder('cda001');

        $qb->where($qb->expr()->eq('cda001.nunidadid', ':unidadid'))
            ->setParameter('unidadid',$_unidad)
            ->andWhere($qb->expr()->eq('cda001.portadorid', ':portadorid'))
            ->setParameter('portadorid', $portadorid);

        if (isset($anno) && $anno != '') {
            $qb->andWhere($qb->expr()->eq('cda001.anno', ':anno'))
                ->setParameter('anno', $anno);
        }

        if (isset($moneda) && $moneda != '') {
            $qb->andWhere($qb->expr()->eq('cda001.moneda', ':moneda'))
                ->setParameter('moneda', $moneda);
        }

        return $qb->getQuery()->getResult();
    }

} 