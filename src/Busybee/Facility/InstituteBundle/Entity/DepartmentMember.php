<?php
namespace Busybee\Facility\InstituteBundle\Entity;

use Busybee\People\StaffBundle\Entity\Staff;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * DepartmentMember
 */
class DepartmentMember
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
	 * @var int
	 */
	private $department;

	/**
	 * @var ArrayCollection
	 */
	private $staff;

	/**
	 * @var \Busybee\Core\SecurityBundle\Entity\User
	 */
	private $createdBy;

	/**
	 * @var \Busybee\Core\SecurityBundle\Entity\User
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
	 * @return DepartmentMember
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
	 * @return DepartmentMember
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
	 * @return DepartmentMember
	 */
	public function setCreatedOn($createdOn)
	{
		$this->createdOn = $createdOn;

		return $this;
	}

	/**
	 * Get createdBy
	 *
	 * @return \Busybee\Core\SecurityBundle\Entity\User
	 */
	public function getCreatedBy()
	{
		return $this->createdBy;
	}

	/**
	 * Set createdBy
	 *
	 * @param \Busybee\Core\SecurityBundle\Entity\User $createdBy
	 *
	 * @return DepartmentMember
	 */
	public function setCreatedBy(\Busybee\Core\SecurityBundle\Entity\User $createdBy = null)
	{
		$this->createdBy = $createdBy;

		return $this;
	}

	/**
	 * Get modifiedBy
	 *
	 * @return \Busybee\Core\SecurityBundle\Entity\User
	 */
	public function getModifiedBy()
	{
		return $this->modifiedBy;
	}

	/**
	 * Set modifiedBy
	 *
	 * @param \Busybee\Core\SecurityBundle\Entity\User $modifiedBy
	 *
	 * @return DepartmentMember
	 */
	public function setModifiedBy(\Busybee\Core\SecurityBundle\Entity\User $modifiedBy = null)
	{
		$this->modifiedBy = $modifiedBy;

		return $this;
	}

	public function __toString()
	{
		if (is_null($this->getDepartment()))
			return $this->getStaff()->formatName();

		return $this->getStaff()->formatName() . ' in department ' . $this->getDepartment()->getName();
	}

	/**
	 * Get staff
	 *
	 * @return \Busybee\People\StaffBundle\Entity\Staff|null
	 */
	public function getStaff()
	{
		return $this->staff;
	}

	/**
	 * Set staff
	 *
	 * @param Staff $staff
	 *
	 * @return DepartmentMember
	 */
	public function setStaff(Staff $staff)
	{
		$this->staff = $staff;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getDepartment(): ?int
	{
		return $this->department;
	}

	/**
	 * @param ArrayCollection $department
	 *
	 * @return DepartmentMember
	 */
	public function setDepartment(int $department = null): DepartmentMember
	{
		$this->department = $department;

		return $this;
	}
}
