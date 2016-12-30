<?php

namespace Busybee\FamilyBundle\Entity;

use Busybee\FamilyBundle\Model\FamilyModel;

/**
 * Family
 */
class Family extends FamilyModel
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
    private $careGiver1;

    /**
     * @var \Busybee\PersonBundle\Entity\Person
     */
    private $careGiver2;

    /**
     * @var \Busybee\PersonBundle\Entity\Address
     */
    private $address1;

    /**
     * @var \Busybee\PersonBundle\Entity\Address
     */
    private $address2;

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
    private $phone;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $emergencyContact;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->phone = new \Doctrine\Common\Collections\ArrayCollection();
        $this->emergencyContact = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Family
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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
     * Set lastModified
     *
     * @param \DateTime $lastModified
     *
     * @return Family
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
     * @return Family
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
     * Set careGiver1
     *
     * @param \Busybee\PersonBundle\Entity\Person $careGiver1
     *
     * @return Family
     */
    public function setCareGiver1(\Busybee\PersonBundle\Entity\Person $careGiver1 = null)
    {
        $this->careGiver1 = $careGiver1;

        return $this;
    }

    /**
     * Get careGiver1
     *
     * @return \Busybee\PersonBundle\Entity\Person
     */
    public function getCareGiver1()
    {
        return $this->careGiver1;
    }

    /**
     * Set careGiver2
     *
     * @param \Busybee\PersonBundle\Entity\Person $careGiver2
     *
     * @return Family
     */
    public function setCareGiver2(\Busybee\PersonBundle\Entity\Person $careGiver2 = null)
    {
        $this->careGiver2 = $careGiver2;

        return $this;
    }

    /**
     * Get careGiver2
     *
     * @return \Busybee\PersonBundle\Entity\Person
     */
    public function getCareGiver2()
    {
        return $this->careGiver2;
    }

    /**
     * Set address1
     *
     * @param \Busybee\PersonBundle\Entity\Address $address1
     *
     * @return Family
     */
    public function setAddress1(\Busybee\PersonBundle\Entity\Address $address1 = null)
    {
        $this->address1 = $address1;

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
     * Set address2
     *
     * @param \Busybee\PersonBundle\Entity\Address $address2
     *
     * @return Family
     */
    public function setAddress2(\Busybee\PersonBundle\Entity\Address $address2 = null)
    {
        $this->address2 = $address2;

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
     * Set createdBy
     *
     * @param \Busybee\SecurityBundle\Entity\User $createdBy
     *
     * @return Family
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
     * @return Family
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
     * Add phone
     *
     * @param \Busybee\PersonBundle\Entity\Phone $phone
     *
     * @return Family
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
     * Add emergencyContact
     *
     * @param \Busybee\PersonBundle\Entity\Person $emergencyContact
     *
     * @return Family
     */
    public function addEmergencyContact(\Busybee\PersonBundle\Entity\Person $emergencyContact)
    {
        $this->emergencyContact[] = $emergencyContact;

        return $this;
    }

    /**
     * Remove emergencyContact
     *
     * @param \Busybee\PersonBundle\Entity\Person $emergencyContact
     */
    public function removeEmergencyContact(\Busybee\PersonBundle\Entity\Person $emergencyContact)
    {
        $this->emergencyContact->removeElement($emergencyContact);
    }

    /**
     * Get emergencyContact
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEmergencyContact()
    {
        return $this->emergencyContact;
    }
}

