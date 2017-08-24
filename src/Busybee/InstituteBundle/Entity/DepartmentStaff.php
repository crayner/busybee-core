<?php

namespace Busybee\InstituteBundle\Entity;

/**
 * DepartmentStaff
 */
class DepartmentStaff
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
     * @var \DateTime
     */
    private $lastModified;

    /**
     * @var \DateTime
     */
    private $createdOn;

    /**
     * @var \Busybee\InstituteBundle\Entity\Department
     */
    private $department;

    /**
     * @var \Busybee\People\StaffBundle\Entity\Staff
     */
    private $staff;

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
     * @return DepartmentStaff
     */
    public function setStaffType($staffType)
    {
        $this->staffType = $staffType;

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
     * @return DepartmentStaff
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
     * @return DepartmentStaff
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
     * @return DepartmentStaff
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
     * @return DepartmentStaff
     */
    public function setModifiedBy(\Busybee\SecurityBundle\Entity\User $modifiedBy = null)
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }

    public function __toString()
    {
        return $this->getStaff()->formatName() . ' in department ' . $this->getDepartment()->getName();
    }

    /**
     * Get staff
     *
     * @return \Busybee\People\StaffBundle\Entity\Staff
     */
    public function getStaff()
    {
        return $this->staff;
    }

    /**
     * Set staff
     *
     * @param \Busybee\People\StaffBundle\Entity\Staff $staff
     *
     * @return DepartmentStaff
     */
	public function setStaff(\Busybee\People\StaffBundle\Entity\Staff $staff = null)
    {
        $this->staff = $staff;

        return $this;
    }

    /**
     * Get department
     *
     * @return \Busybee\InstituteBundle\Entity\Department
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Set department
     *
     * @param \Busybee\InstituteBundle\Entity\Department $department
     *
     * @return DepartmentStaff
     */
    public function setDepartment(\Busybee\InstituteBundle\Entity\Department $department = null)
    {
        $this->department = $department;

        return $this;
    }
}
