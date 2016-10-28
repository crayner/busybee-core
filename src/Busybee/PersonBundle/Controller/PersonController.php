<?php

namespace Busybee\PersonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Busybee\PersonBundle\Entity\Person ;


class PersonController extends Controller
{
    public function indexAction(Request $request)
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, 'Unable to access this page!');
		
		$up = $this->get('person.pagination');
		
		$up->injectRequest($request);
		
		$up->getDataSet();

        return $this->render('BusybeePersonBundle:Person:index.html.twig', 
			array(
            	'pagination' => $up,
        	)
		);
    }


    public function editAction($id, Request $request)
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, 'Unable to access this page!');
		$person = new Person();
		if ($id !== 'Add')
			$person = $this->get('person.repository')->findOneBy(array('id' => $id));
		

		$form = $this->createFormBuilder()
			->add('person', 'Busybee\PersonBundle\Form\PersonType', 
				array(
					'data_class' => 'Busybee\PersonBundle\Entity\Person',
					'data' => $person,
				)
			)
			->add('address1', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', 
				array(
					'class' => 'Busybee\PersonBundle\Entity\Address',
					'data' => $person->getAddress1(),
				)
			)
			->add('address2', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', 
				array(
					'class' => 'Busybee\PersonBundle\Entity\Address',
					'data' => $person->getAddress2(),
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

        return $this->render('BusybeePersonBundle:Person:edit.html.twig',
			array('id' => $id, 'form' => $form->createView())			
		);
    }
}
