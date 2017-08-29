<?php

namespace Busybee\AVETMISS\AVETMISSBundle\Entity;

use Busybee\AVETMISS\AVETMISSBundle\Model\ClientModel;

/**
 * Client
 */
class Client extends ClientModel
{
	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var string
	 */
	private $clientID;

	/**
	 * @var string
	 */
	private $schoolAttainment;

	/**
	 * @var \DateTime
	 */
	private $lastModified;

	/**
	 * @var \DateTime
	 */
	private $createdOn;

	/**
	 * @var \Busybee\People\PersonBundle\Entity\Student
	 */
	private $student;

	/**
	 * @var \Busybee\Core\SecurityBundle\Entity\User
	 */
	private $createdBy;

	/**
	 * @var \Busybee\Core\SecurityBundle\Entity\User
	 */
	private $modifiedBy;
	/**
	 * @var string
	 */
	private $schoolAttainmentYear;
	/**
	 * @var string
	 */
	private $indigenous;
	/**
	 * @var string
	 */
	private $language;
	/**
	 * @var string
	 */
	private $labourForce;
	/**
	 * @var string
	 */
	private $countryBorn;
	/**
	 * @var string
	 */
	private $disability;
	/**
	 * @var string
	 */
	private $priorEducation;
	/**
	 * @var boolean
	 */
	private $atSchool;
	/**
	 * @var string
	 */
	private $englishProficiency;
	/**
	 * @var string
	 */
	private $usi;
	/**
	 * @var string
	 */
	private $sal1;
	/**
	 * @var string
	 */
	private $sal2;

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
	 * Get clientID
	 *
	 * @return string
	 */
	public function getClientID()
	{
		return $this->clientID;
	}

	/**
	 * Set clientID
	 *
	 * @param string $clientID
	 *
	 * @return Client
	 */
	public function setClientID($clientID)
	{
		$this->clientID = $clientID;

		return $this;
	}

	/**
	 * Get schoolAttainment
	 *
	 * @return string
	 */
	public function getSchoolAttainment()
	{
		return $this->schoolAttainment;
	}

	/**
	 * Set schoolAttainment
	 *
	 * @param string $schoolAttainment
	 *
	 * @return Client
	 */
	public function setSchoolAttainment($schoolAttainment)
	{
		$this->schoolAttainment = $schoolAttainment;

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
	 * @return Client
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
	 * @return Client
	 */
	public function setCreatedOn($createdOn)
	{
		$this->createdOn = $createdOn;

		return $this;
	}

	/**
	 * Get student
	 *
	 * @return \Busybee\People\PersonBundle\Entity\Student
	 */
	public function getStudent()
	{
		return $this->student;
	}

	/**
	 * Set student
	 *
	 * @param \Busybee\People\PersonBundle\Entity\Student $student
	 *
	 * @return Client
	 */
	public function setStudent(\Busybee\People\PersonBundle\Entity\Student $student = null)
	{
		$this->student = $student;

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
	 * @return Client
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
	 * @return Client
	 */
	public function setModifiedBy(\Busybee\Core\SecurityBundle\Entity\User $modifiedBy = null)
	{
		$this->modifiedBy = $modifiedBy;

		return $this;
	}

	/**
	 * Get schoolAttainmentYear
	 *
	 * @return string
	 */
	public function getSchoolAttainmentYear()
	{
		return $this->schoolAttainmentYear;
	}

	/**
	 * Set schoolAttainmentYear
	 *
	 * @param string $schoolAttainmentYear
	 *
	 * @return Client
	 */
	public function setSchoolAttainmentYear($schoolAttainmentYear)
	{
		$this->schoolAttainmentYear = $schoolAttainmentYear;

		return $this;
	}

	/**
	 * Get indigenous
	 *
	 * @return string
	 */
	public function getIndigenous()
	{
		return $this->indigenous;
	}

	/**
	 * Set indigenous
	 *
	 * @param string $indigenous
	 *
	 * @return Client
	 */
	public function setIndigenous($indigenous)
	{
		$this->indigenous = $indigenous;

		return $this;
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
	 * @return Client
	 */
	public function setLanguage($language)
	{
		$this->language = $language;

		return $this;
	}

	/**
	 * Get labourForce
	 *
	 * @return string
	 */
	public function getLabourForce()
	{
		return $this->labourForce;
	}

	/**
	 * Set labourForce
	 *
	 * @param string $labourForce
	 *
	 * @return Client
	 */
	public function setLabourForce($labourForce)
	{
		$this->labourForce = $labourForce;

		return $this;
	}

	/**
	 * Get countryBorn
	 *
	 * @return string
	 */
	public function getCountryBorn()
	{
		return $this->countryBorn;
	}

	/**
	 * Set countryBorn
	 *
	 * @param string $countryBorn
	 *
	 * @return Client
	 */
	public function setCountryBorn($countryBorn)
	{
		$this->countryBorn = $countryBorn;

		return $this;
	}

	/**
	 * Get disability
	 *
	 * @return string
	 */
	public function getDisability()
	{
		return $this->disability;
	}

	/**
	 * Set disability
	 *
	 * @param string $disability
	 *
	 * @return Client
	 */
	public function setDisability($disability)
	{
		$this->disability = $disability;

		return $this;
	}

	/**
	 * Get priorEducation
	 *
	 * @return string
	 */
	public function getPriorEducation()
	{
		return $this->priorEducation;
	}

	/**
	 * Set priorEducation
	 *
	 * @param string $priorEducation
	 *
	 * @return Client
	 */
	public function setPriorEducation($priorEducation)
	{
		$this->priorEducation = $priorEducation;

		return $this;
	}

	/**
	 * Get atSchool
	 *
	 * @return boolean
	 */
	public function getAtSchool()
	{
		return $this->atSchool;
	}

	/**
	 * Set atSchool
	 *
	 * @param boolean $atSchool
	 *
	 * @return Client
	 */
	public function setAtSchool($atSchool)
	{
		$this->atSchool = (bool) $atSchool;

		return $this;
	}

	/**
	 * Get englishProficiency
	 *
	 * @return string
	 */
	public function getEnglishProficiency()
	{
		return $this->englishProficiency;
	}

	/**
	 * Set englishProficiency
	 *
	 * @param string $englishProficiency
	 *
	 * @return Client
	 */
	public function setEnglishProficiency($englishProficiency)
	{
		$this->englishProficiency = $englishProficiency;

		return $this;
	}

	/**
	 * Get usi
	 *
	 * @return string
	 */
	public function getUsi()
	{
		return $this->usi;
	}

	/**
	 * Set usi
	 *
	 * @param string $usi
	 *
	 * @return Client
	 */
	public function setUsi($usi)
	{
		$this->usi = $usi;

		return $this;
	}

	/**
	 * Get sal1
	 *
	 * @return string
	 */
	public function getSal1()
	{
		return $this->sal1;
	}

	/**
	 * Set sal1
	 *
	 * @param string $sal1
	 *
	 * @return Client
	 */
	public function setSal1($sal1)
	{
		$this->sal1 = $sal1;

		return $this;
	}

	/**
	 * Get sal2
	 *
	 * @return string
	 */
	public function getSal2()
	{
		return $this->sal2;
	}

	/**
	 * Set sal2
	 *
	 * @param string $sal2
	 *
	 * @return Client
	 */
	public function setSal2($sal2)
	{
		$this->sal2 = $sal2;

		return $this;
	}
}
