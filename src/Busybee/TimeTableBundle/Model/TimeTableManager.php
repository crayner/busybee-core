<?php
namespace Busybee\TimeTableBundle\Model;

use Busybee\Core\CalendarBundle\Entity\SpecialDay;
use Busybee\Core\CalendarBundle\Entity\Term;
use Busybee\Core\CalendarBundle\Entity\Year;
use Busybee\StaffBundle\Entity\Staff;
use Busybee\ActivityBundle\Entity\Activity;
use Busybee\Core\SystemBundle\Setting\SettingManager;
use Busybee\TimeTableBundle\Entity\StartRotate;
use Busybee\TimeTableBundle\Entity\TimeTable;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\TranslatorInterface;

class TimeTableManager
{
    /**
     * @var \Busybee\Core\CalendarBundle\Entity\Year
     */
    private $year;

    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var SettingManager
     */
    private $sm;

    /**
     * @var TimeTable
     */
    private $timetable;

    /**
     * @var array
     */
    private $specialDays;

    /**
     * @var array
     */
    private $terms;

    /**
     * @var array
     */
    private $weeks;

    /**
     * @var array
     */
    private $days = [];

    /**
     * @var array
     */
    private $columns;
    /**
     * @var array
     */
    private $schoolDays;

    /**
     * @var array
     */
    private $startRotateDays;

    /**
     * @var array
     */
    private $schoolWeek;

    /**
     * @var string
     */
    private $firstDayofWeek;

    /**
     * @var string
     */
    private $dateFormat;

    /**
     * @var PeriodManager
     */
    private $pm;

    /**
     * @var \stdClass
     */
    private $report;

    /**
     * @var Session
     */
    private $sess;

    /**
     * @var array
     */
    private $gradeControl;

    /**
     * @var \stdClass
     */
    private $display;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var integer
     */
    private $schoolDayTime;


    private $statusLevel = [
        'default' => 0,
        'primary' => 1,
        'success' => 2,
        'info' => 4,
        'warning' => 8,
        'danger' => 16,
    ];

    /**
     * TimeTableDisplayManager constructor.
     * @param Year $year
     * @param ObjectManager $om
     * @param SettingManager $sm
     * @param PeriodManager $pm
     * @param Session $sess
     */
    public function __construct(Year $year, ObjectManager $om, SettingManager $sm, PeriodManager $pm, Session $sess, TranslatorInterface $translator)
    {
        $this->setYear($year);
        $this->om = $om;
        $this->sm = $sm;
        $this->translator = $translator;
        try {
            $this->timetable = $this->om->getRepository(TimeTable::class)->createQueryBuilder('t')
                ->leftJoin('t.year', 'y')
                ->where('y.id = :year_id')
                ->setParameter('year_id', $this->year->getId())
	            ->setMaxResults(1)
                ->getQuery()
                ->getSingleResult();
        } catch (\Exception $e) {
            $this->timetable = new TimeTable();
        }
        $this->schoolWeek = $this->sm->get('schoolWeek');
        $this->firstDayofWeek = $this->sm->get('firstDayofWeek');
        $this->pm = $pm;
        $this->sess = $sess;
        $this->gradeControl = $this->sess->get('gradeControl');
        $fin = new \DateTime('1970-01-01 ' . $this->sm->get('SchoolDay.Finish'));
        $sta = new \DateTime('1970-01-01 ' . $this->sm->get('SchoolDay.Begin'));
        $this->schoolDayTime = ($fin->getTimestamp() - $sta->getTimestamp()) / 60;
    }

    /**
     * @param $date
     */
    public function toggleRotateStart($date)
    {
        if (!$this->testDate($date))
            return;

        $date = new \DateTime($date);

        $rd = $this->om->getRepository(StartRotate::class)->findOneBy(['day' => $date]);

        if (is_null($rd)) {
            $rd = new StartRotate();
            $rd->setDay($date);
            $this->om->persist($rd);
            $removed = 'create';
        } else {
            $this->om->remove($rd);
            $removed = 'remove';
        }

        $this->om->flush();
        $this->resetManager();

        return $removed;
    }

    /**
     * @param $date
     * @return bool
     */
    public function testDate($date)
    {
        $date = new \DateTime($date);

        $result = $this->om->getRepository(Year::class)->createQueryBuilder('y')
            ->where('y.firstDay <= :dates')
            ->andWhere('y.lastDay >= :datel')
            ->setParameter('dates', $date)
            ->setParameter('datel', $date)
            ->getQuery()
            ->getResult();

        if (empty($result) || count($result) != 1)
            return false;
        if ($result[0] != $this->year)
            return false;
        return true;
    }

