<?php

namespace Busybee\People\LocalityBundle\Validator;

use Symfony\Component\Validator\Constraint;

class Locality extends Constraint
{
	public $message = 'locality.error';

	public function validatedBy()
	{
		return 'locality_validator';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTargets()
	{
		return self::CLASS_CONSTRAINT;
	}
}
