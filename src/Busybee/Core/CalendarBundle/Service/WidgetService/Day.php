<?php

namespace Busybee\Core\CalendarBundle\Service\WidgetService;

use Busybee\Core\CalendarBundle\Entity\Year;
use Busybee\Core\TemplateBundle\Source\SettingManagerInterface;

class Day
{
	/**
	 * @var \DateTime
	 */
	protected $date;

	/**
	 * @var array
	 */
	protected $parameters;

	/**
	 * @var SettingManagerInterface
	 */
	protected $sm;

	/**
	 * @var int
	 */
	private $firstDayofWeek;

	/**
	 * @var int
	 */
	private $lastDayofWeek;

	/**
	 * @var int|null
	 */
	private $weekNumber = null;

	/**
	 * Day constructor.
	 *
	 * @param \DateTime               $date
	 * @param SettingManagerInterface $sm
	 * @param Year                    $year
	 */
	public function __construct(\DateTime $date, SettingManagerInterface $sm, int $weeks)
	{
		$this->parameters     = array();
		$this->date           = $date;
		$this->day            = $date->format('D jS M Y');
		$this->sm             = $sm;
		$this->firstDayofWeek = $this->sm->get('firstDayofWeek', 'Monday') == 'Sunday' ? 7 : 1;
		$this->lastDayofWeek  = $this->sm->get('firstDayofWeek', 'Monday') == 'Sunday' ? 6 : 7;
	}

	/**
	 * @param int $weekNumber
	 *
	 * @return Week
	 */
	public function setWeekNumber(int $weekNumber): Day
	{
		$this->weekNumber = $weekNumber;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getWeekNumber(): int
	{
		return $this->weekNumber;
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
}
