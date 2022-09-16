<?php
namespace Geocuba\PortadoresBundle\Controller;

use Doctrine\ORM\EntityManager;
use Exception;
use Geocuba\AdminBundle\Repository\QueryHelper;
use Geocuba\PortadoresBundle\Entity\MarcaTecnologica;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MarcaTecnologicaController extends Controller
{
    use ViewActionTrait;

    /**
     * @param $request
     * @return JsonResponse
     */
    public function loadAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $_nombre = trim($request->get('nombre'));

        $start = $request->get('start');
        $limit = $request->get('limit');

        $entities = $em->getRepository('PortadoresBundle:MarcaTecnologica')->findAllBy($_nombre, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:MarcaTecnologica')->findAllBy($_nombre, $start, $limit, true);

        $_data = array_map(static function($entity){
            /** @var MarcaTecnologica $entity */
            return [
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre()
            ];
        }, $entities);

        return new JsonResponse(array('rows' => $_data, 'total' => $total));
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function addAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $nombre = trim($request->get('nombre'));
        $marca_er = $em->getRepository('PortadoresBundle:MarcaTecnologica');

        $duplicates = QueryHelper::findByFieldValue($marca_er, 'nombre', $nombre, null, null, 'id', null);
        if ($duplicates) return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'La marca de vehículo ya existe.'));

        $entity = new MarcaTecnologica();
        $entity->setNombre($nombre);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Marca tecnológica adicionada con éxito.'));
        } catch (Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function updAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $id = trim($request->get('id'));
        $nombre = trim($request->get('nombre'));

        $duplicates = $em->getRepository('PortadoresBundle:MarcaTecnologica')->duplicates($nombre, $id);
        if ($duplicates) return new JsonResponse(['success' => false, 'cls' => 'danger', 'message' => 'La marca de vehículo ya existe.']);

        $entity = $em->getRepository('PortadoresBundle:MarcaTecnologica')->find($id);
        if (!$entity) return new JsonResponse(['success' => false, 'message' => 'La marca no se encuentra disponible.']);

        $entity->setNombre($nombre);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(['success' => true, 'message' => 'Marca tecnológica modificada con éxito.']);
        } catch (Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    /**
     * @param $request
     * @return JsonResponse
     * @throws HttpException
     */
    public function delAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $marca_er = $em->getRepository('PortadoresBundle:MarcaTecnologica');

        try {
            $em->transactional(static function ($em) use ($request, $marca_er) {
                /** @var EntityManager $em */

                foreach ($request->request->get('ids') as $marca_id) {
                    $marca = $marca_er->find($marca_id);

                    if (!$marca) throw new NotFoundHttpException(sprintf('No existe la Marca con identificador <strong>%s</strong>', $marca_id));

                    $em->remove($marca);
                }
            });
            $em->clear();
        } catch (Exception $e) {
            $em->clear();

            if (strpos($e->getMessage(), 'SQLSTATE[23503]')) {
                throw new HttpException(500, sprintf('Una marca no se puede eliminar porque se encuentra en uso'));
            }

            if ($e instanceof HttpException) {
                return new JsonResponse(['success' => false, 'message' => $e->getMessage()]);
            }

            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), $e);
        }

        return new JsonResponse(['success' => true, 'message' => count($request->request->get('ids')) === 1 ? 'Marca eliminada con éxito' : 'Marcas eliminadas con éxito']);

    }
}