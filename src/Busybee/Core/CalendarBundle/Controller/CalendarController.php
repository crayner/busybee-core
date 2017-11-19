<?php

namespace Busybee\Core\CalendarBundle\Controller;

use Busybee\Core\CalendarBundle\Model\Day;
use Busybee\Core\CalendarBundle\Model\Month;
use Busybee\Core\HomeBundle\Exception\Exception;
use Busybee\Core\TemplateBundle\Controller\BusybeeController;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Symfony\Component\HttpFoundation\Request;
use Busybee\Core\CalendarBundle\Form\YearType;
use Busybee\Core\CalendarBundle\Entity\Year;
use Busybee\Core\CalendarBundle\Entity\SpecialDay;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use DateTime;
use Spipu\Html2Pdf\Html2Pdf;

class CalendarController extends BusybeeController
{


	/**
	 * @return Response
	 */
	public function yearsAction()
	{
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);

		$years = $this->get('busybee_core_calendar.repository.year_repository')->findBy([], ['firstDay' => 'DESC']);

		return $this->render('BusybeeCalendarBundle:Calendar:years.html.twig', array('Years' => $years, 'manager' => $this->get('busybee_core_calendar.model.year_manager')));
	}

	/**
	 * @param         $id
	 * @param Request $request
	 *
	 * @return RedirectResponse|Response
	 */
	public function editYearAction($id, Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);

		$year = $id === 'Add' ? new Year() : $this->get('busybee_core_calendar.repository.year_repository')->find($id);

		$form = $this->createForm(YearType::class, $year);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$em = $this->get('doctrine')->getManager();
			$em->persist($year);
			$em->flush();

			$request->getSession()
				->getFlashBag()
				->add('success', 'calendar.success');
			if ($id === 'Add')
				return new RedirectResponse($this->generateUrl('year_edit', array('id' => $year->getId())));

			$id = $year->getId();
		}

		return $this->render('BusybeeCalendarBundle:Calendar:calendar.html.twig',
			[
				'form'     => $form->createView(),
				'fullForm' => $form,
				'id'       => $id,
				'year_id'  => $id,
			]
		);
	}

	/**
	 * @param $id
	 *
	 * @return RedirectResponse
	 */
	public function deleteYearAction($id)
	{
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);

		$repo = $this->get('busybee_core_calendar.repository.year_repository');

		$year = $repo->find($id);

		$em = $this->get('doctrine')->getManager();
		$em->remove($year);
		$em->flush();

		return new RedirectResponse($this->generateUrl('calendar_years'));
	}

	/**
	 * @param   int  $id
	 * @param   bool $closeWindow
	 *
	 * @return  Response
	 */
	public function calendarAction($id, $closeWindow = null)
	{
		$this->denyAccessUnlessGranted('ROLE_USER', null, null);

		$repo = $this->get('busybee_core_calendar.repository.year_repository');

		if ($id == 'current')
		{
			$year = $this->get('busybee_core_security.doctrine.user_manager')->getSystemYear($this->getUser());
		}
		else
			$year = $repo->find($id);

		$years = $repo->findBy([], ['name' => 'ASC']);

		$year = $repo->find($year->getId());

		$service = $this->get('busybee_core_calendar.service.widget_service.calendar'); //calling a calendar service

		//Defining a custom classes for rendering of months and days
		$dayModelClass   = Day::class;

		/**
		 * Set model classes for calendar. Arguments:
		 * 1. For the whole calendar (watch $calendar variable). Default: \TFox\CalendarBundle\Service\WidgetService\Calendar
		 * 2. Month. Default: \TFox\CalendarBundle\Service\WidgetService\Month
		 * 3. Week. Default: '\TFox\CalendarBundle\Service\WidgetService\Week
		 * 4. Day. Default: '\TFox\CalendarBundle\Service\WidgetService\Day'
		 * To set default classes null should be passed as argument
		 */
		$service->setModels(null, null, $dayModelClass);

		$year->initialiseTerms();

		$calendar = $service->generate($year); //Generate a calendar for specified year

		$cm = $this->get('busybee_core_calendar.model.calendar_manager');

		$cm->setCalendarDays($year, $calendar);

		$this->render('BusybeeCalendarBundle:Calendar:calendarView.pdf.twig',
			array(
				'calendar' => $calendar,
				'year'     => $year,
			)
		);

		/*
         * Pass calendar to Twig
         */

		return $this->render('BusybeeCalendarBundle:Calendar:yearCalendar.html.twig',
			array(
				'calendar'    => $calendar,
				'year'        => $year,
				'years'       => $years,
				'closeWindow' => $closeWindow,
			)
		);
	}

	/**
	 * @param $id
	 *
	 * @return Response
	 */
	public function printCalendarAction($id)
	{
		$this->denyAccessUnlessGranted('ROLE_USER', null, null);

		$repo = $this->get('busybee_core_calendar.repository.year_repository');

		$year = $repo->find($id);

		$service = $this->get('busybee_core_calendar.service.widget_service.calendar'); //calling a calendar service

		//Defining a custom classes for rendering of months and days
		$dayModelClass   = '\Busybee\Core\CalendarBundle\Model\Day';
		/*
		 * Set model classes for calendar. Arguments:
		 * 1. For the whole calendar (watch $calendar variable). Default: \TFox\CalendarBundle\Service\WidgetService\Calendar
		 * 2. Month. Default: \TFox\CalendarBundle\Service\WidgetService\Month
		 * 3. Week. Default: '\TFox\CalendarBundle\Service\WidgetService\Week
		 * 4. Day. Default: '\TFox\CalendarBundle\Service\WidgetService\Day'
		 * To set default classes null should be passed as argument
		 */
		$service->setModels(null, null, $dayModelClass);
		$calendar = $service->generate($year); //Generate a calendar for specified year

		$cm = $this->get('busybee_core_calendar.model.calendar_manager');

		$cm->setCalendarDays($year, $calendar);

		/*
         * Pass calendar to Twig
         */
		$content = $this->renderView('BusybeeCalendarBundle:Calendar:calendarView.pdf.twig',
			array(
				'calendar' => $calendar,
				'year'     => $year,
			)
		);

		try
		{
			$dompdf = new Html2Pdf('L', 'A4', substr(empty($this->getUser()->getLocale()) ? $this->getParameter('locale') : $this->getUser()->getLocale(), 0, 2));
			ini_set('max_execution_time', 90);
			$dompdf->writeHtml($content);
			$headers = array(
				'Content-type'        => 'application/pdf',
				'Content-Disposition' => 'attachment; filename=' . basename('Calendar_' . preg_replace('/\s+/', '_', $year->getName()) . '.pdf'),
			);

			return new Response($dompdf->output('ignore_me.pdf', 'S'), 200, $headers);

		}
		catch (Html2PdfException $e)
		{
			$formatter = new ExceptionFormatter($e);
			throw new Exception($formatter->getHtmlMessage());
		}
	}

	/**
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function calendarChangeAction(Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_USER', null, null);

		$id = $request->get('id');

		if (empty($id))
		{
			$repo = $this->get('busybee_core_calendar.repository.year_repository');
			$year = $repo->createQueryBuilder('y')
				->where('y.firstDay LIKE :now')
				->setParameter('now', date('Y') . '%')
				->setMaxResults(1)
				->getQuery()
				->getResult();
			if (is_array($year)) $year = reset($year);
			if ($year instanceof Year)
				$id = $year->getId();
			else
			{
				$request->getSession()
					->getFlashBag()
					->add('warning', 'year.current.missing');

				return new RedirectResponse($this->generateUrl('year_edit', array('id' => 'Add')));
			}
		}

		return new RedirectResponse($this->generateUrl('year_calendar', array('id' => $id)));
	}

	/**
	 * @param $day
	 * @param $id
	 *
	 * @return RedirectResponse|Response
	 */
	public function copySpecialDayAction($day, $id, Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);

		$cm = $this->get('busybee_core_calendar.model.calendar_manager');

		$day = new DateTime($day);

		if ($cm->testNextYear($day))
		{
			$nextYear   = $cm->getNextYear($day);
			$currentDay = $this->get('busybee_core_calendar.repository.special_day_repository')->findOneBy(array('day' => $day));

			$oneYear = new \DateInterval('P1Y');

			$newDay = new SpecialDay();
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
				->add('success', 'specialDay.copy.success');

		}

		return new RedirectResponse($this->generateUrl('year_edit', array('id' => $id)));
	}
}
