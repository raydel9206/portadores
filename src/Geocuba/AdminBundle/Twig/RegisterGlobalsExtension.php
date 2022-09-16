<?php

namespace Geocuba\AdminBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class RegisterGlobalsExtension
 * @package Geocuba\AdminBundle\Twig
 */
class RegisterGlobalsExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * GlobalExtension constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'register_globals';
    }

    /**
     * Returns a list of global variables to add to the existing list.
     *
     * @return array An array of global variables
     *
     * @deprecated since 1.23 (to be removed in 2.0), implement Twig_Extension_GlobalsInterface instead
     */
    public function getGlobals()
    {
        // TODO: InvalidArgumentException if the parameter is not defined

        return [
            'app_name' => $this->container->getParameter('app_name'),
            'app_version' => $this->container->getParameter('app_version'),
            'app_owner' => $this->container->getParameter('app_owner'),
            'app_copyright' => $this->container->getParameter('app_copyright'),
            'in_maintenance_until' => $this->container->getParameter('in_maintenance_until'),
            'extjs_version' => $this->container->getParameter('extjs_version'),
            'extjs_theme' => $this->container->getParameter('extjs_theme'),
            'login_theme' => $this->container->getParameter('login_theme'),
        ];
    }
}