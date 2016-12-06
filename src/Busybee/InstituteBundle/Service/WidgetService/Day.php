<?php
namespace Busybee\InstituteBundle\Service\WidgetService;

use Busybee\SystemBundle\Setting\SettingManager ;

class Day {
	protected $date;
	
	protected $parameters;
	
	private $firstDayofWeek;
	
	private $lastDayofWeek;
	
	private $weekNumber = null ;
	
	protected $sm ;
	
	public function __construct(\DateTime $date, SettingManager $sm)
	{
		$this->parameters = array();
		$this->date = $date;
		$this->day = $date->format('D jS M Y');
		$this->sm = $sm ;
		$this->firstDayofWeek = $this->sm->get('firstDayofWeek', 'Monday') == 'Sunday' ? 7 : 1 ;
		$this->lastDayofWeek = $this->sm->get('firstDayofWeek', 'Monday') == 'Sunday' ? 6 : 7 ;
		$this->weekNumber = $this->getWeekNumber();
	}
	
	public function getDate()
	{
		return $this->date;
	}
	
	public function getNumber()
	{
		return $this->date->format('j');
	}
	
	public function isFirstInWeek()
	{

		return $this->date->format('N') == $this->firstDayofWeek;
	}
	
	public function isLastInWeek()
	{
		return $this->date->format('N') == $this->lastDayofWeek;
	}
	
	public function isInWeek($week)
	{
		return $this->date->format('W') == $week->getNumber();
	}
	
	public function isInMonth($month)
	{
		return (($this->date->format('n') == $month->getNumber())
			&& ($this->date->format('Y') == $month->getYear()));
	}
	
	public function isInYear($year)
	{
		return $this->date->format('Y') == $year;
	}
	
	public function setParameter($key, $value)
	{
		$this->parameters[$key] = $value;
	}
	
	public function getParameter($key)
	{
		return key_exists($key, $this->parameters) ? $this->parameters[$key] : null;
	}
	
	public function getWeekNumber()
	{
		if (! is_null($this->weekNumber))
			return $this->weekNumber;
		$date = clone $this->date ;
		if ($this->sm->get('firstDayofWeek') === 'Monday')
			return (int)$date->format('W');

		//  First day of week is Sunday ...
		$oneDayInterval = new \DateInterval('P1D');
		$date->add($oneDayInterval);
		return (int)$date->format('W');
	}
}
