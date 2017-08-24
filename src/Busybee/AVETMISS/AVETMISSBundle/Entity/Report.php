<?php

namespace Busybee\AVETMISS\AVETMISSBundle\Entity;

/**
 * Report
 */
class Report
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
	private $year;

	/**
	 * @var string
	 */
	private $status;

	/**
	 * @var string
	 */
	private $filePath;

	/**
	 * @var integer
	 */
	private $fileLength;

	/**
	 * @var \stdClass
	 */
	private $errors;

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
	 * Get name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Set name
	 *
	 * @param string $name
	 *
	 * @return Report
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * Get year
	 *
	 * @return string
	 */
	public function getYear()
	{
		return $this->year;
	}

	/**
	 * Set year
	 *
	 * @param string $year
	 *
	 * @return Report
	 */
	public function setYear($year)
	{
		$this->year = $year;

		return $this;
	}

	/**
	 * Get status
	 *
	 * @return string
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * Set status
	 *
	 * @param string $status
	 *
	 * @return Report
	 */
	public function setStatus($status)
	{
		$this->status = $status;

		return $this;
	}

	/**
	 * Get filePath
	 *
	 * @return string
	 */
	public function getFilePath()
	{
		return $this->filePath;
	}

	/**
	 * Set filePath
	 *
	 * @param string $filePath
	 *
	 * @return Report
	 */
	public function setFilePath($filePath)
	{
		$this->filePath = $filePath;

		return $this;
	}

	/**
	 * Get fileLength
	 *
	 * @return integer
	 */
	public function getFileLength()
	{
		return $this->fileLength;
	}

	/**
	 * Set fileLength
	 *
	 * @param integer $fileLength
	 *
	 * @return Report
	 */
	public function setFileLength($fileLength)
	{
		$this->fileLength = $fileLength;

		return $this;
	}

	/**
	 * Get errors
	 *
	 * @return \stdClass
	 */
	public function getErrors()
	{
		return $this->errors;
	}

	/**
	 * Set errors
	 *
	 * @param \stdClass $errors
	 *
	 * @return Report
	 */
	public function setErrors($errors)
	{
		$this->errors = $errors;

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
	 * @return Report
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
	 * @return Report
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
	 * @return Report
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
	 * @return Report
	 */
	public function setModifiedBy(\Busybee\SecurityBundle\Entity\User $modifiedBy = null)
	{
		$this->modifiedBy = $modifiedBy;

		return $this;
	}
}

