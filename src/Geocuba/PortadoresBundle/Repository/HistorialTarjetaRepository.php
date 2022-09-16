<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pfcadenas
 * Date: 09/01/2017
 * Time: 16:25
 * To change this template use File | Settings | File Templates.
 */

namespace Geocuba\PortadoresBundle\Repository;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityRepository;

class HistorialTarjetaRepository extends EntityRepository
{
    /**
     * @param $_tarjeta
     * @param $_fecha
     * @param null $start
     * @param null $limit
     * @param bool $count
     * @return array|mixed
     */
    public function buscarHistorial($_tarjeta, $_fecha, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('historial');
        if ($count)
            $qb->select('count(historial)');

        $qb->innerJoin('historial.tarjetaid', 'tarjeta');

        if (isset($_tarjeta) && $_tarjeta != '') {
            $qb->andWhere('tarjeta.nroTarjeta = :nroTarjeta')
                ->setParameter('nroTarjeta', "$_tarjeta");
        }
        if (isset($_fecha) && $_fecha != '') {
            $qb->andWhere('historial.fecha >= :fecha')
                ->setParameter('fecha', "$_fecha");
        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('historial.fecha', 'ASC')
                ->setMaxResults($limit)
                ->setFirstResult($start)
                ->getQuery()->getResult();
        }
    }

    /**
     * @param $_tarjeta
     * @param $_fecha
     * @param bool $count
     * @return array|mixed
     */
    public function buscarHistorialValidar($_tarjeta, $_fecha, $count = false)
    {
        $qb = $this->createQueryBuilder('historial');
        if ($count)
            $qb->select('count(historial)');

        $qb->andWhere($qb->expr()->eq('historial.cancelado', ':cancelado'))
            ->setParameter('cancelado', false);

        if ($_tarjeta !== null && $_tarjeta != '') {
            $qb->andWhere('historial.tarjetaid = :nroTarjeta')
                ->setParameter('nroTarjeta', $_tarjeta);
        }
        if ($_fecha !== null && $_fecha != '') {
            $qb->andWhere('historial.fecha > :fecha')
                ->setParameter('fecha', $_fecha);
        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('historial.fecha', 'ASC')
                ->getQuery()->getResult();
        }
    }

    /**
     * @param $_tarjeta
     * @param $_fecha
     * @return array
     */
    public function buscarHistorialCorregir($_tarjeta, $_fecha, $fechaHasta)
    {
        $qb = $this->createQueryBuilder('historial');

        if (isset($_tarjeta) && $_tarjeta != '') {
            $qb->andWhere('historial.tarjetaid = :tarjeta')
                ->setParameter('tarjeta', "$_tarjeta");
        }
        if (isset($_fecha) && $_fecha != '') {
            $qb->andWhere('historial.fecha >= :fecha')
                ->setParameter('fecha', "$_fecha");
        }
        if (isset($_fecha) && $_fecha != '') {
            $qb->andWhere('historial.fecha <= :fecha')
                ->setParameter('fecha', "$fechaHasta");
        }

        return $qb->orderBy('historial.fecha', 'ASC')
            ->getQuery()->getResult();
    }

    /**
     * @param $_tarjeta
     * @return array
     */
//    public function saldoTarjeta($_tarjeta)
//    {
//        $importe =  $this->createQueryBuilder('historial')
//            ->select('sum(historial.entradaImporte - historial.salidaImporte)')
//            ->andWhere('historial.tarjetaid = :tarjeta')
//            ->setParameter('tarjeta', "$_tarjeta")
//            ->getQuery()->getSingleScalarResult();
//
//        if(is_null($importe))
//            return 0;
//        else
//            return $importe;
//    }
    public function saldoTarjeta($_tarjeta)
    {
//        $importe =  $this->createQueryBuilder('historial')
//            ->select('max(historial.fecha)')
//            ->andWhere('historial.tarjetaid = :tarjeta')
//            ->setParameter('tarjeta', "$_tarjeta")
//            ->getQuery()->getSingleScalarResult();
//
//        if(is_null($importe))
//            return 0;
//        else
//            return $importe;

        $consulta = $this->getEntityManager()->createQuery('select tbl.tarjetaid, tbl.existencia_importe, tbl.fecha from PortadoresBundle:HistorialTarjeta tbl
    where tbl.fecha =: (select max(fecha) from  PortadoresBundle:HistorialTarjeta where tarjetaid =:tarjetaid);');
        $consulta->setParameters(array(
            'tarjetaid' => $_tarjeta,
        ));

//        print_r($consulta);die;
        return $consulta->getResult();


    }


    public function cargasTarjeta($_tarjeta, $_fecha)
    {
        $cargas = $this->createQueryBuilder('historial')
            ->select('sum(historial.entradaImporte)')
            ->andWhere('historial.tarjetaid = :tarjeta')
            ->setParameter('tarjeta', "$_tarjeta")
            ->andWhere('historial.fecha >= :fecha')
            ->setParameter('fecha', "$_fecha")
            ->getQuery()->getSingleScalarResult();

        if (is_null($cargas))
            return 0;
        else
            return $cargas;
    }

    public function salidasTarjeta($_tarjeta, $_fecha)
    {
        $cargas = $this->createQueryBuilder('historial')
            ->select('sum(historial.salidaImporte)')
            ->andWhere('historial.tarjetaid = :tarjeta')
            ->setParameter('tarjeta', "$_tarjeta")
            ->andWhere('historial.fecha >= :fecha')
            ->setParameter('fecha', "$_fecha")
            ->getQuery()->getSingleScalarResult();

        if (is_null($cargas))
            return 0;
        else
            return $cargas;
    }


    /**
     * @param $tarjetaid
     * @param $mes
     * @param $anno
     * @return array|mixed
     */
    public function postMensual($tarjetaid, $mes, $anno)
    {
        $qb = $this->createQueryBuilder('historial');
        $qb->select('historial');

        if (isset($tarjetaid) && $tarjetaid != '') {
            $qb->andWhere('historial.tarjetaid = :tarjetaid')
                ->setParameter('tarjetaid', $tarjetaid);
        }
        if (isset($mes) && $mes != '') {
            $qb->andWhere('historial.mes = :mes')
                ->setParameter('mes', $mes);
        }
        if (isset($anno) && $anno != '') {
            $qb->andWhere('historial.anno = :anno')
                ->setParameter('anno', $anno);
        }

        return $qb->orderBy('historial.fecha', 'ASC')
            ->getQuery()->getResult();
    }

    /**
     * @param $tarjetaid
     * @return array|mixed
     */
    public function post($tarjetaid)
    {
        $qb = $this->createQueryBuilder('historial');
        $qb->select('historial');
        $qb->andWhere('historial.tarjetaid = :tarjetaid')
            ->setParameter('tarjetaid', $tarjetaid);

        return $qb->orderBy('historial.fecha', 'ASC')
            ->getQuery()->getResult();
    }


}