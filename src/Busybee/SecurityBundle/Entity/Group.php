<?php
namespace Busybee\SecurityBundle\Entity ;

class Group 
{	

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $groupname;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $roles;

    /**
     * Constructor
     */
    public function __construct($group = null)
    {
        $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
		if ($group != null)
			$this->setGroupname($group);
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
     * Set groupname
     *
     * @param string $groupname
     * @return Group
     */
    public function setGroupname($groupname)
    {
        $this->groupname = $groupname;

        return $this;
    }

    /**
     * Get groupname
     *
     * @return string 
     */
    public function getGroupname()
    {
        return $this->groupname;
    }

    /**
     * Add roles
     *
     * @param \Busybee\SecurityBundle\Entity\Role $roles
     * @return Group
     */
    public function addRole(\Busybee\SecurityBundle\Entity\Role $roles)
    {
        $this->roles[] = $roles;

        return $this;
    }

    /**
     * Remove roles
     *
     * @param \Busybee\SecurityBundle\Entity\Role $roles
     */
    public function removeRole(\Busybee\SecurityBundle\Entity\Role $roles)
    {
        $this->roles->removeElement($roles);
    }

    /**
     * Get roles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRoles()
    {
        return $this->roles;
    }
}
