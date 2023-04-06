<?php
/**
 * Created by PhpStorm.
 * User: Soft
 * Date: 4/5/2023
 * Time: 8:40 PM
 */

namespace Geocuba\PortadoresBundle\Repository;
use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityRepository;


class CierreMesRepository extends EntityRepository
{
    public function deleteCierre($idunidad,$anno, $mes)
    {
        $consulta = $this->getEntityManager()->createQuery('DELETE  FROM PortadoresBundle:CierreMes cm
        where cm.idunidad = :idunidad and cm.anno = :anno and cm.mes > :mes');

        $consulta->setParameters(array(
            'idunidad' => $idunidad,
            'anno' => $anno,
            'mes' => $mes
        ));

        return $consulta->getResult();
    }


}