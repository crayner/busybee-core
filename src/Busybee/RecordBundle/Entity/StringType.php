<?php

namespace Busybee\RecordBundle\Entity;

/**
 * String
 */
class StringType extends \Busybee\RecordBundle\Model\ElementType
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
     * @return StringType
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
