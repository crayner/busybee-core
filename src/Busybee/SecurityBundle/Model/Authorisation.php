<?php
namespace Busybee\SecurityBundle\Model ;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\HttpFoundation\RedirectResponse ;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface ;
use Symfony\Component\HttpFoundation\JsonResponse ;

class Authorisation extends AuthorizationChecker
{
    private $router;
    private $session;
    private $translator;
    private $route;
    private $requestUri;
    private $container;
    private $response;
    private $ip_test;
    private $ip;

    private $key = '_security.secured_area.target_path';


    public function __construct(TokenStorageInterface $tokenStorage, AuthenticationManagerInterface $authenticationManager, AccessDecisionManagerInterface $accessDecisionManager, ContainerInterface $container)
    {

        parent::__construct($tokenStorage, $authenticationManager, $accessDecisionManager);
        $this->router = $container->get('router');
        $this->session = $container->get('session');
        $this->translator = $container->get('translator');
        $this->route = $container->get('request_stack')->getCurrentRequest()->attributes->get('_route');
        $this->requestUri = $container->get('request_stack')->getCurrentRequest()->server->get('REQUEST_URI');
        $this->container = $container;
        $this->response = NULL;
        $this->ip_test = $container->get('security.failure.repository')->testRemoteAddress($container->get('request_stack')->getCurrentRequest()->server->get('REMOTE_ADDR'));
        $this->ip = $container->get('request_stack')->getCurrentRequest()->server->get('REMOTE_ADDR');
    }

    public function redirectAuthorisation($role, $message = 'security.authorisation.not_valid', $messParams = [])
    {
        return $this->checkAuthorisation($role, $message, $messParams);
    }

    /**
     * @return true or a response.
     */
    private function checkAuthorisation($role, $message = 'security.authorisation.not_valid', $messParams = [])
    {
        $this->response = true;
        $this->session->remove( $this->key );
        $this->session->set('_url', $this->router->generate('home_page'));
        $this->session->set('_authorised', true);
        if ($role === 'IS_AUTHENTICATED_FULLY' && in_array($this->route, array('home_page')))
            return $this->response;
        elseif (parent::isGranted($role))
            return $this->response;
        elseif ($this->session->get('_timeout')) {
            $this->session->set($this->key, $this->requestUri);
            $this->session->getFlashBag()->add(
                'info',
                $this->translator->trans('security.authorisation.required', [], 'BusybeeSecurityBundle')
            );
            $url = $this->router->generate('busybee_security_login');
            $this->session->set('_url', $url);
            $this->response = new RedirectResponse($url);
            return $this->response;
        } else {
            if (false === $this->ip_test) {
                $this->session->getFlashBag()->add(
                    'warning',
                    $this->translator->trans('security.authorisation.blocked_ip', ["%remoteIP%" => $this->ip], 'BusybeeSecurityBundle')
                );
                $url = $this->router->generate('home_page');
                $this->response = new RedirectResponse($url);
                return $this->response ;
            }
            $this->session->set($this->key, $this->requestUri);
            $this->session->set('_authorised', false);
            $url = $this->router->generate('home_page');
            if (! parent::isGranted('ROLE_USER')){
                $this->session->getFlashBag()->add(
                    'info',
                    $this->translator->trans('security.authorisation.required', [], 'BusybeeSecurityBundle')
                );
                $url = $this->router->generate('busybee_security_login');
            } else
                $this->session->getFlashBag()->add(
                    'warning',
                    $this->translator->trans($message, $messParams, 'BusybeeSecurityBundle')
                );
            $this->response = new RedirectResponse($url);
            return $this->response;
        }
    }

    public function ajaxAuthorisation($role, $request)
    {
        $this->checkAuthorisation($role);
        if ($request->getMethod() !== "POST")
            return $this->response;
        if ($this->session->get('_authorised') and ! $this->session->get('_timeout'))
            return true;
        $this->response = new JsonResponse(
            array(
                'form' => $this->container->get('templating')->render('BusybeeSecurityBundle:Ajax:login_content.html.twig',
                    array(
                        'redirect'			=> $this->session->get('_url'),
                    )
                )
            ), 200
        );
        return $this->response;
    }
}