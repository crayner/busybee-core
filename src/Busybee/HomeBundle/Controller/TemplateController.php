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
		$config = new \stdClass();
		$config->signin = $this->get('security.failure.repository')->testRemoteAddress($request->server->get('REMOTE_ADDR'));

		return $this->render('BusybeeHomeBundle:Default:template.html.twig', array('config' => $config));
    }

}
