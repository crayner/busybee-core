<?php

namespace Busybee\TimeTableBundle\Model;

abstract class DayModel
{
    public function __construct()
    {
        $this->setDayType(true);
    }
}