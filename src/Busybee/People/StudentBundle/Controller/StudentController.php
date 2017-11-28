<?php

namespace Busybee\People\StudentBundle\Controller;

use Busybee\People\PersonBundle\Entity\Person;
use Busybee\Core\TemplateBundle\Controller\BusybeeController;
use Busybee\People\StudentBundle\Form\GradeType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
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

		$pm = $this->get('busybee_people_person.model.person_manager');

		$person = $pm->find($id);

		if (!$person instanceof Person)
			return new JsonResponse(
				array(
					'message' => '<div class="alert alert-danger fadeAlert">' . $this->get('translator')->trans('student.toggle.personMissing', array(), 'BusybeeStudentBundle') . '</div>',
					'status'  => 'failed'
				),
				200
			);

		$em = $this->get('doctrine')->getManager();

		if (!$pm->isStudent())
		{
			if ($pm->canBeStudent())
			{
				$pm->createStudent($person);

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
		elseif ($pm->isStudent())
		{
			if ($pm->canDeleteStudent(null, $this->getParameter('PersonTabs')))
			{
				$pm->deleteStudent(null, $this->getParameter('PersonTabs'));

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
				'manager'    => $this->get('busybee_people_person.model.person_manager'),
			)
		);
	}

	/**
	 * @param $id
	 *
	 * @return RedirectResponse
	 */
	public function removePassportScanAction(Request $request, $id)
	{
		$this->denyAccessUnlessGranted('ROLE_ADMIN');

		$pm = $this->get('busybee_people_person.model.person_manager');

		$person = $pm->getPerson($id);

		$em = $this->get('doctrine')->getManager();

		$photo = $person->getCitizenship1PassportScan();

		$person->setPhoto(null);

		if (file_exists($photo))
			unlink($photo);

		$em->persist($person);
		$em->flush();

		return $this->redirectToRoute('person_edit', ['id' => $id]);
	}

	/**
	 * @param $id
	 *
	 * @return RedirectResponse
	 */
	public function removeIDScanAction(Request $request, $id)
	{
		$this->denyAccessUnlessGranted('ROLE_ADMIN');

		$pm = $this->get('busybee_people_person.model.person_manager');

		$person = $pm->getPerson($id);

		$em = $this->get('doctrine')->getManager();

		$photo = $person->getNationalIDScan();

		$person->setPhoto(null);

		if (file_exists($photo))
			unlink($photo);

		$em->persist($person);
		$em->flush();

		return $this->redirectToRoute('person_edit', ['id' => $id]);
	}


	/**
	 * @param Request $request
	 *
	 * @param         $id  Grade ID
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function addToGradeAction(Request $request, $id)
	{
		$this->denyAccessUnlessGranted('ROLE_ADMIN');

		$studentManager = $this->get('busybee_people_student.model.student_manager');

		$studentManager->initiateGrade($id);

		$form = $this->createForm(GradeType::class, $studentManager->getGrade(), ['manager' => $studentManager]);

		$studentManager->handleRequest($request, $form);

		return $this->render('@BusybeeStudent/Student/grade.html.twig', [
			'manager' => $studentManager,
			'form'    => $form->createView(),
		]);
	}

	public function addStudentToGradeAction($grade, $student, $status)
	{
		$this->denyAccessUnlessGranted('ROLE_ADMIN');

		$studentManager = $this->get('busybee_people_student.model.student_manager');

		$studentManager->initiateGrade($grade);

		$studentManager->addStudentToGrade($student, $status);

		$studentManager->getPossibleStudents(false);
		$studentManager->getCurrentStudents(false);

		$message = $this->get('busybee_core_system.model.flash_bag_manager')->renderMessages($studentManager->getMessages());

		return new JsonResponse(
			[
				'message'  => $message,
				'current'  => $this->renderView('@BusybeeStudent/Student/addStudent.html.twig', ['manager' => $studentManager]),
				'possible' => $this->renderView('@BusybeeStudent/Student/removeStudent.html.twig', ['manager' => $studentManager]),
			],
			200);

	}

	public function removeStudentFromGradeAction($grade, $student)
	{
		$this->denyAccessUnlessGranted('ROLE_ADMIN');

		$studentManager = $this->get('busybee_people_student.model.student_manager');

		$studentManager->initiateGrade($grade);

		$studentManager->removeStudentFromGrade($student);

		$studentManager->getPossibleStudents(false);
		$studentManager->getCurrentStudents(false);

		$message = $this->get('busybee_core_system.model.flash_bag_manager')->renderMessages($studentManager->getMessages());

		return new JsonResponse(
			[
				'message'  => $message,
				'current'  => $this->renderView('@BusybeeStudent/Student/addStudent.html.twig', ['manager' => $studentManager]),
				'possible' => $this->renderView('@BusybeeStudent/Student/removeStudent.html.twig', ['manager' => $studentManager]),
			],
			200);

	}
}