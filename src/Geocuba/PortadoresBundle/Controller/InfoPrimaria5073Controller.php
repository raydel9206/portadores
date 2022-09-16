<?php
/**
 * Created by PhpStorm.
 * User: Yosley
 * Date: 14/07/2017
 * Time: 9:02
 */

namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\AdminBundle\Util\Util;
use Geocuba\PortadoresBundle\Entity\InfoPrimaria5073;
use Geocuba\PortadoresBundle\Entity\InfoPrimaria5073All;
use Geocuba\PortadoresBundle\Entity\Tarjeta;
use Geocuba\PortadoresBundle\Entity\TarjetaVehiculo;
use Geocuba\PortadoresBundle\Entity\Liquidacion;
use Geocuba\PortadoresBundle\Entity\Autolecturaprepago;
use Geocuba\PortadoresBundle\Entity\AutolecturaTresescalas;
use Geocuba\PortadoresBundle\Entity\Servicios;
use Geocuba\PortadoresBundle\Util\Datos;
use Doctrine\Common\CommonException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;

class InfoPrimaria5073Controller extends Controller
{
    

    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();

        $unidadid= $request->get('nunidadid');
        $anno = $session->get('selected_year');
        $mes_filtro = $request->get('mes');
        if ($mes_filtro) {
            $mes = $request->get('mes');
        } else
            $mes = $session->get('selected_month');


        $submayor=$this->loadSubmayor_5073Action($mes,$anno,$unidadid);



