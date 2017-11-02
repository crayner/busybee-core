<?php

namespace Busybee\AVETMISS\AVETMISSBundle\Entity;

use Busybee\AVETMISS\AVETMISSBundle\Model\Subject as Base;

/**
 * Subject
 */
class Subject extends Base
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
	private $FOEIdentifier;

	/**
	 * @var string
	 */
	private $VETFlag;

	/**
	 * @var boolean
	 */
	private $subjectFlag;

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
	 * @var \Busybee\Program\CurriculumBundle\Entity\Subject
	 */
	private $subject;

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
	 * @return Subject
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
	 * @return Subject
	 */
	public function setNominalHours($nominalHours)
	{
		$this->nominalHours = $nominalHours;

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
	 * @return Subject
	 */
	public function setFOEIdentifier($FOEIdentifier)
	{
		$this->FOEIdentifier = $FOEIdentifier;

		return $this;
	}

	/**
	 * Get vETFlag
	 *
	 * @return string
	 */
	public function getVETFlag()
	{
		return $this->VETFlag;
	}

	/**
	 * Set vETFlag
	 *
	 * @param string $vETFlag
	 *
	 * @return Subject
	 */
	public function setVETFlag($vETFlag)
	{
		$this->VETFlag = $vETFlag;

		return $this;
	}

	/**
	 * Get subjectFlag
	 *
	 * @return boolean
	 */
	public function getSubjectFlag()
	{
		return $this->subjectFlag;
	}

	/**
	 * Set subjectFlag
	 *
	 * @param boolean $subjectFlag
	 *
	 * @return Subject
	 */
	public function setSubjectFlag($subjectFlag)
	{
		$this->subjectFlag = (bool) $subjectFlag;

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
	 * @return Subject
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
	 * @return Subject
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
	 * @return Subject
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
	 * @return Subject
	 */
	public function setModifiedBy(\Busybee\Core\SecurityBundle\Entity\User $modifiedBy = null)
	{
		$this->modifiedBy = $modifiedBy;

		return $this;
	}

	/**
	 * Get subject
	 *
	 * @return \Busybee\Program\CurriculumBundle\Entity\Subject
	 */
	public function getSubject()
	{
		return $this->subject;
	}

	/**
	 * Set subject
	 *
	 * @param \Busybee\Program\CurriculumBundle\Entity\Subject $subject
	 *
	 * @return Subject
	 */
	public function setSubject(\Busybee\Program\CurriculumBundle\Entity\Subject $subject = null)
	{
		$this->subject = $subject;

		return $this;
	}
}
