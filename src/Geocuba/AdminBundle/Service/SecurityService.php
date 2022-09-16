<?php

namespace Geocuba\AdminBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Geocuba\AdminBundle\Entity\{Grupo, Recurso, Usuario};
use Monolog\Logger;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\{Route, RouteCollection, RouterInterface};
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class SecurityService
 * @package Geocuba\AdminBundle\Service
 */
class SecurityService
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ObjectManager
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
     * @var SessionInterface
     */
    private $session;

    /**
     * @var NotifierService
     */
    private $notifier;

    /**
     * @var string
     */
    private $appPrefix;

    /**
     * @var boolean
     */
    private $minifiedFiles;

    /**
     * SecurityHandler constructor.
     *
     * @param RouterInterface $router
     * @param ObjectManager $manager
     * @param TokenStorageInterface $tokenStorage
     * @param Logger $logger
     * @param SessionInterface $session
     * @param NotifierService $notifier
     * @param string $appPrefix
     * @param boolean $minifiedFiles
     */
    public function __construct(RouterInterface $router, ObjectManager $manager, TokenStorageInterface $tokenStorage, Logger $logger, SessionInterface $session, NotifierService $notifier, $appPrefix, $minifiedFiles)
    {
        $this->router = $router;
        $this->manager = $manager;
        $this->tokenStorage = $tokenStorage;
        $this->logger = $logger;
        $this->session = $session;
        $this->notifier = $notifier;

        $this->appPrefix = $appPrefix;
        $this->minifiedFiles = $minifiedFiles;
    }

    /**
     * TODO
     */
    public function fetchSessionData()
    {
        $token = $this->tokenStorage->getToken();
        $is_admin = $this->isAdmin();

        $valid_routes = $this->getValidRoutes();
        $allowed_routes = $is_admin ? $valid_routes : new RouteCollection(); // // la colección de rutas permitidas del usuario está vacía inicialmente, excepto para <admin>

        if (!$is_admin) {
            $groups_ids = array_map(function ($g) {
                /** @var Grupo $g */
                return $g->getId();
            }, $token->getRoles());

            /** @var Recurso[] $stored_resources */
            $stored_resources = array_unique($this->manager->getRepository('AdminBundle:Recurso')->findByGrupos($groups_ids), SORT_STRING); // el orden de la db tal vez no coincide con el orden del .yml

            foreach ($stored_resources as $stored_resource) {
                $stored_resource_key = $stored_resource->getNombre();
                $stored_route_key = $stored_resource->getRuta();

                $sf_route = $valid_routes->get($stored_route_key);
                if (!$sf_route) {
                    continue;
                }

                $sf_route_resources = $sf_route->getOption('resources');
                if (!is_array($sf_route_resources)) {
                    continue;
                }

                $sf_route_resources_keys = array_map(function ($k) {
                    return trim($k); // Eliminar espacios innecesarios
                }, array_keys($sf_route_resources)); // Sólo se necesitan los keys de los recursos

                if (!in_array($stored_resource_key, $sf_route_resources_keys)) { // si el key del recurso almacenado en db no existe en el routing.yml entonces se ignora
                    continue;
                }

                $allowed_route = $allowed_routes->get($stored_route_key); // buscar la ruta en la colección de rutas permitidas del usuario...

                if (!$allowed_route) { // si la ruta NO EXISTE en la colección de rutas permitidas entonces se clona la ruta del routing.yml y se le agrega sólo el recurso actual
                    $allowed_route = clone $sf_route;
                    $allowed_route->setOption('resources', [
                        $stored_resource_key => $sf_route_resources[$stored_resource_key]
                    ]);
                } else { // si la ruta EXISTE en la colección de rutas permitidas entonces se le agrega el recurso actual
                    $allowed_resources = $allowed_route->getOption('resources');
                    $allowed_resources[$stored_resource_key] = $sf_route_resources[$stored_resource_key];

                    // the magic: ordenar según el orden del routing.yml !!!
                    uksort($allowed_resources, function ($k1, $k2) use ($sf_route_resources_keys) {
                        return array_search($k1, $sf_route_resources_keys) > array_search($k2, $sf_route_resources_keys);
                    });

                    $allowed_route->setOption('resources', $allowed_resources);
                }

                $allowed_routes->add($stored_route_key, $allowed_route);
            }

            $_allowed_routes = $allowed_routes->getIterator()->getArrayCopy();
            $sf_routes_keys = array_keys($valid_routes->getIterator()->getArrayCopy());

            // the magic: ordenar según el orden del routing.yml !!!
            uksort($_allowed_routes, function ($k1, $k2) use ($sf_routes_keys) {
                return array_search($k1, $sf_routes_keys) > array_search($k2, $sf_routes_keys);
            });

            $allowed_routes = new RouteCollection();
            foreach ($_allowed_routes as $sf_route_key => $sf_route) {
                $allowed_routes->add($sf_route_key, $sf_route);
            }
        }

        $routes = [];
        $modules = [];
        foreach ($allowed_routes as $sf_route_key => $sf_route) {
            $routes[$sf_route_key] = ['path' => $sf_route->getPath()];

            foreach ($sf_route->getOptions() as $opt_name => $opt) {
                if ($opt_name !== 'compiler_class') {
                    $routes[$sf_route_key][trim($opt_name)] = is_string($opt) ? trim($opt) : $opt;

                    if (trim($opt_name) === 'module' && array_search(trim($opt), $modules) === false) {
                        $modules[] = $opt;
                    }
                }
            }
        }

        $notifications = count($this->notifier->getAll(true));

        $var = new \DateTime();
        $this->session->set('modules', $modules);
        $this->session->set('routes', $routes);
        $this->session->set('notifications', $notifications);
        $this->session->set('current_month', (int)$var->format('m'));
        $this->session->set('current_year', (int)$var->format('Y'));

        $this->logger->debug('Session data was loaded from routes.', ['username' => $token->getUsername(), 'modules' => $modules, 'routes' => $allowed_routes]);
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return $this->tokenStorage->getToken()->getUsername() === 'admin';
    }

    /**
     * Devuelve un arreglo de las rutas válidas para la aplicación, es decir, que comienzan con el prefijo 'app.'.
     *
     * @return RouteCollection
     */
    public function getValidRoutes()
    {
        $routes = new RouteCollection();
        foreach ($this->router->getRouteCollection() as $name => $route) {
            /** @var Route $route */
            if (mb_strpos($name, $this->appPrefix, 0, 'UTF-8') !== false && $route->hasOption('text')) { // only view routes
                $routes->add($name, $route);
            }
        }
        return $routes;
    }

    /**
     * Elimina los datos de la sesión.
     */
    public function cleanSessionData()
    {
        $this->session->remove('modules');
        $this->session->remove('routes');
        $this->session->remove('notifications');
        $this->session->remove('route');
        $this->session->remove('module');
        $this->session->remove('current_year');
        $this->session->remove('current_month');
        $this->session->remove('selected_year');
        $this->session->remove('selected_month');
    }

    /**
     * TODO
     *
     * @param string $route_name
     * @return array
     */
    public function getAllowedFiles($route_name)
    {
        $routes = $this->session->get('routes');

        $files = ['javascripts' => [], 'stylesheets' => []];
        if (array_key_exists($route_name, $routes) && array_key_exists('resources', $routes[$route_name]) && is_array($routes[$route_name]['resources'])) {
            foreach ($routes[$route_name]['resources'] as $resource) {
                if (array_key_exists('files', $resource) && is_array($resource['files'])) {
                    foreach ($resource['files'] as $file) {
                        if (($dot_pos = strrpos($file, '.')) != false) {
                            $file_extension = substr($file, $dot_pos + 1);
                            if ($file_extension === 'js' || $file_extension === 'css') {
                                $files[$file_extension === 'js' ? 'javascripts' : 'stylesheets'][] = $this->minifiedFiles === true
                                    ? str_replace($file_extension === 'js' ? '.js' : '.css', $file_extension === 'js' ? '.min.js' : '.min.css', $file)
                                    : $file;
                            }
                        }
                    }
                }
            }
        }

        $this->logger->debug("Allowed files were loaded from route '{$route_name}'.", ['username' => $this->tokenStorage->getToken()->getUsername(), 'files' => $files]);

        return $files;
    }

    /**
     * Devuelve si la ruta especificada es válida para la aplicación, es decir, que comienzan con el prefijo 'app.'.
     *
     * @param $route_name
     * @return boolean
     */
    public function isAppRoute($route_name)
    {
        return mb_strpos($route_name, $this->appPrefix, 0, 'UTF-8') !== false;
    }

    /**
     * Devuelve si la ruta especificada es una vista de la aplicación.
     *
     * @param $route_name
     * @return bool
     */
    public function isViewRoute($route_name)
    {
        if ($route = $this->router->getRouteCollection()->get($route_name)) {
            return mb_strpos($route_name, $this->appPrefix, 0, 'UTF-8') !== false && $route->hasOption('text');
        }

        return false;
    }

    /**
     * TODO
     *
     * @param $route_name
     * @return bool
     */
    public function getRouteTitle($route_name)
    {
        if ($route = $this->router->getRouteCollection()->get($route_name)) {
            return $route->getOption('text');
        }

        return false;
    }

    /**
     * Devuelve si el acceso a la ruta es permitido en la vista especificada.
     *
     * @param $route_name
     * @param $view_id
     * @return bool
     */
    public function isRouteAllowedFromView($route_name, $view_id)
    {
        $routes = $this->session->get('routes');

        if (array_key_exists($view_id, $routes)) {
            $route_data = $routes[$view_id];

            if (array_key_exists('resources', $route_data) && is_array($route_data['resources'])) {
                foreach ($route_data['resources'] as $resource) {
                    if (array_key_exists('dependencies', $resource) && is_array($resource['dependencies'])) {
                        foreach ($resource['dependencies'] as $dependency) {
                            if ($dependency === $route_name) {
                                $this->logger->debug("Access allowed to route '{$route_name}' from view '{$view_id}'", ['view_data' => $route_data]);
                                return true;
                            }
                        }
                    }
                }
            }
        }

        $this->logger->debug("Access denied to route '{$route_name}' from view '{$view_id}'.", ['username' => $this->tokenStorage->getToken()->getUsername()]);

        return false;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        $user = $this->tokenStorage->getToken()->getUser();
        return $user instanceof Usuario ? $user->getNombreCompleto() : $this->tokenStorage->getToken()->getUsername();
    }
}