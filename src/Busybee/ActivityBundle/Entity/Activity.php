<?php

namespace Busybee\ActivityBundle\Entity;

use Busybee\ActivityBundle\Model\ActivityModel;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @var \Busybee\Core\CalendarBundle\Entity\Year
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
     * @var ArrayCollection
     */
    private $students;

    /**
     * @var bool
     */
    private $studentsSorted = false;

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
     * @var ArrayCollection
     */
    private $grades;

    /**
     * @var \Busybee\InstituteBundle\Entity\Space
     */
    private $space;

    /**
     * @var \Busybee\ActivityBundle\Entity\Activity
     */
    private $studentReference;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $periods;

    /**
     * @var integer
     */
    private $teachingLoad;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->students = new ArrayCollection();
        $this->periods = new ArrayCollection();
        $this->grades = new ArrayCollection();
        $this->setTeachingLoad(0);
        $this->studentsSorted = false;
        parent::__construct();
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
        $this->nameShort = str_replace([' ', '\t', '\n', '\r', '\0', '\x0B'], '', strtoupper($nameShort));

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
     * @return \Busybee\Core\CalendarBundle\Entity\Year
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set year
     *
     * @param \Busybee\Core\CalendarBundle\Entity\Year $year
     *
     * @return Activity
     */
	public function setYear(\Busybee\Core\CalendarBundle\Entity\Year $year = null)
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
     * @return ArrayCollection
     */
    public function getStudents()
    {
        if ($this->getStudentReference() instanceof Activity)
            $this->students = $this->getStudentReference()->getStudents();

        if (!$this->studentsSorted && $this->students->count() > 0) {

            $iterator = $this->students->getIterator();
            $iterator->uasort(
                function ($a, $b) {
                    return ($a->formatName(['surnameFirst' => true]) < $b->formatName(['surnameFirst' => true])) ? -1 : 1;
                }
            );

            $this->students = new ArrayCollection(iterator_to_array($iterator, false));

            $this->studentsSorted = true;
        }

        return $this->students;
    }

    /**
     * Get studentReference
     *
     * @return \Busybee\ActivityBundle\Entity\Activity
     */
    public function getStudentReference()
    {
        return $this->studentReference;
    }

    /**
     * Set studentReference
     *
     * @param \Busybee\ActivityBundle\Entity\Activity $studentReference
     *
     * @return Activity
     */
    public function setStudentReference(\Busybee\ActivityBundle\Entity\Activity $studentReference = null)
    {
        // stop self reference
        if ($studentReference instanceof Activity && $studentReference->getId() == $this->getId())
            $studentReference = null;

        $this->studentReference = $studentReference;

        return $this;
    }

    /**
     * Get ids
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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

    /**
     * Get space
     *
     * @return \Busybee\InstituteBundle\Entity\Space
     */
    public function getSpace()
    {
        return $this->space;
    }

    /**
     * Set space
     *
     * @param \Busybee\InstituteBundle\Entity\Space $space
     *
     * @return Activity
     */
    public function setSpace(\Busybee\InstituteBundle\Entity\Space $space = null)
    {
        $this->space = $space;

        return $this;
    }

    /**
     * Add period
     *
     * @param \Busybee\TimeTableBundle\Entity\PeriodActivity $period
     *
     * @return Activity
     */
    public function addPeriod(\Busybee\TimeTableBundle\Entity\PeriodActivity $period)
    {
        if ($this->periods->contains($period))
            return $this;
        $period->setActivity($this, false);

        $this->periods->add($period);

        return $this;
    }

    /**
     * Remove period
     *
     * @param \Busybee\TimeTableBundle\Entity\PeriodActivity $period
     */
    public function removePeriod(\Busybee\TimeTableBundle\Entity\PeriodActivity $period)
    {
        $this->periods->removeElement($period);
    }

    /**
     * Get periods
     *
     * @return ArrayCollection
     */
    public function getPeriods()
    {
        return $this->periods;
    }

    /**
     * Add grade
     *
     * @param \Busybee\Core\CalendarBundle\Entity\Grade $grade
     *
     * @return Activity
     */
	public function addGrade(\Busybee\Core\CalendarBundle\Entity\Grade $grade)
    {
        if ($this->grades->contains($grade))
            return $this;

        $grade->setActivity($this);

        $this->grades->add($grade);

        return $this;
    }

    /**
     * Remove grade
     *
     * @param \Busybee\Core\CalendarBundle\Entity\Grade $grade
     */
	public function removeGrade(\Busybee\Core\CalendarBundle\Entity\Grade $grade)
    {
        $this->grades->removeElement($grade);

        return $this;
    }

    /**
     * Get grades
     *
     * @return ArrayCollection
     */
    public function getGrades()
    {
        return $this->grades;
    }

    /**
     * Get teachingLoad
     *
     * @return integer
     */
    public function getTeachingLoad()
    {
        return intval($this->teachingLoad);
    }

    /**
     * Set teachingLoad
     *
     * @param integer $teachingLoad
     *
     * @return Activity
     */
    public function setTeachingLoad($teachingLoad)
    {
        $this->teachingLoad = intval($teachingLoad);

        return $this;
    }
}
