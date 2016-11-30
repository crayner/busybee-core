<?php
namespace Busybee\SecurityBundle\Entity ;

use \Doctrine\Common\Collections\ArrayCollection ;

class Role implements \Symfony\Component\Security\Core\Role\RoleInterface
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $role;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $childrenRoles;

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
     * Constructor
     */
    public function __construct($role = null)
    {
        $this->childrenRoles = new \Doctrine\Common\Collections\ArrayCollection();
		if ($role != null)
			$this->setRole($role);
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
     * Set role
     *
     * @param string $role
     * @return Role
     */
    public function setRole($role)
    {
        
		$role = strtoupper($role);
		if (0 !== strpos($role, 'ROLE_'))
			$role = 'ROLE_' . $role;
		$this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string 
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Add childrenRoles
     *
     * @param \Busybee\SecurityBundle\Entity\Role $childrenRoles
     * @return Role
     */
    public function addChildrenRole(\Busybee\SecurityBundle\Entity\Role $childrenRoles)
    {
        $this->childrenRoles[] = $childrenRoles;

        return $this;
    }

    /**
     * Remove childrenRoles
     *
     * @param \Busybee\SecurityBundle\Entity\Role $childrenRoles
     */
    public function removeChildrenRole(\Busybee\SecurityBundle\Entity\Role $childrenRoles)
    {
        $this->childrenRoles->removeElement($childrenRoles);
    }

    /**
     * Get childrenRoles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildrenRoles()
    {
        return $this->childrenRoles;
    }

    /**
     * Set lastModified
     *
     * @param \DateTime $lastModified
     *
     * @return Role
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
     * @return Role
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
     * @return Role
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
     * @return Role
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
     * to String
     *
     * @return \Busybee\SecurityBundle\Entity\User
     */
    public function __toString()
    {
        return $this->getRole();
    }
}
