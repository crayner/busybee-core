<?php

namespace Busybee\FamilyBundle\Entity;

/**
 * FamilyCareGiver
 */
class FamilyCareGiver
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $contactOrder;

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
     * @var \Busybee\FamilyBundle\Entity\Family
     */
    private $family;

    /**
     * @var \Busybee\PersonBundle\Entity\CareGiver
     */
    private $careGiver;


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
     * Get contactOrder
     *
     * @return integer
     */
    public function getContactOrder()
    {
        return $this->contactOrder;
    }

    /**
     * Set contactOrder
     *
     * @param integer $contactOrder
     *
     * @return FamilyCareGiver
     */
    public function setContactOrder($contactOrder)
    {
        $this->contactOrder = $contactOrder;

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
     * @return FamilyCareGiver
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
     * @return FamilyCareGiver
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
     * @return FamilyCareGiver
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
     * @return FamilyCareGiver
     */
    public function setModifiedBy(\Busybee\SecurityBundle\Entity\User $modifiedBy = null)
    {
        $this->modifiedBy = $modifiedBy;

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
     * @return FamilyCareGiver
     */
    public function setFamily(\Busybee\FamilyBundle\Entity\Family $family = null)
    {
        $this->family = $family;

        return $this;
    }

    /**
     * Get careGiver
     *
     * @return \Busybee\PersonBundle\Entity\CareGiver
     */
    public function getCareGiver()
    {
        return $this->careGiver;
    }

    /**
     * Set careGiver
     *
     * @param \Busybee\PersonBundle\Entity\CareGiver $careGiver
     *
     * @return FamilyCareGiver
     */
    public function setCareGiver(\Busybee\PersonBundle\Entity\CareGiver $careGiver = null)
    {
        $this->careGiver = $careGiver;

        return $this;
    }
}
