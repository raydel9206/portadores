<?php

namespace Geocuba\GISBundle\Controller;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Geocuba\Utils\Functions;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class RouteController
 * @package Geocuba\GISBundle\Controller
 */
class TableController extends Controller
{
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
            $rows = $conn->fetchAll('SELECT matrix_id as id, matrix_name as name FROM matrix_names', [
                'limit' => $request->query->get('limit'),
                'start' => $request->query->get('start')
            ]);
            $total = $conn->fetchColumn('SELECT COUNT(matrix_id) FROM matrix_names');

            foreach ($rows as &$row) {
                $row['points'] = $this->_getPoints($conn, $row['id']);
            }
        } catch (DBALException $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), $e);
        }

        return new JsonResponse(['success' => true, 'rows' => $rows, 'total' => $total]);
    }

    /**
     * Generar una nueva tabla de distancia con los parámetros especificados.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function addAction(Request $request)
    {
        $table_name = trim($request->request->get('name'));
        $points_ids = json_decode($request->request->get('points_ids'), true);


        try {
            /** @var Connection $conn */
            $conn = $this->getDoctrine()->getConnection('gis');

            // SELECT matrix_names.matrix_name
            if ($conn->fetchColumn("SELECT matrix_id FROM matrix_names WHERE LOWER(TRIM(matrix_name)) = :table_name", ['table_name' => strtolower(trim($table_name))]) !== false) {
                return new JsonResponse(array('success' => false, 'message' => "La Tabla de distancia <strong>{$table_name}</strong> ya existe."));
            }

            // INSERT matrix_names
            $conn->executeUpdate("INSERT INTO matrix_names(matrix_name) VALUES (:table_name)", [ // params
                'table_name' => $table_name
            ]);

            // SELECT matrix_names.matrix_id
            $table_id = $conn->fetchColumn("SELECT matrix_id from matrix_names where matrix_name = :table_name", [
                'table_name' => $table_name
            ]);

