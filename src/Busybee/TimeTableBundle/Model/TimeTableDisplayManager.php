<?php

namespace Busybee\TimeTableBundle\Model;


use Busybee\HomeBundle\Exception\Exception;
use Busybee\InstituteBundle\Entity\Term;
use Busybee\TimeTableBundle\Entity\Line;
use Busybee\TimeTableBundle\Entity\Period;
use Busybee\TimeTableBundle\Entity\PeriodActivity;

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
        'grad' => 'Grade'
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
    public function getTerm(): Term
    {
        return $this->term;
    }

    /**
     * @param Term $term
     */
    public function setTerm(Term $term): TimeTableDisplayManager
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
     * @param $identifier
     */
    public function generateTimeTable($identifier, $displayDate)
    {
        $this->parseIdentifier($identifier);

        $this->setDisplayDate(new \DateTime($displayDate));

        $year = $this->getYear();
        $term = null;
        foreach ($year->terms as $term)
            if ($this->getDisplayDate() >= $term->getFirstDay() && $this->getDisplayDate() <= $term->getLastDay())
                break;
        $this->setTerm($term);


        foreach ($term->weeks as $q => $week)
            foreach ($week as $details)
                if ($details->date->format('Ymd') === $this->getDisplayDate()->format('Ymd')) {
                    $this->setWeek($week);
                    $this->setWeekNumber($q + 1);
                    break;
                }

        $actSearch = 'generate' . ucfirst($this->gettype()) . 'Activities';
        foreach ($this->getWeek() as $q => $day) {
            $day->class = '';
            foreach ($day->ttday->getPeriods() as $p => $period)
                $period->activity = $this->$actSearch($period);
            if (isset($day->specialDay))
                $day = $this->manageSpecialDay($day);
        }
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
        $this->displayDate = $displayDate;

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
     * @return array
     */
    public function getWeek(): array
    {
        return $this->week;
    }

    /**
     * @param array $week
     * @return TimeTableDisplayManager
     */
    public function setWeek(array $week): TimeTableDisplayManager
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
                if ($period->getStart() < $day->specialDay->getFinish() && $period->getEnd() > $day->specialDay->getFinish())
                    $period->setStart($day->specialDay->getFinish());
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
}