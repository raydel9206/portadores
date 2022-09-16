<?php

namespace Geocuba\GISBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class RegisterGlobalsExtension
 * @package Geocuba\GISBundle\Twig
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
        return 'register_gis_globals';
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
        return [
            'ol_version' => $this->container->getParameter('ol_version'),
            'geoserver_url' => $this->container->getParameter('geoserver_url'),
        ];
    }
}