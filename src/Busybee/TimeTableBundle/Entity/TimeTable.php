<?php

namespace Busybee\TimeTableBundle\Entity;

use Busybee\TimeTableBundle\Model\TimeTableModel;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * TimeTable
 */
class TimeTable extends TimeTableModel
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
     * @var \Busybee\Core\SecurityBundle\Entity\User
     */
    private $createdBy;

    /**
     * @var \Busybee\Core\SecurityBundle\Entity\User
     */
    private $modifiedBy;

    /**
     * @var \Busybee\Core\CalendarBundle\Entity\Year
     */
    private $year;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $columns;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $days;

    /**
     * @var bool
     */
    private $columnSorted = false;

    /**
     * @var boolean
     */
    private $locked;

	/**
	 * @var boolean
	 */
	private $generated;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->columns = new ArrayCollection();
        $this->days = new ArrayCollection();
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
     * @return TimeTable
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
     * @return TimeTable
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
     * @return TimeTable
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
     * @return TimeTable
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

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
     * @return TimeTable
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
     * @return TimeTable
     */
	public function setModifiedBy(\Busybee\Core\SecurityBundle\Entity\User $modifiedBy = null)
    {
        $this->modifiedBy = $modifiedBy;

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
     * @return TimeTable
     */
	public function setYear(\Busybee\Core\CalendarBundle\Entity\Year $year = null)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Add column
     *
     * @param \Busybee\TimeTableBundle\Entity\Column $column
     *
     * @return TimeTable
     */
    public function addColumn(\Busybee\TimeTableBundle\Entity\Column $column)
    {
        if ($this->columns->contains($column))
            return $this;

        $column->setTimetable($this);

        $this->columns->add($column);

        return $this;
    }

    /**
     * Remove column
     *
     * @param \Busybee\TimeTableBundle\Entity\Column $column
     */
    public function removeColumn(\Busybee\TimeTableBundle\Entity\Column $column)
    {
        $this->columns->removeElement($column);
    }

    /**
     * Get columns
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getColumns($sort = false)
    {
        if ((!$this->columnSorted && $this->columns->count() > 0) || ($this->columns->count() > 0 && $sort)) {
            $iterator = $this->columns->getIterator();
            $iterator->uasort(function ($a, $b) {
                return ($a->getSequence() < $b->getSequence()) ? -1 : 1;
            });

            $this->columns = new ArrayCollection(iterator_to_array($iterator, false));

            $this->columnSorted = true;
        }
        return $this->columns;
    }

    /**
     * Add day
     *
     * @param \Busybee\TimeTableBundle\Entity\Day $day
     *
     * @return TimeTable
     */
    public function addDay(\Busybee\TimeTableBundle\Entity\Day $day)
    {
        if ($this->days->contains($day))
            return $this;

        $day->setTimetable($this);

        $this->days->add($day);

        return $this;
    }

    /**
     * Remove day
     *
     * @param Day $day
     *
     * @return $this
     */
    public function removeDay(\Busybee\TimeTableBundle\Entity\Day $day)
    {
        $this->days->removeElement($day);

	    return $this;
    }

    /**
     * Get days
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDays()
    {
        return $this->days;
    }

    /**
     * Set locked
     *
     * @param boolean $locked
     *
     * @return TimeTable
     */
	public function setLocked($locked): TimeTable
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * Get locked
     *
     * @return boolean
     */
	public function getLocked(): bool
    {
        return $this->locked;
    }

    /**
     * Set generated
     *
     * @param boolean $generated
     *
     * @return TimeTable
     */
	public function setGenerated($generated): TimeTable
    {
        $this->generated = $generated;

        return $this;
    }

    /**
     * Get generated
     *
     * @return boolean
     */
	public function getGenerated(): bool
    {
        return $this->generated;
    }
}
