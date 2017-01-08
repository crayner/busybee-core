<?php

namespace Busybee\PersonBundle\Model;

use Busybee\PersonBundle\Entity\CareGiver;
use Busybee\PersonBundle\Entity\Person;
use Busybee\SecurityBundle\Entity\User;
use Doctrine\Common\CommonException;

trait FormatNameExtension
{
    /**
     * @return string
     * @throws CommonException
     */
    public function getFormatName()
    {
        $options = array();

        if ($this instanceof CareGiver)
            $options['preferredOnly'] = true ;

        $person = $this->getPerson();
        if ($person instanceof Person)
            return $person->getFormatName($options);
        elseif ($this instanceof User)
            return $this->getUsername();
        elseif (null === $person)
            return '';

        throw new CommonException('The record ' . __CLASS__ . ' does not have a valid person.');
    }
}
