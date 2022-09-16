<?php

namespace Geocuba\Utils;

trait ViewActionTrait
{
    public function viewAction()
    {
        return ['tpl' => '::base.html.twig']; // a non response will dispatch a kernel's view event
//        return ['tpl' => '::base.html.twig', 'extra_params' => ['hello' => 'somedata']]; // a non response will dispatch a kernel's view event
    }
}