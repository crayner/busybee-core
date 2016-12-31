<?php

namespace Busybee\PersonBundle\Model ;

/**
 * Care Giver Model
 *
 * @version	31st October 2016
 * @since	31st October 2016
 * @author	Craig Rayner
 */
abstract class CareGiverModel
{
	use \Busybee\PersonBundle\Model\FormatNameExtension ;

    /**
     * @return bool
     */
    public function canDelete()
    {
        //Place rules here to stop delete

        return true ;
    }
}