<?php

namespace Busybee\RecordBundle\Model;

/**
 * Record
 */
class ElementType
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var integer
     */
    protected $record;

    /**
     * @var integer
     */
    protected $field;

    /**
     * @var integer
     */
    protected $table;

    /**
     * @var integer
     */
    protected $user;

    /**
     * @var \DateTime
     */
    protected $createdOn;

    /**
     * @var \DateTime
     */
    protected $lastModified;


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
     * Set record
     *
     * @param integer $record
     *
     * @return Record
     */
    public function setRecord($record)
    {
        $this->record = $record;

        return $this;
    }

    /**
     * Get record
     *
     * @return integer
     */
    public function getRecord()
    {
        return $this->record;
    }

    /**
     * Set field
     *
     * @param integer $field
     *
     * @return Record
     */
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * Get field
     *
     * @return integer
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Set table
     *
     * @param integer $table
     *
     * @return Record
     */
    public function setTable($table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Get table
     *
     * @return integer
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Set user
     *
     * @param integer $user
     *
     * @return Record
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return integer
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return Record
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
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateLastModified()
    {
        if (empty($this->getCreatedOn()))
			$this->setCreatedOn(new \DateTime('now')); 
		$this->setLastModified(new \DateTime('now'));
    }

    /**
     * Set lastModified
     *
     * @param \DateTime $lastModified
     *
     * @return EnumType
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
	 * Value to String
	 *
	 * @return 	string
	 */
	public function valueToString()
	{
		return strval($this->getValue());
	}
}
