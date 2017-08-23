<?php

namespace Busybee\Core\SystemBundle\Model\Test;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use DirectoryIterator;

/**
 * Test Update Availabel
 *
 * @version    29th November 2016
 * @since      29th November 2016
 * @author     Craig Rayner
 */
class TestUpdateAvailable extends Tester
{
	/**
	 * Test
	 *
	 * @version    29th November 2016
	 * @since      29th November 2016
	 * @return    boolean
	 */
	public function test()
	{
		$setting = $this->container->get('setting.manager');
		if (version_compare($setting->get('Version.System'), $this->container->getParameter('version.system'), '<'))
			return true;
		if (version_compare($setting->get('Version.Database'), $this->container->getParameter('version.database'), '<'))
			return true;

//  Add remote test here ....
		return false;
	}
}