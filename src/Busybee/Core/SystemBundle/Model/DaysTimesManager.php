<?php

namespace Busybee\Core\SystemBundle\Model;

use Busybee\Core\SystemBundle\Setting\SettingManager;
use Busybee\People\StaffBundle\Entity\Staff;
use Busybee\People\StudentBundle\Entity\Student;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Form;

class DaysTimesManager
{
	/**
	 * @var Day
	 */
	private $day;

	/**
	 * @var Time
	 */
	private $time;

	/**
	 * @var SettingManager
	 */
	private $settingManager;

	/**
	 * @var ObjectManager
	 */
	private $objectManager;

	/**
	 * HouseManager constructor.
	 */
	public function __construct(SettingManager $settingManager, ObjectManager $objectManager)
	{
		$this->setDay(new Day());
		$this->setTime(new Time());
		$this->settingManager = $settingManager;
		$this->objectManager  = $objectManager;
		$this->loadDaysTimes();
	}

	/**
	 * @return DaysTimesManager
	 */
	private function loadDaysTimes(): DaysTimesManager
	{
		$this->time->setOpen(new \DateTime('1970-01-01 ' . $this->settingManager->get('schoolday.open')));

		$this->time->setBegin(new \DateTime('1970-01-01 ' . $this->settingManager->get('schoolday.begin')));

		$this->time->setFinish(new \DateTime('1970-01-01 ' . $this->settingManager->get('schoolday.finish')));

		$this->time->setClose(new \DateTime('1970-01-01 ' . $this->settingManager->get('schoolday.close')));


		foreach ($this->settingManager->get('schoolweek') as $name => $shortName)
		{
			$method = 'set' . ucfirst($shortName);
			$this->day->$method(true);
		}

		return $this;
	}

	/**
	 * @return Day
	 */
	public function getDay(): Day
	{
		return $this->day;
	}

	/**
	 * @param Day $day
	 *
	 * @return DaysTimesManager
	 */
	public function setDay(Day $day): DaysTimesManager
	{
		$this->day = $day;

		return $this;
	}

	/**
	 * @return Time
	 */
	public function getTime(): Time
	{
		return $this->time;
	}

	/**
	 * @param Time $time
	 *
	 * @return DaysTimesManager
	 */
	public function setTime(Time $time): DaysTimesManager
	{
		$this->time = $time;

		return $this;
	}

	public function saveDaysTimes(Form $form): DaysTimesManager
	{
		$data = $form->getData();

		$days = $data->getDay();

		$w = [];

		if ($days->isSun())
			$w['Sunday'] = 'Sun';
		if ($days->isMon())
			$w['Monday'] = 'Mon';
		if ($days->isTue())
			$w['Tuesday'] = 'Tue';
		if ($days->isWed())
			$w['Wednesday'] = 'Wed';
		if ($days->isThu())
			$w['Thursday'] = 'Thu';
		if ($days->isFri())
			$w['Friday'] = 'Fri';
		if ($days->isSat())
			$w['Saturday'] = 'Sat';

		$this->settingManager->set('schoolweek', $w);

		$this->settingManager->set('schoolday.open', $data->getTime()->getOpen());
		$this->settingManager->set('schoolday.begin', $data->getTime()->getBegin());
		$this->settingManager->set('schoolday.finish', $data->getTime()->getFinish());
		$this->settingManager->set('schoolday.close', $data->getTime()->getClose());

		return $this;
	}
}