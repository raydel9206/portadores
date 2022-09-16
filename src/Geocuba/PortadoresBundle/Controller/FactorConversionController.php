<?php

namespace Geocuba\PortadoresBundle\Controller;

use Exception;
use Geocuba\PortadoresBundle\Entity\FactorConversion;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\Utils\ViewActionTrait;

class FactorConversionController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $start = $request->get('start');
        $limit = $request->get('limit');
        $entities = $em->getRepository('PortadoresBundle:FactorConversion')->findBy([], [], $limit, $start);
        $entitiesTotal = count($em->getRepository('PortadoresBundle:FactorConversion')->findAll());

        $_data = array_map(static function ($entity) {
            /** @var FactorConversion $entity */
            return [
                'id' => $entity->getId(),
                'portador_id' => $entity->getPortador()->getId(),
                'portador_nombre' => $entity->getPortador()->getNombre(),
                'de_um_id' => $entity->getDeUm()->getId(),
                'de_um_nombre' => $entity->getDeUm()->getNombre(),
                'a_um_id' => $entity->getAUm()->getId(),
                'a_um_nombre' => $entity->getAUm()->getNombre(),
                'factor' => $entity->getFactor()
            ];
        }, $entities);

        return new JsonResponse(array('success' => true, 'rows' => $_data, 'total' => $entitiesTotal));
    }

    public function addAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $portadorId = trim($request->get('portador_id'));
        $deUmId = trim($request->get('de_um_id'));
        $aUmId = trim($request->get('a_um_id'));
        $factor = trim($request->get('factor'));

        $validateResult = $this->validate($portadorId, $deUmId, $aUmId);
        if ($validateResult !== null) return $validateResult;

        $entity = new FactorConversion();
        $entity->setPortador($em->getRepository('PortadoresBundle:Portador')->find($portadorId))
            ->setDeUm($em->getRepository('PortadoresBundle:UnidadMedida')->find($deUmId))
            ->setAUm($em->getRepository('PortadoresBundle:UnidadMedida')->find($aUmId))
            ->setFactor($factor);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(['success' => true, 'cls' => 'success', 'message' => 'Factor de conversión adicionado con éxito.']);
        } catch (Exception $ex) {
            return new JsonResponse(['success' => false, 'cls' => 'danger', 'message' => 'Error insertando los datos, si el error persiste contacte a su administrador.']);
        }
    }

    public function updAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $portadorId = trim($request->get('portador_id'));
        $deUmId = trim($request->get('de_um_id'));
        $aUmId = trim($request->get('a_um_id'));
        $factor = trim($request->get('factor'));

        $validateResult = $this->validate($portadorId, $deUmId, $aUmId, $id);
        if ($validateResult !== null) return $validateResult;

        $entity = $em->getRepository('PortadoresBundle:FactorConversion')->find($id);
        if (!$entity) return new JsonResponse(['success' => false, 'message' => 'El factor de conversión no esta disponible.']);

        /** @var FactorConversion $entity */
        $entity->setPortador($em->getRepository('PortadoresBundle:Portador')->find($portadorId))
            ->setDeUm($em->getRepository('PortadoresBundle:UnidadMedida')->find($deUmId))
            ->setAUm($em->getRepository('PortadoresBundle:UnidadMedida')->find($aUmId))
            ->setFactor($factor);

        try {
            $em->persist($entity);
            $em->flush();
            return new JsonResponse(['success' => true, 'cls' => 'success', 'message' => 'Factor de conversión modificado con éxito.']);
        } catch (Exception $ex) {
            return new JsonResponse(['success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.']);
        }
    }

    public function delAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');

        $entity = $em->getRepository('PortadoresBundle:FactorConversion')->find($id);
        if (!$entity) return new JsonResponse(['success' => false, 'message' => 'El factor de conversión no esta disponible.']);

        try {
            $em->remove($entity);
            $em->flush();
            return new JsonResponse(['success' => true, 'cls' => 'success', 'message' => 'Factor de conversión eliminado con éxito.']);
        } catch (Exception $ex) {
            return new JsonResponse(['success' => false, 'cls' => 'danger', 'message' => 'Error guardando los datos, si el error persiste contacte a su administrador.']);
        }
    }

    private function validate($portadorId, $deUmId, $aUmId, $id = null)
    {
        $em = $this->getDoctrine()->getManager();

        if ($deUmId === $aUmId) return new JsonResponse(['success' => false, 'message' => 'Las dos unidades de medidas no pueden ser iguales.']);

        $qb = $em->createQueryBuilder();
        $qb->select('factor')
            ->from('PortadoresBundle:FactorConversion', 'factor')
            ->where($qb->expr()->eq('factor.portador', ':portador'))
            ->andWhere($qb->expr()->eq('factor.deUm', ':deUm'))
            ->andWhere($qb->expr()->eq('factor.aUm', ':aUm'))
            ->setParameters([
                'portador' => $portadorId,
                'deUm' => $deUmId,
                'aUm' => $aUmId
            ]);
        if ($id) {
            $qb->andWhere($qb->expr()->neq('factor.id', ':id'))
                ->setParameter('id', $id);
        }
        $duplicates = $qb->getQuery()->getResult();

        if ($duplicates) return new JsonResponse(['success' => false, 'message' => 'Ese factor de conversión ya existe para ese portador.']);

        return null;
    }
}