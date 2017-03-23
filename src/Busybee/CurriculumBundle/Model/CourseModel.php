<?php

namespace Busybee\CurriculumBundle\Model;

/**
 * Course
 */
class CourseModel
{
    /**
     * @return string
     */
    public function getNameVersion()
    {
        return $this->getName() . ' ' . $this->getVersion();
    }

    /**
     * @return string
     */
    public function getStudentYearName()
    {
        return is_null($this->getStudentYear()) ? '' : $this->getName() . ' ' . $this->getStudentYear()->getName();

    }
}
