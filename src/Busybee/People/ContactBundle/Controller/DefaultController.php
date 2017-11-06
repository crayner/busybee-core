<?php

namespace Busybee\People\ContactBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
	public function indexAction()
	{
		return $this->render('BusybeeContactBundle:Default:index.html.twig');
	}
}
