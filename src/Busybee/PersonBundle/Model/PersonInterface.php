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

    /**
     * Format Name
     *
     * @param array $options
     * @return string
     */
    public function formatName($options = []);
}