<?php 

namespace Busybee\SystemBundle\Test ;

use Symfony\Component\DependencyInjection\ContainerInterface as Container ;

/**
 * Test Interface
 *
 * @version	15th November 2016
 * @since	15th November 2016
 * @author	Craig Rayner
 */
interface TestInterface
{
	/**
	 * Test
	 *
	 * @version	15th November 2016
	 * @since	15th November 2016
	 * @param	Symfony Container
	 * @return	this
	 */
	public function test() ;

	/**
	 * Constructor
	 *
	 * @version	15th November 2016
	 * @since	15th November 2016
	 * @param	Symfony Container
	 * @return	this
	 */
	public function __construct(Container $container) ;
}