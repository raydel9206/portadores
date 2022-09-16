<?php
/**
 * Created by PhpStorm.
 * User: yosley
 * Date: 31/01/2018
 * Time: 05:13 PM
 */

namespace Geocuba\PortadoresBundle\Repository;


use Doctrine\ORM\EntityRepository;

class HistorialContableRecargaRepository extends EntityRepository
{

    public function buscarHistorialRecargas($_tarjeta, $_fecha)
    {
        $qb = $this->createQueryBuilder('HistorialContableRecarga');

        if (isset($_tarjeta) && $_tarjeta != '') {
            $qb->andWhere('HistorialContableRecarga.idTarjeta = :idTarjeta')
                ->setParameter('idTarjeta', "$_tarjeta");
        }
        if (isset($_fecha) && $_fecha != '') {
            $qb->andWhere('HistorialContableRecarga.fecha >= :fecha')
                ->setParameter('fecha', "$_fecha");
        }

        return $qb->orderBy('HistorialContableRecarga.fecha', 'ASC')
            ->getQuery()->getResult();
    }

}