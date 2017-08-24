<?php

namespace Busybee\StudentBundle\Entity;

use Busybee\StudentBundle\Model\StudentGradeModel;

/**
 * StudentGrade
 */
class StudentGrade extends StudentGradeModel
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $status;

    /**
     * @var \DateTime
     */
    private $lastModified;

    /**
     * @var \DateTime
     */
    private $createdOn;

    /**
     * @var \Busybee\StudentBundle\Entity\Student
     */
    private $student;

    /**
     * @var \Busybee\Core\CalendarBundle\Entity\Grade
     */
    private $grade;

    /**
     * @var \Busybee\Core\SecurityBundle\Entity\User
     */
    private $createdBy;

    /**
     * @var \Busybee\Core\SecurityBundle\Entity\User
     */
    private $modifiedBy;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return StudentGrade
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get lastModified
     *
     * @return \DateTime
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * Set lastModified
     *
     * @param \DateTime $lastModified
     *
     * @return StudentGrade
     */
    public function setLastModified($lastModified)
    {
        $this->lastModified = $lastModified;

        return $this;
    }

    /**
     * Get createdOn
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return StudentGrade
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get student
     *
     * @return \Busybee\StudentBundle\Entity\Student
     */
    public function getStudent()
    {
        return $this->student;
    }

    /**
     * Set student
     *
     * @param \Busybee\StudentBundle\Entity\Student $student
     *
     * @return StudentGrade
     */
    public function setStudent(\Busybee\StudentBundle\Entity\Student $student = null, $add = true)
    {
        if ($add)
            $student->addGrade($this);

        $this->student = $student;

        return $this;
    }

    /**
     * Get grade
     *
     * @return \Busybee\Core\CalendarBundle\Entity\Grade
     */
    public function getGrade()
    {
        return $this->grade;
    }

    /**
     * Set grade
     *
     * @param \Busybee\Core\CalendarBundle\Entity\Grade $grade
     *
     * @return StudentGrade
     */
	public function setGrade(\Busybee\Core\CalendarBundle\Entity\Grade $grade = null, $add = true)
    {
        if ($add)
            $grade->addStudent($this);

        $this->grade = $grade;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \Busybee\Core\SecurityBundle\Entity\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set createdBy
     *
     * @param \Busybee\Core\SecurityBundle\Entity\User $createdBy
     *
     * @return StudentGrade
     */
	public function setCreatedBy(\Busybee\Core\SecurityBundle\Entity\User $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get modifiedBy
     *
     * @return \Busybee\Core\SecurityBundle\Entity\User
     */
    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    /**
     * Set modifiedBy
     *
     * @param \Busybee\Core\SecurityBundle\Entity\User $modifiedBy
     *
     * @return StudentGrade
     */
	public function setModifiedBy(\Busybee\Core\SecurityBundle\Entity\User $modifiedBy = null)
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }
}
