<?php

namespace Busybee\Core\CalendarBundle\Validator;

use Symfony\Component\Validator\Constraint;

class CalendarDate extends Constraint
{
	public $message = 'calendar.error.date';

	public $fields;

	public function __construct($options)
	{
		$this->fields = $options;
	}

	public function validatedBy()
	{
		return 'calendar_date_validator';
	}
}
