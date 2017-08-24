<?php
// src/General/ValidationBundle/Validator/Constraints/Password.php
namespace Busybee\Core\SecurityBundle\Validator;

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