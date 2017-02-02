<?php

namespace Busybee\PersonBundle\Model ;

use Busybee\PersonBundle\Entity\Person;

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

	public function __construct()
    {
        $this->setGender('U');
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
     * @param   array   $options
	 * @return 	string
	 */
	public function getFormatName($options = array())
	{
        /**
         *
         * Options
         *
         * surnameFirst boolean default = true
         * preferredOnly boolean default = false
         */
        if (empty($this->getSurname())) return '';

        $options['surnameFirst'] = ! isset($options['surnameFirst']) ? true : false ;
        $options['preferredOnly'] = ! isset($options['preferredOnly']) ? false : true ;

        if ($options['surnameFirst']) {
            if ($options['preferredOnly'])
                return $this->getSurname() . ': ' . $this->getPreferredName();
            return $this->getSurname() . ': ' . $this->getFirstName() . ' (' . $this->getPreferredName() . ')';
        }
        if ($options['preferredOnly'])
            return $this->getPreferredName(). ' ' . $this->getSurname();
        return $this->getFirstName() . ' (' . $this->getPreferredName() . ') ' . $this->getSurname();
	}
}