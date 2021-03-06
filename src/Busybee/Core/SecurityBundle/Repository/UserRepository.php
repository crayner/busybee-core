<?php

namespace Busybee\Core\SecurityBundle\Repository;

use Busybee\Core\SecurityBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository
{
	/**
	 * find
	 *
	 * @param mixed $id
	 *
	 * @return User
	 */
	public function find($id)
	{
		$entity = parent::find($id);

		if (is_null($entity) && intval($id) === 1)
			$entity = new User();

		return $entity;
	}
}
