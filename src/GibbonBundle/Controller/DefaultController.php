<?php

namespace GibbonBundle\Controller;

use Busybee\Core\TemplateBundle\Controller\BusybeeController;

class DefaultController extends BusybeeController
{
	/**
	 * @param int $offset
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function indexAction($offset = 0)
	{
		$this->denyAccessUnlessGranted('ROLE_SYSTEM_ADMIN', null, null);

		if ($offset == 0)
			$this->get('gibbon.model.import_houses');


		$people = $this->get('gibbon.model.import_people')->importPeople($offset);


		return $this->render('GibbonBundle:Default:index.html.twig', ['people' => $people]);
	}
}
