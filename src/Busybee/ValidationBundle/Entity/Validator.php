<?php

namespace General\ValidationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection ;

/**
 * Validator
 */
class Validator extends \General\ValidationBundle\Model\Validator
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $ConstraintName;

    /**
     * @var boolean
     */
    private $unique;

    /**
     * @var string
     */
    private $match;

    /**
     * @var collection
     */
    protected $message;

    /**
     * @var integer
     */
    private $length;

    /**
     * @var integer
     */
    private $minlength;

    /**
     * @var string
     */
    private $replace;

    /**
     * @var string
     */
    private $format;

    /**
     * @var collection
     */
    private $enumeration;

    /**
     * @var string
     */
    private $ConstraintGroup;


    /**
     * @var boolean
     */
    private $notblank;

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
     * Set ConstraintName
     *
     * @param string $constraintName
     * @return Validator
     */
    public function setConstraintName($constraintName)
    {
        $this->ConstraintName = $constraintName;

        return $this;
    }

    /**
     * Get ConstraintName
     *
     * @return string 
     */
    public function getConstraintName()
    {
        return $this->ConstraintName;
    }

    /**
     * Set unique
     *
     * @param boolean $unique
     * @return Validator
     */
    public function setUnique($unique)
    {
        $this->unique = $unique;

        return $this;
    }

    /**
     * Get unique
     *
     * @return boolean 
     */
    public function getUnique()
    {
        return $this->unique;
    }

    /**
     * Set match
     *
     * @param string $match
     * @return Validator
     */
    public function setMatch($match)
    {
        $this->match = $match;

        return $this;
    }

    /**
     * Get match
     *
     * @return string 
     */
    public function getMatch()
    {
        return $this->match;
    }

    /**
     * Set replace
     *
     * @param string $replace
     * @return Validator
     */
    public function setReplace($replace)
    {
        $this->replace = $replace;

        return $this;
    }

    /**
     * Get replace
     *
     * @return string 
     */
    public function getReplace()
    {
        return $this->replace;
    }

    /**
     * Set format
     *
     * @param string $format
     * @return Validator
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Get format
     *
     * @return string 
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set length
     *
     * @param integer $length
     * @return Validator
     */
    public function setLength($length)
    {
        $this->length = $length;

        return $this;
    }

    /**
     * Get length
     *
     * @return integer 
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Set minlength
     *
     * @param integer $minlength
     * @return Validator
     */
    public function setMinlength($minlength)
    {
        $this->minlength = $minlength;

        return $this;
    }

    /**
     * Get minlength
     *
     * @return integer 
     */
    public function getMinlength()
    {
        return $this->minlength;
    }


    /**
     * Set ConstraintGroup
     *
     * @param string $constraintGroup
     * @return Validator
     */
    public function setConstraintGroup($constraintGroup)
    {
        $this->ConstraintGroup = $constraintGroup;

        return $this;
    }

    /**
     * Get ConstraintGroup
     *
     * @return string 
     */
    public function getConstraintGroup()
    {
        return $this->ConstraintGroup;
    }

    /**
     * Set notblank
     *
     * @param boolean $notblank
     * @return Validator
     */
    public function setNotblank($notblank)
    {
        $this->notblank = $notblank;

        return $this;
    }

    /**
     * Get notblank
     *
     * @return boolean 
     */
    public function getNotblank()
    {
        return $this->notblank;
    }

    /**
     * Set enumeration
     *
     * @param \collection $enumeration
     * @return Validator
     */
    public function setEnumeration(\collection $enumeration)
    {
        $this->enumeration = $enumeration;

        return $this;
    }

    /**
     * Get enumeration
     *
     * @return \collection 
     */
    public function getEnumeration()
    {
        return $this->enumeration;
    }

    /**
     * Set message
     *
     * @param array
     * @return Validator
     */
    public function setMessage($message)
    {
        $this->message = $message;
		if (is_array($this->message))
			$this->message = json_encode($this->message);
        return $this;
    }

    /**
     * Get message
     *
     * @return array
     */
    public function getMessage()
    {
        if (empty($this->message)) 
				return $this->testMessages(array());
		elseif (is_array($this->message))
			return $this->testMessage($this->message);
		else
			$this->message = json_decode($this->message, false);
		
		return $this->testMessages($this->message);
			
    }
    /**
     * @var string
     */
    private $constraintName;

    /**
     * @var string
     */
    private $constraintGroup;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $messages;


    /**
     * Add messages
     *
     * @param \General\ValidationBundle\Entity\Messages $messages
     * @return Validator
     */
    public function addMessage(\General\ValidationBundle\Entity\Messages $messages)
    {
        $this->messages[] = $messages;

        return $this;
    }

    /**
     * Remove messages
     *
     * @param \General\ValidationBundle\Entity\Messages $messages
     */
    public function removeMessage(\General\ValidationBundle\Entity\Messages $messages)
    {
        $this->messages->removeElement($messages);
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
