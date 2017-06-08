<?php

namespace Busybee\TimeTableBundle\Model;

abstract class LineModel
{
    public function getFullName()
    {
        return $this->getName() . ' (' . $this->getNameShort() . ')';
    }
}