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
}
