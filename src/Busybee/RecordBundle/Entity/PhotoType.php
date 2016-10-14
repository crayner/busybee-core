<?php

namespace Busybee\RecordBundle\Entity;

/**
 * String
 */
class PhotoType extends \Busybee\RecordBundle\Model\ElementType
{
    /**
     * @var string
     */
    private $value;

    /**
     * Set value
     *
     * @param blog $value
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

	/**
	 * Value to String
	 *
	 * @return 	string
	 */
	public function valueToString()
	{
		return strval($this->getValue());
	}
}
