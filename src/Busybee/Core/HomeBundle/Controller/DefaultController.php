<?php

namespace Busybee\Core\HomeBundle\Controller;

use Busybee\Core\TemplateBundle\Controller\BusybeeController;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends BusybeeController
{
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

	/**
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function indexAction()
	{
		return $this->render('BusybeeHomeBundle::home.html.twig');
	}

	/**
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
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
