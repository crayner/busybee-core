<?php

namespace Busybee\People\StaffBundle\Entity;

use Busybee\People\StaffBundle\Model\StaffModel;

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
}
