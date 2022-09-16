<?php

namespace Geocuba\GISBundle\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class MapController
 * @package Geocuba\GISBundle\Controller
 */
class MapController extends Controller
{
    /**
     * @return array
     */
    public function indexAction()
    {
        return ['tpl' => 'GISBundle:Map:index.html.twig'];
    }

    /**
     * Devuelve un arreglo de datos con los Municipios.
     *
     * @return JsonResponse
     */
    public function listLocalitiesAction()
    {
        /** @var Connection $conn */
        $conn = $this->getDoctrine()->getConnection('gis');
        $localities = $conn->fetchAll("SELECT municipios.gid as id, municipio as nombre, provincias.gid as provincia_id FROM municipios LEFT JOIN provincias on municipios.provincia = provincias.provincia ORDER BY municipio");

        return new JsonResponse(['success' => true, 'rows' => $localities, 'total' => count($localities)]);
    }

    /**
     * Devuelve un arreglo de datos con las Provincias.
     *
     * @return JsonResponse
     */
    public function listStatesAction()
    {
        /** @var Connection $conn */
        $conn = $this->getDoctrine()->getConnection('gis');
        $states = $conn->fetchAll("SELECT gid as id, provincia as nombre FROM provincias ORDER BY provincia");

        return new JsonResponse(['success' => true, 'rows' => $states, 'total' => count($states)]);
    }
}
