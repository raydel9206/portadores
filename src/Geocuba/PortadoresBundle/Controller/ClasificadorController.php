<?php
/**
 * Created by PhpStorm.
 * User: Mire
 * Date: 16/04/2020
 * Time: 10:01
 */

namespace Geocuba\PortadoresBundle\Controller;

use Geocuba\AdminBundle\Util\Util;
use Geocuba\PortadoresBundle\Util\FechaUtil;
use Geocuba\PortadoresBundle\Entity\AnexoUnico;
use Geocuba\PortadoresBundle\Entity\CombustibleKilometros;
use Geocuba\PortadoresBundle\Entity\Vehiculo;
use Doctrine\Common\CommonException;
use Doctrine\Common\Util\Debug;
use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Geocuba\Utils\ViewActionTrait;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Geocuba\PortadoresBundle\Util\Utiles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Geocuba\PortadoresBundle\Util\DocumentosEnum;

class ClasificadorController extends Controller
{
    use ViewActionTrait;

    public function loadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $start = $request->get('start');
        $limit = $request->get('limit');

        $entities = $em->getRepository('PortadoresBundle:Clasificador')->findBy(array('visible' => true));

        $_data = array();

        foreach ($entities as $entity) {
            $_data[] = array(
                'id' => $entity->getId(),
                'nombre' => $entity->getNombre(),
                'codigo' => $entity->getCodigo()
            );
        }

        return new JsonResponse(array('rows' => $_data));
    }

}