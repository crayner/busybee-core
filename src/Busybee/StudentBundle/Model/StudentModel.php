<?php

namespace Busybee\StudentBundle\Model;

use Busybee\InstituteBundle\Entity\Year;

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

    /**
     * @return bool
     */
    public function canDelete()
    {
        return true;
    }

    /**
     * @param Year $year
     */
    public function getStudentGrade(Year $year)
    {
        $grades = $this->getGrades();

        foreach ($grades as $grade) {
            if ($grade->getGrade()->getYear()->getId() == $year->getId())
                return $grade->getGrade();
        }

        return null;
    }
}