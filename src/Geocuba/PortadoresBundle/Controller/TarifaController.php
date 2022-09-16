<?php
/**
 * Created by PhpStorm.
 * User: yosley
 * Date: 02/11/2015
 * Time: 18:51
 */

namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\AdminBundle\Util\Util;
use Doctrine\Common\CommonException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\Utils\ViewActionTrait;

class TarifaController extends  Controller{

    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
//        $entities = $em->getRepository('PortadoresBundle:Tarifa')->FindListNtarifa(0, 1000);
        $entitiesTotal = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:Tarifa')->findByVisible(true);
        $_data = array();

            foreach ($entitiesTotal as $entity) {
//                \Doctrine\Common\Util\Debug::dump($entity);
                    $_data[] = array(
                        'id' => $entity->getId(),
                        'nombre' => $entity->getNombre(),
                        'costopico' => $entity->getCvPico(),
                        'costomadrugada' => $entity->getCvMadrugada(),
                        'costodia' => $entity->getCvDia(),
                        'costofijo' => $entity->getCf(),
                        'PrecioxKW' => $entity->getPrecioXKw(),
                        'grupo' => $entity->getGrupo(),
                        'costocualquierhorario' => $entity->getCvCualquierhorario(),

                    );
            }
        return new JsonResponse(array('rows' => $_data, 'total' => \count($_data)));
    }
    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');

        $precio_horario_pico=0;
        $precio_horario_dia=0;
        $precio_horario_madrugada=0;
        $precio_horario_todo_dia=0;

        $nombre = trim($request->get('nombre'));

        $momento_dia_c2 = trim($request->get('ecuacion'));
        $momento_dia = trim($request->get('momento_dia'));
        $horario_pico = trim($request->get('horario_pico'));
        $horario_dia = trim($request->get('horario_dia'));
        $horario_madrugada = trim($request->get('horario_madrugada'));

        $k_md= trim($request->get('k_md'));
        $k_hp = trim($request->get('k_hp'));
        $k_hd = trim($request->get('k_hd'));
        $k_hm = trim($request->get('k_hm'));

        $consumo_pico_md= trim($request->get('consumo_pico_md'));
        $consumo_pico_hp = trim($request->get('consumo_pico_hp'));
        $consumo_pico_hd = trim($request->get('consumo_pico_hd'));
        $consumo_pico_hm = trim($request->get('consumo_pico_hm'));

        if($nombre == 'C-2'){
            $hmd1=explode('*',$momento_dia_c2);
            $hmd2=$hmd1[0];
            $hmd3=explode(' $/KW/h ',$hmd2);
            $hmd4=$hmd3[0];
            $hmd5=explode('(',$hmd4);
            $hmd6=$hmd5[1];//Aqui esta el 1er Valor para el calculo

            $kgee= $k_md/550;
            $precio_horario_todo_dia= ($hmd6*$kgee)*$consumo_pico_md;
        }

        if(!empty($momento_dia)){
            /*AQUI BUSCO LOS VALORES PARA CUALQUIER MOMENTO DEL DIA*/
            $hmd1=explode('*',$momento_dia);
            $hmd2=$hmd1[0];
            $hmd3=explode(' $/KW/h ',$hmd2);
            $hmd4=$hmd3[0];
            $hmd5=explode('(',$hmd4);
            $hmd6=$hmd5[1];//Aqui esta el 1er Valor para el calculo
            $hmd7=$hmd1[1];
            $hmd8=explode(' $/KW/h',$hmd7);
            $hmd9=$hmd8[0];
            $hmd10=explode('K + ',$hmd9);
            $hmd11=$hmd10[1]; //Aqui esta el 2do Valor para el calculo

            $precio_horario_todo_dia= ($hmd6*$k_md+$hmd11)*$consumo_pico_md;
        }

        if(!empty($consumo_pico_hp)){

            /*AQUI BUSCO LOS VALORES PARA EL HORARIO PICO*/
            $hp1=explode('*',$horario_pico);
            $hp2=$hp1[0];
            $hp3=explode(' $/KW/h ',$hp2);
            $hp4=$hp3[0];
            $hp5=explode('(',$hp4);
            $hp6=$hp5[1];//Aqui esta el 1er Valor para el calculo
            $hp7=$hp1[1];
            $hp8=explode(' $/KW/h',$hp7);
            $hp9=$hp8[0];
            $hp10=explode('K + ',$hp9);
            $hp11=$hp10[1]; //Aqui esta el 2do Valor para el calculo

            /*AQUI BUSCO LOS VALORES PARA EL HORARIO DEL DIA*/
            $hd1=explode('*',$horario_dia);
            $hd2=$hd1[0];
            $hd3=explode(' $/KW/h ',$hd2);
            $hd4=$hd3[0];
            $hd5=explode('(',$hd4);
            $hd6=$hd5[1];//Aqui esta el 1er Valor para el calculo
            $hd7=$hd1[1];
            $hd8=explode(' $/KW/h',$hd7);
            $hd9=$hd8[0];
            $hd10=explode('K + ',$hd9);
            $hd11=$hd10[1]; //Aqui esta el 2do Valor para el calculo

            /*AQUI BUSCO LOS VALORES PARA EL HORARIO DE LA MADRUGADA*/
            $hm1=explode('*',$horario_madrugada);
            $hm2=$hm1[0];
            $hm3=explode(' $/KW/h ',$hm2);
            $hm4=$hm3[0];
            $hm5=explode('(',$hm4);
            $hm6=$hm5[1];//Aqui esta el 1er Valor para el calculo
            $hm7=$hm1[1];
            $hm8=explode(' $/KW/h',$hm7);
            $hm9=$hm8[0];
            $hm10=explode('K + ',$hm9);
            $hm11=$hm10[1]; //Aqui esta el 2do Valor para el calculo

            $precio_horario_pico=($hp6*$k_hp+$hp11)*$consumo_pico_hp;
            $precio_horario_dia=($hd6*$k_hd+$hd11)*$consumo_pico_hd;
            $precio_horario_madrugada=($hm6*$k_hm+$hm11)*$consumo_pico_hm;
        }

        $entity = $em->getRepository('PortadoresBundle:Tarifa')->find($id);
        $entity->setNombre($nombre);
        $entity->setPrecioHorarioTodoDia($precio_horario_todo_dia);
        $entity->setPrecioHorarioDia($precio_horario_dia);
        $entity->setPrecioHorarioPico($precio_horario_pico);
        $entity->setPrecioHorarioMadrugada($precio_horario_madrugada);

        try{
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Tarifa calculada con éxito.'));
            return $response;
        }
        catch(CommonException $ex){
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

//    public function modAction(Request $request)
//    {
//
//        $em = $this->getDoctrine()->getManager();
//        $id = $request->get('id');
//        $nombre = trim($request->get('nombre'));
//        $momento_dia = trim($request->get('momento_dia'));
//        $horario_pico = trim($request->get('horario_pico'));
//        $horario_dia = trim($request->get('horario_dia'));
//        $horario_madrugada = trim($request->get('horario_madrugada'));
//
//        if($nombre =='C-2'){
//            $momento_dia = trim($request->get('ecuacion')).' donde '.trim($request->get('kgee'));
//            $entity = $em->getRepository('PortadoresBundle:Tarifa')->find($id);
//            $entity->setNombre($nombre);
//            $entity->setHorarioTodoDia($momento_dia);
//            $entity->setHorarioDia($horario_dia);
//            $entity->setHorarioPico($horario_pico);
//            $entity->setHorarioMadrugada($horario_madrugada);
//            try{
//                $em->persist($entity);
//                $em->flush();
//                $response = new JsonResponse();
//                $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Ecuación modificada con éxito.'));
//                return $response;
//            }
//            catch(CommonException $ex){
//                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
//            }
//        }
//        else{
//            $entity = $em->getRepository('PortadoresBundle:Tarifa')->find($id);
//            $entity->setNombre($nombre);
//            $entity->setHorarioTodoDia($momento_dia);
//            $entity->setHorarioDia($horario_dia);
//            $entity->setHorarioPico($horario_pico);
//            $entity->setHorarioMadrugada($horario_madrugada);
//            try{
//                $em->persist($entity);
//                $em->flush();
//                $response = new JsonResponse();
//                $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Tarifa modificada con éxito.'));
//                return $response;
//            }
//            catch(CommonException $ex){
//                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
//            }
//        }
//    }
//    public function delAction(Request $request){
//        $em = $this->getDoctrine()->getManager();
//        $id = $request->get('id');
//        $entity = $em->getRepository('PortadoresBundle:Tarifa')->find($id);
//        $entity->setVisible(false);
//
//        try{
//            $em->persist($entity);
//            $em->flush();
//            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Tarifa eliminada con éxito.'));
//        }
//        catch(CommonException $ex){
//            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
//        }
//    }





}








