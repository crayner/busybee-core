<?php

namespace Busybee\TimeTableBundle\Model;

use Busybee\TimeTableBundle\Entity\TimeTable;

abstract class TimeTableModel
{
    /**
     * @var bool
     */
    protected $columnSort = false;

    /**
     * Get Full Name
     *
     * @return string
     */
    public function getFullName()
    {
        $year = is_null($this->getYear()) ? '' : $this->getYear()->getName();
        if (empty($this->getName()))
            return 'TimeTable';
        return $this->getName() . ' (' . $year . ')';
    }

    /**
     * Clear Column Sort
     *
     * @return TimeTable
     */
    public function clearColumnSort()
    {
        $this->columnSort = false;

        return $this;
    }
}