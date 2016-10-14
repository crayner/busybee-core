<?php

namespace Busybee\RecordBundle\Entity;

/**
 * Record
 */
class Record extends \Busybee\RecordBundle\Model\ElementType
{
    /**
     * Get record as Value
     *
     * @return integer
     */
    public function getValue()
    {
        return $this->record;
    }
}
