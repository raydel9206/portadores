<?php

namespace Geocuba\AdminBundle\Twig;

use Geocuba\AdminBundle\Entity\Usuario;
use Geocuba\AdminBundle\Service\NotifierService;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class LoadSessionDataFilterExtension
 * @package Geocuba\AdminBundle\Twig
 */
class LoadSessionDataFilterExtension extends \Twig_Extension
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var NotifierService
     */
    private $notifierService;

    /**
     * SessionDataFilterExtension constructor.
     * @param RouterInterface $router
     * @param Logger $logger
     * @param NotifierService $notifierService
     * @param ContainerInterface $container
     */
    public function __construct(RouterInterface $router, Logger $logger, NotifierService $notifierService, ContainerInterface $container)
    {
        $this->router = $router;
        $this->logger = $logger;
        $this->notifierService = $notifierService;
        $this->container = $container;
    }

    /**
     * @param ContainerInterface $container
     * @param RouterInterface $router
     * @param NotifierService $notifier
     * @param SessionInterface $session
     * @param Usuario $user
     * @param boolean $debug
     * @return array
     */
    public static function loadData($container, $router, $notifier, $session, $user, $debug)
    {
        $modules = $session->get('modules');
        $module = is_null($session->get('module'))
            ? (is_array($modules) && !empty($modules) ? $modules[0] : null)
            : $session->get('module');

        $session_data = [
            'user' => [
                'id' => $user instanceof Usuario ? $user->getId() : 'admin',
                'username' => $user instanceof Usuario ? $user->getUsuario() : 'admin',
                'fullname' => $user instanceof Usuario ? $user->getNombreCompleto() : null,
                'created_at' => date('h:i A d/m/Y', $session->getMetadataBag()->getCreated()), // TODO: timezone
                'email' => $user instanceof Usuario ? $user->getEmail() : null,
                'unidad_id' => $user instanceof Usuario ? $user->getUnidad()->getId() : 'apr_portadores_0',
            ],
            'app_name' => self::_getParameter($container, 'app_name', 'GeoApp'),
            'base_url' => $router->generate('homepage'),
            'routes' => $session->get('routes'),
            'route' => $session->get('route'),
            'modules' => $modules,
            'module' => $module,
            'notifications' => count($notifier->getAll(true, null, null)),
            'notifications_interval' => self::_getParameter($container, 'notifications_interval', 300),
            'session_timeout' => self::_getParameter($container, 'session_timeout', 600),
            'ajax_timeout' => self::_getParameter($container, 'ajax_timeout', 60), // ExtJS defaults is 30000 ms (30 sec).,
            'verbose' => $debug,
            'current_month' => $session->get('current_month'),
            'current_year' =>  $session->get('current_year'),
            'selected_month' => $session->get('selected_month'),
            'selected_year' => $session->get('selected_year'),
            'min_month' => $session->get('min_month'),
            'min_year' => $session->get('min_year'),
        ];

        return $session_data;
    }

    /**
     * @param ContainerInterface $container
     * @param string $name
     * @param mixed $default_value
     * @return bool|mixed
     */
    private static function _getParameter($container, $name, $default_value)
    {
        return $container->hasParameter($name) ? $container->getParameter($name) : $default_value;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('load_data', array($this, 'loadSessionData')),
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'load_data';
    }

    /**
     * @param \Symfony\Bridge\Twig\AppVariable $app
     * @return string
     */
    public function loadSessionData($app)
    {
        return json_encode(self::loadData($this->container, $this->router, $this->notifierService, $app->getSession(), $app->getUser(), $app->getDebug()));
    }
}