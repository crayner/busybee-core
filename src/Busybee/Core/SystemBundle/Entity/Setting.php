<?php

namespace Busybee\Core\SystemBundle\Entity;

use Busybee\Core\SystemBundle\Model\SettingModel;

/**
 * Setting
 */
class Setting extends SettingModel
{
	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var string
	 */
	private $type;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var blob
	 */
	private $value;

	/**
	 * @var \DateTime
	 */
	private $lastModified;

	/**
	 * @var \DateTime
	 */
	private $createdOn;

	/**
	 * @var boolean
	 */
	private $securityActive;

	/**
	 * @var string
	 */
	private $choice;
	/**
	 * @var string
	 */
	private $role;
	/**
	 * @var \Busybee\SecurityBundle\Entity\User
	 */
	private $createdBy;
	/**
	 * @var \Busybee\SecurityBundle\Entity\User
	 */
	private $modifiedBy;
	/**
	 * @var string
	 */
	private $description;
	/**
	 * @var string
	 */
	private $displayName;
	/**
	 * @var string
	 */
	private $validator;

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
	 * Get type
	 *
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * Set type
	 *
	 * @param string $type
	 *
	 * @return Setting
	 */
	public function setType($type)
	{
		$this->type = $type;

		return $this;
	}

	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getName()
	{
		return strtolower($this->name);
	}

	/**
	 * Set name
	 *
	 * @param string $name
	 *
	 * @return Setting
	 */
	public function setName($name)
	{
		$this->name = strtolower($name);

		return $this;
	}

	/**
	 * Get value
	 *
	 * @return \blog
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * Set value
	 *
	 * @param blob $value
	 *
	 * @return blob
	 */
	public function setValue($value)
	{
		$this->value = $value;

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
	 * @return Setting
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
	 * @return Setting
	 */
	public function setCreatedOn($createdOn)
	{
		$this->createdOn = $createdOn;

		return $this;
	}

	/**
	 * Get role
	 *
	 * @return string
	 */
	public function getRole()
	{
		return $this->role;
	}

	/**
	 * Set role
	 *
	 * @param string $role
	 *
	 * @return Setting
	 */
	public function setRole($role)
	{
		$this->role = $role;

		return $this;
	}

	/**
	 * Get role
	 *
	 * @return boolean
	 */
	public function getSecurityActive()
	{
		return $this->securityActive;
	}

	/**
	 * Set role
	 *
	 * @param \Busybee\SecurityBundle\Entity\Role $role
	 *
	 * @return Setting
	 */
	public function setSecurityActive($sa = true)
	{
		$this->securityActive = $sa;

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
	 * @return Setting
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
	 * @return Setting
	 */
	public function setModifiedBy(\Busybee\SecurityBundle\Entity\User $modifiedBy = null)
	{
		$this->modifiedBy = $modifiedBy;

		return $this;
	}

	/**
	 * Get description
	 *
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * Set description
	 *
	 * @param string $description
	 *
	 * @return Setting
	 */
	public function setDescription($description)
	{
		$this->description = $description;

		return $this;
	}

	/**
	 * Get displayName
	 *
	 * @return string
	 */
	public function getDisplayName()
	{
		return $this->displayName;
	}

	/**
	 * Set displayName
	 *
	 * @param string $displayName
	 *
	 * @return Setting
	 */
	public function setDisplayName($displayName)
	{
		$this->displayName = $displayName;

		return $this;
	}

	/**
	 * Get choice
	 *
	 * @return string
	 */
	public function getChoice()
	{
		return $this->choice;
	}

	/**
	 * Set choice
	 *
	 * @param string $choice
	 *
	 * @return Setting
	 */
	public function setChoice($choice)
	{
		$this->choice = $choice;

		return $this;
	}

	/**
	 * Get validator
	 *
	 * @return string
	 */
	public function getValidator()
	{
		return $this->validator;
	}

	/**
	 * Set validator
	 *
	 * @param string $validator
	 *
	 * @return Setting
	 */
	public function setValidator($validator)
	{
		$this->validator = $validator;

		return $this;
	}
}
