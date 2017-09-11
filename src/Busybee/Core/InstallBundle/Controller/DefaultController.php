<?php

namespace Busybee\Core\InstallBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('BusybeeInstallBundle:Default:index.html.twig');
    }
}
