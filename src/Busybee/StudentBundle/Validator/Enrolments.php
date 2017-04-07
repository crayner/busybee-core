<?php

namespace Busybee\StudentBundle\Validator;

use Symfony\Component\Validator\Constraint;

class Enrolments extends Constraint
{
    public $message = 'student.enrolments.error';

    public function validatedBy()
    {
        return 'student_enrolments_validator';
    }
}
