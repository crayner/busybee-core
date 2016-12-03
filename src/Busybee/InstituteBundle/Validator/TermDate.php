<?php
namespace Busybee\InstituteBundle\Validator ;

use Symfony\Component\Validator\Constraint;

class TermDate extends Constraint
{
    public $message = 'term.error.date';
	
	public $year ;
	
	public function validatedBy()
	{
		return 'term_date_validator' ; 
	}

	public function __construct($year)
    {
		$this->year = $year;
    }
}
