<?php

namespace Busybee\TimeTableBundle\Model;

abstract class ActivityGroupsModel
{
    public function getFullName()
    {
        return $this->getName() . ' (' . $this->getNameShort() . ')';
    }
}