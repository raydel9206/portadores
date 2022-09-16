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

class FactorRepository extends EntityRepository {

    public function FindListFactor($start = null, $limit = null)
    {
        $consulta = $this->getEntityManager()->createQuery('Select a FROM PortadoresBundle:Factor a
        where a.visible = true Order By a.portador ASC');

        if($limit != -1)
        {
            $consulta->setMaxResults($limit);
            $consulta->setFirstResult($start);
        }

        return $consulta->getResult();

    }

}