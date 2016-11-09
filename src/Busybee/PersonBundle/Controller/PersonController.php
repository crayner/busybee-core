<?php

namespace Busybee\PersonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Busybee\PersonBundle\Entity\Person ;
use Busybee\PersonBundle\Entity\Address ;
use Busybee\PersonBundle\Entity\Locality ;
use Busybee\PersonBundle\Entity\Image ;
use Busybee\PersonBundle\Entity\Phone ;
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
			$data['address1'] = intval($data['address1']['AddressValue']);
			$data['address2'] = intval($data['address2']['AddressValue']);
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
			
				foreach($person->getPhone() as $q=>$w)
				{
					if (empty($w->getPhoneNumber()))
						$person->removePhone($w);   
					else {
						$phone = $this->get('phone.repository')->findOneBy(array('phoneNumber' => $w->getPhoneNumber()));
						if ($phone instanceof Phone && $phone->getId() !== $w->getId())
						{
							$person->getPhone()->remove($q);
							$person->getPhone()->set($q, $phone);
						}
					}
				}


				$em->persist($person);
				$em->flush();
				$id = $person->getId();
				return new RedirectResponse($this->generateUrl('person_edit', array('id' => $id)));

			} 
			$request->request->set('person', null);
		} else
       	 	$form->setData($person);

		$view = $form->createView();
		
		$formattedAddress1 = $this->formatAddress($person->getAddress1Record());
		$formattedAddress2 = $this->formatAddress($person->getAddress2Record());

        return $this->render('BusybeePersonBundle:Person:edit.html.twig',
			array(
				'id' => $id, 
				'form' => $view, 
				'address1' => $formattedAddress1, 
				'address2' => $formattedAddress2, 
				'addressLabel1' => $this->get('address.manager')->getAddressListLabel($person->getAddress1Record()),		
				'addressLabel2' => $this->get('address.manager')->getAddressListLabel($person->getAddress2Record()),
			)		
		);
    }


    private function getPerson($id)
    {
		$person = new Person();
		if ($id !== 'Add')
			$person = $this->get('person.repository')->findOneBy(array('id' => $id));
		$person->cancelURL = $this->generateUrl('person_edit', array('id' => $id));

		$sm = $this->get('setting.manager');

		$person->setAddress1Record($this->get('address.repository')->setPersonAddress($person->getAddress1()));
		$person->getAddress1Record()->localityRecord = $this->get('locality.repository')->setAddressLocality($person->getAddress1Record()->getLocality());

		$person->getAddress1Record()->setClassSuffix('address1');
		$person->getAddress1Record()->localityRecord->setClassSuffix('address1');

		$person->setAddress2Record($this->get('address.repository')->setPersonAddress($person->getAddress2()));
		$person->getAddress2Record()->localityRecord = $this->get('locality.repository')->setAddressLocality($person->getAddress2Record()->getLocality());

		$person->getAddress2Record()->setClassSuffix('address2');
		$person->getAddress2Record()->localityRecord->setClassSuffix('address2');
		return $person ;
	}


    private function formatAddress($address)
    {
		return $this->get('address.manager')->formatAddress($address);
	}
}
