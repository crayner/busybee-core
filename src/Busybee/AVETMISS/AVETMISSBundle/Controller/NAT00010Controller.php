<?php

namespace Busybee\AVETMISS\AVETMISSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Busybee\Core\SystemBundle\Form\SettingListType;

class NAT00010Controller extends Controller
{
	use \Busybee\Core\SecurityBundle\Security\DenyAccessUnlessGranted;

	public function indexAction(Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);

		$settings = $this->get('service_container')->getParameter('AVETMISS Report.nat00010');

		$sm = $this->get('setting.manager');

		$form = $this->createForm(SettingListType::class, array());

		$form = $sm->buildForm($form, $settings);

		//Transformers
		$values = $request->request->get('setting_list');
		if (!empty($values))
		{
			$values['Org_Contact_Phone']     = preg_replace('/\D/', '', $values['Org_Contact_Phone']);
			$values['Org_Contact_Facsimile'] = preg_replace('/\D/', '', $values['Org_Contact_Facsimile']);
			$request->request->set('setting_list', $values);
		}

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$values = $request->request->get('setting_list');
			foreach ($values as $name => $value)
				$sm->set(str_replace('_', '.', $name), $value);

		}

		return $this->render('BusybeeAVETMISSBundle:NAT00010:index.html.twig', array('form' => $form->createView()));

	}
}