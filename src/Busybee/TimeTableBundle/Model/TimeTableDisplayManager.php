<?php

namespace Busybee\TimeTableBundle\Model;


use Busybee\Core\HomeBundle\Exception\Exception;
use Busybee\Core\CalendarBundle\Entity\Grade;
use Busybee\InstituteBundle\Entity\Space;
use Busybee\Core\CalendarBundle\Entity\Term;
use Busybee\Core\CalendarBundle\Entity\Year;
use Busybee\People\PersonBundle\Model\PersonManager;
use Busybee\Core\SecurityBundle\Entity\User;
use Busybee\Core\SecurityBundle\Model\UserInterface;
use Busybee\People\StaffBundle\Entity\Staff;
use Busybee\ActivityBundle\Entity\Activity;
use Busybee\People\StudentBundle\Entity\Student;
use Busybee\Core\SystemBundle\Setting\SettingManager;
use Busybee\TimeTableBundle\Entity\Column;
use Busybee\TimeTableBundle\Entity\Line;
use Busybee\TimeTableBundle\Entity\Period;
use Busybee\TimeTableBundle\Entity\PeriodActivity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\TranslatorInterface;

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
        'staf' => 'Staff',
        'spac' => 'Space',
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
     * @var array
     */
    private $staffActivities;

    /**
     * @var integer
     */
    private $staffIdentifier;

    /**
     * @var array
     */
    private $spaceActivities;

    /**
     * @var integer
     */
    private $spaceIdentifier;

    /**
     * @var string
     */
    private $idDesc;

    /**
     * @var PersonManager
     */
    private $personManager;

    /**
     * @var boolean
     */
    private $isTimeTable;

    /**
     * @var string
     */
    private $header = '';

    /**
     * TimeTableDisplayManager constructor.
     * @param Year $year
     * @param ObjectManager $om
     * @param SettingManager $sm
     * @param PeriodManager $pm
     * @param Session $sess
     */
    public function __construct(Year $year, ObjectManager $om, SettingManager $sm, PeriodManager $pm, Session $session, PersonManager $personManager, TranslatorInterface $translator)
    {
        parent::__construct($year, $om, $sm, $pm, $session, $translator);
        $this->studentActivities = new ArrayCollection();
        $this->studentIdentifier = 0;
        $this->staffActivities = new ArrayCollection();
        $this->staffIdentifier = 0;
        $this->spaceActivities = new ArrayCollection();
        $this->spaceIdentifier = 0;
        $this->personManager = $personManager;
        $this->isTimeTable = true;
        if (empty($this->getTimeTable()->getId()))
            $this->isTimeTable = false;
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
	public function getDescription($translated = false): string
    {
	    if (!$translated)
		    return $this->description;

	    return $this->getTranslator()->trans($this->description, ['%type%' => $this->getType(), '%identifier%' => $this->getIdDesc()], 'BusybeeTimeTableBundle');
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
        if (false === $this->isTimeTable || empty($identifier))
            return;

        if (!$this->parseIdentifier($identifier))
            return;

        $this->getSession()->set('tt_displayDate', $displayDate);

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
     * @param string $identifier
     * @return bool
     */
    protected function parseIdentifier(string $identifier): bool
    {
        if (false === $this->isTimeTable)
            return $this->isTimeTable;

        if (is_null($identifier) || strlen($identifier) < 5)
            return $this->isTimeTable = false;

        if (!$this->setType(substr($identifier, 0, 4)))
            return $this->isTimeTable = false;

        if (!$this->setIdentifier(substr($identifier, 4)))
            return $this->isTimeTable = false;

        $this->header = 'timetable.header.blank';
        switch ($this->getType()) {
            case 'Grade':
                if (empty($this->getIdDesc())) {
                    $this->setIdDesc($this->getOm()->getRepository(Grade::class)->findOneByGrade($this->getIdentifier())->getName());
                }
                $this->header = 'timetable.header.grade';
                break;
            case 'Student':
                $this->studentIdentifier = $this->getIdentifier();
                if (empty($this->getIdDesc())) {
                    $this->setIdDesc($this->getOm()->getRepository(Student::class)->find($this->studentIdentifier)->formatName(['surnameFirst' => false, 'preferredOnly' => true]));
                }
                $this->header = 'timetable.header.student';
                break;
            case 'Staff':
                $this->staffIdentifier = $this->getIdentifier();
                if (empty($this->getIdDesc())) {
                    $this->setIdDesc($this->getOm()->getRepository(Staff::class)->find($this->staffIdentifier)->formatName(['surnameFirst' => true, 'preferredOnly' => true]));
                }
                $this->header = 'timetable.header.staff';
                break;
            case 'Space':
                $this->spaceIdentifier = $this->getIdentifier();
                if (empty($this->getIdDesc())) {
                    $this->setIdDesc($this->getOm()->getRepository(Space::class)->find($this->spaceIdentifier)->getNameCapacity());
                }
                $this->header = 'timetable.header.space';
                break;
            default:
                throw new Exception('The TimeTable Type ' . $this->getType() . ' is not defined.');
        }
        $this->isTimeTable = true;

        $this->getSession()->set('tt_identifier', $identifier);

        return $this->isTimeTable;
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
     * @return bool
     */
    public function setType(string $type): bool
    {

        if (isset($this->types[$type])) {
            $this->type = $this->types[$type];
        } else
            $this->isTimeTable = false;

        return true;
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
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set Identifier
     *
     * @param string $identifier
     * @return bool
     */
    public function setIdentifier(string $identifier): bool
    {
        $this->identifier = $identifier;

        return true;
    }

    /**
     * Generate Weeks
     *
     * @return TimeTableDisplayManager
     */
    private function generateWeeks(): TimeTableDisplayManager
    {
        if (false === $this->isTimeTable)
            return $this;
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
	        if ($day->format('l') === $this->getFirstDayofWeek() && !empty($week->days))
	        {
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
	    dump($this);

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDisplayDate(): \DateTime
    {
        return $this->displayDate instanceof \DateTime ? $this->displayDate : new \DateTime();
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
        if (false === $this->isTimeTable)
            return;

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
        return $this->week instanceof \stdClass ? $this->week : new \stdClass();
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
     * @param UserInterface $user
     * @return null|string
     */
    public function getTimeTableIdentifier(User $user)
    {
        // Determine if user is staff or student
        if (!$this->isTimeTable($user)) {
            if ($this->getSession()->has('tt_identifier'))
                $this->getSession()->remove('tt_identifier');
            return null;
        }
        $identifier = '';

        if ($this->personManager->isStudent($user->getPerson())) {
            $identifier = 'stud' . $user->getPerson()->getStudent()->getId();
        }
        if ($this->personManager->isStaff($user->getPerson())) {
            $identifier = 'staf' . $user->getPerson()->getStaff()->getId();
        }
        $this->getSession()->set('tt_identifier', $identifier);

        return $identifier;
    }

    /**
     * Is TimeTable
     *
     * @param User $user
     * @return bool
     */
    public function isTimeTable(User $user)
    {
        if (is_bool($this->isTimeTable))
            return $this->isTimeTable;

        $this->isTimeTable = true;

        $this->isTimeTable = $user->hasPerson();

        return $this->isTimeTable;
    }

    /**
     * @return false|string
     */
    public function getTimeTableDisplayDate()
    {
        return date('Ymd');
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
        if ($this->studentActivities->count() === 0 || $this->studentIdentifier !== $this->getIdentifier()) {
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

    /**
     * @param $period
     * @return null|PeriodActivity
     */
    private function generateStaffActivities($period)
    {
        // test and load staff activities only once.
        if ($this->staffActivities->count() === 0 || $this->staffIdentifier !== $this->getIdentifier()) {
            $staff = $this->getOm()->getRepository(Staff::class)->find($this->getIdentifier());
            $acts = $this->getOm()->getRepository(Activity::class)->findByTutor($staff, $this->getYear());
            $this->staffActivities = new ArrayCollection();
            $this->staffIdentifier = $this->getIdentifier();
            foreach ($acts as $w)
                if (!$this->staffActivities->contains($w))
                    $this->staffActivities->add($w);
            $acts = $this->getOm()->getRepository(PeriodActivity::class)->findByTutor($staff, $this->getYear());

            foreach ($acts as $w)
                if (!$this->staffActivities->contains($w->getActivity()))
                    $this->staffActivities->add($w->getActivity());
        }

        foreach ($period->getActivities() as $activity) {
            if ($this->staffActivities->contains($activity->getActivity())) {
                return $activity;
            }
        }

        return null;
    }

    /**
     * @param $period
     * @return null|PeriodActivity
     */
    private function generateSpaceActivities($period)
    {
        // test and load staff activities only once.
        if ($this->spaceActivities->count() === 0 || $this->spaceIdentifier !== $this->getIdentifier()) {
            $space = $this->getOm()->getRepository(Space::class)->find($this->getIdentifier());
            $acts = $this->getOm()->getRepository(Activity::class)->findBySpace($space, $this->getYear());
            $this->spaceActivities = new ArrayCollection();
            $this->spaceIdentifier = $this->getIdentifier();
            foreach ($acts as $w)
                if (!$this->spaceActivities->contains($w))
                    $this->spaceActivities->add($w);
            $acts = $this->getOm()->getRepository(PeriodActivity::class)->findBySpace($space, $this->getYear());

            foreach ($acts as $w)
                if (!$this->spaceActivities->contains($w->getActivity()))
                    $this->spaceActivities->add($w->getActivity());
        }

        foreach ($period->getActivities() as $activity) {
            if ($this->spaceActivities->contains($activity->getActivity())) {
                return $activity;
            }
        }

        return null;
    }

    /**
     * @return string
     */
    public function getHeader(): string
    {
        return $this->header = empty($this->header) ? 'timetable.header.blank' : $this->header;
    }
}