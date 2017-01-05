<?php

namespace Busybee\PersonBundle\Model ;

class Staff
{
    use \Busybee\PersonBundle\Model\FormatNameExtension ;

    public function __construct()
    {
        $this->setType('Unknown');
        $this->setJobTitle('Unknown');
    }

    public function canDelete()
    {
        return true ;
    }
}