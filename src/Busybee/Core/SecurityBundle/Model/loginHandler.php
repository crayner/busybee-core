<?php

namespace Busybee\Core\SecurityBundle\Model;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Routing\RouterInterface;
use Doctrine\ORM\EntityManager;


class loginHandler implements AuthenticationSuccessHandlerInterface
{

	private static $key;
	private $router;
	private $container;

	public function __construct(RouterInterface $router, EntityManager $em, $container)
	{

		self::$key = '_security.secured_area.target_path';

		$this->router    = $router;
		$this->em        = $em;
		$this->session   = $container->get('session');
		$this->container = $container;

	}

	public function onAuthenticationSuccess(Request $request, TokenInterface $token)
	{

		$user_entity = $token->getUser();

		if (!$user_entity->getChangePassword())
		{

			$route = $this->router->generate('home_page');

		}
		else
		{

			$this->session->getFlashBag()->add('warning', 'Your password must be changed now');

			$route = $this->router->generate('security_user_edit');

		}

		//check if the referer session key has been set
		if ($this->session->has(self::$key))
		{

			//set the url based on the link they were trying to access before being authenticated
			$route = $this->session->get(self::$key);

			//remove the session key
			$this->session->remove(self::$key);
			//if the referer key was never set, redirect to a default route

		}
		else
		{

			$route = $this->router->generate('home_page');

		}

		$this->clearFailureCount($request->server->get('REMOTE_ADDR'));

		return new RedirectResponse($route);

	}

	private function clearFailureCount($ip)
	{
		$failRep = $this->container->get('security.failure.repository');
		if ($failRep->exceptedIP($ip))
			return;
		$failure = $failRep->findOneBy(array('address' => $ip));
		if (empty($failure))
			return;
		$em = $this->container->get('doctrine')->getManager();
		$em->remove($failure);
		$em->flush();

		return;
	}
}