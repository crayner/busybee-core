<?php

namespace Busybee\Core\SystemBundle\Validator;

use Symfony\Component\Validator\Constraint;

class Times extends Constraint
{
	public function validatedBy()
	{
		return 'times_validator';
	}

}