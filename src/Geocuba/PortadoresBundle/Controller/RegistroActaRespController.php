<?php
/**
 * Created by PhpStorm.
 * User: javier
 * Date: 17/05/2016
 * Time: 15:46
 */

namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\PortadoresBundle\Entity\ActaRespMaterial;
use Geocuba\PortadoresBundle\Entity\Tarjeta;
use Doctrine\Common\CommonException;
use Doctrine\Common\Util\Debug;
use Geocuba\PortadoresBundle\Entity\Unidad;
use Geocuba\Utils\ViewActionTrait;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Geocuba\PortadoresBundle\Util\Utiles;


class RegistroActaRespController extends Controller
{
    use ViewActionTrait;

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loadAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();

        $mes = $request->get('mes');
        $anno = $request->get('anno');

        $nunidadid = $request->get('unidadid');
        $_chofer = trim($request->get('chofer'));
        $_tarjeta = trim($request->get('tarjeta'));

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $start = $request->get('start');
        $limit = $request->get('limit');

        $entities = $em->getRepository('PortadoresBundle:ActaRespMaterial')->buscarActaResponsabilidadMaterial($_unidades, $_tarjeta, $_chofer, $mes, $anno, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:ActaRespMaterial')->buscarActaResponsabilidadMaterial($_unidades, $_tarjeta, $_chofer, $mes, $anno, $start, $limit, true);

        $_data = array();
        /** @var ActaRespMaterial $entity */
        foreach ($entities as $entity) {
            $array_tarjetas = explode(',', $entity->getTarjeta());
            $nro_tarjeta = '';
            for ($i = 0, $iMax = \count($array_tarjetas); $i < $iMax; $i++) {
                /** @var Tarjeta $tar */
                $tar = $em->getRepository('PortadoresBundle:Tarjeta')->find($array_tarjetas[$i]);
                if (\count($array_tarjetas) - 1 == $i) {
                    $nro_tarjeta .= $tar->getNroTarjeta();
                } else {
                    $nro_tarjeta .= $tar->getNroTarjeta() . ', ';
                }
            }

            $_data[] = array(
                'id' => $entity->getId(),
                'fecha' => date_format($entity->getFecha(), 'd/m/Y'),
                'nunidadid' => $entity->getUnidadid()->getId(),
                'nombreunidadid' => $entity->getUnidadid()->getNombre(),
                'tarjetaid' => $entity->getTarjeta(),
                'tarjeta' => $nro_tarjeta,
                'entregaid' => $entity->getEntregaid()->getId(),
                'entrega' => $entity->getEntregaid()->getNombre(),
                'recibeid' => $entity->getRecibeid()->getId(),
                'recibe' => $entity->getRecibeid()->getNombre()
            );
        }
//var_dump($_data);die;
        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addAction(Request $request): ?JsonResponse
    {
        $em = $this->getDoctrine()->getManager();


        $entregaid = $request->get('entregaid');
        $recibeid = $request->get('recibeid');
        $tarjetaid = $request->get('tarjetaid');
        $nunidadid = $request->get('nunidadid');

        $entrega = $em->getRepository('PortadoresBundle:Persona')->find($entregaid);
        $recibe = $em->getRepository('PortadoresBundle:Persona')->find($recibeid);

        $tarjeta = implode(',', $tarjetaid);

        $_date = str_replace('/', '-', $request->request->get('fecha'));
        $fecha = new \DateTime($_date);

        $mes = $fecha->format('n');
        $anno = $fecha->format('Y');


        $fecha = new \DateTime($fecha->format('Y-m-d H:i'));


        $entity = new ActaRespMaterial();
        $entity->setFecha($fecha);
        $entity->setMes($mes);
        $entity->setAnno($anno);
        $entity->setEntregaid($entrega);
        $entity->setRecibeid($recibe);
        $entity->setTarjeta($tarjeta);
        $entity->setUnidadid($em->getRepository('PortadoresBundle:Unidad')->find($nunidadid));
        $entity->setVisible(true);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Acta de Responsabilidad adicionada con éxito.'));
        } catch (\Exception $ex) {
            if ($ex instanceof HttpException) {
                return new JsonResponse(['success' => false, 'message' => $ex->getMessage()]);
            } else {
                throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $ex->getMessage(), $ex);
            }
        }
    }

    /*** @param Request $request
     * @return JsonResponse
     */
    public function modAction(Request $request): ?JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $entregaid = $request->get('entregaid');
        $recibeid = $request->get('recibeid');
        $tarjetaid = $request->get('tarjetaid');

        $entrega = $em->getRepository('PortadoresBundle:Persona')->find($entregaid);
        $recibe = $em->getRepository('PortadoresBundle:Persona')->find($recibeid);

        $tarjeta = implode(',', $tarjetaid);

        $_date = str_replace('/', '-', $request->request->get('fecha'));
        $fecha = new \DateTime($_date);
        $fecha = new \DateTime($fecha->format('Y-m-d H:i'));

        $entity = $em->getRepository('PortadoresBundle:ActaRespMaterial')->find($id);
        $entity->setFecha($fecha);
        $entity->setEntregaid($entrega);
        $entity->setRecibeid($recibe);
        $entity->setTarjeta($tarjeta);
        try {
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Acta de Responsabilidad modificada con éxito.'));
            return $response;
        } catch (\Exception $ex) {
            if ($ex instanceof HttpException) {
                return new JsonResponse(['success' => false, 'message' => $ex->getMessage()]);
            } else {
                throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $ex->getMessage(), $ex);
            }
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function delAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $entity = $em->getRepository('PortadoresBundle:ActaRespMaterial')->find($id);
        $entity->setVisible(false);
