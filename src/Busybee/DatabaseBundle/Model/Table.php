<?php

namespace Busybee\DatabaseBundle\Model;

/**
 * Table
 */
class Table
{
	/**
	 * @return Busybee\SecurityBundle\Entity\Role
	 */
	public function getSelectRole()
	{
		$roles = $this->getRole();
		if ( $roles instanceof \Doctrine\Common\Collections\ArrayCollection)
			$role = $roles->first();
		elseif ( $roles instanceof \Doctrine\ORM\PersistentCollection)
			$role = $roles->first();
		elseif ( is_array($roles) )
			$role = array_shift($roles);
		else
			$role = null;
		return $role;
	}

	/**
	 * @return Busybee\DatabaseBundle\Entity\Table
	 */
	public function getSelectParent()
	{
		$roles = $this->getParent();
		if ( $roles instanceof \Doctrine\Common\Collections\ArrayCollection)
			$role = $roles->first();
		elseif ( $roles instanceof \Doctrine\ORM\PersistentCollection)
			$role = $roles->first();
		elseif ( is_array($roles) )
			$role = array_shift($roles);
		else
			$role = null;
		return $role;
	}

	public function __construct()
	{
	}
	
	public function getLimitChoices()
	{
		return array(
						'No Limit' 				=> 'unlimited',
						'Single Record'			=> 'single',
					);
	}

	/**
	 * get List Link
	 *
	 * @return	array
	 */
	public function getListLink()
	{
		$result = array();
		$result['url'] = '/record/list/'.$this->getName().'/';
		return $result ;
	}

	/**
	 * get Edit Link
	 *
	 * @return	array
	 */
	public function getEditLink()
	{
		$result = array();
		$result['url'] = '/record/edit/'.$this->getName().'/';
		return $result ;
	}

	/**
	 * get New Link
	 *
	 * @return	array
	 */
	public function getNewLink()
	{
		$result = array();
		$result['url'] = '/record/new/'.$this->getName().'/';
		return $result ;
	}
}
