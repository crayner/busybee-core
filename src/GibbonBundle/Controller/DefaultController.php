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
	public function indexAction($offset = 0, $function = 'importPeople')
	{
		$this->denyAccessUnlessGranted('ROLE_SYSTEM_ADMIN', null, null);

		if ($offset == 0)
			$this->get('gibbon.model.import_houses');

		$people = [];
		if ($function === 'importPeople')
		{
			$people = $this->get('gibbon.model.import_people')->$function($offset);

			return $this->render('@Gibbon/Default/index.html.twig', ['people' => $people]);
		}
		elseif ($function === 'importFamily')
		{
			$people = $this->get('gibbon.model.import_family')->$function($offset);

			return $this->render('@Gibbon/Default/family.html.twig', ['families' => $people]);
		}
	}
}
