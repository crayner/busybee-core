<?php

namespace Busybee\PersonBundle\Model ;

/**
 * Address Model
 *
 * @version	31st October 2016
 * @since	31st October 2016
 * @author	Craig Rayner
 */
abstract class PersonModel
{
	/**
	 * @var	\Busybee\personBundle\Entity\Address
	 */
	protected $address1Record ;

	/**
	 * @var	\Busybee\personBundle\Entity\Address
	 */
	protected $address2Record ;

	/**
	 * set Address 1 Record
	 *
	 * @version	1st November 2016
	 * @since	1st November 2016
	 * @param	\Busybee\personBundle\Entity\Address $address
	 * @return	this
	 */
	public function setAddress1Record(\Busybee\personBundle\Entity\Address $address)
	{
		$this->address1Record = $address;
		
		return $this ;
	}

	/**
	 * set Address 2 Record
	 *
	 * @version	1st November 2016
	 * @since	1st November 2016
	 * @param	\Busybee\personBundle\Entity\Address $address
	 * @return 	this
	 */
	public function setAddress2Record(\Busybee\personBundle\Entity\Address $address)
	{
		$this->address2Record = $address;
		
		return $this ;
	}

	/**
	 * get Address 1 Record
	 *
	 * @version	1st November 2016
	 * @since	1st November 2016
	 * @return 	\Busybee\personBundle\Entity\Address
	 */
	public function getAddress1Record()
	{
		return $this->address1Record ;
	}

	/**
	 * get Address 2 Record
	 *
	 * @version	1st November 2016
	 * @since	1st November 2016
	 * @return 	\Busybee\personBundle\Entity\Address
	 */
	public function getAddress2Record()
	{
		return $this->address2Record ;
	}
}