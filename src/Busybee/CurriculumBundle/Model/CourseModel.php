<?php

namespace Busybee\CurriculumBundle\Model;

use Doctrine\ORM\PersistentCollection;

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
    public function getTargetYearName()
    {
        return $this->getName() . ' ' . implode(' ', $this->getTargetYear());

    }
}
