<?php

namespace Busybee\DatabaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Busybee\DatabaseBundle\Model\Table as TableBase;

/**
 * Table
 */
class Table extends TableBase
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $role;

    /**
     * @var array
     */
    private $linkDetails;

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
     * @return Table
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
     * Constructor
     */
    public function __construct()
    {
        $this->role = new \Doctrine\Common\Collections\ArrayCollection();
		$this->setLimits('unlimited');
		parent::__construct();
    }

    /**
     * Add role
     *
     * @param \Busybee\SecurityBundle\Entity\Role $role
     * @return Table
     */
    public function addRole(\Busybee\SecurityBundle\Entity\Role $role)
    {
        $this->role[] = $role;

        return $this;
    }

    /**
     * Remove role
     *
     * @param \Busybee\SecurityBundle\Entity\Role $role
     */
    public function removeRole(\Busybee\SecurityBundle\Entity\Role $role)
    {
        $this->role->removeElement($role);
    }

    /**
     * Get role
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set role
     *
	 * @param \Busybee\SecurityBundle\Entity\Role
     * @return Field 
     */
    public function setRole( $role )
    {
        if ($role instanceof \Doctrine\Common\Collections\ArrayCollection)
			$role = $role->first();

		$this->role = new \Doctrine\Common\Collections\ArrayCollection();
		if (empty($role))
			return $this ;

		return $this->addRole($role);
    }
    /**
     * @var string
     */
    private $limits;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $parent;


    /**
     * Set limits
     *
     * @param string $limits
     *
     * @return Table
     */
    public function setLimits($limits)
    {
        if (empty($limits))
			$limits = 'unlimited';
		$this->limits = $limits;

        return $this;
    }

    /**
     * Get limits
     *
     * @return string
     */
    public function getLimits()
    {
        return $this->limits;
    }

    /**
     * Add parent
     *
     * @param \Busybee\DatabaseBundle\Entity\Table $parent
     *
     * @return Table
     */
    public function addParent(\Busybee\DatabaseBundle\Entity\Table $parent)
    {
        $this->parent[] = $parent;

        return $this;
    }

    /**
     * Remove parent
     *
     * @param \Busybee\DatabaseBundle\Entity\Table $parent
     */
    public function removeParent(\Busybee\DatabaseBundle\Entity\Table $parent)
    {
        $this->parent->removeElement($parent);
    }

    /**
     * Get parent
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set parent
     *
	 * @param \Busybee\DatabaseBundle\Entity\Table
     * @return Field 
     */
    public function setParent( $parent )
    {
        if ($parent instanceof \Doctrine\Common\Collections\ArrayCollection)
			$parent = $parent->first();

        $this->parent = new \Doctrine\Common\Collections\ArrayCollection();
		if (empty($parent))
			return $this ;

		return $this->addParent($parent);
    }
	
	/**
	 * to String
	 *
	 * @return	string
	 */
	public function __toString()
	{
		return 'Table - '.$this->getName();
	}

    /**
     * Set linkDetails
     *
     * @param array $linkDetails
     *
     * @return Table
     */
    public function setLinkDetails($linkDetails)
    {
		if  (! is_array($linkDetails))
			$linkDetails = array();
        $this->linkDetails = $linkDetails;

        return $this;
    }

    /**
     * Get linkDetails
     *
     * @return array
     */
    public function getLinkDetails()
    {
		if  (! is_array($this->linkDetails))
			$this->linkDetails = array();
		return $this->linkDetails;
    }
}
