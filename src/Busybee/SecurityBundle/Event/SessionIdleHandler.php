<?php
namespace Busybee\SecurityBundle\Event ;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SessionIdleHandler
{

    protected $session;
    protected 	$securityToken;
    protected 	$router;
    protected 	$translator;
    protected 	$maxIdleTime;
    private $cookieNames;

    public function __construct(Container $container, TokenStorageInterface $securityToken, $maxIdleTime = 900)
    {
        $this->session = $container->get('session');
        $this->securityToken = $securityToken;
        $this->router = $container->get('router');
        $this->translator = $container->get('translator');
        $this->maxIdleTime = $maxIdleTime;
		$this->cookieNames = array(
			'PHPSESSID',
			$container->getParameter('session_name'),
			$container->getParameter('session_remember_me_name'),
		);
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
		$this->session->set('_timeout', false);

        if (HttpKernelInterface::MASTER_REQUEST != $event->getRequestType())
            return ;

		if ($this->securityToken->getToken() === NULL)
			return ;		

		if ( $this->securityToken->getToken()->getUser() === 'anon.' )
			return ;

        if ($this->maxIdleTime > 0) {

            $lapse = time() - $this->session->getMetadataBag()->getLastUsed();

            if ($lapse > $this->maxIdleTime)
			{

                $this->securityToken->setToken(NULL);
				$this->session->set('_timeout', true);
				
                $this->session->getFlashBag()->set(
					'info', 
					$this->translator->trans('security.session.timeout', array('%hours%' => gmdate('H', $lapse), '%minutes%' => gmdate('i', $lapse)), 'BusybeeSecurityBundle')
				);
				
				return ;
            }
        }
    }

}