<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Busybee\Core\SecurityBundle\Controller;

use Busybee\Core\SecurityBundle\Google\Google;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AuthenticationController extends Controller
{
	use \Busybee\Core\SecurityBundle\Security\DenyAccessUnlessGranted;

	public function loginAction(Request $request)
	{
		/** @var $session \Symfony\Component\HttpFoundation\Session\Session */
		$session = $this->get('session');
		$this->clearSession();

		if (false === $this->get('security.failure.repository')->testRemoteAddress($request->server->get('REMOTE_ADDR')))
		{
			$session->getFlashBag()->add(
				'warning',
				$this->get('translator')->trans('security.authorisation.blocked_ip', ["%remoteIP%" => $request->server->get('REMOTE_ADDR')], 'BusybeeSecurityBundle')
			);
			$url = $this->generateUrl('home_page');

			return new RedirectResponse($url);
		}

		$authErrorKey    = Security::AUTHENTICATION_ERROR;
		$lastUsernameKey = Security::LAST_USERNAME;

		// get the error if any (works with forward and redirect -- see below)
		if ($request->attributes->has($authErrorKey))
		{
			$error = $request->attributes->get($authErrorKey);
		}
		elseif (null !== $session && $session->has($authErrorKey))
		{
			$error = $session->get($authErrorKey);
			$session->remove($authErrorKey);
		}
		else
		{
			$error = null;
		}

		if (!$error instanceof AuthenticationException)
			$error = null; // The value does not come from the security component.

		// last username entered by the user
		$lastUsername = (null === $session) ? '' : $session->get($lastUsernameKey);

		$csrfToken = $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue();

		return $this->renderLogin(array(
			'last_username' => $lastUsername,
			'error'         => $error,
			'csrf_token'    => $csrfToken,
		), $request);
	}

	/**
	 * Clear Session
	 */
	private function clearSession()
	{
		$session = $this->get('session');

		if ($session->has('tt_identifier'))
			$session->remove('tt_identifier');

		if ($session->has('tt_displayDate'))
			$session->remove('tt_displayDate');
	}

	/**
	 * Renders the login template with the given parameters. Overwrite this function in
	 * an extended controller to provide additional data for the login template.
	 *
	 * @param array $data
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	protected function renderLogin(array $data, Request $request)
	{
		$data['ajaxOn']         = 'xxxyyyzzz';
		$data['config']         = new \stdClass();
		$data['config']->signin = $this->get('security.failure.repository')->testRemoteAddress($request->server->get('REMOTE_ADDR'));

		return $this->render('BusybeeSecurityBundle:Security:login.html.twig', $data);
	}

	public function checkAction()
	{
		throw new \RuntimeException('You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.');
	}

	public function logoutAction()
	{
		throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
	}

	public function timeoutAction()
	{
		$session = $this->get('session');

		$session->set('_timeout', false);

		$token = $this->get('security.token_storage');
		$token->setToken(null);
		$session->set('_timeout', true);

		$lapse = $this->get('busybee_core_system.setting.setting_manager')->get('idleTimeout', 15);

		$session->getFlashBag()->add(
			'info',
			$this->get('translator')->trans('security.session.timeout', array('%hours%' => '00', '%minutes%' => $lapse), 'BusybeeSecurityBundle')
		);

		$url = $this->generateUrl('home_page');

		return new RedirectResponse($url);
	}

	/**
	 * @param Request $request
	 */
	public function googleAction(Request $request)
	{
		// Replace these with your token settings
		// Create a project at https://console.developers.google.com/
		$google       = $this->getParameter('google');
		$clientId     = $google['client_id'];
		$clientSecret = $google['client_secret'];

		// Change this if you are not using the built-in PHP server
		$redirectUri = $this->generateUrl('google_oauth', array(), UrlGeneratorInterface::ABSOLUTE_URL);

		// Start the session
		if (!$this->container->get('session')->isStarted())
		{
			$session = new Session();
			$session->start();
		}
		else
			$session = $this->container->get('session');

		$this->clearSession();

		// Initialize the provider
		$provider = new Google(compact('clientId', 'clientSecret', 'redirectUri'));

		$get = $request->query;

		if (!empty($get->get('error')))
		{
			// Got an error, probably user denied access
			throw new AuthenticationException(htmlspecialchars($get->get('error'), ENT_QUOTES, 'UTF-8'));

		}
		elseif (empty($get->get('code')))
		{
			// If we don't have an authorization code then get one
			$authUrl = $provider->getAuthorizationUrl();
			$session->set('oauth2state', $provider->getState());
			header('Location: ' . $authUrl);
			exit;

		}
		elseif (empty($get->get('state')) || ($get->get('state') !== $session->get('oauth2state')))
		{

			// State is invalid, possible CSRF attack in progress
			$session->remove('oauth2state');
			throw new AuthenticationException($this->get('translator')->trans('google.invalid.state', array(), 'BusybeeSecurityBundle'));

		}
		else
		{

			// Try to get an access token (using the authorization code grant)
			$token = $provider->getAccessToken('authorization_code',
				array(
					'code' => $get->get('code')
				)
			);

			// Optional: Now you have a token you can look up a users profile data
			try
			{

				// We got an access token, let's now get the owner details
				$ownerDetails = $provider->getResourceOwner($token);

			}
			catch (\Exception $e)
			{

				// Failed to get user details
				throw new AuthenticationException($this->get('translator')->trans('google.invalid.user', array('%message%' => $e->getMessage()), 'BusybeeSecurityBundle'));

			}

			// Use this to interact with an API on the users behalf
			echo $token->getToken();

			// Use this to get a new access token if the old one expires
			echo $token->getRefreshToken();

			// Number of seconds until the access token will expire, and need refreshing
			echo $token->getExpires();
		}
		$user = $this->get('busybee_core_security.repository.user_repository')->findOneByEmail($ownerDetails->getEmail());

		if (empty($user))
			throw new AuthenticationException($this->get('translator')->trans('google.notAvailable', array('%email%' => $ownerDetails->getEmail(), '%name%' => $ownerDetails->getName()), 'BusybeeSecurityBundle'));

		// Here, "default" is the name of the firewall in your security.yml
		$token = new UsernamePasswordToken($user, null, "default", $user->getRoles());

		$this->get('security.token_storage')->setToken($token);

		$user->setLastLogin(new \DateTime());
		$em = $this->get('doctrine')->getManager();
		$em->persist($user);
		$em->flush();
		$route = $this->get('router')->generate('home_page');

		if ($user->getChangePassword())
		{

			$this->session->getFlashBag()->add('warning', $this->get('translator')->trans('password.change.now', array(), 'BusybeeSecurityBundle'));

			$route = $this->get('router')->generate('security_user_edit');

		}

		return new RedirectResponse($route);
	}
}
