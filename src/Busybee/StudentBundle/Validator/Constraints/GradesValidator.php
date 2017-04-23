<?php

namespace Busybee\StudentBundle\Validator\Constraints;

use Busybee\InstituteBundle\Entity\Year;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class GradesValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {

        if (empty($value))
            return;

        $current = 0;
        $year = [];

        dump($value);
        /*
                foreach ($year as $q => $w)
                    if ($w > 1) {
                        $constraint->message = 'student.enrolments.year';
                        $this->context->buildViolation($constraint->message, ['%year%' => $q])
                            ->addViolation();
                    }
                */
    }
}