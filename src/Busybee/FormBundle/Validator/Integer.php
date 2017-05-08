<?php

namespace Busybee\FormBundle\Validator;

use Symfony\Component\Validator\Constraint;

class Integer extends Constraint
{
    public $message = 'integer.invalid.message';

    public function validatedBy()
    {
        return 'integer_validator';
    }
}
