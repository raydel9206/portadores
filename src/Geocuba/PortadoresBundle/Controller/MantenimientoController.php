<?php
/**
 * Created by PhpStorm.
 * User: kireny
 * Date: 22/10/15
 * Time: 11:17
 */

namespace Geocuba\PortadoresBundle\Controller;

use Doctrine\Common\Util\Debug;
use Geocuba\PortadoresBundle\Entity\Mantenimiento;
use Doctrine\Common\CommonException;
use Geocuba\PortadoresBundle\Entity\MarcaVehiculo;
use Geocuba\PortadoresBundle\Entity\ModeloVehiculo;
use Geocuba\PortadoresBundle\Entity\Norma;
use Geocuba\PortadoresBundle\Entity\TipoMantenimiento;
use Geocuba\PortadoresBundle\Entity\Vehiculo;
use Geocuba\Utils\Constants;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Geocuba\AdminBundle\Util\Util;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;

class MantenimientoController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $start = $request->get('start');
        $limit = $request->get('limit');
        $nvehiculoid = trim($request->get('nvehiculoid'));

        $_data = array();
        $qb = $em->createQueryBuilder();
        $qb->select('mantenimiento')
            ->from('PortadoresBundle:Mantenimiento', 'mantenimiento')
            ->innerJoin('mantenimiento.nvehiculoid', 'vehiculo')
            ->innerJoin('vehiculo.ndenominacionVehiculoid', 'denominacion');

        $qb->Where($qb->expr()->eq('mantenimiento.nvehiculoid', ':nvehiculoid'))
            ->setParameter('nvehiculoid', $nvehiculoid);

        $entities = $qb->orderBy('denominacion.nombre,mantenimiento.fecha', 'ASC')
            ->getQuery()
            ->setMaxResults($limit)
            ->setFirstResult($start)
            ->getResult();

        $qb = $em->createQueryBuilder();
        $qb->select('count(mantenimiento)')
            ->from('PortadoresBundle:Mantenimiento', 'mantenimiento')
            ->innerJoin('mantenimiento.nvehiculoid', 'vehiculo')
            ->innerJoin('vehiculo.ndenominacionVehiculoid', 'denominacion');

        $qb->Where($qb->expr()->eq('mantenimiento.nvehiculoid', ':nvehiculoid'))
            ->setParameter('nvehiculoid', $nvehiculoid);

        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('clearstr(vehiculo.matricula) like clearstr(:nombre)')
                ->setParameter('nombre', "%$_nombre%");
        }
        $total = $qb->getQuery()->getSingleScalarResult();


        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'kilometraje' => $entity->getKilometraje(),
                'fecha' => $entity->getFecha()->format('d/m/Y'),
                'tipo_mantenimientoid' => $entity->getTipoMantenimientoid()->getId(),
                'tipo_mantenimientonombre' => $entity->getTipoMantenimientoid()->getNombre(),
                'nvehiculoid' => $entity->getNvehiculoid()->getId(),
                'nvehiculomatricula' => $entity->getNvehiculoid()->getMatricula(),
                'observaciones' => $entity->getObservaciones(),
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    public function loadUltimoAction(Request $request)
    {
        $nvehiculoid = trim($request->get('nvehiculoid'));
        $sql = "select max(m.fecha) as fecha,
                       max(m.nvehiculoid) as vehiculo
                
                from datos.mantenimiento as m
                join nomencladores.vehiculo as v on v.id = m.nvehiculoid
                where v.id = '$nvehiculoid'
                group by v.id";
        $sql_result = $this->getDoctrine()->getConnection()->fetchAll($sql);

        return new JsonResponse(array('success' => true, 'fecha' => (count($sql_result) > 0) ? $sql_result[0]['fecha'] : null));
    }

    public function loadVehiculosAction(Request $request)
    {

        $_nombre = trim($request->get('nombre'));
        $_tipoCombustible = trim($request->get('tipoCombustible'));
        $session = $request->getSession();
        $year = $session->get('current_year');

        $nunidadid = $request->get('unidadid');
        $start = $request->get('start');
        $limit = $request->get('limit');
        $em = $this->getDoctrine()->getManager();

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $entities = $em->getRepository('PortadoresBundle:Vehiculo')->buscarVehiculo($_nombre, '', '', $_unidades, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:Vehiculo')->buscarVehiculo($_nombre, '', '', $_unidades, $start, $limit, true);

        $datos = array();
        if (!$entities) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'No hay vehículos registrados'));
        }
        foreach ($entities as $entity) {
            $idvehiculo = $entity->getId();

            $sql2 = "select tm.id as tm_id,
                            tm.nombre, 
                            n.cant_horas as km_to_mant

                    from nomencladores.vehiculo as v
                    inner join nomencladores.modelo_vehiculo as mv  on v.nmodeloid = mv.id
                    inner join nomencladores.marca_vehiculo as m on m.id = mv.nmarca_vehiculoid
                    inner join nomencladores.norma as n on n.marca = m.id
                    inner join nomencladores.tipo_mantenimiento as tm on n.tipo_mantenimiento = tm.id
                               
                    where v.id = '$idvehiculo'";
            $sql_result2 = $this->getDoctrine()->getConnection()->fetchAll($sql2);

            $sql3 = "select max(m.fecha) as fecha,
                            max(m.nvehiculoid) as vehiculo,
                            max(m.kilometraje)as kilometraje
                
                from datos.mantenimiento as m
                join nomencladores.vehiculo as v on v.id = m.nvehiculoid
                where v.id = '$idvehiculo'
                group by v.id";
            $sql_result3 = $this->getDoctrine()->getConnection()->fetchAll($sql3);

            //Inicial
            $fecha = $year . '-01-01';
            $kInicial = 0;
            $sql4 = "select v.matricula as matricula,
                           rc.id,
                           v.id as vehiculoid,
						   rc.fecha 

                    from datos.registro_combustible as rc
                    join nomencladores.vehiculo as v on v.id = rc.vehiculoid
                    where v.id = '$idvehiculo' and rc.fecha < '$fecha'
					order by rc.fecha DESC";
            $sql_result4 = $this->getDoctrine()->getConnection()->fetchAll($sql4);

            if (count($sql_result4) > 0) {
                for ($i = 0; $i < count($sql_result4); $i++) {
                    $id = $sql_result4[$i]['id'];
                    $sql5 = "select rca.km

                    from datos.registro_combustible_analisis as rca
                    join datos.registro_combustible as rc on rc.id = rca.registro_combustible_id
                    where rca.registro_combustible_id = '$id' and rca.visible = TRUE and rca.conceptoid = '4'
					order by rca.numerosemana ASC";
                    $sql_result5 = $this->getDoctrine()->getConnection()->fetchAll($sql5);

                    if (count($sql_result5) > 0) {
                        $kInicial = $sql_result5[count($sql_result5) - 1]['km'];
                        break;
                    }
                }
            }


            //Final
            $kFinal = 0;
            $sql6 = "select v.matricula as matricula,
                           rc.id,
                           v.id as vehiculoid,
						   rc.fecha 

                    from datos.registro_combustible as rc
                    join nomencladores.vehiculo as v on v.id = rc.vehiculoid
                    where v.id = '$idvehiculo'
					order by rc.fecha DESC";
            $sql_result6 = $this->getDoctrine()->getConnection()->fetchAll($sql6);

            if (count($sql_result6) > 0) {
                for ($j = 0; $j < count($sql_result6); $j++) {
                    $id = $sql_result6[$j]['id'];
                    $sql7 = "select rca.km

                    from datos.registro_combustible_analisis as rca
                    join datos.registro_combustible as rc on rc.id = rca.registro_combustible_id
                    where rca.registro_combustible_id = '$id' and rca.visible = TRUE and rca.conceptoid = '4'
					order by rca.numerosemana ASC";
                    $sql_result7 = $this->getDoctrine()->getConnection()->fetchAll($sql7);

                    if (count($sql_result7) > 0) {
                        $kFinal = $sql_result7[count($sql_result7) - 1]['km'];
                        break;
                    }
                }
            }

            $mants = array();
            if ($kFinal !== 0) {
                foreach ($sql_result2 as $item2) {
                    if ($item2['km_to_mant'] > 0) {
                        $tonext = (count($sql_result3) > 0) ? intval($sql_result3[0]['kilometraje']) + intval($item2['km_to_mant']) : 0;

                        $dif = ($tonext !== 0) ? intval($tonext) - intval($kFinal) : 0;
                        $mants[] = array(
                            'mantenimiento' => $item2['nombre'],
                            'proximo' => (count($sql_result3) > 0) ? intval($sql_result3[0]['kilometraje']) + intval($item2['km_to_mant']) : 'Realice el primer mantenimiento',
                            'dif' => ($dif > 0) ? $dif : 'Pasado',
                        );
                    }
                }
            }

            $datos[] = array(
                'id' => $entity->getId(),
                'matricula' => $entity->getMatricula(),
                'odometro_inicio' => ($kInicial !== 0) ? intval($kInicial) : 'Sin registro disponible',
                'odometro' => ($kFinal !== 0) ? intval($kFinal) : 'Sin registro disponible',
                'kms_to_mant' => (count($sql_result2) > 0) ? intval($sql_result2[0]['km_to_mant']) : 0,
                'proximo_mant' => $mants
            );
        }

        return new JsonResponse(array('rows' => $datos, 'total' => $total));

    }

    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $fecha = trim($request->get('fecha'));
        $kilometraje = trim($request->get('kilometraje'));
        $observaciones = trim($request->get('observaciones'));
        $tipo_mantenimientoid = trim($request->get('tipo_mantenimientoid'));
        $nvehiculoid = trim($request->get('nvehiculoid'));

        if (!is_null($fecha)) {
            $fechaA = date_create_from_format('d/m/Y', $fecha);
        } else {
            $fechaA = ' ';
        }

        $entities = $em->getRepository('PortadoresBundle:Mantenimiento')->findByNvehiculoid($nvehiculoid);
        foreach ($entities as $ent) {
            /**@var Mantenimiento $ent */
            if (intval($ent->getKilometraje()) === intval($kilometraje)) {
                return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Verifique el kilometraje del vehículo. Ya tiene un mantenimiento realizado a los ' . intval($ent->getKilometraje()) . ' Km'));
            }
            $ent->setUltimo(false);
            $em->persist($ent);
        }

        $entity = new Mantenimiento();
        $entity->setFecha($fechaA);
        $entity->setKilometraje($kilometraje);
        $entity->setObservaciones($observaciones);
        $entity->setTipoMantenimientoid($em->getRepository('PortadoresBundle:TipoMantenimiento')->find($tipo_mantenimientoid));
        $entity->setNvehiculoid($em->getRepository('PortadoresBundle:Vehiculo')->find($nvehiculoid));
        $entity->setUltimo(true);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Mantenimiento adicionado con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function modAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $fecha = trim($request->get('fecha'));
        $kilometraje = trim($request->get('kilometraje'));
        $observaciones = trim($request->get('observaciones'));
        $tipo_mantenimientoid = trim($request->get('tipo_mantenimientoid'));
        $nvehiculoid = trim($request->get('nvehiculoid'));

        $entity = $em->getRepository('PortadoresBundle:Mantenimiento')->find($id);
        $fechaA = date_create_from_format('d/m/Y', $fecha);

        $entity->setFecha($fechaA);
        $entity->setKilometraje($kilometraje);
        $entity->setObservaciones($observaciones);
        $entity->setTipoMantenimientoid($em->getRepository('PortadoresBundle:TipoMantenimiento')->find($tipo_mantenimientoid));
        $entity->setNvehiculoid($em->getRepository('PortadoresBundle:Vehiculo')->find($nvehiculoid));

        try {
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Mantenimiento modificado con éxito.'));
            return $response;
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function delAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $entity = $em->getRepository('PortadoresBundle:Mantenimiento')->find($id);
        $vehiculoid = $entity->getNvehiculoid()->getId();

        $em->remove($entity);
        $sql = "select max(m.fecha) as fecha,
                       max(m.nvehiculoid) as vehiculo
                
                from datos.mantenimiento as m
                join nomencladores.vehiculo as v on v.id = m.nvehiculoid
                where v.id = '$vehiculoid'
                group by v.id";
        $sql_result = $this->getDoctrine()->getConnection()->fetchAll($sql);
        $ultimo_mant = $em->getRepository('PortadoresBundle:Mantenimiento')->findOneBy(array('nvehiculoid' => $sql_result[0]['vehiculo']));
        if ($ultimo_mant) {
            $ultimo_mant->setUltimo(true);
        }
        try {
            $em->persist($ultimo_mant);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Mantenimiento eliminado con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function loadTipoMantAction(Request $request)
    {
        $vehiculoid = $request->get('vehiculoid');

        /**@var Vehiculo $vehiculo */
        $vehiculo = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:Vehiculo')->findOneBy(array('id' => $vehiculoid));
        $entities = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:Norma')->findBy(array('marca' => $vehiculo->getNmodeloid()->getMarcaVehiculoid()->getId()));
        $_data = array();
        foreach ($entities as $entity) {
            /**@var Norma $entity */
            if (intval($entity->getCantHoras()) > 0) {
                $_data[] = array(
                    'id' => $entity->getTipoMantenimiento()->getId(),
                    'nombre' => $entity->getTipoMantenimiento()->getNombre()
                );
            }

        }
        return new JsonResponse(array('rows' => $_data));
    }

    public function addNotMantAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $_user_unidad = $this->getUser()->getUnidad()->getId();
        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($_user_unidad), $_unidades);

        $entities = $em->getRepository('PortadoresBundle:Vehiculo')->buscarVehiculo('', '', $_unidades);

        if ($entities) {
            foreach ($entities as $entity) {
                $idvehiculo = $entity->getId();
                $matricula = $entity->getMatricula();
                $sql = "select max(v.matricula) as matricula,
                           max(rc.id) as registroid,
                           max(rc.fecha) as fecha,
                           v.id as vehiculoid,
                           max(rca.km) as km

                    from datos.registro_combustible as rc
                    join nomencladores.vehiculo as v on v.id = rc.vehiculoid
                    join datos.registro_combustible_analisis as rca on rca.registro_combustible_id = rc.id
                    join  (SELECT max(rca1.numerosemana) AS numerosemana,
                                  max(rc1.fecha) as fecha,
                                  rc1.id
                           FROM datos.registro_combustible_analisis rca1
                           join datos.registro_combustible as rc1 on rc1.id = rca1.registro_combustible_id
                           GROUP BY rc1.id) as semanalast ON semanalast.id::text = rc.id::text 
                    where rca.conceptoid = '4' and v.id = '$idvehiculo'
                    group by v.id";
                $sql_result = $this->getDoctrine()->getConnection()->fetchAll($sql);

                $sql2 = "select tm.id as tm_id,
                            tm.nombre, 
                            n.cant_horas as km_to_mant

                    from nomencladores.vehiculo as v
                    inner join nomencladores.modelo_vehiculo as mv  on v.nmodeloid = mv.id
                    inner join nomencladores.marca_vehiculo as m on m.id = mv.nmarca_vehiculoid
                    inner join nomencladores.norma as n on n.marca = m.id
                    inner join nomencladores.tipo_mantenimiento as tm on n.tipo_mantenimiento = tm.id
                               
                    where v.id = '$idvehiculo'";
                $sql_result2 = $this->getDoctrine()->getConnection()->fetchAll($sql2);

                $sql3 = "select max(m.fecha) as fecha,
                            max(m.nvehiculoid) as vehiculo,
                            max(m.kilometraje)as kilometraje
                
                from datos.mantenimiento as m
                join nomencladores.vehiculo as v on v.id = m.nvehiculoid
                where v.id = '$idvehiculo'
                group by v.id";
                $sql_result3 = $this->getDoctrine()->getConnection()->fetchAll($sql3);
                if (count($sql_result) > 0) {
                    foreach ($sql_result2 as $item2) {
                        if ($item2['km_to_mant'] > 0) {
                            $tonext = (count($sql_result3) > 0) ? intval($sql_result3[0]['kilometraje']) + intval($item2['km_to_mant']) : 0;
                            $dif = ($tonext !== 0) ? abs($tonext - intval($sql_result[0]['km'])) : 0;

                            if ($dif <= 100) {
                                $not = "select * from admin.notificacion as n
                                        where n.mensaje like '%$matricula%' and n.fecha_aceptacion is null";
                                $sql_not = $this->getDoctrine()->getConnection()->fetchAll($not);
                                if (count($sql_not) === 0) {
                                    $notnew = new Notificacion();
                                    $notnew->setFechaCreacion(new \DateTime());
                                    $notnew->setMensaje('El vehículo ' . $matricula . ' esta próximo a mantenimiento. Por favor verifique');
                                    $notnew->setTipo(Constants::NOTIFICACION_USUARIO);
                                    $em->persist($entity);
                                }
                            }
                        }
                    }
                }
            }
        }
        try {
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Notificación adicionada con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }
}