<?php

namespace Busybee\People\PersonBundle\Entity;

/**
 * PersonPreference
 */
class PersonPreference
{
	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var string
	 */
	private $language;

	/**
	 * @var \DateTime
	 */
	private $lastModified;

	/**
	 * @var \DateTime
	 */
	private $createdOn;

	/**
	 * @var \Busybee\People\PersonBundle\Entity\Person
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
	 * Get language
	 *
	 * @return string
	 */
	public function getLanguage()
	{
		return $this->language;
	}

	/**
	 * Set language
	 *
	 * @param string $language
	 *
	 * @return PersonPreference
	 */
	public function setLanguage($language)
	{
		$this->language = $language;

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
	 * @return PersonPreference
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
	 * @return PersonPreference
	 */
	public function setCreatedOn($createdOn)
	{
		$this->createdOn = $createdOn;

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
	 * @return PersonPreference
	 */
	public function setPerson(\Busybee\People\PersonBundle\Entity\Person $person = null)
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
	 * @return PersonPreference
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
	 * @return PersonPreference
	 */
	public function setModifiedBy(\Busybee\SecurityBundle\Entity\User $modifiedBy = null)
	{
		$this->modifiedBy = $modifiedBy;

		return $this;
	}
}

