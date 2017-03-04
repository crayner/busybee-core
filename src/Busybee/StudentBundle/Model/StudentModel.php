<?php

namespace Busybee\StudentBundle\Model;

class StudentModel
{
    use \Busybee\PersonBundle\Model\FormatNameExtension;

    public function __construct()
    {
        $this->setStatus('Future');
        $this->setStartAtSchool(new \DateTime());
        $this->setStartAtThisSchool(new \DateTime());
    }

    public function canDelete()
    {
        return true;
    }

}