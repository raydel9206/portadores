<?php

namespace Geocuba\PortadoresBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class PlanificacionCombustibleTecnRepository extends EntityRepository
{

    /**
     * @param $_unidades
     * @param $tipoComb
     * @param $anno
     * @param string $searchText
     * @return array|mixed
     */
    public function findAllBy($_unidades, $tipoComb, $anno, $searchText = '')
    {
        $sql = "SELECT
                max(pct.id) AS id,
                pct.anno,
                bool_and(pct.aprobada) AS aprobada,
                max(pct.cant_combustible) AS combustible_total,
                max(pct.nivel_actividad) AS nivel_actividad_total,
                max(pct.unidad_id) AS unidad_id,
                max(pct.tipo_combustible_id) AS tipo_combustible_id,
                max(caldera.tipo_combustible_recirculacion_id) AS tipo_combustible_recirculacion_id,
                string_agg(pctd.nivel_actividad::text, ',') AS niveles_actividad,
                string_agg(pctd.cant_combustible::text, ',') AS cantidades_combustible,
                max(eq.id) AS equipo_tecnologico_id,
                max(eq.descripcion) AS equipo_tecnologico_descripcion,
                max(eq.nro_inventario) AS equipo_tecnologico_nro_inventario,
                max(eq.norma) AS equipo_tecnologico_norma,
                max(caldera.norma_recirculacion) AS equipo_tecnologico_norma_recirculacion
            
            FROM datos.planificacion_combustible_tecn pct
            JOIN (SELECT * FROM datos.planificacion_combustible_tecn_desglose pctd ORDER BY pctd.mes) pctd ON pctd.planificacion_combustible_tecn_id = pct.id
            JOIN nomencladores.equipos_tecnologicos eq ON eq.id = pct.equipo_tecnologico_id
            LEFT JOIN nomencladores.calderas caldera ON caldera.id = eq.id            
            WHERE anno = '$anno' AND pct.unidad_id IN $_unidades";

        if ($searchText)
            $sql .= " AND (lower(unaccent(eq.descripcion)) LIKE lower(unaccent('%$searchText%')) OR lower(unaccent(eq.nro_inventario)) LIKE lower(unaccent('%$searchText%')))";
        if ($tipoComb)
            $sql .= "AND pct.tipo_combustible_id = '$tipoComb'";

        $sql .= ' GROUP BY pct.equipo_tecnologico_id, pct.anno, pct.tipo_combustible_id ORDER BY equipo_tecnologico_descripcion';

        $conn = $this->getEntityManager()->getConnection();

        return $conn->fetchAll($sql);
    }
}