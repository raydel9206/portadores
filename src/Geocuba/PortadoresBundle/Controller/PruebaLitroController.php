<?php
/**
 * Created by PhpStorm.
 * User: Yosley
 * Date: 5/11/15
 * Time: 15:42
 */

namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\PortadoresBundle\Entity\PruebaLitro;
use Doctrine\Common\CommonException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Geocuba\AdminBundle\Util\Util;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Utiles;

class PruebaLitroController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $_nombre = trim($request->get('nombre'));
        $start = $request->get('start');
        $limit = $request->get('limit');
        $_data = array();
        $nunidadid = $request->get('nunidadid');

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $qb = $em->createQueryBuilder();
        $qb->select('pruebalitro')
            ->from('PortadoresBundle:PruebaLitro', 'pruebalitro')
            ->innerJoin('pruebalitro.nvehiculoid', 'vehiculo')
            ->innerJoin('vehiculo.ndenominacionVehiculoid', 'denominacion')
            ->Where($qb->expr()->in('vehiculo.nunidadid', $_unidades));
        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('clearstr(vehiculo.matricula) like clearstr(:nombre)')
                ->setParameter('nombre', "%$_nombre%");
        }
        $entities = $qb->orderBy('denominacion.nombre,pruebalitro.fechaPrueba', 'ASC')
            ->getQuery()
            ->setMaxResults($limit)
            ->setFirstResult($start)
            ->getResult();

        $qb = $em->createQueryBuilder();
        $qb->select('count(pruebalitro)')
            ->from('PortadoresBundle:PruebaLitro', 'pruebalitro')
            ->innerJoin('pruebalitro.nvehiculoid', 'vehiculo')
            ->innerJoin('vehiculo.ndenominacionVehiculoid', 'denominacion')
            ->Where($qb->expr()->in('vehiculo.nunidadid', $_unidades));
        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('clearstr(vehiculo.matricula) like clearstr(:nombre)')
                ->setParameter('nombre', "%$_nombre%");
        }
        $total = $qb->getQuery()->getSingleScalarResult();

        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'fecha_prueba' => $entity->getFechaPrueba()->format('d/m/Y'),
                'responsable' => $entity->getResponsable(),
                'indice' => $entity->getIndice(),
                'indice_far' => $entity->getIndiceFar(),
                'nvehiculoid' => $entity->getNvehiculoid()->getId(),
                'nvehiculomatricula' => $entity->getNvehiculoid()->getMatricula(),

            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $fecha_prueba = trim($request->get('fecha_prueba'));
        $responsable = trim($request->get('responsable'));
        $indice = (float)($request->get('indice'));
        $actualizar = trim($request->get('actualizar'));
        $nvehiculoid = trim($request->get('nvehiculoid'));
        $fecha_pruebaA = date_create_from_format('d/m/Y',$fecha_prueba);


        $entity = new PruebaLitro();
        $entity ->setFechaPrueba($fecha_pruebaA);
        $entity->setResponsable($responsable);
        $entity->setIndice($indice);;
        $nvehiculo = $em->getRepository('PortadoresBundle:Vehiculo')->find($nvehiculoid);

        if($actualizar==1 and $indice > 0 )
            $nvehiculo->setNorma($indice);

        $entity->setNvehiculoid($nvehiculo);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Prueba de litro adicionada con éxito.'));
        }
        catch (\Exception $ex){
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function modAction (Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $fecha_prueba = trim($request->get('fecha_prueba'));
        $responsable = trim($request->get('responsable'));
        $indice = trim($request->get('indice'));
        $actualizar = trim($request->get('actualizar'));
        $nvehiculoid = trim($request->get('nvehiculoid'));

        $entity = $em->getRepository('PortadoresBundle:PruebaLitro')->find($id);
        $fecha_pruebaA = date_create_from_format('d/m/Y',$fecha_prueba);

        $entity->setFechaPrueba($fecha_pruebaA);
        $entity->setResponsable($responsable);
        $entity->setIndice($indice);
        $nvehiculo = $em->getRepository('PortadoresBundle:Vehiculo')->find($nvehiculoid);
        if($actualizar==1 and $indice > 0 )
            $nvehiculo->setNorma($indice);

        $entity->setNvehiculoid($nvehiculo);

        try{
            $em->persist($nvehiculo);
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Prueba de litro modificada con éxito.'));
            return $response;
        }
        catch(\Exception $ex){
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function delAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $entity = $em->getRepository('PortadoresBundle:PruebaLitro')->find($id);
        $em->remove($entity);
        try{

            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Prueba de litro eliminada con éxito.'));
        }
        catch(\Exception $ex){
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function recursivoAction($id)
    {

//
        $_data = array();
        $em = $this->getDoctrine()->getManager();
        $hoja = true;
        $id_U = $id;
        $tree = array();

        $entitiess = $em->getRepository('PortadoresBundle:NestructuraUnidades')->findByPadreid($id);
//        $tree[]=$id;
        array_push($tree,$id);

        if ($entitiess) {

            foreach ($entitiess as $entity) {
//                        print_r($entity->getNunidadid()->getNombre());die;
//                $tree[]=$this->recursivoAction($entity->getNunidadid()->getId());
                array_push($tree,$entity->getNunidadid()->getId());
//                print_r($tree);die;
                $this->recursivoAction($entity->getNunidadid()->getId());
//                        $tree[] = array(
//                            'id' => $entity->getNunidadid()->getId(),
//                            'hijos'=>)
//                        );
            }

        }

//        for($i=0;$i<count($tree);$i++)
//        {
//            array_push($tree,$this->recursivoAction($tree[$i]));
//        }



//        print_r($tree);die;


        return $tree;
    }
}