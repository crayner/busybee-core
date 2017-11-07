<?php

namespace Busybee\Core\SystemBundle\Validator;

use Symfony\Component\Validator\Constraint;

class Houses extends Constraint
{
	public function validatedBy()
	{
		return 'houses_validator';
	}

}