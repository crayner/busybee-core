<?php

namespace Busybee\Core\InstallBundle\Controller;

use Busybee\Core\TemplateBundle\Controller\BusybeeController;

class DefaultController extends BusybeeController
{
    public function indexAction()
    {
        return $this->render('BusybeeInstallBundle:Default:index.html.twig');
    }
}
