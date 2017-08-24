<?php

namespace Busybee\People\PersonBundle\Model;

/**
 * Locality Model
 *
 * @version    31st October 2016
 * @since      31st October 2016
 * @author     Craig Rayner
 */
abstract class PhoneModel
{
	/**
	 * to String
	 *
	 * @return string
	 */
	public function __toString()
	{
		return trim($this->getCountryCode() . ' ' . $this->getPhoneNumber());
	}

	/**
	 * to String
	 *
	 * @return string
	 */
	public function displayNumber()
	{
		return trim($this->getPhoneType() . ": " . $this->getCountryCode() . ' ' . $this->getPhoneNumber(), ' :');
	}
}