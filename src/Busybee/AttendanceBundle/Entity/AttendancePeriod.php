<?php

namespace Busybee\AttendanceBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * AttendancePeriod
 */
class AttendancePeriod
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $attendanceDate;

    /**
     * @var \DateTime
     */
    private $lastModified;

    /**
     * @var \DateTime
     */
    private $createdOn;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $students;

    /**
     * @var \Busybee\SecurityBundle\Entity\User
     */
    private $createdBy;

    /**
     * @var \Busybee\SecurityBundle\Entity\User
     */
    private $modifiedBy;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->students = new ArrayCollection();
    }

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
     * Get attendanceDate
     *
     * @return \DateTime
     */
    public function getAttendanceDate()
    {
        return $this->attendanceDate;
    }

    /**
     * Set attendanceDate
     *
     * @param \DateTime $attendanceDate
     *
     * @return AttendancePeriod
     */
    public function setAttendanceDate($attendanceDate)
    {
        $this->attendanceDate = $attendanceDate;

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
     * @return AttendancePeriod
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
     * @return AttendancePeriod
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Add student
     *
     * @param \Busybee\AttendanceBundle\Entity\AttendancePeriodStudent $student
     *
     * @return AttendancePeriod
     */
    public function addStudent(\Busybee\AttendanceBundle\Entity\AttendancePeriodStudent $student)
    {
        $this->students[] = $student;

        return $this;
    }

    /**
     * Remove student
     *
     * @param \Busybee\AttendanceBundle\Entity\AttendancePeriodStudent $student
     */
    public function removeStudent(\Busybee\AttendanceBundle\Entity\AttendancePeriodStudent $student)
    {
        $this->students->removeElement($student);
    }

    /**
     * Get students
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStudents()
    {
        return $this->students;
    }

    /**
     * Get createdBy
     *
     * @return \Busybee\SecurityBundle\Entity\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set createdBy
     *
     * @param \Busybee\SecurityBundle\Entity\User $createdBy
     *
     * @return AttendancePeriod
     */
    public function setCreatedBy(\Busybee\SecurityBundle\Entity\User $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get modifiedBy
     *
     * @return \Busybee\SecurityBundle\Entity\User
     */
    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    /**
     * Set modifiedBy
     *
     * @param \Busybee\SecurityBundle\Entity\User $modifiedBy
     *
     * @return AttendancePeriod
     */
    public function setModifiedBy(\Busybee\SecurityBundle\Entity\User $modifiedBy = null)
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }
}

