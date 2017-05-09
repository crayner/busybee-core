<?php

namespace Busybee\TimeTableBundle\Model;

abstract class TimeTableModel
{
    public function __construct()
    {
        $this->setSpecialDaySkip(true);
    }

    /**
     * Get Full Name
     * @return string
     */
    public function getFullName()
    {
        $year = is_null($this->getYear()) ? '' : $this->getYear()->getName();
        if (empty($this->getName()))
            return 'TimeTable';
        return $this->getName() . ' (' . $year . ')';
    }
}