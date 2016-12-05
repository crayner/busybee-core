<?php
namespace Busybee\InstituteBundle\Validator\Constraints ;

use Symfony\Component\Validator\Constraint ;
use Symfony\Component\Validator\ConstraintValidator as ConstraintValidatorBase ;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Busybee\InstituteBundle\Repository\YearRepository ;
use DateTime ;

class SpecialDayDateValidator extends ConstraintValidatorBase 
{

	public function validate($value, Constraint $constraint)
    {
        if (empty($value))
            return ;
			
		foreach($value as $key=>$day)
		{
			if ($day->getType() == 'alter')
			{
				$ok = true;
				if (empty($day->getOpen()))
				{
					$this->context->buildViolation('specialDay.error.timeEmpty')
						->addViolation();
					return ;
				}
				if (empty($day->getStart()))
				{
					$this->context->buildViolation('specialDay.error.timeEmpty')
						->addViolation();
					return ;
				}
				if (empty($day->getFinish()))
				{
					$this->context->buildViolation('specialDay.error.timeEmpty')
						->addViolation();
					return ;
				}
				if (empty($day->getClose()))
				{
					$this->context->buildViolation('specialDay.error.timeEmpty')
						->addViolation();
					return ;
				}
				$time = array(
					'a' => $day->getOpen(),
					'b' => $day->getStart(),
					'c' => $day->getFinish(),
					'd' => $day->getClose(),
				);
				$ttime = $time;
				asort($ttime);
				if ($time !== $ttime)
				{
					$this->context->buildViolation('specialDay.error.timeInvalid')
						->addViolation();
					return ;
				}
			}
		}
    }
}