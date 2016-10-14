<?php

namespace Busybee\RecordBundle\Entity;

/**
 * EnumType
 */
class EnumType extends \Busybee\RecordBundle\Model\ElementType
{
    /**
     * @var string
     */
    private $value;

    /**
     * Set value
     *
     * @param string $value
     *
     * @return EnumType
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

}
