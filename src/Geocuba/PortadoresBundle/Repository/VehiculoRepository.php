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

class VehiculoRepository extends EntityRepository
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
    public function buscarVehiculo($_nombre, $_tipoCombustible, $_tipoMedio, $_unidades, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('vehiculo');
        if ($count)
            $qb->select('count(vehiculo)');

        $qb->innerJoin('vehiculo.ndenominacionVehiculoid', 'denominacion')
            ->where($qb->expr()->in('vehiculo.nunidadid', ':unidades'))
            ->setParameter('unidades',$_unidades)
            ->andWhere($qb->expr()->eq('vehiculo.visible', ':visible'))
            ->setParameter('visible', true);

        if ($_nombre !== null && $_nombre != '') {
            $qb->andWhere('lower(vehiculo.matricula) like lower(:nombre)')
                ->setParameter('nombre', "%$_nombre%");
        }
        if ($_tipoCombustible !== null && $_tipoCombustible != '') {
            $qb->andWhere('vehiculo.ntipoCombustibleid = :tipoCombustible')
                ->setParameter('tipoCombustible', $_tipoCombustible);
        }

        if ($_tipoMedio !== null && $_tipoMedio != '') {
            $qb->andWhere('vehiculo.ndenominacionVehiculoid = :tipoMedio')
                ->setParameter('tipoMedio', $_tipoMedio);
        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('denominacion.orden', 'ASC')
                 ->orderBy('vehiculo.matricula', 'ASC')
                ->setMaxResults($limit)
                ->setFirstResult($start)
                ->getQuery()->getResult();
        }
    }


    public function buscarVehiculoCombo($_unidades, $_tipoCombustible)
    {
        $qb = $this->createQueryBuilder('vehiculo');
        $qb->select('vehiculo.id','vehiculo.matricula','vehiculo.norma','vehiculo.odometro','marca.nombre','unidad.id as nunidadid', 'tipo_combustible.id as tipo_combustibleid');

        $qb->innerJoin('vehiculo.nunidadid', 'unidad')
            ->innerJoin('vehiculo.nmodeloid', 'modelo')
            ->innerJoin('vehiculo.ntipoCombustibleid', 'tipo_combustible')
            ->innerJoin('modelo.marcaVehiculoid', 'marca')
            ->where($qb->expr()->in('vehiculo.nunidadid', ':unidades'))
            ->setParameter('unidades',$_unidades)
            ->andWhere($qb->expr()->eq('vehiculo.visible', ':visible'))
            ->setParameter('visible', true);

        if ($_tipoCombustible !== null && $_tipoCombustible != '') {
            $qb->andWhere('vehiculo.ntipoCombustibleid = :tipoCombustible')
                ->setParameter('tipoCombustible', $_tipoCombustible);
        }

            return $qb->orderBy('vehiculo.matricula', 'ASC')
                ->getQuery()->getResult();
    }

    /**
     * @param $_nombre
     * @param $_tipoCombustible
     * @param $_unidades
     * @return array
     */
    public function buscarVehiculoTipoCombustible($_nombre, $_tipoCombustible, $_unidades): array
    {
        $qb = $this->createQueryBuilder('vehiculo');

        $qb->innerJoin('vehiculo.ntipoCombustibleid', 'ntipoCombustibleid')
            ->innerJoin('vehiculo.ndenominacionVehiculoid', 'ndenominacionVehiculoid')
            ->where($qb->expr()->in('vehiculo.nunidadid', $_unidades))
            ->andWhere($qb->expr()->eq('vehiculo.visible', ':visible'))
            ->setParameter('visible', true);

        if ($_nombre !== null && $_nombre != '') {
            $qb->andWhere('lower(vehiculo.matricula) like lower(:nombre)')
                ->setParameter('nombre', "%$_nombre%");
        }
        if ($_tipoCombustible !== null && $_tipoCombustible != '') {
            $qb->andWhere('vehiculo.ntipoCombustibleid = :tipoCombustible')
                ->setParameter('tipoCombustible', $_tipoCombustible);
        }

        return $qb->orderBy('ntipoCombustibleid.nombre', 'ASC')
            ->addOrderBy('ndenominacionVehiculoid.orden', 'ASC')
            ->getQuery()->getResult();
    }

    public function buscarVehiculoxActividad($actividad)
    {
        $qb = $this->createQueryBuilder('vehiculo');


        $qb->innerJoin('vehiculo.actividad', 'actividad')
            ->where($qb->expr()->eq('vehiculo.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($actividad) && $actividad != '') {
            $qb->andWhere('vehiculo.actividad = :actividad')
                ->setParameter('actividad', $actividad);
        }
//        if ($_unidad !== null && $_unidad != '') {
//            $qb->andWhere('vehiculo.nunidadid = :unidad')
//                ->setParameter('unidad', $_unidad);
//        }

            return $qb->orderBy('actividad.nombre', 'ASC')
                ->getQuery()->getResult();

    }

    public function buscarVehiculoParalizadosPorDenominacion($_denominacion, $_unidades)
    {
        $qb = $this->createQueryBuilder('vehiculo');

        $qb->Where($qb->expr()->eq('vehiculo.visible', ':visible'))
            ->setParameter('visible', true)
            ->andWhere($qb->expr()->eq('vehiculo.paralizado', ':paralizado'))
            ->setParameter('paralizado', true);

        if (isset($_unidades) && $_unidades != [])
            $qb->andWhere($qb->expr()->in('vehiculo.nunidadid', $_unidades));

        if (isset($_denominacion) && $_denominacion != '')
            $qb->andWhere($qb->expr()->eq('vehiculo.ndenominacionVehiculoid', ':denominacionid'))
                ->setParameter('denominacionid', $_denominacion);

        return $qb->getQuery()->getResult();
    }

    public function buscarVehiculoPorDenominacion($_denominacion, $_unidades)
    {
        $qb = $this->createQueryBuilder('vehiculo');
        $qb->Where($qb->expr()->eq('vehiculo.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_unidades) && $_unidades != [])
            $qb->andWhere($qb->expr()->in('vehiculo.nunidadid', $_unidades));

        if (isset($_denominacion) && $_denominacion != '')
            $qb->andWhere($qb->expr()->eq('vehiculo.ndenominacionVehiculoid', ':denominacionid'))
                ->setParameter('denominacionid', $_denominacion);

        return $qb->getQuery()->getResult();
    }

}