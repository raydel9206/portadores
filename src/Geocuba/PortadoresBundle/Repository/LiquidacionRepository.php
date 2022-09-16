<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pfcadenas
 * Date: 11/11/2014
 * Time: 11:32
 * To change this template use File | Settings | File Templates.
 */

namespace Geocuba\PortadoresBundle\Repository;

use Doctrine\ORM\EntityRepository;

class LiquidacionRepository extends EntityRepository
{
    /**
     * @param $_noVale
     * @param $_unidad
     * @param $_unidades
     * @param $fechaDesde
     * @param $fechaHasta
     * @param null $start
     * @param null $limit
     * @param bool $count
     * @return array|mixed
     */
    public function buscarLiquidacion($_unidades, $_noVale, $tarjetaid, $anticipoid, $fechaDesde, $fechaHasta, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('liquidacion');
        if ($count)
            $qb->select('count(liquidacion)');

        $qb->innerJoin('liquidacion.ntarjetaid', 'tarjeta');
//        $qb->innerJoin('liquidacion.anticipo', 'anticipo');

        if (isset($tarjetaid) && $tarjetaid != '') {
            $qb->Where('liquidacion.ntarjetaid = :tarjetaid')
                ->setParameter('tarjetaid', $tarjetaid);
        }

        if (isset($anticipoid) && $anticipoid != '') {
            $qb->andWhere('liquidacion.anticipo = :anticipoid')
                ->setParameter('anticipoid', $anticipoid);
        }
//        else {
//            $qb->andWhere('liquidacion.anticipo is NULL');
//        }

        if (isset($_unidades) && $_unidades != '') {
            $qb->andWhere($qb->expr()->in('liquidacion.nunidadid', ':unidades'))
                ->setParameter('unidades', $_unidades);
        }

        if (isset($_noVale) && $_noVale != '') {
            $qb->andWhere('clearstr(liquidacion.nroVale) like clearstr(:noVale)')
                ->setParameter('noVale', "%$_noVale%");
        }

        if (isset($fechaDesde) && $fechaDesde != '' && isset($fechaHasta) && $fechaHasta != '') {
            $qb->andWhere('liquidacion.fechaVale between :fechaDesde and :fechaHasta')
                ->setParameter('fechaDesde', $fechaDesde)
                ->setParameter('fechaHasta', $fechaHasta);
        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('liquidacion.fechaRegistro', 'DESC')
                ->setMaxResults($limit)
                ->setFirstResult($start)
                ->getQuery()->getResult();
        }
    }

    public function buscarEntregas($_unidades, $_noVale, $tarjetaid, $fechaDesde, $fechaHasta, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('liquidacion');
        if ($count)
            $qb->select('count(liquidacion)');

        $qb->innerJoin('liquidacion.ntarjetaid', 'tarjeta');
//        $qb->innerJoin('liquidacion.anticipo', 'anticipo');

        if (isset($tarjetaid) && $tarjetaid != '') {
            $qb->Where('liquidacion.ntarjetaid = :tarjetaid')
                ->setParameter('tarjetaid', $tarjetaid);
        }

        if (isset($_unidades) && $_unidades != '') {
            $qb->andWhere($qb->expr()->in('liquidacion.nunidadid', ':unidades'))
                ->setParameter('unidades', $_unidades);
        }

        if (isset($_noVale) && $_noVale != '') {
            $qb->andWhere('clearstr(liquidacion.nroVale) like clearstr(:noVale)')
                ->setParameter('noVale', "%$_noVale%");
        }

        if (isset($fechaDesde) && $fechaDesde != '' && isset($fechaHasta) && $fechaHasta != '') {
            $qb->andWhere('liquidacion.fechaVale between :fechaDesde and :fechaHasta')
                ->setParameter('fechaDesde', $fechaDesde)
                ->setParameter('fechaHasta', $fechaHasta);
        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('liquidacion.fechaRegistro', 'DESC')
                ->setMaxResults($limit)
                ->setFirstResult($start)
                ->getQuery()->getResult();
        }
    }

