<?php

namespace Busybee\SecurityBundle\Security;

use Busybee\StudentBundle\Entity\Student;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;

class VoterDetails
{
    /**
     * @var ArrayCollection
     */
    private $grades;

    /**
     * @var Student
     */
    private $student;

    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * VoterDetails constructor.
     */
    public function __construct(ObjectManager $om)
    {
        $this->grades = new ArrayCollection();
        $this->student = null;
        $this->om = $om;
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

    /**
     * Get Student
     *
     * @return null
     */
    public function getStudent()
    {
        return $this->student;
    }

    /**
     * Set Student
     *
     * @param Student $student
     * @return VoterDetails
     */
    public function setStudent(Student $student): VoterDetails
    {
        $this->student = $student;

        return $this;
    }

    /**
     * Add Student
     *
     * @param int $id
     * @return VoterDetails
     */
    public function addStudent(int $id): VoterDetails
    {
        if (gettype($id) !== 'integer')
            return $this;

        $student = $this->om->getRepository(Student::class)->find($id);
        if ($student instanceof Student)
            $this->setStudent($student);
        return $this;
    }
}
