<?php

namespace Busybee\TimeTableBundle\Model;

abstract class ColumnModel
{
    /**
     * @return bool
     */
    public function canDelete()
    {
        if ($this->getPeriods()->count() > 0)
            return false;

        return true;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->getName() . ' (' . $this->getNameShort() . ')';
    }
}