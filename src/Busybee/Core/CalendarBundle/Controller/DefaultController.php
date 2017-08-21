<?php

namespace Busybee\Core\CalendarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
	public function indexAction()
	{
		return $this->render('BusybeeCalendarBundle:Default:index.html.twig');
	}
}
