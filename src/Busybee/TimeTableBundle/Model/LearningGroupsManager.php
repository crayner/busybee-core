<?php

namespace Busybee\TimeTableBundle\Model;

use Busybee\InstituteBundle\Entity\Year;
use Busybee\StudentBundle\Entity\Activity;
use Busybee\StudentBundle\Entity\Student;
use Busybee\TimeTableBundle\Entity\LearningGroups;
use Busybee\TimeTableBundle\Repository\LearningGroupsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;

class LearningGroupsManager
{
    /**
     * @var LearningGroups
     */
    private $lg;

    /**
     * @var LearningGroupsRepository
     */
    private $om;
    /**
     * @var Year
     */
    private $year;
    /**
     * @var ArrayCollection
     */
    private $grades;
    /**
     * @var boolean
     */
    private $gradesGenerated = false;
    /**
     * @var ArrayCollection
     */
    private $students;
    /**
     * @var boolean
     */
    private $studentsGenerated = false;
    /**
     * @var ArrayCollection
     */
    private $participant;
    /**
     * @var boolean
     */
    private $participantGenerated = false;
    /**
     * @var ArrayCollection
     */
    private $possible;
    /**
     * @var boolean
     */
    private $possibleGenerated = false;

    /**
     * LearningGroupsManager constructor.
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
        $this->students = new ArrayCollection();
        $this->grades = new ArrayCollection();
    }

    /**
     * @param null $id
     * @param Year $year
     */
    public function generateReport($id = null, Year $year)
    {
        $this->year = $year;

        $this->getLearningGroup($id);

        $this->generateGrades()
            ->generateStudentList()
            ->generateParticipantList()
            ->generatePossibleList();
    }

    /**
     * @param null $id
     * @return LearningGroups
     */
    public function getLearningGroup($id = null)
    {
        if (is_null($id))
            return $this->lg;

        $this->lg = $this->om->getRepository(LearningGroups::class)->find($id);
        $this->gradesGenerated = false;
        $this->studentsGenerated = false;
        $this->participantGenerated = false;
        $this->possibleGenerated = false;

        return $this->lg;
    }

    /**
     * @return LearningGroupsManager
     */
    private function generatePossibleList()
    {
        if ($this->possibleGenerated)
            return $this;
        $this->possible = new ArrayCollection();

        foreach ($this->students->toArray() as $student)
            if (!$this->participant->contains($student))
                $this->possible->add($student);

        $this->possibleGenerated = true;
        return $this;
    }

    /**
     * @return LearningGroupsManager
     */
    private function generateParticipantList()
    {
        if ($this->participantGenerated)
            return $this;
        $this->participant = new ArrayCollection();

        foreach ($this->lg->getActivities()->toArray() as $activity)
            foreach ($activity->getStudents()->toArray() as $student)
                if (!$this->participant->contains($student))
                    $this->participant->add($student);

        $this->participantGenerated = true;
        return $this;
    }

    /**
     * @return LearningGroupsManager
     */
    private function generateStudentList()
    {
        if ($this->studentsGenerated)
            return $this;
        $this->students = new ArrayCollection();

        foreach ($this->grades->toArray() as $grade) {
            $students = $this->om->getRepository(Student::class)->createQueryBuilder('s')
                ->leftJoin('s.grades', 'r')
                ->leftJoin('r.grade', 'g')
                ->leftJoin('g.year', 'y')
                ->where('g.grade = :grade')
                ->andWhere('y.id = :year_id')
                ->setParameter('grade', $grade)
                ->setParameter('year_id', $this->year->getId())
                ->getQuery()
                ->getResult();
            foreach ($students as $student)
                if (!$this->students->contains($student))
                    $this->students->add($student);
        }
        $this->studentsGenerated = true;
        return $this;
    }

    /**
     * @return LearningGroupsManager
     */
    private function generateGrades()
    {
        if ($this->gradesGenerated)
            return $this;
        $this->grades = new ArrayCollection();

        foreach ($this->lg->getActivities()->toArray() as $activity) {
            foreach ($activity->getGrades() as $grade)
                if (!$this->grades->contains($grade))
                    $this->grades->add($grade);
        }
        $this->gradesGenerated = true;
        return $this;
    }
}