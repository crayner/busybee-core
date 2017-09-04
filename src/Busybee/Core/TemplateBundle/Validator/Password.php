<?php

namespace Busybee\Core\TemplateBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Password extends Constraint
{
	public $message = 'user.password.message';

	public function validatedBy()
	{
		return 'password.validator';
	}

}