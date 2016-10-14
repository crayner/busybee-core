<?php

namespace Busybee\DatabaseBundle\Validator\Constraints ;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NameField extends Constraint
{
    public $message = 'field.error.name.unique';

	public function validatedBy()
	{
		return 'name_field_validator';
	}

}