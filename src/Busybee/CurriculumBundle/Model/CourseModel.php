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
}
