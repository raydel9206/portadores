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

class AreaMedidaRepository extends EntityRepository
{
    /**
     * @param $_nombre
     * @param $_area
     * @param null $start
     * @param null $limit
     * @param bool $count
     * @return array|mixed
     */
    public function buscarAreaMedidas($_area, $_unidades, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('areaMedida');
        if ($count)
            $qb->select('count(areaMedida)');

        $qb->innerJoin('areaMedida.nlistaAreaid', 'area');
        $qb->innerJoin('area.unidad', 'unidad');

        $qb->andWhere($qb->expr()->eq('areaMedida.visible', ':visible'))
            ->setParameter('visible', true);

        if ($_unidades !== null && $_unidades != '') {
            $qb->andWhere($qb->expr()->in('area.unidad', $_unidades));
        }

        if (isset($_area) && $_area != '') {
            $qb->andWhere('lower(area.nombre) like lower(:nombre)')
                ->setParameter('nombre', "%$_area%");
        }

        if (isset($_unidad) && $_unidad != '') {
            $qb->andWhere('area.unidad = :unidad')
                ->setParameter('unidad', "$_unidad");
        }
//        Debug::dump($qb->getQuery()->getSQL());
//        die;

        if ($count) {

            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('area.nombre')
                ->setMaxResults($limit)
                ->setFirstResult($start)
                ->getQuery()->getResult();
        }
    }

    /**
     * @param $_nombre
     * @param $_area
     * @param string $id
     * @return mixed
     */
    public function buscarAreaMedidasRepetido($_nombre, $_area, $id = '')
    {
        $qb = $this->createQueryBuilder('areaMedidas');
        $qb->select('count(areaMedidas)');

        $qb->andWhere($qb->expr()->eq('areaMedidas.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('lower(areaMedidas.nombre) = lower(:nombre)')
                ->setParameter('nombre', $_nombre);
        }

        if (isset($_area) && $_area != '') {
            $qb->andWhere('areaMedidas.nlistaAreaid = :area')
                ->setParameter('area', $_area);
        }

        if (isset($id) && $id != '') {
            $qb->andWhere('areaMedidas.id != :id')
                ->setParameter('id', $id);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }
}