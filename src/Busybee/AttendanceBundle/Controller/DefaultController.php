<?php

namespace Busybee\AttendanceBundle\Controller;

use Busybee\AttendanceBundle\Entity\AttendancePeriod;
use Busybee\AttendanceBundle\Form\AttendancePeriodType;
use Busybee\Core\TemplateBundle\Controller\BusybeeController;

class DefaultController extends BusybeeController
{


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('BusybeeAttendanceBundle:Default:index.html.twig');
    }

    /**
     * @param integer $id
     */
    public function staffActivityAction($id)
    {
        $vd = $this->get('busybee_core_security.security.voter_details');

        $vd->addStaff($this->getUser());

        $this->denyAccessUnlessGranted('ROLE_PRINCIPAL', $vd, null);

        $attendPeriod = new AttendancePeriod();

        $attendanceManager = $this->get('attendance.manager')->setActivity($this->get('activity.repository')->find($id))->setAttendancePeriod($attendPeriod);

        $form = $this->createForm(AttendancePeriodType::class, $attendPeriod, ['manager' => $attendanceManager]);

        return $this->render('BusybeeAttendanceBundle:Attendance:activity.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
