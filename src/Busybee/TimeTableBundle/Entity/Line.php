<?php

namespace Busybee\TimeTableBundle\Entity;

use Busybee\TimeTableBundle\Model\LineModel;

/**
 * Line
 */
class Line extends LineModel
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
     * @var \Busybee\CurriculumBundle\Entity\Course
     */
    private $course;

    /**
     * @var \Busybee\SecurityBundle\Entity\User
     */
    private $createdBy;

    /**
     * @var \Busybee\SecurityBundle\Entity\User
     */
    private $modifiedBy;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $activities;
    /**
     * @var \Busybee\Core\CalendarBundle\Entity\Year
     */
    private $year;
    /**
     * @var integer
     */
    private $participants;
    /**
     * @var boolean
     */
    private $includeAll;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->activities = new \Doctrine\Common\Collections\ArrayCollection();
        $this->setParticipants(0);
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
     * @return LearningGroups
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
     * @return LearningGroups
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
     * @return LearningGroups
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
     * @return LearningGroups
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get course
     *
     * @return \Busybee\CurriculumBundle\Entity\Course
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * Set course
     *
     * @param \Busybee\CurriculumBundle\Entity\Course $course
     *
     * @return LearningGroups
     */
    public function setCourse(\Busybee\CurriculumBundle\Entity\Course $course = null)
    {
        $this->course = $course;

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
     * @return LearningGroups
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
     * @return LearningGroups
     */
    public function setModifiedBy(\Busybee\SecurityBundle\Entity\User $modifiedBy = null)
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }

    /**
     * Add activity
     *
     * @param \Busybee\ActivityBundle\Entity\Activity $activity
     *
     * @return LearningGroups
     */
    public function addActivity(\Busybee\ActivityBundle\Entity\Activity $activity)
    {
        $this->activities[] = $activity;

        return $this;
    }

    /**
     * Remove activity
     *
     * @param \Busybee\ActivityBundle\Entity\Activity $activity
     */
    public function removeActivity(\Busybee\ActivityBundle\Entity\Activity $activity)
    {
        $this->activities->removeElement($activity);
    }

    /**
     * Get activities
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getActivities()
    {
        return $this->activities;
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
     * @return LearningGroups
     */
	public function setYear(\Busybee\Core\CalendarBundle\Entity\Year $year = null)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get participants
     *
     * @return integer
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * Set participants
     *
     * @param integer $participants
     *
     * @return LearningGroups
     */
    public function setParticipants($participants)
    {
        $this->participants = $participants;

        return $this;
    }

    /**
     * Get includeAll
     *
     * @return boolean
     */
    public function getIncludeAll()
    {
        return $this->includeAll;
    }

    /**
     * Set includeAll
     *
     * @param boolean $includeAll
     *
     * @return LearningGroups
     */
    public function setIncludeAll($includeAll)
    {
        $this->includeAll = $includeAll;

        return $this;
    }
}
