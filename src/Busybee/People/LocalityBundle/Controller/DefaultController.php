<?php

namespace Busybee\People\LocalityBundle\Controller;

use Busybee\Core\TemplateBundle\Controller\BusybeeController;

class DefaultController extends BusybeeController
{
	public function indexAction()
	{
		return $this->render('BusybeeLocalityBundle:Default:index.html.twig');
	}
}
