<?php

namespace Busybee\Core\CalendarBundle\Controller;

use Busybee\Core\CalendarBundle\Form\GradeType;
use Busybee\Core\TemplateBundle\Controller\BusybeeController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CalendarGroupController extends BusybeeController
{
	/**
	 * @param Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function deleteStudentCalendarGroupAction(Request $request, $id = null)
	{
		$this->denyAccessUnlessGranted('ROLE_ADMIN');

		$gradeManager = $this->get('busybee_core_calendar.model.calendar_group_manager');

		$data            = $gradeManager->deleteStudentCalendarGroup($id);
		$data['message'] = $this->get('translator')->trans($data['message'], [], 'BusybeePersonBundle');

		return new JsonResponse($data, 200);
	}

	public function editAction(Request $request, $id)
	{
		$this->denyAccessUnlessGranted('ROLE_ADMIN');

		$manager = $this->get('busybee_core_calendar.model.grade_manager');

		$grade = $manager->getEntity($id);

		$form = $this->createForm(GradeType::class, $grade, ['manager' => $manager]);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$om = $this->get('doctrine')->getManager();
			$om->persist($grade);
			$om->flush();
		}

		return $this->render('@BusybeeCalendar/Grade/manage.html.twig',
			[
				'form' => $form->createView(),
			]
		);
	}

}