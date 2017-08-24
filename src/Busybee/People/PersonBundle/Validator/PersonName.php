<?php

namespace Busybee\People\PersonBundle\Validator;

use Symfony\Component\Validator\Constraint;

class PersonName extends Constraint
{
	public $message = 'person.validator.preferredName.error';

	public $errorPath = 'preferredName';

	/**
	 * @return string
	 */
	public function validatedBy()
	{
		return 'person_name_validator';
	}

	/**
	 * @return array
	 */
	public function getTargets()
	{
		return array(self::PROPERTY_CONSTRAINT, self::CLASS_CONSTRAINT);
	}
}
