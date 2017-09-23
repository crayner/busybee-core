<?php

namespace Busybee\People\AddressBundle\Controller;

use Busybee\Core\TemplateBundle\Controller\BusybeeController;

class DefaultController extends BusybeeController
{
	public function indexAction()
	{
		return $this->render('BusybeeAddressBundle:Default:index.html.twig');
	}
}
