<?php
namespace Busybee\SecurityBundle\Security\Role;

use Doctrine\ORM\EntityManager ;
use Symfony\Component\Debug\Exception\ContextErrorException;
use Symfony\Component\Security\Core\Role\RoleHierarchy as RoleHierarchyBase;
use Symfony\Component\Yaml\Yaml;

class RoleHierarchy extends RoleHierarchyBase
{

    /**
     * @var array
     */
    protected $hierarchy;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var array
     */
    private $roles;

    /**
     * RoleHierarchy constructor.
     * @param array $hierarchy
     * @param EntityManager $em
     */
    public function __construct(array $hierarchy, EntityManager $em, array $roles)
    {
        $this->em = $em;
		$y = $em->getConnection()->getParams();
		$x = array();
        $this->roles = $roles;
		if ($y['driver'] != 'pdo_sqlite')
			$x = $this->buildRolesTree();
		parent::__construct($x);
    }
    /**
     * Here we build an array with roles. It looks like a two-levelled tree - just 
     * like original Symfony roles are stored in security.yml
     * @return array
     */
    private function buildRolesTree() {
		
        $hierarchy = array();
        $roles = $this->getRoles();
        foreach ($roles as $role => $subRoles) {
            if (empty($subRoles))
                $subRoles = array();

            if (!isset($hierarchy[$role]))
                $hierarchy[$role] = array();

            foreach ($subRoles as $name)
                $hierarchy[$role][] = $name;

            if (!in_array('ROLE_USER', $hierarchy[$role]) && !in_array($role, array('ROLE_USER', 'ROLE_ALLOWED_TO_SWITCH')))
                $hierarchy[$role][] = 'ROLE_USER';
        }

		$this->hierarchy = $hierarchy;
        return $hierarchy;
    }

    /**
     * @return array
     */
    private function getRoles()
    {
        return $this->roles;

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
        $roles = $this->getRoles();

        foreach ($roles as $role => $subRoles) {
            if (!isset($hierarchy[$role]))
                $hierarchy[$role] = array();
            if (empty($subRoles)) $subRoles = array();
            foreach ($subRoles as $name)
                $hierarchy[$role][] = $name;
        }
        return $hierarchy;
	}
}