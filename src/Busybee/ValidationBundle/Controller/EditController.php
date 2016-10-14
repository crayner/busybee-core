<?php

namespace General\ValidationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use Symfony\Component\HttpFoundation\Request ;
use Busybee\DisplayBundle\Model\MessageManager ;
use Symfony\Component\HttpFoundation\JsonResponse ;
use General\ValidationBundle\Form\ValidatorType ;

class EditController extends Controller
{
    /**
     * Edit the user
     */
    public function indexAction($val_id, Request $request)
    {
		if (true !== ($response = $this->get('busybee_security.authorisation.checker')->redirectAuthorisation('ROLE_REGISTRAR'))) return $response;

		if ($val_id > 0)
			$validator = $this->get('validation.repository')->find($val_id);
		else
			$validator = $this->get('validation.repository')->createNew();

		$form = $this->createForm(new ValidatorType, $validator);

        $form->handleRequest($request);

		$mm = new MessageManager($this->get('translator'), $request->getLocale(), 'GeneralValidationBundle');

		if ($form->isSubmitted()) {
			if ($form->isValid()) {

				$em = $this->getDoctrine()->getEntity();
				$em->persist($validator);
				$em->flush();
				
				return new JsonResponse(
					array(
						'message' => $mm->message('validation.edit.success', 'success'),
						'form' => $this->renderView('GeneralValidationBundle:Validation:edit_content.html.twig',
							array(
									'validator' 			=> $validator,
									'form' 					=> $form->createView(),
								)
							)
						), 
						302);
	
			} else {

				foreach( $form->getErrors() as $error )
					$mm->message($error->getMessage(), 'danger');

				return new JsonResponse(
					array(
						'message' => $mm->getMessages(),
						'form' => $this->renderView('GeneralValidationBundle:Validation:edit_content.html.twig',
							array(
									'validator' 			=> $validator,
									'form' 					=> $form->createView(),
								)
							)
						), 
						400);
			}
		}

        return $this->render('GeneralValidationBundle:Validation:edit.html.twig', array(
				'validator' 			=> $validator,
				'form' 					=> $form->createView(),
			)
		);
    }
}
