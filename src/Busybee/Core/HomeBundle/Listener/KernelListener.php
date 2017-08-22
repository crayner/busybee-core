<?php

namespace Busybee\Core\HomeBundle\Listener;

use Doctrine\DBAL\Exception\ConnectionException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Routing\Router;

class KernelListener
{
	/**
	 * @var string
	 */
	private $route;

	/**
	 * @var Session
	 */
	private $session;

	/**
	 * TableNotFoundListener Sonstructor.
	 *
	 * @param Router $router
	 */
	public function __construct(Router $router, Session $session)
	{
		$this->route                  = [];
		$this->route['install_start'] = $router->generate('install_start');
		$this->session                = $session;
	}

	/**
	 * on Kernel Exception
	 *
	 * @param GetResponseForExceptionEvent $event
	 */
	public function onKernelException(GetResponseForExceptionEvent $event)
	{
		$exception = $event->getException();
		if ($exception instanceof ConnectionException)
		{
			$this->session->set('databaseException', $exception);
			$event->setResponse(new RedirectResponse($this->route['install_start']));
		}
	}
}