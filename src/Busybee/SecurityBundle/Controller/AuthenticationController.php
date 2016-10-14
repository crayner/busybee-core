<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Busybee\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\RedirectResponse ;

class AuthenticationController extends Controller
{
	public function loginAction(Request $request)
	{
        /** @var $session \Symfony\Component\HttpFoundation\Session\Session */
        $session = $request->getSession();
		if (false === $this->get('security.failure.repository')->testRemoteAddress($request->server->get('REMOTE_ADDR')))
		{
			$session->getFlashBag()->add(
				'warning',
				$this->get('translator')->trans('security.authorisation.blocked_ip', array("%remoteIP%" => $request->server->get('REMOTE_ADDR')), 'BusybeeSecurityBundle')
			);
			$url = $this->generateUrl('homepage');
			return new RedirectResponse($url);
		}

		$authErrorKey = Security::AUTHENTICATION_ERROR;
		$lastUsernameKey = Security::LAST_USERNAME;

        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has($authErrorKey)) {
            $error = $request->attributes->get($authErrorKey);
        } elseif (null !== $session && $session->has($authErrorKey)) {
            $error = $session->get($authErrorKey);
            $session->remove($authErrorKey);
        } else {
            $error = NULL;
        }

        if (!$error instanceof AuthenticationException) 
            $error = NULL; // The value does not come from the security component.

        // last username entered by the user
        $lastUsername = (NULL === $session) ? '' : $session->get($lastUsernameKey);

		$csrfToken = $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue();

        return $this->renderLogin(array(
            'last_username' => $lastUsername,
            'error' => $error,
            'csrf_token' => $csrfToken,
        ), $request);
	}
    /**
     * Renders the login template with the given parameters. Overwrite this function in
     * an extended controller to provide additional data for the login template.
     *
     * @param array $data
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderLogin( array $data, Request $request )
    {
        $data['ajaxOn'] = 'xxxyyyzzz';
		$data['config'] = new \stdClass();
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

}
