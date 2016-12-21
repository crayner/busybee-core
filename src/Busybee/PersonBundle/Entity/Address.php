<?php

namespace Busybee\PersonBundle\Entity;

use Busybee\PersonBundle\Model\AddressModel ;

/**
 * Address
 */
class Address extends AddressModel 
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $propertyName;

    /**
     * @var string
     */
    private $streetName;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set propertyName
     *
     * @param string $propertyName
     *
     * @return Address
     */
    public function setPropertyName($propertyName)
    {
        $this->propertyName = empty($propertyName) || is_null($propertyName) ? "" : $propertyName ;

        return $this;
    }

    /**
     * Get propertyName
     *
     * @return string
     */
    public function getPropertyName()
    {
        return empty($this->propertyName) ? "" : $this->propertyName ;
    }

    /**
     * Set streetName
     *
     * @param string $streetName
     *
     * @return Address
     */
    public function setStreetName($streetName)
    {
        $this->streetName = $streetName;

        return $this;
    }

    /**
     * Get streetName
     *
     * @return string
     */
    public function getStreetName()
    {
        return $this->streetName;
    }

    /**
     * Set lastModified
     *
     * @param \DateTime $lastModified
     *
     * @return Address
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
     * @return Address
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
     * Set createdBy
     *
     * @param \Busybee\SecurityBundle\Entity\User $createdBy
     *
     * @return Address
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
     * @return Address
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
     * @var string
     */
    private $buildingType;

    /**
     * @var string
     */
    private $buildingNumber;

    /**
     * @var string
     */
    private $streetNumber;


    /**
     * Set buildingType
     *
     * @param string $buildingType
     *
     * @return Address
     */
    public function setBuildingType($buildingType)
    {

        $this->buildingType = empty($buildingType) ? '' : $buildingType;

        return $this;
    }

    /**
     * Get buildingType
     *
     * @return string
     */
    public function getBuildingType()
    {
        return $this->buildingType ;
    }

    /**
     * Set buildingNumber
     *
     * @param string $buildingNumber
     *
     * @return Address
     */
    public function setBuildingNumber($buildingNumber)
    {
        $this->buildingNumber = empty($buildingNumber) || is_null($buildingNumber) ? '' : $buildingNumber ;

        return $this;
    }

    /**
     * Get buildingNumber
     *
     * @return string
     */
    public function getBuildingNumber()
    {
        return empty($this->buildingNumber) ? "" : $this->buildingNumber ;
    }

    /**
     * Set streetNumber
     *
     * @param string $streetNumber
     *
     * @return Address
     */
    public function setStreetNumber($streetNumber)
    {
        $this->streetNumber = empty($streetNumber) ? '' : $streetNumber;

        return $this;
    }

    /**
     * Get streetNumber
     *
     * @return string
     */
    public function getStreetNumber()
    {
        return empty($this->streetNumber) ? "" : $this->streetNumber ;
    }

    /**
     * @var \Busybee\PersonBundle\Entity\Locality
     */
    private $locality;


    /**
     * Set locality
     *
     * @param \Busybee\PersonBundle\Entity\Locality $locality
     *
     * @return Address
     */
    public function setLocality(\Busybee\PersonBundle\Entity\Locality $locality = null)
    {
        $this->locality = $locality;

        return $this;
    }

    /**
     * Get locality
     *
     * @return \Busybee\PersonBundle\Entity\Locality
     */
    public function getLocality()
    {
        return $this->locality;
    }
}
