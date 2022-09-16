<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pfcadenas
 * Date: 19/05/2017
 * Time: 12:00
 * To change this template use File | Settings | File Templates.
 */

namespace Geocuba\PortadoresBundle\Repository;

use Doctrine\ORM\EntityRepository;

class HojaRutaRepository extends EntityRepository
{
    /**
     * @param $fechaDesde
     * @param $fechaHasta
     * @param null $start
     * @param null $limit
     * @param bool $count
     * @return array|mixed
     */
    public function buscarHojaRuta($_chapa, $_unidades, $fechaDesde, $fechaHasta, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('hojaRuta');
        if ($count)
            $qb->select('count(hojaRuta)');

        $qb->innerJoin('hojaRuta.vehiculo', 'vehiculo')
            ->andWhere($qb->expr()->in('vehiculo.nunidadid', $_unidades));

        $qb->andWhere($qb->expr()->eq('hojaRuta.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_chapa) && $_chapa != '') {
            $qb->andWhere('lower(vehiculo.matricula) like lower(:nombre)')
                ->setParameter('nombre', "%$_chapa%");
        }

        if (isset($fechaDesde) && $fechaDesde != '' && isset($fechaHasta) && $fechaHasta != '') {
            $qb->andWhere('hojaRuta.fecha between :fechaDesde and :fechaHasta')
                ->setParameter('fechaDesde', $fechaDesde)
                ->setParameter('fechaHasta', $fechaHasta);
        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('hojaRuta.fecha', 'ASC')
                ->setMaxResults($limit)
                ->setFirstResult($start)
                ->getQuery()->getResult();
        }
    }
}