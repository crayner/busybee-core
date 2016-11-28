<?php

namespace Busybee\SystemBundle\Controller ;

use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use Symfony\Component\HttpFoundation\Request ;

class PluginController extends Controller
{
	public function managerAction()
	{
		$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');
		

        return $this->render('SystemBundle:Plugin:manage.html.twig');
	}


 }