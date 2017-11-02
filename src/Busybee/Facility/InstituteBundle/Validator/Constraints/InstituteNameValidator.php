<?php

namespace Busybee\Facility\InstituteBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator as ConstraintValidatorBase;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class InstituteNameValidator extends ConstraintValidatorBase
{
	public function validate($value, Constraint $constraint)
	{
		if (empty($value))
			return;

		if ($value === 'Busybee Institute')
		{
			$this->context->buildViolation($constraint->message)
				->addViolation();
		}
	}
}