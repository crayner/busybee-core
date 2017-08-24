<?php

namespace Busybee\AVETMISS\AVETMISSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Busybee\AVETMISS\AVETMISSBundle\Entity\Course;
use Busybee\CurriculumBundle\Entity\Course as Core;
use Busybee\AVETMISS\AVETMISSBundle\Form\CourseType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;

class ScheduleController extends Controller
{
	use \Busybee\SecurityBundle\Security\DenyAccessUnlessGranted;

	public function indexAction(Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_HEAD_TEACHER', null, null);

		return $this->render('BusybeeAVETMISSBundle:Schedule:index.html.twig');
	}
}
