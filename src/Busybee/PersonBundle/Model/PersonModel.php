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