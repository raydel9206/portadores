<?php
/**
 * Created by PhpStorm.
 * User: asisoftware11
 * Date: 7/13/2018
 * Time: 9:22 AM
 */

namespace Geocuba\PortadoresBundle\Repository;


use Doctrine\ORM\EntityRepository;

class EstadoTecnicoRepository extends EntityRepository
{
    /**
     * @param $_nombre
     * @return mixed
     */
    public function buscarEstadoTecnicoRepetido($_nombre)
    {
        $qb = $this->createQueryBuilder('estado_tecnico');
        $qb->select('count(estado_tecnico)');

        $qb->andWhere($qb->expr()->eq('estado_tecnico.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('clearstr(estado_tecnico.nombre) = clearstr(:nombre)')
                ->setParameter('nombre', $_nombre);
        }


        return $qb->getQuery()->getSingleScalarResult();
    }


}