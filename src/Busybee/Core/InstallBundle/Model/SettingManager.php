<?php

namespace Busybee\Core\InstallBundle\Model;

use Busybee\Core\FormBundle\Source\SettingManagerInterface;

class SettingManager implements SettingManagerInterface
{
	/**
	 * get Setting
	 *
	 * @version    31st October 2016
	 * @since      20th October 2016
	 *
	 * @param    string $name
	 * @param    mixed  $default
	 * @param    array  $options
	 *
	 * @return    mixed    Value
	 */
	public function get($name, $default = null, $options = [])
	{
		switch (strtolower($name))
		{
			case 'org.name.long':
				return 'Busybee Institute';
				break;
			case 'background.image':
				return 'img/backgroundPage.jpg';
				break;
			case 'org.logo':
				return 'img/bee.png';
				break;
			case 'version.system':
				return '0.0.00';
				break;
			case 'version.database':
				return '0.0.00';
				break;
			default:
				throw new \Exception('Install Settings not correctly returned for setting: ' . strtolower($name));
		}
	}

}