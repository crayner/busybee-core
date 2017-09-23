<?php
namespace Busybee\People\PersonBundle\Controller;

use Busybee\Core\TemplateBundle\Controller\BusybeeController;
use Symfony\Component\HttpFoundation\Request;

class PeopleController extends BusybeeController
{
	/**
	 * @param Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function importAction(Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR');

		$data            = new \stdClass();
		$data->returnURL = $this->generateUrl('home_page');
		$data->action    = $this->generateUrl('people_import_match');

		$form = $this->createForm(ImportType::class, $data);


		return $this->render('BusybeePersonBundle:People:import.html.twig',
			array(
				'form' => $form->createView(),
			)
		);
	}

}