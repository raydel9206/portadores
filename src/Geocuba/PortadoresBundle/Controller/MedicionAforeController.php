<?php
/**
 * Created by PhpStorm.
 * User: kireny
 * Date: 5/11/15
 * Time: 10:50
 */

namespace Geocuba\PortadoresBundle\Controller;

use Doctrine\Common\CommonException;
use Geocuba\PortadoresBundle\Entity\MedicionAfore;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\PortadoresBundle\Util\Utiles;


class MedicionAforeController extends Controller
{
    use ViewActionTrait;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loadAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $tanqueId = $request->get('tanque_id');

        $mediciones = $em->getRepository('PortadoresBundle:MedicionAfore')->findBy(['tanqueId' => $tanqueId], ['nivelCm' => 'ASC']);
        $_data = array_map(static function ($medicion) {
            /** @var MedicionAfore $medicion */
            return [
                'id' => $medicion->getId(),
                'nivel_cm' => $medicion->getNivelCm(),
                'eistencia_m3' => $medicion->getExistencia(),
                'tanque_id' => $medicion->getTanque()->getId(),
            ];
        }, $mediciones);

        return new JsonResponse(['rows' => $_data]);
    }
}