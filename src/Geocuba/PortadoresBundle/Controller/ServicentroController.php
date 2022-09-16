<?php
/**
 * Created by PhpStorm.
 * User: adonis
 * Date: 23/09/2015
 * Time: 11:05 AM
 */
namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\PortadoresBundle\Entity\Servicentro;
use Doctrine\Common\CommonException;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ServicentroController extends Controller
{
    // See below how it is used.
    const FLUSH_THRESHOLD = 100;

    use ViewActionTrait;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $_nombre = trim($request->get('nombre'));

        $start = $request->get('start');
        $limit = $request->get('limit');

        $entities = $em->getRepository('PortadoresBundle:Servicentro')->buscarServicentro($_nombre, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:Servicentro')->buscarServicentro($_nombre, $start, $limit, true);

        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre()
            );
        }

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $nombre = trim($request->get('nombre'));

        $repetido = $em->getRepository('PortadoresBundle:Servicentro')->buscarServicentroRepetido($nombre);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe un servicentro con el mismo nombre.'));

        $entity = new Servicentro();
        $entity->setNombre($nombre);
        $entity->setVisible(true);
        try {
            $em->persist($entity);
            $em->flush();
            return new Response(json_encode(array('success' => true, 'cls' => 'success', 'message' => 'Servicentro adicionado con éxito.'), JSON_UNESCAPED_UNICODE));
        } catch (CommonException $ex) {
            return new Response(json_encode(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'), JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function modAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $nombre = trim($request->get('nombre'));

        $repetido = $em->getRepository('PortadoresBundle:Servicentro')->buscarServicentroRepetido($nombre,$id);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe un servicentro con el mismo nombre.'));

        $entity = $em->getRepository('PortadoresBundle:Servicentro')->find($id);
        $entity->setNombre($nombre);
        try {
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Servicentro modificado con éxito.'));
            return $response;
        } catch (CommonException $ex) {
            return new Response(json_encode(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'), JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function delAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $servicentro_er = $em->getRepository('PortadoresBundle:Servicentro');

        try {
            $em->transactional(function () use ($request, $servicentro_er) {
                /** @var EntityManager $em */
                $em = func_get_arg(0);

                foreach ($request->request->get('ids') as $servicentro_id) {

                    $servicentro = $servicentro_er->find($servicentro_id);

                    if (!$servicentro) {
                        throw new NotFoundHttpException(sprintf('No existe el Servicentro con identificador <strong>%s</strong>', $servicentro_id));
                    }

                    $em->persist(
                        $servicentro->setVisible(false)
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

        return new JsonResponse(['success' => true, 'message' => count($request->request->get('ids')) === 1 ? 'Servicentro eliminado con éxito': 'Servicentros eliminados con éxito']);

    }
}