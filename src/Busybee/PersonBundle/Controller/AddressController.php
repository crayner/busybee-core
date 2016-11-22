<?php

namespace Busybee\PersonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse ;
use Busybee\PersonBundle\Entity\Address ;
use Busybee\PersonBundle\Form\AddressType ;

class AddressController extends Controller
{
    public function checkAction(Request $request)
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, 'Unable to access this page!');
		
		$address = array();
		$address['propertyName'] = $request->request->get('propertyName');
		$address['streetName'] = $request->request->get('streetName');
		$address['streetNumber'] = $request->request->get('streetNumber');
		$address['buildingNumber'] = $request->request->get('buildingNumber');
		$address['buildingType'] = $request->request->get('buildingType');
		$address['locality'] = $request->request->get('locality');
		$address['territory'] = $request->request->get('territory');
		$address['postCode'] = $request->request->get('postCode');
		$address['country'] = $request->request->get('country');

		return new JsonResponse(
				$this->get('address.manager')->testAddress($address), 
				200
			);
	
    }


    public function indexAction($id, Request $request)
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, 'Unable to access this page!');
		
		$address = new Address();
		$repo = $this->get('address.repository');
		if ($id !== 'Add')
			$address = $repo->findOneBy(array('id' => $id));
		$address->injectRepository($this->get('address.repository'));
		$address->localityRecord = $this->get('locality.repository')->setAddressLocality($address->getLocality());

        $form = $this->createForm(AddressType::class, $address);

        return $this->render('BusybeePersonBundle:Address:index.html.twig',
			array('id' => $id, 'form' => $form->createView())			
		);
    }

    public function fetchAction(Request $request)
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, 'Unable to access this page!');
		
		$id = $request->request->get('id');

		$address = $id > 0 ? $this->get('address.repository')->findOneBy(array('id' => $id)) : new Address();
		$address->localityRecord = $this->get('locality.repository')->setAddressLocality($address->getLocality());
		
		$list = $this->get('locality.repository')->getLocalityChoices();
		$localityOptions = '<option value="">'.$this->get('translator')->trans('locality.placeholder.choice', array(), 'BusybeePersonBundle').'</option>';
		foreach($list as $name=>$value) {
			$localityOptions .= '<option value="'.$value.'">'.$name.'</option>';	
		}
		return new JsonResponse(
			array(
				'propertyName' => $address->getPropertyName(),
				'streetName' => $address->getStreetName(),
				'streetNumber' => $address->getStreetNumber(),
				'buildingType' => $address->getBuildingType(),
				'buildingNumber' => $address->getBuildingNumber(),
				'locality_id' => is_null($address->getLocality()) ? 0 : (is_int($address->getLocality()) ? $address->getLocality() : $address->getLocality()->getId()),
				'territory' => $address->localityRecord->getTerritory(),
				'locality' => $address->localityRecord->getLocality(),
				'country' => $address->localityRecord->getCountry(),
				'postCode' => $address->localityRecord->getPostCode(),
				'options' => $localityOptions,
				'address' => $this->get('address.manager')->formatAddress($address),
				'addressListLabel' => $this->get('address.manager')->getAddressListLabel($address),
				'id' => $address->getId(),
			),
			200
		);
    }

    public function editAction(Request $request)
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, 'Unable to access this page!');
		
		$id = $request->request->get('id');

		$entity = $id > 0 ? $this->get('address.repository')->findOneBy(array('id' => $id)) : new Address();	
		$entity->injectRepository($this->get('address.repository')) ;
		$entity->localityRecord = $this->get('locality.repository')->setAddressLocality($request->request->get('locality_id'));
		$valid = true;

		$locality_id = $request->request->get('locality_id');		
		if (empty($locality_id)) 
			$valid = false;
		else
			$entity->setLocality($locality_id);

		$streetName = $request->request->get('streetName');		
		if (empty($streetName)) 
			$valid = false;
		else
			$entity->setStreetName($streetName);

		$entity->setPropertyName((empty($request->request->get('propertyName')) ? null : $request->request->get('propertyName')));
		$entity->setStreetNumber((empty($request->request->get('streetNumber')) ? null : $request->request->get('streetNumber')));
		if (empty($entity->getStreetNumber()) && intval($entity->getStreetName()) > 0)
		{
			$num = strval(intval($entity->getStreetName()));
			$entity->setStreetNumber($num);
			$entity->setStreetName(trim(str_replace($num, '', $entity->getStreetName())));
		}
		$entity->setBuildingType((empty($request->request->get('buildingType')) ? null : $request->request->get('buildingType')));
		$entity->setBuildingNumber((empty($request->request->get('buildingNumber')) ? null : $request->request->get('buildingNumber')));
		
		$addressList =$this->get('address.manager')->getAddressList($request->request->get('locality_id'));
		$al = array();
		foreach($addressList as $detail)
			$al[$detail['value']] = $detail['label'] ;
			
		if (in_array($this->get('address.manager')->getAddressListLabel($entity), $al))
		{
			$value = array_search($this->get('address.manager')->getAddressListLabel($entity), $al);
			if ($value !== $entity->getId())
				$valid = false ;
		}
		if ($valid)
		{
			$message = $this->get('translator')->trans('address.edit.success', array(), 'BusybeePersonBundle');
			$status = 'success';
			$em = $this->getDoctrine()->getManager();
            
            $em->persist($entity);
            $em->flush();
			$id = $entity->getId();
			$saved = true ;
		} else {
			$message = $this->get('translator')->trans('address.edit.failure', array(), 'BusybeePersonBundle');
			$status = 'danger';
			$saved = false ;
		}

		$addressList =$this->get('address.manager')->getAddressList($request->request->get('locality_id'));

		$addressDisabled = empty($addressList) || $saved ? 'true' : 'false';

		$formattedAddress = $this->get('address.manager')->formatAddress($entity);

		return new JsonResponse(
			array(
				'message' => $message,
				'status' => $status,
				'id' => $id,
				'locality' => $entity->getLocality(),
				'propertyName' => $entity->getPropertyName(),
				'streetName' => $entity->getStreetName(),
				'streetNumber' => $entity->getStreetNumber(),
				'buildingType' => $entity->getBuildingType(),
				'buildingNumber' => $entity->getBuildingNumber(),
				'address' => $formattedAddress,
				'addressListLabel' => $this->get('address.manager')->getAddressListLabel($entity),
				'addressList' => $addressList,
				'addressDisabled' => $addressDisabled,
			),
			200
		);
    }
}
