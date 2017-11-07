<?php

namespace Busybee\Core\SystemBundle\Controller;

use Busybee\Core\SystemBundle\Form\HousesType;
use Busybee\Core\TemplateBundle\Controller\BusybeeController;
use Symfony\Component\HttpFoundation\Request;
use Busybee\Core\TemplateBundle\Type\YamlArrayType;


class HouseController extends BusybeeController
{


	/**
	 * @param Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function editAction(Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR');

		$hm = $this->get('busybee_core_system.model.house_manager');

		$form = $this->createForm(HousesType::class, $hm);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$hm->saveHouses($form);
		}

		return $this->render('@System/House/edit.html.twig',
			[
				'form'     => $form->createView(),
				'fullForm' => $form,
			]
		);
	}
}