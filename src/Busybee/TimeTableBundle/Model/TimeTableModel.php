<?php

namespace Busybee\TimeTableBundle\Model;

abstract class TimeTableModel
{
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