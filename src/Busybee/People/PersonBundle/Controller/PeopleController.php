<?php
namespace Busybee\People\PersonBundle\Controller;

use Busybee\Core\TemplateBundle\Controller\BusybeeController;
use Busybee\People\PersonBundle\Form\ImportType;
use Busybee\People\PersonBundle\Form\MatchImportType;
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

		$form = $this->createForm(ImportType::class, $data, ['action' => $this->generateUrl('people_import_match')]);


		return $this->render('@BusybeePerson/People/import.html.twig',
			array(
				'form' => $form->createView(),
			)
		);
	}

	/**
	 * @param Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function matchAction(Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR');

		$data              = array();
		$data['returnURL'] = $this->generateUrl('people_import');

		$files = $request->files->get('import');

		$file = reset($files);

		$im = $this->get('busybee_people_person.model.import_manager');

		$im->setFile($this->get('busybee_core_template.model.file_upload')->upload($file));

		$form = $this->createForm(MatchImportType::class, $im, ['manager' => $im, 'action' => $this->generateUrl('people_import_data')]);

		return $this->render('@BusybeePerson/People/importMatch.html.twig',
			array(
				'form' => $form->createView(),
			)
		);
	}

	/**
	 * @param Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function dataAction(Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR');

		$import = $request->get('match_import');

		$results = $this->get('busybee_people_person.model.import_manager')->importPeople($import, $request->getSession());

		return $this->render('BusybeePersonBundle:People:importResults.html.twig',
			array(
				'results' => $results,
				'import'  => $import,
			)
		);
	}
}