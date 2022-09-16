<?php

namespace Geocuba\PortadoresBundle\Util;

class DocumentosEnum
{
    const planCombustible = '1';
    const analisisConsumoEquipoEquipo = '2';
    const distribucionCombustible = '3';
    const autorizoTarjeta = '4';
    const equiposParalizados = '5';
    const planAgua = '6';
    const planElectricidad = '7';
    const controlCombustibleDepositos = '8';
    const controlCombustibleVehiculos = '9';
    const estadoTarjetas = '10';
    const libroCombustibleCaja = '11';
    const parteMensualExplotacion = '12';
    const resumenExplotaciónVehículos = '13';
    const reporteDiarioServiciosElectricos = '14';
    const reporteParteDiarioAgua = '15';
    const relacionEquiposIneficientes = '16';
    const modeloCDA002 = '17';
    const bitacora = '18';
    const anexo1RegistroOperaciones = '19';
    const medicionesDiariasGruposElectrogenosEmergencia = '20';
    const entregachips = '21';
    const anexo8 = '22';
    const conciliacionMensual = '23';
    const registroCombustible = '24';
    const cierreMensual = '25';
    const modeloCDA001 = '26';

    /**
     * @param $documento
     * @return string
     */
    public static function getNombre($documento)
    {
        switch ($documento) {
            case '1':
                return 'Plan de Combustible';
                break;
            case '2':
                return 'Análisis de los consumos Equipo a Equipo';
                break;
            case '3':
                return 'Distribución de Combustible';
                break;
            case '4':
                return 'Autorizo de cambio o entrega de tarjeta';
                break;
            case '5':
                return 'Equipos paralizados';
                break;
            case '6':
                return 'Plan de Agua';
                break;
            case '7':
                return 'Plan de Electricidad';
                break;
            case '8':
                return 'Control de Combustible por Depósitos';
                break;
            case '9':
                return 'Control de Combustible por Vehículos';
                break;
            case '10':
                return 'Estado de las Tarjetas';
                break;
            case '11':
                return 'Libro de Combustible en Caja';
                break;
            case '12':
                return 'Parte Mensual de Explotación';
                break;
            case '13':
                return 'Resumen de Explotación de los Vehículos';
                break;
            case '14':
                return 'Reporte Diario los Servicios Eléctricos';
                break;
            case '15':
                return 'Reporte del Parte Diario de Agua';
                break;
            case '16':
                return 'Relación de Equipos Ineficientes';
                break;
            case '17':
                return 'Modelo CDA 002';
                break;
            case '18':
                return 'Bitácora';
                break;
            case '19':
                return 'Anexo 1. Registro de Operaciones';
                break;
            case '20':
                return 'Mediciones Diarias de los Grupos Electrógenos de Emergencia';
                break;
            case '21':
                return 'Entrega de Chip de Combustibles';
                break;
            case '22':
                return 'Anexo 8';
                break;
            case '23':
                return 'Conciliación Mensual de Transporte';
                break;
            case '24':
                return 'Registro de Combustible';
                break;
            case '25':
                return 'Cierre Mensual';
                break;
            case '26':
                return 'Modelo CDA 001';
                break;
            case '27':
                return 'Conciliación de motorrecursos';
                break;
            case '28':
                return 'Reembolso de combustible';
                break;
            case '9':
                return 'Modelo 5073';
                break;
            default:
                return 'No Existe el documento solicitado';
                break;
        }
    }
}
