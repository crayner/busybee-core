<?php

namespace Busybee\AVETMISS\AVETMISSBundle\Entity;

use Busybee\AVETMISS\AVETMISSBundle\Model\Course as Base;

/**
 * Program
 */
class Course extends Base
{
	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var string
	 */
	private $identifier;

	/**
	 * @var string
	 */
	private $nominalHours;

	/**
	 * @var string
	 */
	private $recognitionIdentifier;

	/**
	 * @var string
	 */
	private $levelEducationIdentifier;

	/**
	 * @var string
	 */
	private $FOEIdentifier;

	/**
	 * @var string
	 */
	private $ANZSCOIdentifier;

	/**
	 * @var boolean
	 */
	private $VETFlag;

	/**
	 * @var \DateTime
	 */
	private $lastModified;

	/**
	 * @var \DateTime
	 */
	private $createdOn;

	/**
	 * @var \Busybee\CurriculumBundle\Entity\Course
	 */
	private $course;

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
	 * Get identifier
	 *
	 * @return string
	 */
	public function getIdentifier()
	{
		return $this->identifier;
	}

	/**
	 * Set identifier
	 *
	 * @param string $identifier
	 *
	 * @return Course
	 */
	public function setIdentifier($identifier)
	{
		$this->identifier = $identifier;

		return $this;
	}

	/**
	 * Get nominalHours
	 *
	 * @return string
	 */
	public function getNominalHours()
	{
		return $this->nominalHours;
	}

	/**
	 * Set nominalHours
	 *
	 * @param string $nominalHours
	 *
	 * @return Course
	 */
	public function setNominalHours($nominalHours)
	{
		$this->nominalHours = $nominalHours;

		return $this;
	}

	/**
	 * Get recognitionIdentifier
	 *
	 * @return string
	 */
	public function getRecognitionIdentifier()
	{
		return $this->recognitionIdentifier;
	}

	/**
	 * Set recognitionIdentifier
	 *
	 * @param string $recognitionIdentifier
	 *
	 * @return Course
	 */
	public function setRecognitionIdentifier($recognitionIdentifier)
	{
		$this->recognitionIdentifier = $recognitionIdentifier;

		return $this;
	}

	/**
	 * Get levelEducationIdentifier
	 *
	 * @return string
	 */
	public function getLevelEducationIdentifier()
	{
		return $this->levelEducationIdentifier;
	}

	/**
	 * Set levelEducationIdentifier
	 *
	 * @param string $levelEducationIdentifier
	 *
	 * @return Course
	 */
	public function setLevelEducationIdentifier($levelEducationIdentifier)
	{
		$this->levelEducationIdentifier = $levelEducationIdentifier;

		return $this;
	}

	/**
	 * Get fOEIdentifier
	 *
	 * @return string
	 */
	public function getFOEIdentifier()
	{
		return $this->FOEIdentifier;
	}

	/**
	 * Set fOEIdentifier
	 *
	 * @param string $fOEIdentifier
	 *
	 * @return Course
	 */
	public function setFOEIdentifier($FOEIdentifier)
	{
		$this->FOEIdentifier = $FOEIdentifier;

		return $this;
	}

	/**
	 * Get aNZSCOIdentifier
	 *
	 * @return string
	 */
	public function getANZSCOIdentifier()
	{
		return $this->ANZSCOIdentifier;
	}

	/**
	 * Set aNZSCOIdentifier
	 *
	 * @param string $aNZSCOIdentifier
	 *
	 * @return Course
	 */
	public function setANZSCOIdentifier($ANZSCOIdentifier)
	{
		$this->ANZSCOIdentifier = $ANZSCOIdentifier;

		return $this;
	}

	/**
	 * Get vETFlag
	 *
	 * @return boolean
	 */
	public function getVETFlag()
	{
		return $this->VETFlag;
	}

	/**
	 * Set vETFlag
	 *
	 * @param boolean $vETFlag
	 *
	 * @return Course
	 */
	public function setVETFlag($VETFlag)
	{
		$this->VETFlag = (bool) $VETFlag;

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
	 * @return Course
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
	 * @return Course
	 */
	public function setCreatedOn($createdOn)
	{
		$this->createdOn = $createdOn;

		return $this;
	}

	/**
	 * Get course
	 *
	 * @return \Busybee\CurriculumBundle\Entity\Course
	 */
	public function getCourse()
	{
		return $this->course;
	}

	/**
	 * Set course
	 *
	 * @param \Busybee\CurriculumBundle\Entity\Course $course
	 *
	 * @return Course
	 */
	public function setCourse(\Busybee\CurriculumBundle\Entity\Course $course = null)
	{
		$this->course = $course;

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
	 * @return Course
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
	 * @return Course
	 */
	public function setModifiedBy(\Busybee\SecurityBundle\Entity\User $modifiedBy = null)
	{
		$this->modifiedBy = $modifiedBy;

		return $this;
	}
}
