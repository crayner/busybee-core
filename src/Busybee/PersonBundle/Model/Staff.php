<?php

namespace Busybee\PersonBundle\Model ;

class Staff
{
    use \Busybee\PersonBundle\Model\FormatNameExtension ;

    public function __construct()
    {
        $this->setType('Unknown');
        $this->setJobTitle('Not available');
    }

    public function canDelete()
    {
        return true ;
    }
}