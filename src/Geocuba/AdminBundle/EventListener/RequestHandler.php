<?php

namespace Geocuba\AdminBundle\EventListener;

use Geocuba\AdminBundle\Service\{NotifierService, SecurityService};
use Monolog\Logger;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\{Response};
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Templating\EngineInterface;

/**
 * Class RequestHandler
 * @package Geocuba\AdminBundle\EventListener
 */
class RequestHandler
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var SecurityService
     */
    private $securityService;

    /**
     * @var NotifierService
     */
    private $notifier;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @param SessionInterface $session
     * @param Logger $logger
     * @param TokenStorageInterface $tokenStorage
     * @param SecurityService $securityService
     * @param NotifierService $notifier
     * @param ContainerInterface $container
     */
    public function __construct(SessionInterface $session, Logger $logger, TokenStorageInterface $tokenStorage, SecurityService $securityService, NotifierService $notifier, ContainerInterface $container, EngineInterface $templating)
    {
        $this->session = $session;
        $this->logger = $logger;
        $this->tokenStorage = $tokenStorage;
        $this->securityService = $securityService;
        $this->notifier = $notifier;
        $this->container = $container;
        $this->templating = $templating;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $route_name = $request->get('_route');

        // error_log(print_r($routeKey, true));

        if (!$this->securityService->isAppRoute($route_name)) {
            $this->session->set('route', $route_name === 'homepage' ? 'homepage' : null);
            $this->session->set('module', null);

            if (!is_string($this->tokenStorage->getToken())) {
                // $this->session->set('notifications', count($this->securityHandler->getUserNotifications())); TODO
            }

            if (($route_name === 'homepage' || $route_name === 'login') && $this->_inMaintenanceMode()) {
                $this->_closeSessionByMaintenance($event);
            }

            return;
        }

        $username = $this->tokenStorage->getToken()->getUsername();

        if ($this->_inMaintenanceMode()) {
            $this->_closeSessionByMaintenance($event);
            return;
        }

        $this->session->set('client_ip', $request->getClientIp()); // $this->_getClientIpFromRequest($request)

        $this->logger->debug("Requested route '{$route_name}'.",
            ['username' => $username, 'client_ip' => $request->getClientIp(), 'request_uri' => $request->getUri()]
        );

        $allowed_routes = $this->session->get('routes', []);

        if ($this->securityService->isViewRoute($route_name)) { // its a view route
            if (array_key_exists($route_name, $allowed_routes)) {
                $this->session->set('route', $route_name);
                $this->session->set('module', $allowed_routes[$route_name]['module']);
                $this->session->set('notifications', count($this->notifier->getAll(true, null, null)));
            } else {
                $this->session->set('route', null);
                $this->session->set('module', null);
                $this->session->set('notifications', null);

                $this->logger->warning("User access to the view '{$route_name}' was denied.",
                    ['username' => $username, 'client' => $request->getClientIp(), 'request_uri' => $request->getUri(), 'allowed_routes' => array_keys($allowed_routes)]
                );

                $route_url = $this->container->get('router')->generate($route_name);
                throw new AccessDeniedHttpException(sprintf("User access to the view '{$route_url}' was denied.")); // get route url
            }
        } else { // its a data route
            $view_id = $request->get('view_id');
            if (empty($view_id)) {
                // http://ca2.php.net/manual/en/ini.core.php#ini.post-max-size
                $view_id = '<NULL>';
            }

            if (!$this->securityService->isRouteAllowedFromView($route_name, $view_id)) {
                $this->logger->warning("User access to the route '{$route_name}' from view {$view_id} was denied.",
                    ['username' => $username, 'client_ip' => $request->getClientIp(), 'request_uri' => $request->getUri(), 'allowed_routes' => array_keys($allowed_routes)]
                );

                $message = !$this->container->get('kernel')->isDebug() ? 'Access Denied.' : sprintf("Access Denied. The access to route '%s' is not allowed from view '%s'.", $route_name, $view_id);
                $event->setResponse(new Response($message, Response::HTTP_FORBIDDEN)); // TODO: trans
            }
        }
    }

    /**
     * @return bool
     */
    private function _inMaintenanceMode()
    {
        return $this->container->hasParameter('in_maintenance') && $this->container->getParameter('in_maintenance') === true;
    }

    /**
     * @param GetResponseEvent $event
     */
    private function _closeSessionByMaintenance($event)
    {
        $event->setResponse(new Response($this->templating->render('::maintenance.html.twig'), Response::HTTP_SERVICE_UNAVAILABLE));
        $event->stopPropagation();

        if ($this->container->hasParameter('invalidate_session_in_maintenance') && $this->container->getParameter('invalidate_session_in_maintenance') === true) {
            $event->getRequest()->getSession()->save();
            $this->tokenStorage->setToken(null);
        }
    }
}