    /**
     * Reset Manager
     */
    private function resetManager()
    {
        $this->startRotateDays = [];
        $this->terms = [];
    }

    /**
     * @return TimeTable
     */
    public function getTimeTable()
    {
        return $this->timetable;
    }

    /**
     * @param TimeTable $tt
     * @return TimeTableManager
     */
    public function setTimeTable(TimeTable $tt)
    {
        $this->timetable = $tt;

        return $this;
    }

    /**
     * Get Report
     *
     * @return null|\stdClass
     */
    public function getReport(PeriodPagination $pag)
    {
        if ($this->report instanceof \stdClass)
            return $this->report;

        return $this->generateReport($pag);
    }

    /**
     * Generate Report
     *
     * @return \stdClass
     * @throws \Exception
     */
    private function generateReport(PeriodPagination $pag)
    {
        if (!$this->timetable instanceof TimeTable)
            throw new \Exception('The time table has not been injected into the manager.');

        $this->report = new \stdClass();

        $this->report->periods = [];
        $this->report->activities = [];
        $this->report->staff = [];

        foreach ($pag->getResult() as $period) {
            $per = new \stdClass();
            $per->status = $this->pm->getPeriodStatus($period['id']);
            $per->period = $period['0'];
            $per->id = $period['id'];
            $per->name = $period['name'];
            $per->start = $period['start'];
            $per->end = $period['end'];
            $per->nameShort = $period['nameShort'];
            $per->columnName = $period['columnName'];
            $per->activities = [];

            $this->pm->clearResults();

            foreach ($per->period->getActivities() as $activity) {
                if ($this->activeGrade($activity)) {
                    $act = new \stdClass();
                    $act->activity = $activity;
                    $act->details = $this->pm->getActivityDetails($activity);
                    $act->status = $this->pm->getActivityStatus($activity);
                    $act->id = $activity->getId();
                    $act->fullName = $activity->getFullName();
                    $per->activities[] = $act;
                    if ($activity->getActivity() instanceof Activity) {
                        if (isset($this->report->activities[$activity->getActivity()->getId()])) {
                            $act = $this->report->activities[$activity->getActivity()->getId()];
                        } else {
                            $act = $activity->getActivity();
                        }

                        $this->report->activities[$activity->getActivity()->getId()] = $act;

                    }
                }
                if ($activity->getTutor1()) {
                    $this->getStaffReport($activity->getTutor1(), $per);
                }
                if ($activity->getTutor2()) {
                    $this->getStaffReport($activity->getTutor2(), $per);
                }
                if ($activity->getTutor3()) {
                    $this->getStaffReport($activity->getTutor3(), $per);
                }
            }

            $this->report->periods[] = $this->setPeriodStatusLevel($per);
        }

        return $this->report;
    }

    /**
     * @param $activity
     * @return bool
     */
    private function activeGrade($activity)
    {
        foreach ($activity->getActivity()->getGrades() as $grade)
            if (!isset($this->gradeControl[$grade->getGrade()]) || $this->gradeControl[$grade->getGrade()])
                return true;

        return false;
    }

