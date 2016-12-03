<?php
namespace Busybee\InstituteBundle\Validator\Constraints ;

use Symfony\Component\Validator\Constraint ;
use Symfony\Component\Validator\ConstraintValidator as ConstraintValidatorBase ;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Busybee\InstituteBundle\Repository\YearRepository ;

class CalendarStatusValidator extends ConstraintValidatorBase 
{
    private $yr ;
	
	public function validate($value, Constraint $constraint)
    {
        
        if (empty($value))
            return ;
		
		if ($value == 'current')
		{
			$xx = $this->yr->findOneByStatus('current');
			if (! is_null($xx) && $xx->getId() !== $constraint->id)
				$this->context->buildViolation($constraint->message)
					->addViolation();
		}
		
    }
		
	public function __construct(YearRepository $yr)
	{
		$this->yr = $yr ;
	}
}