<?php

namespace Busybee\PersonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Busybee\PersonBundle\Entity\Staff ;

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


    public function editAction($id, Request $request)
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, 'Unable to access this page!');
		
		$staff = new Staff();
		if ($id !== 'Add')
			$staff = $this->get('staff.repository')->findOneBy(array('id' => $id));
		

		$form = $this->createFormBuilder()
			->add('staff', 'Busybee\PersonBundle\Form\StaffType', 
				array(
					'data_class' => 'Busybee\PersonBundle\Entity\Staff',
					'data' => $staff,
				)
			)
			->add('person', 'Busybee\PersonBundle\Form\PersonType', 
				array(
					'data_class' => 'Busybee\PersonBundle\Entity\Person',
					'data' => $staff->getPerson(),
				)
			)
            ->add('save', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
					'label' 				=> 'form.save',
					'translation_domain' 	=> 'BusybeeHomeBundle',
					'attr' 					=> array(
						'class' 				=> 'btn btn-success glyphicons glyphicons-disk-save'
					),
				)
			)
			->add('cancel', 'Symfony\Component\Form\Extension\Core\Type\ButtonType', array(
					'label'					=> 'form.cancel', 
					'translation_domain' 	=> 'BusybeeHomeBundle',
					'attr' 					=> array(
						'formnovalidate' 		=> 'formnovalidate',
						'class' 				=> 'btn btn-info glyphicons glyphicons-remove-circle',
						'onClick'				=> 'location.href=\''.$this->generateUrl('busybee_security_user_list')."'",
					),
				)
			)
			->getForm();

        return $this->render('BusybeePersonBundle:Staff:edit.html.twig',
			array('id' => $id, 'form' => $form->createView())			
		);
    }
}
