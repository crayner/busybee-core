<?php

namespace Busybee\Management\GradeBundle\Controller;

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

		$gradeManager = $this->get('busybee_management_grade.model.grade_manager');

		$data            = $gradeManager->deleteStudentGrade($id);
		$data['message'] = $this->get('translator')->trans($data['message'], [], 'BusybeePersonBundle');

		return new JsonResponse($data, 200);

	}

}