<?php

namespace Busybee\Core\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends Controller
{
	use \Busybee\Core\SecurityBundle\Security\DenyAccessUnlessGranted;

	/**
	 * Load fixtures for all bundles
	 *
	 * @param Kernel $kernel
	 */
	private static function loadFixtures(Kernel $kernel)
	{
		$loader = new DataFixturesLoader($kernel->getContainer());

		$em = $kernel->getContainer()->get('doctrine')->getManager();

		foreach ($kernel->getBundles() as $bundle)
		{
			$path = $bundle->getPath() . '/DataFixtures/ORM';

			if (is_dir($path))
			{
				$loader->loadFromDirectory($path);
			}
		}

		$fixtures = $loader->getFixtures();
		if (!$fixtures)
		{
			throw new InvalidArgumentException('Could not find any fixtures to load in');
		}
		$purger   = new ORMPurger($em);
		$executor = new ORMExecutor($em, $purger);
		$executor->execute($fixtures, true);
	}

	public function indexAction(Request $request)
	{
		$setting = $this->get('busybee_core_system.setting.setting_manager');


		return $this->render('BusybeeHomeBundle::home.html.twig');
	}

	public function acknowledgementAction()
	{
		$versions = $this->get('busybee_core_template.model.version_manager')->getVersion();

		require_once $this->get('kernel')->getProjectDir() . '/var/SymfonyRequirements.php';

		$SymfonyRequirements = new \SymfonyRequirements();


		return $this->render('@BusybeeTemplate/Acknowledgement/acknowledgement.html.twig',
			[
				'versions'      => $versions,
				'majorProblems' => $SymfonyRequirements->getFailedRequirements(),
				'minorProblems' => $SymfonyRequirements->getFailedRecommendations(),
			]
		);
	}
}
