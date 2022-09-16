<?php
/**
 * Created by PhpStorm.
 * User: Marielis
 * Date: 11/21/2020
 * Time: 11:30 a.m.
 */

namespace Geocuba\SoporteBundle\Controller;


use Geocuba\PortadoresBundle\Entity\ActaRespMaterial;
use Geocuba\PortadoresBundle\Entity\CuentaGasto;
use Geocuba\PortadoresBundle\Entity\HistorialTarjeta;
use Geocuba\PortadoresBundle\Entity\Liquidacion;
use Geocuba\PortadoresBundle\Entity\Anticipo;
use Geocuba\PortadoresBundle\Entity\Consecutivos;
use Doctrine\Common\CommonException;
use Doctrine\Common\Util\Debug;
use Geocuba\PortadoresBundle\Entity\Tarjeta;
use Geocuba\PortadoresBundle\Entity\Vehiculo;
use Geocuba\PortadoresBundle\Util\FechaUtil;
use Geocuba\Utils\Functions;
use Geocuba\Utils\ViewActionTrait;
use Geocuba\PortadoresBundle\Util\Datos;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Geocuba\PortadoresBundle\Util\DocumentosEnum;
use Symfony\Component\HttpKernel\Exception\{
    HttpException, NotFoundHttpException
};
use Geocuba\PortadoresBundle\Util\Utiles;

class AnticipoEventosController extends Controller
{
    use ViewActionTrait;


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function abrirAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $anticipo = $request->get('anticipo');


        $entity = $em->getRepository('PortadoresBundle:Anticipo')->findOneBy(array(
            'id' => $anticipo,
        ));

        $entity->setAbierto(true);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Anticipo abierto con éxito.'));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }
}