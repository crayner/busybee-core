<?php
namespace Busybee\StudentBundle\Entity;

use Busybee\StudentBundle\Model\ActivityModel;

/**
 * Activity
 */
class Activity extends ActivityModel
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $nameShort;

    /**
     * @var \DateTime
     */
    private $lastModified;

    /**
     * @var \DateTime
     */
    private $createdOn;

    /**
     * @var \Busybee\InstituteBundle\Entity\Year
     */
    private $year;

    /**
     * @var \Busybee\SecurityBundle\Entity\User
     */
    private $createdBy;

    /**
     * @var \Busybee\SecurityBundle\Entity\User
     */
    private $modifiedBy;
    /**
     * @var array
     */
    private $grades;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $students;
    /**
     * @var \Busybee\StaffBundle\Entity\Staff
     */
    private $tutor1;
    /**
     * @var \Busybee\StaffBundle\Entity\Staff
     */
    private $tutor2;
    /**
     * @var \Busybee\StaffBundle\Entity\Staff
     */
    private $tutor3;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->students = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Activity
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get nameShort
     *
     * @return string
     */
    public function getNameShort()
    {
        return $this->nameShort;
    }

    /**
     * Set nameShort
     *
     * @param string $nameShort
     *
     * @return Activity
     */
    public function setNameShort($nameShort)
    {
        $this->nameShort = $nameShort;

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
     * @return Activity
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
     * @return Activity
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get year
     *
     * @return \Busybee\InstituteBundle\Entity\Year
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set year
     *
     * @param \Busybee\InstituteBundle\Entity\Year $year
     *
     * @return Activity
     */
    public function setYear(\Busybee\InstituteBundle\Entity\Year $year = null)
    {
        $this->year = $year;

        return $this;
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
     * @return Activity
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
     * @return Activity
     */
    public function setModifiedBy(\Busybee\SecurityBundle\Entity\User $modifiedBy = null)
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }

    /**
     * Get grades
     *
     * @return array
     */
    public function getGrades()
    {
        return $this->grades;
    }

    /**
     * Set grades
     *
     * @param array $grades
     *
     * @return Activity
     */
    public function setGrades($grades)
    {
        $this->grades = $grades;

        return $this;
    }

    /**
     * Add student
     *
     * @param \Busybee\StudentBundle\Entity\Student $student
     *
     * @return Activity
     */
    public function addStudent(\Busybee\StudentBundle\Entity\Student $student)
    {
        if ($this->students->contains($student))
            return $this;

        $this->students->add($student);

        return $this;
    }

    /**
     * Remove student
     *
     * @param \Busybee\StudentBundle\Entity\Student $student
     */
    public function removeStudent(\Busybee\StudentBundle\Entity\Student $student)
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
     * Get tutor1
     *
     * @return \Busybee\StaffBundle\Entity\Staff
     */
    public function getTutor1()
    {
        return $this->tutor1;
    }

    /**
     * Set tutor1
     *
     * @param \Busybee\StaffBundle\Entity\Staff $tutor1
     *
     * @return Activity
     */
    public function setTutor1(\Busybee\StaffBundle\Entity\Staff $tutor1 = null)
    {
        $this->tutor1 = $tutor1;

        return $this;
    }

    /**
     * Get tutor2
     *
     * @return \Busybee\StaffBundle\Entity\Staff
     */
    public function getTutor2()
    {
        return $this->tutor2;
    }

    /**
     * Set tutor2
     *
     * @param \Busybee\StaffBundle\Entity\Staff $tutor2
     *
     * @return Activity
     */
    public function setTutor2(\Busybee\StaffBundle\Entity\Staff $tutor2 = null)
    {
        $this->tutor2 = $tutor2;

        return $this;
    }

    /**
     * Get tutor3
     *
     * @return \Busybee\StaffBundle\Entity\Staff
     */
    public function getTutor3()
    {
        return $this->tutor3;
    }

    /**
     * Set tutor3
     *
     * @param \Busybee\StaffBundle\Entity\Staff $tutor3
     *
     * @return Activity
     */
    public function setTutor3(\Busybee\StaffBundle\Entity\Staff $tutor3 = null)
    {
        $this->tutor3 = $tutor3;

        return $this;
    }
}
