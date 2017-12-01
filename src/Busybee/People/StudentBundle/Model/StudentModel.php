<?php
namespace Busybee\People\StudentBundle\Model;

use Busybee\Core\CalendarBundle\Entity\Year;
use Busybee\People\PersonBundle\Entity\Person;

abstract class StudentModel extends Person
{
	/**
	 * @var Year
	 */
	private $year;

	/**
	 * @todo Add Student Delete checks.
	 * @return bool
	 */
	public function canDelete()
	{
		return parent::canDelete();
	}

	/**
	 * @param Year $year
	 */
	public function getStudentCalendarGroup(Year $year)
	{
		$grades = $this->getGrades();

		foreach ($grades as $grade)
		{
			if ($grade->getGrade()->getYear()->getId() == $year->getId())
				return $grade->getGrade();
		}

		return null;
	}

	/**
	 * @return Year
	 */
	public function getYear(): Year
	{
		return $this->year;
	}

	/**
	 * @param Year $year
	 */
	public function setYear(Year $year)
	{
		$this->year = $year;

		return $this;
	}
}