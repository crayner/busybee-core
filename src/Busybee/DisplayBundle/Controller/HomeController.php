<?php

namespace Busybee\DisplayBundle\Controller ;

use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use Symfony\Component\Yaml\Dumper ;
use Symfony\Component\HttpFoundation\Request;


class HomeController extends Controller
{
    public function homeAction( Request $request )
    {
		if (true !== ($response = $this->get('busybee_security.authorisation.checker')->redirectAuthorisation('IS_AUTHENTICATED_FULLY'))) return $response;

		$name = 'BusybeeDisplayBundle::home.html.twig';
		$config = new \stdClass();
		$config->signin = $this->get('security.failure.repository')->testRemoteAddress($request->server->get('REMOTE_ADDR'));

		return $this->render('BusybeeDisplayBundle::home.html.twig', array('name' => $name, 'config' => $config));
    }

    public function adminAction()
    {
		if (true !== ($response = $this->get('busybee_security.authorisation.checker')->redirectAuthorisation('ROLE_USER'))) return $response;

		$name = 'BusybeeDisplayBundle:Bootstrap:jumbotron-narrow.html.twig';
        return $this->render('BusybeeDisplayBundle:Bootstrap:jumbotron-narrow.html.twig', array('name' => $name));
    }

    public function contactAction()
    {
		if (true !== ($response = $this->get('busybee_security.authorisation.checker')->redirectAuthorisation('ROLE_ADMIN'))) return $response;

		$name = 'BusybeeDisplayBundle:Bootstrap:theme.html.twig';
        return $this->render('BusybeeDisplayBundle:Bootstrap:theme.html.twig', array('name' => $name));
    }
}
