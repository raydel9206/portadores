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

class TarjetaRepository extends EntityRepository
{

    public function buscarTarjera($_numero, $_unidades, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('tarjeta');
        if ($count)
            $qb->select('count(tarjeta)');

        $qb->innerJoin('tarjeta.centrocosto', 'centrocosto');

        $qb->Where($qb->expr()->in('centrocosto.nunidadid', $_unidades))
            ->andWhere($qb->expr()->eq('tarjeta.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_numero) && $_numero != '') {
            $qb->andWhere("lower(tarjeta.nroTarjeta) like lower(:numero)")
                ->setParameter('numero', "%$_numero%");
        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('tarjeta.ntipoCombustibleid')
                ->addOrderBy('tarjeta.nroTarjeta')
//                ->addOrderBy('unidad.nombre')
//                ->setMaxResults($limit)
//                ->setFirstResult($start)
                ->getQuery()->getResult();
        }
    }

    public function buscarTarjetaCombo($_numero, $_unidades, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('tarjeta');
        $qb->select('tarjeta.id', 'tarjeta.nroTarjeta as nro_tarjeta');
        if ($count)
            $qb->select('count(tarjeta)');

        $qb->innerJoin('tarjeta.centrocosto', 'centrocosto');

        $qb->Where($qb->expr()->in('centrocosto.nunidadid', $_unidades))
            ->andWhere($qb->expr()->eq('tarjeta.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_numero) && $_numero != '') {
            $qb->andWhere("lower(tarjeta.nroTarjeta) like lower(:numero)")
                ->setParameter('numero', "%$_numero%");
        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('tarjeta.ntipoCombustibleid')
                ->addOrderBy('tarjeta.nroTarjeta')
                ->getQuery()->getResult();
        }
    }

    public function buscarTarjeraTipoCombustible($_tipoCombustible, $_unidades)
    {
        $qb = $this->createQueryBuilder('tarjeta');


        $qb->innerJoin('tarjeta.centrocosto', 'centrocosto');
        $qb->innerJoin('tarjeta.ntipoCombustibleid', 'tipocombustible');

        $qb->Where($qb->expr()->in('centrocosto.nunidadid', $_unidades))
            ->andWhere($qb->expr()->eq('tipocombustible.id', ':tipocombustible'))
            ->setParameter('tipocombustible', $_tipoCombustible);


        return $qb->orderBy('tarjeta.ntipoCombustibleid')
            ->addOrderBy('tarjeta.nroTarjeta')
            ->getQuery()->getResult();

    }

    public function buscarTarjetaConFecha($_numero, $_fechaActual, $_unidades, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('tarjeta');
        if ($count)
            $qb->select('count(tarjeta)');

        $qb->innerJoin('tarjeta.centrocosto', 'centrocosto');

        $qb->Where($qb->expr()->in('centrocosto.nunidadid', $_unidades))
            ->andWhere($qb->expr()->eq('tarjeta.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_numero) && $_numero != '') {
            $qb->andWhere("lower(tarjeta.nroTarjeta,1) like lower(:numero)")
                ->setParameter('numero', "%$_numero%");
        }

        if (isset($_fechaActual) && $_fechaActual != '') {
            $qb->andWhere("tarjeta.fechaVencimieno >= :fechaActual")
                ->setParameter('fechaActual', "$_fechaActual");
        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('tarjeta.ntipoCombustibleid')
                ->addOrderBy('tarjeta.nroTarjeta')
                ->setMaxResults($limit)
                ->setFirstResult($start)
                ->getQuery()->getResult();
        }
    }


    public function buscarTarjeraLiquidacion($_unidades)
    {
        $_importe = 0;
        $qb = $this->createQueryBuilder('tarjeta');

        $qb->innerJoin('tarjeta.centrocosto', 'centrocosto');

        $qb->Where($qb->expr()->in('centrocosto.nunidadid', $_unidades))
            ->andWhere($qb->expr()->eq('tarjeta.visible', ':visible'))
            ->setParameter('visible', true)
            ->andWhere("tarjeta.importe > :importe")
            ->setParameter('importe', "$_importe");
        return $qb->getQuery()->getResult();
    }

    public function buscarTarjetaConFechaPost($_numero, $_unidades)
    {
        $qb = $this->createQueryBuilder('tarjeta');
        $qb->innerJoin('tarjeta.nunidadid', 'unidad');

        $qb->Where($qb->expr()->in('tarjeta.nunidadid', $_unidades))
            ->andWhere($qb->expr()->eq('tarjeta.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_numero) && $_numero != '') {
            $qb->andWhere("clearstr(tarjeta.nroTarjeta) like clearstr(:numero)")
                ->setParameter('numero', "%$_numero%");
        }

//        if (isset($_fechaActual) && $_fechaActual != '') {
//            $qb->andWhere("tarjeta.fechaVencimieno >= :fechaActual")
//                ->setParameter('fechaActual', "$_fechaActual");
//        }
        return $qb->getQuery()->getOneOrNullResult();
    }


}