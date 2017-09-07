<?php

namespace Busybee\Core\SystemBundle\Plugin;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use DirectoryIterator;
use stdClass;
use Symfony\Component\Yaml\Yaml;


/**
 * Plugin Manager
 *
 * @version    29th November 2016
 * @since      29th November 2016
 * @author     Craig Rayner
 */
class PluginManager
{
	/**
	 * @var    Container
	 */
	protected $container;

	/**
	 * @var    Setting Manager
	 */
	protected $sm;

	/**
	 * @var    array
	 */
	protected $details;

	/**
	 * Constructor
	 *
	 * @version    29th November 2016
	 * @since      29th November 2016
	 *
	 * @param    Symfony Container
	 *
	 * @return    PluginManager
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
		$this->sm        = $this->container->get('busybee_core_system.setting.setting_manager');
		$this->sm->setCurrentUser($this->container->get('security.token_storage')->getToken()->getUser());

		return $this;
	}

	/**
	 * Manage Plugins
	 *
	 * @version    29th November 2016
	 * @since      29th November 2016
	 * @return    PluginManager
	 */
	public function managePlugins()
	{
		$this->details = array();

		$path = realpath(__DIR__ . '/Plugin');
		$x    = 0;
		while (!is_dir($path))
		{
			if ($x > 4)
				return $this;
			$path = __DIR__;
			$y    = 0;
			$x++;
			while ($y < $x)
			{
				$path .= '/..';
				$y++;
			}
			$path = realpath($path . '/Plugin');
		}

		foreach (new DirectoryIterator($path) as $fileInfo)
		{
			if ($fileInfo->isDot()) continue;
			if ($fileInfo->isDir())
			{
				$bundle                            = str_replace('Bundle', '', $fileInfo->getFileName());
				$this->details[$bundle]            = new stdClass();
				$this->details[$bundle]->name      = $bundle;
				$bundleSettings                    = $this->container->getParameter($bundle, false);
				$this->details[$bundle]->version   = $bundleSettings['Version'];
				$this->details[$bundle]->installed = $this->sm->get($bundle . '.Version');

				$bundleSettings                    = Yaml::parse(file_get_contents($fileInfo->getPathName() . '/Resources/config/services.yml'));
				$this->details[$bundle]->namespace = $bundleSettings['parameters'][$bundle]['Namespace'];
				$this->details[$bundle]->route     = $bundleSettings['parameters'][$bundle]['Route'];
			}
		}

		return $this;
	}

	/**
	 * get Details
	 *
	 * @version    29th November 2016
	 * @since      29th November 2016
	 * @return    array
	 */
	public function getDetails()
	{
		return $this->details;
	}
}