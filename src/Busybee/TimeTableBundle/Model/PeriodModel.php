<?php

namespace Busybee\TimeTableBundle\Model;

abstract class PeriodModel
{
    /**
     * @return bool
     * @todo    Build canDelete Test for Period
     */
    public function canDelete()
    {
        return true;
    }
}