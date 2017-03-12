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
     * @version 12th March 2016
     * @since    12th March 2016
     * @return    array
     */
    public function loadSettingFile();

    /**
     * get Count
     *
     * @version    23rd October 2016
     * @since    23rd October 2016
     * @return    integer
     */
    public function getCount();
}