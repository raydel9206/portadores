<?php

namespace Geocuba\AdminBundle\Controller;

use Geocuba\AdminBundle\Twig\LoadSessionDataFilterExtension;
use Geocuba\Utils\{ViewActionTrait};
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class DefaultController
 * @package Geocuba\AdminBundle\Controller
 */
class DefaultController extends Controller
{
    use ViewActionTrait;

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction()
    {
        return $this->render('::login.html.twig');
    }

    /**
     * @param Request $request
     * @param string $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function helpAction(Request $request, $page)
    {
        $page = $page !== '/' ? rtrim($page, '/') : $page;
        return $this->redirect($request->getBasePath() . $request->getPathInfo() . ($page === '.+' || $page === '/' ? '/index.htm' : ''), Response::HTTP_MOVED_PERMANENTLY);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function reloadRoutesAction(Request $request)
    {
        $container = $this->get('service_container');

        if (!$container->get('kernel')->isDebug()) {
            throw new AccessDeniedException();
        }

        $this->get('app.service.security')->fetchSessionData();
        $session_data = LoadSessionDataFilterExtension::loadData($container, $this->get('router'), $this->get('app.service.notifier'), $request->getSession(), $this->getUser(), $this->getParameter('kernel.debug'));

        return new JsonResponse(['success' => true, 'data' => $session_data]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showMaintenanceAction()
    {
        $container = $this->get('service_container');

        if (!$container->get('kernel')->isDebug()) {
            throw new AccessDeniedException();
        }

        return $this->render('::maintenance.html.twig');
    }

    /**
     * https://stackoverflow.com/questions/27992992/i-need-list-of-all-class-name-of-font-awesome
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function loadFontAwesomeIconsAction()
    {
        $metadata = file_get_contents($this->get('kernel')->getRootDir() . "/../web/assets/vendor/fontawesome-free@5.4.1/metadata/icons.json");
        $json = json_decode($metadata);

        $icons = [];
        foreach ($json as $icon => $value) {
            foreach ($value->styles as $style) {
                $icons[] = [
                    'clazz' => 'fa' . substr($style, 0, 1) . ' fa-' . $icon,
                    'unicode' => $value->unicode
                ];
            }
        }

        return new JsonResponse(['success' => true, 'data' => $icons, 'version' => '5.4.1']);
    }
}
