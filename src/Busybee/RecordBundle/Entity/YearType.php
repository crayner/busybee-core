<?php

namespace Busybee\RecordBundle\Entity;

/**
 * EnumType
 */
class YearType extends \Busybee\RecordBundle\Model\ElementType
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
     * @return YearType
     */
    public function setValue( $value )
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
