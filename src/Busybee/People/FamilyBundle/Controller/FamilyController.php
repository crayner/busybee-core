<?php
namespace Busybee\People\FamilyBundle\Controller;

use Busybee\People\FamilyBundle\Form\FamilyType;
use Busybee\Core\TemplateBundle\Controller\BusybeeController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class FamilyController extends BusybeeController
{


	/**
	 * @param Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function indexAction(Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);

		$up = $this->get('busybee_people_family.model.family_pagination');

		$up->injectRequest($request);

		$up->getDataSet();

		return $this->render('BusybeeFamilyBundle:Family:index.html.twig',
			array(
				'pagination' => $up,
			)
		);
	}

	/**
	 * @param Request $request
	 * @param         $id
	 *
	 * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function editAction(Request $request, $id)
	{
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);

		$family = $this->get('busybee_people_family.repository.family_repository')->find($id);

		$form = $this->createForm(FamilyType::class, $family);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$em = $this->get('doctrine')->getManager();
			$em->persist($family);
			$em->flush();
			$id = $family->getId();

			return new RedirectResponse($this->generateUrl('family_edit', array('id' => $id)));

		}

		$editOptions              = array();
		$editOptions['id']        = $id;
		$editOptions['family_id'] = $id;
		$editOptions['form']      = $form->createView();
		$editOptions['fullForm']  = $form;

		return $this->render('BusybeeFamilyBundle:Family:edit.html.twig',
			$editOptions
		);
	}
}