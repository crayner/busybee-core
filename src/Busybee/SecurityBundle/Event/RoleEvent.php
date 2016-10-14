<?php
namespace Busybee\SecurityBundle\Event;

use Symfony\Component\Security\Core\Role\RoleInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class RoleEvent extends Event
{
    private $role;
    private $request;

    public function __construct(RoleInterface $role, Request $request)
    {
        $this->role = $role;
        $this->request = $request;
    }

    /**
     * @return RoleInterface
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