//        $em->remove($entity);
        try {
            //$em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Acta de Responsabilidad eliminada con éxito.'));
        } catch (\Exception $ex) {
            if ($ex instanceof HttpException) {
                return new JsonResponse(['success' => false, 'message' => $ex->getMessage()]);
            } else {
                throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $ex->getMessage(), $ex);
            }
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $acta_resp_er = $em->getRepository('PortadoresBundle:ActaRespMaterial');

        try {
            $em->transactional(function () use ($request, $acta_resp_er) {
                /** @var EntityManager $em */
                $em = func_get_arg(0);

                foreach ($request->request->get('ids') as $acta_resp_id) {

                    $respon_acta = $acta_resp_er->find($acta_resp_id);

                    if (!$respon_acta) {
                        throw new NotFoundHttpException(sprintf('No existe la Responsabilidad con identificador <strong>%s</strong>', $acta_resp_id));
                    }

                    $em->persist(
                        $respon_acta->setVisible(false)
                    );
                }
            });

            $em->clear();
        } catch (\Exception $e) {
            $em->clear();

            if ($e instanceof HttpException) {
                return new JsonResponse(['success' => false, 'message' => $e->getMessage()]);
            } else {
                throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), $e);
            }
        }

        return new JsonResponse(['success' => true, 'message' => count($request->request->get('ids')) === 1 ? 'La acta de responsabilidad material ha sido eliminada' : 'Las actas de responsabilidad material han sido eliminadas']);

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function printAction(Request $request): JsonResponse
    {
        $conn = $this->get('database_connection');
        $_user = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');

        $acta = $em->getRepository('PortadoresBundle:ActaRespMaterial')->find($id);
        $dia = date_format($acta->getFecha(), 'd');
        $mes = date_format($acta->getFecha(), 'n');
        $anno = date_format($acta->getFecha(), 'Y');

        $meses = array('Año', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

        $array_tarjeta = explode(',', $acta->getTarjeta());
        $tarjeta_vehiculo = array();
        $tarjetas = '';
        for ($i = 0, $iMax = \count($array_tarjeta); $i < $iMax; $i++) {
            $tar = $em->getRepository('PortadoresBundle:Tarjeta')->find($array_tarjeta[$i]);
            if (\count($array_tarjeta) - 1 == $i) {
                $tarjetas .= 'No. ' . $tar->getNroTarjeta() . ' de combustible ' . $tar->getTipoCombustibleid()->getNombre() . ' en CUP';
            } else {
                $tarjetas .= 'No. ' . $tar->getNroTarjeta() . ' de combustible ' . $tar->getTipoCombustibleid()->getNombre() . ' en CUP , ';
            }

            $tarjeta_vehiculo = array_merge($tarjeta_vehiculo, $em->getRepository('PortadoresBundle:TarjetaVehiculo')->findBy(array('ntarjetaid' => $array_tarjeta[$i])));
        }
        $s = \count($array_tarjeta) > 1 ? 's' : '';

        $vehiculos = '';
        if (\count($tarjeta_vehiculo) == 1) {
            $vehiculos = ', del veh&iacute;culo: ';
        } elseif (\count($tarjeta_vehiculo) > 1) {
            $vehiculos = ', de los veh&iacute;culos: ';
        }
        foreach ($tarjeta_vehiculo as $tv) {
            if (strpos($vehiculos , $tv->getVehiculoid()->getMatricula() ) == false)
              $vehiculos .= $tv->getVehiculoid()->getMatricula() . ', ';
        }
        $vehiculos = rtrim($vehiculos, ', ');


        //ESTO ES PARA TENER LAS RESPONSABILIDADES QUE SE DEBEN CUMPLIR//
        $_user = $this->get('security.token_storage')->getToken()->getUser();

        $rol = $_user->getCargo();
        $_super_administrador = false;

        if ($rol == "super administrador") {
            $_super_administrador = true;
        }


        $_data = array();

        $entities = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:ResponsabilidadActaMaterial')->findByVisible(true);
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre(),
            );
        }

        //AQUI TERMINA LA BUSQUEDAD DE RESPONSABILIDADES//

        $_html = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
        <html xmlns='http://www.w3.org/1999/xhtml'>
        <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <title>Acta de Responsabilidad Material</title>
        <style>
            table {
                border:0 solid;
                border-radius:0;
                font-family: 'Arial', serif;
                font-size: 12px;
                border-collapse: collapse;
            }
            td{
                height: 10px;
                padding: 2px;
            }
        </style>
        </head>

        <body>
        <header>
            <img  src='../../assets/img/PNG/logo.png' height='60px' width='160px'>
        </header>
        <table cellspacing='0' cellpadding='5' border='0' width='100%'>            
            <tr>
                <td style='text-align: center;border: none; font-size: 16px;'><strong>Acta de Responsabilidad Material</strong></td>
            </tr>

          <tr>
            <td style='border: none;'><p style='text-align: justify'>Yo " . $acta->getRecibeid()->getNombre() . ' con carnet de identidad
            ' . $acta->getRecibeid()->getCi() . " he sido impuesto de la responsabilidad material y obligaciones
            en la preservación de la tarjeta magnética de combustible estipulado en la Orden no.7/08 del Ministro 
            de las FAR para aprobar las normas y procedimientos sobre responsabilidad material de la actividad presupuestada
            de las FAR y su Sistema Empresarial incluido el Grupo de Administracion Empresarial y sus dependencias 
            atendidas por este, de fecha 25 de junio del 2008.</p></td>
          </tr>
          <tr>
            <td style='border: none;margin-top: 10px;'><strong>Además, me responsabilizo del control de consumo y los saldos existentes en la tarjeta y de cumplir las siguientes responsabilidades:</strong></td>
          </tr>";

        $_html .= "<tr><td style='border: none;'>";
        for ($i = 1, $iMax = \count($_data); $i <= $iMax; $i++) {
            $var = '- ';
            $_html .= "
            <p style='text-align: justify;padding-left: 15px;'> " . "<strong>$i$var</strong>" . $_data[$i - 1]['nombre'] . ' </p>
           ';
        }
        $_html .= '</td></tr>';

        $cargoRecibe = null == $acta->getRecibeid()->getCargoid() ? '' : $acta->getRecibeid()->getCargoid()->getNombre();
        $cargoEntrega = \is_null($acta->getEntregaid()->getCargoid()) ? '' : $acta->getEntregaid()->getCargoid()->getNombre();
        $_html .= "<tr>
            <td style='border: none;'>Y como constancia se firma la presente a los " . $dia . ' días del mes ' . $meses[$mes] . ' de ' . $anno . '</td>
          </tr>
          ';

        $pieFirma = "<table cellspacing='0' cellpadding='5' border='0' width='100%' style='margin-top: 10px;'>";

        $pieFirma .= "<tr>";
        $pieFirma .= "<td style='text-align: center; border: none;'><strong>Receptor:</strong></td>";
        $pieFirma .= "<td style='text-align: center; border: none;'><strong>Jefe de Entrega:</strong></td>";
        $pieFirma .= "</tr>";

        $pieFirma .= '<tr>';
        $pieFirma .= "<td style='text-align: center; border: none;'>__________________________</td>";
        $pieFirma .= "<td style='text-align: center; border: none;'>__________________________</td>";
        $pieFirma .= '</tr>';

        $pieFirma .= '<tr>';
        $pieFirma .= "<td style='text-align: center; border: none;'>" . $acta->getRecibeid()->getNombre() . "</td>";
        $pieFirma .= "<td style='text-align: center; border: none;'>" . $acta->getEntregaid()->getNombre() . "</td>";
        $pieFirma .= '</tr>';

        $pieFirma .= '<tr>';
        $pieFirma .= "<td style='text-align: center; border: none;'>$cargoRecibe</td>";
        $pieFirma .= "<td style='text-align: center; border: none;'>$cargoEntrega</td>";
        $pieFirma .= '</tr>';

        $pieFirma .= '</table>';

        $_html .= "<tr>
                <td style='text-align: left;border: none;'>$pieFirma</td>
            </tr>";

        $_html .= '</table>
        </body>
        </html>';

        return new JsonResponse(array('success' => true, 'html' => $_html));
    }

} 