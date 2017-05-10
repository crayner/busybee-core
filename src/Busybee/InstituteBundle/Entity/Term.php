<?php

namespace Busybee\InstituteBundle\Entity;

use Busybee\InstituteBundle\Model\TermModel;

/**
 * Term
 */
class Term extends TermModel
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
    private $firstDay;

    /**
     * @var \DateTime
     */
    private $lastDay;

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
     * @var \Busybee\InstituteBundle\Entity\Year
     */
    private $year;


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
     * @return Term
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
     * @return Term
     */
    public function setNameShort($nameShort)
    {
        $this->nameShort = $nameShort;

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
     * @return Term
     */
    public function setFirstDay($firstDay)
    {
        $this->firstDay = $firstDay;

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
     * @return Term
     */
    public function setLastDay($lastDay)
    {
        $this->lastDay = $lastDay;

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
     * @return Term
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
     * @return Term
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
     * @return Term
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
     * @return Term
     */
    public function setModifiedBy(\Busybee\SecurityBundle\Entity\User $modifiedBy = null)
    {
        $this->modifiedBy = $modifiedBy;

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
     * @return Term
     */
    public function setYear(\Busybee\InstituteBundle\Entity\Year $year = null)
    {
        $this->year = $year;

        return $this;
    }
}
