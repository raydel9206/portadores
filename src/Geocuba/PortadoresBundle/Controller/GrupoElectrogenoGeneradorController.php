<?php

namespace Geocuba\PortadoresBundle\Controller;

use Doctrine\Common\Util\Debug;
use Exception;
use Geocuba\AdminBundle\Repository\QueryHelper;
use Geocuba\PortadoresBundle\Entity\GrupoElectrogenoGenerador;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GrupoElectrogenoGeneradorController extends Controller
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

        $entities = $em->getRepository('PortadoresBundle:GrupoElectrogenoGenerador')->findAllBy($_noSerie, $start, $limit);
        $total = $em->getRepository('PortadoresBundle:GrupoElectrogenoGenerador')->findAllBy($_noSerie, $start, $limit, true);

        $_data = array_map(static function($entity) {
            /** @var GrupoElectrogenoGenerador $entity */
            return [
                'id' => $entity->getId(),
                'marca_id' => $entity->getModelo()->getMarcaTecnologica()->getId(),
                'marca_nombre' => $entity->getModelo()->getMarcaTecnologica()->getNombre(),
                'modelo_id' => $entity->getModelo()->getId(),
                'modelo_nombre' => $entity->getModelo()->getNombre(),
                'no_serie' => $entity->getNoSerie(),
                'potencia_kva' => $entity->getPotenciaKva(),
                'potencia_kw' => $entity->getPotenciaKw(),
                'amperaje' => $entity->getAmperaje(),
                'reconexion_voltaje' => $entity->getReconexionVoltaje(),
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
        $potenciakVA = $request->get('potencia_kva');
        $potenciakW = $request->get('potencia_kw');
        $amperaje = $request->get('amperaje');
        $reconexionVoltaje = $request->get('reconexion_voltaje');

        $generador_er = $em->getRepository('PortadoresBundle:GrupoElectrogenoGenerador');
        $duplicates = QueryHelper::findByFieldValue($generador_er, 'noSerie', $noSerie, null, null, null, null);
        if ($duplicates) return new JsonResponse(['success' => false, 'message' => 'Ya existe un generador con ese número de serie.']);

        $entity = new GrupoElectrogenoGenerador();
        $entity->setNoSerie($noSerie);
        $entity->setModelo($em->find('PortadoresBundle:ModeloTecnologico', $modeloId));
        $entity->setPotenciaKva($potenciakVA);
        $entity->setPotenciaKw($potenciakW);
        $entity->setAmperaje($amperaje);
        $entity->setReconexionVoltaje($reconexionVoltaje);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(array('success' => true, 'cls' => 'success', 'message' => 'Generador adicionado con éxito.'));
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
        $generador_er = $em->getRepository('PortadoresBundle:GrupoElectrogenoGenerador');

        $id = $request->get('id');
        $noSerie = trim($request->get('no_serie'));
        $modeloId = trim($request->get('modelo_id'));
        $potenciakVA = $request->get('potencia_kva');
        $potenciakW = $request->get('potencia_kw');
        $amperaje = $request->get('amperaje');
        $reconexionVoltaje = $request->get('reconexion_voltaje');

        $entity = $generador_er->find($id);
        if (!$entity) return new JsonResponse(['success' => false, 'message' => 'El generador no se encuentra disponible']);


//        $duplicates = QueryHelper::findByFieldValue($generador_er, 'noSerie', $noSerie, null, null, 'id', $id);
//        if ($duplicates) return new JsonResponse(['success' => false, 'message' => 'Ya existe un generador con ese número de serie.']);

        $entity->setNoSerie($noSerie);
        $entity->setModelo($em->find('PortadoresBundle:ModeloTecnologico', $modeloId));
        $entity->setPotenciaKva($potenciakVA);
        $entity->setPotenciaKw($potenciakW);
        $entity->setAmperaje($amperaje);
        $entity->setReconexionVoltaje($reconexionVoltaje);
        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(['success' => true, 'cls' => 'success', 'message' => 'Generador modificado con éxito.']);
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

        $entity = $em->getRepository('PortadoresBundle:GrupoElectrogenoGenerador')->find($id);
        if (!$entity) return new JsonResponse(['success' => false, 'message' => 'El generador no se encuentra disponible']);

        $em->remove($entity);
        try {
            $em->flush();
            return new JsonResponse(['success' => true, 'cls' => 'success', 'message' => 'Generador eliminado con éxito.']);
        } catch (Exception $ex) {
            if (strpos($ex->getMessage(), 'SQLSTATE[23503]')) {
                throw new HttpException(500, sprintf('El generador %s no se puede eliminar porque se encuentra en uso', $entity->getNoSerie()));
            }
            return new JsonResponse(['success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.']);
        }
    }
}