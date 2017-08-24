<?php

namespace Busybee\Core\SecurityBundle\Event;

use Symfony\Component\Security\Core\Role\RoleInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FilterRoleResponseEvent extends RoleEvent
{
	private $response;

	public function __construct(RoleInterface $role, Request $request, Response $response)
	{
		parent::__construct($role, $request);

		$this->response = $response;
	}

	/**
	 * @return Response|null
	 */
	public function getResponse()
	{
		return $this->response;
	}

	public function setResponse(Response $response)
	{
		$this->response = $response;
	}
}
