<?php

namespace Busybee\SystemBundle\Controller ;

use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use stdClass ;
use Symfony\Component\HttpFoundation\JsonResponse ;

class UpdateController extends Controller
{
    public function indexAction()
    {
		if (true !== ($response = $this->get('busybee_security.authorisation.checker')->redirectAuthorisation('ROLE_ADMIN'))) return $response;
		$config = new stdClass();
		$config->signin = null;
		
		$um = $this->get('system.update.manager');
		
		$config->version = $um->getVersion();
		$config->dbUpdate = $um->getUpdateDetails();
		
        return $this->render('SystemBundle:Update:index.html.twig', array('config' => $config));
    }

    public function databaseAction()
    {
		if (true !== ($response = $this->get('busybee_security.authorisation.checker')->redirectAuthorisation('ROLE_ADMIN'))) return $response;
		
		$um = $this->get('system.update.manager');

		$config = new stdClass();
		$config->signin = null;
		
		$um->build();
		$config->version = $um->getVersion();
		$config->dbUpdate = $um->getUpdateDetails();
		$content = $this->renderView('SystemBundle:Update:index_content.html.twig', array('config' => $config));
dump($config);
dump($content);
		return new JsonResponse(
			array('content' => $content),
			200
		);

    }
}