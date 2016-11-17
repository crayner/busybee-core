<?php 

namespace Busybee\SystemBundle\Model\Test ;

use Symfony\Component\DependencyInjection\ContainerInterface as Container ;
use DirectoryIterator ;

/**
 * Test Interface
 *
 * @version	15th November 2016
 * @since	15th November 2016
 * @author	Craig Rayner
 */
class TestPluginInstalled extends Tester
{
	/**
	 * Test
	 *
	 * @version	15th November 2016
	 * @since	15th November 2016
	 * @return	boolean
	 */
	public function test()
	{
		return $this->menuItems(14);
	}
}