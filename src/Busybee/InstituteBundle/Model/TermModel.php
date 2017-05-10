<?php

namespace Busybee\InstituteBundle\Model;

abstract class TermModel
{
    /**
     * Can Delete
     *
     * @return bool
     */
	public function canDelete()
	{
        return true;
	}

    public function getLabel()
    {
        return $this->getName();
    }
}