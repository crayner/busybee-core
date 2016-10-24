<?php 
namespace Busybee\SystemBundle\Update ;

/**
 * Update Interface
 *
 * @version	23rd October 2016
 * @since	23rd October 2016
 * @author	Craig Rayner
 */
interface UpdateInterface
{
	/**
	 * Build
	 *
	 * @version	23rd October 2016
	 * @since	23rd October 2016
	 * @param	Symfony Container
	 * @return	this
	 */
	public function build() ;
}