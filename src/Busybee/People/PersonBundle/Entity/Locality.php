<?php

namespace Busybee\People\PersonBundle\Entity;

use Busybee\People\PersonBundle\Model\LocalityModel;

/**
 * Locality
 */
class Locality extends LocalityModel
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
	private $territory;

	/**
	 * @var string
	 */
	private $postCode;

	/**
	 * @var string
	 */
	private $country;

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
	 * Get id
	 *
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Set Id
	 *
	 * @param integer $id
	 *
	 * @return Locality
	 */
	public function setId($id)
	{
		$this->id = $id;

		return $this;
	}

	/**
	 * Get locality
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Set locality
	 *
	 * @param string $locality
	 *
	 * @return Locality
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * Get territory
	 *
	 * @return string
	 */
	public function getTerritory()
	{
		return $this->territory;
	}

	/**
	 * Set territory
	 *
	 * @param string $territory
	 *
	 * @return Locality
	 */
	public function setTerritory($territory)
	{
		$this->territory = $territory;

		return $this;
	}

	/**
	 * Get postCode
	 *
	 * @return string
	 */
	public function getPostCode()
	{
		return $this->postCode;
	}

	/**
	 * Set postCode
	 *
	 * @param string $postCode
	 *
	 * @return Locality
	 */
	public function setPostCode($postCode)
	{
		$this->postCode = $postCode;

		return $this;
	}

	/**
	 * Get country
	 *
	 * @return string
	 */
	public function getCountry()
	{
		return $this->country;
	}

	/**
	 * Set country
	 *
	 * @param string $country
	 *
	 * @return Locality
	 */
	public function setCountry($country)
	{
		$this->country = $country;

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
	 * @return Locality
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
	 * @return Locality
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
	 * @return Locality
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
	 * @return Locality
	 */
	public function setModifiedBy(\Busybee\SecurityBundle\Entity\User $modifiedBy = null)
	{
		$this->modifiedBy = $modifiedBy;

		return $this;
	}
}
