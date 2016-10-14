<?php

namespace Busybee\RecordBundle\Entity;

/**
 * Parent
 */
class ParentType extends \Busybee\RecordBundle\Model\ElementType
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * Set value
     *
     * @param	mixed $value
     *
     * @return ParentType
     */
    public function setValue($value)
    {
        $this->value = serialize($value);

        return $this;
    }

    /**
     * Get value
     *
     * @return 	mixed
     */
    public function getValue()
    {
        return unserialize($this->value);
    }
}
