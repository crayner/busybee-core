<?php

namespace General\ValidationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use Symfony\Component\HttpFoundation\Request ;
use Busybee\DisplayBundle\Model\MessageManager ;
use Symfony\Component\HttpFoundation\JsonResponse ;

class ListController extends Controller
{
    /**
     * Edit the user
     */
    public function indexAction(Request $request)
    {
		if (true !== ($response = $this->get('busybee_security.authorisation.checker')->redirectAuthorisation('ROLE_REGISTRAR'))) return $response;
		
		$pagination = $this->getParameter('validation.list.pagination');

        $form = $this->createForm(new \General\ValidationBundle\Form\PaginationType, $pagination);

        $form->handleRequest($request);

		$mm = new MessageManager($this->get('translator'), $request->getLocale(), 'GeneralValidationBundle');

		$allValidators = $this->get('validation.repository')->findAll();

		if ($form->isSubmitted()) {
			if ($form->isValid()) {

				return new JsonResponse(
					array(
						'form' => $this->renderView('GeneralValidationBundle:Validation:list_content.html.twig',
							array(
									'listdata' 			=> $allValidators,
									'form' 			=> $form->createView(),
								)
							)
						), 
						200);
	
			} else {

				return new JsonResponse(
					array(
						'message' => $mm->message('institute.edit.error', 'danger'),
						'form' => $this->renderView('GeneralValidationBundle:Validation:list_content.html.twig',
							array(
									'listdata' 			=> $allValidators,
									'form' 			=> $form->createView(),
								)
							)
						), 
						400);
			}
		}

        return $this->render('GeneralValidationBundle:Validation:list.html.twig', array(
				'listdata' 			=> $allValidators,
				'form' 			=> $form->createView(),
			)
		);
    }
}
