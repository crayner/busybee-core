<?php
namespace Busybee\InstituteBundle\Model ;

use Busybee\SystemBundle\Setting\SettingManager ;
use Busybee\InstituteBundle\Repository\YearRepository ;

class CalendarManager
{
	private $year;
	
	private $calendar;
	
	private $sm ;
	
	public function setCalendarDays($year, $calendar)
	{
		$this->year = $year ;
		$this->calendar = $calendar ;
		$this->setTermBreaks() ;
		$this->setClosedDays() ;
		$this->setSpecialDays() ;
		return $this->calendar ;
	}
	
	public function setTermBreaks()
	{
		$oneDayInterval = new \DateInterval('P1D');
		$currentDate = $this->year->getFirstDay();
		
		while ($currentDate <= $this->year->getLastDay())
		{
			// School Day ?
			$this->calendar->getDay($currentDate->format('d.m'))->setTermBreak($this->isTermBreak($currentDate));	

			$currentDate->add($oneDayInterval);
		}
	}
	
	public function isTermBreak($currentDate)
	{
		// Check if the day is a possible school day. i.e. Ignore Weekends 
		if (! in_array($currentDate->format('l'), $this->sm->get('schoolWeek')))
			return false ;
		foreach($this->year->getTerms() as $term)
			if ($currentDate >= $term->getFirstDay() && $currentDate <= $term->getLastDay())
				return false ;
				
		return true ;
	}

	public function __construct(SettingManager $sm, YearRepository $repo)
	{
		$this->sm = $sm ;
		$this->repo = $repo ;
	}
	
	public function setClosedDays()
	{
		foreach($this->year->getSpecialDays() as $specialDay)
			if ($specialDay->getType() == 'closure')
				$this->calendar->getDay($specialDay->getDay()->format('d.m'))->setClosed(true, $specialDay->getName());
	}
	
	public function setSpecialDays()
	{
		foreach($this->year->getSpecialDays() as $specialDay)
			if ($specialDay->getType() != 'closure')
				$this->calendar->getDay($specialDay->getDay()->format('d.m'))->setSpecial(true, $specialDay->getName());
	}
}