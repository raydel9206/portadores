<?php

namespace Geocuba\AdminBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Exception\{AccountExpiredException,
    AccountStatusException,
    DisabledException,
    LockedException};
use Symfony\Component\Security\Core\User\{AdvancedUserInterface, UserCheckerInterface, UserInterface};

/**
 * Class UserChecker
 * @package Geocuba\AdminBundle\EventListener
 */
class UserChecker implements UserCheckerInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * UserChecker constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Checks the user account before authentication.
     *
     * @param UserInterface $user a UserInterface instance
     *
     * @throws AccountStatusException
     */
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof AdvancedUserInterface) {
            return;
        }

        // \Symfony\Component\Security\Core\User\UserChecker
//        if (!$user->isAccountNonLocked() || ($user->getUsername() === 'admin' && !$this->container->get('kernel')->isDebug())) {
//            $ex = new LockedException('Account is locked.');
//            $ex->setUser($user);
//            throw $ex;
//        }

        if (!$user->isAccountNonLocked()) {
            $ex = new LockedException('Account is locked.');
            $ex->setUser($user);
            throw $ex;
        }

        if (!$user->isEnabled()) {
            $ex = new DisabledException('Account is disabled.');
            $ex->setUser($user);
            throw $ex;
        }

        if (!$user->isAccountNonExpired()) {
            $ex = new AccountExpiredException('Account has expired.');
            $ex->setUser($user);
            throw $ex;
        }
    }

    /**
     * Checks the user account after authentication.
     *
     * @throws AccountStatusException
     */
    public function checkPostAuth(UserInterface $user)
    {
        // TODO: Implement checkPostAuth() method.
    }
}