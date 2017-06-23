<?php

namespace Busybee\TimeTableBundle\Model;

abstract class LineModel
{
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
    public function getSpaceName()
    {
        return '';
    }
}