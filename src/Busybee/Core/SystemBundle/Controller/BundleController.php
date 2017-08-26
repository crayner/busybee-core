<?php

namespace Busybee\Core\SystemBundle\Controller;

use Busybee\Core\SystemBundle\Form\BundlesType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;

class BundleController extends Controller
{
	use \Busybee\Core\SecurityBundle\Security\DenyAccessUnlessGranted;

	public function listAction(Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_SYSTEM_ADMIN');

		$bundles = $this->get('busybee_core_system.model.bundle_manager');

		$form = $this->createForm(BundlesType::class, $bundles);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$bundles->saveBundles($form->get('bundles')->getData());
		}

		return $this->render('@System/Bundle/list.html.twig', ['form' => $form->createView()]);
	}
}