<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pfcadenas
 * Date: 16/12/2016
 * Time: 15:15
 * To change this template use File | Settings | File Templates.
 */

namespace Geocuba\PortadoresBundle\Repository;
use Doctrine\ORM\EntityRepository;

class TrabajoAsignacionCombustibleRepository extends EntityRepository {

    /**
     * @param $_trabajo
     * @param $_tipoCombustible
     * @param $_moneda
     * @param bool $count
     * @return array|mixed
     */
    public function buscarAsignacion($_trabajo, $_tipoCombustible, $_moneda, $count = false)
    {
        $qb = $this->createQueryBuilder('asignacion');
        if ($count)
            $qb->select('count(asignacion)');

        $qb->innerJoin('asignacion.moneda' , 'moneda');

        if (isset($_trabajo) && $_trabajo != '') {
            $qb->andWhere('asignacion.trabajoid = :trabajo')
                ->setParameter('trabajo',$_trabajo);
        }
        if (isset($_tipoCombustible) && $_tipoCombustible != '') {
            $qb->andWhere('asignacion.tipoCombustible = :tipoCombustible')
                ->setParameter('tipoCombustible',$_tipoCombustible);
        }
        if (isset($_moneda) && $_moneda != '') {
            $qb->andWhere('asignacion.moneda = :moneda')
                ->setParameter('moneda',$_moneda);
        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('moneda.nombre', 'ASC')
                ->getQuery()->getResult();
        }
    }
}