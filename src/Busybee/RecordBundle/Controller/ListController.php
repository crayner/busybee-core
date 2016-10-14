<?php

namespace Busybee\RecordBundle\Controller ;

use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use Symfony\Component\HttpFoundation\Request ;
use Symfony\Component\HttpFoundation\JsonResponse ;
use Busybee\DisplayBundle\Model\MessageManager ;

class ListController extends Controller
{
    /**
     * List Records
     */
    public function indexAction($table_form, Request $request)
    {

		if (true !== ($response = $this->get('record_security')->testAuthorisation($table_form))) return $response;

		$pagination = $this->get('record.pagination.manager')->recordDefaults($table_form)->controlButton($request);
		
        $form = $this->get('form.factory')->createNamedBuilder('paginator', 'Busybee\RecordBundle\Form\RecordPaginationType', $pagination->getPagination())->getForm();

        $form->handleRequest($request);
		
		$pagination->injectForm($form);

		$mm = new MessageManager($this->get('translator'), $request->getLocale(), 'BusybeeRecordBundle');

		$allFields = $pagination->listManager();

		if ($form->isSubmitted()) {
			
			if ($form->isValid()) {

				return new JsonResponse(
					array(
						'form' => $this->renderView('BusybeeRecordBundle:Display:list_content.html.twig',
							array(
								'listdata' 			=> $allFields,
								'form' 				=> $form->createView(),
								'pagination'		=> $pagination->getPagination(),
							)
						)
					), 200 
				);
	
			} else {

				return new JsonResponse(
					array(
						'form' => $this->renderView('BusybeeRecordBundle:Display:list_content.html.twig',
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

        return $this->render('BusybeeRecordBundle:Display:list.html.twig', array(
				'listdata' 			=> $allFields,
				'form' 				=> $form->createView(),
				'pagination'		=> $pagination->getPagination(),
			)
		);
    }
}
