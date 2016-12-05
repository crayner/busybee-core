<?php
namespace Busybee\InstituteBundle\Model ;

use Doctrine\Common\Collections\ArrayCollection ;

class Year
{
	protected $specialDays = null ;
	
	public function canDelete()
	{
		if (is_array($this->getTerms()))
			foreach($this->getTerms() as $term)
				if (! $term->canDelete())
					return false ;
		return true;
	}

	public function getSpecialDays()
	{
		$this->specialDays = new ArrayCollection();
		foreach($this->getTerms() as $term)
			$this->specialDays = new ArrayCollection(array_merge($this->specialDays->toArray(), $term->getSpecialDays()->toArray()));
		return $this->specialDays ;
	} 

	public function setSpecialDays(ArrayCollection $days)
	{
		foreach($days as $dayKey=>$day)
		{
			if (empty($day->getName()))
				$days->remove($dayKey);
			foreach($this->getTerms() as $term) 
			{
				if ($day->getDay() >= $term->getFirstDay() && $day->getDay() <= $term->getLastDay())
				{
					$day->setTerm($term);
					$term->addSpecialDay($day);
				}
				if ($day->getType() === 'closure')
				{
					$day->setOpen(null);
					$day->setFinish(null);
					$day->setClose(null);
					$day->setStart(null);
				}
			}
		}
		$this->specialDays = $days ;
		return $this ;
	} 
}