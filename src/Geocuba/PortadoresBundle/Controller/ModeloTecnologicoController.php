<?php
namespace Geocuba\PortadoresBundle\Controller;

use Doctrine\ORM\EntityManager;
use Exception;
use Geocuba\PortadoresBundle\Entity\ModeloTecnologico;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ModeloTecnologicoController extends Controller
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
        $_marcaId = trim($request->get('marca_id'));

        $start = $request->get('start');
        $limit = $request->get('limit');

        $entities = $em->getRepository('PortadoresBundle:ModeloTecnologico')->findAllBy($_nombre, $_marcaId, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:ModeloTecnologico')->findAllBy($_nombre, $_marcaId, $start, $limit, true);

        $_data = array_map(static function($entity){
            /** @var ModeloTecnologico $entity */
            return [
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre(),
                'marca_tecnologica_id' => $entity->getMarcaTecnologica()->getId(),
                'marca_tecnologica_nombre' => $entity->getMarcaTecnologica()->getNombre()
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
        $marcaId = trim($request->get('marca_id'));
        $nombre = trim($request->get('nombre'));
        $modelo_er = $em->getRepository('PortadoresBundle:ModeloTecnologico');

        $duplicates = $modelo_er->duplicates($nombre, $marcaId);
        if ($duplicates) return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El modelo a insertar ya existe.'));

        $entity = new ModeloTecnologico();
        $entity->setNombre($nombre);
        $entity->setMarcaTecnologica($em->getRepository('PortadoresBundle:MarcaTecnologica')->find($marcaId));

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Modelo tecnológico adicionado con éxito.'));
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
        $modelo_er = $em->getRepository('PortadoresBundle:ModeloTecnologico');
        $id = $request->get('id');
        $marcaId = $request->get('marca_id');
        $nombre = trim($request->get('nombre'));

        $duplicates = $modelo_er->duplicates($nombre, $marcaId, $id);
        if ($duplicates) return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'El modelo a insertar ya existe.'));

        $entity = $em->getRepository('PortadoresBundle:ModeloTecnologico')->find($id);
        if (!$entity) return new JsonResponse(['success' => false, 'message' => 'El modelo no se encuentra disponible.']);

        $entity->setNombre($nombre);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(['success' => true, 'message' => 'Modelo tecnológico modificado con éxito.']);
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
        $modelo_er = $em->getRepository('PortadoresBundle:ModeloTecnologico');

        try {
            $em->transactional(static function ($em) use ($request, $modelo_er) {
                /** @var EntityManager $em */

                foreach ($request->request->get('ids') as $modelo_id) {
                    $modelo = $modelo_er->find($modelo_id);

                    if (!$modelo) throw new NotFoundHttpException(sprintf('No existe el modelo con identificador <strong>%s</strong>', $modelo_id));

                    $em->remove($modelo);
                }
            });
            $em->clear();
        } catch (Exception $e) {
            $em->clear();

            if (strpos($e->getMessage(), 'SQLSTATE[23503]')) {
                throw new HttpException(500, sprintf('Un modelo no se puede eliminar porque se encuentra en uso'));
            }

            if ($e instanceof HttpException) {
                return new JsonResponse(['success' => false, 'message' => $e->getMessage()]);
            }

            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), $e);
        }

        return new JsonResponse(['success' => true, 'message' => count($request->request->get('ids')) === 1 ? 'Modelos eliminado con éxito' : 'Modelos eliminados con éxito']);

    }
}