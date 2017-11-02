<?php

namespace Busybee\Program\CurriculumBundle\Model;

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
    public function getFullName()
    {
        return $this->getName() . ' (' . $this->getCode() . ') ' . $this->getVersion();
    }
}
