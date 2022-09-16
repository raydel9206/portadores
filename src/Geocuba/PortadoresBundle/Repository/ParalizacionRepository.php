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

class ParalizacionRepository extends EntityRepository {

    public function buscarParalizacion($_matricula, $_unidades, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('paralizacion');
        if ($count)
            $qb->select('count(paralizacion)');

        $qb->innerJoin('paralizacion.vehiculoid', 'vehiculo')
            ->Where($qb->expr()->in('vehiculo.nunidadid', $_unidades))
            ->andWhere($qb->expr()->eq('paralizacion.visible', 'true'));

        if (isset($_matricula) && $_matricula != '') {
            $qb->andWhere('lower(vehiculo.matricula) like lower(:matricula)')
                ->setParameter('matricula', "%$_matricula%");
        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('vehiculo.matricula', 'ASC')
//                ->setMaxResults($limit)
//                ->setFirstResult($start)
                ->getQuery()->getResult();
        }
    }
}