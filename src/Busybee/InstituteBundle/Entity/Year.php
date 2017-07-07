<?php

namespace Busybee\InstituteBundle\Entity;

use Busybee\HomeBundle\Exception\Exception;
use Busybee\InstituteBundle\Model\Year as YearModel ;
use Doctrine\Common\Collections\ArrayCollection ;

/**
 * Year
 */
class Year extends YearModel
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
     * @var \Busybee\SecurityBundle\Entity\User
     */
    private $createdBy;

    /**
     * @var \Busybee\SecurityBundle\Entity\User
     */
    private $modifiedBy;
    /**
     * @var \DateTime
     */
    private $firstDay;
    /**
     * @var \DateTime
     */
    private $lastDay;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $terms;
    /**
     * @var boolean
     */
    private $termsSorted = false;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $specialDays;

    /**
     * @var boolean
     */
    private $specialDaysSorted = false;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $grades;

    /**
     * @var boolean
     */
    private $gradesSorted = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->specialDays = new ArrayCollection();
        $this->terms = new ArrayCollection();
        $this->grades = new ArrayCollection();
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
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return strtolower($this->status);
    }

    /**
     * Set status
     *
     * @param $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = strtolower($status);

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
     * @return Year
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
     * @return Year
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
     * @return Year
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
     * @return Year
     */
    public function setModifiedBy(\Busybee\SecurityBundle\Entity\User $modifiedBy = null)
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }

    /**
     * Get lastDay
     *
     * @return \DateTime
     */
    public function getLastDay()
    {
        return $this->lastDay;
    }

    /**
     * Set lastDay
     *
     * @param \DateTime $lastDay
     *
     * @return Year
     */
    public function setLastDay($lastDay)
    {
        $this->lastDay = $lastDay;

        return $this;
    }

    /**
     * Add term
     *
     * @param \Busybee\InstituteBundle\Entity\Term $term
     *
     * @return Year
     */
    public function addTerm(\Busybee\InstituteBundle\Entity\Term $term)
    {
        if ($this->terms->contains($term))
            return $this;

        $term->setYear($this);

        $this->terms->add($term);

        return $this;
    }

    /**
     * Remove term
     *
     * @param \Busybee\InstituteBundle\Entity\Term $term
     */
    public function removeTerm(\Busybee\InstituteBundle\Entity\Term $term)
    {
        $this->terms->removeElement($term);
    }

    /**
     * Get terms
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTerms()
    {
        if (count($this->terms) == 0 || $this->termsSorted)
            return $this->terms;

        $iterator = $this->terms->getIterator();
        $iterator->uasort(function ($a, $b) {
            return ($a->getFirstDay() < $b->getFirstDay()) ? -1 : 1;
        });

        $this->terms = new ArrayCollection(iterator_to_array($iterator, false));

        $this->termsSorted = true;

        return $this->terms;
    }

    /**
     * Add specialDay
     *
     * @param \Busybee\InstituteBundle\Entity\SpecialDay $specialDay
     *
     * @return Term
     */
    public function addSpecialDay(\Busybee\InstituteBundle\Entity\SpecialDay $specialDay)
    {
        if (! is_null($specialDay->getName()))
            $this->specialDays[] = $specialDay;

        return $this;
    }

    /**
     * Remove specialDay
     *
     * @param \Busybee\InstituteBundle\Entity\SpecialDay $specialDay
     */
    public function removeSpecialDay(\Busybee\InstituteBundle\Entity\SpecialDay $specialDay)
    {
        $this->specialDays->removeElement($specialDay);
    }

    /**
     * Get specialDays
     *
     * @return null|\Doctrine\Common\Collections\Collection
     */
    public function getSpecialDays($renew = false)
    {
        if (count($this->specialDays) == 0)
            return null ;

        if ($this->specialDaysSorted && !$renew)
            return $this->specialDays;

        $iterator = $this->specialDays->getIterator();
        $iterator->uasort(function ($a, $b) {
            return ($a->getDay() < $b->getDay()) ? -1 : 1;
        });
        $this->specialDays = new ArrayCollection(iterator_to_array($iterator, false));
        $this->specialDaysSorted = true ;

        return $this->specialDays;
    }

    /**
     * Add grade
     *
     * @param \Busybee\InstituteBundle\Entity\Grade $grade
     *
     * @return Year
     */
    public function addGrade(\Busybee\InstituteBundle\Entity\Grade $grade)
    {
        if ($this->grades->contains($grade))
            return $this;

        $grade->setYear($this);

        $this->grades->add($grade);

        return $this;
    }

    /**
     * Remove grade
     *
     * @param \Busybee\InstituteBundle\Entity\Grade $grade
     */
    public function removeGrade(\Busybee\InstituteBundle\Entity\Grade $grade)
    {
        $this->grades->removeElement($grade);
    }

    /**
     * Get grades
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGrades()
    {
        if (count($this->grades) == 0)
            return null;

        if ($this->gradesSorted)
            return $this->grades;

        $iterator = $this->grades->getIterator();
        $iterator->uasort(function ($a, $b) {
            return ($a->getSequence() < $b->getSequence()) ? -1 : 1;
        });

        $this->grades = new ArrayCollection(iterator_to_array($iterator, false));
        $this->gradesSorted = true;

        return $this->grades;

    }

    public function __toString()
    {
        return $this->getName() . ' ' . $this->getFirstDay()->format('d/M/Y');
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
     * @return Year
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get firstDay
     *
     * @return \DateTime
     */
    public function getFirstDay()
    {
        return $this->firstDay;
    }

    /**
     * Set firstDay
     *
     * @param \DateTime $firstDay
     *
     * @return Year
     */
    public function setFirstDay($firstDay)
    {
        $this->firstDay = $firstDay;

        return $this;
    }
}
