<?php

namespace Geocuba\PortadoresBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class EquipoTecnologicoRepository extends EntityRepository {

    /**
     * @param $_unidades
     * @param $tipo
     * @param null $start
     * @param null $limit
     * @param bool $count
     * @return array|mixed
     * @throws NonUniqueResultException
     */
    public function findAllBy($_unidades, $tipo, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('equipo');
        if ($count)
            $qb->select('count(equipo)');

        $qb->innerJoin('equipo.unidad', 'unidad');

        if($_unidades) $qb->andWhere($qb->expr()->in('equipo.unidad', $_unidades));

        if ($tipo){
            if ($tipo !== 'otro')
                $qb->andWhere($qb->expr()->eq('equipo.denominacionTecnologica', ':tipo'))
                    ->setParameter('tipo', '' . $tipo);
            else $qb->andWhere($qb->expr()->notIn('equipo.denominacionTecnologica', ['static_tec_denomination_1', 'static_tec_denomination_2', 'static_tec_denomination_3']));
        }

        if ($count) return $qb->getQuery()->getSingleScalarResult();

        return $qb->orderBy('unidad.nombre')
            ->addOrderBy('equipo.descripcion')
            ->setMaxResults($limit)
            ->setFirstResult($start)
            ->getQuery()->getResult();
    }

    public function findDuplicate($numeroInventario, $descripcion, $unidad, $id = null)
    {
        $qb = $this->createQueryBuilder('equipo');

        $qb->Where($qb->expr()->eq('equipo.nroInventario', ':numeroInventario'))
            ->setParameter('numeroInventario', $numeroInventario)
            ->andWhere($qb->expr()->eq('equipo.unidad', ':unidad'))
            ->setParameter('unidad', $unidad)
            ->andWhere('clearstr(equipo.descripcion) like clearstr(:descripcion)')
            ->setParameter('descripcion', $descripcion);

        if ($id)
            $qb->andWhere($qb->expr()->neq('equipo.id', ':id'))
                ->setParameter('id', $id);

        return $qb->getQuery()->getResult();
    }
}