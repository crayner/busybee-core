<?php

namespace Busybee\Core\CalendarBundle\Validator;

use Symfony\Component\Validator\Constraint;

class CalendarGroup extends Constraint
{
	public $message = 'calendar.group.error.duplicate';

	public $year;

	public $errorPath = 'calendarGroups';

	public function __construct($year)
	{
		$this->year = $year;
	}

	public function validatedBy()
	{
		return 'calendar_group_validator';
	}
}
