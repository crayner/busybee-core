<?php

namespace Busybee\PersonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse ;
use Busybee\PersonBundle\Entity\Address ;


class AddressController extends Controller
{
    public function checkAction(Request $request)
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, 'Unable to access this page!');
		
		$address = array();
		$address['line1'] = $request->request->get('line1');
		$address['line2'] = $request->request->get('line2');
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
		$address->getLocality()->injectRepository($this->get('locality.repository'));

        $form = $this->createForm('Busybee\PersonBundle\Form\AddressType', $address);

        return $this->render('BusybeePersonBundle:Address:index.html.twig',
			array('id' => $id, 'form' => $form->createView())			
		);
    }

    public function fetchAction(Request $request)
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, 'Unable to access this page!');
		
		$id = $request->request->get('id');

		$address = $id > 0 ? $this->get('address.repository')->findOneBy(array('id' => $id)) : new Address();	

		return new JsonResponse(
			array(
				'locality' => $address->getLocality()->getId(),
				'line1' => $address->getLine1(),
				'line2' => $address->getLine2(),
			),
			200
		);
    }

    public function editAction(Request $request)
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, 'Unable to access this page!');
		
		$id = $request->request->get('id');

		$entity = $id > 0 ? $this->get('address.repository')->findOneBy(array('id' => $id)) : new Address();	
		$entity->repo = $this->get('address.repository') ;
		$valid = true;

		$locality_id = $request->request->get('locality_id');		
		if (empty($locality_id)) 
			$valid = false;
		else
			$entity->setLocality($this->get('locality.repository')->findOneBy(array('id' => $locality_id)));

		$line1 = $request->request->get('line1');		
		if (empty($line1)) 
			$valid = false;
		else
			$entity->setLine1($line1);

		$entity->setLine2($request->request->get('line2'));
		
		if ($valid)
		{
			$message = $this->get('translator')->trans('address.edit.success', array(), 'BusybeePersonBundle');
			$status = 'success';
			$em = $this->getDoctrine()->getManager();
            
            $em->persist($entity);
            $em->flush();
			$id = $entity->getId();
		} else {
			$message = $this->get('translator')->trans('address.edit.failure', array(), 'BusybeePersonBundle');
			$status = 'danger';
		}

		$list = $entity->repo->getAddressChoices();
		$addressOptions = '<option value="">'.$this->get('translator')->trans('address.placeholder.choice', array(), 'BusybeePersonBundle').'</option>';
		foreach($list as $name=>$value) {
			$addressOptions .= '<option value="'.$value.'">'.$name.'</option>';	
		}
	

		return new JsonResponse(
			array(
				'message' => $message,
				'status' => $status,
				'id' => $id,
				'locality' => $entity->getLocality()->getId(),
				'line1' => $entity->getLine1(),
				'line2' => $entity->getLine2(),
				'options' => $addressOptions,
			),
			200
		);
    }
}
