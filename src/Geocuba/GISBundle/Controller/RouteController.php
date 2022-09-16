<?php

namespace Geocuba\GISBundle\Controller;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Geocuba\Utils\Functions;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class RouteController
 * @package Geocuba\GISBundle\Controller
 */
class RouteController extends Controller
{
    const PERFIL_SINGLE_ORDER = 'SINGLE';
    const PERFIL_GO_COME_ORDER = 'GO_COME';
    const PERFIL_CUSTOM_ORDER = 'CUSTOM_ORDER';
    const PERFIL_N_WAYS = 'N_WAYS';
    const PERFIL_FIRST_LAST_ORDER = 'PERFIL_BEST_FIRST_LAST_ORDER';
    const PERFIL_FIRST_FIRST_ORDER = 'PERFIL_FIRST_FIRST_ORDER';

    const PERFILES = [
        self::PERFIL_SINGLE_ORDER => 'Ruta óptima de origen a destino',
        self::PERFIL_GO_COME_ORDER => 'Ruta óptima para ida y regreso',
        self::PERFIL_CUSTOM_ORDER => 'Partiendo del primer punto, respetar el orden seleccionado',
        self::PERFIL_N_WAYS => 'N mejores rutas de origen a destino',
        self::PERFIL_FIRST_LAST_ORDER => 'Proponer el mejor orden para salir del primer punto y llegar al último',
        self::PERFIL_FIRST_FIRST_ORDER => 'Proponer el mejor orden para salir del primer punto y llegar al mismo'
    ];

    /**
     * Devuelve un arreglo de datos con las rutas.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function listAction(Request $request)
    {
        /**@var Connection $conn */
        $conn = $this->getDoctrine()->getConnection('gis');

        try {
            $rows = $conn->fetchAll('SELECT gid, route_name, metadata, xid, distance FROM routes limit :limit offset :start', [
                'limit' => $request->query->get('limit'),
                'start' => $request->query->get('start')
            ]);

            $total = $conn->fetchColumn('SELECT COUNT(gid) FROM routes');

            foreach ($rows as &$row) {
                $row['metadata'] = $this->_getMetadata($conn, $row['gid'], $row['metadata']);
            }
        } catch (DBALException $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), $e);
        }

        return new JsonResponse(['success' => true, 'rows' => $rows, 'total' => $total]);
    }

    /**
     * Inserta una nueva ruta con los parámetros especificados.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function addAction(Request $request)
    {
        $SELECT_GEOM_SQL = "SELECT %s(:source, :target, false, true)";
        $INSERT_ROUTE_SQL = "INSERT INTO gen_routes(route_name, metadata, the_geom) VALUES (:route_name, :metadata, :the_geom)";
        $UPDATE_ROUTE_SQL = "UPDATE gen_routes SET the_geom = :the_geom WHERE gid = :gid";
        $SELECT_ROUTE_GID_SQL = "SELECT gid FROM gen_routes WHERE route_name = :route_name";
        $SELECT_ROUTE_DISTANCE_SQL = "SELECT round(st_length_spheroid((SELECT the_geom FROM gen_routes WHERE gid = :gid), 'SPHEROID(\"WGS84\",6378137,298.25728)'::spheroid)::numeric / 1000::numeric, 3) as distance";
        $INSERT_SEGMENT_SQL = "INSERT INTO sub_routes(route_id, source, target, distance, no_order, route_streets) VALUES (:gid, :source, :target, :distance, :nro_order, :streets)";

        $profile = $request->request->get('profile');
        if (!in_array($profile, array_keys(self::PERFILES))) {
            throw new BadRequestHttpException("{$profile} not implemented yet.");
        }

        try {
            /** @var Connection $conn */
            $conn = $this->getDoctrine()->getConnection('gis');

            // TODO: TRANSACTIONAL!!!

