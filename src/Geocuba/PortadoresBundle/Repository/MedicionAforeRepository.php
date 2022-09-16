<?php

namespace Geocuba\PortadoresBundle\Repository;
use Doctrine\ORM\EntityRepository;

class MedicionAforeRepository extends EntityRepository {

    /**
     * @param $nivel
     * @param $existencia
     * @param $id
     * @param bool $isTanque
     * @return array|mixed
     */
    public function findDuplicates($nivel, $existencia, $id, $isTanque = false)
    {
        $qb = $this->createQueryBuilder('medicion');
        $qb->select('medicion')
            ->Where('medicion.nivel = :nivel or medicion.existencia = :existencia')
            ->setParameter('nivel', $nivel)
            ->setParameter('existencia', $existencia);

        if ($isTanque) $qb->andWhere($qb->expr()->eq('medicion.tanque', ':id'));
        else $qb->andWhere($qb->expr()->neq('medicion.id', ':id'));

        return $qb->setParameter('id', $id)
            ->getQuery()->getResult();
    }
}