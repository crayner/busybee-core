<?php

namespace Busybee\PersonBundle\Model ;

/**
 * Student Model
 *
 * @version	31st October 2016
 * @since	31st October 2016
 * @author	Craig Rayner
 */
abstract class StudentModel
{
	use \Busybee\PersonBundle\Model\FormatNameExtension ;

    /**
     * Student constructor.
     */
    public function __construct()
    {
        $this->setStartAtThisSchool(new \DateTime());
        $this->setStartAtSchool(new \DateTime());
    }

    /**
     * @return bool
     */
    public function canDelete()
    {
        //Place rules here to stop delete
        return true ;
    }
}