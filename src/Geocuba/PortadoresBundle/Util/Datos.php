<?php
/**
 * Created by PhpStorm.
 * User: asisoftware16
 * Date: 10/25/2018
 * Time: 1:42 PM
 */

namespace Geocuba\PortadoresBundle\Util;


use Doctrine\Common\Util\Debug;

class Datos
{
    public static function getCombustibles($em, $id)
    {

        $datos = array();
        $entity = $em->getRepository('PortadoresBundle:TipoCombustible')->findBy(array(
            'id' => $id
        ));
        if ($entity)
            foreach ($entity as $ent) {
                $datos = array(
                    'precio' => $ent->getPrecio(),
                    'nombre' => $ent->getNombre()
                );
            }
        return $datos;
    }

    public static function getPlanDisponibleFincimex($em, $unidadid, $tipoCombustible, $moneda)
    {
        $disponible_fincimex = 0;
        if ($moneda) {
            $last_asignacion = $em->getRepository('PortadoresBundle:Asignacion')->findOneBy(array('unidad' => $unidadid, 'moneda' => $moneda, 'tipoCombustible' => $tipoCombustible, 'visible' => true), array('fecha' => 'DESC'));
        } else {
            $last_asignacion = $em->getRepository('PortadoresBundle:Asignacion')->findOneBy(array('unidad' => $unidadid, 'tipoCombustible' => $tipoCombustible, 'visible' => true), array('fecha' => 'DESC'));
        }
        if ($last_asignacion)
            $disponible_fincimex += $last_asignacion->getDisponible();

        return $disponible_fincimex;

    }

    public static function getSaldoDisponibleFincimex($em, $unidadid, $tipoCombustible, $moneda, $enLitros = true)
    {
        $saldo_fincimex = 0;
        if ($moneda) {
            $cuentas = $em->getRepository('PortadoresBundle:CuentaRecarga')->findBy(array('unidad' => $em->getRepository('PortadoresBundle:Unidad')->find($unidadid), 'tipoCombustible' => $tipoCombustible, 'moneda' => $moneda));
        } else {
            $cuentas = $em->getRepository('PortadoresBundle:CuentaRecarga')->findBy(array('unidad' => $em->getRepository('PortadoresBundle:Unidad')->find($unidadid), 'tipoCombustible' => $tipoCombustible));
        }

        foreach ($cuentas as $cuenta) {
            $saldo_fincimex += $cuenta->getMonto();
        }

        return $enLitros ? ($tipoCombustible->getPrecio() != 0) ? round($saldo_fincimex / $tipoCombustible->getPrecio(), 0) : 0 : $saldo_fincimex;

    }

    public static function getSaldoCaja($em, $unidadid, $tipoCombustible, $moneda, $enLitros = true)
    {
        $_unidades = [];
        Utiles::findDomainByChildren($em, $em->getRepository('PortadoresBundle:Unidad')->find($unidadid), $_unidades);

        $_string_unidades = "'" . $_unidades[0] . "'";
        for ($i = 1; $i < count($_unidades); $i++) {
            $_string_unidades .= ",'" . $_unidades[$i] . "'";
        }

        $tipoCombustible_id = $tipoCombustible->getId();

        if ($moneda) {
            $moneda_id = $moneda->getId();
            $saldo_caja = $em->getConnection()->fetchAll(
                "SELECT SUM(t.importe) as saldo_caja from nomencladores.tarjeta t INNER JOIN nomencladores.centro_costo cc on cc.id = t.centrocosto WHERE t.ntipo_combustibleid = '$tipoCombustible_id' AND t.nmonedaid = '$moneda_id'  AND cc.nunidadid IN ($_string_unidades)");
        } else {
            $saldo_caja = $em->getConnection()->fetchAll(
                "SELECT SUM(t.importe) as saldo_caja from nomencladores.tarjeta t INNER JOIN nomencladores.centro_costo cc on cc.id = t.centrocosto WHERE t.ntipo_combustibleid = '$tipoCombustible_id' AND cc.nunidadid IN ($_string_unidades)");
        }

//        $tarjetas = $em->getRepository('PortadoresBundle:Tarjeta')->findBy(array('nunidadid' => $unidadid, 'ntipoCombustibleid' => $tipoCombustible, 'visible' => true));
//        $saldo_caja = 0;
//        foreach ($tarjetas as $tarjeta) {
//            $saldo_caja += $tarjeta->getImporte();
//        }

        return $enLitros ? ($tipoCombustible->getPrecio() != 0) ? round($saldo_caja[0]['saldo_caja'] / $tipoCombustible->getPrecio(), 0) : 0 : $saldo_caja;

    }


}