//            $conn->transactional(function (Connection $conn) use ($request) {
            /** @var Request $request */

            $route_name = trim($request->request->get('name'));
            if ($conn->fetchColumn("SELECT gid FROM gen_routes WHERE LOWER(TRIM(route_name)) = :route_name", ['route_name' => strtolower(trim($route_name))]) !== false) {
                return new JsonResponse(array('success' => false, 'message' => "La ruta <strong>{$route_name}</strong> ya existe."));
            }

            $points_ids = json_decode($request->request->get('points_ids'), true);
            $locked = $request->request->get('locked', false); // FIXME

            $metadata = $this->_buildMetadata($points_ids, $locked);
            $source_point_id = $points_ids[0];
            $target_point_id = $points_ids[1];

            switch ($profile) {
                case self::PERFIL_SINGLE_ORDER:
                    // SELECT the_geom
                    $the_geom_subquery = sprintf($SELECT_GEOM_SQL, $locked ? 'route_rdijkstra' : 'route_dijkstra');
                    $the_geom = $conn->fetchColumn($the_geom_subquery, [// params
                        'source' => $source_point_id,
                        'target' => $target_point_id,
                    ]);

                    // INSERT public.gen_routes
                    $conn->executeUpdate($INSERT_ROUTE_SQL, [// params
                        'route_name' => $route_name,
                        'metadata' => $metadata,
                        'the_geom' => $the_geom
                    ]);

                    // SELECT gid
                    $route_gid = $conn->fetchColumn($SELECT_ROUTE_GID_SQL, [// params
                        'route_name' => $route_name
                    ]);

                    // Calcular la distancia de la ruta
                    $distance = $conn->fetchColumn($SELECT_ROUTE_DISTANCE_SQL, [// params
                        'gid' => $route_gid
                    ]);

                    // INSERT public.sub_routes
                    $conn->executeUpdate($INSERT_SEGMENT_SQL, [
                        'gid' => $route_gid,
                        'source' => $source_point_id,
                        'target' => $target_point_id,
                        'distance' => $distance,
                        'nro_order' => 1,
                        'streets' => $this->_buildRouteStreets($conn, $source_point_id, $target_point_id, $locked), // FIXME
                    ]);

                    break;
                case self::PERFIL_GO_COME_ORDER:
                    $the_geom = $conn->fetchColumn(sprintf($SELECT_GEOM_SQL, 'route_bdDijkstra'), [// params
                        'source' => $source_point_id,
                        'target' => $target_point_id,
                    ]);

                    // INSERT public.gen_routes
                    $conn->executeUpdate($INSERT_ROUTE_SQL, [// params
                        'route_name' => $route_name,
                        'metadata' => $metadata,
                        'the_geom' => $the_geom
                    ]);

                    break;
                case self::PERFIL_CUSTOM_ORDER:
                    // SELECT the_geom
                    $the_geom_subquery = sprintf($SELECT_GEOM_SQL, $locked ? 'route_rdijkstra' : 'route_dijkstra');
                    $the_geom = $conn->fetchColumn($the_geom_subquery, [// params
                        'source' => $source_point_id,
                        'target' => $target_point_id,
                    ]);

                    // INSERT public.gen_routes
                    $conn->executeUpdate($INSERT_ROUTE_SQL, [// params
                        'route_name' => $route_name,
                        'metadata' => $metadata,
                        'the_geom' => $the_geom
                    ]);

                    // SELECT gid
                    $route_gid = $conn->fetchColumn($SELECT_ROUTE_GID_SQL, [// params
                        'route_name' => $route_name
                    ]);

                    // Calcular la distancia de la ruta
                    $distance = $conn->fetchColumn($SELECT_ROUTE_DISTANCE_SQL, [// params
                        'gid' => $route_gid
                    ]);

                    // INSERT public.sub_routes
                    $conn->executeUpdate($INSERT_SEGMENT_SQL, [
                        'gid' => $route_gid,
                        'source' => $source_point_id,
                        'target' => $target_point_id,
                        'distance' => $distance,
                        'nro_order' => 1,
                        'streets' => $this->_buildRouteStreets($conn, $source_point_id, $target_point_id, $locked),
                    ]);

                    for ($i = 1; $i < count($points_ids) - 1; $i++) {
                        $_source_point_id = $points_ids[$i];
                        $_target_point_id = $points_ids[$i + 1];

                        // ST_Union route:the_geom - poi:the_geom
                        $_the_geom = $conn->fetchColumn("SELECT ST_Union (:the_geom, (" . $the_geom_subquery . "))", [// params
                            'the_geom' => $the_geom,
                            'source' => $_source_point_id,
                            'target' => $_target_point_id,
                        ]);

                        // UPDATE public.gen_routes
                        $conn->executeUpdate($UPDATE_ROUTE_SQL, [// params
                            'gid' => $route_gid,
                            'the_geom' => $_the_geom
                        ]);

                        // Calcular la distancia del segmento, luego de actualizar la geometría de la ruta
                        $updated_distance = $conn->fetchColumn($SELECT_ROUTE_DISTANCE_SQL, [// params
                            'gid' => $route_gid
                        ]);
                        $updated_distance = $updated_distance - $distance; // TODO: comments

                        // INSERT public.sub_routes
                        $conn->executeUpdate($INSERT_SEGMENT_SQL, [
                            'gid' => $route_gid,
                            'source' => $_source_point_id,
                            'target' => $_target_point_id,
                            'distance' => $updated_distance,
                            'nro_order' => $i + 1,
                            'streets' => $this->_buildRouteStreets($conn, $_source_point_id, $_target_point_id, $locked),
                        ]);

                        $distance = $updated_distance + $distance; // TODO: comments
                    }

                    break;
                default:
                    throw new BadRequestHttpException("{$profile} not implemented yet.");
            }

