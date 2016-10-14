<?php

namespace Busybee\DatabaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use Symfony\Component\HttpFoundation\Request ;
use Symfony\Component\HttpFoundation\JsonResponse ;
use Busybee\DisplayBundle\Model\MessageManager ;

class ListController extends Controller
{
    /**
     * List Fields
     */
    public function fieldAction(Request $request)
    {

		$pagination = $this->get('field.pagination.manager')->controlButton($request);

        $form = $this->get('form.factory')->createNamedBuilder('paginator', 'Busybee\DatabaseBundle\Form\FieldPaginationType', $pagination->getPagination())->getForm();

		if (true !== ($response = $this->get('busybee_security.authorisation.checker')->ajaxAuthorisation('ROLE_REGISTRAR', $request))) return $response ;

        $form->handleRequest($request);
		
		$pagination->injectForm($form);

		$mm = new MessageManager($this->get('translator'), $request->getLocale(), 'BusybeeDatabaseBundle');

		$allFields = $pagination->listManager();

		if ($form->isSubmitted()) {
			
			if ($form->isValid()) {

				return new JsonResponse(
					array(
						'form' => $this->renderView('BusybeeDatabaseBundle:Field:list_content.html.twig',
							array(
								'listdata' 			=> $allFields,
								'form' 				=> $form->createView(),
								'pagination'		=> $pagination->getPagination(),
								'enum'				=> $this->get('enumerator.repository'),
							)
						)
					), 200 
				);
	
			} else {

				return new JsonResponse(
					array(
						'form' => $this->renderView('BusybeeDatabaseBundle:Field:list_content.html.twig',
							array(
									'listdata' 			=> $allFields,
									'form' 				=> $form->createView(),
									'pagination'		=> $pagination->getPagination(),
									'enum'				=> $this->get('enumerator.repository'),
								)
							)
						), 
						400);
			}
		}

        return $this->render('BusybeeDatabaseBundle:Field:list.html.twig', array(
				'listdata' 			=> $allFields,
				'form' 				=> $form->createView(),
				'pagination'		=> $pagination->getPagination(),
				'enum'				=> $this->get('enumerator.repository'),
			)
		);
    }


    /**
     * List Tables
     */
    public function tableAction(Request $request)
    {
		if (true !== ($response = $this->get('busybee_security.authorisation.checker')->redirectAuthorisation('ROLE_REGISTRAR'))) return $response;
 
		$pagination = $this->get('table.pagination.manager');

        $form = $this->get('form.factory')->createNamedBuilder('paginator', 'Busybee\DatabaseBundle\Form\TablePaginationType', $pagination->getPagination())->getForm();
 
        $form->handleRequest($request);

		$mm = new MessageManager($this->get('translator'), $request->getLocale(), 'BusybeeDatabaseBundle');

		$allTables = $pagination->listManager();

		if ($form->isSubmitted()) {
			if ($form->isValid()) {

				return new JsonResponse(
					array(
						'form' => $this->renderView('BusybeeDatabaseBundle:Table:list_content.html.twig',
							array(
									'listdata' 		=> $allTables,
									'form' 			=> $form->createView(),
									'pagination'	=> $pagination->getPagination(),
								)
							)
						), 
						200);
	
			} else {

				return new JsonResponse(
					array(
						'form' => $this->renderView('BusybeeDatabaseBundle:Table:list_content.html.twig',
							array(
									'listdata' 		=> $allTables,
									'form' 			=> $form->createView(),
									'pagination'	=> $pagination->getPagination(),
								)
							)
						), 
						400);
			}
		}
 
 
        return $this->render('BusybeeDatabaseBundle:Table:list.html.twig', array(
				'listdata' 		=> $allTables,
				'form' 			=> $form->createView(),
				'pagination'	=> $pagination->getPagination(),
			)
		);
	}


    /**
     * Sort Fields
     */
    public function sortFieldAction(Request $request)
    {
		if (true !== ($response = $this->get('busybee_security.authorisation.checker')->redirectAuthorisation('ROLE_REGISTRAR'))) return $response;
		
		$pagination = $this->get('field.pagination.manager');

        $form = $this->createForm(new \Busybee\DatabaseBundle\Form\FieldPaginationType, $pagination->getPagination());

        $form->handleRequest($request);

		$mm = new MessageManager($this->get('translator'), $request->getLocale(), 'BusybeeDatabaseBundle');

		$query = $pagination->initiateQuery();
		
		$allFields = $pagination->listManager();

		if ($form->isSubmitted()) {
			if ($form->isValid()) {

				return new JsonResponse(
					array(
						'form' => $this->renderView('BusybeeDatabaseBundle:Field:list_content.html.twig',
							array(
									'listdata' 			=> $allFields,
									'form' 				=> $form->createView(),
									'pagination'		=> $pagination->getPagination(),
								)
							)
						), 
						200);
	
			} else {

				return new JsonResponse(
					array(
						'form' => $this->renderView('BusybeeDatabaseBundle:Field:list_content.html.twig',
							array(
									'listdata' 			=> $allFields,
									'form' 				=> $form->createView(),
									'pagination'		=> $pagination->getPagination(),
								)
							)
						), 
						400);
			}
		}

        return $this->render('BusybeeDatabaseBundle:Field:list.html.twig', array(
				'listdata' 			=> $allFields,
				'form' 				=> $form->createView(),
				'pagination'		=> $pagination->getPagination(),
			)
		);
    }

    /**
     * List Enumerators
     */
    public function enumeratorAction(Request $request)
    {
		if (true !== ($response = $this->get('busybee_security.authorisation.checker')->redirectAuthorisation('ROLE_REGISTRAR'))) return $response;
 
		$pagination = $this->get('enumerator.pagination.manager');

        $form = $this->createForm(new \Busybee\DatabaseBundle\Form\EnumeratorPaginationType, $pagination->getPagination());

        $form->handleRequest($request);

		$mm = new MessageManager($this->get('translator'), $request->getLocale(), 'BusybeeDatabaseBundle');

		$allValidators = $pagination->listManager();

		if ($form->isSubmitted()) {
			if ($form->isValid()) {

				return new JsonResponse(
					array(
						'form' => $this->renderView('BusybeeDatabaseBundle:Enumerator:list_content.html.twig',
							array(
									'listdata' 		=> $allValidators,
									'form' 			=> $form->createView(),
									'pagination'	=> $pagination->getPagination(),
								)
							)
						), 
						200);
	
			} else {

				return new JsonResponse(
					array(
						'form' => $this->renderView('BusybeeDatabaseBundle:Enumerator:list_content.html.twig',
							array(
									'listdata' 		=> $allValidators,
									'form' 			=> $form->createView(),
									'pagination'	=> $pagination->getPagination(),
								)
							)
						), 
						400);
			}
		}
 
 
        return $this->render('BusybeeDatabaseBundle:Enumerator:list.html.twig', array(
				'listdata' 		=> $allValidators,
				'form' 			=> $form->createView(),
				'pagination'	=> $pagination->getPagination(),
			)
		);
	}

}
