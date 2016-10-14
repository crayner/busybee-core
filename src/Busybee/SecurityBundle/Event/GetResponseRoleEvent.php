<?php
namespace Busybee\SecurityBundle\Event;

use Symfony\Component\HttpFoundation\Response;

class GetResponseRoleEvent extends RoleEvent
{
    private $response;

    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return Response|null
     */
    public function getResponse()
    {
        return $this->response;
    }
}
