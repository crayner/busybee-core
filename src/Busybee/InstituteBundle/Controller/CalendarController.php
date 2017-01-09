<?php

namespace Busybee\InstituteBundle\Controller ;

use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use Symfony\Component\HttpFoundation\Request ;
use Busybee\InstituteBundle\Form\YearType ;
use Busybee\InstituteBundle\Entity\Year ;
use Busybee\InstituteBundle\Entity\SpecialDay ;
use Symfony\Component\HttpFoundation\RedirectResponse ;
use Symfony\Component\HttpFoundation\Response ;
use DateTime ;

class CalendarController extends Controller
{
    use \Busybee\SecurityBundle\Security\DenyAccessUnlessGranted ;

    public function yearsAction()
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);
		
		$repo = $this->get('year.repository');
		
		$years = $repo->findBy(array(), array('firstDay'=>'ASC', 'lastDay'=>'ASC'));

		return $this->render('BusybeeInstituteBundle:Calendar:years.html.twig', array('Years' => $years));
    }

    public function editYearAction($id, Request $request)
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);
		
		$repo = $this->get('year.repository');
		
		if ($id === 'Add')
			$year = new Year();
		else
			$year = $repo->find($id);
		
		$year->cancelURL = $this->get('router')->generate('calendar_years');
 
        $form = $this->createForm(YearType::class, $year);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
            $em = $this->get('doctrine')->getManager();
			$em->persist($year);
			$em->flush();

            $request->getSession()
                ->getFlashBag()
                ->add('success', 'calendar.success')
            ;

            return new RedirectResponse($this->generateUrl('year_edit', array('id' => $year->getId())));
		} 
		
		return $this->render('BusybeeInstituteBundle:Calendar:calendar.html.twig', 
			array(
				'form' 			=> $form->createView(),
				'fullForm'		=> $form,
				'id'			=> $id,
			)
		);
    }

    public function deleteYearAction($id)
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);
		
		$repo = $this->get('year.repository');
		
		$year = $repo->find($id);
	
		$em = $this->get('doctrine')->getManager();
		$em->remove($year);
		$em->flush();
		
		return new RedirectResponse($this->generateUrl('calendar_years'));
    }

    public function deleteSpecialDayAction($id, $year)
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);
		
		$repo = $this->get('specialDay.repository');
		
		$day = $repo->find($id);
	
		$em = $this->get('doctrine')->getManager();
		$em->remove($day);
		$em->flush();
		
		return new RedirectResponse($this->generateUrl('year_edit', array('id' => $year)));
    }

    public function deleteTermAction($id, $year)
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);
		
		$repo = $this->get('term.repository');
		
		$term = $repo->find($id);
	
		$em = $this->get('doctrine')->getManager();
		$em->remove($term);
		$em->flush();
		
		return new RedirectResponse($this->generateUrl('year_edit', array('id' => $year)));
    }

    public function calendarAction($id)
    {
		$this->denyAccessUnlessGranted('ROLE_USER', null, null);
	
        $now = new \DateTime();
		
		$sm = $this->get('setting.manager');
		
		$firstDayofWeek = $sm->get('firstDayofWeek', 'Monday');
		
		$repo = $this->get('year.repository');
		
		$year = $repo->find($id);
		$years = $repo->findBy(array(), array('name'=>'ASC'));
		
        $service = $this->get('calendar.widget'); //calling a calendar service

        //Defining a custom classes for rendering of months and days
        $dayModelClass = '\Busybee\InstituteBundle\Model\Day';
        $monthModelClass = '\Busybee\InstituteBundle\Model\Month';
        /*
         * Set model classes for calendar. Arguments:
         * 1. For the whole calendar (watch $calendar variable). Default: \TFox\CalendarBundle\Service\WidgetService\Calendar
         * 2. Month. Default: \TFox\CalendarBundle\Service\WidgetService\Month
         * 3. Week. Default: '\TFox\CalendarBundle\Service\WidgetService\Week
         * 4. Day. Default: '\TFox\CalendarBundle\Service\WidgetService\Day'
         * To set default classes null should be passed as argument
         */
        $service->setModels(null, $monthModelClass, null, $dayModelClass);
        $calendar = $service->generateCalendar($year); //Generate a calendar for specified year

		$cm = $this->get('calendar.manager') ;
		
		$cm->setCalendarDays($year, $calendar);

		$content = $this->render('BusybeeInstituteBundle:Calendar:calendarView.pdf.twig', 
			array(
				'calendar'	=> $calendar,
				'year'		=> $year,
			)
		);
		
		/*
         * Pass calendar to Twig
         */
		return $this->render('BusybeeInstituteBundle:Calendar:yearCalendar.html.twig', 
			array(
				'calendar'	=> $calendar,
				'year'		=> $year,
				'years' 	=> $years,
			)
		);
	}

    /**
     * @param $id
     * @return Response
     */
    public function printCalendarAction($id)
    {
		$this->denyAccessUnlessGranted('ROLE_USER', null, null);
	
        $now = new \DateTime();
		
		$sm = $this->get('setting.manager');
		
		$firstDayofWeek = $sm->get('firstDayofWeek', 'Monday');
		
		$repo = $this->get('year.repository');
		
		$year = $repo->find($id);
		
        $service = $this->get('calendar.widget'); //calling a calendar service

        //Defining a custom classes for rendering of months and days
        $dayModelClass = '\Busybee\InstituteBundle\Model\Day';
        $monthModelClass = '\Busybee\InstituteBundle\Model\Month';
        /*
         * Set model classes for calendar. Arguments:
         * 1. For the whole calendar (watch $calendar variable). Default: \TFox\CalendarBundle\Service\WidgetService\Calendar
         * 2. Month. Default: \TFox\CalendarBundle\Service\WidgetService\Month
         * 3. Week. Default: '\TFox\CalendarBundle\Service\WidgetService\Week
         * 4. Day. Default: '\TFox\CalendarBundle\Service\WidgetService\Day'
         * To set default classes null should be passed as argument
         */
        $service->setModels(null, $monthModelClass, null, $dayModelClass);
        $calendar = $service->generateCalendar($year); //Generate a calendar for specified year

		$cm = $this->get('calendar.manager') ;
		
		$cm->setCalendarDays($year, $calendar);

		/*
         * Pass calendar to Twig
         */
		$content = $this->renderView('BusybeeInstituteBundle:Calendar:calendarView.pdf.twig', 
			array(
				'calendar'	=> $calendar,
				'year'		=> $year,
			)
		);

        /**
         * @todo Remove this constaint on render when PHP 7.1 compatible
         */
		$dompdf = $this->get('dompdf')->createDompdf();
		$dompdf->setPaper('a4', 'landscape');
        $dompdf->loadHtml($content);
        @$dompdf->render();
		$headers = array(
			'Content-type' => 'application/pdf'
		);
        return new Response($dompdf->output(), 200, $headers);
	}

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function calendarChangeAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);

        $id = $request->get('id');

        if (empty($id)) {
            $repo = $this->get('year.repository');
            $year = $repo->createQueryBuilder('y')
                ->where('y.firstDay LIKE :now')
                ->setParameter('now', date('Y').'%')
                ->setMaxResults(1)
                ->getQuery()
                ->getResult();
            if (is_array($year)) $year = reset($year);
            $id = $year->getId();
        }

        return new RedirectResponse($this->generateUrl('year_calendar', array('id' => $id)));
    }

    /**
     * @param $day
     * @param $id
     * @return RedirectResponse|Response
     */
    public function copySpecialDayAction($day, $id, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);

        $cm = $this->get('calendar.manager');

        $day = new DateTime($day);

        if ($cm->testNextYear($day)) {
            $nextYear = $cm->getNextYear($day);
            $currentDay = $this->get('specialday.repository')->findOneBy(array('day' => $day));

            $oneYear = new \DateInterval('P1Y');

            $newDay =  new SpecialDay();
            $newDay->setName($currentDay->getName());
            $newDay->setYear($nextYear);
            $newDay->setType($currentDay->getType());
            $newDay->setOpen($currentDay->getOpen());
            $newDay->setClose($currentDay->getClose());
            $newDay->setStart($currentDay->getStart());
            $newDay->setFinish($currentDay->getFinish());
            $newDay->setDay($currentDay->getDay()->add($oneYear));

            $em = $this->get('doctrine')->getManager();
            $em->persist($newDay);
            $em->flush();

            $request->getSession()
                ->getFlashBag()
                ->add('success', 'specialDay.copy.success')
            ;

        }

        return new RedirectResponse($this->generateUrl('year_edit', array('id'=>$id)));
    }
}
