<?php

namespace Busybee\FormBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('BusybeeFormBundle:Default:index.html.twig');
    }
}
