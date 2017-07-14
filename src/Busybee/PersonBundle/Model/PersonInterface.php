<?php

namespace Busybee\PersonBundle\Model;

use Busybee\PersonBundle\Entity\Person;

interface PersonInterface
{

    /**
     * Get Person
     *
     * @return Person
     */
    public function getPerson();
}