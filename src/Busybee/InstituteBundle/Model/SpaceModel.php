<?php

namespace Busybee\InstituteBundle\Model;

abstract class SpaceModel
{
    public function getNameCapacity()
    {
        return $this->getName() . ' (' . $this->getCapacity() . ')';
    }
}