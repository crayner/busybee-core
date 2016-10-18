<?php

namespace Busybee\DatabaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use Symfony\Component\HttpFoundation\Request ;
use Symfony\Component\HttpFoundation\JsonResponse ;
use Symfony\Component\HttpFoundation\RedirectResponse ;

class NewController extends Controller
{
    /**
     * Create a Field Record
     */
    public function fieldAction(Request $request)
    {
		if (true !== ($response = $this->get('busybee_security.authorisation.checker')->redirectAuthorisation('ROLE_REGISTRAR'))) return $response;

		$field = $this->get('field.repository')->createNew();

		$field->enumeratorRepository = $this->get('enumerator.repository');

		$form = $this->createForm( '\Busybee\DatabaseBundle\Form\FieldType', $field );
		$form
			->add('cancel', '\Symfony\Component\Form\Extension\Core\Type\ButtonType', array(
					'label'					=> 'form.cancel', 
					'translation_domain' 	=> 'BusybeeDisplayBundle',
					'attr' 					=> array(
						'formnovalidate' 		=> 'formnovalidate',
						'class' 				=> 'btn btn-info glyphicons glyphicons-exclamation-sign',
						'onClick'				=> 'location.href=\''.$this->generateUrl('database_field_list')."'",
					),
				)
			)
		;


        $form->handleRequest($request);

		if ($form->isSubmitted()) {
			if ($form->isValid()) {

				$em = $this->getDoctrine()->getManager();
				$em->persist($field);
				$em->flush();
				
				$url = $this->generateUrl('database_field_list');
				if ($form->get('save_and_add')->isClicked())
					$url = $this->generateUrl('database_field_new');
				$response = new RedirectResponse($url);
				$this->addFlash(
					'success',
					$this->get('translator')->trans('field.edit.success', array(), 'BusybeeDatabaseBundle')
				);
				return $response;
	
			} else {

				$this->addFlash(
					'danger',
					$this->get('translator')->trans('field.edit.failed', array(), 'BusybeeDatabaseBundle')
				);
				   
			}
		}

        return $this->render('BusybeeDatabaseBundle:Field:new.html.twig', array(
									'field' 			=> $field,
									'form' 				=> $form->createView(),
			)
		);
    }
    /**
     * Create a Table Record
     */
    public function tableAction(Request $request)
    {
		if (true !== ($response = $this->get('busybee_security.authorisation.checker')->redirectAuthorisation('ROLE_REGISTRAR'))) return $response;

		$table = $this->get('table.repository')->createNew();

		$form = $this->createForm('\Busybee\DatabaseBundle\Form\TableType', $table);
		$form
			->add('cancel', '\Symfony\Component\Form\Extension\Core\Type\ButtonType', array(
					'label'					=> 'form.cancel', 
					'translation_domain' 	=> 'BusybeeDisplayBundle',
					'attr' 					=> array(
						'formnovalidate' 		=> 'formnovalidate',
						'class' 				=> 'btn btn-info glyphicons glyphicons-exclamation-sign',
						'onClick'				=> 'location.href=\''.$this->generateUrl('database_table_list')."'",
					),
				)
			)
		;
		
        $form->handleRequest($request);

		if ($form->isSubmitted()) {
			if ($form->isValid()) {

				$em = $this->getDoctrine()->getManager();
				$em->persist($table);
				$em->flush();

				$url = $this->generateUrl('database_table_list');
				if ($form->get('save_and_add')->isClicked())
					$url = $this->generateUrl('database_table_new');
				$response = new RedirectResponse($url);
				$this->addFlash(
					'success',
					$this->get('translator')->trans('table.edit.success', array(), 'BusybeeDatabaseBundle')
				);
				return $response;
	
			} else {

				$this->addFlash(
					'danger',
					$this->get('translator')->trans('table.edit.failed', array(), 'BusybeeDatabaseBundle')
				);
				   
			}
		}

        return $this->render('BusybeeDatabaseBundle:Table:new.html.twig', array(
									'table'		=> $table,
									'form'		=> $form->createView(),
			)
		);
    }

    /**
     * Create a Enumerator Record
     */
    public function enumeratorAction(Request $request)
    {
		if (true !== ($response = $this->get('busybee_security.authorisation.checker')->redirectAuthorisation('ROLE_REGISTRAR'))) return $response;

		$enumerator = $this->get('enumerator.repository')->createNew();

		$form = $this->createForm('\Busybee\DatabaseBundle\Form\EnumeratorType', $enumerator);
		$form
			->add('cancel', '\Symfony\Component\Form\Extension\Core\Type\ButtonType', array(
					'label'					=> 'form.cancel', 
					'translation_domain' 	=> 'BusybeeDisplayBundle',
					'attr' 					=> array(
						'formnovalidate' 		=> 'formnovalidate',
						'class' 				=> 'btn btn-info glyphicons glyphicons-exclamation-sign',
						'onClick'				=> 'location.href=\''.$this->generateUrl('database_enumerator_list')."'",
					),
				)
			)
		;


        $form->handleRequest($request);

		if ($form->isSubmitted()) {
			if ($form->isValid()) {

				$em = $this->getDoctrine()->getManager();
				$em->persist($enumerator);
				$em->flush();
				
				$url = $this->generateUrl('database_enumerator_list');
				if ($form->get('save_and_add')->isClicked())
					$url = $this->generateUrl('database_enumerator_new');
				$response = new RedirectResponse($url);
				$this->addFlash(
					'success',
					$this->get('translator')->trans('enumerator.edit.success', array(), 'BusybeeDatabaseBundle')
				);
				return $response;
	
			} else {

				$this->addFlash(
					'danger',
					$this->get('translator')->trans('enumerator.edit.failed', array(), 'BusybeeDatabaseBundle')
				);
				   
			}
		}

        return $this->render('BusybeeDatabaseBundle:Enumerator:new.html.twig', array(
									'field' 			=> $enumerator,
									'form' 				=> $form->createView(),
			)
		);
    }


}
