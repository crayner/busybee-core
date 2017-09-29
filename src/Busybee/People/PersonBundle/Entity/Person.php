<?php
namespace Busybee\People\PersonBundle\Entity;

use Busybee\People\AddressBundle\Entity\Address;
use Busybee\People\PersonBundle\Model\PersonModel;
use Busybee\Core\SecurityBundle\Entity\User;
use Busybee\People\PhoneBundle\Entity\Phone;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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
	private $honorific;

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
	 * @var \Busybee\People\AddressBundle\Entity\Address
	 */
	private $address1;

	/**
	 * @var \Busybee\People\AddressBundle\Entity\Address
	 */
	private $address2;

	/**
	 * @var Collection
	 */
	private $phone;
	/**
	 * @var string
	 */
	private $identifier;

	/**
	 * @var string
	 */
	private $importIdentifier;

	/**
	 * @var string
	 */
	private $vehicleRegistration;

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
	public function getHonorific()
	{
		return $this->honorific;
	}

	/**
	 * Set title
	 *
	 * @param string $title
	 *
	 * @return Person
	 */
	public function setHonorific($honorific)
	{
		$this->honorific = $honorific;

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
	 * @return Address
	 */
	public function getAddress1()
	{
		return $this->address1;
	}

	/**
	 * Set address1
	 *
	 * @param Address $address1
	 *
	 * @return Person
	 */
	public function setAddress1(Address $address1 = null)
	{
		$this->address1 = $address1;

		return $this;
	}

	/**
	 * Get address2
	 *
	 * @return Address
	 */
	public function getAddress2()
	{
		return $this->address2;
	}

	/**
	 * Set address2
	 *
	 * @param Address $address2
	 *
	 * @return Person
	 */
	public function setAddress2(Address $address2 = null)
	{
		$this->address2 = $address2;

		return $this;
	}

	/**
	 * Add phone
	 *
	 * @param Phone $phone
	 *
	 * @return Person
	 */
	public function addPhone(Phone $phone)
	{
		$this->phone[] = $phone;

		return $this;
	}

	/**
	 * Remove phone
	 *
	 * @param Phone $phone
	 */
	public function removePhone(Phone $phone)
	{
		$this->phone->removeElement($phone);
	}

	/**
	 * Get phone
	 *
	 * @return Collection
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
	 * @var string
	 */
	private $person_type;

	/**
	 * @return string
	 */
	public function getPersonType()
	{
		return $this->person_type;
	}

	/**
	 * Get vehicleRegistration
	 *
	 * @return string
	 */
	public function getVehicleRegistration(): string
	{
		return $this->vehicleRegistration;
	}

	/**
	 * Set vehicleRegistration
	 *
	 * @param string $vehicleRegistration
	 *
	 * @return Person
	 */
	public function setVehicleRegistration($vehicleRegistration): Person
	{
		$this->vehicleRegistration = $vehicleRegistration;

		return $this;
	}

	/**
	 * @param int $id
	 */
	public function setId(int $id)
	{
		$this->id = $id;
	}

	/**
	 * Set Phone
	 *
	 * @param Collection $phone
	 *
	 * @return $this
	 */
	public function setPhone(Collection $phone)
	{
		$this->phone = $phone;

		return $this;
	}
}
