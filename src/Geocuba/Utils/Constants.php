<?php

namespace Geocuba\Utils;

/**
 * Class Constants
 * @package Geocuba\Utils
 */
abstract class Constants
{
    const DATE_FORMAT = 'd/m/Y';
    const DATETIME_FORMAT = 'd/m/Y H:i';
    const TIME_FORMAT = 'H:i';

    /*******************************************************************************************************************
     * Notificaciones
     ******************************************************************************************************************/
    const NOTIFICACION_GLOBAL = 1;
    const NOTIFICACION_GRUPO = 2;
    const NOTIFICACION_USUARIO = 3;

    const NOTIFICACIONES = [
        self::NOTIFICACION_GLOBAL => 'GLOBAL',
        self::NOTIFICACION_GRUPO => 'GRUPO',
        self::NOTIFICACION_USUARIO => 'USUARIO'
    ];

    /*******************************************************************************************************************
     * Eventos
     ******************************************************************************************************************/
    const EVENTO_INSERT = 1;
    const EVENTO_UPDATE = 2;
    const EVENTO_DELETE = 3;

    /*******************************************************************************************************************
     * FORMATOS
     ******************************************************************************************************************/
    const FORMATO_PDF = 1;
    const FORMATO_EXCEL = 2;
    const FORMATO_WORD = 3;
    const FORMATO_PNG = 4;
}