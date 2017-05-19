<?php

namespace Busybee\TimeTableBundle\Entity;

/**
 * Column
 */
class Column
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
     * @var string
     */
    private $mappingInfo;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $periods;
    /**
     * @var \DateTime
     */
    private $start;
    /**
     * @var \DateTime
     */
    private $end;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->periods = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Column
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
     * @return Column
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
     * @return Column
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
     * @return Column
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
     * @return Column
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
     * @return Column
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
     * @return Column
     */
    public function setModifiedBy(\Busybee\SecurityBundle\Entity\User $modifiedBy = null)
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }

    /**
     * Get mappingInfo
     *
     * @return string
     */
    public function getMappingInfo()
    {
        if (empty($this->mappingInfo))
            $this->mappingInfo = 'Rotate';
        return $this->mappingInfo;
    }

    /**
     * Set mappingInfo
     *
     * @param string $mappingInfo
     *
     * @return Column
     */
    public function setMappingInfo($mappingInfo)
    {
        $this->mappingInfo = $mappingInfo;

        return $this;
    }

    /**
     * Add Period
     *
     * @param \Busybee\TimeTableBundle\Entity\Period $period
     *
     * @return Column
     */
    public function addPeriod(\Busybee\TimeTableBundle\Entity\Period $period)
    {
        if ($this->periods->contains($period))
            return $this;

        $period->setColumn($this);
        $this->periods->add($period);

        return $this;
    }

    /**
     * Remove Period
     *
     * @param \Busybee\TimeTableBundle\Entity\Period $period
     *
     * @return Column
     */
    public function removePeriod(\Busybee\TimeTableBundle\Entity\Period $period)
    {
        $this->periods->removeElement($period);

        return $this;
    }

    /**
     * Get periods
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPeriods()
    {
        return $this->periods;
    }

    /**
     * Get start
     *
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set start
     *
     * @param \DateTime $start
     *
     * @return Column
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get end
     *
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Set end
     *
     * @param \DateTime $end
     *
     * @return Column
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }
}
