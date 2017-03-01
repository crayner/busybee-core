<?php

namespace Busybee\FamilyBundle\Entity;

use Busybee\FamilyBundle\Model\CareGiverModel;
use Busybee\SecurityBundle\Entity\User;

/**
 * CareGiver
 */
class CareGiver extends CareGiverModel

{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $lastModified;

    /**
     * @var \DateTime
     */
    private $createdOn;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $careGiverFamily;

    /**
     * @var \Busybee\SecurityBundle\Entity\User
     */
    private $createdBy;

    /**
     * @var \Busybee\SecurityBundle\Entity\User
     */
    private $modifiedBy;

    /**
     * @var \Busybee\PersonBundle\Entity\Person
     */
    private $person;

    /**
     * @var Family
     */
    private $family;
    /**
     * @var string
     */
    private $comment;
    /**
     * @var boolean
     */
    private $phoneContact;
    /**
     * @var boolean
     */
    private $smsContact;
    /**
     * @var boolean
     */
    private $emailContact;
    /**
     * @var boolean
     */
    private $mailContact;
    /**
     * @var integer
     */
    private $contactPriority;
    /**
     * @var string
     */
    private $vehicleRegistration;
    /**
     * @var string
     */
    private $relationship;

    /**
     * Constructor
     */
    public function __construct()
    {
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
     * @return CareGiver
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
     * @return CareGiver
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
     * @return CareGiver
     */
    public function setCreatedBy(User $createdBy = null)
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
     * @return CareGiver
     */
    public function setModifiedBy(\Busybee\SecurityBundle\Entity\User $modifiedBy = null)
    {
        $this->modifiedBy = $modifiedBy;

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
     * @return CareGiver
     */
    public function setPerson(\Busybee\PersonBundle\Entity\Person $person = null)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get family
     *
     * @return Family
     */
    public function getFamily()
    {
        return $this->family;
    }

    /**
     * Set family
     *
     * @param Family $family
     *
     * @return CareGiver
     */
    public function setFamily(Family $family = null)
    {
        $this->family = $family;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return CareGiver
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get phoneContact
     *
     * @return boolean
     */
    public function getPhoneContact()
    {
        return $this->phoneContact;
    }

    /**
     * Set phoneContact
     *
     * @param boolean $phoneContact
     *
     * @return CareGiver
     */
    public function setPhoneContact($phoneContact)
    {
        $this->phoneContact = $phoneContact;

        return $this;
    }

    /**
     * Get smsContact
     *
     * @return boolean
     */
    public function getSmsContact()
    {
        return $this->smsContact;
    }

    /**
     * Set smsContact
     *
     * @param boolean $smsContact
     *
     * @return CareGiver
     */
    public function setSmsContact($smsContact)
    {
        $this->smsContact = $smsContact;

        return $this;
    }

    /**
     * Get emailContact
     *
     * @return boolean
     */
    public function getEmailContact()
    {
        return $this->emailContact;
    }

    /**
     * Set emailContact
     *
     * @param boolean $emailContact
     *
     * @return CareGiver
     */
    public function setEmailContact($emailContact)
    {
        $this->emailContact = $emailContact;

        return $this;
    }

    /**
     * Get mailContact
     *
     * @return boolean
     */
    public function getMailContact()
    {
        return $this->mailContact;
    }

    /**
     * Set mailContact
     *
     * @param boolean $mailContact
     *
     * @return CareGiver
     */
    public function setMailContact($mailContact)
    {
        $this->mailContact = $mailContact;

        return $this;
    }

    /**
     * Get contactPriority
     *
     * @return integer
     */
    public function getContactPriority()
    {
        return $this->contactPriority;
    }

    /**
     * Set contactPriority
     *
     * @param integer $contactPriority
     *
     * @return CareGiver
     */
    public function setContactPriority($contactPriority)
    {
        $this->contactPriority = $contactPriority;

        return $this;
    }

    /**
     * Get vehicleRegistration
     *
     * @return string
     */
    public function getVehicleRegistration()
    {
        return $this->vehicleRegistration;
    }

    /**
     * Set vehicleRegistration
     *
     * @param string $vehicleRegistration
     *
     * @return CareGiver
     */
    public function setVehicleRegistration($vehicleRegistration)
    {
        $this->vehicleRegistration = $vehicleRegistration;

        return $this;
    }

    /**
     * Get relationship
     *
     * @return string
     */
    public function getRelationship()
    {
        return $this->relationship;
    }

    /**
     * Set relationship
     *
     * @param string $relationship
     *
     * @return CareGiver
     */
    public function setRelationship($relationship)
    {
        $this->relationship = $relationship;

        return $this;
    }
}
