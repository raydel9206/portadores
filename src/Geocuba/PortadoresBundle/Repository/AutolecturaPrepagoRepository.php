<?php
/**
 * Created by PhpStorm.
 * User: yosley
 * Date: 10/04/2017
 * Time: 10:23 AM
 */

namespace Geocuba\PortadoresBundle\Repository;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityRepository;

class AutolecturaPrepagoRepository extends EntityRepository
{


    public function getAutolecturaPrepago($serviciosid)
    {
        $consulta = $this->getEntityManager()->createQuery('Select a FROM PortadoresBundle:AutolecturaTresescalas a
        where a.serviciosid =:serviciosid Order By a.id ASC');

        $consulta->setParameters(array(
            'serviciosid' => $serviciosid
        ));
        return $consulta->getResult();

    }

    public function ValidarFecha($fecha, $servicio)
    {
        $validar = false;

        $consulta = $this->getEntityManager()->createQuery('Select MAX(a.fechaLectura) FROM PortadoresBundle:Autolecturaprepago a where a.serviciosid =:serviciosid ');
        $consulta->setParameters(array(
            'serviciosid' => $servicio
        ));
        $fd = $fecha->format('Y-m-d');
        $datos = $consulta->getResult();

        if ($datos[0][1] > $fd) {
            $validar = true;
        }
        return $validar;

    }

    public function getLastFecha($servicio)
    {

        $consulta = $this->getEntityManager()->createQuery('Select MAX(a.fechaLectura) FROM PortadoresBundle:Autolecturaprepago a where a.serviciosid =:serviciosid ');
        $consulta->setParameters(array(
            'serviciosid' => $servicio
        ));

        return $consulta->getResult();

    }

    public function getAutolecturbyMesAbierto($serviciosid,$fecha,$fechaFin)
    {
        if(isset($fecha) && isset($fechaFin)){
            $consulta = $this->getEntityManager()->createQuery("Select a FROM PortadoresBundle:Autolecturaprepago a
            where a.serviciosid =:serviciosid and a.fechaLectura BETWEEN :fecha AND :fechaFin Order By a.id ASC");
            $consulta->setParameters(array(
                'serviciosid' => $serviciosid,
                'fecha' => $fecha,
                'fechaFin' => $fechaFin,
            ));
        }else{
            $consulta = $this->getEntityManager()->createQuery('Select a FROM PortadoresBundle:Autolecturaprepago a
            where a.serviciosid =:serviciosid Order By a.id ASC');
            $consulta->setParameters(array(
                'serviciosid' => $serviciosid,
            ));
        }
        return $consulta->getResult();
    }

    public function getAutolecturaprepagoMes($serviciosid,$fecha_inicial,$fecha_final)
    {

        $consulta = $this->getEntityManager()->createQuery('Select a FROM PortadoresBundle:Autolecturaprepago a
        where a.serviciosid =:serviciosid and a.fechaLectura >= :fecha_inicial and a.fechaLectura < :fecha_final Order By a.id ASC');
        $consulta->setParameters(array(
            'serviciosid' => $serviciosid,
            'fecha_inicial' => $fecha_inicial,
            'fecha_final' => $fecha_final,
        ));
        return $consulta->getResult();

    }



    public function cleanAllAutolecturas($servicio_id,$fecha_lectura)
    {
        $consulta = $this->getEntityManager()->createQuery('DELETE  FROM PortadoresBundle:Autolecturaprepago a
        where a.serviciosid = :servicio and a.fechaLectura >= :fecha');

        $consulta->setParameters(array(
            'servicio' => $servicio_id,
            'fecha' => $fecha_lectura
        ));
        return $consulta->getResult();
    }

    public function getdatosServicio($servicios)
    {
        $consulta = $this->getEntityManager()->createQuery('Select a. FROM PortadoresBundle:Servicios a
        where a.serviciosid =:serviciosid Order By a.id ASC');

        $consulta->setParameters(array(
            'serviciosid' => $servicios
        ));
        return $consulta->getResult();
    }


}