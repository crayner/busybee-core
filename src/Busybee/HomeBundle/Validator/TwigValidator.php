<?php
namespace Busybee\HomeBundle\Validator ;

use Symfony\Component\Validator\Constraint ;
use Symfony\Component\Validator\ConstraintValidator as ConstraintValidatorBase ;
use Twig_Environment;

class TwigValidator extends ConstraintValidatorBase 
{
	public function validate($value, Constraint $constraint)
    {
        
        if (empty($value))
            return ;

		$message = '';
		try {
			$twig = new \Twig_Environment(new \Twig_Loader_String());
			$test = $twig->render($value);
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