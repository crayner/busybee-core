<?php

namespace Busybee\People\LocalityBundle\Controller;

use Busybee\People\LocalityBundle\Entity\Locality;
use Busybee\People\LocalityBundle\Form\LocalityType;
use Busybee\Core\TemplateBundle\Controller\BusybeeController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class LocalityController extends BusybeeController
{


	/**
	 * @param integer|string $id
	 * @param Request        $request
	 *
	 * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function indexAction($id, Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);

		$lm = $this->get('busybee_people_locality.model.locality_manager');

		$locality = $lm->find($id);

		$form = $this->createForm(LocalityType::class, $locality);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$em = $this->get('doctrine')->getManager();
			$em->persist($locality);
			$em->flush();

			$lm->addMessage('success', 'locality.save.success', ['%name%' => $locality->getFullLocality()]);

			$this->get('busybee_core_system.model.flash_bag_manager')->addMessages($lm->getMessageManager());

			if ($id === 'Add')
				return $this->redirectToRoute('locality_manage', array('id' => $locality->getId()));
		}

		return $this->render('@BusybeeLocality/Locality/index.html.twig',
			[
				'id'      => $id,
				'form'    => $form->createView(),
				'manager' => $lm,
			]
		);
	}

	/**
	 * @param int $id
	 *
	 * @return RedirectResponse
	 */
	public function deleteAction($id, Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);

		$lm     = $this->get('busybee_people_locality.model.locality_manager');
		$entity = $lm->find($id);

		if ($id > 0 && $entity instanceof Locality)
		{
			$name = $entity->getFullLocality();
			if ($lm->canDelete())
			{
				$em = $this->get('doctrine')->getManager();
				$em->remove($entity);
				$em->flush();

				$lm->addMessage('success', 'locality.delete.success', ['%name%' => $name]);
			}
			else
			{
				$lm->addmessage('warning', 'locality.delete.notAllowed', ['%name%' => $name]);
			}
		}

		$this->get('busybee_core_system.model.flash_bag_manager')->addMessages($lm->getMessageManager());

		return $this->redirectToRoute('locality_manage', array('id' => 'Add'));
	}

	/**
	 * Fetch Action
	 *
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function fetchAction(Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);

		$localities = $this->get('busybee_people_locality.repository.locality_repository')->findBy(array(), array('name' => 'ASC', 'postCode' => 'ASC'));
		$localities = is_array($localities) ? $localities : array();

		$options   = array();
		$option    = array('value' => "", "text" => $this->get('translator')->trans('address.placeholder.locality', array(), 'BusybeePersonBundle'));
		$options[] = $option;
		foreach ($localities as $locality)
		{
			$option    = array('value' => strval($locality->getId()), "text" => $locality->getFullLocality());
			$options[] = $option;
		}

		return new JsonResponse(
			array(
				'options' => $options,
			),
			200
		);
	}
}
