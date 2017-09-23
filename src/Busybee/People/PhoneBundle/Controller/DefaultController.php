<?php

namespace Busybee\People\PhoneBundle\Controller;

use Busybee\Core\TemplateBundle\Controller\BusybeeController;

class DefaultController extends BusybeeController
{
	public function indexAction()
	{
		return $this->render('BusybeePhoneBundle:Default:index.html.twig');
	}
}
