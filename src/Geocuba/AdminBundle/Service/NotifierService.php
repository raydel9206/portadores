<?php

namespace Geocuba\AdminBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Geocuba\AdminBundle\Entity\{Notificacion, Usuario};
use Geocuba\Utils\Constants;
use Monolog\Logger;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class NotifierService
 * @package Geocuba\AdminBundle\Service
 */
class NotifierService
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * SecurityHandler constructor.
     *
     * @param EntityManagerInterface $manager
     * @param TokenStorageInterface $tokenStorage
     * @param Logger $logger
     */
    public function __construct($manager, $tokenStorage, $logger)
    {
        $this->manager = $manager;
        $this->tokenStorage = $tokenStorage;
        $this->logger = $logger;
    }

    /**
     * @param bool $only_session
     * @param null $limit
     * @param null $offset
     * @return array|\Doctrine\Common\Persistence\ObjectRepository
     */
    public function getAll($only_session, $limit = null, $offset = null)
    {
        $notificacion_er = $this->manager->getRepository('AdminBundle:Notificacion');
        $token = $this->tokenStorage->getToken();
        $is_admin = $token->getUsername() === 'admin';

        $notifications = $notificacion_er->findAllBy($only_session, $is_admin ? null : $token->getUser(), $limit, $offset);
        foreach ($notifications as &$notificacion) {
            /** @var Notificacion $notificacion */
            $notificacion = $notificacion->toArray();
        }

        $this->logger->debug('Notifications were loaded from database.', ['username' => $is_admin ? 'admin' : $token->getUsername()]);

        return $notifications;
    }

    /**
     * @param string $message
     * @return Notificacion[]
     */
    public function notifyAll($message)
    {
        $usuarios = $this->manager->getRepository('AdminBundle:Usuario')->findByActivo(true);

        if (empty($usuarios)) {
            return null;
        }

        $notificaciones = [];

        $this->manager->transactional(function () use ($message, $usuarios, &$notificaciones) {
            /** @var EntityManagerInterface $em */
            $em = func_get_arg(0);

            foreach ($usuarios as $usuario) {
                /** @var Usuario $usuario */

                $notificacion = new Notificacion();
                $em->persist(
                    $notificacion
                        ->setMensaje($message)
                        ->setTipo(Constants::NOTIFICACION_GLOBAL)
                        ->setFechaCreacion(new \DateTime())
                        ->setUsuario($usuario)
                );

                $notificaciones[] = $notificacion;
            }
        });

        return $notificaciones;
    }

    /**
     * @param string $message
     * @param array $groups_ids
     * @return Notificacion[]
     */
    public function notifyGroups($message, $groups_ids)
    {
        $usuarios = $this->manager->getRepository('AdminBundle:Usuario')->findAllByGrupos($groups_ids);

        if (empty($usuarios)) {
            return null;
        }

        $notificaciones = [];

        $this->manager->transactional(function () use ($message, $usuarios, &$notificaciones) {
            /** @var EntityManagerInterface $em */
            $em = func_get_arg(0);

            foreach ($usuarios as $usuario) {
                /** @var Usuario $usuario */
                $notificacion = new Notificacion();

                $em->persist(
                    $notificacion
                        ->setMensaje($message)
                        ->setTipo(Constants::NOTIFICACION_GRUPO)
                        ->setFechaCreacion(new \DateTime())
                        ->setUsuario($usuario)
                );

                $notificaciones[] = $notificacion;
            }
        });

        return $notificaciones;
    }

    /**
     * @param string $message
     * @param array $users_ids
     * @return Notificacion[]
     */
    public function notifyUsers($message, $users_ids)
    {
        $usuario_er = $this->manager->getRepository('AdminBundle:Usuario');

        $usuarios = array_map(function ($usuario_id) use ($usuario_er) {
            return $usuario_er->findOneBy(['id' => $usuario_id, 'activo' => true]);
        }, $users_ids);

        $usuarios = array_filter($usuarios);// removes null

        if (empty($usuarios)) {//TODO: remove null
            return null;
        }

        $notificaciones = [];

        $this->manager->transactional(function () use (&$notificaciones, $message, $usuarios) {
            /** @var EntityManagerInterface $em */
            $em = func_get_arg(0);

            foreach ($usuarios as $usuario) {
                $notificacion = (new Notificacion());

                $em->persist(
                    $notificacion
                        ->setMensaje($message)
                        ->setTipo(Constants::NOTIFICACION_USUARIO)
                        ->setFechaCreacion(new \DateTime())
                        ->setUsuario($usuario)
                );

                $notificaciones[] = $notificacion;
            }
        });

        return $notificaciones;
    }
}