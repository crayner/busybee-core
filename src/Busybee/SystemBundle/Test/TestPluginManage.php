<?php 

namespace Busybee\SystemBundle\Test ;

use Symfony\Component\DependencyInjection\ContainerInterface as Container ;
use DirectoryIterator ;

/**
 * Test Interface
 *
 * @version	15th November 2016
 * @since	15th November 2016
 * @author	Craig Rayner
 */
class TestPluginManage implements TestInterface
{
	protected $container ;
	
	/**
	 * Test
	 *
	 * @version	15th November 2016
	 * @since	15th November 2016
	 * @param	Symfony Container
	 * @return	this
	 */
	public function test()
	{
		$setting = $this->container->get('setting.manager');
		foreach (new DirectoryIterator(__DIR__.'/../../Plugin') as $fileInfo) {
			if ($fileInfo->isDot()) continue;
			if ($fileInfo->isDir())
			{
				$bundle = str_replace('Bundle', '', $fileInfo->getFileName());
				$bundleSettings = $this->container->getParameter($bundle);
				if (version_compare($setting->get($bundle.'.Version'), $bundleSettings['Version'], '<'))
					return false ;
			}
		}
		return true;
	}

	/**
	 * Constructor
	 *
	 * @version	15th November 2016
	 * @since	15th November 2016
	 * @param	Symfony Container
	 * @return	this
	 */
	public function __construct(Container $container) 
	{
		$this->container = $container ;
		return $this ;
	}
}