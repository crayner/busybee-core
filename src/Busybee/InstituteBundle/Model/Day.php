<?php
namespace Busybee\InstituteBundle\Model;

class Day extends \Busybee\InstituteBundle\Service\WidgetService\Day 
{

    private $isHoliday = false;

    public function getIsHoliday()
    {
        return $this->isHoliday;
    }

    public function getIsWeekEnd()
    {
        return in_array((int)$this->date->format('N'), array(0,6,7));
    }

    public function setIsHoliday($value)
    {
        $this->isHoliday = $value;
    }
}
