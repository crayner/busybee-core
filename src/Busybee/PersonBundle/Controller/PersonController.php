<?php

namespace Busybee\PersonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Busybee\PersonBundle\Entity\Person ;
use Busybee\PersonBundle\Entity\Address ;
use Busybee\PersonBundle\Entity\Locality ;
use Busybee\PersonBundle\Entity\Image ;
use Symfony\Component\HttpFoundation\RedirectResponse ;
use Symfony\Component\HttpFoundation\JsonResponse ;
use Busybee\PersonBundle\Form\PersonType ;


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
		$person = $this->getPerson($id);
		
		$setting = $this->get('setting.manager') ;

		$em = $this->get('doctrine')->getManager();
		$em->detach($person->getAddress1Record()->localityRecord);
		$em->detach($person->getAddress2Record()->localityRecord);
		$em->detach($person->getAddress1Record());
		$em->detach($person->getAddress2Record());

        $form = $this->createForm(PersonType::class, $person);

		if (! empty($request->request->get('person')))
		{
			$data = $request->request->get('person');
			$data['address1'] = intval($data['add1']['addressList']);
			$data['address2'] = intval($data['add2']['addressList']);
			if ($data['address1'] < 1)
				$data['address1'] = null;
			
			if ($data['address2'] < 1)
				$data['address2'] = null;

			if ($data['address2'] > 0 && $data['address1'] < 1)
			{
				$data['address1'] = $data['address2'];
				$data['address2'] = null ;
			}
			if ($data['address2'] > 0 && $data['address1'] == $data['address2'])
			{
				$data['address2'] = null ;
			}
			$request->request->set('person', $data);
			$person->setAddress1($data['address1']);
			$person->setAddress2($data['address2']);
        	
			$form->setData($person);

			$form->handleRequest($request);
			
			if ($form->isSubmitted() && $form->isValid())
			{
			
				$em->persist($person);
				$em->flush();
				$id = $person->getId();
				return new RedirectResponse($this->generateUrl('person_edit', array('id' => $id)));

			} 
			$request->request->set('person', null);
		} else
       	 	$form->setData($person);

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


    private function getPerson($id)
    {
		$person = new Person();
		if ($id !== 'Add')
			$person = $this->get('person.repository')->findOneBy(array('id' => $id));
		
		$person->cancelURL = $this->generateUrl('busybee_security_user_list');

		$person->setAddress1Record($this->get('address.repository')->setPersonAddress($person->getAddress1()));
		$person->getAddress1Record()->localityRecord = $this->get('locality.repository')->setAddressLocality($person->getAddress1Record()->getLocality());

		$person->getAddress1Record()->setClassSuffix('address1');
		$person->getAddress1Record()->localityRecord->setClassSuffix('address1');

		$person->setAddress2Record($this->get('address.repository')->setPersonAddress($person->getAddress2()));
		$person->getAddress2Record()->localityRecord = $this->get('locality.repository')->setAddressLocality($person->getAddress2Record()->getLocality());

		$person->getAddress2Record()->setClassSuffix('address2');
		$person->getAddress2Record()->localityRecord->setClassSuffix('address2');

		$setting = $this->get('setting.manager') ;
		$person->setGenderList($setting->get('Gender.List'));
		$person->setTitleList($setting->get('Person.Titles'));
		
		return $person ;
	}
}
