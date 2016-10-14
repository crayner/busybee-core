<?php

namespace Busybee\InstituteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use Symfony\Component\HttpFoundation\Request ;
use Busybee\DisplayBundle\Model\MessageManager ;
use Symfony\Component\HttpFoundation\JsonResponse ;

class InstituteController extends Controller
{
    /**
     * Edit the user
     */
    public function editAction(Request $request)
    {
		if (true !== ($response = $this->get('busybee_security.authorisation.checker')->redirectAuthorisation('ROLE_REGISTRAR'))) return $response;
		
		$institute = $this->get('institute.repository')->find(1);

        $form = $this->createForm(new \Busybee\InstituteBundle\Form\InstituteType, $institute);

        $form->handleRequest($request);

		$mm = new MessageManager($this->get('translator'), $request->getLocale(), 'BusybeeInstituteBundle');
		
		if ($form->isSubmitted()) {
			if ($form->isValid()) {

				$em = $this->getDoctrine()->getManager();
		
				$em->persist($institute);
				$em->flush();

dump($form);
				return new JsonResponse(
					array(
						'message' => $mm->message('institute.edit.success', 'success'),
						'form' => $this->renderView('BusybeeInstituteBundle:Institute:edit_content.html.twig',
							array(
								'institute' => $institute,
								'form' => $form->createView(),
								)
							)
						), 
						200);
	
			} else {

dump($form);
				return new JsonResponse(
					array(
						'message' => $mm->message('institute.edit.error', 'danger'),
						'form' => $this->renderView('BusybeeInstituteBundle:Institute:edit_content.html.twig',
							array(
								'institute' => $institute,
								'form' => $form->createView(),
								)
							)
						), 
						400);
			}
		}

        return $this->render('BusybeeInstituteBundle:Institute:edit.html.twig', array(
				'institute' 	=> $institute,
				'form' 			=> $form->createView(),
			)
		);
    }
}
