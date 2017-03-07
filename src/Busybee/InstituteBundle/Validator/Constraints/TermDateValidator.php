<?php
namespace Busybee\InstituteBundle\Validator\Constraints ;

use Symfony\Component\Validator\Constraint ;
use Symfony\Component\Validator\ConstraintValidator as ConstraintValidatorBase ;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Busybee\InstituteBundle\Repository\YearRepository ;
use DateTime ;

class TermDateValidator extends ConstraintValidatorBase 
{

	public function validate($value, Constraint $constraint)
    {
        
        if (empty($value))
            return ;
		
		foreach($value as $key=>$term)
		{
			if (empty($term->getName()) || empty($term->getnameShort()))
			{
				$value->remove($key);
				$constraint->year->removeTerm($term);
			}
		}

		$yearStart = $constraint->year->getFirstDay();
		$yearEnd = $constraint->year->getLastDay();

		if (! $yearStart instanceof DateTime  || ! $yearEnd instanceof DateTime)
		{
			$this->context->buildViolation('term.error.invalidYear')
				->addViolation();
			return ;
		}
		if ($yearStart > $yearEnd)
		{
			$this->context->buildViolation('calendar.error.dateOrder')
				->addViolation();
			return ;
		}
		if ($yearStart->diff($yearEnd)->y > 0)
		{
			$this->context->buildViolation('calendar.error.date')
				->addViolation();
			return ;
		}
		
		$terms = array();
		
		foreach($value as $term)
		{
			if (! $term->getFirstDay() instanceof DateTime  || ! $term->getLastDay() instanceof DateTime)
			{
				$this->context->buildViolation('term.error.invalid')
					->addViolation();
				return ;
			}
            if ($term->getFirstDay() > $term->getLastDay()) {
                $this->context->buildViolation('term.error.order')
                    ->addViolation();
                return;
            }
			if ($term->getFirstDay() < $yearStart)
			{	
				$this->context->buildViolation('term.error.outsideYear', array('%term_date%' => $term->getFirstDay()->format('jS M Y'), '%year_date%' => $yearStart->format('jS M Y'), '%operator%' => '<'))
					->addViolation();
				return ;
			}
			if ($term->getLastDay() > $yearEnd)
			{
				$this->context->buildViolation('term.error.outsideYear', array('%term_date%' => $term->getLastDay()->format('jS M Y'), '%year_date%' => $yearEnd->format('jS M Y'), '%operator%' => '>'))
					->addViolation();
				return ;
			}
			foreach($terms as $name=>$test)
			{
				if ($term->getFirstDay() >= $test['start'] && $term->getFirstDay() <= $test['end']) 
				{
					$this->context->buildViolation('term.error.overlapped', array('%name1%' => $name, '%name2%' => $term->getName()))
						->addViolation();
					return ;
				}
				if ($term->getLastDay() >= $test['start'] && $term->getLastDay() <= $test['end']) 
				{
					$this->context->buildViolation('term.error.overlapped', array('%name1%' => $name, '%name2%' => $term->getName()))
						->addViolation();
					return ;
				}
			}
			$terms[$term->getName()]['start'] = $term->getFirstDay();
			$terms[$term->getName()]['end'] = $term->getLastDay();
		}
		
		return $value ;
    }
}