    /**
     * @return \stdClass
     */
    public function getTTYear()
    {
        $year = new \stdClass();
        $year->tt = $this->timetable;
        $year->status = 'success';
        $year->terms = [];
        $year->weeks = [];

        if ($this->timetable->getColumns()->count() == 0 || $this->timetable->getDays()->count() == 0) {
            $year->status = 'failure';
            $year->message = 'timetable.year.notconfigured';
            return $year;
        }

        $dayOfWeekFormat = $this->firstDayofWeek == 'Sunday' ? 'w' : 'N';
        $daysOfWeek = $this->schoolWeek;
        $this->getSpecialDays();

        $terms = $this->getTerms();

        $term = reset($terms);
        if ($term instanceof Term)
            $term->weeks = [];
        $week = [];
        $lastDayOfWeek = 0;

        $y = clone $this->year->getFirstDay();

        do {
            $day = new \stdClass();
            $day->date = clone $y;
            if ($term instanceof Term && $y >= $term->getFirstDay() && $y <= $term->getLastDay()) {
                if ($day->date->format($dayOfWeekFormat) < $lastDayOfWeek) {
                    $week = $this->validateWeek($week);
                    $term->weeks[] = $week;
                    $year->weeks[] = $week;
                    $week = [];
                }
                $lastDayOfWeek = $day->date->format($dayOfWeekFormat);
                if (in_array($day->date->format('D'), $daysOfWeek)) {
                    $day->startRotate = $this->isStartRotate($day);
                    $day->useDay = true;
                    if (!empty($this->specialDays[$day->date->format('Ymd')]))
                        $day->specialDay = $this->specialDays[$day->date->format('Ymd')];
                    $week[$lastDayOfWeek] = $day;
                }
            }
            if ($term instanceof Term && $y > $term->getLastDay()) {
                if (!empty($week)) {
                    $term->weeks[] = $week;
                    $year->weeks[] = $week;
                }
                $year->terms[$term->getName()] = $term;
                $term = next($terms);
                $lastDayOfWeek = 0;
                $week = [];
                if ($term instanceof Term)
                    $term->weeks = [];
            }
            $y->add(new \DateInterval('P1D'));
        } while ($y <= $this->year->getLastDay());

        if ($term instanceof Term && !empty($week))
            $term->weeks[] = $week;

        if (!empty($week))
            $year->weeks[] = $week;


        $this->getTimeTableDays();

        try {
            $year = $this->mapDays($year);

        } catch (\Exception $e) {
            //throw new \Exception($e->getMessage());
            $year->status = 'failure';
            $year->message = 'timetable.year.mapdaysfailed';
            $year->options = ['%message%' => $e->getMessage()];
            $year->error = $e;
        }

        return $year;
    }

    /**
     * @return array
     */
    public function getSpecialDays()
    {
        if (!empty($this->specialDays))
            return $this->specialDays;

        $days = $this->om->getRepository(SpecialDay::class)->createQueryBuilder('s')
            ->leftJoin('s.year', 'y')
            ->where('y.id = :year_id')
            ->setParameter('year_id', $this->year->getId())
            ->orderBy('s.day', 'ASC')
            ->getQuery()
            ->getResult();
        $this->specialDays = [];

        foreach ($days as $day)
            $this->specialDays[$day->getDay()->format('Ymd')] = $day;

        return $this->specialDays;

    }

    /**
     * @return array
     */
    public function getTerms()
    {
        if (!empty($this->terms))
            return $this->terms;

        $result = $this->om->getRepository(Term::class)->createQueryBuilder('t')
            ->leftJoin('t.year', 'y')
            ->where('y.id = :year_id')
            ->setParameter('year_id', $this->year->getId())
            ->orderBy('t.firstDay', 'ASC')
            ->getQuery()
            ->getResult();

        foreach ($result as $term)
            $this->terms[$term->getName()] = $term;


        return $this->terms;
    }

    /**
     * @param $week
     * @return mixed
     */
    private function validateWeek($week)
    {
        $schoolDays = $this->schoolWeek;
        if (count($week) == count($schoolDays))
            return $week;

        $days = [];
        $day = new \DateTime('now');

        while (empty($days[$day->format('w')])) {
            $days[$day->format('w')] = $day->format('D');
            $day->add(new \DateInterval('P1D'));
        }

        $days[7] = $days[0];
        $day = new \stdClass();
        $day->date = 'blank';
        $day->useDay = false;
        $startOfWeek = clone reset($week)->date;
        $startOfWeek->sub(new \DateInterval('P' . $startOfWeek->format('w') . 'D'));

        foreach ($days as $q => $w) {
            if (!in_array($w, $schoolDays))
                unset($days[$q]);
            elseif (!array_key_exists($q, $week)) {
                $day->date = clone $startOfWeek;
                $day->date->add(new \DateInterval('P' . $q . 'D'));
                $day->specialDay = 'blank';
                $day->startRotate = false;
                $day->useDay = true;
                $week[$q] = $day;
            }
        }

        ksort($week);

        return $week;
    }

    /**
     * @param $day
     * @return bool
     */
    private function isStartRotate($day)
    {
        if (!empty($this->getStartRotateDays()[$day->date->format('Ymd')]))
            return true;
        return false;
    }

