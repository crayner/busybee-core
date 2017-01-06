<?php

namespace Busybee\PersonBundle\Controller;

use Busybee\PersonBundle\Entity\Staff;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Busybee\PersonBundle\Entity\Person ;
use Symfony\Component\HttpFoundation\RedirectResponse ;
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


    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id)
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, 'Unable to access this page!');

		$person = $this->getPerson($id);
		
		$em = $this->get('doctrine')->getManager();

		$formDefinition = $this->get('service_container')->getParameter('person');
		
		unset($formDefinition['person'], $formDefinition['contact'], $formDefinition['address1'], $formDefinition['address2']);

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

		$form->handleRequest($request);

        $validator = $this->get('validator');

		if ($form->isSubmitted() && $form->isValid())
		{
		    $ok = true ;
            $personData = $request->request->get('person');

dump($person);

            foreach($formDefinition as $defined)
			{
                $req = isset($defined['request']['post']) ? $defined['request']['post'] : null ;
                if (! is_null($req) && isset($person->$req))
				{
                    $entity = $person->$req;
                    $errors = $validator->validate($entity);
                    if (count($errors) > 0)
                    {
                        foreach($errors as $w){
                            $subForm = $form->get($req);
                            $field = $w->getConstraint()->errorPath;
                            if (null !== $subForm->get($field))
                                $subForm->get($field)->addError(new FormError($w->getMessage(), $w->getParameters()));
                        }
                        $ok = false ;
                    }
					if ($ok) {
                        $em->persist($person->$req);
                    }
				}
			}
			if ($person->getStaff() instanceof Staff)
			    $person->getStaff()->setPerson($person);

			if ($ok) {
                $em->persist($person);
                $em->flush();
                $id = $person->getId();

                return new RedirectResponse($this->generateUrl('person_edit', array('id' => $id)));
            }
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
