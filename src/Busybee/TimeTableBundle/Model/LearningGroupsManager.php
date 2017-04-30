<?php

namespace Busybee\TimeTableBundle\Model;

use Busybee\InstituteBundle\Entity\Year;
use Busybee\StudentBundle\Entity\Student;
use Busybee\TimeTableBundle\Entity\LearningGroups;
use Busybee\TimeTableBundle\Repository\LearningGroupsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Translation\TranslatorInterface as Translator;

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
     * @var Translator
     */
    private $trans;
    /**
     * @var integer
     */
    private $possibleCount;
    /**
     * @var integer
     */
    private $studentCount;
    /**
     * @var integer
     */
    private $participantCount;
    /**
     * @var bool
     */
    private $includeAll;
    /**
     * @var bool
     */
    private $exceededMax;
    /**
     * @var array
     */
    private $missingStudents;

    /**
     * LearningGroupsManager constructor.
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om, Translator $trans)
    {
        $this->om = $om;
        $this->trans = $trans;
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

    /**
     *
     */
    public function getReport()
    {
        $report = [];
        $report['%learninggroup%'] = $this->lg->getName();
        $report['%possibleCount%'] = $this->possibleCount = $this->getPossibleCount();
        $report['%studentCount%'] = $this->studentCount = $this->getStudentCount();
        $report['%participantCount%'] = $this->participantCount = $this->getParticipantCount();
        $report['%includeAll%'] = $this->getIncludeAll();
        $report['%exceededMax%'] = $this->getExceededMax();
        $report['%missingStudents%'] = $this->getMissingStudents();


        $report['report'] = $this->trans->trans('learninggroups.report.header', $report, 'BusybeeTimeTableBundle');

        if (!$this->includeAll) {
            $report['report'] .= $this->trans->trans('learninggroups.report.includeAll', $report, 'BusybeeTimeTableBundle');
            $report['report'] .= "<ul>";
            foreach ($this->possible as $student) {
                $data = [];
                $data['%name%'] = $student->getFormatName();
                $data['%identifier%'] = $student->getPerson()->getIdentifier();
                $report['report'] .= '<li>' . $this->trans->trans('learninggroups.report.student', $data, 'BusybeeTimeTableBundle') . '</li>';
            }
            $report['report'] .= '</ul>';
        }

        if ($this->exceededMax)
            $report['report'] .= $this->trans->trans('learninggroups.report.exceededMax', $report, 'BusybeeTimeTableBundle');
        $report['report'] .= $this->trans->trans('learninggroups.report.footer', $report, 'BusybeeTimeTableBundle');

        return $report;
    }

    /**
     * @return int
     */
    public function getPossibleCount()
    {
        $this->possibleCount = $this->possible->count();
        return $this->possibleCount;
    }

    /**
     * @return int
     */
    public function getStudentCount()
    {
        $this->studentCount = $this->students->count();
        return $this->studentCount;
    }

    /**
     * @return int
     */
    public function getParticipantCount()
    {
        $this->participantCount = $this->participant->count();
        return $this->participantCount;
    }

    /**
     * @return bool
     */
    public function getIncludeAll()
    {
        $this->includeAll = true;

        // Test OK if includeAll not set

        if (!$this->lg->getIncludeAll())
            return $this->includeAll;

        if ($this->getPossibleCount() > 0)
            $this->includeAll = false;

        return $this->includeAll;
    }

    /**
     * @return bool
     */
    public function getExceededMax()
    {
        $this->exceededMax = false;

        // Test OK if includeAll not set

        if ($this->lg->getParticipants() == 0)
            return $this->exceededMax;

        if ($this->getParticipantCount() > $this->lg->getParticipants())
            $this->exceededMax = true;

        return $this->exceededMax;
    }

    /**
     * @return array
     */
    public function getMissingStudents()
    {
        $this->missingStudents = [];
        if ($this->getPossibleCount() == 0)
            return $this->missingStudents;

        foreach ($this->possible as $student)
            $this->missingStudents[] = $student->getFormatName() . ': ' . $student->getPerson()->getIdentifier();

        return $this->missingStudents;
    }
}