<?php

namespace Busybee\StaffBundle\Model;

class StaffModel
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
}