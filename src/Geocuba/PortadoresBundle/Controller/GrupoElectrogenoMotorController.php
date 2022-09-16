<?php

namespace Geocuba\PortadoresBundle\Controller;

use Doctrine\Common\Util\Debug;
use Exception;
use Geocuba\AdminBundle\Repository\QueryHelper;
use Geocuba\PortadoresBundle\Entity\GrupoElectrogenoMotor;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GrupoElectrogenoMotorController extends Controller
{
    use ViewActionTrait;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loadAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $_noSerie = trim($request->get('no_serie'));

        $start = $request->get('start');
        $limit = $request->get('limit');

        $entities = $em->getRepository('PortadoresBundle:GrupoElectrogenoMotor')->findAllBy($_noSerie, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:GrupoElectrogenoMotor')->findAllBy($_noSerie, $start, $limit, true);

        $_data = array_map(static function($entity) {
            /** @var GrupoElectrogenoMotor $entity */
            return [
                'id' => $entity->getId(),
                'marca_id' => $entity->getModelo()->getMarcaTecnologica()->getId(),
                'marca_nombre' => $entity->getModelo()->getMarcaTecnologica()->getNombre(),
                'modelo_id' => $entity->getModelo()->getId(),
                'modelo_nombre' => $entity->getModelo()->getNombre(),
                'no_serie' => $entity->getNoSerie(),
                'hp' => $entity->getHp(),
                'rpm' => $entity->getRpm(),
            ];
        }, $entities);

        return new JsonResponse(['success' => true, 'rows' => $_data, 'total' => $total]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $noSerie = trim($request->get('no_serie'));
        $modeloId = trim($request->get('modelo_id'));
        $hp = $request->get('hp');
        $rpm = $request->get('rpm');

        $motor_er = $em->getRepository('PortadoresBundle:GrupoElectrogenoMotor');
        $duplicates = QueryHelper::findByFieldValue($motor_er, 'noSerie', $noSerie, null, null, null, null);
        if ($duplicates) return new JsonResponse(['success' => false, 'message' => 'Ya existe un motor con ese número de serie.']);

        $entity = new GrupoElectrogenoMotor();
        $entity->setNoSerie($noSerie);
        $entity->setModelo($em->find('PortadoresBundle:ModeloTecnologico', $modeloId));
        $entity->setHp($hp);
        $entity->setRpm($rpm);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Motor adicionado con éxito.'));
        } catch (Exception $ex) {
            return new JsonResponse(array('success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.'));
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $motor_er = $em->getRepository('PortadoresBundle:GrupoElectrogenoMotor');

        $id = $request->get('id');
        $noSerie = trim($request->get('no_serie'));
        $modeloId = trim($request->get('modelo_id'));
        $hp = $request->get('hp');
        $rpm = $request->get('rpm');

        $entity = $motor_er->find($id);
        if (!$entity) return new JsonResponse(['success' => false, 'message' => 'El motor no se encuentra disponible']);

//        $duplicates = QueryHelper::findByFieldValue($motor_er, 'noSerie', $noSerie, null, null, 'id', $id);
//        if ($duplicates) return new JsonResponse(['success' => false, 'message' => 'Ya existe un motor con ese número de serie.']);

        $entity->setNoSerie($noSerie);
        $entity->setModelo($em->find('PortadoresBundle:ModeloTecnologico', $modeloId));
        $entity->setHp($hp);
        $entity->setRpm($rpm);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(['success' => true, 'cls' => 'success', 'message' => 'Motor modificado con éxito.']);
        } catch (Exception $ex) {
            return new JsonResponse(['success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.']);
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

        $entity = $em->getRepository('PortadoresBundle:GrupoElectrogenoMotor')->find($id);
        if (!$entity) return new JsonResponse(['success' => false, 'message' => 'El motor no se encuentra disponible']);

        $em->remove($entity);
        try {
            $em->flush();
            return new JsonResponse(['success' => true, 'cls' => 'success', 'message' => 'Motor eliminado con éxito.']);
        } catch (Exception $ex) {
            if (strpos($ex->getMessage(), 'SQLSTATE[23503]')) {
                throw new HttpException(500, sprintf('El motor %s no se puede eliminar porque se encuentra en uso', $entity->getNoSerie()));
            }
            return new JsonResponse(['success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.']);
        }
    }
}