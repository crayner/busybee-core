<?php

namespace Busybee\Core\HomeBundle\Controller;

use Busybee\Core\TemplateBundle\Controller\BusybeeController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Response;
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

	/**
	 * @param string $file
	 * @param string $role
	 *
	 * @return Response
	 * @throws \Exception
	 */
	public function downloadFileAction($file, $role = 'ROLE_SYSTEM_ADMIN')
	{
		$this->denyAccessUnlessGranted($role);

		$content = '';

		if (!empty($file) && file_exists(base64_decode($file)))
		{
			$content = file_get_contents(base64_decode($file));
			$file    = new File(base64_decode($file));
			$headers = array(
				'Content-type'        => $file->getMimeType(),
				'Content-Disposition' => 'attachment; filename=' . basename($file->getPathname()),
				'Content-Length'      => $file->getSize(),
			);

			return new Response($content, 200, $headers);
		}

		throw new \Exception('The file is not available to download. ' . $file);
	}
}
