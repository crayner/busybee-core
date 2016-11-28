<?php

namespace Busybee\CampusBundle\Controller ;

use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use Symfony\Component\HttpFoundation\Request ;
use Busybee\CampusBundle\Form\CampusType ;
use Busybee\CampusBundle\Entity\Campus ;

class CampusController extends Controller
{
    public function indexAction(Request $request)
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, 'Unable to access this page!');
		
		$campus = new Campus();
		$id = $request->get('id');   
		if (intval($id) > 0)
			$campus = $this->get('campus.repository')->findOneBy(array('id' => $id));   

		$campus->cancelURL = $this->get('router')->generate('campus_manage');
		if (empty($campus->getCountry())) $campus->setCountry($this->getParameter('country'));

        $form = $this->createForm(CampusType::class, $campus);
		if (intval($id) > 0)
			$form->get('locationList')->setData($id);
			 
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid())
		{
			$em = $this->get('doctrine')->getManager();
			$em->persist($campus);
			$em->flush();
	
		}
		
		return $this->render('BusybeeCampusBundle:Campus:index.html.twig', array('form' => $form->createView()));
    }
}
