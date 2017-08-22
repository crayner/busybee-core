<?php

namespace Busybee\Core\CalendarBundle\Service\WidgetService;

use Busybee\SystemBundle\Setting\SettingManager;
use Busybee\Core\CalendarBundle\Service\WidgetService\Calendar;
use Busybee\Core\CalendarBundle\Service\WidgetService\Day;

class Week
{
	protected $calendar;
	protected $days;
	protected $weekNumber = null;
	protected $parameters;

	public function __construct(Calendar $calendar, Day $day)
	{
		$this->parameters = array();
		$this->calendar   = $calendar;
		$this->days       = array();
		$this->addDay($day);
		$this->weekNumber = $day->getWeekNumber();
	}

	public function addDay($day)
	{
		$this->days[] = $day;
	}

	public function getDays()
	{
		return $this->days;
	}

	public function getYear()
	{
		$firstDay  = $this->days[count($this->days) - 1];
		$firstDate = $firstDay->getDate();

		return (int) $firstDate->format('Y');
	}

	public function isInMonth($month)
	{
		$months = $this->getMonths();
		foreach ($months as $monthIterator)
		{
			if ($monthIterator->getNumber() == $month->getNumber()
				&& $monthIterator->getYear() == $month->getYear())
				return true;
		}

		return false;
	}

	public function getMonths()
	{
		$_this  = $this;
		$months = array_filter($this->calendar->getMonths(), function ($month) use ($_this) {
			if (($_this->getFirstDate() < $month->getFirstDate()
					&& $_this->getLastDate() < $month->getFirstDate())
				|| ($_this->getFirstDate() > $month->getLastDate()
					&& $_this->getLastDate() > $month->getLastDate()))
			{
				return false;
			}
			else
			{
				return true;
			}
		});

		return $months;
	}

	public function getFirstDate()
	{
		return $this->getFirstDay()->getDate();
	}

	public function getFirstDay()
	{
		return $this->days[0];
	}

	public function getLastDate()
	{
		return $this->getLastDay()->getDate();
	}

	public function getLastDay()
	{
		return $this->days[count($this->days) - 1];
	}

	public function getFullName()
	{
		$fullNames = $this->calendar->getWeekFullNames();

		return $fullNames[$this->getNumber() - 1];
	}

	public function getNumber()
	{
		return (int) $this->weekNumber;
	}

	public function getnameShort()
	{
		$nameShorts = $this->calendar->getWeeknameShorts();

		return $nameShorts[$this->getNumber() - 1];
	}

	public function setParameter($key, $value)
	{
		$this->parameters[$key] = $value;
	}

	public function getParameter($key)
	{
		return key_exists($key, $this->parameters) ? $this->parameters[$key] : null;
	}
}
