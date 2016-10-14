<?php

namespace Busybee\DatabaseBundle\Validator\Constraints ;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NameTable extends Constraint
{
    public $message = 'table.error.name.unique';

	public function validatedBy()
	{
		return 'name_table_validator';
	}

}