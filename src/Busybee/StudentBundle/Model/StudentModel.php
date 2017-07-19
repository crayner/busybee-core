<?php

namespace Busybee\StudentBundle\Model;

use Busybee\InstituteBundle\Entity\Year;

abstract class StudentModel
{
    use \Busybee\PersonBundle\Model\FormatNameExtension;

    /**
     * @var string
     */
    public $activityList;

    /**
     * @var Year
     */
    private $year;

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

    /**
     * @return Year
     */
    public function getYear(): Year
    {
        return $this->year;
    }

    /**
     * @param Year $year
     */
    public function setYear(Year $year)
    {
        $this->year = $year;
    }
}