<?php

namespace Busybee\TimeTableBundle\Validator;

use Symfony\Component\Validator\Constraint;

class Columns extends Constraint
{
    public $message = 'columns.message';

    /**
     * @return string
     */
    public function validatedBy()
    {
        return 'columns_validator';
    }
}
