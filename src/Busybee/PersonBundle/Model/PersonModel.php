<?php

namespace Busybee\PersonBundle\Model ;

use Busybee\PersonBundle\Entity\Address ;
use Busybee\PersonBundle\Entity\Locality ;

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
	 * @var	boolean
	 */
	protected $deletePhoto = false;

	/**
	 * @var	array
	 */
	protected $genderList;

	/**
	 * @var	array
	 */
	protected $titleList;

	/**
	 * set Address 1 Record
	 *
	 * @version	2nd November 2016
	 * @since	1st November 2016
	 * @param	\Busybee\personBundle\Entity\Address $address
	 * @return	this
	 */
	public function setAddress1Record(Address $address = null)
	{
		if (! $address instanceof Address)
		{
			$address = new Address();
			$address->setLocality(new Locality());
		}
		$address->localityRecord = $address->getLocality();
		$this->address1Record = $address;
		
		return $this ;
	}

	/**
	 * set Address 2 Record
	 *
	 * @version	2nd November 2016
	 * @since	1st November 2016
	 * @param	\Busybee\personBundle\Entity\Address $address
	 * @return 	this
	 */
	public function setAddress2Record(Address $address = null)
	{
		if (! $address instanceof Address)
		{
			$address = new Address();
			$address->setLocality(new Locality());
		}
		$address->localityRecord = $address->getLocality();
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
		if ($this->address1Record instanceof Address)
			return $this->address1Record ;
		if ($this->address1 instanceof Address)
		{
			return $this->setAddress1Record($this->address1)->getAddress1Record();
		}
		return $this->setAddress1Record(null)->getAddress1Record();
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
		if ($this->address2Record instanceof Address)
			return $this->address2Record ;
		if ($this->address2 instanceof Address)
		{
			return $this->setAddress2Record($this->address2)->getAddress2Record();
		}
		return $this->setAddress2Record(null)->getAddress2Record();
	}

	/**
	 * delete Photo
	 *
	 * @version	4th November 2016
	 * @since	4th November 2016
	 * @return 	Person
	 */
	public function deletePhoto()
	{
		$this->deletePhoto = true ;
		
		return $this ;
	}

	/**
	 * delete Photo
	 *
	 * @version	4th November 2016
	 * @since	4th November 2016
	 * @return 	Person
	 */
	public function removePhotoFile()
	{
		if (isset($this->oldPhoto) && ! is_null($this->oldPhoto))
		{
			//  Delete old photo file
			$w = $this->oldPhoto;
			if (file_exists($w))
				unlink($w);				
		}
		return $this ;
	}

	/**
	 * get Gender List
	 *
	 * @version	4th November 2016
	 * @since	4th November 2016
	 * @return 	array
	 */
	public function getGenderList()
	{
		return $this->genderList ;
	}

	/**
	 * get Titles
	 *
	 * @version	4th November 2016
	 * @since	4th November 2016
	 * @return 	array
	 */
	public function getTitleList()
	{
		return $this->titleList ;
	}

	/**
	 * set Gender List
	 *
	 * @version	4th November 2016
	 * @since	4th November 2016
	 * @param	array	$list
	 * @return 	Person
	 */
	public function setGenderList($list)
	{
		$this->genderList = $list ;
		
		return $this ;
	}

	/**
	 * set Title List
	 *
	 * @version	4th November 2016
	 * @since	4th November 2016
	 * @param	array	$list
	 * @return 	Person
	 */
	public function setTitleList($list)
	{
		$this->titleList  = $list;
		
		return $this;
	}

	/**
	 * get Format Name
	 *
	 * @version	21st November 2016
	 * @since	21st November 2016
	 * @return 	string
	 */
	public function getFormatName()
	{
		return $this->getSurname().': '.$this->getFirstName().' ('.$this->getPreferredName().')';
	}
}