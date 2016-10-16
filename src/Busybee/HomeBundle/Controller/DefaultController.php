<?php

namespace Busybee\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use Symfony\Component\Yaml\Dumper ;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse ;

class DefaultController extends Controller
{
    public function indexAction( Request $request )
    {
//		if (true !== ($response = $this->get('busybee_security.authorisation.checker')->redirectAuthorisation('IS_AUTHENTICATED_FULLY'))) return $response;

		$name = 'BusybeeHomeBundle:Default:index.html.twig';
		$config = new \stdClass();
		$config->signin = $this->get('security.failure.repository')->testRemoteAddress($request->server->get('REMOTE_ADDR'));

        $em = $this->get('doctrine')->getManager();
		$x = $em->getConnection()->getParams();
		if ($x['driver'] == 'pdo_sqlite')
			return new RedirectResponse($this->generateUrl('install_start'));

		return $this->render('BusybeeHomeBundle:Default:index.html.twig', array('name' => $name, 'config' => $config));
    }

	/**
     * Load fixtures for all bundles
     *
     * @param Kernel $kernel
     */
    private static function loadFixtures(Kernel $kernel)
    {
        $loader = new DataFixturesLoader($kernel->getContainer());
        $em = $kernel->getContainer()->get('doctrine')->getManager();

        foreach ($kernel->getBundles() as $bundle) {
            $path = $bundle->getPath().'/DataFixtures/ORM';

            if (is_dir($path)) {
                $loader->loadFromDirectory($path);
            }
        }

        $fixtures = $loader->getFixtures();
        if (!$fixtures) {
            throw new InvalidArgumentException('Could not find any fixtures to load in');
        }
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->execute($fixtures, true);
    }
}
