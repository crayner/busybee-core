<?php

namespace Busybee\PersonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class StaffController extends Controller
{
    public function indexAction(Request $request)
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, 'Unable to access this page!');
		
		$up = $this->get('staff.pagination');
		
		$up->injectRequest($request);
		
		$up->getDataSet();

        return $this->render('BusybeePersonBundle:Staff:index.html.twig', 
			array(
            	'pagination' => $up,
        	)
		);
    }
}
