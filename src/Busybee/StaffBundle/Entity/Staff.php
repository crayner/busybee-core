<?php

namespace Busybee\StaffBundle\Entity;

use Busybee\PersonBundle\Entity\Person;
use Busybee\SecurityBundle\Entity\User;
use Busybee\StaffBundle\Model\StaffModel;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Staff
 */
class Staff extends StaffModel
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $staffType;

    /**
     * @var string
     */
    private $jobTitle;

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
     * @var string
     */
    private $house;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $department;
    /**
     * @var \Busybee\InstituteBundle\Entity\Space
     */
    private $homeroom;

    /**
     * Staff constructor.
     */
    public function __construct()
    {
        $this->department = new ArrayCollection();
        parent::__construct();
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
     * Get staffType
     *
     * @return string
     */
    public function getStaffType()
    {
        return $this->staffType;
    }

    /**
     * Set staffType
     *
     * @param string $staffType
     *
     * @return Staff
     */
    public function setStaffType($staffType)
    {
        $this->staffType = $staffType;

        return $this;
    }

    /**
     * Get jobTitle
     *
     * @return string
     */
    public function getJobTitle()
    {
        return $this->jobTitle;
    }

    /**
     * Set jobTitle
     *
     * @param string $jobTitle
     *
     * @return Staff
     */
    public function setJobTitle($jobTitle)
    {
        $this->jobTitle = $jobTitle;

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
     * @return Staff
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
     * @return Staff
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
     * @return Staff
     */
    public function setPerson(Person $person = null)
    {
        if (!empty($person))
            $person->setStaff($this);

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
     * @return Staff
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
     * @return Staff
     */
    public function setModifiedBy(User $modifiedBy = null)
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }

    /**
     * Get house
     *
     * @return string
     */
    public function getHouse()
    {
        return $this->house;
    }

    /**
     * Set house
     *
     * @param string $house
     *
     * @return Staff
     */
    public function setHouse($house)
    {
        $this->house = $house;

        return $this;
    }

    /**
     * Add department
     *
     * @param \Busybee\InstituteBundle\Entity\DepartmentStaff $department
     *
     * @return Staff
     */
    public function addDepartment(\Busybee\InstituteBundle\Entity\DepartmentStaff $department)
    {
        if ($this->department->contains($department))
            return $this;

        $department->setStaff($this);
        $this->department->add($department);

        return $this;
    }

    /**
     * Remove department
     *
     * @param \Busybee\InstituteBundle\Entity\DepartmentStaff $department
     */
    public function removeDepartment(\Busybee\InstituteBundle\Entity\DepartmentStaff $department)
    {
        $this->department->removeElement($department);
    }

    /**
     * Get department
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Get homeroom
     *
     * @return \Busybee\InstituteBundle\Entity\Space
     */
    public function getHomeroom()
    {
        return $this->homeroom;
    }

    /**
     * Set homeroom
     *
     * @param \Busybee\InstituteBundle\Entity\Space $homeroom
     *
     * @return Staff
     */
    public function setHomeroom(\Busybee\InstituteBundle\Entity\Space $homeroom = null)
    {
        $this->homeroom = $homeroom;

        return $this;
    }
}
