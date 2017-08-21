<?php

namespace Busybee\Core\CalendarBundle\Validator;

use Symfony\Component\Validator\Constraint;

class TermDate extends Constraint
{
	public $message = 'year.term.error.date';

	public $year;

	public function __construct($year)
	{
		$this->year = $year;
	}

	public function validatedBy()
	{
		return 'term_date_validator';
	}
}
