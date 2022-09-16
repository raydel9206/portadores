<?php

namespace Geocuba\AdminBundle\EventListener;

use Geocuba\AdminBundle\Service\SecurityService;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\{RedirectResponse, Request, Response};
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\{AuthenticationFailureHandlerInterface,
    AuthenticationSuccessHandlerInterface};
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class AuthenticationHandler
 * @package Geocuba\AdminBundle\EventListener
 */
class AuthenticationHandler implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface, LogoutSuccessHandlerInterface
{
    /**
     * @var string
     */
    const TARGET_PATH_KEY = '_security.secured_area.target_path';  #where "secured_area" is your firewall name

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var SecurityService
     */
    private $security;

    /**
     * @param RouterInterface $router
     * @param Logger $logger
     * @param TokenStorageInterface $tokenStorage
     * @param TranslatorInterface $translator
     * @param SecurityService $security
     */
    public function __construct(RouterInterface $router, Logger $logger, TokenStorageInterface $tokenStorage, TranslatorInterface $translator, SecurityService $security)
    {
        $this->router = $router;
        $this->logger = $logger;
        $this->tokenStorage = $tokenStorage;
        $this->translator = $translator;
        $this->security = $security;
    }

    /**
     * Creates a Response object to send upon a successful logout.
     *
     * @param Request $request
     * @param TokenInterface $token
     * @return RedirectResponse|Response
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $session = $request->getSession();
        $url = $this->router->generate('homepage');
        // Redirect to requested page after login
        if ($session->has($this::TARGET_PATH_KEY)) { // check if the referrer session key has been set
//            $url = $session->get($this::TARGET_PATH_KEY); // set the url based on the link they were trying to access before being authenticated
            $session->remove($this::TARGET_PATH_KEY); // remove the session key
        } else { // if the referrer key was never set, redirect to the last route (if exists in the cookie) or homepage
            // error_log(print_r($request, true));
//            try {
//                $url = $this->router->generate($request->cookies->has($token->getUsername()) ? $request->cookies->get($token->getUsername()) : 'homepage');
//            } catch (RouteNotFoundException $e) {
//                $url = $this->router->generate('homepage');
//            }
        }

        $this->security->fetchSessionData();

        $this->logger->info('User has been login.', ['username' => $token->getUsername(), 'client_ip' => $request->getClientIp(), 'target_url' => $url]);

        return new RedirectResponse($url);
    }

    /**
     * This is called when an interactive authentication attempt fails. This is
     * called by authentication listeners inheriting from
     * AbstractAuthenticationListener.
     *
     * @param Request $request
     * @param AuthenticationException $exception
     *
     * @return Response The response to return, never null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        /** @var Session $session */
        $session = $request->getSession();

        $this->logger->error($exception->getMessageKey(), ['username' => $request->get('_username'), 'client_ip' => $request->getClientIp(), 'exception' => $exception->getMessage()]);

        $session->getFlashBag()->add('notice', ['type' => 'danger', 'message' => $this->translator->trans($exception->getMessageKey())]);

        return new RedirectResponse('login');
    }

    /**
     * Creates a Response object to send upon a successful logout.
     *
     * @param Request $request
     *
     * @return Response never null
     */
    public function onLogoutSuccess(Request $request)
    {
        if ($request->hasPreviousSession()) {
            $this->security->cleanSessionData();

            $username = $this->tokenStorage->getToken() ? $this->tokenStorage->getToken()->getUsername() : null;
            $message = $this->translator->trans($request->get('expired') ? 'The session has expired due to inactivity.' : 'User has been logout.');

            $this->logger->info($message, ['username' => $username, 'client_ip' => $request->getClientIp(), 'route_cookie' => $username ? $request->cookies->get($username) : null]);

            $request->getSession()->getFlashBag()->add('notice', ['type' => 'success', 'message' => $message]);
        }

        return new RedirectResponse('login');
    }
}