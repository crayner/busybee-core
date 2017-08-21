<?php

namespace Busybee\Core\CalendarBundle\Model;


abstract class GradeModel
{
	/**
	 * Can Delete
	 *
	 * @return bool
	 */
	public function canDelete()
	{
		return true;
	}

	/**
	 * Get Full Name
	 *
	 * @return string
	 */
	public function getFullName()
	{
		return $this->getGradeYear();
	}

	/**
	 * Get Grade Year
	 *
	 * @return string
	 */
	public function getGradeYear()
	{
		return $this->getGrade() . ' (' . $this->getYear()->getName() . ')';
	}
}