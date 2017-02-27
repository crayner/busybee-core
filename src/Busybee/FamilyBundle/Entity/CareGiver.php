<?php

namespace Busybee\FamilyBundle\Entity;

use Busybee\FamilyBundle\Model\CareGiverModel;

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
     * @var \Busybee\FamilyBundle\Entity\Family
     */
    private $family;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->careGiverFamily = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add careGiverFamily
     *
     * @param \Busybee\FamilyBundle\Entity\FamilyCareGiver $careGiverFamily
     *
     * @return CareGiver
     */
    public function addCareGiverFamily(\Busybee\FamilyBundle\Entity\FamilyCareGiver $careGiverFamily)
    {
        $this->careGiverFamily[] = $careGiverFamily;

        return $this;
    }

    /**
     * Remove careGiverFamily
     *
     * @param \Busybee\FamilyBundle\Entity\FamilyCareGiver $careGiverFamily
     */
    public function removeCareGiverFamily(\Busybee\FamilyBundle\Entity\FamilyCareGiver $careGiverFamily)
    {
        $this->careGiverFamily->removeElement($careGiverFamily);
    }

    /**
     * Get careGiverFamily
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCareGiverFamily()
    {
        return $this->careGiverFamily;
    }

    /**
     * Set careGiverFamily
     *
     * @param \Busybee\FamilyBundle\Entity\FamilyCareGiver $careGiverFamily
     *
     * @return CareGiver
     */
    public function setCareGiverFamily(\Busybee\FamilyBundle\Entity\FamilyCareGiver $careGiverFamily = null)
    {
        $this->careGiverFamily = $careGiverFamily;

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
     * @return \Busybee\FamilyBundle\Entity\Family
     */
    public function getFamily()
    {
        return $this->family;
    }

    /**
     * Set family
     *
     * @param \Busybee\FamilyBundle\Entity\Family $family
     *
     * @return CareGiver
     */
    public function setFamily(\Busybee\FamilyBundle\Entity\Family $family = null)
    {
        $this->family = $family;

        return $this;
    }
}
