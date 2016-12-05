<?php
namespace Busybee\InstituteBundle\Validator\Constraints ;

use Symfony\Component\Validator\Constraint ;
use Symfony\Component\Validator\ConstraintValidator as ConstraintValidatorBase ;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Busybee\InstituteBundle\Repository\YearRepository ;
use DateTime ;

class CalendarDateValidator extends ConstraintValidatorBase 
{

	public function validate($value, Constraint $constraint)
    {
        
        if (empty($value))
            return ;
		
		$year = reset($constraint->fields);		
		$start = $year->getFirstDay();
		$end = $year->getLastDay();
		$name = $year->getName();

		if (! $start instanceof DateTime  || ! $end instanceof DateTime)
		{
			$this->context->buildViolation('calendar.error.invalid')
				->addViolation();
			return ;
		}
		if ($start > $end )
		{
			$this->context->buildViolation('calendar.error.dateOrder')
				->addViolation();
			return ;
		}
		if ($start->diff($end)->y > 0)
		{
			$this->context->buildViolation($constraint->message)
				->addViolation();
			return ;
		}
		
		$years = $this->yr->createQueryBuilder('y')
			->where('y.id != :id')
			->setParameter('id', $year->getId())
			->getQuery()
			->getResult();

		if (is_array($years))
			foreach($years as $year)
			{
				if ($year->getFirstDay() >= $start && $year->getFirstDay() <= $end) 
				{
					$this->context->buildViolation('calendar.error.overlapped', array('%name1%' => $year->getName(), '%name2%' => $name))
						->addViolation();
					return ;
				}
				if ($year->getLastDay() >= $start && $year->getLastDay() <= $end) 
				{
					$this->context->buildViolation('calendar.error.overlapped', array('%name1%' => $year->getName(), '%name2%' => $name))
						->addViolation();
					return ;
				}
			}
    }

	private $yr ;
	
	public function __construct(YearRepository $yr)
	{
		$this->yr = $yr;
	}
}