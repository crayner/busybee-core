<?php

namespace Busybee\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use Symfony\Component\Yaml\Dumper ;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction( Request $request )
    {
		if (true !== ($response = $this->get('busybee_security.authorisation.checker')->redirectAuthorisation('IS_AUTHENTICATED_FULLY'))) return $response;

		$name = 'BusybeeHomeBundle:Default:index.html.twig';
		$config = new \stdClass();
		$config->signin = $this->get('security.failure.repository')->testRemoteAddress($request->server->get('REMOTE_ADDR'));

		return $this->render('BusybeeHomeBundle:Default:index.html.twig', array('name' => $name, 'config' => $config));
    }
}
