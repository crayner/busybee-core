<?php

namespace Busybee\SecurityBundle\Entity;

/**
 * Failure
 */
class Failure
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $address;

    /**
     * @var integer
     */
    private $failures;

    /**
     * @var \DateTime
     */
    private $lastModified;

    /**
     * @var \DateTime
     */
    private $createdOn;


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
     * Set address
     *
     * @param string $address
     *
     * @return Failure
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set failures
     *
     * @param \number $count
     *
     * @return Failure
     */
    public function setFailures( $failures )
    {
        $this->failures = intval($failures);

        return $this;
    }

    /**
     * Get count
     *
     * @return \number
     */
    public function getFailures()
    {
        return $this->failures;
    }

    /**
     * Inc count
     *
     * @return	Failure
     */
    public function incFailures()
    {
        $this->failures++;
		return $this;
    }

    /**
     * Construct
     *
     * @return	Failure
     */
    public function __construct()
    {
        $this->failures = 0;
		$this->address = NULL;
		$this->createdOn = NULL;
		$this->lastModified = NULL;
		return $this;
    }

    /**
     * Set lastModified
     *
     * @param \DateTime $lastModified
     *
     * @return Failure
     */
    public function setLastModified(\DateTime $lastModified)
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
     * @return Failure
     */
    public function setCreatedOn(\DateTime $createdOn)
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
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateLastModified()
    {
        if (empty($this->getCreatedOn()))
			$this->setCreatedOn(new \DateTime('now')); 
		$this->setLastModified(new \DateTime('now'));
    }
}
