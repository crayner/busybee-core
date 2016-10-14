<?php

namespace Busybee\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('BusybeeHomeBundle:Default:index.html.twig');
    }
}