//                return true;
//            });
        } catch (\Throwable $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), $e);
        }

        return new JsonResponse(['success' => true, 'route_name' => $route_name, 'message' => "La Ruta <strong>{$route_name}</strong> ha sido registrada."]);
    }

    /**
     * Exporta Excel (.xlsx) con los datos de la ruta especificada.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportAction(Request $request)
    {
        $route_id = $request->query->get('gid');

        try {
            /** @var Connection $conn */
            $conn = $this->getDoctrine()->getConnection('gis');

            $route = $conn->fetchAssoc("SELECT route_name, metadata, xid, distance FROM routes WHERE gid = :route_id", [
                'route_id' => $route_id
            ]);

            // Actualizar el metadato de la ruta...
            $metadata = $this->_getMetadata($conn, $route_id, $route['metadata']);

            $spreadsheet = Functions::createSpreadsheet($this->get('service_container'), $route['route_name'], $route['route_name']);
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->mergeCells('B2:C2')->getCell('B2')->setValue('Origen:');
            $sheet->mergeCells('D2:G2')->getCell('D2')->setValue($metadata['from']);
            $sheet->mergeCells('B3:C3')->getCell('B3')->setValue('Destino:');
            $sheet->mergeCells('D3:G3')->getCell('D3')->setValue($metadata['to']);

            $row_offset = 5;

            $sheet->getCellByColumnAndRow(2, $row_offset)->setValue('#');
            $sheet->mergeCellsByColumnAndRow(3, $row_offset, 4, $row_offset)->getCellByColumnAndRow(3, $row_offset)->setValue('Origen');
            $sheet->getCellByColumnAndRow(5, $row_offset)->setValue('Destino');
            $sheet
                ->mergeCellsByColumnAndRow(6, $row_offset, 7, $row_offset)
                ->getCellByColumnAndRow(6, $row_offset)->setValue('Distancia (km)');

            $row_offset++;
            foreach ($metadata['segments'] as $idx => $segment) {
                $sheet->getCellByColumnAndRow(2, $idx + $row_offset)->setValue($idx + 1);
                $sheet->mergeCellsByColumnAndRow(3, $idx + $row_offset, 4, $idx + $row_offset)->getCellByColumnAndRow(3, $idx + $row_offset)->setValue($segment['source']);
                $sheet->getCellByColumnAndRow(5, $idx + $row_offset)->setValue($segment['target']);
                $sheet->getCellByColumnAndRow(6, $idx + $row_offset)->setValue($segment['distance']);
                $sheet->getCellByColumnAndRow(7, $idx + $row_offset)->setValue($segment['cumul_distance']);
            }

            $total_offset = count($metadata['segments']) + $row_offset;
            $sheet
                ->mergeCellsByColumnAndRow(2, $total_offset, 5, $total_offset)
                ->getCellByColumnAndRow(2, $total_offset)->setValue('Total');

            $sheet
                ->mergeCellsByColumnAndRow(6, $total_offset, 7, $total_offset)
                ->getCellByColumnAndRow(6, $total_offset)->setValue($route['distance']);


            $sheet->getStyle('B2:B3')->getFont()->setBold(true);
            $sheet->getStyle('B5:F5')->getFont()->setBold(true);
            $sheet->getStyleByColumnAndRow(2, $total_offset, 6, $total_offset)->getFont()->setBold(true);

            $sheet->getStyle('B2:B3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('D2:D3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyleByColumnAndRow(2, $total_offset)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyleByColumnAndRow(6, $row_offset, 7, $total_offset)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
//            $sheet->getStyleByColumnAndRow(6, $total_offset)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $style = $sheet->getStyleByColumnAndRow(2, 2, 7, 3);
            $style->getBorders()->applyFromArray(['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '888888']]]);

            $style = $sheet->getStyleByColumnAndRow(2, $row_offset - 1, 7, $total_offset);
            $style->getBorders()->applyFromArray(['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '888888']]]);
            $sheet->getStyleByColumnAndRow(6, $row_offset, 7, $total_offset)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
//            $style->getAlignment()->setWrapText(true);

            foreach (range(2, 7) as $column_idx) {
                $sheet->getColumnDimensionByColumn($column_idx)->setAutoSize(true);
            }

            $sheet->setSelectedCellByColumnAndRow(6, $total_offset);

        } catch (\Exception $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), $e);
        }

        return Functions::stream($spreadsheet, $route['route_name']);
    }

    /**
     * @param Connection $conn
     * @param string $route_id
     * @param $raw_metadata
     * @return array
     */
    private function _getMetadata($conn, $route_id, $raw_metadata)
    {
        $route_length = 0; // Longitud de la ruta

        // Obtener los segmentos de la ruta...
        $segments = $conn->fetchAll("SELECT * FROM v_sub_routes WHERE route_id = :route_id", [
            'route_id' => $route_id
        ]);
        foreach ($segments as &$segment) {
            $route_length += floatval($segment['distance']);
            $segment['cumul_distance'] = round($route_length, 3); // Actualizar la longitud del segmento con respecto al punto inicial...
        }

        // Decodificar el metadato (JSON) de la ruta...
        $metadata = json_decode($raw_metadata, true);

        // Actualizar el metadato de la ruta...
        return [
            'from' => $metadata['from'],
            'to' => $metadata['to'],
            'segments' => $segments
        ];
    }

    /**
     * TODO: comments
     * TODO: review
     *
     * @param $_points
     * @param $_locked
     * @return bool|false|string
     */
    private function _buildMetadata($_points, $_locked)
    {
        $_list = array();
        $conn = $this->getDoctrine()->getConnection('gis');
        foreach ($_points as $_point) {
            $_entities = $conn->fetchAll("select point_name from waypoints where node_id = $_point limit 1");
            array_push($_list, array('id' => $_point, 'name' => $_entities[0]['point_name']));
        }
        $_points = $_list;

        $retval = array('points' => array());

        foreach ($_points as $pto)
            array_push($retval['points'], $pto['id']);

        $first = array_shift($_points);
        $last = array_pop($_points);
        $retval['from'] = $first['name'];
        $retval['to'] = $last['name'];

//        $conn = $this->container->get('doctrine.dbal.rutas_cuba_connection');

        if ($_locked) {
            $selection = "SELECT street_gid FROM locked_streets WHERE locked = true";
            $entities = $conn->fetchAll($selection);
            if (is_null($selection)) return false;
            if (count($entities) > 0) {
                $retval['locked'] = array();
                foreach ($entities as $row)
                    array_push($retval['locked'], $row['street_gid']);
            }
        }

        return json_encode($retval);
    }

    /**
     * TODO: comments
     *
     * @param Connection $conn
     * @param string $source_point_id
     * @param string $target_point_id
     * @param boolean $locked
     * @return string
     */
    private function _buildRouteStreets($conn, $source_point_id, $target_point_id, $locked)
    {
        $streets = $conn->fetchAll("SELECT route_strrdijkstra(:source, :target, false, true) as street", [
            'source' => $source_point_id,
            'target' => $target_point_id
        ]);

        if ($locked) {
            // TODO
        }

        $last_street = null;
        return is_array($streets) ? array_reduce($streets, function ($streets, $row) use (&$last_street) {
            $street = $row['street'];
            $streets = $streets . (empty($street) ? '' : ($last_street !== $street ? (($streets === '' ? '' : ';') . $street) : ''));
            $last_street = empty($street) ? $last_street : $street;

            return $streets;
        }, '') : '';
    }
}
