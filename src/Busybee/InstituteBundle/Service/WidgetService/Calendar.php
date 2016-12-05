<?php
namespace Busybee\InstituteBundle\Service\WidgetService;

/**
 * Represents a calendar for specified year
 * @author tfox
 *
 */
class Calendar {
	const DEFAULT_MONTH_MODEL = '\Busybee\InstituteBundle\Service\WidgetService\Month';
	const DEFAULT_WEEK_MODEL = '\Busybee\InstituteBundle\Service\WidgetService\Week';
	const DEFAULT_DAY_MODEL = '\Busybee\InstituteBundle\Service\WidgetService\Day';
	
	protected $monthModel = self::DEFAULT_MONTH_MODEL;
	protected $weekModel = self::DEFAULT_WEEK_MODEL;
	protected $dayModel = self::DEFAULT_DAY_MODEL;
	
	/**
	 * Year for calendar
	 * @var int
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
	
	protected $monthShortNames;
	
	protected $monthFullNames;
	
	protected $weekShortNames;
	
	protected $weekFullNames;
	
	protected $parameters;
	
	public function generate($year)
	{
		$this->year = $year;
		$this->months = array();
		$this->weeks = array();
		$this->days = array();
		$this->parameters = array();
		$oneDayInterval = new \DateInterval('P1D');
		
		//Calculate first and last days of year
		$firstYearDate = \DateTime::createFromFormat('d.m.Y H:i:s', sprintf('01.01.%s 00:00:00', $year));
		$lastYearDate = clone $firstYearDate;
		$lastYearDate->add(new \DateInterval('P1Y'));
		$lastYearDate->sub($oneDayInterval);
		
		//Calculate first and last days in calendar.
		//It's monday on the 1st week and sunday on the last week
		$firstDate = clone $firstYearDate;
		$lastDate = clone $lastYearDate;		
		
		while($firstDate->format('N') != 1) {
			$firstDate->sub($oneDayInterval);
		}
		while($lastDate->format('N') != 7) {
			$lastDate->add($oneDayInterval);
		}
		
		//Build calendar
		$dateIterator = clone $firstDate;
		$currentWeek = null;
		$currentMonth = null;
		while($dateIterator <= $lastDate) {
			$currentDate = clone $dateIterator;
			$day = new $this->dayModel($currentDate);
			$this->addDay($day);
			
			//Calculate month and week numbers
			$monthNumber = $currentDate->format('n');
			$monthYear = $currentDate->format('Y');
			$weekNumber = (int)$currentDate->format('W');
			
			if($monthYear >= $this->year) {				
					
				//Add a day to month
				if(is_null($currentMonth)) {
					$currentMonth = new $this->monthModel($this);
				} else if($currentMonth->getNumber() != $monthNumber) {
					$this->addMonth($currentMonth);
					$currentMonth = new $this->monthModel($this);
				}
				$currentMonth->addDay($day);
			}

			
			//Add a day to week
			if(is_null($currentWeek)) {
				$currentWeek = new $this->weekModel($this);
			} else if($currentWeek->getNumber() != $weekNumber) {
				$this->addWeek($currentWeek);
				$currentWeek = new $this->weekModel($this);
			}
			$currentWeek->addDay($day);
			
			$dateIterator->add($oneDayInterval);
		}		
		$this->addWeek($currentWeek);

		$this->initNames();
	}
	
	private function initNames()
	{
		$this->monthFullNames = array(
			'January', 'February', 'March', 'April', 'May', 'June',
			'July', 'August', 'September', 'October', 'November', 'December'
		);
		$this->monthShortNames = array(
			'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
			'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
		);
		$this->weekFullNames = array(
			'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'
		);
		$this->weekShortNames = array(
			'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'
		);
		
		
	}
	
	public function addMonth($month)
	{
		$this->months[] = $month;
	}
	
	public function addWeek($week)
	{
		$this->weeks[] = $week;
	}
	
	public function addDay($day)
	{
		$this->days[] = $day;
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
	
	public function getFirstDay()
	{
		return $this->days[0];
	}
	
	public function getFirstDate()
	{
		return $this->getFirstDay()->getDate();
	}
	
	public function getLastDay()
	{
		return $this->days[count($this->days) - 1];
	}
	
	public function getLastDate()
	{
		return $this->getLastDay()->getDate();
	}
	
	public function getMonthFullNames()
	{
		return $this->monthFullNames;
	}
	
	public function getMonthShortNames()
	{
		return $this->monthShortNames;
	}
	
	public function getWeekFullNames()
	{
		return $this->weekFullNames;
	}
	
	public function getWeekShortNames()
	{
		return $this->weekShortNames;
	}

	public function setMonthFullNames($arg)
	{
		$this->monthFullNames = $arg;
	}
	
	public function setMonthShortNames($arg)
	{
		$this->monthShortNames = $arg;
	}
	
	public function setWeekFullNames($arg)
	{
		$this->weekFullNames = $arg;
	}
	
	public function setWeekShortNames($arg)
	{
		$this->weekShortNames = $arg;
	}
	
	public function setModels($monthModel, $weekModel, $dayModel)
	{
		$this->monthModel = is_null($monthModel) ? self::DEFAULT_MONTH_MODEL : $monthModel;
		if(!class_exists($this->monthModel))
			throw new \Exception(sprintf('Class %s not found.', $this->monthModel));
		
		$this->weekModel = is_null($weekModel) ? self::DEFAULT_WEEK_MODEL : $weekModell;
		if(!class_exists($this->weekModel))
			throw new \Exception(sprintf('Class %s not found.', $this->weekModel));
		
		$this->dayModel = is_null($dayModel) ? self::DEFAULT_DAY_MODEL : $dayModel;
		if(!class_exists($this->dayModel))
			throw new \Exception(sprintf('Class %s not found.', $this->dayModel));
	}
	
	public function getDay($param)
	{
		if(is_int($param)) {
			return key_exists($param, $this->days) ? $this->days[$param] : null;
		} elseif(is_string($param)) {
			foreach($this->days as $day) {
				$date = $day->getDate()->format('d.m');
				if($date == $param)
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
	
}
