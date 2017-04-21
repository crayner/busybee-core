<?php

namespace Busybee\InstituteBundle\Validator;

use Symfony\Component\Validator\Constraint;

class Grade extends Constraint
{
    public $message = 'grade.error.default';

    public $year;

    public $errorPath = 'grades';

    public function __construct($year)
    {
        $this->year = $year;
    }

    public function validatedBy()
    {
        return 'grade_validator';
    }
}
