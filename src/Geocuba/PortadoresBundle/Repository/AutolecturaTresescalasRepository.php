<?php
/**
 * Created by PhpStorm.
 * User: kireny
 * Date: 25/04/2016
 * Time: 10:24
 */

namespace Geocuba\PortadoresBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;

class AutolecturaTresescalasRepository extends EntityRepository {

    public function getFecha($mesBitacora,$anoBitacora,$servicio)
    {
        $consulta = $this->getEntityManager()->createQuery('Select a FROM PortadoresBundle:AutolecturaTresescalas a
        where a.mes =:mes and a.anno=:anno and a.serviciosid=:serviciosid Order By a.fechaLectura ASC');

        $consulta->setParameters(array(
            'mes' => $mesBitacora,
            'anno' => $anoBitacora,
            'serviciosid'=>$servicio,
        ));
        return $consulta->getResult();

    }
    public function getAutolecturbyMesAbierto($serviciosid,$fecha,$fechaFin)
    {
        if(isset($fecha) && isset($fechaFin)){
            $consulta = $this->getEntityManager()->createQuery('Select a FROM PortadoresBundle:AutolecturaTresescalas a
        where a.serviciosid =:serviciosid and a.fechaLectura BETWEEN :fecha AND :fechaFin Order By a.id ASC');

            $consulta->setParameters(array(
                'serviciosid' => $serviciosid,
                'fecha' => $fecha,
                'fechaFin' => $fechaFin,
            ));
        }else{
            $consulta = $this->getEntityManager()->createQuery('Select a FROM PortadoresBundle:AutolecturaTresescalas a
        where a.serviciosid =:serviciosid Order By a.id ASC');

            $consulta->setParameters(array(
                'serviciosid' => $serviciosid
            ));
        }
        return $consulta->getResult();

    }


    public function getFechaAutoinspeccion($mesBitacora)
    {
        $consulta = $this->getEntityManager()->createQuery('Select a FROM PortadoresBundle:AutolecturaAutoinspeccion a
        where a.mes =:mes Order By a.fecha ASC');

        $consulta->setParameters(array(
            'mes' => $mesBitacora
        ));
        return $consulta->getResult();

    }

    public function getFechaAutolecturaTresescalas($mesBitacora)
    {
        $consulta = $this->getEntityManager()->createQuery('Select a FROM PortadoresBundle:AutolecturaTresescalas a
        where a.mes =:mes Order By a.fechaLectura ASC');

        $consulta->setParameters(array(
            'mes' => $mesBitacora
        ));
        return $consulta->getResult();

    }
    public function cleanAllAutolecturas($servicio_id,$fecha_lectura)
    {
        $consulta = $this->getEntityManager()->createQuery('DELETE  FROM PortadoresBundle:AutolecturaTresescalas a
        where a.serviciosid = :servicio and a.fechaLectura >= :fecha');

        $consulta->setParameters(array(
            'servicio' => $servicio_id,
            'fecha' => $fecha_lectura
        ));
        return $consulta->getResult();
    }
    public function getAutolecturaTresescalas($serviciosid,$mes_abierto,$anno_abierto,$fecha_inicial)
    {

        $consulta = $this->getEntityManager()->createQuery('Select a FROM PortadoresBundle:AutolecturaTresescalas a
        where a.serviciosid =:serviciosid and a.fechaLectura >= :fecha_inicial and a.anno=:anno Order By a.id ASC');
        $consulta->setParameters(array(
            'serviciosid' => $serviciosid,
            'fecha_inicial' => $fecha_inicial,
//            'mes' => $mes_abierto,
            'anno' => $anno_abierto
        ));
        return $consulta->getResult();

    }

    public function getAutolecturaTresescalasMes($serviciosid,$fecha_inicial,$fecha_final)
    {

        $consulta = $this->getEntityManager()->createQuery('Select a FROM PortadoresBundle:AutolecturaTresescalas a
        where a.serviciosid =:serviciosid and a.fechaLectura >= :fecha_inicial and a.fechaLectura <= :fecha_final Order By a.id ASC');
        $consulta->setParameters(array(
            'serviciosid' => $serviciosid,
            'fecha_inicial' => $fecha_inicial,
            'fecha_final' => $fecha_final,
        ));
        return $consulta->getResult();

    }
    public function getserviciobyunidad($_unidad)
    {
        $consulta = $this->getEntityManager()->createQuery('Select serv FROM PortadoresBundle:Servicios serv
        where serv.nunidadid =:nunidadid and serv.visible = true Order By serv.id ASC');

        $consulta->setParameters(array(
            'nunidadid' => $_unidad
        ));

        return $consulta->getResult();

    }
    public function getserviciobyunidadMayor($_unidad)
    {
        $consulta = $this->getEntityManager()->createQuery('Select serv FROM PortadoresBundle:Servicios serv
        where serv.nunidadid =:nunidadid and serv.visible = true AND serv.servicioMayor = true Order By serv.id ASC');

        $consulta->setParameters(array(
            'nunidadid' => $_unidad
        ));

        return $consulta->getResult();

    }

        public function getPreciokWh($tarifaid)
    {
        $consulta = $this->getEntityManager()->createQuery('Select tarif FROM PortadoresBundle:Ntarifa tarif
        where tarif.id =:id  Order By tarif.id ASC');

        $consulta->setParameters(array(
            'id' => $tarifaid
        ));
        $data=array();
        foreach ($consulta->getResult() as $entity) {
      $data=array(
          'id'=>$entity->getId(),
          'nombre'=>$entity->getNombre(),
          'cv_pico'=>$entity->getCvPico(),
          'cv_madrugada'=>$entity->getCvMadrugada(),
          'cv_dia'=>$entity->getCvDia(),
          'cf'=>$entity->getCf(),
          'precioXKw'=>$entity->getPrecioXKw(),
          'cualquierhorario'=>$entity->getCvCualquierhorario()

      );
        }
//        print_r($data);die;
//
//        $CV_pico=$data['cv_pico'];
//        $CV_madrugada=$data['cv_madrugada'];
//        $CV_dia=$data['cv_dia'];
//        $CF=$data['cf'];

////Aplico la formula definida por la une (k x CVP+CFP)
//            $precio_kWh_PICO=$CV_pico*$factorK+$CF;
//            $precio_kWh_Madrug=$CV_madrugada*$factorK+$CF;
//            $precio_kWh_DIA=$CV_dia*$factorK+$CF;

//            $precios=array(
//                'precio_kWh_PICO'=>$CV_pico,
//                'precio_kWh_Madrug'=>$CV_madrugada,
//                'precio_kWh_DIA'=>$CV_dia,
//
//
//            );


        return $data;

    }

}