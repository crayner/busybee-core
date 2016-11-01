<?php

namespace Busybee\PersonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Busybee\PersonBundle\Entity\Person ;
use Busybee\PersonBundle\Entity\Address ;
use Busybee\PersonBundle\Entity\Locality ;
use Symfony\Component\HttpFoundation\RedirectResponse ;
use Symfony\Component\HttpFoundation\JsonResponse ;

class PersonController extends Controller
{
    public function indexAction(Request $request)
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, 'Unable to access this page!');
		
		$up = $this->get('person.pagination');
		
		$up->injectRequest($request);
		
		$up->getDataSet();

        return $this->render('BusybeePersonBundle:Person:index.html.twig', 
			array(
            	'pagination' => $up,
        	)
		);
    }


    public function editAction($id, Request $request)
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, 'Unable to access this page!');
		$person = new Person();
		if ($id !== 'Add')
			$person = $this->get('person.repository')->findOneBy(array('id' => $id));
		
		$person->cancelURL = $this->generateUrl('busybee_security_user_list');

		$person->setAddress1Record($this->get('address.repository')->setPersonAddress($person->getAddress1()));
		$person->getAddress1Record()->localityRecord = $this->get('locality.repository')->setAddressLocality($person->getAddress1Record()->getLocality());

		$person->setAddress2Record($this->get('address.repository')->setPersonAddress($person->getAddress2()));
		$person->getAddress2Record()->localityRecord = $this->get('locality.repository')->setAddressLocality($person->getAddress2Record()->getLocality());

		$person->getAddress2Record()->setClassSuffix('_alt');
		$person->getAddress2Record()->localityRecord->setClassSuffix('_alt');

		$setting = $this->get('setting.manager') ;
		$person->genderList = $setting->get('Gender.List');

        $form = $this->createForm('Busybee\PersonBundle\Form\PersonType', $person);

        $form->setData($person);

		if (! empty($request->request->get('person')))
		{
			$data = $request->request->get('person');
			$data['address1'] = intval($data['address1']['addressList']);
			$data['address2'] = intval($data['address2']['addressList']);
			if ($data['address1'] < 1)
				$data['address1'] = null;
			
			if ($data['address2'] < 1)
				$data['address2'] = null;

			if ($data['address2'] > 0 && $data['address1'] < 1)
			{
				$data['address1'] = $data['address2'];
				$data['address2'] = null ;
			}
			$request->request->set('person', $data);

			$form->handleRequest($request);
	
			if ($form->isSubmitted() && $form->isValid())
			{
				$em = $this->get('doctrine')->getManager();
				$em->persist($person);
				$em->flush();
				$id = $person->getId();
			}
			$request->request->set('person', null);
		}


		$view = $form->createView();
		
		$formattedAddress1 = $setting->get('Address.Format', null, array('line1' => $person->getAddress1Record()->getLine1(), 
			'line2' => $person->getAddress1Record()->getLine2(), 'locality' => $person->getAddress1Record()->localityRecord->getLocality(), 'territory' =>$person->getAddress1Record()->localityRecord->getTerritory(), 
			'postCode' => $person->getAddress1Record()->localityRecord->getPostCode(), 'country' => $person->getAddress1Record()->localityRecord->getCountryName()));

		$formattedAddress2 = $setting->get('Address.Format', null, array('line1' => $person->getAddress2Record()->getLine1(), 
			'line2' => $person->getAddress2Record()->getLine2(), 'locality' => $person->getAddress2Record()->localityRecord->getLocality(), 'territory' => $person->getAddress2Record()->localityRecord->getTerritory(), 
			'postCode' => $person->getAddress2Record()->localityRecord->getPostCode(), 'country' => $person->getAddress2Record()->localityRecord->getCountryName()));
		
        return $this->render('BusybeePersonBundle:Person:edit.html.twig',
			array('id' => $id, 'form' => $view, 'address1' => $formattedAddress1, 'address2' => $formattedAddress2)			
		);
    }


    public function saveAction($id, Request $request)
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, 'Unable to access this page!');

		$entity = $id > 0 ? $this->get('person.repository')->findOneBy(array('id' => $id)) : new Person();	

		$message = $this->get('translator')->trans('person.save.success', array(), 'BusybeePersonBundle');
		$status = 'success';

		return new JsonResponse(
			array(
				'message' => $message,
				'status' => $status,
				'id' => $id,
				'title' => $entity->getTitle(),
			),
			200
		);
	}
}
