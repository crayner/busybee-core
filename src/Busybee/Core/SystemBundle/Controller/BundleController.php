<?php

namespace Busybee\Core\SystemBundle\Controller;

use Busybee\Core\SystemBundle\Form\BundlesType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

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

		if ($name === 'All')
			$um->updateAllBundles();
		else
			$um->updateBundle($name);

		$this->get('busybee_core_system.model.flash_bag_manager')->addMessages($um->getMessages());

		return new RedirectResponse($this->generateUrl('bundle_list'));
	}

	/**
	 * Update Database Scheme
	 *
	 * @param $name
	 *
	 * @return RedirectResponse
	 */
	public function databaseAction()
	{
		$this->denyAccessUnlessGranted('ROLE_SYSTEM_ADMIN');

		$um = $this->get('busybee_core_system.model.bundle_manager');

		$um->buildDatabase();

		$this->get('busybee_core_system.model.flash_bag_manager')->addMessages($um->getMessages());

		return new RedirectResponse($this->generateUrl('bundle_list'));
	}

	/**
	 * @return RedirectResponse
	 */
	public function removeFileAction()
	{
		$sm = $this->get('setting.manager');
		if ($sm->has('settings.default.overwrite') && !empty($sm->get('settings.default.overwrite', '')))
		{
			unlink($this->get('setting.manager')->get('settings.default.overwrite'));
			$sm->set('settings.default.overwrite', '');
		}

		return new RedirectResponse($this->generateUrl('bundle_list'));
	}
}