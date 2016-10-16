<?php

namespace Busybee\SystemBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('SystemBundle:Default:index.html.twig');
    }
}
