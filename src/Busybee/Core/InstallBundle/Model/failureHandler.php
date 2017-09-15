<?php

namespace Busybee\Core\InstallBundle\Model;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class failureHandler extends DefaultAuthenticationFailureHandler
{

	private $container;

	public function __construct(HttpKernelInterface $httpKernel, HttpUtils $httpUtils, array $options, Container $container)
	{
		$this->container = $container;
		$logger          = $this->container->get('busybee_core_template.model.logger');

		parent::__construct($httpKernel, $httpUtils, $options, $logger);
	}

	public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
	{
		if ($request->isXmlHttpRequest())
		{
			$response = new JsonResponse(array('success' => false, 'message' => $exception->getMessage()));
		}
		else
		{
			$response = parent::onAuthenticationFailure($request, $exception);
		}
		$this->container->get('session')->getFlashBag()->add(
			'danger',
			$this->container->get('translator')->trans(
				'security.authentication.failure',
				array(
					'%name%'     => $request->request->get('_username'),
					'%remoteIP%' => $request->server->get('REMOTE_ADDR'),
				),
				'BusybeeInstallBundle')
		);
		$context = array();
		$this->logger->warning(
			$this->container->get('translator')->trans(
				'security.authentication.failure',
				array(
					'%name%'     => $request->request->get('_username'),
					'%remoteIP%' => $request->server->get('REMOTE_ADDR'),
				),
				'BusybeeInstallBundle'),
			$context);
		if ($this->incrementFailureCount($request->server->get('REMOTE_ADDR')) > 5)
			$response = new RedirectResponse($this->container->get('router')->generate('homepage'));
		if (false === $this->container->get('busybee_core_security.repository.failure_repository')->testRemoteAddress($request->server->get('REMOTE_ADDR')))
		{
			$this->container->get('session')->getFlashBag()->add
			(
				'warning',
				$this->container->get('translator')->trans
				(
					'security.authorisation.blocked_ip', array
				(
					"%remoteIP%" => $request->server->get('REMOTE_ADDR'),
				),
					'BusybeeInstallBundle')
			);
			$context = array();
			$this->logger->warning
			(
				$this->container->get('translator')->trans
				(
					'security.authorisation.blocked_ip',
					array
					(
						'%remoteIP%' => $request->server->get('REMOTE_ADDR'),
					),
					'BusybeeInstallBundle'
				),
				$context
			);
		}

		return $response;
	}

	private function incrementFailureCount($ip)
	{
		$failRep = $this->container->get('busybee_core_security.repository.failure_repository');
		if ($failRep->exceptedIP($ip))
			return 0;
		$failure = $failRep->findOneBy(array('address' => $ip));
		if (empty($failure))
			$failure = $failRep->createNew();
		$em = $this->container->get('doctrine')->getManager();
		$failure->incFailures();
		$failure->setAddress($ip);
		$em->persist($failure);
		$em->flush();

		return $failure->getFailures();
	}
}