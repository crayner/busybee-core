<?php

namespace Busybee\PeriodBundle\Controller;

use Busybee\Core\TemplateBundle\Controller\BusybeeController;

class DefaultController extends BusybeeController
{
    public function indexAction()
    {
        return $this->render('BusybeePeriodBundle:Default:index.html.twig');
    }
}
