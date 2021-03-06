<?php
namespace Busybee\Core\CalendarBundle\Service\WidgetService;

use Busybee\Core\CalendarBundle\Service\WidgetService\Calendar;
use Busybee\Core\CalendarBundle\Service\WidgetService\Day;

class Month
{
	protected $calendar;
	protected $days;
	protected $weeks;

	protected $parameters;

	public function __construct(Calendar $calendar, Day $day)
	{
		$this->parameters = [];
		$this->calendar   = $calendar;
		$this->days       = [];
		$this->addDay($day);
	}

	public function addDay(Day $day)
	{
		$this->days[] = $day;

		return $this;
	}

	public function getDays()
	{
		return $this->days;
	}

	public function addWeek(Week $week): Month
	{
		$this->weeks[] = $week;

		return $this;
	}

	public function getWeeks(): array
	{
		return $this->weeks;
	}

	public function get_Weeks()
	{
		$_this = $this;
		$weeks = array_filter($this->calendar->getWeeks(), function ($week) use ($_this) {
			if (($week->getFirstDate() < $_this->getFirstDate()
					&& $week->getLastDate() < $_this->getFirstDate())
				|| ($week->getFirstDate() > $_this->getLastDate()
					&& $week->getLastDate() > $_this->getLastDate()))
			{
				return false;
			}
			else
			{
				return true;
			}
		});

		return $weeks;
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

	public function getYear()
	{
		$firstDay  = $this->days[0];
		$firstDate = $firstDay->getDate();

		return (int) $firstDate->format('Y');
	}

	public function getDay($index)
	{
		if (isset($this->days[$index]))
			return $this->days[$index];
		else
			return null;
	}

	public function getFullName()
	{
		$fullNames = $this->calendar->getMonthFullNames();

		return $fullNames[$this->getNumber() - 1];
	}

	public function getNumber()
	{
		$firstDay  = $this->days[0];
		$firstDate = $firstDay->getDate();

		return (int) $firstDate->format('n');
	}

	public function getnameShort()
	{
		$nameShorts = $this->calendar->getMonthnameShorts();

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
