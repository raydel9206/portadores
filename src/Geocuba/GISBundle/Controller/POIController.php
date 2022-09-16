<?php

namespace Geocuba\GISBundle\Controller;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class POIController
 * @package Geocuba\GISBundle\Controller
 */
class POIController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function listAction(Request $request)
    {
        /** @var Connection $conn */
        $conn = $this->getDoctrine()->getConnection('gis');

        $nombre = $request->query->get('nombre');
        $provincia_id = $request->query->get('provincia_id');
        $municipio_id = $request->query->get('municipio_id');

        $FILTER_SQL = '';

        if (!empty($nombre)) {
            $nombre = strtolower(trim($nombre));
            $FILTER_SQL .= "LOWER(point_name) LIKE '%{$nombre}%'";
        }
        if (!empty($municipio_id)) {
            $FILTER_SQL = (empty($FILTER_SQL)
                    ? ""
                    : ($FILTER_SQL . " AND ")
                ) . "ST_Contains( (SELECT ST_Union(the_geom) FROM municipios WHERE municipios.gid = '{$municipio_id}'),  the_geom)";
        } else if (!empty($provincia_id)) {
            $FILTER_SQL = (empty($FILTER_SQL)
                    ? ""
                    : ($FILTER_SQL . " AND ")
                ) . "ST_Contains( (SELECT ST_Union(the_geom) FROM provincias WHERE provincias.gid = '{$provincia_id}'),  the_geom)";
        }

        $FILTER_SQL = !empty($FILTER_SQL)
            ? ("WHERE {$FILTER_SQL}")
            : null;

        $LIMIT_SQL = $request->query->has('limit') && $request->query->has('start')
            ? "LIMIT {$request->query->get('limit')} OFFSET {$request->query->get('start')}" :
            '';

        $SELECT_SQL = "SELECT node_id as id, gid, point_name, st_extent(the_geom) as coord FROM waypoints {$FILTER_SQL} group by node_id, gid, point_name {$LIMIT_SQL}";

        $result = [];
        foreach ($conn->fetchAll($SELECT_SQL) as &$row) {
            $str_coord = $row['coord'];
            $str_coord = str_replace('BOX(', '', $str_coord);
            $str_coord = str_replace(')', '', $str_coord);
            $str_coord = str_replace(' ', ',', $str_coord);
            $coord = explode(',', $str_coord);

            $result[] = [
                'id' => $row['id'],
                'gid' => $row['gid'],
                'point_name' => $row['point_name'],
                'minX' => $coord[2],
                'minY' => $coord[3],
                'maxX' => $coord[0],
                'maxY' => $coord[1]
            ];
        }
        return new JsonResponse(['success' => true, 'rows' => $result, 'total' => count($result)]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addAction(Request $request)
    {
        foreach (['point_name', 'point_description', 'point_coordinates'] as $param_name) {
            if (!$request->request->has($param_name) || empty($request->request->get($param_name))) {
                throw new BadRequestHttpException("The required param '{$param_name}' is missing.");
            }
        }

        try {
            /** @var Connection $conn */
            $conn = $this->getDoctrine()->getConnection('gis');

            $point_name = trim($request->request->get('point_name'));
            if ($conn->fetchColumn("SELECT gid FROM waypoints WHERE LOWER(TRIM(point_name)) = :point_name", ['point_name' => strtolower(trim($point_name))]) !== false) {
                return new JsonResponse(array('success' => false, 'message' => "El Punto de interés <strong>{$point_name}</strong> ya existe."));
            }

            $coordinates = $request->request->get('point_coordinates');

            $node_id = $conn->fetchColumn("SELECT locate_waypoint(ST_SetSRID(ST_MakePoint(:x, :y), 4326), :point_name, :point_description)", [
                'x' => $coordinates[0],
                'y' => $coordinates[1],
                'point_name' => $point_name,
                'point_description' => $request->request->get('point_description')
            ]);
        } catch (DBALException $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), $e);
        }

        return new JsonResponse(['success' => true, 'node_id' => $node_id, 'message' => "El Punto de interés <strong>{$point_name}</strong> ha sido registrado."]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function delAction(Request $request)
    {
        $node_id = $request->request->get('node_id');
        if (empty($node_id)) {
            throw new BadRequestHttpException("The required param 'node_id' is missing.");
        }

        /** @var Connection $conn */
        $conn = $this->getDoctrine()->getConnection('gis');
        try {
            $point_name = $conn->fetchColumn("SELECT point_name from waypoints where node_id = :node_id", [
                'node_id' => $node_id
            ]);

            $conn->executeUpdate("SELECT delete_waypoint(:node_id)", [
                'node_id' => $node_id
            ]);
        } catch (DBALException $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), $e);
        }

        return new JsonResponse(['success' => true, 'message' => "El Punto de interés <strong>{$point_name}</strong> ha sido eliminado."]);
    }
}