    /**
     * @return array
     */
    public function getStartRotateDays()
    {
        if (!empty($this->startRotateDays))
            return $this->startRotateDays;

        $days = $this->om->getRepository(StartRotate::class)->createQueryBuilder('s')
            ->where('s.day >= :firstDay')
            ->setParameter('firstDay', $this->year->getFirstDay())
            ->andWhere('s.day <= :lastDay')
            ->setParameter('lastDay', $this->year->getLastDay())
            ->orderBy('s.day', 'ASC')
            ->getQuery()
            ->getResult();

        $this->startRotateDays = [];

        foreach ($days as $day)
            $this->startRotateDays[$day->getDay()->format('Ymd')] = $day->getDay();

        if (empty($this->startRotateDays))
            $this->startRotateDays['19000101'] = '19000101';


        return $this->startRotateDays;

    }

    /**
     * Get Timetable Days
     */
    private function getTimeTableDays()
    {
        $this->columns = [];

        if ($this->timetable->getColumns()->count() > 0) {
            foreach ($this->timetable->getColumns()->toArray() as $key => $col) {
                if ($col->getMappingInfo() == 'Rotate') {
                    $this->columns['rotate'][$col->getNameShort()] = $key;
                } else {
                    $this->columns['fixed'][$col->getNameShort()] = $key;
                }
            }
        }

        $this->schoolDays = [];

        if ($this->timetable->getDays()->count() > 0) {
            foreach ($this->timetable->getDays()->toArray() as $key => $day)
                if ($day->getDayType())
                    $this->schoolDays[strtoupper($day->getName())] = 'rotate';
                else
                    $this->schoolDays[strtoupper($day->getName())] = 'fixed';
        }
    }

    /**
     * @param \stdClass $year
     * @return \stdClass
     */
    private function mapDays(\stdClass $year)
    {
        foreach ($year->terms as $t => $term) {
            if (!empty($this->columns['rotate']) && is_array($this->columns['rotate'])) {
                $col = reset($year->columns['rotate']);

                foreach ($term->weeks as $w => $week) {
                    foreach ($week as $d => $day) {
                        if (in_array($day->date, $this->getStartRotateDays()))
                            $col = reset($this->columns['rotate']);
                        if ($day->useDay) {
                            $code = strtoupper($day->date->format('D'));
                            if ($this->schoolDays[$code] == 'rotate') {
                                $day->ttday = $this->timetable->getColumns()->get($col);

                                $col = next($this->columns['rotate']);
                                if (false === $col)
                                    $col = reset($this->columns['rotate']);

                            } else {
                                $day->ttday = $this->timetable->getColumns()->get($this->columns['fixed'][$code]);
                            }
                            if (in_array($day->date, $this->getStartRotateDays()))
                                $day->startRotate = true;

                            $this->terms[$t]->weeks[$w][$d] = $day;
                        }
                    }
                }
            } else {
                // Handle if system is set as all fixed.
                foreach ($term->weeks as $w => $week) {
                    foreach ($week as $d => $day) {
                        if ($day->useDay) {
                            $code = strtoupper($day->date->format('D'));
                            $day->ttday = $this->timetable->getColumns()->get($this->columns['fixed'][$code]);
                            $this->terms[$t]->weeks[$w][$d] = $day;
                        }
                    }
                }
            }
        }
        return $year;
    }

    /**
     * @return stdClass
     */
    public function getDisplay()
    {
        return $this->display;
    }

    public function getDayHours()
    {
        $hours = [];

        $time = new \DateTime('1970-01-01 ' . $this->sm->get('schoolDay.begin'));

        do {
            $hours[] = $time->format($this->sm->get('time.format.short'));

            $time->add(new \DateInterval('PT1H'));

        } while ($time < new \DateTime('1970-01-01 ' . $this->sm->get('schoolDay.finish')));

        return $hours;
    }

    public function isCurrentTime($day, $period)
    {
        if ($day->format('Ymd') === date('Ymd')) {
            if ($period->getStart()->format('Hi') <= date('Hi') && $period->getEnd()->format('Hi') > date('Hi'))
                return true;
        }

        return false;
    }

    /**
     * @return ObjectManager
     */
    public function getOm()
    {
        return $this->om;
    }

    /**
     * @return SettingManager
     */
    public function getSm()
    {
        return $this->sm;
    }

    /**
     * @return string
     */
    public function getFirstDayofWeek()
    {
        return $this->firstDayofWeek;
    }

    /**
     * @return array
     */
    public function getWeeks(): array
    {
        return empty($this->weeks) ? [] : $this->weeks;
    }

