<?php
namespace Busybee\People\AddressBundle\Controller;

use Busybee\Core\TemplateBundle\Controller\BusybeeController;
use Busybee\People\AddressBundle\Form\AddressType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AddressController extends BusybeeController
{
	/**
	 * @param string  $id
	 * @param Request $request
	 *
	 * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function indexAction($id = 'Add', Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_ADMIN');

		$am      = $this->get('busybee_people_address.model.address_manager');
		$address = $am->find($id);

		$form = $this->createForm(AddressType::class, $address, ['manager' => $am]);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$em = $this->get('doctrine')->getManager();
			$em->persist($address);
			$em->flush();

			$sess = $request->getSession();
			$sess->getFlashBag()->add('success', 'address.save.success');
			if ($id === 'Add')
			{
				$id = $address->getId();

				return $this->redirectToRoute('address_manage', array('id' => $id));
			}
		}
		elseif ($form->isSubmitted())
		{
			$sess = $request->getSession();
			$sess->getFlashBag()->add('danger', 'address.save.failure');
		}

		return $this->render('@BusybeeAddress/Address/index.html.twig',
			[
				'id'      => $id,
				'form'    => $form->createView(),
				'manager' => $am,
			]
		);
	}

	/**
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function fetchAction(Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_ADMIN');

		$addresses = $this->get('busybee_people_address.repository.address_repository')->findBy(array(), array('propertyName' => 'ASC', 'streetName' => 'ASC', 'streetNumber' => 'ASC'));
		$addresses = is_array($addresses) ? $addresses : array();

		$options   = array();
		$option    = array('value' => "", "label" => $this->get('translator')->trans('person.address.placeholder', array(), 'BusybeePersonBundle'));
		$options[] = $option;
		$am        = $this->get('busybee_people_address.model.address_manager');
		foreach ($addresses as $address)
		{
			$option    = array('value' => strval($address->getId()), "label" => $am->getAddressListLabel($address));
			$options[] = $option;
		}

		return new JsonResponse(
			array(
				'options' => $options,
			),
			200
		);
	}

}
