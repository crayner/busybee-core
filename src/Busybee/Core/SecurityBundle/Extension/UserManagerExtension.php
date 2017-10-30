<?php

namespace Busybee\Core\SecurityBundle\Extension;

use Busybee\Core\SecurityBundle\Doctrine\UserManager;

class UserManagerExtension extends \Twig_Extension
{
	/**
	 * @var UserManager
	 */
	private $userManager;

	/**
	 * FormErrorsExtension constructor.
	 *
	 * @param UserManager $userManager
	 */
	public function __construct(UserManager $userManager)
	{
		$this->userManager = $userManager;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFunctions()
	{
		return [
			new \Twig_SimpleFunction('formatUserName', [$this->userManager, 'formatUserName']),
			new \Twig_SimpleFunction('get_userManager', [$this, 'getUserManager']),
			new \Twig_SimpleFunction('get_SystemYear', [$this->userManager, 'getSystemYear']),
		];
	}

	/**
	 * Get User Manager
	 *
	 * @return  UserManager
	 */
	public function getUserManager()
	{
		return $this->userManager;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'user_manager_extension';
	}
}