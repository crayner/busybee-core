<?php

namespace Busybee\Core\FormBundle\Validator;

use Symfony\Component\Validator\Constraints\Choice;

class SettingChoice extends Choice
{
	public $name;
	public $strict = true;
	public $choices = array();  // Add additional choices not found in the setting.
	public $message = 'setting.choice.invalid';

	public function validatedBy()
	{
		return 'setting_validator';
	}
}
