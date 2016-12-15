<?php

namespace Busybee\InstituteBundle\Entity;

/**
 * HomeRoom
 */
class HomeRoom
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
     * @var string
     */
    private $website;

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
    private $schoolYear;

    /**
     * @var \Busybee\PersonBundle\Entity\Staff
     */
    private $tutor1;

    /**
     * @var \Busybee\PersonBundle\Entity\Staff
     */
    private $tutor2;

    /**
     * @var \Busybee\PersonBundle\Entity\Staff
     */
    private $tutor3;

    /**
     * @var \Busybee\InstituteBundle\Entity\CampusResource
     */
    private $campusResource;


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
     * @return HomeRoom
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
     * Set nameShort
     *
     * @param string $nameShort
     *
     * @return HomeRoom
     */
    public function setNameShort($nameShort)
    {
        $this->nameShort = $nameShort;

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
     * Set website
     *
     * @param string $website
     *
     * @return HomeRoom
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set lastModified
     *
     * @param \DateTime $lastModified
     *
     * @return HomeRoom
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
     * @return HomeRoom
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
     * @return HomeRoom
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
     * @return HomeRoom
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
     * Set schoolYear
     *
     * @param \Busybee\InstituteBundle\Entity\Year $schoolYear
     *
     * @return HomeRoom
     */
    public function setSchoolYear(\Busybee\InstituteBundle\Entity\Year $schoolYear = null)
    {
        $this->schoolYear = $schoolYear;

        return $this;
    }

    /**
     * Get schoolYear
     *
     * @return \Busybee\InstituteBundle\Entity\Year
     */
    public function getSchoolYear()
    {
        return $this->schoolYear;
    }

    /**
     * Set tutor1
     *
     * @param \Busybee\PersonBundle\Entity\Staff $tutor1
     *
     * @return HomeRoom
     */
    public function setTutor1(\Busybee\PersonBundle\Entity\Staff $tutor1 = null)
    {
        $this->tutor1 = $tutor1;

        return $this;
    }

    /**
     * Get tutor1
     *
     * @return \Busybee\PersonBundle\Entity\Staff
     */
    public function getTutor1()
    {
        return $this->tutor1;
    }

    /**
     * Set tutor2
     *
     * @param \Busybee\PersonBundle\Entity\Staff $tutor2
     *
     * @return HomeRoom
     */
    public function setTutor2(\Busybee\PersonBundle\Entity\Staff $tutor2 = null)
    {
        $this->tutor2 = $tutor2;

        return $this;
    }

    /**
     * Get tutor2
     *
     * @return \Busybee\PersonBundle\Entity\Staff
     */
    public function getTutor2()
    {
        return $this->tutor2;
    }

    /**
     * Set tutor3
     *
     * @param \Busybee\PersonBundle\Entity\Staff $tutor3
     *
     * @return HomeRoom
     */
    public function setTutor3(\Busybee\PersonBundle\Entity\Staff $tutor3 = null)
    {
        $this->tutor3 = $tutor3;

        return $this;
    }

    /**
     * Get tutor3
     *
     * @return \Busybee\PersonBundle\Entity\Staff
     */
    public function getTutor3()
    {
        return $this->tutor3;
    }

    /**
     * Set campusResource
     *
     * @param \Busybee\InstituteBundle\Entity\CampusResource $campusResource
     *
     * @return HomeRoom
     */
    public function setCampusResource(\Busybee\InstituteBundle\Entity\CampusResource $campusResource = null)
    {
        $this->campusResource = $campusResource;

        return $this;
    }

    /**
     * Get campusResource
     *
     * @return \Busybee\InstituteBundle\Entity\CampusResource
     */
    public function getCampusResource()
    {
        return $this->campusResource;
    }
}

