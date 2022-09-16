<?php
/**
 * Created by PhpStorm.
 * User: yosley
 * Date: 08/03/2016
 * Time: 16:21
 */

namespace Geocuba\PortadoresBundle\Controller;
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

        //validar que no se puede cerrar periodo si existen anticipos abiertos
        $anticipos = $em->getRepository('PortadoresBundle:Anticipo')->findBy(array('abierto' => true,'visible' => true));
        if (count($anticipos) > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'No se puede cerrar el período, existen anticipos abiertos.'));

        $entity = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:CierreMes')->findOneBy(array('cerrado' => false, 'disponible' => true));
        $entity->setCerrado(true);
        $entity->setDisponible(false);

        $_user = $this->get('security.token_storage')->getToken()->getUser();
        $unidadid = $em->getRepository('AdminBundle:UsuarioUnidad')->findOneBy(array('usuario' => $_user->getId()));

        $cierreMes = new CierreMes();
        $cierreMes->setMes(($mes_abierto == 12) ? 1 : $mes_abierto + 1);
        $cierreMes->setAnno(($mes_abierto == 12) ? $anno_abierto + 1 : $anno_abierto);
        $cierreMes->setCerrado(false);
        $cierreMes->setDisponible(true);
        $cierreMes->setIdunidad($em->getRepository('PortadoresBundle:Unidad')->find($unidadid));

        try {
            $em->persist($entity);
            $em->persist($cierreMes);
            $em->flush();

            //si se cierra el mes de diciembre reiniciar la secuancia del numero de orden de trabajo
//            if ($mes_abierto == 12) {
//                $conn = $this->getDoctrine()->getConnection();
//                $conn->fetchAll("SELECT setval('datos.no_orden_seq', 0, true)");
//            }

            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Cierre realizado con éxito.'));
        } catch (\Exception $ex) {
            if ($ex instanceof HttpException) {
                return new JsonResponse(['success' => false, 'message' => $ex->getMessage()]);
            } else {
                throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $ex->getMessage(), $ex);
            }
        }
    }
}