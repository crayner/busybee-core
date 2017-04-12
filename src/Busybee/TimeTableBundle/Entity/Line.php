<?php

namespace Busybee\TimeTableBundle\Entity;

/**
 * Line
 */
class Line
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $sequence;

    /**
     * @var \DateTime
     */
    private $lastModified;

    /**
     * @var \DateTime
     */
    private $createdOn;

    /**
     * @var \Busybee\TimeTableBundle\Entity\TimeTable
     */
    private $timetable;

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
    private $learningGroups;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->learningGroups = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Get sequence
     *
     * @return integer
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * Set sequence
     *
     * @param integer $sequence
     *
     * @return Line
     */
    public function setSequence($sequence)
    {
        $this->sequence = $sequence;

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
     * @return Line
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
     * @return Line
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get timetable
     *
     * @return \Busybee\TimeTableBundle\Entity\TimeTable
     */
    public function getTimetable()
    {
        return $this->timetable;
    }

    /**
     * Set timetable
     *
     * @param \Busybee\TimeTableBundle\Entity\TimeTable $timetable
     *
     * @return Line
     */
    public function setTimetable(\Busybee\TimeTableBundle\Entity\TimeTable $timetable = null)
    {
        $this->timetable = $timetable;

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
     * @return Line
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
     * @return Line
     */
    public function setModifiedBy(\Busybee\SecurityBundle\Entity\User $modifiedBy = null)
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }

    /**
     * Add learningGroup
     *
     * @param \Busybee\TimeTableBundle\Entity\LearningGroups $learningGroup
     *
     * @return Line
     */
    public function addLearningGroup(\Busybee\TimeTableBundle\Entity\LearningGroups $learningGroup)
    {
        if ($this->learningGroups->contains($learningGroup))
            return $this;

        $learningGroup->setLine($this);
        $this->learningGroups->add($learningGroup);

        return $this;
    }

    /**
     * Remove learningGroup
     *
     * @param \Busybee\TimeTableBundle\Entity\LearningGroups $learningGroup
     */
    public function removeLearningGroup(\Busybee\TimeTableBundle\Entity\LearningGroups $learningGroup)
    {
        $this->learningGroups->removeElement($learningGroup);
    }

    /**
     * Get learningGroups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLearningGroups()
    {
        return $this->learningGroups;
    }
}
