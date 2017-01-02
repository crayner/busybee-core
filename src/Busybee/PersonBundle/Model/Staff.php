<?php

namespace Busybee\PersonBundle\Model ;

class Staff
{
    use \Busybee\PersonBundle\Model\FormatNameExtension ;

    public function canDelete()
    {
        return true ;
    }
}