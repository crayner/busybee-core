<?php

namespace Busybee\SecurityBundle\Security;

use Doctrine\Common\Collections\ArrayCollection;

class VoterDetails
{
    /**
     * @var ArrayCollection
     */
    private $grades;

    /**
     * VoterDetails constructor.
     */
    public function __construct()
    {
        $this->grades = new ArrayCollection();
    }

    /**
     * Add Grade
     *
     * @param string $grade
     * @return VoterDetails
     */
    public function addGrade($grade): VoterDetails
    {
        if ($this->grades->contains($grade))
            return $this;

        $this->grades->add($grade);
        return $this;
    }

    /**
     * Remove Grade
     *
     * @param string $grade
     * @return VoterDetails
     */
    public function removeGrade($grade): VoterDetails
    {
        $this->grades->removeElement($grade);
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getGrades(): ArrayCollection
    {
        return $this->grades;
    }
}
