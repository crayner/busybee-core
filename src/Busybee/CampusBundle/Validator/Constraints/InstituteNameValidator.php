<?php
namespace Busybee\CampusBundle\Validator\Constraints ;

use Symfony\Component\Validator\Constraint ;
use Symfony\Component\Validator\ConstraintValidator as ConstraintValidatorBase ;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Busybee\SystemBundle\Setting\SettingManager ;

class InstituteNameValidator extends ConstraintValidatorBase 
{
	public function validate($value, Constraint $constraint)
    {
        if (empty($value))
            return ;

		if ($value === 'Busybee Institute') {
			$this->context->buildViolation($constraint->message)
				->addViolation();
		}
    }
}