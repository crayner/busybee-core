<?php
namespace Busybee\People\PersonBundle\Controller;

use Busybee\Core\TemplateBundle\Controller\BusybeeController;
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

		$person = $this->getPerson($id);

		$em = $this->get('doctrine')->getManager();

		$formDefinition = $this->getParameter('person');

		unset($formDefinition['person'], $formDefinition['contact'], $formDefinition['address1'], $formDefinition['address2']);

		$editOptions = array();

		$year = $person->yearData = $this->get('busybee_core_security.doctrine.user_manager')->getSystemYear($this->getUser());

		$form = $this->createForm(PersonType::class, $person);

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
				{
					return new RedirectResponse($this->generateUrl('person_edit', array('id' => $person->getId())));
				}
			}
		}

		$editOptions['id']            = $id;
		$editOptions['form']          = $form->createView();
		$editOptions['fullForm']      = $form;
		$editOptions['address1']      = $this->formatAddress($person->getAddress1());
		$editOptions['address2']      = $this->formatAddress($person->getAddress2());
		$editOptions['addressLabel1'] = $this->get('busybee_people_person.model.address_manager')->getAddressListLabel($person->getAddress1());
		$editOptions['addressLabel2'] = $this->get('busybee_people_person.model.address_manager')->getAddressListLabel($person->getAddress2());
		$editOptions['identifier']    = $person->getIdentifier();
		$editOptions['addresses']     = $this->get('busybee_people_person.model.person_manager')->getAddresses($person);
		$editOptions['phones']        = $this->get('busybee_people_person.model.person_manager')->getPhones($person);
		$editOptions['year']          = $year;

		return $this->render('BusybeePersonBundle:Person:edit.html.twig',
			$editOptions
		);
	}

}