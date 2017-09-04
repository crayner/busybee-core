<?php

namespace Busybee\Core\FormBundle\Source;

interface SettingManagerInterface
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
	public function get($name, $default = null, $options = []);

}