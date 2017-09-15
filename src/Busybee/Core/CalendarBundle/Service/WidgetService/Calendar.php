<?php
namespace Busybee\Core\CalendarBundle\Service\WidgetService;

use Busybee\Core\CalendarBundle\Model\Day;
use Busybee\Core\TemplateBundle\Source\SettingManagerInterface;

/**
 * Represents a calendar for specified year
 * @author tfox
 *
 */
class Calendar
{
	const DEFAULT_MONTH_MODEL = Month::class;
	const DEFAULT_WEEK_MODEL = Week::class;
	const DEFAULT_DAY_MODEL = Day::class;

	protected $monthModel = self::DEFAULT_MONTH_MODEL;
	protected $weekModel = self::DEFAULT_WEEK_MODEL;
	protected $dayModel = self::DEFAULT_DAY_MODEL;

	/**
	 * Year for calendar
	 * @var datetime
	 */
	protected $year;

	/**
	 * Array with instances of Month objects
	 * @var array
	 */
	protected $months;

	/**
	 * Array with instances of Week objects
	 * @var array
	 */
	protected $weeks;

	/**
	 * Array with instances of Day objects
	 * @var array
	 */
	protected $days;

	protected $monthnameShorts;

	protected $monthFullNames;

	protected $weeknameShorts;

	protected $weekFullNames;

	protected $parameters;

	private $sm;

	/**
	 * Calendar constructor.
	 *
	 * @param SettingManagerInterface $sm
	 */
	public function __construct(SettingManagerInterface $sm)
	{
		$this->sm = $sm;
	}

	public function generate($year)
	{
		$this->year       = $year;
		$this->months     = array();
		$this->weeks      = array();
		$this->days       = array();
		$this->parameters = array();
		$oneDayInterval   = new \DateInterval('P1D');

		//Calculate first and last days of year
		$firstYearDate = \DateTime::createFromFormat('d.m.Y H:i:s', sprintf('01.%s 00:00:00', $year->getFirstDay()->format('m.Y')));
		$lastYearDate  = clone $firstYearDate;
		$lastYearDate->add(new \DateInterval('P1Y'));
		$lastYearDate->sub($oneDayInterval);

		//Calculate first and last days in calendar.
		//It's monday on the 1st week and sunday on the last week. or Sunday and Saturday
		$firstDate = clone $firstYearDate;
		$lastDate  = clone $lastYearDate;

		while ($firstDate->format('N') != $this->getFirstDayofWeek())
		{
			$firstDate->sub($oneDayInterval);
		}
		while ($lastDate->format('N') != $this->getLastDayofWeek())
		{
			$lastDate->add($oneDayInterval);
		}

		//Build calendar
		$dateIterator = clone $firstDate;
		$currentWeek  = null;
		$currentMonth = null;
		while ($dateIterator <= $lastDate)
		{
			$currentDate = clone $dateIterator;
			$day         = new $this->dayModel($currentDate, $this->sm, count($this->weeks));
			$this->addDay($day);

			if (is_null($currentWeek))
				$currentWeek = new $this->weekModel($this, $day, count($this->weeks) + 1);
			else
				$currentWeek->addDay($day);

			if ($currentDate >= $firstYearDate && $currentDate <= $lastYearDate)
			{
				if (is_null($currentMonth))
				{
					$currentMonth = new $this->monthModel($this, $day);
				}
				elseif ($day->isInMonth($currentMonth))
				{
					$currentMonth->addDay($day);
					if ($currentDate == $lastYearDate)
					{
						$currentMonth->addWeek($currentWeek);
						$this->addWeek($currentWeek);
						$this->addMonth($currentMonth);
						if (count($currentWeek->getDays()) == 7) $currentWeek = null;
					}
				}
				elseif (!$day->isInMonth($currentMonth))
				{
					if (count($currentWeek->getDays()) > 1)
						$currentMonth->addWeek($currentWeek);
					$this->addMonth($currentMonth);
					$currentMonth = new $this->monthModel($this, $day);
				}

				if ($currentWeek instanceof Week && count($currentWeek->getDays()) == 7)
				{
					$this->addWeek($currentWeek);
					$currentMonth->addWeek($currentWeek);
					$currentWeek = null;
				}
			}
			$dateIterator->add($oneDayInterval);
		}
		$this->initNames();
dump($this);
		return $this;
	}

