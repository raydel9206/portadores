<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Javier
 * Date: 16/01/14
 * Time: 16:09
 * To change this template use File | Settings | File Templates.
 */

namespace Geocuba\PortadoresBundle\Repository;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityRepository;

class Modelo5073Repository extends EntityRepository
{
    /**
     * @param $_nombre
     * @param $_tipoCombustible
     * @param $_unidades
     * @param null $start
     * @param null $limit
     * @param bool $count
     * @return array|mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function buscarModelo5073($anno, $mes, $unidad)
    {
        $qb = $this->createQueryBuilder('modelo');


        $qb->innerJoin('modelo.producto', 'producto')
            ->Where('modelo.anno = :anno')
            ->andWhere('modelo.mes = :mes')
            ->andWhere('modelo.unidadid = :unidad')
            ->setParameter('anno',$anno)
            ->setParameter('mes',$mes)
            ->setParameter('unidad',$unidad);

            return $qb->orderBy('producto.fila', 'ASC')
                ->getQuery()->getResult();

    }


}