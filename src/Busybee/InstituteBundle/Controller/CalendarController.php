<?php

namespace Busybee\InstituteBundle\Controller ;

use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use Symfony\Component\HttpFoundation\Request ;
use Busybee\InstituteBundle\Form\YearType ;
use Busybee\InstituteBundle\Entity\Year ;
use Symfony\Component\HttpFoundation\RedirectResponse ;

class CalendarController extends Controller
{
    public function yearsAction()
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, 'Unable to access this page!');
		
		$repo = $this->get('year.repository');
		
		$years = $repo->findBy(array(), array('firstDay'=>'ASC', 'lastDay'=>'ASC'));

		return $this->render('BusybeeInstituteBundle:Calendar:years.html.twig', array('Years' => $years));
    }

    public function editYearAction($id, Request $request)
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, 'Unable to access this page!');
		
		$repo = $this->get('year.repository');
		
		if ($id === 'Add')
			$year = new Year();
		else
			$year = $repo->find($id);
		
		$year->cancelURL = $this->get('router')->generate('calendar_years');
 
        $form = $this->createForm(YearType::class, $year);
		
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$em = $this->get('doctrine')->getManager();
			$em->persist($year);
			$em->flush();
			$id = $year->getId();
			
			return new RedirectResponse($this->generateUrl('year_edit', array('id' => $id)));
		} 
		
		return $this->render('BusybeeInstituteBundle:Calendar:calendar.html.twig', 
			array(
				'form' 			=> $form->createView(),
				'fullForm'		=> $form,
				'id'			=> $id,
			)
		);
    }

    public function deleteYearAction($id)
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, 'Unable to access this page!');
		
		$repo = $this->get('year.repository');
		
		$year = $repo->find($id);
	
		$em = $this->get('doctrine')->getManager();
		$em->remove($year);
		$em->flush();
		
		return new RedirectResponse($this->generateUrl('calendar_years'));
    }
}
