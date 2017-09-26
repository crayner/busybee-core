<?php
namespace Busybee\People\PersonBundle\Controller;

use Busybee\Core\TemplateBundle\Controller\BusybeeController;
use Busybee\People\PersonBundle\Form\PersonType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class PersonController extends BusybeeController
{
	/**
	 * @param Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function indexAction(Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_ADMIN');

		$up = $this->get('busybee_people_person.pagination.person_pagination');

		$up->injectRequest($request);

		$up->getDataSet();

		return $this->render('BusybeePersonBundle:Person:index.html.twig',
			array(
				'pagination' => $up,
				'manager'    => $this->get('busybee_people_person.model.person_manager'),
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
		$this->denyAccessUnlessGranted('ROLE_ADMIN');

		$pm = $this->get('busybee_people_person.model.person_manager');

		$person = $pm->getPerson($id);

		$em = $this->get('doctrine')->getManager();

		$tm = $this->get('busybee_core_template.model.tab_manager');

		$formDefinition = $tm->loadDefinition('PersonTabs');

		unset($formDefinition['person'], $formDefinition['contact'], $formDefinition['address1'], $formDefinition['address2']);

		$editOptions = array();

		$year = $person->yearData = $this->get('busybee_core_security.doctrine.user_manager')->getSystemYear($this->getUser());

		$form               = $this->createForm(PersonType::class, $person, ['deletePhoto' => $this->generateUrl('person_photo_remove', ['id' => $id]), 'isSystemAdmin' => $this->isGranted('ROLE_SYSTEM_ADMIN')]);

		foreach ($formDefinition as $extra)
		{
			if (isset($extra['form']) && isset($extra['name']))
			{
				$options = array();
				if (!empty($extra['options']) && is_array($extra['options']))
					$options = $extra['options'];
				$name            = $extra['data']['name'];
				$options['data'] = $this->get($name)->findOneByPerson($person->getId());
				$options['data']->setPerson($person);
				$form->add($extra['name'], $extra['form'], $options);
				$name          = $extra['name'];
				$person->$name = $options['data'];

			}
			if (isset($extra['script']))
				$editOptions['script'][] = $extra['script'];
		}

		$form->handleRequest($request);

		$validator = $this->get('validator');

		if ($form->isSubmitted() && $form->isValid())
		{
			$ok = true;

			foreach ($formDefinition as $defined)
			{
				$req = isset($defined['request']['post']) ? $defined['request']['post'] : null;
				if (!is_null($req) && isset($person->$req))
				{
					$entity = $person->$req;
					$errors = $validator->validate($entity);
					if (count($errors) > 0)
					{
						foreach ($errors as $w)
						{
							$subForm = $form->get($req);
							$field   = $w->getConstraint()->errorPath;
							if (null !== $subForm->get($field))
								$subForm->get($field)->addError(new FormError($w->getMessage(), $w->getParameters()));
						}
						$ok = false;
					}
					if ($ok)
					{
						$em->persist($person->$req);
					}
				}
			}

			if ($ok)
			{
				$em->persist($person);
				$em->flush();
				if ($id === 'Add')
					return $this->redirectToRoute('person_edit', array('id' => $person->getId()));
			}
		}

		$editOptions['id']            = $id;
		$editOptions['form']          = $form->createView();
		$editOptions['fullForm']      = $form;
		$editOptions['address1']      = $this->get('busybee_people_address.model.address_manager')->formatAddress($person->getAddress1());
		$editOptions['address2']      = $this->get('busybee_people_address.model.address_manager')->formatAddress($person->getAddress2());
		$editOptions['addressLabel1'] = $this->get('busybee_people_address.model.address_manager')->getAddressListLabel($person->getAddress1());
		$editOptions['addressLabel2'] = $this->get('busybee_people_address.model.address_manager')->getAddressListLabel($person->getAddress2());
		$editOptions['identifier']    = $person->getIdentifier();
		$editOptions['addresses']     = $this->get('busybee_people_person.model.person_manager')->getAddresses($person);
		$editOptions['phones']        = $this->get('busybee_people_person.model.person_manager')->getPhones($person);
		$editOptions['year']          = $year;
		$editOptions['tabs']          = $tm;
		$editOptions['personManager'] = $pm;

		return $this->render('BusybeePersonBundle:Person:edit.html.twig',
			$editOptions
		);
	}

	/**
	 * @param $id
	 *
	 * @return Response
	 */
	public function removePhotoAction($id)
	{
		$this->denyAccessUnlessGranted('ROLE_ADMIN');

		$person = $this->getPerson($id);

		$em = $this->get('doctrine')->getManager();

		$photo = $person->getPhoto();

		$person->setPhoto(null);

		if (file_exists($photo))
			unlink($photo);

		$em->persist($person);
		$em->flush();

		return new Response("<script>window.close();</script>");
	}
}