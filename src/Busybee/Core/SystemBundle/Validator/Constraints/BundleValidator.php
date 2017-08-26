<?php

namespace Busybee\Core\SettingBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator as ConstraintValidatorBase;

class BundleValidator extends ConstraintValidatorBase
{

	public function validate($value, Constraint $constraint)
	{

		if (empty($value))
			return;

		dump($value);

		if (false)
		{
			$this->context->buildViolation($constraint->message)
				->setParameter('%numbers%', $num)
				->setParameter('%mixedCase%', $case)
				->setParameter('%specials%', $spec)
				->setParameter('%minLength%', $pw['minLength'])
				->addViolation();
		}
	}
}