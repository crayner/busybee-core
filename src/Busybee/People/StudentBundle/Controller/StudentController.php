<?php

namespace Busybee\People\StudentBundle\Controller;

use Busybee\People\PersonBundle\Entity\Person;
use Busybee\Core\TemplateBundle\Controller\BusybeeController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class StudentController extends BusybeeController
{
	/**
	 * @param $id
	 *
	 * @return JsonResponse
	 */
	public function toggleAction($id)
	{
		$this->denyAccessUnlessGranted('ROLE_ADMIN', null, null);

		$person = $this->get('busybee_people_person.repository.person_repository')->find($id);

		if (!$person instanceof Person)
			return new JsonResponse(
				array(
					'message' => '<div class="alert alert-danger fadeAlert">' . $this->get('translator')->trans('student.toggle.personMissing', array(), 'BusybeeStudentBundle') . '</div>',
					'status'  => 'failed'
				),
				200
			);

		$em = $this->get('doctrine')->getManager();

		if (!$person->getStudentQuestion())
		{
			if ($this->get('busybee_people_person.model.person_manager')->canBeStudent($person))
			{
				$this->get('busybee_people_person.model.person_manager')->createStudent($person);

				return new JsonResponse(
					array(
						'message' => '<div class="alert alert-success fadeAlert">' . $this->get('translator')->trans('student.toggle.addSuccess', array('%name%' => $person->formatName()), 'BusybeeStudentBundle') . '</div>',
						'status'  => 'added',
					),
					200
				);
			}
			else
			{
				return new JsonResponse(
					array(
						'message' => '<div class="alert alert-warning fadeAlert">' . $this->get('translator')->trans('student.toggle.addRestricted', array('%name%' => $person->formatName()), 'BusybeeStudentBundle') . '</div>',
						'status'  => 'failed',
					),
					200
				);
			}
		}
		elseif ($person->getStudentQuestion())
		{
			if ($this->get('busybee_people_person.model.person_manager')->canDeleteStudent($person, $this->getParameter('person')))
			{
				$this->get('busybee_people_person.model.person_manager')->deleteStudent($person, $this->getParameter('person'));

				return new JsonResponse(
					array(
						'message' => '<div class="alert alert-success fadeAlert">' . $this->get('translator')->trans('student.toggle.removeSuccess', array('%name%' => $person->formatName()), 'BusybeeStudentBundle') . '</div>',
						'status'  => 'removed',
					),
					200
				);

			}
			else
			{
				return new JsonResponse(
					array(
						'message' => '<div class="alert alert-warning fadeAlert">' . $this->get('translator')->trans('student.toggle.removeRestricted', array('%name%' => $person->formatName()), 'BusybeeStudentBundle') . '</div>',
						'status'  => 'failed',
					),
					200
				);
			}
		}
	}

	/**
	 * @param Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function listAction(Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_ADMIN');

		$up = $this->get('busybee_people_student.model.student_pagination');

		$up->injectRequest($request);

		$up->getDataSet();

		return $this->render('BusybeeStudentBundle:Student:index.html.twig',
			array(
				'pagination' => $up,
			)
		);
	}

}