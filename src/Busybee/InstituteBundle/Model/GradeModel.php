<?php

namespace Busybee\InstituteBundle\Model;


abstract class GradeModel
{
    /**
     * Can Delete
     *
     * @return bool
     */
    public function canDelete()
    {
        return true;
    }

    /**
     * Get Grade Year
     *
     * @return string
     */
    public function getGradeYear()
    {
        return $this->getGrade() . ' (' . $this->getYear()->getName() . ')';
    }
}