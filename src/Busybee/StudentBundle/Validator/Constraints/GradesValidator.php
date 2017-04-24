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


        foreach ($value->toArray() as $grade) {
            if (empty($grade->getStudent()) || empty($grade->getGrade())) {

                dump($grade);
                die();

                $this->context->buildViolation('student.grades.empty')
                    ->addViolation();
                return $value;
            }
        }
        /*
                foreach ($year as $q => $w)
                    if ($w > 1) {
                        $constraint->message = 'student.enr lments.year';
                        $this->context->buildViolation($constraint->message, ['%year%' => $q])
                            ->addViolation();
                    }
                */
    }
}