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
abstract class StudentModel
{
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

    /**
     * Student constructor.
     */
    public function __construct()
    {
        $this->setStartAtThisSchool(new \DateTime('now'));
        $this->setStartAtSchool(new \DateTime('now'));
    }
}