<?php

namespace Busybee\Core\SecurityBundle\Event;

use Busybee\Core\SecurityBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class UserEvent extends Event
{
	private $request;
	private $user;

	public function __construct(User $user, Request $request)
	{
		$this->user    = $user;
		$this->request = $request;
	}

	/**
	 * @return UserInterface
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * @return Request
	 */
	public function getRequest()
	{
		return $this->request;
	}
}
