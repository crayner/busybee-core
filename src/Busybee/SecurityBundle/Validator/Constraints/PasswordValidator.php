<?php
// src/General/ValidationBundle/Validator/Constraints/PasswordValidator.php
namespace Busybee\SecurityBundle\Validator\Constraints ;

use Symfony\Component\Validator\Constraint ;
use Symfony\Component\Validator\ConstraintValidator as ConstraintValidatorBase ;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class PasswordValidator extends ConstraintValidatorBase 
{
    /**
     * @var array
     */
    private $passwordParameters;

    public function __construct($passwordParameters = [])
    {
        $this->passwordParameters = (array)$passwordParameters;
    }

    public function validate($value, Constraint $constraint)
    {

        if (empty($value))
            return ;

        $pw = $this->passwordParameters;


		$num = 'No';
		$case = 'No';
		$spec = 'No';

		$pattern = "/^(.*";
		if ($pw['mixedCase']) {
			$pattern .= "(?=.*[a-z])(?=.*[A-Z])";
			$case = 'Yes';
		}
		if ($pw['numbers']) {
			$pattern .= "(?=.*[0-9])";
			$num = 'Yes';
		}
		if ($pw['specials']) {
			$pattern .= "(?=.*?[#?!@$%^&*-])";
			$spec = 'yes';
		}
		$pattern .= ".*){".$pw['minLength'].",}$/";

        if (preg_match($pattern, $value) !== 1) {
			$this->context->buildViolation($constraint->message)
				->setParameter('%numbers%', $num)
				->setParameter('%mixedCase%', $case)
				->setParameter('%specials%', $spec)
				->setParameter('%minLength%', $pw['minLength'])
				->addViolation();
		}
    }
}