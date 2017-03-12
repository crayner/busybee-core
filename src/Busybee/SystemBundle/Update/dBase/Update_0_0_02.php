<?php

namespace Busybee\SystemBundle\Update\dBase ;

use Busybee\SystemBundle\Update\UpdateInterface ;
use Symfony\Component\Debug\Exception\ContextErrorException;
use Symfony\Component\Yaml\Yaml;

/**
 * Update 0.0.02
 *
 * @version	7th February 2017
 * @since	23rd October 2016
 * @author	Craig Rayner
 */
class Update_0_0_02 implements UpdateInterface
{
    /**
     * get Count
     *
     * @version    23rd October 2016
     * @since    23rd October 2016
     * @return    integer
     */
    public function getCount()
    {
        return count($this->loadSettingFile());
    }

    /**
     * load Setting File
     *
     * @version    12th March 2017
     * @since    12th March 2017
     * @return    array
     * @throws ContextErrorException
     */
    public function loadSettingFile()
    {
        try {
            $data = Yaml::parse(file_get_contents('../src/Busybee/SystemBundle/Resources/config/updates/Setting_0_00_02.yml'));
        } catch (ContextErrorException $e) {
            return array();
        }
        return $data;
    }
}