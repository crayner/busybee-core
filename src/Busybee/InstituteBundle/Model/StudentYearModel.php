<?php

namespace Busybee\InstituteBundle\Model;

class StudentYearModel
{
    /**
     * @return string
     */
    public function getYearName()
    {
        return $this->getYear()->getName() . ' ' . $this->getName();
    }
}