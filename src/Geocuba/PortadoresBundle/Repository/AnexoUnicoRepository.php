<?php
/**
 * Created by PhpStorm.
 * User: yosley
 * Date: 14/09/2017
 * Time: 17:15
 */

namespace Geocuba\PortadoresBundle\Repository;
use Doctrine\ORM\EntityRepository;

class AnexoUnicoRepository extends EntityRepository
{
    public function getanexobyfecha($idvehiculo,$fecha)
    {

        $consulta = $this->getEntityManager()->createQuery('Select a FROM PortadoresBundle:AnexoUnico a where a.nvehiculoid =:nvehiculoid and a.fecha >=:fecha');
        $consulta->setParameters(array(
            'nvehiculoid' => $idvehiculo,
            'fecha' => $fecha
        ));
        return $consulta->getResult();

    }


    public function buscarAnexoUnico($_unidades, $matricula, $mes, $anno, $count = false)
    {
        $qb = $this->createQueryBuilder('anexounico');
        if ($count) {
            $qb->select('count(anexounico)');
        }

//        var_dump($_unidades);die;

        $qb->innerJoin('anexounico.nvehiculoid', 'vehiculo');

        $qb->Where($qb->expr()->in('vehiculo.nunidadid', $_unidades))
            ->andWhere($qb->expr()->eq('vehiculo.visible', ':visible'))
            ->setParameter('visible', true);


        if (isset($mes) && $mes != '') {
            $qb->andWhere('anexounico.mes = :mes')
                ->setParameter('mes', "$mes");
        }
        if (isset($anno) && $anno != '') {
            $qb->andWhere('anexounico.anno = :anno')
                ->setParameter('anno', "$anno");
        }
        if (isset($matricula) && $matricula != '') {
            $qb->andWhere("clearstr(anexounico.matricula) like clearstr(:matricula)")
                ->setParameter('matricula', "%$matricula%");
        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('anexounico.id')
//                ->addOrderBy('tarjeta.nroTarjeta')
//                ->addOrderBy('unidad.nombre')
//                ->setMaxResults($limit)
//                ->setFirstResult($start)
                ->getQuery()->getResult();
        }
    }



}