        return new JsonResponse(array('rows' => $submayor, 'total' => count($submayor)));
    }

    public function loadSubmayor_5073Action($mes,$anno,$nunidadid)
    {
        $conn = $this->get('database_connection');
        $em = $this->getDoctrine()->getManager();

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $unidades_string = $this->unidadesToString($_unidades);

   /*     $entities_info=$conn->fetchAll(" SELECT  *
FROM datos.info_primaria_5073
where unidad_id in ($_unidades) and mes=$mes and anno=$anno;");
if($entities_info!=null)
{

     foreach ($entities_info as $info)
     {
         $datos[]=array(
             'com_inicio_mes'=>$info['com_inicio_mes'],
             'compra_mes'=>$info['compra_mes'],
             'consumo'=>$info['consumo'],
             'comprobacion'=>$info['comb_fin_mes'],
             'carga_prox_mes'=>$info['com_inicio_mes'],
             'mes'=>$info['mes'],
             'anno'=>$info['anno'],
             'unidad_id'=>$info['unidad_id'],
             'tarjeta_id'=>$info['tarjeta_id'],
             'vehiculos'=>$info['vehiculos'],
         );
     }

    return $datos;

}else*/
    {


//        print_r($_unidades);die;

    $existencia_importe = 0;
    $entities = $conn->fetchAll(" SELECT  tarjetaid, max(fecha)as fecha , sum(entrada_importe) as entrada_importe ,sum(entrada_cantidad) as entrada_cantidad ,sum(salida_importe) as salida_importe, 
       sum(salida_cantidad) as salida_cantidad,max(mes) as mes, max(anno) as anno, max(liquidacionid) as liquidacionid, max(tarjeta.nro_tarjeta)as nro_tarjeta,max(tarjeta.estado)as estado,
       max(tipocomb.nombre) as tipocomb,max(tipocomb.precio) as precio,max(persona.nombre)as nombre_persona,max(vehiculo.matricula) as matricula
  FROM datos.historial_tarjeta 
  join nomencladores.Tarjeta as tarjeta on tarjeta.id=tarjetaid 
  join  nomencladores.ntipo_combustible as tipocomb on tipocomb.id=tarjeta.ntipo_combustibleid
  join nomencladores.Tarjeta_npersona as per_tar on per_tar.Tarjetaid =tarjetaid 
  join nomencladores.npersona as persona on persona.id=per_tar.npersonaid 
  join nomencladores.Tarjeta_nvehiculo as vehi_tar on vehi_tar.Tarjetaid=tarjetaid
  join nomencladores.nvehiculo as vehiculo on vehiculo.id=vehi_tar.nvehiculoid	
  join nomencladores.nunidad as unidad on unidad.id=tarjeta.nunidadid
  where unidad.id in ($unidades_string) and mes=$mes and anno=$anno and per_tar.visible=TRUE and vehi_tar.visible=TRUE 
   group by tarjetaid,tarjeta.nro_tarjeta ORDER By tarjeta.nro_tarjeta;");

    $_data = array();
    $valor_entregadas = 0;
    $valor_caja = 0;
//          ($entities);die;
    foreach ($entities as $entity) {
        $idtarjeta = $entity['tarjetaid'];
        $entities_saldo = $conn->fetchAll("SELECT id, mes, anno, id_tarjeta, saldo_inicial,saldo_inicial_litros
  FROM datos.saldo_tarjeta where id_tarjeta='$idtarjeta' and mes=$mes and anno=$anno;");

//print_r($entities_saldo);die;
        $entities_saldo_final = $conn->fetchAll("select tarjetaid,existencia_importe,existencia_cantidad,fecha from datos.historial_tarjeta
 where fecha=(select max(fecha) from datos.historial_tarjeta where mes=$mes and anno=$anno and tarjetaid='$idtarjeta')
 and tarjetaid=(select tarjetaid from datos.historial_tarjeta where mes=$mes and anno=$anno and tarjetaid='$idtarjeta' group by tarjetaid);");

        if ($entities_saldo_final != null) {

            $existencia_importe = $entities_saldo_final[0]['existencia_importe'];
            $existencia_importe_cant = $entities_saldo_final[0]['existencia_cantidad'];

        }
        if ($entities_saldo != null) {
            $saldo_inicial = floatval($entities_saldo[0]['saldo_inicial']);
            $saldo_inicial_cant = floatval($entities_saldo[0]['saldo_inicial_litros']);
        } else {
            $saldo_inicial = null;
            $saldo_inicial_cant = null;
        }


        $entities_last_recarga = $conn->fetchAll(" select id_tarjeta,fecha,monto_recarga,monto_recarga_litros 
 from datos.historial_contable_recarga
 where fecha=(select max(fecha)
  from datos.historial_contable_recarga 
  where extract(month from fecha)=$mes and extract(year from fecha)=$anno and id_tarjeta='$idtarjeta')
 and id_tarjeta=(select id_tarjeta from datos.historial_contable_recarga where extract(month from fecha)=$mes and extract(year from fecha)=$anno and id_tarjeta='$idtarjeta' group by id_tarjeta);");


        if ($entities_last_recarga != null) {
            $last_recarga = floatval($entities_last_recarga[0]['monto_recarga']);
            $last_recarga_cant = floatval($entities_last_recarga[0]['monto_recarga_litros']);
        } else {
            $last_recarga = 0;
            $last_recarga_cant = 0;
        }


        $_data[] = array(
            'id_tarjeta' => $entity['tarjetaid'],
            'nro_tarjeta' => $entity['nro_tarjeta'],
            'estado' => $entity['estado'],
            'nombre_persona' => $entity['nombre_persona'],
            'matricula' => $entity['matricula'],
            'entrada_importe' => floatval($entity['entrada_importe']),
            'entrada_cantidad' => floatval($entity['entrada_cantidad']),
            'salida_importe' => floatval($entity['salida_importe']),
            'salida_cantidad' => floatval($entity['salida_cantidad']),
            'saldo_inicial' => floatval($saldo_inicial),
            'saldo_inicial_cant' => floatval($saldo_inicial_cant),
            'tipoComb' => $entity['tipocomb'],
            'tipoComb_precio' =>floatval($entity['precio']) ,
            'saldo_final' => floatval($existencia_importe),
            'saldo_final_cant' => floatval($existencia_importe_cant),
            'importe_total' => floatval($existencia_importe),
            'importe_total_cant' => floatval($existencia_importe_cant),
            'ultima_recarga' => $last_recarga,
            'ultima_recarga_cant' => $last_recarga_cant,
            'comprobracion_importe' => ($saldo_inicial+floatval($entity['entrada_importe']))-floatval($entity['salida_importe']),
            'comprobracion'=>round(((floatval($saldo_inicial_cant)+floatval($entity['entrada_cantidad']))-floatval($entity['salida_cantidad'])),2)
        );
    }

//print_r($_data);die;
    $all = array();

    for ($j = 0; $j < count($_data); $j++) {
        if ($_data[$j]['tipoComb'] == 'Gasolina Especial') {
            array_push($all, $_data[$j]);
        }

    }

    for ($d = 0; $d < count($_data); $d++) {
        if ($_data[$d]['tipoComb'] == 'Diesel') {
            array_push($all, $_data[$d]);
        }

    }

    for ($i = 0; $i < sizeof($all) - 1; $i++) {
        for ($j = $i + 1; $j < sizeof($all); $j++) {
            if ($all[$i]['nro_tarjeta'] > $all[$j]['nro_tarjeta']) {
                $temp = $all[$i];
                $all[$i] = $all[$j];
                $all[$j] = $temp;

            }
        }
    }
    return $all;
}
    }

    public function loadElecAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $nunidadid= $request->get('nunidadid');
        $anno = $session->get('SELECTED_YEAR');
        $mes_filtro = $request->get('mes');
        if ($mes_filtro) {
            $mes = $request->get('mes');
        } else
            $mes = $session->get('SELECTED_MONTH');


        $conn = $this->get('database_connection');
        $_data = array();

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $unidades_string = $this->unidadesToString($_unidades);

//print_r($_unidades);die;
        $entities_info=$conn->fetchAll(" SELECT  *
FROM datos.info_primaria_5073_all
where unidad_id in ($unidades_string) and mes=$mes and anno=$anno;");


        if($entities_info!=null) {

            $entities_tres=$conn->fetchAll("SELECT sum(consumo_total_real) as consumo_total_real
FROM datos.autolectura_tresescalas as tres
join datos.servicios as serv On serv.id=tres.serviciosid
  join nomencladores.nunidad as unidad on unidad.id=serv.nunidadid
where unidad.id in ($unidades_string) and mes=$mes and anno=$anno
group by tres.serviciosid;");

            if($entities_tres!=null)
            {
                $consumo_tresescalas=$entities_tres[0]['consumo_total_real'];
            }else
                {
                    $consumo_tresescalas=0;
                }

            $entities_prep=$conn->fetchAll(" SELECT sum(consumo) as consumo,fecha_lectura
FROM datos.autolecturaprepago as prep
  join datos.servicios as serv On serv.id=prep.serviciosid
  join nomencladores.nunidad as unidad on unidad.id=serv.nunidadid
where unidad.id in ($unidades_string) and  EXTRACT(YEAR FROM fecha_lectura)=$anno and  EXTRACT(MONTH FROM fecha_lectura)=$mes
group by prep.serviciosid,fecha_lectura;");
if($entities_prep!=null)
{
    $consumo_prep=$entities_prep[0]['consumo'];
}else
{
    $consumo_prep=0;
}

  $consumo_electricidad=$consumo_tresescalas+$consumo_prep;

            foreach ($entities_info as $info) {
                $_data[] = array(
                    'id' => $info['id'],
                    'com_asignado' => $info['com_asignado'],
                    'com_disp_fincimex' => $info['com_disp_fincimex'],
                    'comb_disp_cuenta1' => $info['comb_disp_cuenta1'],
                    'no_cliente' => $info['no_cliente'],
                    'comb_entr_constructor' => $info['comb_entr_constructor'],
                    'anno' => $info['anno'],
                    'mes' => $info['mes'],
                    'unidad_id' => $info['unidad_id'],
                    'consumo_electricidad' => $info['consumo_energia']
                );
            }
        }else{

            $entities_tres=$conn->fetchAll("SELECT sum(consumo_total_real) as consumo_total_real
FROM datos.autolectura_tresescalas as tres
join datos.servicios as serv On serv.id=tres.serviciosid
  join nomencladores.nunidad as unidad on unidad.id=serv.nunidadid
where unidad.id in ($unidades_string) and mes=$mes and anno=$anno
group by tres.serviciosid;");

            if($entities_tres!=null)
            {
                $consumo_tresescalas=$entities_tres[0]['consumo_total_real'];
            }else
            {
                $consumo_tresescalas=0;
            }

            $entities_prep=$conn->fetchAll(" SELECT sum(consumo) as consumo,fecha_lectura
FROM datos.autolecturaprepago as prep
  join datos.servicios as serv On serv.id=prep.serviciosid
  join nomencladores.nunidad as unidad on unidad.id=serv.nunidadid
where unidad.id in ($unidades_string) and  EXTRACT(YEAR FROM fecha_lectura)=$anno and  EXTRACT(MONTH FROM fecha_lectura)=$mes
group by prep.serviciosid,fecha_lectura;");
            if($entities_prep!=null)
            {
                $consumo_prep=$entities_prep[0]['consumo'];
            }else
            {
                $consumo_prep=0;
            }


            $consumo_electricidad=$consumo_tresescalas+$consumo_prep;
            $_data[] = array(
                'id' => '',
                'com_asignado' => 0,
                'com_disp_fincimex' => 0,
                'comb_disp_cuenta1' => 0,
                'no_cliente' => 0,
                'comb_entr_constructor' => 0,
                'anno' =>0,
                'mes' => 0,
                'unidad_id' => 0,
                'consumo_electricidad' => $consumo_electricidad
            );

        }

        return new JsonResponse(array('rows' => $_data, 'total' => count($_data)));
    }

    public function guardarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $anno = $session->get('SELECTED_YEAR');
        $unidadid= $request->get('nunidadid');
        $mes_filtro = $request->get('mes');
        if ($mes_filtro) {
            $mes = $request->get('mes');
        } else
            $mes = $session->get('SELECTED_MONTH');

        $store = ($request->get('store'));
        $store_1 = ($request->get('store_1'));

        $entities_oc = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:InfoPrimaria5073All')->findBy(
            array(
                'anno' => $anno,
                'mes' => $mes,
                'unidad'=>$unidadid
            )
        );

//        print_r('sds');die;
if($entities_oc)
{
    foreach ($entities_oc as $oc) {
        $id = $oc->getId();
        $entity = $em->getRepository('PortadoresBundle:InfoPrimaria5073All')->find($id);
        foreach ($store_1 as $sto1)
        {

            $entity->setComAsignado($sto1['com_asignado']);
            $entity->setComDispFincimex($sto1['com_disp_fincimex']);
            $entity->setCombDispCuenta1($sto1['comb_disp_cuenta1']);
            $entity->setCombEntrConstructor($sto1['comb_entr_constructor']);
            $entity->setConsumoEnergia($sto1['consumo_electricidad']);
            $em->persist($entity);

        }
    }

}
else
    {
        $entity= new InfoPrimaria5073All();
        foreach ($store_1 as $sto1)
        {
            $entity->setComAsignado($sto1['com_asignado']);
            $entity->setComDispFincimex($sto1['com_disp_fincimex']);
            $entity->setCombDispCuenta1($sto1['comb_disp_cuenta1']);
            $entity->setCombEntrConstructor($sto1['comb_entr_constructor']);
            $entity->setConsumoEnergia($sto1['consumo_electricidad']);
            $entity->setUnidad($em->getRepository('PortadoresBundle:Unidad')->find($unidadid));
            $entity->setMes($mes);
            $entity->setAnno($anno);
            $em->persist($entity);
        }

    }


        foreach ($store as $sto) {
            $entities_info = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:InfoPrimaria5073')->findBy(
                array(
                    'anno' => $anno,
                    'mes' => $mes,
                    'tarjeta' => $sto['id_tarjeta'],
                    'unidad'=>$unidadid
                )
            );
            if ($entities_info) {
                foreach ($entities_info as $oc) {
                    $id = $oc->getId();
                    $entity = $em->getRepository('PortadoresBundle:InfoPrimaria5073')->find($id);
                    $entity->setVehiculos($sto['matricula']);
                    $entity->setCombInicioMes($sto['saldo_inicial_cant']);
                    $entity->setCompraMes($sto['entrada_importe']);
                    $entity->setConsumo($sto['salida_cantidad']);
                    $entity->setCombFinMes($sto['saldo_final_cant']);
                    $entity->setComprobacion($sto['comprobracion']);
                    $entity->setCargaProxMes($sto['ultima_recarga_cant']);
                    $entity->setMes($mes);
                    $entity->setAnno($anno);
                    $entity->setTarjeta($em->getRepository('PortadoresBundle:Tarjeta')->find($sto['id_tarjeta']));
                    $entity->setUnidad($em->getRepository('PortadoresBundle:Unidad')->find($unidadid));
                    $entity->setNoCliente($sto['cliente_gastos_corrientes']);
                    try {
                        $em->persist($entity);
                    } catch (CommonException $ex) {
                        return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
                    }
                }
            }
            else {
                $entity = new InfoPrimaria5073();
                $entity->setVehiculos($sto['matricula']);
                $entity->setCombInicioMes($sto['saldo_inicial_cant']);
                $entity->setCompraMes($sto['entrada_importe']);
                $entity->setConsumo($sto['salida_cantidad']);
                $entity->setCombFinMes($sto['saldo_final_cant']);
                $entity->setComprobacion($sto['comprobracion']);
                $entity->setCargaProxMes($sto['ultima_recarga_cant']);
                $entity->setMes($mes);
                $entity->setAnno($anno);
                $entity->setTarjeta($em->getRepository('PortadoresBundle:Tarjeta')->find($sto['id_tarjeta']));
                $entity->setUnidad($em->getRepository('PortadoresBundle:Unidad')->find($unidadid));
                $entity->setNoCliente($sto['cliente_gastos_corrientes']);
                try {
                    $em->persist($entity);
                } catch (CommonException $ex) {
                    return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
                }
            }
        }
        $em->flush();
        return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Datos guardados con éxito.'));
    }

    public function damemesAction($Nromes)
    {
        if ($Nromes == 1) {
            $nombremes = 'Enero';
        } elseif ($Nromes == 2) {
            $nombremes = 'Febrero';
        } elseif ($Nromes == 3) {
            $nombremes = 'Marzo';
        } elseif ($Nromes == 4) {
            $nombremes = 'Abril';
        } elseif ($Nromes == 5) {
            $nombremes = 'Mayo';
        } elseif ($Nromes == 6) {
            $nombremes = 'Junio';
        } elseif ($Nromes == 7) {
            $nombremes = 'Julio';
        } elseif ($Nromes == 8) {
            $nombremes = 'Agosto';
        } elseif ($Nromes == 9) {
            $nombremes = 'Septiembre';
        } elseif ($Nromes == 10) {
            $nombremes = 'Octubre';
        } elseif ($Nromes == 11) {
            $nombremes = 'Noviembre';
        } elseif ($Nromes == 12) {
            $nombremes = 'Diciembre';
        }
        return $nombremes;
    }
    
//    public function recursivoAction($id)
//    {
//
////
//        $_data = array();
//        $em = $this->getDoctrine()->getManager();
//        $hoja = true;
//        $id_U = $id;
//        $tree = array();
//
//        $entitiess = $em->getRepository('PortadoresBundle:NestructuraUnidades')->findByPadreid($id);
////        $tree[]=$id;
//        array_push($tree,$id);
//
//        if ($entitiess) {
//
//            foreach ($entitiess as $entity) {
////                        print_r($entity->getNunidadid()->getNombre());die;
////                $tree[]=$this->recursivoAction($entity->getNunidadid()->getId());
//                array_push($tree,$entity->getNunidadid()->getId());
////                print_r($tree);die;
//                $this->recursivoAction($entity->getNunidadid()->getId());
////                        $tree[] = array(
////                            'id' => $entity->getNunidadid()->getId(),
////                            'hijos'=>)
////                        );
//            }
//
//        }
//
//
//
//        return $tree;
//    }

    private function unidadesToString($_unidades){
        $_string_unidades = "'" . $_unidades[0]. "'";
        for ($i = 1; $i < count($_unidades); $i++) {
            $_string_unidades .= ",'" . $_unidades[$i]. "'";
        }
        return $_string_unidades;
    }
}