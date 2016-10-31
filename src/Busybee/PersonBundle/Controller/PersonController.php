<?php

namespace Busybee\PersonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Busybee\PersonBundle\Entity\Person ;
use Busybee\PersonBundle\Entity\Address ;
use Busybee\PersonBundle\Entity\Locality ;

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
		
		$person->getAddress1()->injectRepository($this->get('address.repository'));
		$person->getAddress1()->getLocality()->injectRepository($this->get('locality.repository'));
		$person->getAddress2()->injectRepository($this->get('address.repository'));
		$person->getAddress2()->getLocality()->injectRepository($this->get('locality.repository'));
		$person->getAddress2()->setClassSuffix('_alt');
		$person->getAddress2()->getLocality()->setClassSuffix('_alt');

        $form = $this->createForm('Busybee\PersonBundle\Form\PersonType', $person);

        $form->setData($person);

		$form->handleRequest($request);
		

		if ($form->isSubmitted() && $form->isValid())
		{
			$em = $this->get('doctrine')->getManager();

			$data = $request->request->get('person');
			if ($data['address1']['addressList'] > 0)
				$person->setAddress1($this->get('address.repository')->findOneBy(array('id' => $data['address1']['addressList'])));
			else {
				$person->getAddress1()->setLocality(new Locality());
				$person->setAddress1(new Address());
				$em->detach($person->getAddress1()->getLocality());
				$em->detach($person->getAddress1());
				$person->getAddress1()->setLocality(null);
				$person->getAddress1(null);
			}
			if ($data['address2']['addressList'] > 0)
				$person->setAddress1($this->get('address.repository')->findOneBy(array('id' => $data['address2']['addressList'])));
			else {
				$person->getAddress2()->setLocality(new Locality());
				$person->setAddress2(new Address());
				$em->detach($person->getAddress2()->getLocality());
				$em->detach($person->getAddress2());
				$person->getAddress2()->setLocality(null);
				$person->getAddress2(null);
			}
			
dump($em);			
dump($person);			
			$em->persist($person);
			$em->flush();
		}

		$setting = $this->get('setting.manager');
		
		$address = $person->getAddress1();
		$formattedAddress1 = $setting->get('Address.Format', null, array('line1' => $address->getLine1(), 
			'line2' => $address->getLine2(), 'locality' => $address->getLocality()->getLocality(), 'territory' => $address->getLocality()->getTerritory(), 
			'postCode' => $address->getLocality()->getPostCode(), 'country' => $address->getLocality()->getCountryName()));
		$address = $person->getAddress2();
		$formattedAddress2 = $setting->get('Address.Format', null, array('line1' => $address->getLine1(), 
			'line2' => $address->getLine2(), 'locality' => $address->getLocality()->getLocality(), 'territory' => $address->getLocality()->getTerritory(), 
			'postCode' => $address->getLocality()->getPostCode(), 'country' => $address->getLocality()->getCountryName()));

        return $this->render('BusybeePersonBundle:Person:edit.html.twig',
			array('id' => $id, 'form' => $form->createView(), 'address1' => $formattedAddress1, 'address2' => $formattedAddress2)			
		);
    }
}
