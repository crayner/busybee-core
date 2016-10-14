<?php

namespace Busybee\DatabaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use Symfony\Component\HttpFoundation\Request ;


class LoadController extends Controller
{
    /**
     * List Fields
     */
    public function uploadAction(Request $request)
    {
		if (true !== ($response = $this->get('busybee_security.authorisation.checker')->redirectAuthorisation('ROLE_SYSTEM_ADMIN'))) return $response;
		
		$file = new \Busybee\DatabaseBundle\Entity\File() ;
        
		$form = $this->createForm('Busybee\DatabaseBundle\Form\LoadDatabaseType', $file);

        $form->handleRequest($request);

		$messages = array();
		
		if ($form->isSubmitted()) {
			if ($form->isValid()) {

				$dm = $this->get('database.manager');
				
				$messages = $dm->manageUpload($form);

				$this->addFlash(
					'success',
					$this->get('translator')->trans('database.load.success', array(), 'BusybeeDatabaseBundle')
				);
	
			} else {

				$this->addFlash(
					'danger',
					$this->get('translator')->trans('database.load.failed', array(), 'BusybeeDatabaseBundle')
				);
				   
			}
		}

        return $this->render('BusybeeDatabaseBundle:Load:load.html.twig', array(
				'form' 				=> $form->createView(),
				'messages'			=> $messages,
			)
		);
    }

}
