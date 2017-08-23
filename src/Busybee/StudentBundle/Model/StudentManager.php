<?php

namespace Busybee\StudentBundle\Model;

use Busybee\Core\CalendarBundle\Entity\Year;
use Busybee\PersonBundle\Model\PersonManager;
use Busybee\StudentBundle\Entity\Student;
use Busybee\Core\SystemBundle\Setting\SettingManager;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class StudentManager extends PersonManager
{
    public function __construct(SettingManager $sm, ObjectManager $om, ValidatorInterface $validator, Year $year)
    {
        parent::__construct($sm, $om, $validator, $year);
    }

    public function getStudentNameWithGrade($student_id, Year $year, $options = [])
    {
        $student = $this->getOm()->getRepository(Student::class)->find(intval($student_id));

        $grade = null;
        foreach ($student->getGrades() as $sg) {
            $grade = $sg->getGrade();
            if ($grade->getYear()->getId() == $year->getId())
                break;
            $grade = null;
        }

        if (is_null($grade))
            return $student->formatName($options) . ' (No Grade in ' . $year->getName() . '.)';
        return $student->formatName($options) . ' (' . $grade->getName() . ')';
    }
}