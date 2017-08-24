<?php

namespace Busybee\Core\SecurityBundle\Event;

use Symfony\Component\HttpFoundation\Response;

class GetResponseRoleEvent extends RoleEvent
{
	private $response;

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
