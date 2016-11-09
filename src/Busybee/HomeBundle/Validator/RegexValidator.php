<?php
namespace Busybee\HomeBundle\Validator ;

use Symfony\Component\Validator\Constraint ;
use Symfony\Component\Validator\ConstraintValidator as ConstraintValidatorBase ;

class RegexValidator extends ConstraintValidatorBase 
{
	public function validate($value, Constraint $constraint)
    {
        
        if (empty($value))
            return ;

		$message = '';
		
		try {
			$test = preg_match($value, 'qwlrfhfriwegtiwebnf934htr 5965tb');
		}
		catch (\Exception $e)
		{
			$message = $e->getMessage();
		}

		if (! empty($message)) {
			$this->context->buildViolation($constraint->message)
				->setParameter('%systemMessage%', $message)
				->addViolation();
		}
    }
}