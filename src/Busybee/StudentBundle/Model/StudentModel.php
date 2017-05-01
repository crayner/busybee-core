<?php

namespace Busybee\StudentBundle\Model;

class StudentModel
{
    use \Busybee\PersonBundle\Model\FormatNameExtension;

    /**
     * @var string
     */
    public $activityList;

    /**
     * StudentModel constructor.
     */
    public function __construct()
    {
        $this->setStartAtSchool(new \DateTime());
        $this->setStartAtThisSchool(new \DateTime());
        $this->activityList = '';
    }

    public function canDelete()
    {
        return true;
    }

}