<?php
namespace Busybee\InstituteBundle\Validator ;

use Symfony\Component\Validator\Constraint;

class CalendarStatus extends Constraint
{
    public $message = 'calendar.error.status';
	
	public $id ;
	
	public function validatedBy()
	{
		return 'calendar_status_validator' ; 
	}

	public function __construct($options)
    {
	    if (isset($options['id']))
        {
            $this->id = $options['id'];
        }
        else
        {
            throw new \InvalidArgumentException('ID is not set for Calendar Status validation.');
        }
    }
}