    /**
     * @param $_tarjeta
     * @param $_unidad
     * @param $fechaDesde
     * @param $fechaHasta
     * @param bool $count
     * @return array|mixed
     */
    public function buscarLiquidacionCorregir($_tarjeta, $_unidad, $fechaDesde, $fechaHasta, $count = false)
    {
        $qb = $this->createQueryBuilder('liquidacion');
        if ($count)
            $qb->select('count(liquidacion)');

        if (isset($_tarjeta) && $_tarjeta != '') {
            $qb->andWhere('liquidacion.ntarjetaid = :tarjeta')
                ->setParameter('tarjeta', "$_tarjeta");
        }
        if (isset($_unidad) && $_unidad != '') {
            $qb->andWhere('liquidacion.nunidadid = :unidad')
                ->setParameter('unidad', "$_unidad");
        }
        if (isset($fechaDesde) && $fechaDesde != '' && isset($fechaHasta) && $fechaHasta != '') {
            $qb->andWhere('liquidacion.fechaVale between :fechaDesde and :fechaHasta')
                ->setParameter('fechaDesde', $fechaDesde)
                ->setParameter('fechaHasta', $fechaHasta);
        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('liquidacion.fechaVale', 'ASC')
                ->getQuery()->getResult();
        }
    }

    /**
     * @param $_proyecto
     * @param $_tipoCombustible
     * @param $_fechaDesde
     * @param $_fechaHasta
     * @param $_moneda
     * @return mixed
     */
    public function consumoProyecto($_proyecto, $_tipoCombustible, $_fechaDesde, $_fechaHasta, $_moneda)
    {
        $qb = $this->createQueryBuilder('liquidacion');

        $qb->select('sum(liquidacion.cantLitros)');

        $qb->innerJoin('liquidacion.ntarjetaid', 'tarjeta')
            ->innerJoin('liquidacion.anticipo', 'anticipo')
            ->innerJoin('anticipo.trabajo', 'trabajo')
            ->Where($qb->expr()->eq('anticipo.visible', 'true'))
            ->andWhere($qb->expr()->eq('trabajo.id', ':proyecto'))
            ->andWhere($qb->expr()->eq('tarjeta.ntipoCombustibleid', ':tipoCombustible'))
            ->andWhere($qb->expr()->eq('tarjeta.nmonedaid', ':moneda'))
            ->setParameter('proyecto', $_proyecto)
            ->setParameter('tipoCombustible', $_tipoCombustible)
            ->setParameter('moneda', $_moneda);

        if (isset($_fechaDesde) && $_fechaDesde != '' && isset($_fechaHasta) && $_fechaHasta != '') {
            $qb->andWhere('liquidacion.fechaVale between :fechaDesde and :fechaHasta')
                ->setParameter('fechaDesde', $_fechaDesde)
                ->setParameter('fechaHasta', $_fechaHasta);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param $_vehiculo
     * @param $_tipoCombustible
     * @param $_fechaDesde
     * @param $_fechaHasta
     * @param $_moneda
     * @return mixed
     */
    public function consumoVehiculo($_vehiculo, $_tipoCombustible, $_fechaDesde, $_fechaHasta, $_moneda)
    {
        $qb = $this->createQueryBuilder('liquidacion');

        $qb->select('sum(liquidacion.cantLitros)');

        $qb->innerJoin('liquidacion.ntarjetaid', 'tarjeta')
            ->innerJoin('liquidacion.nvehiculoid', 'vehiculo')
            ->andWhere($qb->expr()->eq('vehiculo.id', ':vehiculo'))
            ->andWhere($qb->expr()->eq('tarjeta.ntipoCombustibleid', ':tipoCombustible'))
            ->andWhere($qb->expr()->eq('tarjeta.nmonedaid', ':moneda'))
            ->setParameter('vehiculo', $_vehiculo)
            ->setParameter('tipoCombustible', $_tipoCombustible)
            ->setParameter('moneda', $_moneda);

        if (isset($_fechaDesde) && $_fechaDesde != '' && isset($_fechaHasta) && $_fechaHasta != '') {
            $qb->andWhere('liquidacion.fechaVale between :fechaDesde and :fechaHasta')
                ->setParameter('fechaDesde', $_fechaDesde)
                ->setParameter('fechaHasta', $_fechaHasta);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param $_vehiculo
     * @param $fechaDesde
     * @param $fechaHasta
     * @return array
     */
    public function buscarLiquidacionRegistroCombustible($_vehiculo, $fechaDesde, $fechaHasta)
    {
        $qb = $this->createQueryBuilder('liquidacion');

        if (isset($_vehiculo) && $_vehiculo != '') {
            $qb->andWhere('liquidacion.nvehiculoid = :vehiculo')
                ->setParameter('vehiculo', "$_vehiculo");
        }
        if (isset($fechaDesde) && $fechaDesde != '' && isset($fechaHasta) && $fechaHasta != '') {
            $qb->andWhere('liquidacion.fechaVale between :fechaDesde and :fechaHasta')
                ->setParameter('fechaDesde', $fechaDesde)
                ->setParameter('fechaHasta', $fechaHasta);
        }

        return $qb->orderBy('liquidacion.fechaVale', 'ASC')
            ->getQuery()->getResult();
    }

    public function consumoVehiculoCierre($_vehiculo, $_mes)
    {
//        $consulta = $this->getEntityManager()->createQuery('Select sum(cant_litros) FROM PortadoresBundle:Liquidacion a
//        where a.nvehiculoid =:nvehiculoid and  MONTH(fecha_registro)=:mes');
//        $consulta->setParameters(array(
//            'nvehiculoid' => $_vehiculo,
//            'mes' => $_mes
//        ));
//        print_r($consulta->getResult());die;
//        return $consulta->getResult();


        $qb = $this->getEntityManager()->createQueryBuilder();
        $result = $qb->select('yd')
            ->from('PortadoresBundle:Liquidacion', 'yd')
            ->where($qb->expr()->like('yd.fecha_registro', ':month'))
            ->setParameter('month', '%-' . $_mes . '-%')
            ->getQuery()
            ->getResult();

    }


    public function liquidacionValidar($_tarjeta, $_fecha, $count = false)
    {

        $qb = $this->createQueryBuilder('liquidacion');
        if ($count)
            $qb->select('count(liquidacion)');

        if (isset($_tarjeta) && $_tarjeta != '') {
            $qb->andWhere('liquidacion.ntarjetaid = :idTarjeta')
                ->setParameter('idTarjeta', $_tarjeta);
        }
        if (isset($_fecha) && $_fecha != '') {
            $qb->andWhere('liquidacion.fechaVale > :fecha')
                ->setParameter('fecha', $_fecha);
        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('liquidacion.fechaVale', 'ASC')
                ->getQuery()->getResult();
        }


    }

    public function buscarLiquidacionRepetido($_nro_vale, $_fecha_servicio, $id = '')
    {
        $qb = $this->createQueryBuilder('liquidacion');
        $qb->select('count(liquidacion)');

        $qb->Where($qb->expr()->eq('liquidacion.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_fecha_servicio) && $_fecha_servicio != '') {
            $qb->andWhere('liquidacion.fechaVale = :fechaVale')
                ->setParameter('fechaVale', $_fecha_servicio);
        }

        if (isset($_nro_vale) && $_nro_vale != '') {
            $qb->andWhere('liquidacion.nroVale = :nroVale')
                ->setParameter('nroVale', $_nro_vale);
        }

        if (isset($id) && $id != '') {
            $qb->andWhere('liquidacion.id != :id')
                ->setParameter('id', $id);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }


}