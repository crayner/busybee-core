<?php

namespace Busybee\TimeTableBundle\Model;

abstract class PeriodModel
{
    public function canDelete()
    {
        return true;
    }
}