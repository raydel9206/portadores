<?php

namespace Geocuba\PortadoresBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('PortadoresBundle:Default:index.html.twig');
    }
}
