<?php

namespace Busybee\Core\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ErrorController extends Controller
{
	public function indexAction(Request $request, $message)
	{
		$config         = new \stdClass();
		$config->signin = $this->get('security.failure.repository')->testRemoteAddress($request->server->get('REMOTE_ADDR'));

		return $this->render('BusybeeHomeBundle:Error:index.html.twig', array('message' => $message, 'config' => $config));
	}

}
