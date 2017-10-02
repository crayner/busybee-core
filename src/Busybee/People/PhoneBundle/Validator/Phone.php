<?php

namespace Busybee\People\PhoneBundle\Validator;

use Symfony\Component\Validator\Constraint;

class Phone extends Constraint
{
	public $message = 'person.error.phone';

	public function validatedBy()
	{
		return 'phone_validator';
	}
}
