<?php

namespace Busybee\InstituteBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

abstract class DepartmentModel
{
    protected $staffSorted = false;

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
}