<?php

namespace Busybee\SecurityBundle\Model;

use Busybee\SecurityBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class GrabUser
{
    /**
     * @var User
     */
    private $user;

    /**
     * GrabUser constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->user = $container->get('security.token_storage')->getToken()->getUser();
    }

    /**
     * @return User
     */
    public function getCurrentUser(): User
    {
        return $this->user;
    }
}