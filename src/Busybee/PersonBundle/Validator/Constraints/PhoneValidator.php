<?php
namespace Busybee\PersonBundle\Validator\Constraints ;

use Symfony\Component\Validator\Constraint ;
use Symfony\Component\Validator\ConstraintValidator as ConstraintValidatorBase ;
use Busybee\Core\SystemBundle\Setting\SettingManager;

class PhoneValidator extends ConstraintValidatorBase 
{
    private $sm ;
	
	public function validate($value, Constraint $constraint)
    {
        
        if (empty($value))
            return ;

		$pattern = $this->sm->get('Phone.Validation');

		if (preg_match($pattern, $value) !== 1) {
			$this->context->buildViolation($constraint->message)
				->addViolation();
		}
    }
		
	public function __construct(SettingManager $sm)
	{
		$this->sm = $sm ;
	}
}