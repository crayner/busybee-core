<?php

namespace Busybee\Core\InstallBundle\Model;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface as SecurityUserInterface;
use Busybee\Core\SecurityBundle\Model\UserInterface;

class UserProvider implements UserProviderInterface
{

	/**
	 * {@inheritDoc}
	 */
	public function loadUserByUsername($username)
	{
		$user = null;

		return $user;
	}

	/**
	 * {@inheritDoc}
	 */
	public function refreshUser(SecurityUserInterface $user)
	{
		return null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function supportsClass($class)
	{
		return true;
	}

	/**
	 * Finds a user by username.
	 *
	 * This method is meant to be an extension point for child classes.
	 *
	 * @param string $username
	 *
	 * @return UserInterface|null
	 */
	protected function findUser($username)
	{
		return null;
	}
}
