<?php

namespace Busybee\TimeTableBundle\Entity;

use Busybee\TimeTableBundle\Model\PeriodModel;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Period
 */
class Period extends PeriodModel
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
    private $start;

    /**
     * @var \DateTime
     */
    private $end;

    /**
     * @var \DateTime
     */
    private $lastModified;

    /**
     * @var \DateTime
     */
    private $createdOn;

    /**
     * @var \Busybee\SecurityBundle\Entity\User
     */
    private $createdBy;

    /**
     * @var \Busybee\SecurityBundle\Entity\User
     */
    private $modifiedBy;

    /**
     * @var \Busybee\TimeTableBundle\Entity\Column
     */
    private $column;

    /**
     * @var boolean
     */
    private $activitiesSorted = false;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $activities;

    /**
     * @var boolean
     */
    private $break = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->activities = new \Doctrine\Common\Collections\ArrayCollection();
        $this->setBreak(false);
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
     * @return Period
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
     * @return Period
     */
    public function setNameShort($nameShort)
    {
        $this->nameShort = strtoupper($nameShort);

        return $this;
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
     * @return Period
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
     * @return Period
     */
    public function setEnd($end)
    {
        $this->end = $end;

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
     * @return Period
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
     * @return Period
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

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
     * @return Period
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
     * @return Period
     */
    public function setModifiedBy(\Busybee\SecurityBundle\Entity\User $modifiedBy = null)
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }

    /**
     * Get column
     *
     * @return \Busybee\TimeTableBundle\Entity\Column
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * Set column
     *
     * @param \Busybee\TimeTableBundle\Entity\Column $column
     *
     * @return Period
     */
    public function setColumn(\Busybee\TimeTableBundle\Entity\Column $column = null)
    {
        $this->column = $column;

        return $this;
    }

    /**
     * Add activity
     *
     * @param \Busybee\TimetableBundle\Entity\PeriodActivity $activity
     *
     * @return Period
     */
    public function addActivity(\Busybee\TimetableBundle\Entity\PeriodActivity $activity)
    {
        if ($this->activities->contains($activity) || $this->getBreak())
            return $this;

        $activity->setPeriod($this, false);
        $this->activities->add($activity);

        return $this;
    }

    /**
     * Get break
     *
     * @return boolean
     */
    public function getBreak()
    {
        return $this->break;
    }

    /**
     * Set break
     *
     * @param boolean $break
     *
     * @return Period
     */
    public function setBreak($break)
    {
        $this->break = $break;

        if ($break)
            $this->activities = new ArrayCollection();
        return $this;
    }

    /**
     * Remove activity
     *
     * @param \Busybee\TimetableBundle\Entity\PeriodActivity $activity
     */
    public function removeActivity(\Busybee\TimetableBundle\Entity\PeriodActivity $activity)
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
        if (!$this->activitiesSorted && $this->activities->count() > 0) {
            $iterator = $this->activities->getIterator();
            $iterator->uasort(function ($a, $b) {
                return ($a->getActivity()->getName() < $b->getActivity()->getName()) ? -1 : 1;
            });

            $this->activities = new ArrayCollection(iterator_to_array($iterator, false));

            $this->activitiesSorted = true;
        }
        return $this->activities;
    }
}
