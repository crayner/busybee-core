<?php

namespace Busybee\Core\HomeBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator as ConstraintValidatorBase;
use Symfony\Component\Yaml\Yaml as YamlParser;

class YamlValidator extends ConstraintValidatorBase
{
	public function validate($value, Constraint $constraint)
	{

		if (empty($value))
			return;

		$message = '';

		try
		{
			YamlParser::parse($value);
		}
		catch (\Exception $e)
		{
			$message = $e->getMessage();
		}

		if (!empty($message))
		{
			$this->context->buildViolation($constraint->message)
				->setParameter('%systemMessage%', $message)
				->addViolation();
		}
	}
}