<?php

namespace Busybee\PersonBundle\Entity;

use Busybee\PersonBundle\Model\PersonModel ;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection ;

/**
 * Person
 */
class Person extends PersonModel
{
    /**
     * @var string
     */
    protected $oldPhoto;
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
     * @var string
     */
    private $photo;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $phone;
    /**
     * @var \Busybee\PersonBundle\Entity\Address
     */
    private $address1;
    /**
     * @var \Busybee\PersonBundle\Entity\Address
     */
    private $address2;
    /**
     * @var \busybee\PersonBundle\Entity\Staff
     */
    private $staff;
    /**
     * @var \Busybee\PersonBundle\Entity\CareGiver
     */
    private $caregiver;
    /**
     * @var \Busybee\PersonBundle\Entity\Student
     */
    private $student;
    /**
     * @var \Busybee\SecurityBundle\Entity\User
     */
    private $user;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->phone = new ArrayCollection();
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
    public function setDob(\DateTime $dob = null)
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
     * @return Person
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
     * @return Person
     */
    public function setModifiedBy(\Busybee\SecurityBundle\Entity\User $modifiedBy = null)
    {
        $this->modifiedBy = $modifiedBy;

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
     * @param	string	$photo
     *
     * @return	Person
     */
    public function setPhoto($photo = null)
    {
		if ($photo instanceof File)
		{
			$this->photo = $photo ;
			return $this ;
		}
        if (is_null($photo) && $this->deletePhoto)
		{
			$this->oldPhoto = $this->photo;
			$this->photo = $photo ;
		}
		elseif (! is_null($photo) && $this->getPhoto() !== $photo)
		{
			$this->oldPhoto = $this->photo;
			$this->photo = $photo ;
		}
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
        $this->phone->add($phone);

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
     * Get staff
     *
     * @return \busybee\PersonBundle\Entity\Staff
     */
    public function getStaff()
    {
        return $this->staff;
    }

    /**
     * Set staff
     *
     * @param \busybee\PersonBundle\Entity\Staff $staff
     *
     * @return Person
     */
    public function setStaff(\busybee\PersonBundle\Entity\Staff $staff = null)
    {
        $this->staff = $staff;

        return $this;
    }

    /**
     * Get caregiver
     *
     * @return \Busybee\PersonBundle\Entity\CareGiver
     */
    public function getCaregiver()
    {
        return $this->caregiver;
    }

    /**
     * Set caregiver
     *
     * @param \Busybee\PersonBundle\Entity\CareGiver $caregiver
     *
     * @return Person
     */
    public function setCaregiver(\Busybee\PersonBundle\Entity\CareGiver $caregiver = null)
    {
        $this->caregiver = $caregiver;

        return $this;
    }

    /**
     * Get student
     *
     * @return \Busybee\PersonBundle\Entity\Student
     */
    public function getStudent()
    {
        return $this->student;
    }

    /**
     * Set student
     *
     * @param \Busybee\PersonBundle\Entity\Student $student
     *
     * @return Person
     */
    public function setStudent(\Busybee\PersonBundle\Entity\Student $student = null)
    {
        $this->student = $student;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Busybee\SecurityBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set user
     *
     * @param \Busybee\SecurityBundle\Entity\User $user
     *
     * @return Person
     */
    public function setUser(\Busybee\SecurityBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }
}
