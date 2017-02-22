<?php
namespace Busybee\StudentBundle\Entity;

use Busybee\PersonBundle\Entity\Person;
use Busybee\SecurityBundle\Entity\User;
use Busybee\StudentBundle\Model\StudentModel;

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
    private $startAtSchool;

    /**
     * @var \DateTime
     */
    private $startAtThisSchool;

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
    private $lastModified;

    /**
     * @var \DateTime
     */
    private $createdOn;

    /**
     * @var Person
     */
    private $person;

    /**
     * @var User
     */
    private $createdBy;

    /**
     * @var User
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
    public function setStartAtSchool(\DateTime $startAtSchool)
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
    public function setStartAtThisSchool(\DateTime $startAtThisSchool)
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
    public function setLastAtThisSchool(\DateTime $lastAtThisSchool = null)
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
     * @return \Busybee\PersonBundle\Entity\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set person
     *
     * @param \Busybee\PersonBundle\Entity\Person $person
     *
     * @return Student
     */
    public function setPerson(\Busybee\PersonBundle\Entity\Person $person = null)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set createdBy
     *
     * @param User $createdBy
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
     * @return User
     */
    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    /**
     * Set modifiedBy
     *
     * @param User $modifiedBy
     *
     * @return Student
     */
    public function setModifiedBy(User $modifiedBy = null)
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }
}

