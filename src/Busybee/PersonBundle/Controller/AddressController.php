<?php

namespace Busybee\PersonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse ;
use Busybee\PersonBundle\Entity\Address ;
use Busybee\PersonBundle\Entity\Locality ;
use Busybee\PersonBundle\Form\AddressType ;

class AddressController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function checkAction(Request $request)
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, $this->get('translator')->trans('security.denied', array(), 'BusybeeHomeBundle'));
		
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

    /**
     * @param string $id
     * @param Request $request
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($id = 'Add', Request $request)
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, $this->get('translator')->trans('security.denied', array(), 'BusybeeHomeBundle'));
		
		$address = new Address();
		$repo = $this->get('address.repository');
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
        } elseif ($form->isSubmitted())
        {
            $sess = $request->getSession();
            $sess->getFlashBag()->add('danger', 'address.save.failure');
        }

        return $this->render('BusybeePersonBundle:Address:index.html.twig',
			array('id' => $id, 'form' => $form->createView())			
		);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function fetchAction(Request $request)
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, $this->get('translator')->trans('security.denied', array(), 'BusybeeHomeBundle'));
		
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
				'addressDisabled' => 'true',
				'id' => $address->getId(),
			),
			200
		);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function editAction(Request $request)
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, $this->get('translator')->trans('security.denied', array(), 'BusybeeHomeBundle'));
		
		$id = $request->request->get('id');

		$entity = $id > 0 ? $this->get('address.repository')->find($id) : new Address();
		$entity->injectRepository($this->get('address.repository')) ;

		$entity->localityRecord = $this->get('locality.repository')->find($request->request->get('locality_id'));

		$valid = true;

		if ($entity->localityRecord instanceof Locality) 
			$entity->setLocality($entity->localityRecord);
		else
		{
			$entity->setLocality(null);
			$valid = false;
		}


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

        $streetName = $request->request->get('streetName');
        if (empty($streetName)) {
            $valid = false;
            $entity = new Address() ;
            $errors = array();
        }
        else{
            $entity->setStreetName($streetName);
		    $errors = $this->get('validator')->validate($entity);
        }
        dump($entity);
        dump($errors);
        dump($request);

		if ($valid && count($errors) == 0)
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

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function fetchListAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, $this->get('translator')->trans('security.denied', array(), 'BusybeeHomeBundle'));

        $addresses = $this->get('address.repository')->findBy(array(), array('propertyName'=>'ASC', 'streetName'=>'ASC', 'streetNumber'=> 'ASC'));
        $addressList = array();
        $am = $this->get('address.manager');
        if (is_array($addresses))
            foreach($addresses as $xx)
            {
                $x = array();
                $x['label'] = $am->getAddressListLabel($xx);
                $x['value'] = $xx->getId();
                $addressList[] = $x;
            }

        return new JsonResponse(
            array(
                'addressList' => $addressList,
            ),
            200
        );
    }


    /**
     * @param int $id
     * @return RedirectResponse
     */
    public function deleteAction($id, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, 'Unable to access this page!');

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
            } else
            {
                $sess->getFlashBag()->add('warning', 'address.delete.notAllowed');
            }
        }

        return new RedirectResponse($this->get('router')->generate('address_manage', array('id' => 'Add')));
    }
}
