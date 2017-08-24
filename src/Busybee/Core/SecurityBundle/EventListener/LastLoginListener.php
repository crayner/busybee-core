<?php

namespace Busybee\Core\SecurityBundle\EventListener;

use Busybee\Core\SecurityBundle\BusybeeSecurityEvents;
use Busybee\Core\SecurityBundle\Event\UserEvent;
use Busybee\Core\SecurityBundle\Model\UserManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\HttpFoundation\Session\Session;

class LastLoginListener implements EventSubscriberInterface
{
	protected $userManager;
	protected $session;

	public function __construct(UserManagerInterface $userManager, Session $session)
	{
		$this->userManager = $userManager;
		$this->session     = $session;
	}

	public static function getSubscribedEvents()
	{
		return array(
			BusybeeSecurityEvents::SECURITY_IMPLICIT_LOGIN => 'onImplicitLogin',
			SecurityEvents::INTERACTIVE_LOGIN              => 'onSecurityInteractiveLogin',
		);
	}

	public function onImplicitLogin(UserEvent $event)
	{
		$user = $event->getUser();

		$user->setLastLogin(new \DateTime());
		$this->userManager->updateUser($user);
	}

	public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
	{
		$user = $event->getAuthenticationToken()->getUser();

		$user->setLastLogin(new \DateTime());
		$this->userManager->updateUser($user);

		if (null !== $user->getLocale())
		{
			$this->session->set('_locale', $user->getLocale());
		}


	}
}
