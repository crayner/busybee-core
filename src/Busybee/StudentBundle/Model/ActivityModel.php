<?php

namespace Busybee\StudentBundle\Model;

class ActivityModel
{
    public function getNameYear()
    {
        return '(' . $this->getYear()->getName() . ') ' . $this->getName();
    }
}