//            $conn->createSavepoint()

            $generated_pairs = [];
            foreach ($points_ids as $idx => $point_id) {
                $filtered_points_ids = array_filter($points_ids, function ($_point_id) use ($point_id, $generated_pairs) {
                    return $_point_id !== $point_id && !in_array($_point_id . '-' . $point_id, $generated_pairs);
                });

                foreach ($filtered_points_ids as $_point_id) {
                    // Generate the_geom that contains both points
                    $the_geom = $conn->fetchColumn("SELECT ST_AsText(route_dijkstra(:source, :target, false, true))", [
                        'source' => $point_id,
                        'target' => $_point_id
                    ]);

                    // Calculate distance between both points
                    $distance = $conn->fetchColumn("SELECT (ST_LengthSpheroid(:the_geom, 'SPHEROID(\"WGS84\", 6378137, 298.25728)')/ 1000)::numeric(19, 3)", [
                        'the_geom' => $the_geom
                    ]);

                    // INSERT matrix_distance
                    $conn->executeUpdate("INSERT INTO matrix_distance(source, target, the_geom, matrix_id, length) VALUES (:source, :target, ST_GeomFromText(:the_geom), :table_id, :length)", [
                        'table_id' => $table_id,
                        'source' => $point_id,
                        'target' => $_point_id,
                        'the_geom' => $the_geom,
                        'length' => $distance
                    ]);

                    $generated_pairs[] = $point_id . '-' . $_point_id;
                    $generated_pairs[] = $_point_id . '-' . $point_id;
                }
            }

        } catch (\Throwable $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }

        return new JsonResponse(['success' => true, 'message' => "La Tabla de distancia <strong>{$table_name}</strong> ha sido registrada."]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function delAction(Request $request)
    {
        $table_id = $request->request->get('id');

        try {
            /** @var Connection $conn */
            $conn = $this->getDoctrine()->getConnection('gis');

            $table_name = $conn->fetchColumn("SELECT matrix_name from matrix_names where matrix_id = :table_id", [
                'table_id' => $table_id
            ]);

            $conn->executeUpdate("DELETE FROM matrix_names WHERE matrix_id = :table_id", [
                'table_id' => $table_id
            ]);
        } catch (DBALException $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), $e);
        }

        return new JsonResponse(['success' => true, 'message' => "La Tabla de distancia <strong>{$table_name}</strong> ha sido eliminada."]);
    }

    /**
     * Exporta Excel (.xlsx) con los datos de la tabla especificada.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportAction(Request $request)
    {
        $table_id = $request->query->get('id');

        try {
            /** @var Connection $conn */
            $conn = $this->getDoctrine()->getConnection('gis');

            $table = $conn->fetchAssoc("SELECT matrix_name as name FROM matrix_names WHERE matrix_id = :table_id", [
                'table_id' => $table_id
            ]);

            $points = $this->_getPoints($conn, $table_id);

            $spreadsheet = Functions::createSpreadsheet($this->get('service_container'), $table['name'], $table['name']);
            $sheet = $spreadsheet->getActiveSheet();

            $row_offset = 2;
            $col_offset = 2;

            $sheet->getCellByColumnAndRow($col_offset, $row_offset)->setValue('POI');

            $points_col_offsets = [];
            foreach ($points as $idx => $point) {
                $points_col_offsets[$idx] = ++$col_offset;
                $sheet->getCellByColumnAndRow($col_offset, $row_offset)->setValue($point['point_name']);
            }

            $row_offset++;
            foreach ($points as $point) {
                $sheet->getCellByColumnAndRow(2, $row_offset)->setValue($point['point_name']);

                foreach ($point['nodes'] as $node_id => $node) {
                    $sheet->getCellByColumnAndRow($points_col_offsets[$node_id], $row_offset)->setValue(empty($node) ? '-' : $node);
                }

                $row_offset++;
            }
            $row_offset--;

            $sheet->getStyleByColumnAndRow(2, 2, 2, $row_offset)->getFont()->setBold(true);
            $sheet->getStyleByColumnAndRow(2, 2, $col_offset, 2)->getFont()->setBold(true);
            $sheet->getStyleByColumnAndRow(2, 2, $col_offset, $row_offset)->getBorders()->applyFromArray(['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '888888']]]);
            $sheet->getStyleByColumnAndRow(3, 3, $col_offset, $row_offset)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

            foreach ($sheet->getColumnIterator() as $column) {
                $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }

            $sheet->setSelectedCell('A1');
        } catch (\Exception $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), $e);
        }

        return Functions::stream($spreadsheet, $table['name']);
    }

    /**
     * @param Connection $conn
     * @param string $table_id
     * @return array
     * @throws DBALException
     */
    private function _getPoints($conn, $table_id)
    {
        // Obtener los puntos de la tabla...
        $source_points = $conn->fetchAll("SELECT matrix_distance.source as gid, COALESCE(TRIM(waypoints.point_name), '-') as point_name FROM matrix_distance LEFT JOIN waypoints ON matrix_distance.source = waypoints.node_id WHERE matrix_distance.matrix_id = :table_id", [
            'table_id' => $table_id
        ]);

        $target_points = $conn->fetchAll("SELECT matrix_distance.target as gid, COALESCE(TRIM(waypoints.point_name), '-') as point_name FROM matrix_distance LEFT JOIN waypoints ON matrix_distance.target = waypoints.node_id WHERE matrix_distance.matrix_id = :table_id", [
            'table_id' => $table_id
        ]);

        $points = array_merge($source_points, $target_points);
        $_points = [];
        foreach ($points as &$point) {
            foreach ($points as $_point) {
                if ($point['gid'] === $_point['gid']) {
                    $distance = 0;
                } else if (array_key_exists($_point['gid'], $points) && array_key_exists($point['gid'], $points[$_point['gid']]['nodes'])) {
                    $distance = $points[$_point['gid']]['nodes'][$point['gid']];
                } else {
                    $distance = $conn->fetchColumn("SELECT length FROM matrix_distance WHERE (source = :source AND target = :target ) OR (source = :target AND target = :source ) AND matrix_id = :table_id", [// params
                        'source' => $point['gid'],
                        'target' => $_point['gid'],
                        'table_id' => $table_id
                    ]);
                }

                $point['nodes'][$_point['gid']] = is_numeric($distance) ? $distance : 0;
            }

            ksort($points);
            $_points[$point['gid']] = $point;
        }

        ksort($_points);
        return $_points;
    }
}
