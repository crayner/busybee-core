<?php

namespace Busybee\PersonBundle\Model ;

class Staff
{
    public function getFormatName()
    {
        return $this->getPerson()->getFormatName();
    }
}