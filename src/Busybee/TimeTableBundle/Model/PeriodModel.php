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
}