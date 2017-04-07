<?php

namespace Busybee\StudentBundle\Validator\Constraints;

use Busybee\InstituteBundle\Entity\Year;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator as ConstraintValidatorBase;

class EnrolmentsValidator extends ConstraintValidatorBase
{
    public function validate($value, Constraint $constraint)
    {

        if (empty($value))
            return;

        $current = 0;
        $year = [];

        foreach ($value as $w) {
            if ($w->getStatus() === 'Current')
                $current++;
            if ($w->getYear() instanceof Year)
                $year[$w->getYear()->getName()] = empty($year[$w->getYear()->getName()]) ? 1 : $year[$w->getYear()->getName()] + 1;
            else {
                $constraint->message = 'student.enrolments.yearempty';
                $this->context->buildViolation($constraint->message)
                    ->addViolation();
            }
        }


        if ($current > 1) {
            $constraint->message = 'student.enrolments.current';
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }

        foreach ($year as $q => $w)
            if ($w > 1) {
                $constraint->message = 'student.enrolments.year';
                $this->context->buildViolation($constraint->message, ['%year%' => $q])
                    ->addViolation();
            }
    }
}