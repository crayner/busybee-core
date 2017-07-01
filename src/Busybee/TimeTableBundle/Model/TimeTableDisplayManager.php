<?php

namespace Busybee\TimeTableBundle\Model;


use Busybee\HomeBundle\Exception\Exception;
use Busybee\InstituteBundle\Entity\Grade;
use Busybee\InstituteBundle\Entity\Term;
use Busybee\InstituteBundle\Entity\Year;
use Busybee\StudentBundle\Entity\Activity;
use Busybee\StudentBundle\Entity\Student;
use Busybee\SystemBundle\Setting\SettingManager;
use Busybee\TimeTableBundle\Entity\Column;
use Busybee\TimeTableBundle\Entity\Line;
use Busybee\TimeTableBundle\Entity\Period;
use Busybee\TimeTableBundle\Entity\PeriodActivity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Session\Session;

class TimeTableDisplayManager extends TimeTableManager
{
    /**
     * @var string
     */
    private $title = 'timetable.display.title';

    /**
     * @var string
     */
    private $description = 'timetable.display.description';

    /**
     * @var string
     */
    private $identifier = '';

    /**
     * @var string
     */
    private $type = '';

    /**
     * @var array
     */
    private $week = [];

    /**
     * @var array
     */
    private $types = [
        'grad' => 'Grade',
        'stud' => 'Student',
    ];

    /**
     * @var \DateTime
     */
    private $displayDate;

    /**
     * @var Term
     */
    private $term;

    /**
     * @var int
     */
    private $weekNumber;

    /**
     * @var array
     */
    private $studentActivities;

    /**
     * @var integer
     */
    private $studentIdentifier;

    /**
     * @var string
     */
    private $idDesc;