	public function getFirstDayofWeek()
	{
		return $this->sm->get('firstDayofWeek', 'Monday') == 'Sunday' ? 7 : 1;
	}

	public function getLastDayofWeek()
	{
		return $this->sm->get('firstDayofWeek', 'Monday') == 'Sunday' ? 6 : 7;
	}

	public function addDay($day)
	{
		$this->days[] = $day;
	}

	public function addWeek($week)
	{
		$this->weeks[] = $week;
	}

	public function addMonth($month)
	{
		$this->months[] = $month;
	}

	private function initNames()
	{
		$this->monthFullNames  = array(
			'January', 'February', 'March', 'April', 'May', 'June',
			'July', 'August', 'September', 'October', 'November', 'December'
		);
		$this->monthnameShorts = array(
			'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
			'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
		);
		$this->weekFullNames   = array(
			'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'
		);
		$this->weeknameShorts  = array(
			'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'
		);
		if ($this->sm->get('firstDayofWeek') === 'Sunday')
		{
			$this->weekFullNames  = array(
				'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'
			);
			$this->weeknameShorts = array(
				'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'
			);
		}


	}

	public function getMonths()
	{
		return $this->months;
	}

	public function getWeeks()
	{
		return $this->weeks;
	}

	public function getDays()
	{
		return $this->days;
	}

	public function getYear()
	{
		return $this->year;
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

	public function getMonthFullNames()
	{
		return $this->monthFullNames;
	}

	public function setMonthFullNames($arg)
	{
		$this->monthFullNames = $arg;
	}

	public function getMonthnameShorts()
	{
		return $this->monthnameShorts;
	}

	public function setMonthnameShorts($arg)
	{
		$this->monthnameShorts = $arg;
	}

	public function getWeekFullNames()
	{
		return $this->weekFullNames;
	}

	public function setWeekFullNames($arg)
	{
		$this->weekFullNames = $arg;
	}

	public function getWeeknameShorts()
	{
		return $this->weeknameShorts;
	}

	public function setWeeknameShorts($arg)
	{
		$this->weeknameShorts = $arg;
	}

	public function setModels($monthModel, $weekModel, $dayModel)
	{
		$this->monthModel = is_null($monthModel) ? self::DEFAULT_MONTH_MODEL : $monthModel;
		if (!class_exists($this->monthModel))
			throw new \Exception(sprintf('Class %s not found.', $this->monthModel));

		$this->weekModel = is_null($weekModel) ? self::DEFAULT_WEEK_MODEL : $weekModel;
		if (!class_exists($this->weekModel))
			throw new \Exception(sprintf('Class %s not found.', $this->weekModel));

		$this->dayModel = is_null($dayModel) ? self::DEFAULT_DAY_MODEL : $dayModel;
		if (!class_exists($this->dayModel))
			throw new \Exception(sprintf('Class %s not found.', $this->dayModel));
	}

	public function getDay($param)
	{
		if (is_int($param))
		{
			return key_exists($param, $this->days) ? $this->days[$param] : null;
		}
		elseif (is_string($param))
		{
			foreach ($this->days as $day)
			{
				$date = $day->getDate()->format('d.m.Y');
				if ($date == $param)
					return $day;
			}

			return null;
		}
	}

	public function setParameter($key, $value)
	{
		$this->parameters[$key] = $value;
	}

	public function getParameter($key)
	{
		return key_exists($key, $this->parameters) ? $this->parameters[$key] : null;
	}

	public function getSM()
	{
		return $this->sm;
	}
}
