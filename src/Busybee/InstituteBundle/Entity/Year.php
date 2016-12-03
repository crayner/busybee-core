<?php

namespace Busybee\InstituteBundle\Entity;

use Busybee\InstituteBundle\Model\Year as YearModel ;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Year
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
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
     * Get lastModified
     *
     * @return \DateTime
     */
    public function getLastModified()
    {
        return $this->lastModified;
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
     * Get createdOn
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
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
     * Get createdBy
     *
     * @return \Busybee\SecurityBundle\Entity\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
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
     * Get modifiedBy
     *
     * @return \Busybee\SecurityBundle\Entity\User
     */
    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    /**
     * @var \DateTime
     */
    private $firstDay;

    /**
     * @var \DateTime
     */
    private $lastDay;

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
     * Get lastDay
     *
     * @return \DateTime
     */
    public function getLastDay()
    {
        return $this->lastDay;
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $terms;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->terms = new \Doctrine\Common\Collections\ArrayCollection();
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
        $this->terms[] = $term;

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
     * @var boolean
     */
    private $termsSorted = false;

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
		$this->terms = new \Doctrine\Common\Collections\ArrayCollection(iterator_to_array($iterator));
		$this->termsSorted = true ;
		return $this->terms;
    }
}
