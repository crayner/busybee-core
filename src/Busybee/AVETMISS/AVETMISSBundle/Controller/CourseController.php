<?php

namespace Busybee\AVETMISS\AVETMISSBundle\Controller;

use Busybee\Core\TemplateBundle\Controller\BusybeeController;
use Busybee\AVETMISS\AVETMISSBundle\Entity\Course;
use Busybee\CurriculumBundle\Entity\Course as Core;
use Busybee\AVETMISS\AVETMISSBundle\Form\CourseType;
use Symfony\Component\HttpFoundation\Request;

class CourseController extends BusybeeController
{
	/**
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function indexAction(Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);

		$course = new Course();
		$id     = $request->get('id') > 0 ? $request->get('id') : 0;

		if (intval($id) > 0)
			$course = $this->get('avetmiss.course.repository')->findOneById($id);

		$course->cancelURL = $this->get('router')->generate('avetmiss_course_manage');

		$core = $course->getCourse();
		if (is_null($core->getName())) $core = new Core();

		$course->name    = $core->getName();
		$course->version = $core->getVersion();
		$course->core    = is_null($course->getId()) ? null : $course;

		$form = $this->createForm(CourseType::class, $course);

		if (intval($id) > 0)
		{
			$form->get('course')->setData($id);
			$form->get('name')->setData($core->getName());
		}

		$data = $request->request->get('avetmiss_course');

		if (!empty($data['name'])) $form->get('name')->setData($data['name']);
		if (!empty($data['version'])) $form->get('version')->setData($data['version']);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$em = $this->get('doctrine')->getManager();
			$course->getCourse()->setName($data['name']);
			$course->getCourse()->setVersion($data['version']);

			$em->persist($course);
			$em->flush();
		}

		return $this->render('BusybeeAVETMISSBundle:Course:index.html.twig', array('form' => $form->createView()));
	}
}
