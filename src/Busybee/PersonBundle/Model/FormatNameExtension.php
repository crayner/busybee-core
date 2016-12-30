<?php

namespace Busybee\PersonBundle\Model;

use Busybee\PersonBundle\Entity\Person;
use Doctrine\Common\CommonException;

trait FormatNameExtension
{
    /**
     * @return string
     * @throws CommonException
     */
    public function getFormatName()
    {
        if ($this->getPerson() instanceof Person)
            return $this->getPerson()->getFormatName();
        elseif (null === $this->getPerson())
            return '';

        throw new CommonException('The record ' . __CLASS__ . ' does not have a valid person.');
    }
}
