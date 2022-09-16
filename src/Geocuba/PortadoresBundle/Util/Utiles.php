<?php
/**
 * Created by PhpStorm.
 * User: asisoftware12
 * Date: 3/6/2019
 * Time: 3:20 PM
 */

namespace Geocuba\PortadoresBundle\Util;


class Utiles
{
    public static function findDomainByChildren($em, $unidad, &$unidadesArray){
        $unidadesArray[] = $unidad->getId();
        $unidadesChild = $em->getRepository('PortadoresBundle:Unidad')->findBy(['padreid' => $unidad->getId(), 'visible' => true]);
        if ($unidadesChild){
            foreach ($unidadesChild as $unidadChild){
                self::findDomainByChildren($em, $unidadChild, $unidadesArray);
            }
        }
    }
}