<?php

namespace Busybee\Core\TemplateBundle\Validator;

use Symfony\Component\Validator\Constraint;

class UniqueOrBlank extends Constraint
{
	public $message = 'unique.blank.invalid';

	public function validatedBy()
	{
		return 'unique_or_blank_validator';
	}
}
