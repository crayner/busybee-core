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

        $tm = $this->get('timetable.manager');

        /*        $specialDays = $this->get('specialDay.repository')->createQueryBuilder('s')
                    ->leftJoin('s.year', 'y')
                    ->where('y.id = :year_id')
                    ->setParameter('year_id', $year->getId())
                    ->orderBy('s.day', 'ASC')
                    ->getQuery()
                    ->getResult();
        */


        return $this->render('BusybeeTimeTableBundle:Days:assign.html.twig',
            [
                'tm' => $tm,
            ]
        );
    }
}
