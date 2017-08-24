<?php

namespace Busybee\AttendanceBundle\Controller;

use Busybee\AttendanceBundle\Entity\AttendancePeriod;
use Busybee\AttendanceBundle\Form\AttendancePeriodType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
	use \Busybee\Core\SecurityBundle\Security\DenyAccessUnlessGranted;

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
        $vd = $this->get('voter.details');

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
