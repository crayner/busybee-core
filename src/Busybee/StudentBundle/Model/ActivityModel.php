<?php

namespace Busybee\StudentBundle\Model;

class ActivityModel
{
    /**
     * @return string
     */
    public function getNameYear()
    {
        return '(' . $this->getYear()->getName() . ') ' . $this->getName();
    }

    /**
     * @return integer
     */
    public function getStudentCount()
    {
        return $this->getStudents()->count();
    }
}