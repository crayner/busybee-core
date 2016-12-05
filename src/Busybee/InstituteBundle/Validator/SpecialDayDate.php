<?php
namespace Busybee\InstituteBundle\Validator ;

use Symfony\Component\Validator\Constraint;

class SpecialDayDate extends Constraint
{
    public $message = 'specialday.error.date';
	
	public $year ;
	
	public function validatedBy()
	{
		return 'specialday_date_validator' ; 
	}

	public function __construct($year)
    {
		$this->year = $year;
    }
}
