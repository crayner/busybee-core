<?php

namespace Busybee\RecordBundle\Entity;

/**
 * IntegerType
 */
class IntegerType extends \Busybee\RecordBundle\Model\ElementType
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
     * @return IntegerType
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
        if (! isset($this->value))
			return 0;
		return $this->value;
    }
}
