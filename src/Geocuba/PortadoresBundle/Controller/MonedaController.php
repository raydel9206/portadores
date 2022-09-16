<?php
/**
 * Created by PhpStorm.
 * User: yosley
 * Date: 06/10/2015
 * Time: 15:41
 */


namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\PortadoresBundle\Entity\Moneda;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class MonedaController extends Controller
{
    // See below how it is used.
    const FLUSH_THRESHOLD = 100;

    use ViewActionTrait;

    public function loadAction()
    {
        $entities = $this->getDoctrine()->getManager()->getRepository('PortadoresBundle:Moneda')->findByVisible(true);
        $_data = array();
        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre(),
                'unica' => $entity->getUnica()

            );
        }
        return new JsonResponse(array('rows' => $_data, 'total' => count($_data)));
    }

    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $nombre = trim($request->get('nombre'));
        $unica = trim($request->get('unica'));

        $repetido = $em->getRepository('PortadoresBundle:Moneda')->buscarMonedaRepetido($nombre);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe una moneda del mismo tipo.'));

        $entity = new Moneda();
        $entity->setNombre($nombre);
        if ($unica)
            $entity->setUnica(true);
        else
            $entity->setUnica(false);
        $entity->setVisible(true);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Tipo de moneda  adicionada con éxito.'));
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function modAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $nombre = trim($request->get('nombre'));
        $unica = trim($request->get('unica'));

        $repetido = $em->getRepository('PortadoresBundle:Moneda')->buscarMonedaRepetido($nombre, $id);
        if ($repetido > 0)
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Ya existe una moneda del mismo tipo.'));

        $entity = $em->getRepository('PortadoresBundle:Moneda')->find($id);

        $entity->setNombre($nombre);
        if ($unica) {
            $entity->setUnica(true);

        } else {
            $entity->setUnica(false);
        }

        try {
            $em->persist($entity);
            $em->flush();
            $response = new JsonResponse();
            $response->setData(array('success' => true, 'cls' => 'success', 'message' => 'Tipo de moneda  modificada con éxito.'));
            return $response;
        } catch (CommonException $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    public function delAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $moneda_er = $em->getRepository('PortadoresBundle:Moneda');

        try {
            $em->transactional(function () use ($request, $moneda_er) {
                /** @var EntityManager $em */
                $em = func_get_arg(0);

                foreach ($request->request->get('ids') as $moneda_id) {

                    $moneda = $moneda_er->find($moneda_id);

                    if (!$moneda) {
                        throw new NotFoundHttpException(sprintf('No existe el tipo de moneda con identificador <strong>%s</strong>', $moneda_id));
                    }

                    $em->persist(
                        $moneda->setVisible(false)
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

        return new JsonResponse(['success' => true, 'message' => count($request->request->get('ids')) === 1 ? 'Tipo de Moneda eliminado con éxito' : 'Tipos de Moneda eliminados con éxito']);
    }
}