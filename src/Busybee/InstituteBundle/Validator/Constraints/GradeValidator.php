<?php

namespace Busybee\InstituteBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator as ConstraintValidatorBase;

class GradeValidator extends ConstraintValidatorBase
{

    public function validate($value, Constraint $constraint)
    {
        if (empty($value))
            return;


        $test = [];
        foreach ($value as $grade)
            $test[$grade->getGrade()] = isset($test[$grade->getGrade()]) ? $test[$grade->getGrade()] + 1 : 1;

        foreach ($test as $w)
            if ($w > 1) {
                $this->context->buildViolation($constraint->message)
                    ->addViolation();
                return;

            }
    }
}