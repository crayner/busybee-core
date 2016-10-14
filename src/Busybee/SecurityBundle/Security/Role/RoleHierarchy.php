<?php
namespace Busybee\SecurityBundle\Security\Role;

use Doctrine\ORM\EntityManager ;
use Symfony\Component\Security\Core\Role\RoleHierarchy as RoleHierarchyBase;

class RoleHierarchy extends RoleHierarchyBase {
	
    private $em;
    protected $hierarchy;
    /**
     * @param array $hierarchy
     */
    public function __construct(array $hierarchy, EntityManager  $em)
    {
        $this->em = $em;
        parent::__construct($this->buildRolesTree());
    }
    /**
     * Here we build an array with roles. It looks like a two-levelled tree - just 
     * like original Symfony roles are stored in security.yml
     * @return array
     */
    private function buildRolesTree() {
		
        $hierarchy = array();
        $roles = $this->em->createQuery('SELECT r FROM BusybeeSecurityBundle:Role r')->execute();;
        foreach ($roles as $role) {
			if (!isset($hierarchy[$role->getRole()]))
				$hierarchy[$role->getRole()] = array();
			foreach ($role->getChildrenRoles() as $name)
				$hierarchy[$role->getRole()][] = $name->getRole();
			if (! in_array('ROLE_USER', $hierarchy[$role->getRole()]))
					$hierarchy[$role->getRole()][] = 'ROLE_USER';
        }
		$this->hierarchy = $hierarchy;
        return $hierarchy;
    }

	public function getMap()
	{
		return $this->map;
	}

	public function getHierarchy()
	{
		return $this->hierarchy;
	}

	public function getAssigned()
	{
        $hierarchy = array();
        $roles = $this->em->createQuery('SELECT r FROM BusybeeSecurityBundle:Role r')->execute();;
        foreach ($roles as $role) {
			if (!isset($hierarchy[$role->getRole()]))
				$hierarchy[$role->getRole()] = array();
			foreach ($role->getChildrenRoles() as $name)
				$hierarchy[$role->getRole()][] = $name->getRole();
        }
        return $hierarchy;
	}

}