    /**
     * @param \stdClass $week
     * @return TimeTableManager
     */
    public function addWeek(\stdClass $week)
    {
        // remove none school days
        foreach ($week->days as $q => $day) {
            if (!in_array($day->date->format('D'), $this->schoolWeek))
                unset($week->days[$q]);
        }

        $this->weeks[] = $week;

        return $this;
    }

    /**
     * @return TimeTableManager
     */
    public function clearWeeks()
    {
        $this->weeks = [];

        return $this;
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

        return $this;
    }

    /**
     * Get School Day Time
     * in Minutes
     *
     * @return integer
     */
    public function getSchoolDayTime(): int
    {
        return $this->schoolDayTime;
    }

    /**
     * @return Session
     */
    public function getSession()
    {
        return $this->sess;
    }

    /**
     * @param Staff $tutor
     * @param \stdClass $per
     */
    private function getStaffReport(Staff $tutor, \stdClass $per)
    {
        $id = $tutor->getId();
        if (empty($this->report->staff[$id]['status']))
            $this->report->staff[$id]['status'] = 'ok';
        $this->report->staff[$id]['staff'] = $tutor;
        $this->report->staff[$id]['period'][$per->period->getColumn()->getId()][$per->id] = $per->period;
        $this->report->staff[$id]['total'] = empty($this->report->staff[$id]['total']) ? 1 : $this->report->staff[$id]['total'] + 1;
        if (count($this->report->staff[$id]['period'][$per->period->getColumn()->getId()]) == $this->getSm()->get('teachingload.column.maximum', 2)) {
            $this->report->staff[$id]['status'] = 'info';
            $this->report->staff[$id]['message'] = $this->translator->trans('teachingload.column.equal', ['%name%' => $tutor->formatName()], 'BusybeeTimeTableBundle');
            $per->status->alert = 'info';
            $per->status->messsage = ['info', $this->report->staff[$id]['message']];
            $per->status->messages[] = $per->status->messsage;
        }
        if ($this->report->staff[$id]['total'] == $this->getSm()->get('teachingload.timetable.maximum', 9)) {
            $this->report->staff[$id]['status'] = 'info';
            $this->report->staff[$id]['message'] = $this->translator->trans('teachingload.timetable.equal', ['%name%' => $tutor->formatName()], 'BusybeeTimeTableBundle');
            $per->status->alert = 'info';
            $per->status->messsage = ['info', $this->report->staff[$id]['message']];
            $per->status->messages[] = $per->status->messsage;
        }
        if (count($this->report->staff[$id]['period'][$per->period->getColumn()->getId()]) > $this->getSm()->get('teachingload.column.maximum', 2)) {
            $this->report->staff[$id]['status'] = 'danger';
            $this->report->staff[$id]['message'] = $this->translator->trans('teachingload.column.exceeded', ['%name%' => $tutor->formatName()], 'BusybeeTimeTableBundle');
            $per->status->alert = 'danger';
            $per->status->messsage = ['danger', $this->report->staff[$id]['message']];
            $per->status->messages[] = $per->status->messsage;
        }
        if ($this->report->staff[$id]['total'] > $this->getSm()->get('teachingload.timetable.maximum', 9)) {
            $this->report->staff[$id]['status'] = 'danger';
            $this->report->staff[$id]['message'] = $this->translator->trans('teachingload.timetable.exceeded', ['%name%' => $tutor->formatName()], 'BusybeeTimeTableBundle');
            $per->status->alert = 'danger';
            $per->status->messsage = ['danger', $this->report->staff[$id]['message']];
            $per->status->messages[] = $per->status->messsage;
        }
    }

    /**
     * @param string $status
     * @return int|mixed
     */
    public function getStatusLevel($status): int
    {
        if (!empty($this->statusLevel[strtolower($status)]))
            return intval($this->statusLevel[strtolower($status)]);
        return 0;
    }

    /**
     * @param $per
     * @return mixed
     */
    private function setPeriodStatusLevel($per)
    {
        $status = $per->status->alert;

        foreach ($per->status->messages as $message)
            if ($this->getStatusLevel($message[0]) > $this->getStatusLevel($status))
                $status = $message[0];

        foreach ($per->activities as $activity)
            foreach ($activity->status->messages as $message)
                if ($this->getStatusLevel($message[0]) > $this->getStatusLevel($status))
                    $status = $message[0];

        $per->status->alert = $status;

        return $per;
    }
}