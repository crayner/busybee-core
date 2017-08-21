<?php

namespace Busybee\Core\CalendarBundle\Validator;

use Symfony\Component\Validator\Constraint;

class SpecialDayDate extends Constraint
{
	public $message = 'specialday.error.date';

	public $year;

	public function __construct($year)
	{
		$this->year = $year;
	}

	public function validatedBy()
	{
		return 'specialday_date_validator';
	}
}
