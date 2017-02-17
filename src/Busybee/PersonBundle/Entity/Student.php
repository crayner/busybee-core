<?php

namespace Busybee\PersonBundle\Entity;

use Busybee\PersonBundle\Model\StudentModel;
use Busybee\SecurityBundle\Entity\User;

/**
 * Student
 */
class Student extends StudentModel
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $lastModified;

    /**
     * @var \DateTime
     */
    private $createdOn;

    /**
     * @var \Busybee\PersonBundle\Entity\Person
     */
    private $person;

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
    private $lastAtThisSchool;

    /**
     * @var string
     */
    private $status;
    /**
     * @var \DateTime
     */
    private $startAtSchool;
    /**
     * @var \DateTime
     */
    private $startAtThisSchool;

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
     * @return Student
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
     * @return Student
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get person
     *
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set person
     *
     * @param Person $person
     *
     * @return Student
     */
    public function setPerson(Person $person = null)
    {
        $this->person = $person;

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
     * @return Student
     */
    public function setCreatedBy(User $createdBy = null)
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
     * @return Student
     */
    public function setModifiedBy(User $modifiedBy = null)
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }

    /**
     * Get startAtSchool
     *
     * @return \DateTime
     */
    public function getStartAtSchool()
    {
        return $this->startAtSchool;
    }

    /**
     * Set startAtSchool
     *
     * @param \DateTime $startAtSchool
     *
     * @return Student
     */
    public function setStartAtSchool($startAtSchool)
    {
        $this->startAtSchool = $startAtSchool;

        return $this;
    }

    /**
     * Get startAtThisSchool
     *
     * @return \DateTime
     */
    public function getStartAtThisSchool()
    {
        return $this->startAtThisSchool;
    }

    /**
     * Set startAtThisSchool
     *
     * @param \DateTime $startAtThisSchool
     *
     * @return Student
     */
    public function setStartAtThisSchool($startAtThisSchool)
    {
        $this->startAtThisSchool = $startAtThisSchool;

        return $this;
    }

    /**
     * Get lastAtThisSchool
     *
     * @return \DateTime
     */
    public function getLastAtThisSchool()
    {
        return $this->lastAtThisSchool;
    }

    /**
     * Set lastAtThisSchool
     *
     * @param \DateTime $lastAtThisSchool
     *
     * @return Student
     */
    public function setLastAtThisSchool($lastAtThisSchool)
    {
        $this->lastAtThisSchool = $lastAtThisSchool;

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
     * Set status
     *
     * @param string $status
     *
     * @return Student
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }
}
