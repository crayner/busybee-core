<?php

namespace Busybee\RecordBundle\Entity;

/**
 * String
 */
class ChildType extends \Busybee\RecordBundle\Model\ElementType
{
    /**
     * @var integer
     */
    private $value;

    /**
     * Set value
     *
     * @param integer $value
     *
     * @return ChildType
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return integer
     */
    public function getValue()
    {
        return $this->value;
    }

}
