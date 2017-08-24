<?php

namespace Busybee\AVETMISS\AVETMISSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Tools\SchemaTool;

class InstallController extends Controller
{
	use \Busybee\SecurityBundle\Security\DenyAccessUnlessGranted;

	public function indexAction()
	{
		$this->denyAccessUnlessGranted('ROLE_ADMIN', null, null);

		return $this->render('BusybeeAVETMISSBundle:Install:index.html.twig');
	}

	public function startAction()
	{
		$this->denyAccessUnlessGranted('ROLE_ADMIN', null, null);

		$em = $this->container->get('doctrine')->getManager();

		$schemaTool = new SchemaTool($em);
		$metaData   = $em->getMetadataFactory()->getAllMetadata();

		$xx = $schemaTool->updateSchema($metaData, true);

		$avetmiss = $this->getParameter('AVETMISS');
		$set      = $this->get('setting.manager');
		$version  = $set->get('AVETMISS.Version', '0.0.00');
		$buildto  = $avetmiss['Version'];

		while (version_compare($version, $buildto, '<'))
		{
			if (file_exists(__DIR__ . '/../Update/dBase/Update_' . str_replace('.', '_', $version) . '.php'))
			{
				$um = $this->get('avetmiss.update.' . str_replace('.', '_', $version));
				$um->build();
			}
			$version = $set->incrementVersion($version);
		}

		$set->set('AVETMISS.Version', $buildto);
		$plugins = $set->get('Plugins.Installed', array());
		if (!in_array('AVETMISS', $plugins))
		{
			$plugins[] = 'AVETMISS';
			$set->set('Plugins.Installed', $plugins);
		}

		return $this->render('BusybeeAVETMISSBundle:Install:index.html.twig');
	}
}
