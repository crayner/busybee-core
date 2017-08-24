<?php

namespace Busybee\People\PersonBundle\Entity;

/**
 * PersonExtra
 */
class PersonExtra
{
	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var string
	 */
	private $vehicleRegistration;

	/**
	 * @var \DateTime
	 */
	private $lastModified;

	/**
	 * @var \DateTime
	 */
	private $createdOn;

	/**
	 * @var \Busybee\Core\SecurityBundle\Entity\User
	 */
	private $createdBy;

	/**
	 * @var \Busybee\Core\SecurityBundle\Entity\User
	 */
	private $modifiedBy;
	/**
	 * @var \Busybee\People\PersonBundle\Entity\Person
	 */
	private $person;

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
	 * Get vehicleRegistration
	 *
	 * @return string
	 */
	public function getVehicleRegistration()
	{
		return $this->vehicleRegistration;
	}

	/**
	 * Set vehicleRegistration
	 *
	 * @param string $vehicleRegistration
	 *
	 * @return PersonExtra
	 */
	public function setVehicleRegistration($vehicleRegistration)
	{
		$this->vehicleRegistration = $vehicleRegistration;

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
	 * @return PersonExtra
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
	 * @return PersonExtra
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
	 * @return PersonExtra
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
	 * @return PersonExtra
	 */
	public function setModifiedBy(\Busybee\Core\SecurityBundle\Entity\User $modifiedBy = null)
	{
		$this->modifiedBy = $modifiedBy;

		return $this;
	}

	/**
	 * Get person
	 *
	 * @return \Busybee\People\PersonBundle\Entity\Person
	 */
	public function getPerson()
	{
		return $this->person;
	}

	/**
	 * Set person
	 *
	 * @param \Busybee\People\PersonBundle\Entity\Person $person
	 *
	 * @return PersonExtra
	 */
	public function setPerson(\Busybee\People\PersonBundle\Entity\Person $person = null)
	{
		$this->person = $person;

		return $this;
	}
}
