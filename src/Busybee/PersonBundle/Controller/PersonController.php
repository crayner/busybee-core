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


    public function editAction(Request $request, $id)
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, 'Unable to access this page!');

		$person = $this->getPerson($id);
		
		$setting = $this->get('setting.manager') ;
		
		$formDefinition = $this->get('my_service_container')->getParameter('person');
		
		unset($formDefinition['person'], $formDefinition['contact'], $formDefinition['address1'], $formDefinition['address2']);

		$em = $this->get('doctrine')->getManager();
		$editOptions = array();

        $form = $this->createForm(PersonType::class, $person);

		foreach($formDefinition as $extra)
		{
			if (isset($extra['form']) && isset($extra['name']))
			{
				$options = array();
				if (! empty($extra['options']) && is_array($extra['options']))
					$options = $extra['options'];
				$name = $extra['data']['name'];
				$options['data'] = $this->get($name)->findOneByPerson($person->getId());
				$options['data']->setPerson($person);
				$form->add($extra['name'], $extra['form'], $options);
				$name = $extra['name'];
				$person->$name = $options['data'];
				
			}
			if (isset($extra['script']))
				$editOptions['script'][] = $extra['script'];
		}


		foreach($formDefinition as $extra)
		{
			if (isset($extra['request']) )
			{
//					$data['client'] = $this->get($extra['request']['name'])->handleRequest($data['client'], $person);
			}
		}

		$form->setData($person);
		
		if (! empty($request->get('person')))
		{
			$data = $request->get('person');
			if (isset($data['address1']['addressList'])) unset($data['address1']['addressList']);
			if (isset($data['address2']['addressList'])) unset($data['address2']['addressList']);
			if (isset($data['address1']['AddressValue'])) unset($data['address1']['AddressValue']);
			if (isset($data['address2']['AddressValue'])) unset($data['address2']['AddressValue']);
			$request->request->set('person', $data);
		}

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$em->persist($person);
			$em->flush();
			$id = $person->getId();
		} 

		$editOptions['id'] = $id;
		$editOptions['form'] = $form->createView();
		$editOptions['fullForm'] = $form;
		$editOptions['address1'] = $this->formatAddress($person->getAddress1());
		$editOptions['address2'] = $this->formatAddress($person->getAddress2());
		$editOptions['addressLabel1'] = $this->get('address.manager')->getAddressListLabel($person->getAddress1());
		$editOptions['addressLabel2'] = $this->get('address.manager')->getAddressListLabel($person->getAddress2());

        return $this->render('BusybeePersonBundle:Person:edit.html.twig',
			$editOptions	
		);
    }


    private function getPerson($id)
    {
		$person = new Person();
		if ($id !== 'Add')
			$person = $this->get('person.repository')->findOneById($id);
		$person->cancelURL = $this->generateUrl('person_edit', array('id' => $id));

		return $person ;
	}


    private function formatAddress($address)
    {
		return $this->get('address.manager')->formatAddress($address);
	}
}
