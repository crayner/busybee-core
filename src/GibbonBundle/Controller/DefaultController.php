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

		if ($function === 'importPeople')
		{
			$people = $this->get('gibbon.model.import_people')->$function($offset);

			return $this->render('@Gibbon/Default/index.html.twig', ['people' => $people]);
		}
		elseif ($function === 'importFamily')
		{
			$manager = $this->get('gibbon.model.import_family')->$function($offset);

			return $this->render('@Gibbon/Default/family.html.twig', ['families' => $manager]);
		}
		elseif ($function === 'importDepartments')
		{
			$manager = $this->get('gibbon.model.import_departments')->$function($offset);

			return $this->render('@Gibbon/Default/departments.html.twig', ['manager' => $manager]);
		}
		elseif ($function === 'importYear')
		{
			$manager = $this->get('gibbon.model.import_year');

			return $this->render('@Gibbon/Default/year.html.twig', ['manager' => $manager]);
		}
		elseif ($function === 'importGrades')
		{
			dump($this);
			die();
			$manager = $this->get('gibbon.model.import_grades');

			return $this->render('@Gibbon/Default/year.html.twig', ['manager' => $manager]);
		}
		elseif ($function === 'importSomething')
		{
			dump($this);
			die();
			$manager = $this->get('gibbon.model.import_year');

			return $this->render('@Gibbon/Default/year.html.twig', ['manager' => $manager]);
		}
	}
}
