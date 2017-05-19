<?php

namespace Busybee\TimeTableBundle\Validator;

use Symfony\Component\Validator\Constraint;

class Periods extends Constraint
{
    public $message = 'periods.message';

    public function __construct()
    {
        $this->message = [];
        $this->message['overlap'] = 'periods.constraint.overlap';
        $this->message['break'] = 'periods.constraint.break';
        $this->message['order'] = 'periods.constraint.order';
        $this->message['early'] = 'periods.constraint.early';
        $this->message['late'] = 'periods.constraint.late';
        $this->message['complete'] = 'periods.constraint.complete';
    }

    /**
     * @return string
     */
    public function validatedBy()
    {
        return 'periods_validator';
    }
}
