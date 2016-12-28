<?php

namespace Busybee\PersonBundle\Entity;

use Busybee\PersonBundle\Model\StudentModel;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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
     * @return Student
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
     * Get person
     *
     * @return \Busybee\PersonBundle\Entity\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set createdBy
     *
     * @param \Busybee\SecurityBundle\Entity\User $createdBy
     *
     * @return Student
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
     * @return Student
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
    private $startAtSchool;

    /**
     * @var \DateTime
     */
    private $startAtThisSchool;


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
     * Get startAtSchool
     *
     * @return \DateTime
     */
    public function getStartAtSchool()
    {
        return $this->startAtSchool;
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
     * Get startAtThisSchool
     *
     * @return \DateTime
     */
    public function getStartAtThisSchool()
    {
        return $this->startAtThisSchool;
    }
}
