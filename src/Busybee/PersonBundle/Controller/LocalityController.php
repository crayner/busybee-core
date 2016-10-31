<?php

namespace Busybee\PersonBundle\Controller ;

use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use Symfony\Component\HttpFoundation\Request ;
use Symfony\Component\HttpFoundation\JsonResponse ;
use Busybee\PersonBundle\Entity\Locality ;

class LocalityController extends Controller
{
    public function indexAction($id, Request $request)
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, 'Unable to access this page!');
		
		$locality = new Locality();
		$lr = $this->get('locality.repository');
		if ($id !== 'Add')
			$locality = $lr->findOneBy(array('id' => $id));
		$locality->injectRepository($lr);
        $form = $this->createForm('Busybee\PersonBundle\Form\LocalityType', $locality);

        return $this->render('BusybeePersonBundle:Locality:index.html.twig',
			array('id' => $id, 'form' => $form->createView())			
		);
    }

    public function editAction(Request $request)
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, 'Unable to access this page!');
		
		$id = $request->request->get('id');

		$entity = $id > 0 ? $this->get('locality.repository')->findOneBy(array('id' => $id)) : new Locality();	
		$entity->injectRepository($this->get('locality.repository')) ;
		$valid = true;

		$locality = $request->request->get('locality');		
		if (empty($locality)) 
			$valid = false;
		else
			$entity->setLocality($locality);

		$entity->setTerritory($request->request->get('territory'));
		$entity->setPostCode($request->request->get('postCode'));
		$entity->setCountry($request->request->get('country'));
		
		if ($valid)
		{
			$message = $this->get('translator')->trans('locality.edit.success', array(), 'BusybeePersonBundle');
			$status = 'success';
			$em = $this->getDoctrine()->getManager();
            
            $em->persist($entity);
            $em->flush();
			$id = $entity->getId();
		} else {
			$message = $this->get('translator')->trans('locality.edit.failure', array(), 'BusybeePersonBundle');
			$status = 'danger';
		}

		$list = $entity->getRepository()->getLocalityChoices();
		$localityOptions = '<option value="">'.$this->get('translator')->trans('locality.placeholder.choice', array(), 'BusybeePersonBundle').'</option>';
		foreach($list as $name=>$value) {
			$localityOptions .= '<option value="'.$value.'">'.$name.'</option>';	
		}
	

		return new JsonResponse(
			array(
				'message' => $message,
				'status' => $status,
				'id' => $id,
				'locality' => $entity->getLocality(),
				'territory' => $entity->getTerritory(),
				'country' => $entity->getCountry(),
				'postCode' => $entity->getPostCode(),
				'options' => $localityOptions,
			),
			200
		);
    }

    public function fetchAction(Request $request)
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, 'Unable to access this page!');
		
		$id = $request->request->get('id');

		$locality = $id > 0 ? $this->get('locality.repository')->findOneBy(array('id' => $id)) : new Locality();	

		return new JsonResponse(
			array(
				'locality' => $locality->getLocality(),
				'territory' => $locality->getTerritory(),
				'country' => $locality->getCountry(),
				'postCode' => $locality->getPostCode(),
			),
			200
		);
    }
}
