<?php

namespace Busybee\InstituteBundle\Entity;

use Busybee\StaffBundle\Entity\Staff;

/**
 * Space
 */
class Space
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
    private $type;

    /**
     * @var integer
     */
    private $capacity;

    /**
     * @var boolean
     */
    private $computer;

    /**
     * @var integer
     */
    private $studentComputers;

    /**
     * @var boolean
     */
    private $projector;

    /**
     * @var boolean
     */
    private $tv;

    /**
     * @var boolean
     */
    private $dvd;

    /**
     * @var boolean
     */
    private $hifi;

    /**
     * @var boolean
     */
    private $speakers;

    /**
     * @var boolean
     */
    private $iwb;

    /**
     * @var string
     */
    private $phoneInt;

    /**
     * @var string
     */
    private $phoneExt;

    /**
     * @var string
     */
    private $comment;

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
     * @var \Busybee\InstituteBundle\Entity\Campus
     */
    private $campus;

    /**
     * @var Staff
     */
    private $staff1;

    /**
     * @var Staff
     */
    private $staff2;


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
     * @return Space
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Space
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get capacity
     *
     * @return integer
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * Set capacity
     *
     * @param integer $capacity
     *
     * @return Space
     */
    public function setCapacity($capacity)
    {
        $this->capacity = $capacity;

        return $this;
    }

    /**
     * Get computer
     *
     * @return boolean
     */
    public function getComputer()
    {
        return $this->computer;
    }

    /**
     * Set computer
     *
     * @param boolean $computer
     *
     * @return Space
     */
    public function setComputer($computer)
    {
        $this->computer = $computer;

        return $this;
    }

    /**
     * Get studentComputers
     *
     * @return integer
     */
    public function getStudentComputers()
    {
        return $this->studentComputers;
    }

    /**
     * Set studentComputers
     *
     * @param integer $studentComputers
     *
     * @return Space
     */
    public function setStudentComputers($studentComputers)
    {
        $this->studentComputers = $studentComputers;

        return $this;
    }

    /**
     * Get projector
     *
     * @return boolean
     */
    public function getProjector()
    {
        return $this->projector;
    }

    /**
     * Set projector
     *
     * @param boolean $projector
     *
     * @return Space
     */
    public function setProjector($projector)
    {
        $this->projector = $projector;

        return $this;
    }

    /**
     * Get tv
     *
     * @return boolean
     */
    public function getTv()
    {
        return $this->tv;
    }

    /**
     * Set tv
     *
     * @param boolean $tv
     *
     * @return Space
     */
    public function setTv($tv)
    {
        $this->tv = $tv;

        return $this;
    }

    /**
     * Get dvd
     *
     * @return boolean
     */
    public function getDvd()
    {
        return $this->dvd;
    }

    /**
     * Set dvd
     *
     * @param boolean $dvd
     *
     * @return Space
     */
    public function setDvd($dvd)
    {
        $this->dvd = $dvd;

        return $this;
    }

    /**
     * Get hifi
     *
     * @return boolean
     */
    public function getHifi()
    {
        return $this->hifi;
    }

    /**
     * Set hifi
     *
     * @param boolean $hifi
     *
     * @return Space
     */
    public function setHifi($hifi)
    {
        $this->hifi = $hifi;

        return $this;
    }

    /**
     * Get speakers
     *
     * @return boolean
     */
    public function getSpeakers()
    {
        return $this->speakers;
    }

    /**
     * Set speakers
     *
     * @param boolean $speakers
     *
     * @return Space
     */
    public function setSpeakers($speakers)
    {
        $this->speakers = $speakers;

        return $this;
    }

    /**
     * Get iwb
     *
     * @return boolean
     */
    public function getIwb()
    {
        return $this->iwb;
    }

    /**
     * Set iwb
     *
     * @param boolean $iwb
     *
     * @return Space
     */
    public function setIwb($iwb)
    {
        $this->iwb = $iwb;

        return $this;
    }

    /**
     * Get phoneInt
     *
     * @return string
     */
    public function getPhoneInt()
    {
        return $this->phoneInt;
    }

    /**
     * Set phoneInt
     *
     * @param string $phoneInt
     *
     * @return Space
     */
    public function setPhoneInt($phoneInt)
    {
        $this->phoneInt = $phoneInt;

        return $this;
    }

    /**
     * Get phoneExt
     *
     * @return string
     */
    public function getPhoneExt()
    {
        return $this->phoneExt;
    }

    /**
     * Set phoneExt
     *
     * @param string $phoneExt
     *
     * @return Space
     */
    public function setPhoneExt($phoneExt)
    {
        $this->phoneExt = $phoneExt;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Space
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

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
     * @return Space
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
     * @return Space
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
     * @return Space
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
     * @return Space
     */
    public function setModifiedBy(\Busybee\SecurityBundle\Entity\User $modifiedBy = null)
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }

    /**
     * Get campus
     *
     * @return \Busybee\InstituteBundle\Entity\Campus
     */
    public function getCampus()
    {
        return $this->campus;
    }

    /**
     * Set campus
     *
     * @param \Busybee\InstituteBundle\Entity\Campus $campus
     *
     * @return Space
     */
    public function setCampus(\Busybee\InstituteBundle\Entity\Campus $campus = null)
    {
        $this->campus = $campus;

        return $this;
    }

    /**
     * Get staff1
     *
     * @return Staff
     */
    public function getStaff1()
    {
        return $this->staff1;
    }

    /**
     * Set staff1
     *
     * @param Staff $staff1
     *
     * @return Space
     */
    public function setStaff1(Staff $staff1 = null)
    {
        $this->staff1 = $staff1;

        return $this;
    }

    /**
     * Get staff2
     *
     * @return Staff
     */
    public function getStaff2()
    {
        return $this->staff2;
    }

    /**
     * Set staff2
     *
     * @param Staff $staff2
     *
     * @return Space
     */
    public function setStaff2(Staff $staff2 = null)
    {
        $this->staff2 = $staff2;

        return $this;
    }
}
