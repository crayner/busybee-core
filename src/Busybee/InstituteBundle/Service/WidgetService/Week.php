<?php
namespace Busybee\InstituteBundle\Service\WidgetService;

class Week {
	protected $calendar;
	protected $days;
	
	protected $parameters;
	
	public function __construct($calendar)
	{
		$this->parameters = array();
		$this->calendar = $calendar;
		$this->days = array();
	}
	
	public function getNumber()
	{
		$lastDay = $this->days[count($this->days) - 1];
		$lastDate = $lastDay->getDate();
		return (int)$lastDate->format('W');
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
		$firstDay = $this->days[count($this->days) - 1];
		$firstDate = $firstDay->getDate();
		return (int)$firstDate->format('Y');
	}
	
	public function getMonths()
	{		
		$_this = $this;
		$months = array_filter($this->calendar->getMonths(), function($month) use($_this) {
			if(($_this->getFirstDate() < $month->getFirstDate()
					&& $_this->getLastDate() < $month->getFirstDate())
					||($_this->getFirstDate() > $month->getLastDate()
							&& $_this->getLastDate() > $month->getLastDate())) {
				return false;
			} else {
				return true;
			}
		});
		
		return $months;
	}
	
	public function isInMonth($month)
	{
		$months = $this->getMonths();
		foreach($months as $monthIterator) {
			if($monthIterator->getNumber() == $month->getNumber()
					&& $monthIterator->getYear() == $month->getYear())
				return true;
		}
		return false;
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
	
	public function getFullName()
	{
		$fullNames = $this->calendar->getWeekFullNames();
		return $fullNames[$this->getNumber() - 1];
	}
	
	public function getShortName()
	{
		$shortNames = $this->calendar->getWeekShortNames();
		return $shortNames[$this->getNumber() - 1];
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
