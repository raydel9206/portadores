<?php
/**
 * Created by PhpStorm.
 * User: asisoftware13
 * Date: 7/9/2020
 * Time: 2:07 p.m.
 */

namespace Geocuba\PortadoresBundle\Controller;

use Doctrine\Common\Util\Debug;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Geocuba\Utils\ViewActionTrait;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\PortadoresBundle\Util\Utiles;
use Symfony\Component\HttpFoundation\JsonResponse;

class SaldosCombustibleController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $nunidadid = trim($request->get('nunidadid'));
        $combustible = $request->get('combustible');

        $_data = array();
        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($nunidadid), $_unidades);

        $qb = $em->createQueryBuilder();
        $qb->select('tarjeta')
            ->from('PortadoresBundle:Tarjeta', 'tarjeta')
            ->Where($qb->expr()->in('tarjeta.nunidadid', $_unidades))
            ->andWhere($qb->expr()->eq('tarjeta.visible', 'true'));

        if (isset($combustible) && $combustible != '' && $combustible !== 'null') {
            $qb->andWhere('tarjeta.ntipoCombustibleid = :ntipoCombustibleid')
                ->setParameter('ntipoCombustibleid', "$combustible");
        }

        $tarjetas = $qb->getQuery()->getResult();

        $tiposCombustible = $em->getRepository('PortadoresBundle:Tipocombustible')->findByVisible(true);

        foreach ($tiposCombustible as $item) {
            $importe = 0;
            foreach ($tarjetas as $tar) {
                $entity = $em->getRepository('PortadoresBundle:HistorialTarjeta')->post($tar->getId());
                $ultimo = count($entity);
                if ($entity) {
                    if ($tar->getTipoCombustibleid()->getId() === $item->getId()) {
                        $importe += $entity[$ultimo - 1]->getExistenciaImporte();
                    }
                } else {
                    if ($tar->getTipoCombustibleid()->getId() === $item->getId()) {
                        $importe += $tar->getImporte();
                    }
                }
            }

            $_data[] = array(
                'id' => $item->getId(),
                'combustible' => $item->getNombre(),
                'tipo' => $item->getNombre(),
                'importe' => round($importe,2),
            );
        }

        $result = array();
        if ($combustible !== 'null') {
            foreach ($_data as $i => $item) {
                if ($item['id'] === $combustible) {
                    $result[] = $item;
                }
            }
        } else {
            $result = $_data;
        }
        return new JsonResponse(array('rows' => $result));
    }
}