<?php

namespace Busybee\People\StaffBundle\Entity;

use Busybee\Facility\InstituteBundle\Entity\Department;
use Busybee\Facility\InstituteBundle\Entity\DepartmentStaff;
use Busybee\Facility\InstituteBundle\Entity\Space;
use Busybee\People\StaffBundle\Model\StaffModel;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Staff
 */
class Staff extends StaffModel
{
	/**
	 * @var string
	 */
	private $staffType;

	/**
	 * @var string
	 */
	private $jobTitle;

	/**
	 * @var string
	 */
	private $house;

	/**
	 * @var ArrayCollection
	 */
	private $departments;

	/**
	 * @var ArrayCollection
	 */
	private $spaces;

	/**
	 * @var string
	 */
	private $status;

	/**
	 * @var string
	 */
	private $profession;

	/**
	 * @var string
	 */
	private $employer;

	/**
	 * Staff constructor.
	 */
	public function __construct()
	{
		$this->departments = new ArrayCollection();
		$this->spaces      = new ArrayCollection();
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
	 * Get staffType
	 *
	 * @return string
	 */
	public function getStaffType()
	{
		if (empty($this->staffType))
			$this->setStaffType('Unknown');

		return $this->staffType;
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
	 * Get jobTitle
	 *
	 * @return string
	 */
	public function getJobTitle()
	{
		if (empty($this->jobTitle))
			$this->setJobTitle('Not Specified');

		return $this->jobTitle;
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
	 * Get house
	 *
	 * @return string
	 */
	public function getHouse()
	{
		return $this->house;
	}

	/**
	 * @return ArrayCollection
	 */
	public function getDepartments(): ArrayCollection
	{
		return $this->departments;
	}

	/**
	 * @param ArrayCollection $departments
	 */
	public function setDepartments(DepartmentStaff $departments): Staff
	{
		$this->departments = $departments;

		return $this;
	}

	/**
	 * @return ArrayCollection
	 */
	public function getSpaces(): ArrayCollection
	{
		return $this->spaces;
	}

	/**
	 * @param ArrayCollection $spaces
	 */
	public function setSpaces(ArrayCollection $spaces): Staff
	{
		$this->spaces = $spaces;

		return $this;
	}

	/**
	 * Add Space
	 *
	 * @param Department $department
	 *
	 * @return Staff
	 */
	public function addSpace(Space $space): Staff
	{
		if (!$this->spaces->contains($space))
			$this->spaces->add($space);

		return $this;
	}

	/**
	 * Remove Space
	 *
	 * @param Department $department
	 *
	 * @return Staff
	 */
	public function removeSpace(Space $space): Staff
	{
		$this->spaces->removeElement($space);

		return $this;
	}

	/**
	 * @return string
	 */
	public function getStatus(): string
	{
		return $this->status;
	}

	/**
	 * @param string $status
	 */
	public function setStatus(string $status): Staff
	{
		$this->status = $status;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getProfession(): string
	{
		return $this->profession;
	}

	/**
	 * @param string $profession
	 */
	public function setProfession(string $profession): Staff
	{
		$this->profession = $profession;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getEmployer(): string
	{
		return $this->employer;
	}

	/**
	 * @param string $employer
	 */
	public function setEmployer(string $employer): Staff
	{
		$this->employer = $employer;

		return $this;
	}
}
