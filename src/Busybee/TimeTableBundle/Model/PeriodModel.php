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

    /**
     * @return mixed
     */
    public function getColumnName()
    {
        if (is_null($this->getColumn()))
            return '';
        return $this->getColumn()->getName() . ' - ' . $this->getFullName();
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->getName() . ' (' . $this->getNameShort() . ')';
    }

    /**
     * @return string
     */
    public function getStartTime()
    {
        return $this->getStart()->format('H:i');
    }

    /**
     * @return string
     */
    public function getEndTime()
    {
        return $this->getEnd()->format('H:i');
    }

    public function getTimeTable()
    {
        $col = $this->getColumn();
        if (is_null($col))
            return null;

        return $col->getTimeTable();
    }
}