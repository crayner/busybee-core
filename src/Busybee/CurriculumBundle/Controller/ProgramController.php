<?php

namespace Busybee\CurriculumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class ProgramController extends Controller
{
    public function indexAction()
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, 'Unable to access this page!');
		
		$campus = new Program();
		$id = $request->get('id');   
		if (intval($id) > 0)
			$campus = $this->get('campus.repository')->findOneBy(array('id' => $id));   

		$campus->cancelURL = $this->get('router')->generate('campus_manage');
        return $this->render('BusybeeProgramBundle:Program:index.html.twig');
    }
}
