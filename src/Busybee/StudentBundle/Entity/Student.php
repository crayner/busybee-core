<?php
namespace Busybee\StudentBundle\Entity;

use Busybee\StudentBundle\Model\StudentModel;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Doctrine\ORM\PersistentCollection;

/**
 * Student
 */
class Student extends StudentModel
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $startAtSchool;

    /**
     * @var \DateTime
     */
    private $startAtThisSchool;

    /**
     * @var \DateTime
     */
    private $lastAtThisSchool;

    /**
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $firstLanguage;

    /**
     * @var string
     */
    private $secondLanguage;

    /**
     * @var string
     */
    private $thirdLanguage;

    /**
     * @var string
     */
    private $countryOfBirth;

    /**
     * @var string
     */
    private $birthCertificateScan;

    /**
     * @var string
     */
    private $ethnicity;

    /**
     * @var string
     */
    private $citizenship1;

    /**
     * @var string
     */
    private $citizenship1Passport;

    /**
     * @var string
     */
    private $citizenship1PassportScan;

    /**
     * @var string
     */
    private $citizenship2;

    /**
     * @var string
     */
    private $citizenship2Passport;

    /**
     * @var string
     */
    private $religion;

    /**
     * @var string
     */
    private $nationalIDCardNumber;

    /**
     * @var string
     */
    private $nationalIDCardScan;

    /**
     * @var string
     */
    private $residencyStatus;

    /**
     * @var \DateTime
     */
    private $visaExpiryDate;

    /**
     * @var string
     */
    private $house;

    /**
     * @var \DateTime
     */
    private $lastModified;

    /**
     * @var \DateTime
     */
    private $createdOn;

    /**
     * @var \Busybee\PersonBundle\Entity\Person
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
     * Student constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->enrolments = new ArrayCollection();
    }

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
     * Get startAtSchool
     *
     * @return \DateTime
     */
    public function getStartAtSchool()
    {
        return $this->startAtSchool;
    }

    /**
     * Set startAtSchool
     *
     * @param \DateTime $startAtSchool
     *
     * @return Student
     */
    public function setStartAtSchool($startAtSchool)
    {
        $this->startAtSchool = $startAtSchool;

        return $this;
    }

    /**
     * Get startAtThisSchool
     *
     * @return \DateTime
     */
    public function getStartAtThisSchool()
    {
        return $this->startAtThisSchool;
    }

    /**
     * Set startAtThisSchool
     *
     * @param \DateTime $startAtThisSchool
     *
     * @return Student
     */
    public function setStartAtThisSchool($startAtThisSchool)
    {
        $this->startAtThisSchool = $startAtThisSchool;

        return $this;
    }

    /**
     * Get lastAtThisSchool
     *
     * @return \DateTime
     */
    public function getLastAtThisSchool()
    {
        return $this->lastAtThisSchool;
    }

    /**
     * Set lastAtThisSchool
     *
     * @param \DateTime $lastAtThisSchool
     *
     * @return Student
     */
    public function setLastAtThisSchool($lastAtThisSchool)
    {
        $this->lastAtThisSchool = $lastAtThisSchool;

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
     * @return Student
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get firstLanguage
     *
     * @return string
     */
    public function getFirstLanguage()
    {
        return $this->firstLanguage;
    }

    /**
     * Set firstLanguage
     *
     * @param string $firstLanguage
     *
     * @return Student
     */
    public function setFirstLanguage($firstLanguage)
    {
        $this->firstLanguage = $firstLanguage;

        return $this;
    }

    /**
     * Get secondLanguage
     *
     * @return string
     */
    public function getSecondLanguage()
    {
        return $this->secondLanguage;
    }

    /**
     * Set secondLanguage
     *
     * @param string $secondLanguage
     *
     * @return Student
     */
    public function setSecondLanguage($secondLanguage)
    {
        $this->secondLanguage = $secondLanguage;

        return $this;
    }

    /**
     * Get thirdLanguage
     *
     * @return string
     */
    public function getThirdLanguage()
    {
        return $this->thirdLanguage;
    }

    /**
     * Set thirdLanguage
     *
     * @param string $thirdLanguage
     *
     * @return Student
     */
    public function setThirdLanguage($thirdLanguage)
    {
        $this->thirdLanguage = $thirdLanguage;

        return $this;
    }

    /**
     * Get countryOfBirth
     *
     * @return string
     */
    public function getCountryOfBirth()
    {
        return $this->countryOfBirth;
    }

    /**
     * Set countryOfBirth
     *
     * @param string $countryOfBirth
     *
     * @return Student
     */
    public function setCountryOfBirth($countryOfBirth)
    {
        $this->countryOfBirth = $countryOfBirth;

        return $this;
    }

    /**
     * Get birthCertificateScan
     *
     * @return string
     */
    public function getBirthCertificateScan()
    {
        return $this->birthCertificateScan;
    }

    /**
     * Set birthCertificateScan
     *
     * @param string $birthCertificateScan
     *
     * @return Student
     */
    public function setBirthCertificateScan($birthCertificateScan)
    {
        $this->birthCertificateScan = $birthCertificateScan;

        return $this;
    }

    /**
     * Get ethnicity
     *
     * @return string
     */
    public function getEthnicity()
    {
        return $this->ethnicity;
    }

    /**
     * Set ethnicity
     *
     * @param string $ethnicity
     *
     * @return Student
     */
    public function setEthnicity($ethnicity)
    {
        $this->ethnicity = $ethnicity;

        return $this;
    }

    /**
     * Get citizenship1
     *
     * @return string
     */
    public function getCitizenship1()
    {
        return $this->citizenship1;
    }

    /**
     * Set citizenship1
     *
     * @param string $citizenship1
     *
     * @return Student
     */
    public function setCitizenship1($citizenship1)
    {
        $this->citizenship1 = $citizenship1;

        return $this;
    }

    /**
     * Get citizenship1Passport
     *
     * @return string
     */
    public function getCitizenship1Passport()
    {
        return $this->citizenship1Passport;
    }

    /**
     * Set citizenship1Passport
     *
     * @param string $citizenship1Passport
     *
     * @return Student
     */
    public function setCitizenship1Passport($citizenship1Passport)
    {
        $this->citizenship1Passport = $citizenship1Passport;

        return $this;
    }

    /**
     * Get citizenship1PassportScan
     *
     * @return string
     */
    public function getCitizenship1PassportScan()
    {
        return $this->citizenship1PassportScan;
    }

    /**
     * Set citizenship1PassportScan
     *
     * @param string $citizenship1PassportScan
     *
     * @return Student
     */
    public function setCitizenship1PassportScan($citizenship1PassportScan)
    {
        $this->citizenship1PassportScan = $citizenship1PassportScan;

        return $this;
    }

    /**
     * Get citizenship2
     *
     * @return string
     */
    public function getCitizenship2()
    {
        return $this->citizenship2;
    }

    /**
     * Set citizenship2
     *
     * @param string $citizenship2
     *
     * @return Student
     */
    public function setCitizenship2($citizenship2)
    {
        $this->citizenship2 = $citizenship2;

        return $this;
    }

    /**
     * Get citizenship2Passport
     *
     * @return string
     */
    public function getCitizenship2Passport()
    {
        return $this->citizenship2Passport;
    }

    /**
     * Set citizenship2Passport
     *
     * @param string $citizenship2Passport
     *
     * @return Student
     */
    public function setCitizenship2Passport($citizenship2Passport)
    {
        $this->citizenship2Passport = $citizenship2Passport;

        return $this;
    }

    /**
     * Get religion
     *
     * @return string
     */
    public function getReligion()
    {
        return $this->religion;
    }

    /**
     * Set religion
     *
     * @param string $religion
     *
     * @return Student
     */
    public function setReligion($religion)
    {
        $this->religion = $religion;

        return $this;
    }

    /**
     * Get nationalIDCardNumber
     *
     * @return string
     */
    public function getNationalIDCardNumber()
    {
        return $this->nationalIDCardNumber;
    }

    /**
     * Set nationalIDCardNumber
     *
     * @param string $nationalIDCardNumber
     *
     * @return Student
     */
    public function setNationalIDCardNumber($nationalIDCardNumber)
    {
        $this->nationalIDCardNumber = $nationalIDCardNumber;

        return $this;
    }

    /**
     * Get nationalIDCardScan
     *
     * @return string
     */
    public function getNationalIDCardScan()
    {
        return $this->nationalIDCardScan;
    }

    /**
     * Set nationalIDCardScan
     *
     * @param string $nationalIDCardScan
     *
     * @return Student
     */
    public function setNationalIDCardScan($nationalIDCardScan)
    {
        $this->nationalIDCardScan = $nationalIDCardScan;

        return $this;
    }

    /**
     * Get residencyStatus
     *
     * @return string
     */
    public function getResidencyStatus()
    {
        return $this->residencyStatus;
    }

    /**
     * Set residencyStatus
     *
     * @param string $residencyStatus
     *
     * @return Student
     */
    public function setResidencyStatus($residencyStatus)
    {
        $this->residencyStatus = $residencyStatus;

        return $this;
    }

    /**
     * Get visaExpiryDate
     *
     * @return \DateTime
     */
    public function getVisaExpiryDate()
    {
        return $this->visaExpiryDate;
    }

    /**
     * Set visaExpiryDate
     *
     * @param \DateTime $visaExpiryDate
     *
     * @return Student
     */
    public function setVisaExpiryDate($visaExpiryDate)
    {
        $this->visaExpiryDate = $visaExpiryDate;

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
     * Set house
     *
     * @param string $house
     *
     * @return Student
     */
    public function setHouse($house)
    {
        $this->house = $house;

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
     * @return Student
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
     * @return Student
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get person
     *
     * @return \Busybee\PersonBundle\Entity\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set person
     *
     * @param \Busybee\PersonBundle\Entity\Person $person
     *
     * @return Student
     */
    public function setPerson(\Busybee\PersonBundle\Entity\Person $person = null)
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
     * @return Student
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
     * @return Student
     */
    public function setModifiedBy(\Busybee\SecurityBundle\Entity\User $modifiedBy = null)
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }
}
