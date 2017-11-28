<?php

namespace Busybee\Core\CalendarBundle\Controller;

use Busybee\Core\CalendarBundle\Form\GradeType;
use Busybee\Core\TemplateBundle\Controller\BusybeeController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class GradeController extends BusybeeController
{
	/**
	 * @param Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function deleteStudentGradeAction(Request $request, $id = null)
	{
		$this->denyAccessUnlessGranted('ROLE_ADMIN');

		$gradeManager = $this->get('busybee_core_calendar.model.grade_manager');

		$data            = $gradeManager->deleteStudentGrade($id);
		$data['message'] = $this->get('translator')->trans($data['message'], [], 'BusybeePersonBundle');

		return new JsonResponse($data, 200);
	}

	/**
	 * @param Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function listAction(Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_ADMIN');

		$up = $this->get('busybee_core_calendar.pagination.grade_pagination');

		$up->injectRequest($request);

		$up->getDataSet();

		return $this->render('@BusybeeCalendar/Grade/list.html.twig',
			[
				'pagination' => $up,
				'manager'    => $this->get('busybee_core_calendar.model.grade_manager'),
			]

		);
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