    /**
     * TimeTableDisplayManager constructor.
     * @param Year $year
     * @param ObjectManager $om
     * @param SettingManager $sm
     * @param PeriodManager $pm
     * @param Session $sess
     */
    public function __construct(Year $year, ObjectManager $om, SettingManager $sm, PeriodManager $pm, Session $sess)
    {
        parent::__construct($year, $om, $sm, $pm, $sess);
        $this->studentActivities = new ArrayCollection();
        $this->studentIdentifier = 0;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return TimeTableDisplayManager
     */
    public function setTitle(string $title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return TimeTableDisplayManager
     */
    public function setDescription(string $description): TimeTableDisplayManager
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Term
     */
    public function getTerm()
    {
        return $this->term;
    }

    /**
     * @param Term $term
     */
    public function setTerm(Term $term = null): TimeTableDisplayManager
    {
        $this->term = $term;

        return $this;
    }

    /**
     * @return int
     */
    public function getWeekNumber()
    {
        return $this->weekNumber;
    }

    /**
     * @param int $weekNumber
     */
    public function setWeekNumber(int $weekNumber): TimeTableDisplayManager
    {
        $this->weekNumber = $weekNumber;

        return $this;
    }

    /**
     * Generate TimeTable
     *
     * @param $identifier
     * @param $displayDate
     */
    public function generateTimeTable($identifier, $displayDate)
    {
        $this->parseIdentifier($identifier);

        $this->setDisplayDate(new \DateTime($displayDate))
            ->generateWeeks();

        $dayDate = $this->getDisplayDate()->format('Ymd');
        foreach ($this->getWeeks() as $q => $week) {
            if ($week->start->format('Ymd') <= $dayDate && $week->finish->format('Ymd') >= $dayDate) {
                $this->setWeek($week);
                break;
            }
        }
        $this->mapCalendarWeek();

        $actSearch = 'generate' . ucfirst($this->gettype()) . 'Activities';
        foreach ($this->getWeek()->days as $q => $day) {
            $day->class = '';
            foreach ($day->ttday->getPeriods() as $p => $period)
                $period->activity = $this->$actSearch($period);
            if (isset($day->specialDay))
                $day = $this->manageSpecialDay($day);
        }

        $this->today = new \DateTime('today');
    }

    /**
     * Parse Identifier
     *
     * @param $identifier
     * @return TimeTableDisplayManager
     */
    protected function parseIdentifier($identifier): TimeTableDisplayManager
    {
        $this->setType(substr($identifier, 0, 4));
        $this->setIdentifier(substr($identifier, 4));

        switch ($this->getType()) {
            case 'Grade':
                if (empty($this->getIdDesc())) {
                    $this->setIdDesc($this->getOm()->getRepository(Grade::class)->findOneByGrade($this->getIdentifier())->getName());
                }
                break;
            case 'Student':
                if (empty($this->getIdDesc())) {
                    $this->setIdDesc($this->getOm()->getRepository(Student::class)->find($this->getIdentifier())->getFormatName(['surnameFirst' => false, 'preferredOnly' => true]));
                }
                break;
        }
        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set Type
     *
     * @param string $type
     * @return TimeTableDisplayManager
     * @throws
     */
    public function setType(string $type): TimeTableDisplayManager
    {
        if (isset($this->types[$type]))
            $this->type = $this->types[$type];
        else
            throw new Exception('The calendar type (' . $type . ') has not been defined.');

        return $this;
    }

    /**
     * @return string
     */
    public function getIdDesc()
    {
        return $this->idDesc;
    }

    /**
     * Set Id Desc
     * @param string $desc
     * @return TimeTableDisplayManager
     */
    public function setIdDesc($desc)
    {
        $this->idDesc = $desc;

        return $this;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     * @return TimeTableDisplayManager
     */
    public function setIdentifier(string $identifier): TimeTableDisplayManager
    {
        $this->identifier = $identifier;

        return $this;
    }

    private function generateWeeks()
    {
        $this->clearWeeks();
        $week = new \stdClass();
        $week->days = [];
        $day = clone $this->getYear()->getFirstDay();
        $week->start = clone $day;
        $week->finish = clone $day;
        $week->weekNumber = 1;
        $weekNum = 1;
        $week->title = 'Hol';

        do {
            if ($day->format('l') === $this->getFirstDayofWeek() && !empty($week->days)) {
                $this->addWeek($week);
                $week = new \stdClass();
                $week->days = [];
                $week->start = clone $day;
                $week->finish = clone $day;
                $week->weekNumber = $weekNum++;
                $week->title = 'Hol';
            }
            $d = new \stdClass();
            $d->date = clone $day;
            $d->day = $d->date->format('l');
            $d->ttday = new Column();
            $d->ttday->setNameShort($d->date->format('D'));
            $week->days[] = $d;
            $week->finish = clone $day;

            $day->add(new \DateInterval('P1D'));
        } while ($day <= $this->getYear()->getLastDay());

        if (!empty($week->days))
            $this->addWeek($week);

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDisplayDate(): \DateTime
    {
        return $this->displayDate;
    }

    /**
     * @param \DateTime $displayDate
     */
    public function setDisplayDate(\DateTime $displayDate): TimeTableDisplayManager
    {
        if ($displayDate < $this->getYear()->getFirstDay())
            $displayDate = $this->getYear()->getFirstDay();

        if ($displayDate > $this->getYear()->getLastDay())
            $displayDate = $this->getYear()->getLastDay();

        $this->displayDate = $displayDate;

        return $this;
    }

    /**
     * Map Calendar Week
     */
    private function mapCalendarWeek()
    {
        $start = clone reset($this->getWeek()->days)->date;
        $term = null;
        foreach ($this->getTTYear()->terms as $x) {
            if ($start >= $x->getFirstDay() && $start <= $x->getLastDay()) {
                $term = $x;
                break;
            }
        }
        $week = [];
        if ($term instanceof Term) {
            foreach ($term->weeks as $w) {
                $first = reset($w)->date;
                if ($start == $first) {
                    $week = $w;
                    break;
                }
            }
        }

        if (!empty($week)) {
            foreach ($week as $d) {
                foreach ($this->getWeek()->days as $q => $day) {
                    if ($d->date == $day->date) {
                        $day->ttday = clone $d->ttday;
                        if (isset($d->specialDay))
                            $day->specialDay = clone $d->specialDay;
                        break;
                    }
                }
            }
            $this->getWeek()->title = $term->getNameShort();
        }
    }

    /**
     * @return array
     */
    public function getWeek(): \stdClass
    {
        return $this->week;
    }

    /**
     * @param \stdClass $week
     * @return TimeTableDisplayManager
     */
    public function setWeek(\stdClass $week): TimeTableDisplayManager
    {
        $this->week = $week;

        return $this;
    }

    /**
     * @param $day
     * @return mixed
     */
    private function manageSpecialDay($day)
    {
        if ($day->specialDay->getType() === 'closure') {
            foreach ($day->ttday->getPeriods() as $period)
                $day->ttday->removePeriod($period);
            $period = new Period();
            $period->setName($day->specialDay->getName());
            $period->description = $day->specialDay->getDescription();

            $period->setStart(new \DateTime('1970-01-01 ' . $this->getSm()->get('schoolDay.begin')));
            $period->setEnd(new \DateTime('1970-01-01 ' . $this->getSm()->get('schoolDay.finish')));


            $day->ttday->addPeriod($period);
            $period->class = ' calendarClosure';
        }
        if ($day->specialDay->getType() === 'alter') {
            foreach ($day->ttday->getPeriods() as $period) {
                if ($period->getStart() >= $day->specialDay->getStart() && $period->getEnd() <= $day->specialDay->getFinish())
                    $day->ttday->removePeriod($period);
                elseif ($period->getStart() < $day->specialDay->getStart() && $period->getEnd() > $day->specialDay->getFinish())
                    $period->setEnd($day->specialDay->getStart());
                elseif ($period->getStart() < $day->specialDay->getFinish() && $period->getEnd() > $day->specialDay->getFinish())
                    $period->setStart($day->specialDay->getFinish());
                elseif ($period->getStart() < $day->specialDay->getStart() && $period->getEnd() > $day->specialDay->getStart())
                    $period->setEnd($day->specialDay->getStart());
            }
            $period = new Period();
            $period->setName($day->specialDay->getName());
            $period->description = $day->specialDay->getDescription();

            $period->setStart($day->specialDay->getStart());
            $period->setEnd($day->specialDay->getFinish());

            $day->ttday->addPeriod($period);
            $period->class = ' calendarAlter';

            $day->ttday->getPeriods(true);
        }

        return $day;
    }

    /**
     * @param $period
     * @return null|PeriodActivity
     */
    private function generateGradeActivities($period)
    {
        foreach ($period->getActivities() as $activity) {
            foreach ($activity->getActivity()->getGrades() as $grade)
                if ($grade->getGrade() === $this->getIdentifier()) {
                    $x = $this->getOm()->getRepository(Line::class)->findByActivity($activity->getActivity()->getId());
                    if (!is_null($x)) {
                        return $x;
                    }
                    return $activity;
                }
        }


        return null;
    }

    /**
     * @param $period
     * @return null|PeriodActivity
     */
    private function generateStudentActivities($period)
    {
        // test and load student activities only once.
        if ($this->studentActivities->count() === 0 && $this->studentIdentifier !== $this->getIdentifier()) {
            $acts = $this->getOm()->getRepository(Activity::class)->findByStudent($this->getIdentifier(), $this->getYear());
            $this->studentActivities = new ArrayCollection();
            $this->studentIdentifier = $this->getIdentifier();
            foreach ($acts as $w)
                if (!$this->studentActivities->contains($w))
                    $this->studentActivities->add($w);
        }

        foreach ($period->getActivities() as $activity) {
            if ($this->studentActivities->contains($activity->getActivity())) {
                return $activity;
            }
        }

        return null;
    }
}