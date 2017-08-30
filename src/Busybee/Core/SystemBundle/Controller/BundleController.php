<?php

namespace Busybee\Core\SystemBundle\Controller;

use Busybee\Core\SystemBundle\Form\BundlesType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;

class BundleController extends Controller
{
	use \Busybee\Core\SecurityBundle\Security\DenyAccessUnlessGranted;

	/**
	 * List Bundles
	 *
	 * @param Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function listAction(Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_SYSTEM_ADMIN');

		$bundles = $this->get('busybee_core_system.model.bundle_manager');

		$form = $this->createForm(BundlesType::class, $bundles);

		$bundles->handleRequest($form, $request);

		$this->get('busybee_core_system.model.flash_bag_manager')->addMessages($bundles->getMessages());

		return $this->render('@System/Bundle/list.html.twig', ['form' => $form->createView()]);
	}

	/**
	 * Update Bundle
	 *
	 * @param $name
	 *
	 * @return RedirectResponse
	 */
	public function updateAction($name)
	{
		$this->denyAccessUnlessGranted('ROLE_SYSTEM_ADMIN');

		$um = $this->get('busybee_core_system.model.bundle_manager');

		$um->updateBundle($name);

		$this->get('busybee_core_system.model.flash_bag_manager')->addMessages($um->getMessages());

		return new RedirectResponse($this->generateUrl('bundle_list'));
	}
}