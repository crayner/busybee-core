<?php

namespace Busybee\InstituteBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

abstract class DepartmentModel
{
    /**
     * @var bool
     */
    protected $staffSorted = false;

    /**
     * @var bool
     */
    protected $coursesSorted = false;

    /**
     * Sort Staff
     *
     * @return ArrayCollection
     */
    protected function sortStaff()
    {
        if (count($this->getStaff(false)) == 0 || $this->staffSorted)
            return $this->getStaff(false);

        $iterator = $this->getStaff(false)->getIterator();
        $iterator->uasort(function ($a, $b) {
            return ($a->getStaff()->getFullName() < $b->getStaff()->getFullName()) ? -1 : 1;
        });

        $staff = new ArrayCollection(iterator_to_array($iterator, false));

        $this->staffSorted = true;
        $this->setStaff($staff);

        return $this->getStaff(false);
    }

    /**
     * Sort Courses
     *
     * @return ArrayCollection
     */
    protected function sortCourses()
    {
        if (count($this->getCourses(false)) == 0 || $this->coursesSorted)
            return $this->getCourses(false);

        $iterator = $this->getCourses(false)->getIterator();
        $iterator->uasort(function ($a, $b) {
            return ($a->getName() < $b->getName()) ? -1 : 1;
        });

        $courses = new ArrayCollection(iterator_to_array($iterator, false));

        $this->coursesSorted = true;
        $this->setCourses($courses);

        return $this->getCourses(false);
    }
}