<?php

namespace Busybee\Core\SettingBundle\Validator;

use Symfony\Component\Validator\Constraint;

class Bundle extends Constraint
{
	public $message = 'system.bundle.error.message';

	public function validatedBy()
	{
		return 'bundle.validator';
	}

}