<?php

namespace Busybee\People\PersonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Busybee\People\PersonBundle\Entity\Address;
use Busybee\People\PersonBundle\Entity\Locality;
use Busybee\People\PersonBundle\Form\AddressType;

class AddressController extends Controller
{
	use \Busybee\Core\SecurityBundle\Security\DenyAccessUnlessGranted;

	/**
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function checkAction(Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_ADMIN');

		$address                   = array();
		$address['propertyName']   = $request->request->get('propertyName');
		$address['streetName']     = $request->request->get('streetName');
		$address['streetNumber']   = $request->request->get('streetNumber');
		$address['buildingNumber'] = $request->request->get('buildingNumber');
		$address['buildingType']   = $request->request->get('buildingType');
		$address['locality']       = $request->request->get('locality');
		$address['territory']      = $request->request->get('territory');
		$address['postCode']       = $request->request->get('postCode');
		$address['country']        = $request->request->get('country');

		return new JsonResponse(
			$this->get('address.manager')->testAddress($address),
			200
		);

	}

	/**
	 * @param string  $id
	 * @param Request $request
	 *
	 * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function indexAction($id = 'Add', Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_ADMIN');

		$address = new Address();
		$repo    = $this->get('address.repository');
		if ($id !== 'Add')
			$address = $repo->find($id);

		$address->injectRepository($this->get('address.repository'));

		$form = $this->createForm(AddressType::class, $address);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$em = $this->get('doctrine')->getManager();
			$em->persist($address);
			$em->flush();

			$id = $address->getId();

			$sess = $request->getSession();
			$sess->getFlashBag()->add('success', 'address.save.success');

			return new RedirectResponse($this->get('router')->generate('address_manage', array('id' => $id)));
		}
		elseif ($form->isSubmitted())
		{
			$sess = $request->getSession();
			$sess->getFlashBag()->add('danger', 'address.save.failure');
		}

		return $this->render('BusybeePersonBundle:Address:index.html.twig',
			array('id' => $id, 'form' => $form->createView())
		);
	}


	/**
	 * @param int     $id
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function fetchAction(Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_ADMIN');

		$addresses = $this->get('address.repository')->findBy(array(), array('propertyName' => 'ASC', 'streetName' => 'ASC', 'streetNumber' => 'ASC'));
		$addresses = is_array($addresses) ? $addresses : array();

		$options   = array();
		$option    = array('value' => "", "label" => $this->get('translator')->trans('person.placeholder.address', array(), 'BusybeePersonBundle'));
		$options[] = $option;
		$am        = $this->get('address.manager');
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

	/**
	 * @param int $id
	 *
	 * @return RedirectResponse
	 */
	public function deleteAction($id, Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_ADMIN');

		if ($id > 0 && $entity = $this->get('address.repository')->find($id))
		{
			$entity->injectRepository($this->get('address.repository'));
			$sess = $request->getSession();
			if ($entity->canDelete())
			{
				$em = $this->get('doctrine')->getManager();
				$em->remove($entity);
				$em->flush();

				$sess->getFlashBag()->add('success', 'address.delete.success');
			}
			else
			{
				$sess->getFlashBag()->add('warning', 'address.delete.notAllowed');
			}
		}

		return new RedirectResponse($this->get('router')->generate('address_manage', array('id' => 'Add')));
	}
}
