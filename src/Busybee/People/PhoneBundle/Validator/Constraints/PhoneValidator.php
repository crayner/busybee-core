<?php

namespace Busybee\People\PhoneBundle\Validator\Constraints;

use Busybee\Core\SystemBundle\Setting\SettingManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PhoneValidator extends ConstraintValidator
{
	private $sm;

	public function __construct(SettingManager $sm)
	{
		$this->sm = $sm;
	}

	public function validate($value, Constraint $constraint)
	{

		if (empty($value))
			return;

		$pattern = $this->sm->get('phone.validation');

		if (preg_match($pattern, $value) !== 1)
		{
			$this->context->buildViolation($constraint->message)
				->addViolation();
		}
	}
}