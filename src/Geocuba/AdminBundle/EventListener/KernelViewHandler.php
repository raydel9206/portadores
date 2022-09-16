<?php

namespace Geocuba\AdminBundle\EventListener;

use Geocuba\AdminBundle\Service\SecurityService;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\{
    Cookie, Response
};
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class KernelViewHandler
 * @package Geocuba\AdminBundle\EventListener
 */
class KernelViewHandler
{
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
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * KernelViewHandler constructor.
     * @param Logger $logger
     * @param TokenStorageInterface $tokenStorage
     * @param SecurityService $securityService
     * @param EngineInterface $templating
     * @param ContainerInterface $container
     */
    public function __construct(Logger $logger, TokenStorageInterface $tokenStorage, SecurityService $securityService, EngineInterface $templating, ContainerInterface $container)
    {
        $this->logger = $logger;
        $this->tokenStorage = $tokenStorage;
        $this->securityService = $securityService;
        $this->templating = $templating;
        $this->container = $container;
    }

    /**
     * Transform a non-Response return value from a controller into a Response.
     *
     * When setting a response for the kernel.view event, the propagation is stopped. This means listeners with lower priority won't be executed.
     *
     * @param GetResponseForControllerResultEvent $event
     */
    public function onView(GetResponseForControllerResultEvent $event)
    {
        $routeKey = $event->getRequest()->get('_route');
        $controllerResult = $event->getControllerResult();
        $username = $this->tokenStorage->getToken()->getUsername();

        if ($routeKey === 'homepage' || $this->securityService->isAppRoute($routeKey)) {
            if (!is_array($controllerResult)) {
                throw new \LogicException('The controller must return a response. Did you forget to add a return statement somewhere in your controller?');
            }

            $parameters = [];
            // Catch extra params from controller
            if (array_key_exists('extra_params', $controllerResult)) { // Se puede definir el parámetro 'extra_params' para enviar parámetros a la vista.
                $parameters['extra_params'] = $controllerResult['extra_params'];
            }

            if ($routeKey === 'homepage') {
                // Clear the cookie that stores the current route
                $response = new Response($this->templating->render($controllerResult['tpl'], $parameters));
                $response->headers->clearCookie($this->tokenStorage->getToken()->getUsername());

                $controllerResult = $response;

            } else if ($this->securityService->isAppRoute($routeKey)) {
                $parameters['title'] = $this->securityService->getRouteTitle($routeKey) . ' | ' . $this->container->getParameter('app_name');
                $parameters = array_merge($parameters, $this->securityService->getAllowedFiles($routeKey));

                // Set the cookie that stores the current route: expires in 1 months and it's accesible from JS.
                $response = new Response($this->templating->render($controllerResult['tpl'], $parameters));
                $response->headers->setCookie(new Cookie($username, $routeKey, new \DateTime('+30 days', new \DateTimeZone(date_default_timezone_get())), '/', null, false, false));

                $controllerResult = $response;

                $request = $event->getRequest();
                $this->logger->info("Matched route view '{$routeKey}'.", ['username' => $username, 'client_ip' => $request->getClientIp(), 'request_uri' => $request->getUri(), 'files' => $parameters]);
            }
        }

        $event->setResponse($controllerResult);
    }
}