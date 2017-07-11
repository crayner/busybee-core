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
        if (is_null($container->get('security.token_storage')->getToken()))
            $this->user = null;
        else
            $this->user = $container->get('security.token_storage')->getToken()->getUser();
    }

    /**
     * @return User
     */
    public function getCurrentUser()
    {
        return $this->user;
    }
}