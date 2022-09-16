<?php
/**
 * Created by PhpStorm.
 * User: pfcadenas
 * Date: 05/01/17
 * Time: 09:00
 */

namespace Geocuba\AdminBundle\Util;

class FechaUtil
{
    public static function getUltimoDiaMes($mes, $anno)
    {
        $dia = 30;
        if (in_array($mes, array(1, 3, 5, 7, 8, 10, 12))){
            $dia = 31;
        }
        if ($mes == 2){
            $dia = ($anno % 4 == 0) ? 29 : 28;
        }
        return $anno . '-' . $mes . '-' . $dia;
    }

    public static function getNombreMes($mes)
    {
        $meses = array('-', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
        return $meses[$mes];
    }

    public static function getDiaSemana($dia)
    {
        $dias = array('-', 'Lunes', 'Martes', 'Mi&eacute;rcoles', 'Jueves', 'Viernes', 'S&aacute;bado', 'Domingo');
        return $dias[$dia];
    }
} 