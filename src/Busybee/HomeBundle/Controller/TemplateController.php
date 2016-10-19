<?php

namespace Busybee\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use Symfony\Component\Yaml\Dumper ;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse ;

class TemplateController extends Controller
{
    public function indexAction( Request $request )
    {
		if (true !== ($response = $this->get('busybee_security.authorisation.checker')->ajaxAuthorisation('ROLE_ADMIN', $request))) return $response ;
		$config = new \stdClass();
		$config->signin = $this->get('security.failure.repository')->testRemoteAddress($request->server->get('REMOTE_ADDR'));

		return $this->render('BusybeeHomeBundle:Default:template.html.twig', array('config' => $config));
    }

}
