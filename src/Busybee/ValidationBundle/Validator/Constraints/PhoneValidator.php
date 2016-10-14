<?php
// src/General/ValidationBundle/Validator/Constraints/phoneValidator.php
namespace General\ValidationBundle\Validator\Constraints ;

use Symfony\Component\Validator\Constraint ;
use General\ValidationBundle\Model\ConstraintValidator ;

class PhoneValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        
        if (empty($value))
            return ;
		$this->params = $this->defaultParams();
		$this->params->match = "/^(0[3478][0-9]{8})$|^(02[3-9][0-9]{7})$|^(13[0-9]{4})$|^(1[3|8|9]00[0-9]{6})$|^(\+61[23478][0-9]{8})$|^(19[0-9]{4,6})$|^(0550[0-9]{6})$|^(059[0-9]{7})$|^(0500[0-9]{6})$|^(0198[0-3][0-9]{5})$/";
		$this->params->replace = '/[^\d]/';
		$this->params = $this->loadParams('phone');
		$number = preg_replace($this->params->replace, '', $value);
		if (!preg_match($this->params->match, $number, $matches)) {
			// If you're using the new 2.5 validation API (you probably are!)
			$this->context->buildViolation($constraint->message)
				->setParameter('%value%', $value)
				->addViolation();
		}
    }
}