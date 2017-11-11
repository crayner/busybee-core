<?php

namespace Busybee\People\UserBundle\Controller;

use Busybee\Core\TemplateBundle\Controller\BusybeeController;
use Symfony\Component\HttpFoundation\Request;

class UserController extends BusybeeController
{
	/**
	 * @param Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function listAction(Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_ADMIN');

		$up = $this->get('busybee_people_user.pagination.user_pagination');

		$up->injectRequest($request);

		$up->getDataSet();

		return $this->render('BusybeeUserBundle:User:index.html.twig',
			array(
				'pagination' => $up,
				'manager'    => $this->get('busybee_people_person.model.person_manager'),
			)
		);
	}

}