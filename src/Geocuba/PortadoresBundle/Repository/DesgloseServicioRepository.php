<?php
/**
 * Created by PhpStorm.
 * User: rherrerag
 * Date: 10/11/2017
 * Time: 15:18
 */

namespace Geocuba\PortadoresBundle\Repository;


use Doctrine\ORM\EntityRepository;

class DesgloseServicioRepository extends EntityRepository
{
    public function getdesgloseServicio($serviciosid){
        $consulta = $this->getEntityManager()->createQuery('Select d FROM PortadoresBundle:DesgloseServicios d
        where d.idservicio =:serviciosid Order By d.id ASC');

        $consulta->setParameters(array(
            'serviciosid' => $serviciosid
        ));
        return $consulta->getResult();
    }

    public function getDesgServFecha($mes,$anno,$servicio)
    {
        $consulta = $this->getEntityManager()->createQuery('Select d FROM PortadoresBundle:DesgloseServicios d where  d.mes =:mes and d.anno =:anno and d.idservicio =:servicio');
        $consulta->setParameters(array(
            'mes' => $mes,
            'anno' => $anno,
            'servicio' => $servicio
        ));
        return $consulta->getResult();

    }

}