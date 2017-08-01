<?php
namespace Busybee\TimeTableBundle\Model;

use Busybee\InstituteBundle\Entity\Year;
use Busybee\StudentBundle\Entity\Student;
use Busybee\TimeTableBundle\Entity\Line;
use Busybee\TimeTableBundle\Entity\Period;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Translation\TranslatorInterface as Translator;

class LineManager
{
    /**
     * @var Line
     */
    private $line;

    /**
     * @var ObjectManager
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
     * @var ArrayCollection
     */
    private $duplicated;

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
     * @var integer
     */
    private $duplicateCount;

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
     * @var FlashBagInterface
     */
    private $flashbag;

    /**
     * lineManager constructor.
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om, Translator $trans, FlashBagInterface $flashbag)
    {
        $this->om = $om;
        $this->trans = $trans;
        $this->flashbag = $flashbag;
        $this->students = new ArrayCollection();
        $this->grades = new ArrayCollection();
        $this->duplicated = new ArrayCollection();
        $this->participant = new ArrayCollection();
        $this->possible = new ArrayCollection();
    }

    /**
     * @param null $id
     * @param Year $year
     */
    public function generateReport($id = null, Year $year)
    {
        $this->year = $year;

        $this->addLine($id);

        $this->generateGrades()
            ->generateStudentList()
            ->generateParticipantList()
            ->generatePossibleList();
    }

    /**
     * @param null $id
     * @return line
     */
    public function addLine($id = null)
    {
        if (is_null($id))
            return $this->line;

        $this->line = $this->om->getRepository(Line::class)->find($id);
        $this->gradesGenerated = false;
        $this->studentsGenerated = false;
        $this->participantGenerated = false;
        $this->possibleGenerated = false;

        return $this->getLine();
    }

    /**
     * @return lineManager
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
     * @return lineManager
     */
    private function generateParticipantList()
    {
        if ($this->participantGenerated)
            return $this;
        $this->participant = new ArrayCollection();
        $this->duplicated = new ArrayCollection();

        foreach ($this->line->getActivities()->toArray() as $activity)
            foreach ($activity->getStudents()->toArray() as $student) {
                $student->activityList = empty($student->activityList) ? $activity->getNameShort() : $student->activityList . ', ' . $activity->getNameShort();
                if (!$this->participant->contains($student))
                    $this->participant->add($student);
                else
                    if (!$this->duplicated->contains($student))
                        $this->duplicated->add($student);
            }
        $this->participantGenerated = true;
        return $this;
    }

    /**
     * @return lineManager
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
                ->where('g.id = :grade_id')
                ->andWhere('y.id = :year_id')
                ->setParameter('grade_id', $grade->getId())
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
     * @return lineManager
     */
    private function generateGrades()
    {
        if ($this->gradesGenerated)
            return $this;
        $this->grades = new ArrayCollection();

        foreach ($this->line->getActivities()->toArray() as $activity) {
            foreach ($activity->getGrades() as $grade)
                if (!$this->grades->contains($grade))
                    $this->grades->add($grade);
        }

        $this->gradesGenerated = true;
        return $this;
    }

