<?php

namespace Busybee\RecordBundle\Entity;

/**
 * EnumType
 */
class DateType extends \Busybee\RecordBundle\Model\ElementType
{
    /**
     * @var \DateTime
     */
    private $value;


    /**
     * Set value
     *
     * @param \DateTime $value
     *
     * @return DateType
     */
    public function setValue( $value)
    {
		if (is_array($value))
		{
			if (isset($value['year']) && isset($value['month']) && isset($value['day']))
				$value = new \DateTime($value['year'].'-'.$value['month'].'-'.$value['day']);
			else 
				$value = new \DateTime('today');
		}
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return \DateTime
     */
    public function getValue()
    {
        if (! $this->value instanceof \DateTime)
			$this->setValue(new \DateTime('today'));
		return $this->value;
    }
	
	public function __construct()
	{
		$this->setValue(new \DateTime('today'));
	}

	/**
	 * Value to String
	 *
	 * @return 	string
	 */
	public function valueToString()
	{
		return $this->getValue()->format('Ymd');
	}
}
