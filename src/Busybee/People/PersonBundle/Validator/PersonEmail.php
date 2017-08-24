<?php

namespace Busybee\People\PersonBundle\Validator;

use Symfony\Component\Validator\Constraint;

class PersonEmail extends Constraint
{
	public $message = 'person.validator.email.unique';

	public $errorPath;

	/**
	 * @return string
	 */
	public function validatedBy()
	{
		return 'person_email_validator';
	}
}
