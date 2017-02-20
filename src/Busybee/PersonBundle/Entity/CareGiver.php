<?php

namespace Busybee\PersonBundle\Entity;

use Busybee\FamilyBundle\Entity\FamilyCareGiver;
use Busybee\PersonBundle\Model\CareGiverModel;

/**
 * Care Giver
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $careGiverFamily;

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
     * Add careGiverFamily
     *
     * @param FamilyCareGiver $careGiverFamily
     *
     * @return CareGiver
     */
    public function addCareGiverFamily(FamilyCareGiver $careGiverFamily)
    {
        $this->careGiverFamily[] = $careGiverFamily;

        return $this;
    }

    /**
     * Remove careGiverFamily
     *
     * @param FamilyCareGiver $careGiverFamily
     */
    public function removeCareGiverFamily(FamilyCareGiver $careGiverFamily)
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
}
