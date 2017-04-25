<?php

namespace Busybee\InstituteBundle\Validator;

use Symfony\Component\Validator\Constraint;

class Grade extends Constraint
{
    public $message = 'year.grade.error.duplicate';

    public $year;

    public $errorPath = 'grades';

    public function __construct($year)
    {
        $this->year = $year;
    }

    public function validatedBy()
    {
        return 'calendar_grade_validator';
    }
}
