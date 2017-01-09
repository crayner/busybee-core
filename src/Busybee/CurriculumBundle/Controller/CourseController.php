<?php

namespace Busybee\CurriculumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class CourseController extends Controller
{
    public function indexAction()
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);
		
		$campus = new Program();
		$id = $request->get('id');   
		if (intval($id) > 0)
			$campus = $this->get('campus.repository')->findOneBy(array('id' => $id));   

		$campus->cancelURL = $this->get('router')->generate('campus_manage');
        return $this->render('BusybeeProgramBundle:Course:index.html.twig');
    }
}