    /**
     * Get Report
     *
     * @return string|html
     */
    public function getReport()
    {
        $report = [];
        $report['%learninggroup%'] = $this->line->getName();
        $report['%possibleCount%'] = $this->possibleCount = $this->getPossibleCount();
        $report['%studentCount%'] = $this->studentCount = $this->getStudentCount();
        $report['%duplicateCount%'] = $this->duplicateCount = $this->getDuplicateCount();
        $report['%participantCount%'] = $this->participantCount = $this->getParticipantCount();
        $report['%includeAll%'] = $this->getIncludeAll();
        $report['%exceededMax%'] = $this->getExceededMax();
        $report['%missingStudents%'] = $this->getMissingStudents();
        $report['%allowed%'] = $this->line->getParticipants();
        $report['%class%'] = ' class="alert alert-warning"';


        $report['report'] = $this->trans->trans('line.report.header', $report, 'BusybeeTimeTableBundle');

        if (!$this->includeAll) {
            $report['report'] .= $this->trans->trans('line.report.includeAll', $report, 'BusybeeTimeTableBundle');
            $report['report'] .= "<ul>";

            $iterator = $this->possible->getIterator();
            $iterator->uasort(function ($a, $b) {
                return ($a->getPerson()->getSurname() < $b->getPerson()->getSurname()) ? -1 : 1;
            });
            $this->possible = new ArrayCollection(iterator_to_array($iterator, false));

            foreach ($this->possible as $student) {
                $data = [];
                $data['%name%'] = $student->formatName();
                $data['%identifier%'] = $student->getPerson()->getIdentifier();
                $data['%activityList%'] = $student->activityList;
                $report['report'] .= '<li>' . $this->trans->trans('line.report.student', $data, 'BusybeeTimeTableBundle') . '</li>';
            }
            $report['report'] .= '</ul>';
        }

        if ($this->exceededMax)
            $report['report'] .= $this->trans->trans('line.report.exceededMax', $report, 'BusybeeTimeTableBundle');

        if ($this->getDuplicateCount() > 0) {
            $report['report'] .= $this->trans->trans('line.report.duplicated', $report, 'BusybeeTimeTableBundle');
            $report['report'] .= "<ul>";

            $iterator = $this->duplicated->getIterator();
            $iterator->uasort(function ($a, $b) {
                return ($a->getPerson()->getSurname() < $b->getPerson()->getSurname()) ? -1 : 1;
            });
            $this->duplicated = new ArrayCollection(iterator_to_array($iterator, false));

            foreach ($this->duplicated as $student) {
                $data = [];
                $data['%name%'] = $student->formatName();
                $data['%identifier%'] = $student->getPerson()->getIdentifier();
                $data['%activityList%'] = $student->activityList;
                $report['report'] .= '<li>' . $this->trans->trans('line.report.student', $data, 'BusybeeTimeTableBundle') . '</li>';
            }
            $report['report'] .= '</ul>';
        }

        if ($this->participantCount > $this->studentCount) {
            $report['%class%'] = ' class="alert alert-danger"';
            $report['report'] .= $this->trans->trans('line.report.extra', $report, 'BusybeeTimeTableBundle');
            $report['report'] .= "<ul>";

            $iterator = $this->participant->getIterator();
            $iterator->uasort(function ($a, $b) {
                return ($a->getPerson()->getSurname() < $b->getPerson()->getSurname()) ? -1 : 1;
            });
            $this->participant = new ArrayCollection(iterator_to_array($iterator, false));

            foreach ($this->participant as $student) {
                if (!$this->students->contains($student)) {
                    $data = [];
                    $data['%name%'] = $student->formatName();
                    $data['%identifier%'] = $student->getPerson()->getIdentifier();
                    $data['%activityList%'] = $student->activityList;
                    $report['report'] .= '<li>' . $this->trans->trans('line.report.student', $data, 'BusybeeTimeTableBundle') . '</li>';
                }
            }
            $report['report'] .= '</ul>';
        }

        $report['report'] .= $this->trans->trans('line.report.footer', $report, 'BusybeeTimeTableBundle');

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
    public function getDuplicateCount()
    {
        $this->duplicateCount = $this->duplicated->count();
        return $this->duplicateCount;
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

        if (!$this->line->getIncludeAll())
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

        if ($this->line->getParticipants() == 0)
            return $this->exceededMax;

        if ($this->getParticipantCount() > $this->line->getParticipants())
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
            if (student instanceof Student)
                $this->missingStudents[] = $student->formatName() . ': ' . $student->getPerson()->getIdentifier();

        return $this->missingStudents;
    }

    /**
     * @param $id
     */
    public function deleteLine($id)
    {
        $this->line = $this->om->getRepository(Line::class)->find($id);
        if (!$this->line instanceof Line) {
            $this->flashbag->add('warning', $this->trans->trans('line.delete.notfound', [], 'BusybeeTimeTableBundle'));
            return;
        }
        try {
            foreach ($this->line->getActivities() as $activity)
                $this->line->removeActivity($activity);

            $this->om->remove($this->line);
            $this->om->flush();

            $this->line = null;

            $this->flashbag->add('success', $this->trans->trans('line.delete.success', [], 'BusybeeTimeTableBundle'));
            return;
        } catch (\Exception $e) {
            $this->flashbag->add('danger', $this->trans->trans('line.delete.failure', ['%error%' => $e->getMessage()], 'BusybeeTimeTableBundle'));
        }

    }

    /**
     * @param $tt
     * @param $line
     * @return ArrayCollection
     */
    public function searchForSuitablePeriods($tt, $line)
    {

        $this->addLine($line);

        $spaces = [];
        $staff = [];
        $periods = [];
        $students = [];

        foreach ($this->getLine()->getActivities()->toArray() as $activity) {
            if (!empty($activity->getSpace()))
                $spaces[] = $activity->getSpace()->getId();
            if (!empty($activity->getTutor1()))
                $staff[] = $activity->getTutor1()->getId();
            if (!empty($activity->getTutor2()))
                $staff[] = $activity->getTutor2()->getId();
            if (!empty($activity->getTutor3()))
                $staff[] = $activity->getTutor3()->getId();
            if (!empty($activity->getPeriods()))
                foreach ($activity->getPeriods() as $pa)
                    $periods[] = $pa->getPeriod()->getId();
            if (!empty($activity->getStudents()))
                foreach ($activity->getStudents() as $stu)
                    $students[] = $stu->getId();
        }
        $spaces = array_unique($spaces);
        $staff = array_unique($staff);
        $periods = array_unique($periods);
        $students = array_unique($students);

        $query = $this->om->getRepository(Period::class)->createQueryBuilder('p')
            ->leftJoin('p.column', 'c')
            ->leftJoin('c.timetable', 't')
            ->where('t.id = :tt_id')
            ->setParameter('tt_id', $tt)
            ->andWhere('p.break = :false')
            ->setParameter('false', 0)
            ->orderBy('c.sequence')
            ->addOrderBy('p.start');
        if (!empty($periods))
            $query
                ->andWhere('p.id NOT IN (:periods)')
                ->setParameter('periods', $periods, Connection::PARAM_INT_ARRAY);

        $result = $query->getQuery()
            ->getResult();
        $result = empty($result) ? [] : $result;

        $free = new ArrayCollection();
        foreach ($result as $p => $period) {
            $acts = $period->getActivities();
            $keep = true;
            foreach ($acts->toArray() as $act) {
                if ($act->getSpace() && in_array($act->getSpace()->getId(), $spaces)) {
                    $keep = false;
                    break;
                }
                if ($act->getTutor1() && in_array($act->getTutor1()->getId(), $staff)) {
                    $keep = false;
                    break;
                }
                if ($act->getTutor2() && in_array($act->getTutor2()->getId(), $staff)) {
                    $keep = false;
                    break;
                }
                if ($act->getTutor3() && in_array($act->getTutor3()->getId(), $staff)) {
                    $keep = false;
                    break;
                }
                $activity = $act->getActivity();
                if ($activity->getSpace() && in_array($activity->getSpace()->getId(), $spaces)) {
                    $keep = false;
                    break;
                }
                if ($activity->getTutor1() && in_array($activity->getTutor1()->getId(), $staff)) {
                    $keep = false;
                    break;
                }
                if ($activity->getTutor2() && in_array($activity->getTutor2()->getId(), $staff)) {
                    $keep = false;
                    break;
                }
                if ($activity->getTutor3() && in_array($activity->getTutor3()->getId(), $staff)) {
                    $keep = false;
                    break;
                }
                if ($activity->getStudents()->count() > 0) {
                    foreach ($activity->getStudents() as $stu)
                        if (in_array($stu->getId(), $students)) {
                            $keep = false;
                            break;
                        }
                    if (!$keep)
                        break;
                }

            }
            if ($keep)
                $free->add($period);
        }

        return $free;
    }

    /**
     * @return Line
     */
    public function getLine(): Line
    {
        return $this->line;
    }
}