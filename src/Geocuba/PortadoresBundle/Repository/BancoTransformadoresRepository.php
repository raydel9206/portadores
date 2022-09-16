<?php
/**
 * Created by PhpStorm.
 * User: kireny
 * Date: 06/05/16
 * Time: 10:45
 */
namespace Geocuba\PortadoresBundle\Repository;
use Doctrine\ORM\EntityRepository;

class BancoTransformadoresRepository extends EntityRepository {

    public function FindListBancoTransformadores($start = null, $limit = null)
    {
        $consulta = $this->getEntityManager()->createQuery('Select a FROM PortadoresBundle:BancoTransformadores a
        where a.visible = true Order By a.capacidad ASC');

        if($limit != -1)
        {
            $consulta->setMaxResults($limit);
            $consulta->setFirstResult($start);
        }
        return $consulta->getResult();

    }

}