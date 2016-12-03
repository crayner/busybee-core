<?php
namespace Busybee\InstituteBundle\Validator ;

use Symfony\Component\Validator\Constraint;

class CalendarDate extends Constraint
{
    public $message = 'calendar.error.date';
	
	public $fields ;
	
	public function validatedBy()
	{
		return 'calendar_date_validator' ; 
	}

	public function __construct($options)
    {
		$this->fields = $options;
    }
}
