<?php
/**
 * Created by PhpStorm.
 * User: adonis
 * Date: 24/04/14
 * Time: 17:06
 */

namespace Geocuba\AdminBundle\Util;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Util {
    public static function GetJS($_user, $_module, $em = null){
        $_arrjs = array();
        if($_user == 'Dev'){
//            print_r($_module);die;
            $_submodules = $em->getRepository('AdminBundle:Submodules')->findByName($_module);
//            print_r($_submodules);die;
            $_tempArr = explode(',',$_submodules[0]->getJs());
            foreach ($_tempArr as $_temp)
                if(array_search($_temp,$_arrjs) === false)
                    $_arrjs[] = $_temp;
            $_functions = $em->getRepository('AdminBundle:Funtions')->getFunctionsForModule($_module);
            foreach ($_functions as $_function){
                $_tempArr = explode(',',$_function->getJs());
                foreach ($_tempArr as $_temp)
                    if(array_search($_temp,$_arrjs) === false)
                        $_arrjs[] = $_temp;
            }
        }
        else{
            $_roles = $_user->getRolesid();
            foreach ($_roles as $_role) {
                $_submodules = $_role->getSubmodulesid();
                foreach ($_submodules as $_submodule)
                    if($_submodule->getName() == $_module){
                        $_tempArr = explode(',',$_submodule->getJs());
                        foreach ($_tempArr as $_temp)
                            if(array_search($_temp,$_arrjs) === false)
                                $_arrjs[] = $_temp;
                    }
                $_functions = $_role->getFuntionsid();
                foreach ($_functions as $_function)
                    if($_function->getSubmodulesid()->getName() == $_module){
                        $_tempArr = explode(',',$_function->getJs());
                        foreach ($_tempArr as $_temp)
                            if(array_search($_temp,$_arrjs) === false)
                                $_arrjs[] = $_temp;
                    }
            }
        }
        return $_arrjs;
    }
    public static function GetCss($_user, $_module, $em = null){
        if($_user == 'Dev'){
            $_submodules = $em->getRepository('AdminBundle:Submodules')->findByName($_module);
            return $_submodules[0]->getCss();
        }
        else{
            $_roles = $_user->getRolesid();
            foreach ($_roles as $_role) {
                $_submodules = $_role->getSubmodulesid();
                foreach ($_submodules as $_submodule)
                    if($_submodule->getName() == $_module)
                        return $_submodule->getCss();
            }
        }
    }
    public static function setExcelStreamedResponseHeaders($response, $filename)
    {
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=0');
        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename));
        return $response;
    }
} 