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

class TrabajoRepository extends EntityRepository
{

    /**
     * @param $_nombre
     * @param $_fecha
     * @param null $start
     * @param null $limit
     * @param bool $count
     * @return array|mixed
     */
    public function buscarTrabajo($_nombre, $_unidades, $_fecha, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('trabajo');
        if ($count)
            $qb->select('count(trabajo)');

        $qb->innerJoin('trabajo.ncentrocostoid', 'ncentrocosto')
            ->innerJoin('ncentrocosto.nunidadid', 'nunidadid');

        $qb->andWhere($qb->expr()->eq('trabajo.visible', ':visible'))
            ->setParameter('visible', true);

        if ($_unidades !== null && $_unidades != '') {
            $qb->andWhere($qb->expr()->in('ncentrocosto.nunidadid', $_unidades));
        }

        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('lower(trabajo.nombre) like lower(:nombre)')
                ->setParameter('nombre', "%$_nombre%");
        }

        if (isset($_fecha) && $_fecha != '') {
            $qb->andWhere($qb->expr()->gte('trabajo.fechaFin', ':fecha'))
                ->setParameter('fecha', $_fecha);
        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('nunidadid.nombre')
                ->addOrderBy('trabajo.nombre')
                ->setMaxResults($limit)
                ->setFirstResult($start)
                ->getQuery()->getResult();
        }
    }

    /**
     * @param $_nombre
     * @param string $_codigo
     * @param string $id
     * @return mixed
     */
    public function buscarTrabajoRepetido($_nombre, $_codigo = '', $id = '')
    {
        $qb = $this->createQueryBuilder('trabajo');
        $qb->select('count(trabajo)');

        $qb->andWhere($qb->expr()->eq('trabajo.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('lower(trabajo.nombre) = lower(:nombre)')
                ->setParameter('nombre', $_nombre);
        }

        if (isset($_codigo) && $_codigo != '') {
            $qb->andWhere('lower(trabajo.codigo) = lower(:codigo)')
                ->setParameter('codigo', $_codigo);
        }

        if (isset($id) && $id != '') {
            $qb->andWhere('trabajo.id != :id')
                ->setParameter('id', $id);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }
}