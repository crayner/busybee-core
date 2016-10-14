<?php
namespace Busybee\SecurityBundle\Security;

use Busybee\SecurityBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\Response;

interface LoginManagerInterface
{
    /**
     * @param string        $firewallName
     * @param UserInterface $user
     * @param Response|null $response
     *
     * @return void
     */
    public function loginUser($firewallName, UserInterface $user, Response $response = null);
}
