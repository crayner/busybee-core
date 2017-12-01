<?php

namespace Busybee\TimeTableBundle\Model;

use Busybee\Core\CalendarBundle\Entity\CalendarGroup;
use Busybee\Facility\InstituteBundle\Entity\Space;
use Busybee\Core\CalendarBundle\Entity\Year;
use Busybee\People\StaffBundle\Entity\Staff;
use Busybee\ActivityBundle\Entity\Activity;
use Busybee\TimeTableBundle\Entity\Line;
use Busybee\TimeTableBundle\Entity\Period;
use Busybee\TimeTableBundle\Entity\PeriodActivity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\TranslatorInterface;

class PeriodManager
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var \Busybee\TimeTableBundle\Repository\PeriodRepository
     */
    private $pr;

    /**
     * @var Period
     */
    private $period;

    /**
     * @var \stdClass
     */
    private $status;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var array
     */
    private $failedStatus;

    /**
     * @var array
     */
    private $spaces;

    /**
     * @var array
     */
    private $staff;

    /**
     * @var array
     */
    private $students;

    /**
     * @var FlashBagInterface
     */
    private $flashbag;

    /**
     * @var Year
     */
    private $currentYear;

    /**
     * @var array
     */
    private $alert = [
        'default' => 0,
        'primary' => 1,
        'success' => 2,
        'info' => 3,
        'warning' => 4,
        'error' => 5
    ];

    /**
     * @var \stdClass
     */
    private $periodStatus;

    /**
     * @var array
     */
    private $gradeControl;

    /**
     * @var array
     */
    private $grades;

    /**
     * PeriodManager constructor.
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om, TranslatorInterface $translator, FlashBagInterface $flashbag, Year $cy, Session $sess, $id = null)
    {
        $this->om           = $om;
        $this->pr           = $om->getRepository(Period::class);
        $this->period       = $this->injectPeriod($id);
        $this->status       = new \stdClass();
        $this->translator   = $translator;
        $this->flashbag     = $flashbag;
        $this->currentYear  = $cy;
        $this->gradeControl = is_array($sess->get('gradeControl')) ? $sess->get('gradeControl') : [];
	    $grades             = $this->om->getRepository(CalendarGroup::class)->findByYear($cy);

        foreach ($grades as $grade)
	        if (!isset($this->gradeControl[$grade->getNmaeShort()]))
		        $this->gradeControl[$grade->getNameShort()] = true;

        $this->clearResults();
    }

    /**
     * @param $id
     * @return PeriodManager
     */
    public function injectPeriod($id)
    {
        if ($this->period instanceof Period && $this->period->getId() == $id)
            return $this;

        if ($id)
            $this->period = $this->pr->find($id);

        if (!$this->period instanceof Period)
            $this->period = new Period();

        $this->clearResults();

        return $this;
    }

    public function clearResults()
    {
        $this->failedStatus = [];
        $this->spaces = [];
        $this->staff = [];
        $this->students = [];
    }

    /**
     * @param $id
     * @return boolean
     */
    public function canDelete($id)
    {
        $period = $this->pr->find($id);
        return $period->canDelete();
    }

    /**
     * @param PeriodActivity|null $activity
     * @return array
     */
    public function getActivityDetails(PeriodActivity $activity = null)
    {
        if (!$activity instanceof PeriodActivity) {
            $data = [];
            $data['%space%'] = '';
            $data['%tutor1%'] = '';
            $data['%tutor2%'] = '';
            $data['%tutor3%'] = '';
            return $data;
        }

        $data = [];
        $data['%space%'] = is_null($activity->getSpace()) ? '' : $activity->getSpace()->getName();
        $data['space_id'] = is_null($activity->getSpace()) ? '' : $activity->getSpace()->getId();
        $data['%tutor1%'] = is_null($activity->getTutor1()) ? '' : $activity->getTutor1()->getFullName();
        $data['tutor1_id'] = is_null($activity->getTutor1()) ? '' : $activity->getTutor1()->getId();
        $data['%tutor2%'] = is_null($activity->getTutor2()) ? '' : $activity->getTutor2()->getFullName();
        $data['tutor2_id'] = is_null($activity->getTutor2()) ? '' : $activity->getTutor2()->getId();
        $data['%tutor3%'] = is_null($activity->getTutor3()) ? '' : $activity->getTutor3()->getFullName();
        $data['tutor3_id'] = is_null($activity->getTutor3()) ? '' : $activity->getTutor3()->getId();

        return $data;
    }

    /**
     * @return int
     */
    public function getFailedCount()
    {
        return count($this->failedStatus);
    }

    /**
     * @param $id
     */
    public function generatePeriodReport($id)
    {
        $this->injectPeriod($id);

        if (!$this->period instanceof Period || is_null($this->period->getId()))
            throw new \InvalidArgumentException('The period has not been injected correctly.');

        $result = $this->om->getRepository(Space::class)->findBy([], ['name' => 'ASC']);

        $spaces = new ArrayCollection();
        foreach ($result as $space)
            $spaces->add($space);

        $return = new \stdClass();

        $return->spaces = $this->removeUsedSpaces($spaces);

        $result = $this->om->getRepository(Staff::class)->findAll();

        $staff = new ArrayCollection();
        foreach ($result as $member)
            $staff->add($member);

        $iterator = $staff->getIterator();
        $iterator->uasort(function ($a, $b) {
            return ($a->getFullName() < $b->getFullName()) ? -1 : 1;
        });
        $staff = new ArrayCollection(iterator_to_array($iterator, false));

        $return->staff = $this->removeUsedStaff($staff);

        return $return;
    }

    /**
     * Remove Used Spaces
     *
     * @param $spaces
     * @return mixed
     */
    private function removeUsedSpaces($spaces)
    {
        $used = $this->getSpaces();
        foreach ($used as $space)
            if ($spaces->contains($space))
                $spaces->removeElement($space);

        return $spaces;
    }

    /**
     * Get Spaces
     *
     * @return ArrayCollection
     */
    private function getSpaces()
    {
        $spaces = new ArrayCollection();

        $acts = $this->period->getActivities();

        foreach ($acts as $act)
            if ($act->getSpace() instanceof Space and !$spaces->contains($act->getSpace()))
                $spaces->add($act->getSpace());

        return $spaces;
    }

    /**
     * Remove Used Staff
     *
     * @param $spaces
     * @return mixed
     */
    private function removeUsedStaff($staff)
    {
        $used = $this->getStaff();

        foreach ($used as $member)
            if ($staff->contains($member))
                $staff->removeElement($member);


        return $staff;
    }

    /**
     * Get Staff
     *
     * @return ArrayCollection
     */
    private function getStaff()
    {
        $staff = new ArrayCollection();

        $acts = $this->period->getActivities();

        foreach ($acts as $act) {
            if ($act->getTutor1() instanceof Staff and !$staff->contains($act->getTutor1()))
                $staff->add($act->getTutor1());

            if ($act->getTutor2() instanceof Staff and !$staff->contains($act->getTutor2()))
                $staff->add($act->getTutor2());

            if ($act->getTutor3() instanceof Staff and !$staff->contains($act->getTutor3()))
                $staff->add($act->getTutor3());

        }

        return $staff;
    }

    /**
     * Get Period
     *
     * @return Period
     */
    public function getPeriod()
    {
        if (!$this->period instanceof Period)
            throw new \InvalidArgumentException('The period has not been injected.');
        return $this->period;
    }

    /**
     * @return \Busybee\TimeTableBundle\Entity\TimeTable
     */
    public function getTimeTable()
    {
        $x = $this->period->getColumn()->getTimetable();
        $x->getName();

        return $x;
    }

    /**
     * @param $line
     */
    public function injectLineGroup($line)
    {
        $line = $this->om->getRepository(Line::class)->find($line);

        $count = 0;

        $exists = new ArrayCollection();
        foreach ($this->period->getActivities() as $act)
            $exists->add($act->getActivity());

        foreach ($line->getActivities() as $activity)
            if (!$exists->contains($activity)) {
                $act = new PeriodActivity();
                $act->setPeriod($this->period);
                $act->setActivity($activity);
                $this->period->getActivities()->add($act);
                $count++;
            }

        if ($count > 0) {
            $this->om->persist($this->period);
            $this->om->flush();
            $this->flashbag->add('success', 'period.activities.line.added');
        } else
            $this->flashbag->add('warning', 'period.activities.line.none');

        return;

    }

    public function duplicatePeriod($source, $target)
    {
        $this->injectPeriod($source);

        $source = clone $this->period;

        $this->injectPeriod($target);

        $exists = new ArrayCollection();
        foreach ($this->period->getActivities() as $act)
            $exists->add($act->getActivity());

        $save = false;
        foreach ($source->getActivities() as $pa)
            if (!$exists->contains($pa->getActivity())) {
                $act = new PeriodActivity();
                $act->setActivity($pa->getActivity());
                $act->setPeriod($this->period);
                $act->setSpace($pa->getLocalSpace());
                $act->setTutor1($pa->getLocalTutor1());
                $act->setTutor2($pa->getLocalTutor2());
                $act->setTutor3($pa->getLocalTutor3());
                $this->period->addActivity($act);
                $save = true;
            }

        if ($save) {
            $this->om->persist($this->period);
            $this->om->flush();
        }
    }

    /**
     * Get Period Status
     *
     * @param $id Period ID
     * @return \stdClass
     */
    public function getPeriodStatus($id)
    {
        if (!empty($this->periodStatus->id) && $this->periodStatus->id === $id)
            return $this->periodStatus;

        $this->clearResults();

        $status = new \stdClass();
        $status->students = $this->generateFullPeriodReport($id);

        $status->alert = 'default';
        $status->disableDrop = '';
        $status->id = $id;
        $status->message = '';
        $status->messages = [];

        $problems = false;
        foreach ($this->period->getActivities() as $activity) {
            $report = $this->getActivityStatus($activity);
            if ($this->alert[$report->alert] > $this->alert[$status->alert]) {
                $status->alert = $report->alert;
                $problems = true;
                $status->messages = array_merge($status->messages, $report->messages);
            }
        }

        if ($problems) {
            $status->message .= ' ' . $this->translator->trans('period.activities.problems', [], 'BusybeeTimeTableBundle');
        }

        if (!$this->period->getBreak()) {
            foreach ($status->students->missingStudents as $q => $students) {
                if (count($students) > 0) {
                    $status->alert = 'danger';
                    $mess = $this->translator->trans('period.students.missing', ['%grade%' => $status->students->grades[$q]->getFullName()], 'BusybeeTimeTableBundle');
                    $status->message .= ' ' . $mess;
                    $status->messages[] = [$status->alert, $mess];
                }
            }
        }

        $this->periodStatus = $status;
        return $status;
    }

    /**
     * @param $id
     */
    public function generateFullPeriodReport($id)
    {
        $this->injectPeriod($id);
        $data = new \stdClass();

        $this->grades = $this->getGrades();

        $grades = [];
        foreach ($this->grades as $grade) {
            $data->grades[$grade->getId()] = $grade;
            $students = [];
            foreach ($grade->getStudents() as $student)
                $students[$student->getStudent()->getId()] = $student->getStudent();
            $grades[$grade->getId()] = $students;
        }

        foreach ($this->period->getActivities() as $q => $pa) {
            $act = $pa->getActivity();
            foreach ($act->getStudents() as $student) {
	            $grade = $student->getStudentCalendarGroup($this->currentYear);

                if ($grade instanceof Grade && isset($grades[$grade->getId()][$student->getId()]))
                    unset($grades[$grade->getId()][$student->getId()]);
            }
        }

        foreach ($grades as $q => $grade) {
            if (!empty($grade)) {
                $grade = new ArrayCollection($grade);
                $iterator = $grade->getIterator();
                $iterator->uasort(function ($a, $b) {
                    return ($a->formatName(['surnameFirst' => true, 'preferredOnly' => false]) < $b->formatName(['surnameFirst' => true, 'preferredOnly' => false])) ? -1 : 1;
                });
                $grades[$q] = iterator_to_array($iterator, true);
            }
        }


        $data->missingStudents = $grades;

        return $data;
    }

    /**
     * @return array
     */
    private function getGrades()
    {
        if (!empty($this->grades))
            return $this->grades;
        $grades = [];

        foreach ($this->gradeControl as $grade => $xxx)
            if ($xxx)
                $grades[] = $grade;

	    $this->grades = $this->om->getRepository(CalendarGroup::class)->createQueryBuilder('g')
            ->leftJoin('g.year', 'y')
            ->leftJoin('g.students', 'i')
            ->where('y.id = :year_id')
            ->setParameter('year_id', $this->currentYear->getId())
            ->select('g')
            ->addSelect('i')
            ->andWhere('i.status IN (:status)')
            ->setParameter('status', ['Future', 'Current'], Connection::PARAM_STR_ARRAY)
            ->andWhere('g.grade in (:grades)')
            ->setParameter('grades', $grades, Connection::PARAM_STR_ARRAY)
            ->getQuery()
            ->getResult();

        return $this->grades;
    }

    /**
     * @param $activity
     * @return \stdClass
     */
    public function getActivityStatus(PeriodActivity $activity = null)
    {
        if (!$activity instanceof PeriodActivity) {
            $status = new \stdClass();
            $status->class = 'default';
            $status->alert = 'default';
            $status->message = '';
            $status->id = null;
            $this->status = $status;
            return $status;
        }

        if (isset($this->status->id) && $this->status->id === $activity->getId())
            return $this->status;

        $this->status = new \stdClass();
        $this->status->id = $activity->getId();
        $this->status->alert = 'default';
        $this->status->class = '';
        $this->status->message = '';
        $this->status->messages = [];

        if (is_null($activity->getSpace())) {
            $this->status->class = ' alert-warning';
            $this->status->alert = 'warning';
            $this->status->message .= ' ' . $this->translator->trans('period.activities.activity.space.missing', [], 'BusybeeTimeTableBundle');
            $this->status->messages[] = ['warning', $this->translator->trans('period.activities.activity.space.missing', [], 'BusybeeTimeTableBundle')];
        } else {
            if (isset($this->spaces[$activity->getSpace()->getName()])) {
                $act = $this->spaces[$activity->getSpace()->getName()];
                $this->status->class = ' alert-warning';
                $this->status->alert = 'warning';
                $this->status->message .= ' ' . $this->translator->trans('period.activities.activity.space.duplicate', ['%space%' => $activity->getSpace()->getName(), '%activity%' => $activity->getFullName(), '%activity2%' => $act->getFullName()], 'BusybeeTimeTableBundle');
                $this->status->messages[] = ['warning', $this->translator->trans('period.activities.activity.space.duplicate', ['%space%' => $activity->getSpace()->getName(), '%activity%' => $activity->getFullName(), '%activity2%' => $act->getFullName()], 'BusybeeTimeTableBundle')];
            }
            $this->spaces[$activity->getSpace()->getName()] = $activity->getActivity();
        }

        if (is_null($activity->getTutor1())) {
            $this->status->class = ' alert-warning';
            $this->status->alert = 'warning';
            $this->status->message .= ' ' . $this->translator->trans('period.activities.activity.staff.missing', [], 'BusybeeTimeTableBundle');
            $this->status->messages[] = ['warning', $this->translator->trans('period.activities.activity.staff.missing', [], 'BusybeeTimeTableBundle')];
        } elseif (!is_null($activity->getTutor1()) && isset($this->staff[$activity->getTutor1()->getFullName()])) {
            $act = $this->staff[$activity->getTutor1()->getFullName()];
            $this->status->class = ' alert-warning';
            $this->status->alert = 'warning';
            $this->status->message .= ' ' . $this->translator->trans('period.activities.activity.staff.duplicate', ['%name%' => $activity->getTutor1()->getFullName(), '%activity%' => $activity->getFullName(), '%activity2%' => $act->getFullName()], 'BusybeeTimeTableBundle');
            $this->status->messages[] = ['warning', $this->translator->trans('period.activities.activity.staff.duplicate', ['%name%' => $activity->getTutor1()->getFullName(), '%activity%' => $activity->getFullName(), '%activity2%' => $act->getFullName()], 'BusybeeTimeTableBundle')];
        } elseif (!is_null($activity->getTutor1()) && !isset($this->staff[$activity->getTutor1()->getFullName()]))
            $this->staff[$activity->getTutor1()->getFullName()] = $activity->getActivity();

        if (!is_null($activity->getTutor2()) && isset($this->staff[$activity->getTutor2()->getFullName()])) {
            $act = $this->staff[$activity->getTutor2()->getFullName()];
            $this->status->class = ' alert-warning';
            $this->status->alert = 'warning';
            $this->status->message .= ' ' . $this->translator->trans('period.activities.activity.staff.duplicate', ['%name%' => $activity->getTutor1()->getFullName(), '%activity%' => $activity->getFullName(), '%activity2%' => $act->getFullName()], 'BusybeeTimeTableBundle');
            $this->status->messages[] = ['warning', $this->translator->trans('period.activities.activity.staff.duplicate', ['%name%' => $activity->getTutor1()->getFullName(), '%activity%' => $activity->getFullName(), '%activity2%' => $act->getFullName()], 'BusybeeTimeTableBundle')];
        } elseif (!is_null($activity->getTutor2()) && !isset($this->staff[$activity->getTutor2()->getFullName()]))
            $this->staff[$activity->getTutor2()->getFullName()] = $activity->getActivity();

        if (!is_null($activity->getTutor3()) && isset($this->staff[$activity->getTutor3()->getFullName()])) {
            $act = $this->staff[$activity->getTutor3()->getFullName()];
            $this->status->class = ' alert-warning';
            $this->status->alert = 'warning';
            $this->status->message .= ' ' . $this->translator->trans('period.activities.activity.staff.duplicate', ['%name%' => $activity->getTutor1()->getFullName(), '%activity%' => $activity->getFullName(), '%activity2%' => $act->getFullName()], 'BusybeeTimeTableBundle');
            $this->status->messages[] = ['warning', $this->translator->trans('period.activities.activity.staff.duplicate', ['%name%' => $activity->getTutor1()->getFullName(), '%activity%' => $activity->getFullName(), '%activity2%' => $act->getFullName()], 'BusybeeTimeTableBundle')];
        } elseif (!is_null($activity->getTutor3()) && !isset($this->staff[$activity->getTutor3()->getFullName()]))
            $this->staff[$activity->getTutor3()->getFullName()] = $activity->getActivity();

        if (!empty($this->status->message)) {
            $this->failedStatus[$this->status->id] = $this->status->alert;
            $this->status->message .= ' ' . $this->translator->trans('period.activities.activity.report.button', [], 'BusybeeTimeTableBundle');
            $this->status->message = trim($this->status->message);
        }

        return $this->status;
    }

    /**
     * @param $activity
     */
    public function injectActivityGroup($activity)
    {
        $activity = $this->om->getRepository(Activity::class)->find($activity);

        $exists = new ArrayCollection();
        foreach ($this->period->getActivities() as $act)
            $exists->add($act->getActivity());

        if (!$exists->contains($activity)) {
            $act = new PeriodActivity();
            $act->setPeriod($this->period);
            $act->setActivity($activity);
            $this->period->getActivities()->add($act);
            $this->flashbag->add('success', 'period.activities.line.added');
            $this->om->persist($this->period);
            $this->om->flush();
        } else
            $this->flashbag->add('warning', 'period.activities.line.none');

        return;

    }
}