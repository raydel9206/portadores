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

class ActaRespMaterialRepository extends EntityRepository
{

    public function FindListActaRespMaterial($start = null, $limit = null)
    {
        $consulta = $this->getEntityManager()->createQuery('Select a FROM PortadoresBundle:ActaRespMaterial a
        where a.visible = true Order By a.fecha,a.id ASC');

        if ($limit != -1) {
            $consulta->setMaxResults($limit);
            $consulta->setFirstResult($start);
        }

        return $consulta->getResult();

    }

    /**
     * @param $_unidades
     * @param $_tarjeta
     * @param $_chofer
     * @param $mes
     * @param $anno
     * @param null $start
     * @param null $limit
     * @param bool $count
     * @return array|mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function buscarActaResponsabilidadMaterial($_unidades, $_tarjeta, $_chofer, $mes, $anno, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('acta');
        if ($count)
            $qb->select('count(acta)');
        $qb->andWhere($qb->expr()->in('acta.nunidadid', $_unidades));
        $qb->andWhere($qb->expr()->eq('acta.visible', ':visible'))
            ->setParameter('visible', true);
        $qb->andWhere($qb->expr()->eq('acta.mes', ':mes'))
            ->setParameter('mes', $mes);
        $qb->andWhere($qb->expr()->eq('acta.anno', ':anno'))
            ->setParameter('anno', $anno);

        if ($_chofer !== null && $_chofer != '') {
            $qb->andWhere($qb->expr()->eq('acta.recibeid', ':chofer'))
                ->setParameter('chofer', $_chofer);
        }

        if ($_tarjeta !== null && $_tarjeta != '') {
            $qb->andWhere('clearstr(acta.tarjeta) like clearstr(:tarjeta)')
                ->setParameter('tarjeta', "%$_tarjeta%");
        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('acta.fecha', 'ASC')
                ->setMaxResults($limit)
                ->setFirstResult($start)
                ->getQuery()->getResult();
        }
    }

}