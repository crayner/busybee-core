<?php

namespace Busybee\Core\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class TemplateController extends Controller
{
	public function indexAction(Request $request)
	{
		$config         = new \stdClass();
		$config->signin = $this->get('busybee_core_security.repository.failure_repository')->testRemoteAddress($request->server->get('REMOTE_ADDR'));

		return $this->render('BusybeeTemplateBundle:Default:template.html.twig', array('config' => $config));
	}

}
