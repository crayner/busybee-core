<?php

namespace Busybee\PersonBundle\Entity;

use Busybee\PersonBundle\Model\PersonModel ;

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
     * @var \DateTime
     */
    private $lastModified;

    /**
     * @var \DateTime
     */
    private $createdOn;

    /**
     * @var \Busybee\PersonBundle\Entity\Image
     */
    private $photo;

    /**
     * @var \Busybee\SecurityBundle\Entity\User
     */
    private $createdBy;

    /**
     * @var \Busybee\SecurityBundle\Entity\User
     */
    private $modifiedBy;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $address;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->address = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
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
     * Get surname
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
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
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
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
     * Get preferredName
     *
     * @return string
     */
    public function getPreferredName()
    {
        return $this->preferredName;
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
     * Get officialName
     *
     * @return string
     */
    public function getOfficialName()
    {
        return $this->officialName;
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
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
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
     * Get dob
     *
     * @return \DateTime
     */
    public function getDob()
    {
        return $this->dob;
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
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
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
     * Get email2
     *
     * @return string
     */
    public function getEmail2()
    {
        return $this->email2;
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
     * Get website
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
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
     * Get lastModified
     *
     * @return \DateTime
     */
    public function getLastModified()
    {
        return $this->lastModified;
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
     * Get createdOn
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set photo
     *
     * @param \Busybee\PersonBundle\Entity\Image $photo
     *
     * @return Person
     */
    public function setPhoto(\Busybee\PersonBundle\Entity\Image $photo = null)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return \Busybee\PersonBundle\Entity\Image
     */
    public function getPhoto()
    {
        return $this->photo;
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
     * Get createdBy
     *
     * @return \Busybee\SecurityBundle\Entity\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
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
     * Get modifiedBy
     *
     * @return \Busybee\SecurityBundle\Entity\User
     */
    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    /**
     * Add address
     *
     * @param \Busybee\PersonBundle\Entity\Address $address
     *
     * @return Person
     */
    public function addAddress(\Busybee\PersonBundle\Entity\Address $address)
    {
        $this->address[] = $address;

        return $this;
    }

    /**
     * Remove address
     *
     * @param \Busybee\PersonBundle\Entity\Address $address
     */
    public function removeAddress(\Busybee\PersonBundle\Entity\Address $address)
    {
        $this->address->removeElement($address);
    }

    /**
     * Get address
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAddress()
    {
        return $this->address;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $address1;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $address2;


    /**
     * Add address1
     *
     * @param \Busybee\PersonBundle\Entity\Address $address1
     *
     * @return Person
     */
    public function addAddress1(\Busybee\PersonBundle\Entity\Address $address1)
    {
        $this->address1[] = $address1;

        return $this;
    }

    /**
     * Remove address1
     *
     * @param \Busybee\PersonBundle\Entity\Address $address1
     */
    public function removeAddress1(\Busybee\PersonBundle\Entity\Address $address1)
    {
        $this->address1->removeElement($address1);
    }

    /**
     * Get address1
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * Add address2
     *
     * @param \Busybee\PersonBundle\Entity\Address $address2
     *
     * @return Person
     */
    public function addAddress2(\Busybee\PersonBundle\Entity\Address $address2)
    {
        $this->address2[] = $address2;

        return $this;
    }

    /**
     * Remove address2
     *
     * @param \Busybee\PersonBundle\Entity\Address $address2
     */
    public function removeAddress2(\Busybee\PersonBundle\Entity\Address $address2)
    {
        $this->address2->removeElement($address2);
    }

    /**
     * Get address2
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * Set address1
     *
     * @param integer $address1
     *
     * @return Person
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;

        return $this;
    }

    /**
     * Set address2
     *
     * @param integer $address2
     *
     * @return Person
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;

        return $this;
    }
}
