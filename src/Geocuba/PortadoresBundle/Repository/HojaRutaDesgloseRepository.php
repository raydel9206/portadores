<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pfcadenas
 * Date: 19/05/2017
 * Time: 14:00
 * To change this template use File | Settings | File Templates.
 */

namespace Geocuba\PortadoresBundle\Repository;

use Doctrine\ORM\EntityRepository;

class HojaRutaDesgloseRepository extends EntityRepository
{
    /**
     * @param $_hojarutaid
     * @param null $start
     * @param null $limit
     * @param bool $count
     * @return array|mixed
     */
    public function buscarHojaRutaDesglose($_hojarutaid, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('hojaRutaDesglose');
        if ($count)
            $qb->select('count(hojaRutaDesglose)');

        if (isset($_hojarutaid) && $_hojarutaid != '') {
            $qb->andWhere('hojaRutaDesglose.hojarutaid = :hojaruta')
                ->setParameter('hojaruta', "$_hojarutaid");
        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->setMaxResults($limit)
                ->setFirstResult($start)
                ->getQuery()->getResult();
        }
    }
}