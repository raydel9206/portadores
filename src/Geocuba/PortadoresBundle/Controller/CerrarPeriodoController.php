<?php
/**
 * Created by PhpStorm.
 * User: yosley
 * Date: 08/03/2016
 * Time: 16:21
 */

namespace Geocuba\PortadoresBundle\Controller;

use Doctrine\Common\Util\Debug;
use Geocuba\PortadoresBundle\Entity\CierreMes;
use Doctrine\Common\CommonException;
use Geocuba\Utils\ViewActionTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Geocuba\PortadoresBundle\Util\Utiles;

class CerrarPeriodoController extends Controller
{
    use ViewActionTrait;

    /*** @param Request $request
     * @return JsonResponse
     */
    public function cierreAction(Request $request)
    {
        $session = $request->getSession();
        $mes_abierto = $session->get('selected_month');
        $anno_abierto = $session->get('selected_year');

        $em = $this->getDoctrine()->getManager();

        $_user = $this->get('security.token_storage')->getToken()->getUser();
        $_unidaduser = $_user->getUnidad()->getId();

        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($_user->getUnidad()), $_unidades);
        $unidades_string = $this->unidadesToString($_unidades);

        $conn = $this->get('database_connection');
        $anticipos = $conn->fetchAll("select * from datos.anticipo as a 
                                      inner join nomencladores.tarjeta as t on t.id = a.tarjeta
                                      inner join nomencladores.unidad as u on u.id = t.nunidadid
                                      where
                                      t.nunidadid in ($unidades_string) and a.abierto = TRUE and a.visible = TRUE ");

        if (count($anticipos) > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'No se puede cerrar el período, hay ' . count($anticipos) . ' anticipo(s) abierto(s).'));

        $entity = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:CierreMes')->findOneBy(array('cerrado' => false, 'disponible' => true, 'idunidad' =>$_unidaduser));
        $entity->setCerrado(true);
        $entity->setDisponible(false);


        $cierreMes = new CierreMes();
        $cierreMes->setMes(($mes_abierto == 12) ? 1 : $mes_abierto + 1);
        $cierreMes->setAnno(($mes_abierto == 12) ? $anno_abierto + 1 : $anno_abierto);
        $cierreMes->setCerrado(false);
        $cierreMes->setDisponible(true);
        $cierreMes->setIdunidad($_user->getUnidad());

        try {
            $em->persist($entity);
            $em->persist($cierreMes);
            $em->flush();

            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Cierre realizado con éxito.'));
        } catch (\Exception $ex) {
            if ($ex instanceof HttpException) {
                return new JsonResponse(['success' => false, 'message' => $ex->getMessage()]);
            } else {
                throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $ex->getMessage(), $ex);
            }
        }
    }

    private function unidadesToString($_unidades)
    {
        $_string_unidades = "'" . $_unidades[0] . "'";
        for ($i = 1, $iMax = count($_unidades); $i < $iMax; $i++) {
            $_string_unidades .= ",'" . $_unidades[$i] . "'";
        }
        return $_string_unidades;
    }
}