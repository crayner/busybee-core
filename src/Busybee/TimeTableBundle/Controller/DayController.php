<?php

namespace Busybee\TimeTableBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class DayController extends Controller
{
    use \Busybee\SecurityBundle\Security\DenyAccessUnlessGranted;

    public function dayAssignAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_PRINCIPAL', null, null);

        return $this->render('BusybeeTimeTableBundle:Days:assign.html.twig',
            [
            ]
        );
    }
}
