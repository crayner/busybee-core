<?php

namespace Busybee\Core\TemplateBundle\Validator;

use Busybee\People\PersonBundle\Entity\Person;
use Symfony\Component\Validator\Constraint;

class UniqueOrBlank extends Constraint
{
	public $message = 'unique.blank.invalid';

	public $data_class = Person::class;

	public $field = 'importIdentifier';

	public function validatedBy()
	{
		return 'unique_or_blank_validator';
	}
}
