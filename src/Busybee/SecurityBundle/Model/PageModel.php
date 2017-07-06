<?php

namespace Busybee\SecurityBundle\Model;

use Busybee\SecurityBundle\Entity\Page;

abstract class PageModel
{

    private $cacheTime;
    /**
     * add Role
     *
     * @param $role
     * @return Page
     */
    public function addRole($role)
    {
        $roles = $this->getRoles();
        $roles[] = $role;
        $this->setRoles($roles);

        return $this;
    }

    public function roleToString()
    {
        return implode(',', $this->getRoles());
    }
}