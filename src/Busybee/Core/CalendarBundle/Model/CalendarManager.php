<?php

namespace Busybee\Core\CalendarBundle\Model;

use Busybee\SystemBundle\Setting\SettingManager;
use Busybee\Core\CalendarBundle\Repository\YearRepository;
use Busybee\Core\CalendarBundle\Entity\Year;
use Busybee\Core\CalendarBundle\Entity\SpecialDay;
use DateTime;
use Doctrine\ORM\EntityManager;

class CalendarManager
{
	/**
	 * @var Year
	 */
	private $year;

	/**
	 * @var
	 */
	private $calendar;

	private $sm;

	public function __construct(SettingManager $sm, YearRepository $repo, EntityManager $em)
	{
		$this->sm   = $sm;
		$this->repo = $repo;
		$this->em   = $em;
	}

	/**
	 * @param $year
	 * @param $calendar
	 *
	 * @return mixed
	 */
	public function setCalendarDays($year, $calendar)
	{
		$this->year     = $year;
		$this->calendar = $calendar;
		$this->setNonSchoolDays();
		$this->setTermBreaks();
		$this->setClosedDays();
		$this->setSpecialDays();

		return $this->calendar;
	}

	/**
	 *
	 */
	public function setNonSchoolDays()
	{
		$schoolDays = $this->sm->get('schoolweek');

		foreach ($this->calendar->getMonths() as $monthKey => $month)
		{
			foreach ($month->getWeeks() as $weekKey => $week)
			{
				foreach ($week->getDays() as $dayKey => $day)
				{
					// School Day ?
					if (!in_array($day->getDate()->format('D'), $schoolDays))
						$day->setSchoolDay(false);
					else
						$day->setSchoolDay(true);
				}
				$month->getWeeks()[$weekKey] = $week;
			}
			$this->calendar->getMonths()[$monthKey] = $month;
		}
	}

	/**
	 *
	 */
	public function setTermBreaks()
	{
		foreach ($this->calendar->getMonths() as $monthKey => $month)
		{
			foreach ($month->getWeeks() as $weekKey => $week)
			{
				foreach ($week->getDays() as $dayKey => $day)
				{
					// School Day ?
					$break = $this->isTermBreak($day);
					$this->calendar->getDay($day->getDate()->format('d.m.Y'))->setTermBreak($break);
					$day->setTermBreak($break);
					$week->getDays()[$dayKey] = $day;
				}
				$month->getWeeks()[$weekKey] = $week;
			}
			$this->calendar->getMonths()[$monthKey] = $month;
		}
	}

	/**
	 * @param Day $currentDate
	 *
	 * @return bool
	 */
	public function isTermBreak(Day $currentDate)
	{
		// Check if the day is a possible school day. i.e. Ignore Weekends
		if ($currentDate->isTermBreak()) return true;

		foreach ($this->year->getTerms() as $term)
		{
			if ($currentDate->getDate() >= $term->getFirstDay() && $currentDate->getDate() <= $term->getLastDay())
				return false;
		}

		$currentDate->setTermBreak(true);

		return true;
	}

	/**
	 *
	 */
	public function setClosedDays()
	{
		if (!is_null($this->year->getSpecialDays()))
			foreach ($this->year->getSpecialDays() as $specialDay)
				if ($specialDay->getType() == 'closure')
					$this->calendar->getDay($specialDay->getDay()->format('d.m.Y'))->setClosed(true, $specialDay->getName());
	}

	/**
	 *
	 */
	public function setSpecialDays()
	{
		if (!is_null($this->year->getSpecialDays()))
			foreach ($this->year->getSpecialDays() as $specialDay)
				if ($specialDay->getType() != 'closure')
					$this->calendar->getDay($specialDay->getDay()->format('d.m.Y'))->setSpecial(true, $specialDay->getName());
	}

	public function getDayClass(Day $day, $class = null)
	{

		$class    = '';
		$weekDays = $this->sm->get('schoolWeek');
		$weekEnd  = true;

		if (isset($weekDays[$day->getDate()->format('D')]))
			$weekEnd = false;
		if (!$weekEnd)
			$class .= ' dayBold';

		if ($this->isTermBreak($day))
			$class .= ' termBreak';
		if ($day->isClosed())
		{
			$class .= ' isClosed';
			$class = str_replace(' termBreak', '', $class);
		}
		if ($day->isSpecial())
		{
			$class .= ' isSpecial';
			$class = str_replace(' termBreak', '', $class);
		}

		if (!$day->getSchoolDay())
		{
			$class .= ' isNonSchoolDay';
			$class = str_replace(' termBreak', '', $class);
		}

		if (empty($class)) return '';

		return ' class="' . trim($class) . '"';
	}

	/**
	 * @param $day
	 *
	 * @return bool
	 */
	public function testNextYear($day)
	{
		if ($day instanceof SpecialDay)
			$test = new \DateTime($day->getDay()->format('Y-m-d'), $day->getDay()->getTimeZone());
		elseif ($day instanceof DateTime)
			$test = new \DateTime($day->format('Y-m-d'), $day->getTimeZone());
		elseif (is_string($day))
			$test = new DateTime($day);

		$year = $this->getNextYear($test->format('Y-m-d'));

		if (!$year instanceof Year) return false;
		$oneYear = new \DateInterval('P1Y');
		$test->add($oneYear);

		if ($test < $year->getFirstDay() || $test > $year->getLastDay()) return false;

		$sprepo = $this->em->getRepository('BusybeeInstituteBundle:SpecialDay');

		if (!is_null($sprepo->findOneBy(array('day' => $test)))) return false;

		return true;
	}

	/**
	 * @param $day
	 *
	 * @return array|mixed
	 */
	public function getNextYear($day)
	{
		$year = $this->repo->createQueryBuilder('y')
			->where('y.firstDay > :thisYear')
			->orderBy('y.firstDay', 'ASC')
			->setParameter('thisYear', $day)
			->setMaxResults(1)
			->getQuery()
			->getResult();
		if (is_array($year) && count($year) === 1)
			$year = reset($year);

		return $year;
	}

	/**
	 * @param $day
	 *
	 * @return array|mixed
	 */
	public function getCurrentYear($day)
	{
		$year = $this->repo->createQueryBuilder('y')
			->where('y.firstDay <= :thisYear')
			->orderBy('y.firstDay', 'ASC')
			->setParameter('thisYear', $day)
			->setMaxResults(1)
			->getQuery()
			->getResult();
		if (is_array($year) && count($year) === 1)
			$year = reset($year);

		return $year;
	}
}