<?php

namespace Busybee\DatabaseBundle\Validator\Constraints ;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManager ;
use Symfony\Component\HttpFoundation\Request ;
use Busybee\DatabaseBundle\Entity\TableRepository ;

class NameFieldValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
		if (strpos($value, ' ') !== false)
			$this->context->buildViolation($constraint->message)
				->setParameter('%string%', $value)
				->addViolation();
    }
}