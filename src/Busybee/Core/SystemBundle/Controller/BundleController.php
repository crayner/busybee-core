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

		$bundles->handleRequest($form, $request);

		$this->get('busybee_core_system.model.flash_bag_manager')->addMessages($bundles->getMessages());

		return $this->render('@System/Bundle/list.html.twig', ['form' => $form->createView()]);
	}
}