<?php

namespace Busybee\PersonBundle\Entity;

use Busybee\PersonBundle\Model\PersonModel ;
use Busybee\SecurityBundle\Entity\User;
use Busybee\StaffBundle\Entity\Staff;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Person
 */
class Person extends PersonModel
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $surname;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $preferredName;

    /**
     * @var string
     */
    private $officialName;

    /**
     * @var string
     */
    private $gender;

    /**
     * @var \DateTime
     */
    private $dob;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $email2;

    /**
     * @var string
     */
    private $website;

    /**
     * @var string
     */
    private $photo;

    /**
     * @var \DateTime
     */
    private $lastModified;

    /**
     * @var \DateTime
     */
    private $createdOn;

    /**
     * @var User
     */
    private $user;

    /**
     * @var  User
     */
    private $createdBy;

    /**
     * @var User
     */
    private $modifiedBy;

    /**
     * @var \Busybee\PersonBundle\Entity\Address
     */
    private $address1;

    /**
     * @var \Busybee\PersonBundle\Entity\Address
     */
    private $address2;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $phone;
    /**
     * @var string
     */
    private $identifier;

    /**
     * @var boolean
     */
    private $staffQuestion;

    /**
     * @var boolean
     */
    private $studentQuestion;

    /**
     * @var Staff
     */
    private $staff;

    /**
     * @var \Busybee\StudentBundle\Entity\Student
     */
    private $student;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $careGiver;

    /**
     * @var string
     */
    private $importIdentifier;

    /**
     * @var \Busybee\PersonBundle\Entity\PersonExtra
     */
    private $extra;

    /**
     * @var PersonPreference
     */
    private $preference;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->phone = new ArrayCollection();
        parent::__construct();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return is_null($this->id) ? 0 : $this->id;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Person
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set surname
     *
     * @param string $surname
     *
     * @return Person
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Person
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get preferredName
     *
     * @return string
     */
    public function getPreferredName()
    {
        return $this->preferredName;
    }

    /**
     * Set preferredName
     *
     * @param string $preferredName
     *
     * @return Person
     */
    public function setPreferredName($preferredName)
    {
        $this->preferredName = $preferredName;

        return $this;
    }

    /**
     * Get officialName
     *
     * @return string
     */
    public function getOfficialName()
    {
        return $this->officialName;
    }

    /**
     * Set officialName
     *
     * @param string $officialName
     *
     * @return Person
     */
    public function setOfficialName($officialName)
    {
        $this->officialName = $officialName;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set gender
     *
     * @param string $gender
     *
     * @return Person
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get dob
     *
     * @return \DateTime
     */
    public function getDob()
    {
        return $this->dob;
    }

    /**
     * Set dob
     *
     * @param \DateTime $dob
     *
     * @return Person
     */
    public function setDob($dob)
    {
        $this->dob = $dob;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Person
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email2
     *
     * @return string
     */
    public function getEmail2()
    {
        return $this->email2;
    }

    /**
     * Set email2
     *
     * @param string $email2
     *
     * @return Person
     */
    public function setEmail2($email2)
    {
        $this->email2 = $email2;

        return $this;
    }

    /**
     * Get website
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set website
     *
     * @param string $website
     *
     * @return Person
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get photo
     *
     * @return string
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Set photo
     *
     * @param string $photo
     *
     * @return Person
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;

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
     * @return Person
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
     * @return Person
     */
    public function setCreatedOn(\DateTime $createdOn = null)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return Person
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set createdBy
     *
     * @param User $createdBy
     *
     * @return Person
     */
    public function setCreatedBy(User $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get modifiedBy
     *
     * @return User
     */
    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    /**
     * Set modifiedBy
     *
     * @param User $modifiedBy
     *
     * @return Person
     */
    public function setModifiedBy(User $modifiedBy = null)
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }

    /**
     * Get address1
     *
     * @return \Busybee\PersonBundle\Entity\Address
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * Set address1
     *
     * @param \Busybee\PersonBundle\Entity\Address $address1
     *
     * @return Person
     */
    public function setAddress1(\Busybee\PersonBundle\Entity\Address $address1 = null)
    {
        $this->address1 = $address1;

        return $this;
    }

    /**
     * Get address2
     *
     * @return \Busybee\PersonBundle\Entity\Address
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * Set address2
     *
     * @param \Busybee\PersonBundle\Entity\Address $address2
     *
     * @return Person
     */
    public function setAddress2(\Busybee\PersonBundle\Entity\Address $address2 = null)
    {
        $this->address2 = $address2;

        return $this;
    }

    /**
     * Add phone
     *
     * @param \Busybee\PersonBundle\Entity\Phone $phone
     *
     * @return Person
     */
    public function addPhone(\Busybee\PersonBundle\Entity\Phone $phone)
    {
        $this->phone[] = $phone;

        return $this;
    }

    /**
     * Remove phone
     *
     * @param \Busybee\PersonBundle\Entity\Phone $phone
     */
    public function removePhone(\Busybee\PersonBundle\Entity\Phone $phone)
    {
        $this->phone->removeElement($phone);
    }

    /**
     * Get phone
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPhone()
    {
        return $this->phone;
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
     * @return Person
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Get staffQuestion
     *
     * @return boolean
     */
    public function getStaffQuestion()
    {
        return $this->staffQuestion;
    }

    /**
     * Set staffQuestion
     *
     * @param boolean $staffQuestion
     *
     * @return Person
     */
    public function setStaffQuestion($staffQuestion)
    {
        $this->staffQuestion = $staffQuestion;

        return $this;
    }

    /**
     * Get studentQuestion
     *
     * @return boolean
     */
    public function getStudentQuestion()
    {
        return $this->studentQuestion;
    }

    /**
     * Set studentQuestion
     *
     * @param boolean $studentQuestion
     *
     * @return Person
     */
    public function setStudentQuestion($studentQuestion)
    {
        $this->studentQuestion = $studentQuestion;

        return $this;
    }

    /**
     * Get staff
     *
     * @return Staff
     */
    public function getStaff()
    {
        return $this->staff;
    }

    /**
     * Set staff
     *
     * @param Staff $staff
     *
     * @return Person
     */
    public function setStaff(Staff $staff = null)
    {
        $this->staff = $staff;

        return $this;
    }

    /**
     * Get student
     *
     * @return \Busybee\StudentBundle\Entity\Student
     */
    public function getStudent()
    {
        return $this->student;
    }

    /**
     * Set student
     *
     * @param \Busybee\StudentBundle\Entity\Student $student
     *
     * @return Person
     */
    public function setStudent(\Busybee\StudentBundle\Entity\Student $student = null)
    {
        $this->student = $student;

        return $this;
    }

    /**
     * Add careGiver
     *
     * @param \Busybee\FamilyBundle\Entity\CareGiver $careGiver
     *
     * @return Person
     */
    public function addCareGiver(\Busybee\FamilyBundle\Entity\CareGiver $careGiver)
    {
        $this->careGiver[] = $careGiver;

        return $this;
    }

    /**
     * Remove careGiver
     *
     * @param \Busybee\FamilyBundle\Entity\CareGiver $careGiver
     */
    public function removeCareGiver(\Busybee\FamilyBundle\Entity\CareGiver $careGiver)
    {
        $this->careGiver->removeElement($careGiver);
    }

    /**
     * Get careGiver
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCareGiver()
    {
        return $this->careGiver;
    }

    /**
     * Get importIdentifier
     *
     * @return string
     */
    public function getImportIdentifier()
    {
        return $this->importIdentifier;
    }

    /**
     * Set importIdentifier
     *
     * @param string $importIdentifier
     *
     * @return Person
     */
    public function setImportIdentifier($importIdentifier)
    {
        $this->importIdentifier = $importIdentifier;

        return $this;
    }

    /**
     * Get extra
     *
     * @return \Busybee\PersonBundle\Entity\PersonExtra
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * Set extra
     *
     * @param \Busybee\PersonBundle\Entity\PersonExtra $extra
     *
     * @return Person
     */
    public function setExtra(\Busybee\PersonBundle\Entity\PersonExtra $extra = null)
    {

        if ($extra instanceof PersonExtra)
            $extra->setPerson($this);

        $this->extra = $extra;

        return $this;
    }

    /**
     * Get preference
     *
     * @return PersonPreference
     */
    public function getPreference()
    {
        if (empty($this->preference))
            $this->setPreference(new PersonPreference());

        return $this->preference;
    }

    /**
     * Set preference
     *
     * @param PersonPreference $preference
     *
     * @return Person
     */
    public function setPreference(PersonPreference $preference = null)
    {
        $preference->setPerson($this);
        $this->preference = $preference;

        return $this;
    }
}
