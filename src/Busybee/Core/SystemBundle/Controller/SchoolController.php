<?php

namespace Busybee\Core\SystemBundle\Controller;

use Busybee\Core\SystemBundle\Form\DaysTimesType;
use Busybee\Core\TemplateBundle\Controller\BusybeeController;
use Symfony\Component\HttpFoundation\Request;

class SchoolController extends BusybeeController
{


	/**
	 * @param Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function daysAndTimesAction(Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR');

		$dtm = $this->get('busybee_core_system.model.days_times_manager');

		$form = $this->createForm(DaysTimesType::class, $dtm);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$dtm->saveDaysTimes($form);
		}

		return $this->render('@System/School/daysandtimes.html.twig',
			[
				'form'     => $form->createView(),
				'fullForm' => $form,
			]
		);
	}
}