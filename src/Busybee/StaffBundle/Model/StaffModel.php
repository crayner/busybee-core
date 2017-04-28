<?php

namespace Busybee\StaffBundle\Model;

abstract class StaffModel
{
    use \Busybee\PersonBundle\Model\FormatNameExtension;

    public function __construct()
    {
        $this->setStaffType('Unknown');
        $this->setJobTitle('Not Specified');
    }

    public function canDelete()
    {
        return true;
    }

    public function getPortrait($float = 'none')
    {
        return $this->getPerson()->getPhoto75($float);
    }
}