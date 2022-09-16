<?php
/**
 * Created by JetBrains PhpStorm.
 * User: orlando
 * Date: 16/01/14
 * Time: 16:09
 * To change this template use File | Settings | File Templates.
 */

namespace Geocuba\PortadoresBundle\Repository;
use Doctrine\ORM\EntityRepository;

class AccidenteRepository extends EntityRepository {

    public function buscarAccidentes($_matricula, $_unidades, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('accidente');
        if ($count)
            $qb->select('count(accidente)');

        $qb->innerJoin('accidente.vehiculoid', 'vehiculo')
            ->Where($qb->expr()->in('vehiculo.nunidadid', $_unidades))
            ->andWhere($qb->expr()->eq('accidente.visible', 'true'));

        if (isset($_matricula) && $_matricula != '') {
            $qb->andWhere('lower(vehiculo.matricula) like lower(:matricula)')
                ->setParameter('matricula', "%$_matricula%");
        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('vehiculo.nunidadid', 'ASC')
                ->addOrderBy('vehiculo.matricula', 'ASC')
                ->setMaxResults($limit)
                ->setFirstResult($start)
                ->getQuery()->getResult();
        }
    }
}