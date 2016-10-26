<?php

namespace Busybee\PersonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PersonController extends Controller
{
    public function indexAction()
    {
        return $this->render('BusybeePersonBundle:Default:index.html.twig');
    }
}
