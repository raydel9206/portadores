<?php

namespace Geocuba\SoporteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('SoporteBundle:Default:index.html.twig');
    }
}
