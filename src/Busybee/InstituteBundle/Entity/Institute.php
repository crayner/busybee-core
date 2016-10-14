<?php

namespace Busybee\InstituteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Institute
 */
class Institute
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $identifier;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $address_line_one;

    /**
     * @var string
     */
    private $address_line_two;

    /**
     * @var string
     */
    private $locality;

    /**
     * @var string
     */
    private $postcode;

    /**
     * @var string
     */
    private $state;

    /**
     * @var string
     */
    private $contact;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $facsimile;

    /**
     * @var string
     */
    private $email;

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
     * Set identifier
     *
     * @param string $identifier
     * @return Institute
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
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
     * Set name
     *
     * @param string $name
     * @return Institute
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
     * Set address_line_one
     *
     * @param string $addressLineOne
     * @return Institute
     */
    public function setAddressLineOne($addressLineOne)
    {
        $this->address_line_one = $addressLineOne;

        return $this;
    }

    /**
     * Get address_line_one
     *
     * @return string 
     */
    public function getAddressLineOne()
    {
        return $this->address_line_one;
    }

    /**
     * Set address_line_two
     *
     * @param string $addressLineTwo
     * @return Institute
     */
    public function setAddressLineTwo($addressLineTwo)
    {
        $this->address_line_two = $addressLineTwo;

        return $this;
    }

    /**
     * Get address_line_two
     *
     * @return string 
     */
    public function getAddressLineTwo()
    {
        return $this->address_line_two;
    }

    /**
     * Set locality
     *
     * @param string $locality
     * @return Institute
     */
    public function setLocality($locality)
    {
        $this->locality = $locality;

        return $this;
    }

    /**
     * Get locality
     *
     * @return string 
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * Set postcode
     *
     * @param string $postcode
     * @return Institute
     */
    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;

        return $this;
    }

    /**
     * Get postcode
     *
     * @return string 
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * Set state
     *
     * @param string $state
     * @return Institute
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set contact
     *
     * @param string $contact
     * @return Institute
     */
    public function setContact($contact)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get contact
     *
     * @return string 
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return Institute
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set facsimile
     *
     * @param string $facsimile
     * @return Institute
     */
    public function setFacsimile($facsimile)
    {
        $this->facsimile = $facsimile;

        return $this;
    }

    /**
     * Get facsimile
     *
     * @return string 
     */
    public function getFacsimile()
    {
        return $this->facsimile;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Institute
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
}
