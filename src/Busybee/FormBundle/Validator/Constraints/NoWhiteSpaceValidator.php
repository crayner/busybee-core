<?php

namespace Busybee\FormBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


class NoWhiteSpaceValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (preg_match('/\s/', $value))
            $this->context->buildViolation($constraint->message)
                ->setParameter('%value%', $value)
                ->addViolation();
    }
}