<?php

namespace Busybee\Core\FormBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

class NoWhiteSpace extends Constraint
{
	/**
	 * @var string
	 */
	public $message = 'nowhitespace.error';

	/**
	 * @var bool
	 */
	public $repair = true;

	/**
	 * @return string
	 */
	public function validatedBy()
	{
		return 'nowhitespace_validator';
	}
}
