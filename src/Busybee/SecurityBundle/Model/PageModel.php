<?php

namespace Busybee\SecurityBundle\Model;

use Busybee\SecurityBundle\Entity\Page;

abstract class PageModel
{
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
        $this->setRoles(array_unique($roles));

        return $this;
    }

    public function roleToString()
    {
        return implode(',', $this->getRoles());
    }
}