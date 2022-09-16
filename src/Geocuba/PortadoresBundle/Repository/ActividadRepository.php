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
use Symfony\Component\Debug\Debug;

class ActividadRepository extends EntityRepository
{
    /**
     * @param $_nombre
     * @param null $start
     * @param null $limit
     * @param bool $count
     * @return array|mixed
     */
    public function buscarActividad($_nombre, $portadorid, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('actividad');
        if ($count)
            $qb->select('count(actividad)');

        if (isset($portadorid) && $portadorid != '') {
            $qb->innerJoin('actividad.portadorid', 'portador')
                ->andWhere('trim(lower(portador.id)) like trim(lower(:id))')
                ->setParameter('id', "%$portadorid%");
        }

        $qb->andWhere($qb->expr()->eq('actividad.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('lower(actividad.nombre) like lower(:nombre)')
                ->setParameter('nombre', "%$_nombre%");
        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('actividad.nombre', 'ASC')
                ->setMaxResults($limit)
                ->setFirstResult($start)
                ->getQuery()->getResult();
        }
    }

    public function buscarActividadCombo($portadorid)
    {
        $qb = $this->createQueryBuilder('actividad');
        $qb->select('actividad.id', 'actividad.nombre', 'portador.nombre as portador_nombre');

        if (isset($portadorid) && $portadorid != '') {
            $qb->innerJoin('actividad.portadorid', 'portador')
                ->andWhere('trim(lower(portador.id)) like trim(lower(:id))')
                ->setParameter('id', "%$portadorid%");
        }else
        {
            $qb->innerJoin('actividad.portadorid', 'portador');
        }

        $qb->andWhere($qb->expr()->eq('actividad.visible', ':visible'))
            ->setParameter('visible', true);

        return $qb->orderBy('actividad.nombre', 'ASC')
            ->getQuery()->getResult();
    }

    public function buscarActividadCodigoCombo($portadorid)
    {
        $qb = $this->createQueryBuilder('actividad');
        $qb->select('actividad.id', 'actividad.nombre', 'actividad.nombre');

        if (isset($portadorid) && $portadorid != '') {
            $qb->innerJoin('actividad.portadorid', 'portador')
                ->andWhere('trim(lower(portador.id)) like trim(lower(:id))')
                ->setParameter('id', "%$portadorid%");
        }

        $qb->andWhere($qb->expr()->eq('actividad.visible', ':visible'))
            ->setParameter('visible', true);

        return $qb->orderBy('actividad.nombre', 'ASC')
            ->getQuery()->getResult();
    }

    /**
     * @param $_nombre
     * @param $_umActividad
     * @param $_portador
     * @param string $id
     * @return mixed
     */
    public function buscarActividadRepetido($_nombre, $_umActividad, $_portador, $id = '')
    {
        $qb = $this->createQueryBuilder('actividad');
        $qb->select('count(actividad)');

        $qb->andWhere($qb->expr()->eq('actividad.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('lower(actividad.nombre) = lower(:nombre)')
                ->setParameter('nombre', $_nombre);
        }

        if (isset($_umActividad) && $_umActividad != '') {
            $qb->andWhere('actividad.umActividad = :umActividad')
                ->setParameter('umActividad', $_umActividad);
        }

        if (isset($_portador) && $_portador != '') {
            $qb->andWhere('actividad.portadorid = :portador')
                ->setParameter('portador', $_portador);
        }

        if (isset($id) && $id != '') {
            $qb->andWhere('actividad.id != :id')
                ->setParameter('id', $id);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function buscarActividadxTipoCombustible($portador, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('actividad');
        if ($count)
            $qb->select('count(actividad)');

        $qb->innerJoin('actividad.portadorid', 'portador')
            ->andWhere($qb->expr()->eq('actividad.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($portador) && $portador != '') {
            $qb->andWhere('actividad.portadorid = :portadorid')
                ->setParameter('portadorid', $portador);
        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('portador.nombre', 'ASC')
                ->setMaxResults($limit)
                ->setFirstResult($start)
                ->getQuery()->getResult();
        }
    }
}