<?php

namespace Busybee\PeriodBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('BusybeePeriodBundle:Default:index.html.twig');
    }
}
