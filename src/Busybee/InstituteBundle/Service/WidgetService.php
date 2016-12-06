<?php
namespace Busybee\InstituteBundle\Service;

use Busybee\InstituteBundle\Service\WidgetService\Calendar;
use Busybee\SystemBundle\Setting\SettingManager ;

/**
 * Class for widget servuce. Creates an instance of calendar suitable for rendering in TWIG template.
 * @author tfox
 *
 */
class WidgetService 
{
	const DEFAULT_CALENDAR_MODEL = '\Busybee\InstituteBundle\Service\WidgetService\Calendar';
	
	private $calendarModel = self::DEFAULT_CALENDAR_MODEL;
	private $monthModel = null;
	private $weekModel = null;
	private $dayModel = null;
	protected $sm ;
	
	/**
	 * Returns a calendar for specified year
	 * @param int $year
	 */
	public function generateCalendar($year) {
		$calendar = new $this->calendarModel($this->sm);
		$calendar->setModels($this->monthModel, $this->weekModel, $this->dayModel);
		$calendar->generate($year);
		
		return $calendar;
	}
	
	public function setModels($calendarModel, $monthModel, $weekModel, $dayModel)
	{
		$this->calendarModel = is_null($calendarModel) ? self::DEFAULT_CALENDAR_MODEL : $calendarModel;
		if(! class_exists($this->calendarModel))
			throw new \Exception(sprintf('Class %s not found.', $this->calendarModel));
		$this->monthModel = $monthModel; 
		$this->weekModel = $weekModel; 
		$this->dayModel = $dayModel;
	}
	
	public function __construct(SettingManager $sm)
	{
		$this->sm = $sm ;
	}
}
