<?php

namespace Busybee\Core\SystemBundle\Model\Test;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use DirectoryIterator;

/**
 * Test Plugin Managed
 *
 * @version    15th November 2016
 * @since      15th November 2016
 * @author     Craig Rayner
 */
class TestPluginManage extends Tester
{
	/**
	 * Test
	 *
	 * @version    15th November 2016
	 * @since      15th November 2016
	 *
	 * @param    Symfony Container
	 *
	 * @return    boolean
	 */
	public function test()
	{
		$setting = $this->container->get('busybee_core_system.setting.setting_manager');
		foreach (new DirectoryIterator(__DIR__ . '/../../../Plugin') as $fileInfo)
		{
			if ($fileInfo->isDot()) continue;
			if ($fileInfo->isDir())
			{
				$bundle         = str_replace('Bundle', '', $fileInfo->getFileName());
				$bundleSettings = $this->container->getParameter($bundle, false);
				if (version_compare($setting->get($bundle . '.Version'), $bundleSettings['Version'], '<'))
					return false;
			}
		}

		return true;
	}
}