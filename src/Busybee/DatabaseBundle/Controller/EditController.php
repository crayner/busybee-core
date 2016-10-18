<?php

namespace Busybee\DatabaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use Symfony\Component\HttpFoundation\Request ;
use Symfony\Component\HttpFoundation\JsonResponse ;
use Symfony\Component\HttpFoundation\RedirectResponse ;

class EditController extends Controller
{
    /**
     * Modify Field Record
     */
    public function fieldAction(Request $request, $field_id)
    {
		if (true !== ($response = $this->get('busybee_security.authorisation.checker')->redirectAuthorisation('ROLE_REGISTRAR'))) return $response;

		$field = $this->get('field.repository')->find($field_id);
		$field->enumeratorRepository = $this->get('enumerator.repository');

		$form = $this->createForm( '\Busybee\DatabaseBundle\Form\FieldType', $field);
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
		if ( $this->get('record.entity.manager')->fieldCount($field) === 0 )
			$form
				->add('delete', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
						'label' 				=> 'form.delete',
						'translation_domain' 	=> 'BusybeeDisplayBundle',
						'attr' 					=> array(
							'class' 				=> 'btn btn-warning glyphicon glyphicon-minus-sign'
						),
					)
				)
			;
	
        $form->handleRequest($request);

		if ($form->isSubmitted()) {
			if ($form->isValid()) {

				$em = $this->getDoctrine()->getManager();

				if ($form->has('delete'))
					if ($form->get('delete')->isClicked() and $this->get('record.entity.manager')->fieldCount($field) === 0 )
					{
						$em->remove($field);
						$em->flush();
						$url = $this->generateUrl('database_field_list');
						$response = new RedirectResponse($url);
						$this->addFlash(
							'success',
							$this->get('translator')->trans('field.edit.delete', array(), 'BusybeeDatabaseBundle')
						);
						return $response ;
					}


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

        return $this->render('BusybeeDatabaseBundle:Field:edit.html.twig', array(
				'field' 				=> $field,
				'form' 					=> $form->createView(),
			)
		);
    }

    /**
     * Create a Table Record
     */
    public function tableAction(Request $request, $table_id)
    {
		if (true !== ($response = $this->get('busybee_security.authorisation.checker')->redirectAuthorisation('ROLE_REGISTRAR'))) return $response;

		$table = $this->get('table.repository')->find($table_id);

		$form = $this->createForm( 'Busybee\DatabaseBundle\Form\TableType', $table);
		$form
			->add('cancel', 'Symfony\Component\Form\Extension\Core\Type\ButtonType', array(
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
		
		$form->setData($table);
		
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

        return $this->render('BusybeeDatabaseBundle:Table:edit.html.twig', array(
									'table'		=> $table,
									'form'		=> $form->createView(),
			)
		);
    }

    /**
     * Modify Enumerator Record
     */
    public function enumeratorAction(Request $request, $enumerator_id)
    {
		if (true !== ($response = $this->get('busybee_security.authorisation.checker')->redirectAuthorisation('ROLE_REGISTRAR'))) return $response;

		$enumerator = $this->get('enumerator.repository')->find($enumerator_id);

		$form = $this->createForm($this->get('enumerator.formtype'), $enumerator);
		$form
			->add('cancel', 'button', array(
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

        return $this->render('BusybeeDatabaseBundle:Enumerator:edit.html.twig', array(
				'field' 				=> $enumerator,
				'form' 					=> $form->createView(),
			)
		);
    }


}
