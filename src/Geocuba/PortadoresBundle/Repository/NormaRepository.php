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

class NormaRepository extends EntityRepository
{
    /**
     * @param $tipo_mantenimiento
     * @param $denominacion
     * @param null $start
     * @param null $limit
     * @param bool $count
     * @return array|mixed
     */
    public function buscarNorma($tipo_mantenimiento, $marca, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('norma');
        if ($count)
            $qb->select('count(norma)');


        if (isset($tipo_mantenimiento) && $tipo_mantenimiento != '') {
            $qb->andWhere('norma.tipoMantenimiento = :tipo_mantenimiento')
                ->setParameter('tipo_mantenimiento', "$tipo_mantenimiento");
        }

        if (isset($marca) && $marca != '') {
            $qb->andWhere('norma.marca = :marca')
                ->setParameter('marca', "$marca");
        }

        return $qb->getQuery()->getResult();
    }
}